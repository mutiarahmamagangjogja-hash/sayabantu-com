<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\File;

class AdminRealtimeService
{
    protected string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/admin_realtime_state.json');
        $this->ensureInitialized();
    }

    /**
     * Pastikan state default tersedia
     */
    protected function ensureInitialized(): void
    {
        if (! File::exists(dirname($this->storagePath))) {
            File::makeDirectory(dirname($this->storagePath), 0755, true);
        }

        if (! File::exists($this->storagePath)) {
            $this->saveState($this->defaultState());
        }
    }

    /**
     * Data default awal
     */
    protected function defaultState(): array
    {
        return [
            'stats' => [
                'total_pesanan' => 0,
                'pesanan_growth' => '0%',
                'total_pembayaran' => 0,
                'total_pendapatan' => 'Rp 0',
                'total_pengguna' => 0,
                'total_mitra' => 0,
                'total_konflik' => 0,
                'rating_rata' => 0.0,
            ],
            'recent_orders' => [],
            'recent_activities' => [],
            'notifications' => [],
            'unread_notifications' => 0,

            'orders_detail' => $this->defaultOrdersDetail(),
            'last_updated' => time(),
        ];
    }

    /**
     * Ambil state terkini
     */
    /**
     * Ambil state terkini
     */
    public function getState(): array
    {
        $this->ensureInitialized();
        $content = File::get($this->storagePath);
        $state = json_decode($content, true);
        if (! is_array($state)) {
            $state = $this->defaultState();
            $this->saveState($state);
        }

        if (! isset($state['orders_detail']) || empty($state['orders_detail'])) {
            $state['orders_detail'] = $this->defaultOrdersDetail();
            $this->saveState($state);
        }

        // Sinkronisasi data riil dari database SQLite (User, Mitra, Order)
        try {
            $totalCustomers = User::where('role', 'customer')->count();
            $totalMitras = Mitra::count();
            $totalOrders = Order::count();
            $activeOrders = Order::whereNotIn('status', ['selesai', 'dibatalkan'])->count();
            $waitingOrders = Order::whereIn('status', ['menunggu', 'menunggu_mitra', 'nego', 'menunggu_konfirmasi_admin'])->count();
            $pendingPayments = Order::whereNull('payment_verified_at')
                ->whereNotIn('status', ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'])
                ->where(fn ($query) => $query->whereNotNull('payment_submitted_at')
                    ->orWhere('status', 'menunggu_konfirmasi_pembayaran')
                    ->orWhere('escrow_status', 'holding'))
                ->where(fn ($query) => $query->whereNull('escrow_status')->orWhere('escrow_status', '!=', 'refunded'))
                ->count();
            $verifiedOrders = fn () => Order::where(function ($query) {
                $query->whereNotNull('payment_verified_at')
                    ->orWhere(function ($legacyQuery) {
                        $legacyQuery->whereNull('payment_submitted_at')
                            ->where(function ($statusQuery) {
                                $statusQuery->whereIn('escrow_status', ['held', 'released_to_mitra', 'released'])
                                    ->orWhereIn('status', ['terjadwal', 'otw', 'berjalan', 'selesai']);
                            });
                    });
            })->whereNotIn('status', ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'])
                ->where(function ($query) {
                    $query->whereNull('escrow_status')->orWhere('escrow_status', '!=', 'refunded');
                });
            $releasedPayments = $verifiedOrders()
                ->whereIn('escrow_status', ['released_to_mitra', 'released'])
                ->count();
            $verifiedPaymentOrders = $verifiedOrders()->get(['admin_fee', 'nominal', 'biaya']);
            $totalPayments = $verifiedPaymentOrders->count();
            $adminBalance = $verifiedPaymentOrders->sum(function (Order $order): int {
                if ($order->admin_fee > 0) {
                    return (int) $order->admin_fee;
                }

                $amount = $order->nominal > 0
                    ? (int) $order->nominal
                    : (int) preg_replace('/[^0-9]/', '', (string) $order->biaya);

                return (int) round($amount * 0.10);
            });
            $conflictOrders = Order::where('status', 'konflik')->count();

            $state['stats']['total_pengguna'] = $totalCustomers;
            $state['stats']['total_mitra'] = $totalMitras;
            $state['stats']['total_pesanan'] = $totalOrders;
            $state['stats']['suruhan_aktif'] = $activeOrders;
            $state['stats']['menunggu_konfirmasi'] = $waitingOrders;
            $state['stats']['payment_pending'] = $pendingPayments;
            $state['stats']['payment_released'] = $releasedPayments;
            $state['stats']['total_pembayaran'] = $totalPayments;
            $state['stats']['admin_balance'] = 'Rp '.number_format($adminBalance, 0, ',', '.');
            $state['stats']['total_konflik'] = $conflictOrders;

            // Ambil pesanan nyata dari database untuk recent_orders
            $dbOrders = Order::orderBy('id', 'desc')->take(10)->get();
            if ($dbOrders->isNotEmpty()) {
                $statusMap = [
                    'selesai' => ['status' => 'Selesai', 'class' => 'status-success'],
                    'konflik' => ['status' => 'Konflik', 'class' => 'status-danger'],
                    'dibatalkan' => ['status' => 'Dibatalkan', 'class' => 'status-danger'],
                    'siap_bayar' => ['status' => 'Diproses', 'class' => 'status-warning'],
                    'terjadwal' => ['status' => 'Diproses', 'class' => 'status-warning'],
                    'otw' => ['status' => 'Diproses', 'class' => 'status-warning'],
                    'berjalan' => ['status' => 'Diproses', 'class' => 'status-warning'],
                    'menunggu_konfirmasi_pembayaran' => ['status' => 'Menunggu verifikasi pembayaran', 'class' => 'status-warning'],
                    'menunggu_konfirmasi_admin' => ['status' => 'Menunggu', 'class' => 'status-warning'],
                    'menunggu_keputusan' => ['status' => 'Menunggu', 'class' => 'status-warning'],
                    'menunggu_mitra' => ['status' => 'Menunggu', 'class' => 'status-warning'],
                    'nego' => ['status' => 'Menunggu', 'class' => 'status-warning'],
                ];

                $state['recent_orders'] = $dbOrders->map(function ($o) use ($statusMap) {
                    $st = $statusMap[$o->status] ?? ['status' => 'Menunggu', 'class' => 'status-warning'];

                    return [
                        'id' => $o->code,
                        'service' => $o->layanan,
                        'customer' => $o->customer_name,
                        'customer_phone' => $o->customer_phone,
                        'mitra' => $o->mitra_name ?? 'Mencari Mitra...',
                        'amount' => 'Rp '.$o->biaya,
                        'status' => $st['status'],
                        'status_class' => $st['class'],
                        'icon' => 'fa-clipboard-list',
                        'icon_class' => 'icon-blue',
                        'created_at' => $o->created_at ? $o->created_at->diffForHumans() : 'Baru saja',
                        'timestamp' => $o->created_at ? $o->created_at->timestamp : time(),
                        'raw_status' => $o->status,
                        'metode' => $o->metode,
                        'jadwal' => $o->jadwal,
                        'alamat' => $o->alamat,
                        'note' => $o->note,
                    ];
                })->all();
            }
        } catch (\Throwable $e) {
            // Fallback jika database belum termigrasi
        }

        return $state;
    }

    public function recordAdminPaymentVerification(Order $order): array
    {
        $state = $this->getState();
        $now = time();
        $amount = $order->nominal ?: (int) preg_replace('/[^0-9]/', '', (string) $order->biaya);
        $method = $order->payment_method ?: 'metode tidak tercatat';
        $cleanCode = ltrim($order->code, '#');

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Pembayaran dikonfirmasi Admin',
            'subtitle' => "{$order->code} • {$order->customer_name} • {$method} (Rp ".number_format($amount, 0, ',', '.').')',
            'type' => 'payment',
            'icon' => 'fa-circle-check',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'url' => '/admin/pembayaran/'.$cleanCode,
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Pembayaran Dikonfirmasi Admin',
            'message' => "Pembayaran {$order->code} telah diverifikasi Admin. Rp ".number_format($amount, 0, ',', '.').' tercatat di saldo Admin.',
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-circle-check',
        ]);
        $state['unread_notifications'] = ($state['unread_notifications'] ?? 0) + 1;

        $this->saveState($state);

        return $state;
    }

    /**
     * Simpan state ke file
     */
    public function saveState(array $state): void
    {
        $state['last_updated'] = time();
        File::put($this->storagePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Catat pesanan / suruhan baru dari customer
     */
    public function recordNewOrder(Order $order): array
    {
        $state = $this->getState();
        $now = time();

        $state['stats']['total_pesanan'] = Order::count();
        $state['stats']['suruhan_aktif'] = Order::whereNotIn('status', ['selesai', 'dibatalkan'])->count();

        // Aktivitas Live Admin
        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Suruhan baru masuk',
            'subtitle' => "{$order->code} • {$order->layanan} (Rp {$order->biaya})",
            'type' => 'order',
            'icon' => 'fa-cart-plus',
            'color_class' => 'bg-sky-50 text-sky-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        // Notifikasi Admin
        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => "Suruhan Baru {$order->code}",
            'message' => "Permintaan {$order->layanan} oleh {$order->customer_name} telah diajukan.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-cart-shopping',
        ]);
        $state['unread_notifications']++;

        $this->saveState($state);

        return $state;
    }

    /**
     * Notify Admin when a Customer reports a payment.
     */
    public function recordPaymentSubmission(Order $order, string $method): array
    {
        $state = $this->getState();
        $now = time();
        $cleanCode = ltrim($order->code, '#');

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Customer melaporkan pembayaran',
            'subtitle' => "{$order->code} • {$order->customer_name} membayar via {$method} (Rp ".number_format($order->nominal ?: (int) preg_replace('/[^0-9]/', '', (string) $order->biaya), 0, ',', '.').')',
            'type' => 'payment',
            'icon' => 'fa-money-bill-transfer',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'url' => '/admin/pembayaran/'.$cleanCode,
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Pembayaran Menunggu Verifikasi',
            'message' => "Customer {$order->customer_name} melaporkan pembayaran {$order->code} via {$method}. Menunggu konfirmasi Admin.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-money-bill-transfer',
        ]);
        $state['unread_notifications'] = ($state['unread_notifications'] ?? 0) + 1;

        $this->saveState($state);

        return $state;
    }

    public function recordCustomerWorkCompletion(Order $order): array
    {
        $state = $this->getState();
        $now = time();
        $cleanCode = ltrim($order->code, '#');

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Pekerjaan menunggu konfirmasi Admin',
            'subtitle' => "{$order->code} • {$order->customer_name} mengonfirmasi pekerjaan {$order->layanan} selesai.",
            'type' => 'order',
            'icon' => 'fa-clipboard-check',
            'color_class' => 'bg-indigo-50 text-indigo-600',
            'url' => '/admin/pesanan/'.$cleanCode,
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Konfirmasi Penyelesaian Menunggu',
            'message' => "Customer {$order->customer_name} mengonfirmasi pekerjaan {$order->code} selesai. Periksa dan konfirmasi penutupan pesanan.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-clipboard-check',
            'url' => '/admin/pesanan/'.$cleanCode,
        ]);
        $state['notifications'] = array_slice($state['notifications'], 0, 20);
        $state['unread_notifications'] = ($state['unread_notifications'] ?? 0) + 1;

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat pendaftaran customer baru
     */
    public function recordNewCustomerRegistration(User $user): array
    {
        $state = $this->getState();
        $now = time();

        $state['stats']['total_pengguna'] = User::where('role', 'customer')->count();

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Customer baru terdaftar',
            'subtitle' => "{$user->name} ({$user->phone}) bergabung sebagai customer",
            'type' => 'user',
            'icon' => 'fa-user-plus',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Customer Baru',
            'message' => "Pengguna {$user->name} baru saja mendaftar.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-user-check',
        ]);
        $state['unread_notifications']++;

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat pendaftaran mitra baru (menunggu verifikasi & ACC admin)
     */
    public function recordNewMitraRegistration(Mitra $mitra): array
    {
        $state = $this->getState();
        $now = time();

        $state['stats']['total_mitra'] = Mitra::count();

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Pendaftaran mitra baru',
            'subtitle' => "{$mitra->name} ({$mitra->mitra_id} • {$mitra->keahlian}) menunggu verifikasi berkas",
            'type' => 'partner',
            'icon' => 'fa-id-card',
            'color_class' => 'bg-amber-50 text-amber-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Mitra Baru Menunggu Verifikasi',
            'message' => "Calon mitra {$mitra->name} ({$mitra->mitra_id}) telah mendaftar dan membutuhkan peninjauan berkas.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-user-clock',
        ]);
        $state['unread_notifications']++;

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat mitra di-ACC / diverifikasi oleh admin
     */
    public function recordMitraApproved(Mitra $mitra): array
    {
        $state = $this->getState();
        $now = time();

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Mitra berhasil diverifikasi & disetujui (Approved)',
            'subtitle' => "{$mitra->name} ({$mitra->mitra_id}) kini aktif & tampil di portal mitra serta direktori publik",
            'type' => 'partner',
            'icon' => 'fa-circle-check',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Pendaftaran Diterima!',
            'message' => "Akun mitra {$mitra->name} ({$mitra->mitra_id}) telah disetujui. Akses penuh portal mitra telah dibuka.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-user-check',
        ]);
        $state['unread_notifications']++;

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat penolakan pendaftaran mitra oleh admin
     */
    public function recordMitraRejected(Mitra $mitra, string $reason, bool $canReapply): array
    {
        $state = $this->getState();
        $now = time();

        $reapplyNote = $canReapply ? ' (Mitra diizinkan kirim ulang data perbaikan)' : ' (Kirim ulang tidak diizinkan)';

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => 'Pendaftaran mitra ditolak (Rejected)',
            'subtitle' => "{$mitra->name} ({$mitra->mitra_id}) • Alasan: {$reason}{$reapplyNote}",
            'type' => 'partner',
            'icon' => 'fa-circle-xmark',
            'color_class' => 'bg-rose-50 text-rose-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => 'Pendaftaran Mitra Ditolak',
            'message' => "Calon mitra {$mitra->name} ditolak. Alasan: {$reason}.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-user-xmark',
        ]);
        $state['unread_notifications']++;

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat pembatalan / penolakan pesanan oleh Admin (Skenario B)
     */
    public function recordAdminCancelOrder(Order $order, string $reason): array
    {
        $state = $this->getState();
        $now = time();
        $numericId = preg_replace('/[^0-9]/', '', $order->code);

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Pesanan #{$order->code} dibatalkan oleh Admin",
            'subtitle' => "Alasan: {$reason} • Status: Dibatalkan (Escrow: Refund Diproses)",
            'type' => 'order',
            'icon' => 'fa-ban',
            'color_class' => 'bg-rose-50 text-rose-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => "Pesanan {$order->code} Ditolak/Dibatalkan",
            'message' => "Pesanan dibatalkan oleh Admin. Alasan: {$reason}. Notifikasi pembatalan dan proses refund dikirim ke customer.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-triangle-exclamation',
        ]);
        $state['unread_notifications']++;

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $order->code || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Dibatalkan';
                $ro['status_class'] = 'status-danger';
            }
        }

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat mitra mengundurkan diri / membatalkan tugas setelah kesepakatan (Skenario C Kasus 2)
     */
    public function recordMitraCancelOrder(Order $order, ?Mitra $mitra, string $reason): array
    {
        $state = $this->getState();
        $now = time();
        $numericId = preg_replace('/[^0-9]/', '', $order->code);
        $mitraName = $mitra?->name ?? 'Mitra';

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Mitra {$mitraName} mundur dari pesanan #{$order->code}",
            'subtitle' => "Alasan: {$reason} • Penalti performa tercatat • Status pesanan: Mencari mitra pengganti",
            'type' => 'order',
            'icon' => 'fa-user-minus',
            'color_class' => 'bg-amber-50 text-amber-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => "Mitra Berhalangan Hadir (#{$order->code})",
            'message' => "Mitra {$mitraName} membatalkan tugas ({$reason}). Customer dapat memilih mitra pengganti atau membatalkan pesanan.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-bell',
        ]);
        $state['unread_notifications']++;

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $order->code || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Menunggu';
                $ro['status_class'] = 'status-warning';
                $ro['mitra'] = 'Mencari Mitra Pengganti...';
            }
        }

        $this->saveState($state);

        return $state;
    }

    /**
     * Catat customer membatalkan pesanan (Skenario D)
     */
    public function recordCustomerCancelOrder(Order $order, string $reason, bool $hasAgreement): array
    {
        $state = $this->getState();
        $now = time();
        $numericId = preg_replace('/[^0-9]/', '', $order->code);

        $detailNote = $hasAgreement
            ? "Pembatalan pasca kesepakatan • Alasan: {$reason} • Pengembalian dana diproses"
            : 'Pembatalan sebelum kesepakatan mitra • Ditarik dari bursa suruhan';

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Customer membatalkan pesanan #{$order->code}",
            'subtitle' => $detailNote,
            'type' => 'order',
            'icon' => 'fa-circle-xmark',
            'color_class' => 'bg-rose-50 text-rose-600',
            'time' => 'Baru saja',
            'timestamp' => $now,
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);

        array_unshift($state['notifications'], [
            'id' => 'notif-'.uniqid(),
            'title' => "Pesanan #{$order->code} Dibatalkan Customer",
            'message' => "Customer {$order->customer_name} membatalkan suruhan. Alasan: {$reason}.",
            'read' => false,
            'time' => 'Baru saja',
            'icon' => 'fa-receipt',
        ]);
        $state['unread_notifications']++;

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $order->code || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Dibatalkan';
                $ro['status_class'] = 'status-danger';
            }
        }

        $this->saveState($state);

        return $state;
    }

    /**
     * Update status pesanan secara realtime
     */
    public function updateOrderStatus(string $id, string $status): array
    {
        $state = $this->getState();
        $updated = false;

        $statusClassMap = [
            'Menunggu' => 'status-warning',
            'Diproses' => 'status-warning',
            'Selesai' => 'status-success',
            'Dibatalkan' => 'status-danger',
        ];

        foreach ($state['recent_orders'] as &$order) {
            if ($order['id'] === $id) {
                $order['status'] = $status;
                $order['status_class'] = $statusClassMap[$status] ?? 'status-warning';
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // Tambahkan catatan ke aktivitas
            array_unshift($state['recent_activities'], [
                'id' => 'act-'.uniqid(),
                'title' => "Status pesanan {$id} diubah",
                'subtitle' => "Status diperbarui menjadi {$status}",
                'type' => 'order',
                'icon' => $status === 'Selesai' ? 'fa-circle-check' : ($status === 'Dibatalkan' ? 'fa-ban' : 'fa-arrows-rotate'),
                'color_class' => $status === 'Selesai' ? 'bg-green-50 text-green-600' : ($status === 'Dibatalkan' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600'),
                'time' => 'Baru saja',
                'timestamp' => time(),
            ]);

            $state['recent_activities'] = array_slice($state['recent_activities'], 0, 10);
            $this->saveState($state);
        }

        return $state;
    }

    /**
     * Tandai notifikasi telah dibaca
     */
    public function markNotificationsRead(): array
    {
        $state = $this->getState();
        foreach ($state['notifications'] as &$n) {
            $n['read'] = true;
        }
        $state['unread_notifications'] = 0;
        $this->saveState($state);

        return $state;
    }

    /**
     * Detail pesanan bawaan untuk panel admin
     */
    public function defaultOrdersDetail(): array
    {
        $orders = [];
        try {
            $dbOrders = Order::all();
            foreach ($dbOrders as $o) {
                $detail = $this->getOrderDetail($o->code);
                $clean = preg_replace('/[^0-9]/', '', $o->code) ?: (string) $o->id;
                $orders[$clean] = $detail;
                $orders[$o->code] = $detail;
            }
        } catch (\Throwable $e) {
        }

        return $orders;
    }

    /**
     * Dapatkan detail pesanan berdasarkan ID
     */
    public function getOrderDetail(string $id): array
    {
        $numericId = preg_replace('/[^0-9]/', '', $id);

        // 1. Cek langsung dari database Order
        try {
            $dbOrder = Order::where('code', $id)
                ->orWhere('code', '#'.$id)
                ->orWhere('code', 'like', '%'.$id.'%')
                ->when($numericId !== '', function ($q) use ($numericId) {
                    $q->orWhere('code', 'like', '%'.$numericId.'%');
                })
                ->first();

            if ($dbOrder) {
                $statusMap = [
                    'selesai' => ['status' => 'Selesai', 'class' => 'status-success'],
                    'konflik' => ['status' => 'Konflik', 'class' => 'status-conflict'],
                    'dibatalkan' => ['status' => 'Dibatalkan', 'class' => 'status-conflict'],
                    'cancelled_by_admin' => ['status' => 'Dibatalkan Admin', 'class' => 'status-conflict'],
                    'cancelled_by_customer' => ['status' => 'Dibatalkan Customer', 'class' => 'status-conflict'],
                    'waiting_for_partner' => ['status' => 'Menunggu Mitra Baru', 'class' => 'status-waiting'],
                    'siap_bayar' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'menunggu_pembayaran' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'menunggu_konfirmasi_pembayaran' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'terjadwal' => ['status' => 'Diproses', 'class' => 'status-success'],
                    'otw' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'berjalan' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'menunggu_konfirmasi_customer' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'menunggu_konfirmasi_admin_selesai' => ['status' => 'Diproses', 'class' => 'status-process'],
                    'menunggu_konfirmasi_admin' => ['status' => 'Menunggu', 'class' => 'status-waiting'],
                    'menunggu_keputusan' => ['status' => 'Menunggu', 'class' => 'status-waiting'],
                    'menunggu_mitra' => ['status' => 'Menunggu', 'class' => 'status-waiting'],
                    'nego' => ['status' => 'Menunggu', 'class' => 'status-waiting'],
                ];
                $st = $statusMap[$dbOrder->status] ?? ['status' => 'Menunggu', 'class' => 'status-waiting'];
                $isRefunded = in_array($dbOrder->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
                    || $dbOrder->escrow_status === 'refunded';
                $isPaymentConfirmed = ! $isRefunded && ($dbOrder->payment_verified_at !== null
                    || ($dbOrder->payment_submitted_at === null
                        && (in_array($dbOrder->status, ['terjadwal', 'otw', 'berjalan', 'menunggu_konfirmasi_customer', 'menunggu_konfirmasi_admin_selesai', 'selesai'])
                            || in_array($dbOrder->escrow_status, ['held', 'released_to_mitra', 'released']))));
                $isOrderConfirmed = in_array($dbOrder->status, ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran', 'terjadwal', 'otw', 'berjalan', 'menunggu_konfirmasi_customer', 'menunggu_konfirmasi_admin_selesai', 'selesai']);

                return [
                    'id' => $dbOrder->code,
                    'code' => str_starts_with($dbOrder->code, '#') ? $dbOrder->code : '#'.$dbOrder->code,
                    'workflow_status' => $dbOrder->status,
                    'category' => 'Layanan SayaBantu',
                    'service' => $dbOrder->layanan,
                    'customer' => $dbOrder->customer_name,
                    'customer_initial' => mb_substr($dbOrder->customer_name, 0, 1),
                    'customer_phone' => $dbOrder->customer_phone,
                    'mitra' => $dbOrder->mitra_name ?? 'Mencari Mitra...',
                    'mitra_initial' => mb_substr($dbOrder->mitra_name ?? 'M', 0, 1),
                    'mitra_phone' => $dbOrder->mitra_phone ?? '-',
                    'date' => $dbOrder->created_at ? $dbOrder->created_at->format('d F Y') : date('d F Y'),
                    'time' => $dbOrder->jadwal,
                    'address' => $dbOrder->alamat,
                    'description' => $dbOrder->note ?: 'Pemesanan layanan '.$dbOrder->layanan.' melalui aplikasi SayaBantu.',
                    'tags' => ['Layanan Resmi', 'SayaBantu Terverifikasi'],
                    'service_price' => 'Rp '.$dbOrder->biaya,
                    'platform_fee' => 'Rp 0',
                    'total_price' => 'Rp '.$dbOrder->biaya,
                    'payment_method' => $dbOrder->payment_method ?: 'Belum tercatat',
                    'payment_time' => $dbOrder->payment_submitted_at?->format('d F Y • H.i') ?: 'Belum tercatat',
                    'payment_status' => $isRefunded ? 'dikembalikan' : ($isPaymentConfirmed ? 'terkonfirmasi' : 'menunggu'),
                    'payment_submitted' => $dbOrder->payment_submitted_at !== null,
                    'status' => $st['status'],
                    'status_class' => $st['class'],
                    'notes' => $dbOrder->note,
                    'timeline' => [
                        [
                            'key' => 'created',
                            'title' => 'Pesanan dibuat',
                            'time' => $dbOrder->created_at ? $dbOrder->created_at->format('d F Y • H.i') : date('d F Y • H.i'),
                            'completed' => true,
                        ],
                        [
                            'key' => 'accepted',
                            'title' => 'Mitra menerima pekerjaan',
                            'time' => $dbOrder->created_at ? $dbOrder->created_at->addMinutes(15)->format('d F Y • H.i') : date('d F Y • H.i'),
                            'completed' => ! empty($dbOrder->mitra_name),
                        ],
                        [
                            'key' => 'payment',
                            'title' => $isPaymentConfirmed ? 'Pembayaran dikonfirmasi Admin' : 'Pembayaran pesanan',
                            'time' => $isPaymentConfirmed ? ($dbOrder->payment_verified_at?->format('d F Y • H.i') ?: 'Waktu tidak tercatat') : ($dbOrder->payment_submitted_at ? 'Menunggu konfirmasi Admin' : 'Menunggu Customer membayar'),
                            'completed' => $isPaymentConfirmed,
                        ],
                        [
                            'key' => 'started',
                            'title' => $isOrderConfirmed ? 'Pesanan Dikonfirmasi' : 'Layanan dimulai',
                            'time' => $isOrderConfirmed ? 'Dikonfirmasi oleh Admin' : 'Belum dimulai',
                            'completed' => $isOrderConfirmed,
                        ],
                    ],
                    'flow_actions' => [
                        'payment_confirmed' => $isPaymentConfirmed,
                        'order_confirmed' => $isOrderConfirmed,
                        'in_conflict' => $dbOrder->status === 'konflik',
                        'order_completed' => $dbOrder->status === 'selesai',
                    ],
                ];
            }
        } catch (\Throwable $e) {
        }

        $state = $this->getState();
        $ordersDetail = $state['orders_detail'] ?? [];

        if (isset($ordersDetail[$id])) {
            return $ordersDetail[$id];
        }

        if ($numericId !== '' && isset($ordersDetail[$numericId])) {
            return $ordersDetail[$numericId];
        }

        // Cek fallback di recent_orders
        $fromRecent = collect($state['recent_orders'] ?? [])->firstWhere('id', $id)
            ?? collect($state['recent_orders'] ?? [])->firstWhere('id', 'SB-'.str_pad($numericId, 5, '0', STR_PAD_LEFT));

        if ($fromRecent) {
            return [
                'id' => $id,
                'code' => str_starts_with($fromRecent['id'], '#') ? $fromRecent['id'] : '#'.$fromRecent['id'],
                'category' => 'Layanan SayaBantu',
                'service' => $fromRecent['service'] ?? 'Layanan Jasa',
                'customer' => $fromRecent['customer'] ?? 'Customer',
                'customer_initial' => mb_substr($fromRecent['customer'] ?? 'C', 0, 1),
                'customer_phone' => '0812-'.rand(1000, 9999).'-'.rand(1000, 9999),
                'mitra' => $fromRecent['mitra'] ?? 'Mitra Panggilan',
                'mitra_initial' => mb_substr($fromRecent['mitra'] ?? 'M', 0, 1),
                'mitra_phone' => '0813-'.rand(1000, 9999).'-'.rand(1000, 9999),
                'date' => date('d F Y'),
                'time' => '10.00 - 12.00',
                'address' => 'Jl. Malioboro No. 10, Yogyakarta',
                'description' => 'Pemesanan layanan '.$fromRecent['service'].' melalui aplikasi SayaBantu.',
                'tags' => ['Layanan Kilat', 'Jasa Terverifikasi'],
                'service_price' => $fromRecent['amount'] ?? 'Rp 150.000',
                'platform_fee' => 'Rp 0',
                'total_price' => $fromRecent['amount'] ?? 'Rp 150.000',
                'payment_method' => 'QRIS',
                'payment_time' => date('d F Y • H.i'),
                'payment_status' => 'terkonfirmasi',
                'status' => $fromRecent['status'] ?? 'Diproses',
                'status_class' => $fromRecent['status_class'] ?? 'status-warning',
                'conflict_reason' => null,
                'conflict_time' => null,
                'notes' => 'Pesanan aktif sedang ditangani.',
                'timeline' => [
                    ['key' => 'created', 'title' => 'Pesanan dibuat', 'time' => 'Baru saja', 'completed' => true],
                    ['key' => 'accepted', 'title' => 'Mitra menerima pekerjaan', 'time' => 'Baru saja', 'completed' => true],
                    ['key' => 'payment', 'title' => 'Pembayaran terkonfirmasi', 'time' => 'Dikonfirmasi oleh Admin', 'completed' => true],
                    ['key' => 'started', 'title' => 'Layanan dimulai', 'time' => 'Belum dimulai', 'completed' => false],
                ],
            ];
        }

        $defaults = $this->defaultOrdersDetail();

        return $defaults['1024'] ?? [];
    }

    /**
     * Konfirmasi pesanan oleh Admin
     */
    public function confirmOrder(string $id): array
    {
        $state = $this->getState();
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $cleanId = ltrim($id, '#');
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$cleanId]) ? $cleanId : (isset($state['orders_detail'][$numericId]) ? $numericId : $cleanId));

        $order = $this->getOrderDetail($id);
        $order['status'] = 'Dikonfirmasi';
        $order['status_class'] = 'status-success';
        if (! isset($order['timeline']) || ! is_array($order['timeline'])) {
            $order['timeline'] = [];
        }

        $nowTime = date('d F Y • H.i');
        foreach ($order['timeline'] as &$step) {
            if ($step['key'] === 'started' || $step['title'] === 'Layanan dimulai' || $step['title'] === 'Pesanan Dikonfirmasi') {
                $step['title'] = 'Pesanan Dikonfirmasi';
                $step['time'] = "Dikonfirmasi oleh Admin • {$nowTime}";
                $step['completed'] = true;
            }
        }

        $state['orders_detail'][$targetKey] = $order;
        $state['orders_detail'][$cleanId] = $order;
        if ($numericId !== '') {
            $state['orders_detail'][$numericId] = $order;
        }

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $id || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Dikonfirmasi';
                $ro['status_class'] = 'status-success';
            }
        }

        // Sinkronisasi status pesanan ke database
        try {
            $cleanCode = ltrim($id, '#');
            $dbOrder = Order::where('code', $id)
                ->orWhere('code', '#'.$cleanCode)
                ->orWhere('code', $cleanCode)
                ->orWhere('id', $id)
                ->when($numericId !== '', fn ($q) => $q->orWhere('code', 'like', "%{$numericId}%"))
                ->first();
            if ($dbOrder) {
                // Jangan reset ke menunggu_pembayaran jika pembayaran sudah lunas / terkonfirmasi
                $isAlreadyPaid = in_array($dbOrder->status, ['terjadwal', 'otw', 'berjalan', 'selesai'])
                    || $dbOrder->escrow_status === 'held'
                    || (int) ($dbOrder->step ?? 0) >= 8;

                if ($isAlreadyPaid) {
                    $newStatus = in_array($dbOrder->status, ['otw', 'berjalan', 'selesai']) ? $dbOrder->status : 'terjadwal';
                    $newStep = max(8, (int) ($dbOrder->step ?? 8));
                    $dbOrder->update([
                        'status' => $newStatus,
                        'step' => $newStep,
                        'escrow_status' => 'held',
                    ]);
                } else {
                    $dbOrder->update([
                        'status' => 'menunggu_pembayaran',
                        'step' => 7,
                    ]);
                }

                try {
                    ChatMessage::create([
                        'order_code' => $cleanCode,
                        'sender_type' => 'system',
                        'sender_name' => 'Admin SayaBantu',
                        'message' => $isAlreadyPaid
                            ? 'Pesanan & jadwal pengerjaan telah dikonfirmasi Admin.'
                            : 'Kesepakatan harga & detail pesanan telah disetujui Admin! Customer dapat melanjutkan ke pembayaran.',
                        'message_type' => 'deal_agreed',
                        'metadata' => [
                            'status' => $isAlreadyPaid ? 'terjadwal' : 'menunggu_pembayaran',
                            'confirmed_by' => 'admin',
                        ],
                        'is_read' => false,
                    ]);
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
        }

        $srvTitle = $order['service'] ?? ($order['layanan'] ?? 'Suruhan Jasa');
        $custName = $order['customer'] ?? ($order['customer_name'] ?? 'Customer');
        $mtrName = $order['mitra'] ?? ($order['mitra_name'] ?? 'Mitra');

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Pesanan #SB{$numericId} dikonfirmasi admin",
            'subtitle' => "{$srvTitle} ({$custName} & {$mtrName})",
            'type' => 'order',
            'icon' => 'fa-clipboard-check',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'time' => 'Baru saja',
            'timestamp' => time(),
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 10);

        $this->saveState($state);

        return [
            'order' => $order,
            'state' => $state,
        ];
    }

    /**
     * Tandai pesanan sebagai konflik
     */
    public function markOrderConflict(string $id, string $reason): array
    {
        $state = $this->getState();
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $cleanId = ltrim($id, '#');
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$cleanId]) ? $cleanId : (isset($state['orders_detail'][$numericId]) ? $numericId : $cleanId));

        $order = $this->getOrderDetail($id);
        $nowTime = date('d F Y • H.i');

        $order['status'] = 'Konflik';
        $order['status_class'] = 'status-danger';
        $order['conflict_reason'] = $reason !== '' ? $reason : 'Ditandai konflik oleh Admin';
        $order['conflict_time'] = $nowTime;
        if (! isset($order['timeline']) || ! is_array($order['timeline'])) {
            $order['timeline'] = [];
        }

        foreach ($order['timeline'] as &$step) {
            if ($step['key'] === 'started' || str_contains(strtolower($step['title']), 'layanan') || str_contains(strtolower($step['title']), 'konflik')) {
                $step['title'] = 'Status Konflik Mediasi';
                $step['time'] = "Ditandai oleh Admin • {$nowTime}";
                $step['completed'] = false;
            }
        }

        $state['orders_detail'][$targetKey] = $order;
        if (isset($state['stats']['total_konflik'])) {
            $state['stats']['total_konflik']++;
        }

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $id || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Konflik';
                $ro['status_class'] = 'status-danger';
            }
        }

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Pesanan #SB{$numericId} masuk status konflik",
            'subtitle' => "Alasan: {$order['conflict_reason']}",
            'type' => 'order',
            'icon' => 'fa-triangle-exclamation',
            'color_class' => 'bg-red-50 text-red-600',
            'time' => 'Baru saja',
            'timestamp' => time(),
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 10);

        // Sinkronisasi status pesanan ke database
        try {
            $cleanCode = ltrim($id, '#');
            $dbOrder = Order::where('code', $id)
                ->orWhere('code', '#'.$cleanCode)
                ->orWhere('code', $cleanCode)
                ->orWhere('id', $id)
                ->when($numericId !== '', fn ($q) => $q->orWhere('code', 'like', "%{$numericId}%"))
                ->first();
            if ($dbOrder) {
                $dbOrder->update([
                    'status' => 'konflik',
                    'cancellation_reason' => $order['conflict_reason'],
                ]);

                try {
                    ChatMessage::create([
                        'order_code' => $cleanCode,
                        'sender_type' => 'system',
                        'sender_name' => 'Admin SayaBantu',
                        'message' => 'Pesanan ditandai sebagai konflik mediasi oleh Admin. Alasan: '.$order['conflict_reason'],
                        'message_type' => 'system',
                    ]);
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
        }

        $this->saveState($state);

        return [
            'order' => $order,
            'state' => $state,
        ];
    }

    /**
     * Selesaikan status konflik pesanan (mediasi selesai)
     */
    public function resolveOrderConflict(string $id): array
    {
        $state = $this->getState();
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $cleanId = ltrim($id, '#');
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$cleanId]) ? $cleanId : (isset($state['orders_detail'][$numericId]) ? $numericId : $cleanId));

        $order = $this->getOrderDetail($id);
        $nowTime = date('d F Y • H.i');

        $order['status'] = 'Diproses';
        $order['status_class'] = 'status-warning';
        $order['conflict_reason'] = null;
        $order['conflict_time'] = null;
        if (! isset($order['timeline']) || ! is_array($order['timeline'])) {
            $order['timeline'] = [];
        }

        foreach ($order['timeline'] as &$step) {
            if ($step['key'] === 'started' || str_contains(strtolower($step['title']), 'konflik')) {
                $step['title'] = 'Layanan dilanjutkan';
                $step['time'] = "Mediasi Selesai • {$nowTime}";
                $step['completed'] = true;
            }
        }

        $state['orders_detail'][$targetKey] = $order;
        if (isset($state['stats']['total_konflik']) && $state['stats']['total_konflik'] > 0) {
            $state['stats']['total_konflik']--;
        }

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $id || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Diproses';
                $ro['status_class'] = 'status-warning';
            }
        }

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Konflik pesanan #SB{$numericId} terselesaikan",
            'subtitle' => 'Status dikembalikan ke Diproses',
            'type' => 'order',
            'icon' => 'fa-shield-halved',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'time' => 'Baru saja',
            'timestamp' => time(),
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 10);

        // Sinkronisasi status pesanan ke database
        try {
            $cleanCode = ltrim($id, '#');
            $dbOrder = Order::where('code', $id)
                ->orWhere('code', '#'.$cleanCode)
                ->orWhere('code', $cleanCode)
                ->orWhere('id', $id)
                ->when($numericId !== '', fn ($q) => $q->orWhere('code', 'like', "%{$numericId}%"))
                ->first();
            if ($dbOrder) {
                $targetStatus = ((int) ($dbOrder->step ?? 0) >= 10) ? 'berjalan' : (((int) ($dbOrder->step ?? 0) >= 9) ? 'otw' : 'terjadwal');
                $dbOrder->update([
                    'status' => $targetStatus,
                    'cancellation_reason' => null,
                ]);

                try {
                    ChatMessage::create([
                        'order_code' => $cleanCode,
                        'sender_type' => 'system',
                        'sender_name' => 'Admin SayaBantu',
                        'message' => 'Konflik pesanan telah diselesaikan melalui mediasi Admin. Layanan pekerjaan dapat dilanjutkan.',
                        'message_type' => 'system',
                    ]);
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
        }

        $this->saveState($state);

        return [
            'order' => $order,
            'state' => $state,
        ];
    }

    /**
     * Simpan catatan internal admin untuk pesanan
     */
    public function saveOrderNotes(string $id, string $notes): array
    {
        $state = $this->getState();
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$numericId]) ? $numericId : '1024');

        $order = $state['orders_detail'][$targetKey] ?? $this->getOrderDetail($id);
        $order['notes'] = $notes;
        $order['notes_updated_at'] = date('d F Y • H.i');

        $state['orders_detail'][$targetKey] = $order;
        $this->saveState($state);

        return [
            'order' => $order,
            'state' => $state,
        ];
    }

    /**
     * Batalkan konfirmasi pesanan (kembali ke status Diproses belum dikonfirmasi)
     */
    public function unconfirmOrder(string $id): array
    {
        $state = $this->getState();
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $cleanId = ltrim($id, '#');
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$cleanId]) ? $cleanId : (isset($state['orders_detail'][$numericId]) ? $numericId : $cleanId));

        $order = $this->getOrderDetail($id);
        $order['status'] = 'Diproses';
        $order['status_class'] = 'status-warning';
        if (! isset($order['timeline']) || ! is_array($order['timeline'])) {
            $order['timeline'] = [];
        }

        foreach ($order['timeline'] as &$step) {
            if ($step['key'] === 'started' || $step['title'] === 'Layanan dimulai' || $step['title'] === 'Pesanan Dikonfirmasi') {
                $step['title'] = 'Layanan dimulai';
                $step['time'] = 'Belum dimulai';
                $step['completed'] = false;
            }
        }

        $state['orders_detail'][$targetKey] = $order;

        // Sinkronisasi status pesanan ke database
        try {
            $cleanCode = ltrim($id, '#');
            $dbOrder = Order::where('code', $id)
                ->orWhere('code', '#'.$cleanCode)
                ->orWhere('code', $cleanCode)
                ->orWhere('id', $id)
                ->when($numericId !== '', fn ($q) => $q->orWhere('code', 'like', "%{$numericId}%"))
                ->first();
            if ($dbOrder) {
                $dbOrder->update([
                    'status' => 'menunggu_konfirmasi_admin',
                    'step' => 6,
                ]);

                try {
                    ChatMessage::create([
                        'order_code' => $cleanCode,
                        'sender_type' => 'system',
                        'sender_name' => 'Admin SayaBantu',
                        'message' => 'Konfirmasi pesanan ditinjau ulang oleh Admin.',
                        'message_type' => 'system',
                    ]);
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
        }

        $this->saveState($state);

        return [
            'order' => $order,
            'state' => $state,
        ];
    }

    /**
     * Konfirmasi akhir penyelesaian dan penutupan pesanan oleh Admin (Tahap 11)
     */
    public function confirmCompletion(string $id): array
    {
        $numericId = preg_replace('/[^0-9]/', '', $id);
        $cleanId = ltrim($id, '#');
        $dbOrder = Order::query()
            ->where('code', $id)
            ->orWhere('code', '#'.$cleanId)
            ->orWhere('code', $cleanId)
            ->orWhere('id', $id)
            ->first();

        if ($dbOrder === null) {
            return [
                'success' => false,
                'http_status' => 404,
                'message' => 'Pesanan tidak ditemukan.',
            ];
        }

        if ($dbOrder->status !== 'menunggu_konfirmasi_admin_selesai') {
            return [
                'success' => false,
                'http_status' => 409,
                'message' => 'Pekerjaan harus dikonfirmasi selesai oleh Customer sebelum Admin dapat menyelesaikannya.',
            ];
        }

        if ($dbOrder->payment_verified_at === null || $dbOrder->escrow_status !== 'held') {
            return [
                'success' => false,
                'http_status' => 409,
                'message' => 'Pembayaran harus dikonfirmasi Admin dan ditahan di escrow sebelum pekerjaan ditutup.',
            ];
        }

        $state = $this->getState();
        $targetKey = isset($state['orders_detail'][$id]) ? $id : (isset($state['orders_detail'][$cleanId]) ? $cleanId : (isset($state['orders_detail'][$numericId]) ? $numericId : $cleanId));

        $order = $this->getOrderDetail($id);
        $order['status'] = 'Selesai';
        $order['status_class'] = 'status-success';
        if (! isset($order['timeline']) || ! is_array($order['timeline'])) {
            $order['timeline'] = [];
        }

        $nowTime = date('d F Y • H.i');
        foreach ($order['timeline'] as &$step) {
            if ($step['key'] === 'started' || str_contains(strtolower($step['title']), 'layanan') || str_contains(strtolower($step['title']), 'selesai')) {
                $step['title'] = 'Pesanan Selesai';
                $step['time'] = "Ditutup oleh Admin • {$nowTime}";
                $step['completed'] = true;
            }
        }

        $state['orders_detail'][$targetKey] = $order;

        foreach ($state['recent_orders'] as &$ro) {
            if ($ro['id'] === $id || preg_replace('/[^0-9]/', '', $ro['id']) === $numericId) {
                $ro['status'] = 'Selesai';
                $ro['status_class'] = 'status-success';
            }
        }

        $dbOrder->update([
            'status' => 'selesai',
            'step' => 11,
            'escrow_status' => 'held',
            'escrow_hold_reason' => 'Menunggu Admin meneruskan dana setelah konfirmasi penyelesaian pekerjaan',
        ]);

        if ($dbOrder->mitra_id || $dbOrder->mitra_name || $dbOrder->mitra_phone) {
            $mitra = Mitra::where('mitra_id', $dbOrder->mitra_id)
                ->orWhere('phone', $dbOrder->mitra_phone)
                ->orWhere('name', $dbOrder->mitra_name)
                ->first();
            $mitra?->increment('tasks');
        }

        ChatMessage::create([
            'order_code' => $cleanId,
            'sender_type' => 'system',
            'sender_name' => 'Admin SayaBantu',
            'message' => 'Admin telah mengonfirmasi pekerjaan selesai. Dana tetap ditahan di escrow sampai diteruskan ke Mitra.',
            'message_type' => 'system',
        ]);

        array_unshift($state['recent_activities'], [
            'id' => 'act-'.uniqid(),
            'title' => "Pesanan #SB{$numericId} selesai secara tuntas",
            'subtitle' => 'Pekerjaan dikonfirmasi oleh Customer dan Admin • Dana tetap ditahan di escrow',
            'type' => 'order',
            'icon' => 'fa-circle-check',
            'color_class' => 'bg-emerald-50 text-emerald-600',
            'time' => 'Baru saja',
            'timestamp' => time(),
        ]);
        $state['recent_activities'] = array_slice($state['recent_activities'], 0, 10);

        $this->saveState($state);

        return [
            'success' => true,
            'order' => $order,
            'state' => $state,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use App\Services\AdminRealtimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminDashboardController extends Controller
{
    public function __construct(public AdminRealtimeService $realtimeService) {}

    /**
     * Dashboard Utama Admin
     */
    public function dashboard()
    {
        $state = $this->realtimeService->getState();
        $pendingCompletionOrders = Order::where('status', 'menunggu_konfirmasi_admin_selesai')
            ->latest('updated_at')
            ->get();

        return view('admin.dashboard', [
            'totalPengguna' => $state['stats']['total_pengguna'] ?? 0,
            'totalMitra' => $state['stats']['total_mitra'] ?? 0,
            'suruhanAktif' => $state['stats']['suruhan_aktif'] ?? 0,
            'menungguKonfirmasi' => $state['stats']['menunggu_konfirmasi'] ?? 0,
            'totalPesanan' => $state['stats']['total_pesanan'] ?? 0,
            'totalPembayaran' => $state['stats']['total_pembayaran'] ?? 0,
            'totalKonflik' => $state['stats']['total_konflik'] ?? 0,
            'recentOrders' => $state['recent_orders'] ?? [],
            'recentActivities' => $state['recent_activities'] ?? [],
            'stats' => $state['stats'] ?? [],
            'pendingCompletionOrders' => $pendingCompletionOrders,
        ]);
    }

    /**
     * Halaman Kelola Seluruh Pesanan
     */
    public function pesanan(Request $request)
    {
        $state = $this->realtimeService->getState();
        $dbOrders = Order::orderBy('id', 'desc')->get();

        $countSemua = $dbOrders->count();
        $countMenunggu = $dbOrders->filter(fn ($o) => in_array($o->status, ['menunggu', 'menunggu_mitra', 'nego', 'menunggu_penawaran', 'menunggu_konfirmasi_admin', 'menunggu_keputusan', 'waiting_for_partner']))->count();
        $countDiproses = $dbOrders->filter(fn ($o) => in_array($o->status, ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi', 'diproses', 'menunggu_konfirmasi_customer', 'menunggu_konfirmasi_admin_selesai']))->count();
        $countSelesai = $dbOrders->where('status', 'selesai')->count();
        $countKonflik = $dbOrders->filter(fn ($o) => in_array($o->status, ['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer']))->count();

        return view('admin.pesanan.index', [
            'orders' => $state['recent_orders'] ?? [],
            'ordersDetail' => $state['orders_detail'] ?? [],
            'stats' => $state['stats'] ?? [],
            'dbOrders' => $dbOrders,
            'countSemua' => $countSemua,
            'countMenunggu' => $countMenunggu,
            'countDiproses' => $countDiproses,
            'countSelesai' => $countSelesai,
            'countKonflik' => $countKonflik,
        ]);
    }

    /**
     * Halaman Detail Pesanan
     */
    public function pesananDetail($id)
    {
        $order = $this->realtimeService->getOrderDetail((string) $id);

        return view('admin.pesanan.detail', [
            'id' => $id,
            'order' => $order,
        ]);
    }

    /**
     * Halaman Kelola Seluruh Pembayaran
     */
    public function pembayaran()
    {
        $state = $this->realtimeService->getState();
        $orders = Order::where(function ($query) {
            $query->whereIn('escrow_status', ['holding', 'held', 'released_to_mitra', 'released', 'refunded'])
                ->orWhereIn('status', ['menunggu_konfirmasi_pembayaran', 'terjadwal', 'otw', 'berjalan', 'selesai'])
                ->orWhereNotNull('payment_submitted_at');
        })->orderBy('id', 'desc')->get();

        $totalDanaMasuk = 0;
        $totalDanaDitahan = 0;
        $totalDanaKeluar = 0;
        $saldoAdmin = 0;
        $countSemua = $orders->count();
        $countMenunggu = 0;
        $countBerhasil = 0;
        $countGagal = 0;
        $paymentSummaryRows = [];

        foreach ($orders as $o) {
            $nom = $o->nominal > 0 ? $o->nominal : (int) preg_replace('/[^0-9]/', '', (string) $o->biaya);
            $adminFee = (int) round($nom * 0.10);
            $mitraFee = (int) round($nom * 0.90);

            $o->nominal = $nom;
            $o->admin_fee = $o->admin_fee > 0 ? $o->admin_fee : $adminFee;
            $o->mitra_fee = $o->mitra_fee > 0 ? $o->mitra_fee : $mitraFee;

            $isRefund = in_array($o->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan']) || $o->escrow_status === 'refunded';
            $hasPaymentSubmission = $o->payment_submitted_at !== null;
            $isVerified = ! $isRefund && ($o->payment_verified_at !== null || (! $hasPaymentSubmission && (
                in_array($o->escrow_status, ['held', 'released_to_mitra', 'released'])
                || in_array($o->status, ['terjadwal', 'otw', 'berjalan', 'selesai'])
            )));
            $isReleased = $isVerified && in_array($o->escrow_status, ['released_to_mitra', 'released']);
            $isPendingVerification = ! $isRefund && ! $isVerified && (
                $hasPaymentSubmission || $o->status === 'menunggu_konfirmasi_pembayaran' || $o->escrow_status === 'holding'
            );
            $o->payment_is_pending = $isPendingVerification;
            $o->payment_is_verified = $isVerified;
            $cleanCode = ltrim($o->code, '#');

            $paymentSummaryRows[] = [
                'id' => '#PAY-'.$cleanCode,
                'order' => $o->code.' • '.$o->customer_name,
                'amount' => $isRefund && $o->refund_amount > 0 ? $o->refund_amount : $nom,
                'admin_fee' => $o->admin_fee,
                'mitra_fee' => $o->mitra_fee,
                'method' => $o->payment_method ?: 'Metode belum tercatat',
                'group' => $isRefund ? 'refund' : ($isPendingVerification ? 'pending' : ($isReleased ? 'keluar' : 'ditahan')),
                'status' => $isRefund ? 'Refund' : ($isPendingVerification ? 'Menunggu konfirmasi Admin' : ($isReleased ? 'Diteruskan ke Mitra' : 'Tercatat di Escrow')),
            ];

            if ($isRefund) {
                $countGagal++;
            } elseif ($isPendingVerification) {
                $countMenunggu++;
            } elseif ($isReleased) {
                $countBerhasil++;
                $totalDanaMasuk += $nom;
                $totalDanaKeluar += $o->mitra_fee;
                $saldoAdmin += $o->admin_fee;
            } elseif ($isVerified) {
                $countBerhasil++;
                $totalDanaMasuk += $nom;
                $totalDanaDitahan += $nom;
                $saldoAdmin += $o->admin_fee;
                if (empty($o->escrow_hold_reason)) {
                    $o->escrow_hold_reason = 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai';
                }
            }
        }

        return view('admin.pembayaran.index', [
            'stats' => $state['stats'] ?? [],
            'recentActivities' => $state['recent_activities'] ?? [],
            'orders' => $orders,
            'paymentSummaryRows' => $paymentSummaryRows,
            'saldoAdmin' => $saldoAdmin,
            'totalDanaMasuk' => $totalDanaMasuk,
            'totalDanaDitahan' => $totalDanaDitahan,
            'totalDanaKeluar' => $totalDanaKeluar,
            'countSemua' => $countSemua,
            'countMenunggu' => $countMenunggu,
            'countBerhasil' => $countBerhasil,
            'countGagal' => $countGagal,
        ]);
    }

    /**
     * Halaman Detail Pembayaran & Penyaluran Dana ke Mitra
     */
    public function pembayaranDetail($id)
    {
        $state = $this->realtimeService->getState();
        $cleanId = ltrim((string) $id, '#');
        $numericId = preg_replace('/[^0-9]/', '', $cleanId);

        $order = Order::where('code', $id)
            ->orWhere('code', '#'.$cleanId)
            ->orWhere('code', $cleanId)
            ->orWhere('code', 'like', '%'.$cleanId.'%')
            ->when($numericId !== '', fn ($q) => $q->orWhere('code', 'like', '%'.$numericId.'%')->orWhere('id', $numericId))
            ->first();

        if (! $order) {
            $order = Order::orderBy('id', 'desc')->first();
        }

        if ($order) {
            $nom = $order->nominal > 0 ? $order->nominal : (int) preg_replace('/[^0-9]/', '', (string) $order->biaya);
            $adminFee = (int) round($nom * 0.10);
            $mitraFee = (int) round($nom * 0.90);
            $isRefund = in_array($order->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
                || $order->escrow_status === 'refunded';
            $hasPaymentSubmission = $order->payment_submitted_at !== null;
            $isVerified = ! $isRefund && ($order->payment_verified_at !== null || (! $hasPaymentSubmission && (
                in_array($order->escrow_status, ['held', 'released_to_mitra', 'released'])
                || in_array($order->status, ['terjadwal', 'otw', 'berjalan', 'selesai'])
            )));
            $isReleased = $isVerified && in_array($order->escrow_status, ['released_to_mitra', 'released']);
            $isPendingVerification = ! $isRefund && ! $isVerified && (
                $hasPaymentSubmission || $order->status === 'menunggu_konfirmasi_pembayaran' || $order->escrow_status === 'holding'
            );

            $cleanOrderCode = ltrim($order->code, '#');
            $payment = [
                'id' => $id,
                'kode' => 'PAY-'.$cleanOrderCode,
                'order_id' => $order->code,
                'clean_order_code' => $cleanOrderCode,
                'layanan' => $order->layanan,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone ?: '0812-3344-5566',
                'mitra_name' => $order->mitra_name ?: 'Mitra Terpilih',
                'mitra_id' => $order->mitra_id ?: 'MTR-001',
                'mitra_phone' => $order->mitra_phone ?: '0812-3456-7890',
                'mitra_bank' => 'BCA',
                'mitra_rekening' => '8271 9920 1123',
                'mitra_rekening_an' => $order->mitra_name ?: 'Mitra SayaBantu',
                'total_bayar' => $nom,
                'biaya_platform' => $adminFee,
                'nominal_mitra' => $mitraFee,
                'metode' => $order->payment_method ?: 'Belum dicatat',
                'kode_va' => $cleanOrderCode,
                'status_bayar_customer' => $isPendingVerification ? 'menunggu_verifikasi' : ($isVerified ? 'lunas' : 'belum_dibayar'),
                'is_pending_verification' => $isPendingVerification,
                'is_verified' => $isVerified,
                'is_work_completed' => $order->status === 'selesai',
                'can_release_escrow' => $order->payment_verified_at !== null
                    && $order->escrow_status === 'held'
                    && $order->status === 'selesai'
                    && ! $isRefund,
                'status_diteruskan_mitra' => $isReleased ? 'diteruskan' : 'siap_diteruskan',
                'escrow_status' => $order->escrow_status,
                'escrow_hold_reason' => $order->escrow_hold_reason ?: 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai',
                'escrow_released_at' => $order->escrow_released_at ? $order->escrow_released_at->format('d M Y, H:i WIB') : null,
                'payment_submitted_at' => $order->payment_submitted_at?->format('d M Y, H:i WIB'),
                'payment_verified_at' => $order->payment_verified_at?->format('d M Y, H:i WIB'),
                'payment_proof' => $order->payment_proof,
                'waktu_bayar' => $order->payment_submitted_at?->format('d M Y, H:i WIB') ?: 'Belum tercatat',
                'is_refunded' => $isRefund,
                'payment_submitted' => $hasPaymentSubmission,
            ];
        } else {
            $payment = [
                'id' => $id,
                'kode' => 'PAY'.preg_replace('/[^0-9]/', '', (string) $id),
                'order_id' => 'SB1024',
                'clean_order_code' => '1024',
                'layanan' => 'Bersihkan Rumah (Full Home Cleaning)',
                'customer_name' => 'Andi Pratama',
                'customer_phone' => '0812-3344-5566',
                'mitra_name' => 'Budi Santoso',
                'mitra_id' => 'MTR-001',
                'mitra_phone' => '0812-3456-7890',
                'mitra_bank' => 'BCA',
                'mitra_rekening' => '8271 9920 1123',
                'mitra_rekening_an' => 'Budi Santoso',
                'total_bayar' => 140000,
                'biaya_platform' => 14000,
                'nominal_mitra' => 126000,
                'metode' => 'BCA Virtual Account',
                'kode_va' => '8801 0812 3344 5566',
                'status_bayar_customer' => 'lunas',
                'is_pending_verification' => false,
                'is_verified' => true,
                'is_work_completed' => false,
                'can_release_escrow' => false,
                'status_diteruskan_mitra' => 'siap_diteruskan',
                'escrow_status' => 'held',
                'escrow_hold_reason' => 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai',
                'escrow_released_at' => null,
                'waktu_bayar' => '10 Okt 2026, 14:15 WIB',
            ];
        }

        return view('admin.pembayaran.detail', [
            'id' => $id,
            'payment' => $payment,
            'order' => $order,
            'stats' => $state['stats'] ?? [],
        ]);
    }

    /**
     * Halaman Pusat Mediasi & Konflik
     */
    public function konflik()
    {
        $state = $this->realtimeService->getState();
        $dbKonflik = Order::whereIn('status', ['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer'])
            ->orderBy('id', 'desc')
            ->get();

        $countSemua = $dbKonflik->count();
        $countButuhAksi = $dbKonflik->filter(fn ($o) => in_array($o->status, ['konflik', 'komplain']))->count();
        $countInvestigasi = 0;
        $countSelesai = $dbKonflik->filter(fn ($o) => in_array($o->status, ['dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer']))->count();

        return view('admin.konflik.index', [
            'stats' => $state['stats'] ?? [],
            'dbKonflik' => $dbKonflik,
            'countSemua' => $countSemua,
            'countButuhAksi' => $countButuhAksi,
            'countInvestigasi' => $countInvestigasi,
            'countSelesai' => $countSelesai,
        ]);
    }

    /**
     * Kelompokkan pesanan berdasarkan pemilik (customer) untuk daftar & detail.
     *
     * @return array<int, Collection>
     */
    private function ordersGroupedByCustomer(): array
    {
        $grouped = [];

        foreach (Order::orderBy('id', 'desc')->get() as $order) {
            $owner = User::where('role', 'customer')
                ->where(function ($q) use ($order) {
                    $q->where('name', $order->customer_name);

                    if (! empty($order->customer_phone)) {
                        $q->orWhere('phone', $order->customer_phone);
                    }
                })
                ->value('id');

            if ($owner) {
                $grouped[$owner][] = $order;
            }
        }

        return array_map(fn ($items) => collect($items), $grouped);
    }

    /**
     * Halaman Data Mitra (Dialihkan ke antarmuka terpadu Data Pengguna tab Mitra)
     */
    public function mitra(?Request $request = null)
    {
        $request = $request ?: request();

        return $this->pengguna($request, 'mitra');
    }

    /**
     * Halaman Data Pengguna (Customer & Mitra)
     */
    public function pengguna(?Request $request = null, string $defaultTab = 'customer')
    {
        $request = $request ?: request();
        $state = $this->realtimeService->getState();

        $activeTab = $request->get('tab', $defaultTab);
        if (! in_array($activeTab, ['customer', 'mitra'])) {
            $activeTab = 'customer';
        }

        $ordersByCustomer = $this->ordersGroupedByCustomer();

        $customers = User::where('role', 'customer')->orderBy('id', 'desc')->get()->map(function ($u) use ($ordersByCustomer) {
            $orders = $ordersByCustomer[$u->id] ?? collect();
            $orderCount = $orders->count();
            $totalNominal = (int) $orders->sum('nominal');

            $u->custom_orders = $orders;
            $u->custom_order_count = $orderCount;
            $u->custom_total_spent = $totalNominal > 0 ? 'Rp '.number_format($totalNominal, 0, ',', '.') : 'Rp 0';
            $u->custom_account_score = '100%';
            $u->custom_joined_date = $u->created_at ? $u->created_at->format('d M Y') : 'Baru saja';
            $u->custom_tenure = $u->created_at ? $u->created_at->diffForHumans(null, true).' bergabung' : 'Aktif';
            $u->custom_address = $u->address ?: 'DKI Jakarta & Sekitarnya';
            $u->custom_category_area = 'Personal';
            $u->custom_payment_method = 'QRIS & Transfer Bank';

            return $u;
        });

        $mitras = Mitra::query()
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($m) {
                $m->custom_orders = Order::where('mitra_phone', $m->phone)
                    ->orWhere('mitra_name', $m->name)
                    ->latest()
                    ->get();

                return $m;
            });

        $mitraStatusOf = fn ($m) => strtolower($m->status ?? '');
        $isMitraAktif = fn ($m) => in_array($mitraStatusOf($m), ['aktif', 'approved', 'disetujui', 'active']);
        $isMitraSuspend = fn ($m) => in_array($mitraStatusOf($m), ['suspend', 'ditinjau', 'tolak', 'rejected', 'ditolak']);

        $totalCustomers = $customers->count();
        $aktifCustomers = $customers->filter(fn ($c) => in_array(strtolower($c->status ?? ''), ['aktif', 'active', 'approved', '']))->count();
        $verifCustomers = $customers->filter(fn ($c) => str_contains(strtolower($c->status ?? ''), 'verif'))->count();
        $baruCustomers = $customers->filter(fn ($c) => in_array(strtolower($c->status ?? ''), ['baru', 'new']))->count();

        $totalMitras = $mitras->count();
        $aktifMitras = $mitras->filter($isMitraAktif)->count();
        $suspendMitras = $mitras->filter($isMitraSuspend)->count();
        $verifMitras = $mitras->filter(fn ($m) => ! $isMitraAktif($m) && ! $isMitraSuspend($m))->count();
        $baruMitras = 0;

        return view('admin.pengguna.index', [
            'activeTab' => $activeTab,
            'stats' => $state['stats'] ?? [],
            'customers' => $customers,
            'mitras' => $mitras,
            'totalCustomers' => $totalCustomers,
            'aktifCustomers' => $aktifCustomers,
            'verifCustomers' => $verifCustomers,
            'baruCustomers' => $baruCustomers,
            'totalMitras' => $totalMitras,
            'aktifMitras' => $aktifMitras,
            'verifMitras' => $verifMitras,
            'baruMitras' => $baruMitras,
            'suspendMitras' => $suspendMitras,
        ]);
    }

    /**
     * Mengambil daftar seluruh customer untuk panel admin (JSON)
     */
    public function getAdminCustomerList(): JsonResponse
    {
        $ordersByCustomer = $this->ordersGroupedByCustomer();

        $customers = User::where('role', 'customer')->orderBy('id', 'desc')->get()->map(function ($u) use ($ordersByCustomer) {
            $orders = $ordersByCustomer[$u->id] ?? collect();
            $orderCount = $orders->count();
            $totalNominal = (int) $orders->sum('nominal');

            return [
                'id' => '#USR-'.str_pad((string) $u->id, 4, '0', STR_PAD_LEFT),
                'db_id' => $u->id,
                'name' => $u->name,
                'nama' => $u->name,
                'phone' => $u->phone,
                'whatsapp' => $u->phone,
                'email' => $u->email,
                'status' => $u->status ?: 'aktif',
                'role' => $u->role,
                'orderCount' => $orderCount,
                'totalSpent' => $totalNominal > 0 ? 'Rp '.number_format($totalNominal, 0, ',', '.') : 'Rp 0',
                'accountScore' => '100%',
                'joinedDate' => $u->created_at ? $u->created_at->format('d M Y') : 'Baru saja',
                'tenure' => $u->created_at ? $u->created_at->diffForHumans(null, true).' bergabung' : 'Aktif',
                'address' => $u->address ?: 'DKI Jakarta & Sekitarnya',
                'categoryArea' => 'Personal',
                'paymentMethod' => 'QRIS & Transfer Bank',
                'registeredAt' => $u->created_at ? $u->created_at->toISOString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'counts' => [
                'total' => $customers->count(),
                'aktif' => $customers->filter(fn ($c) => in_array(strtolower($c['status']), ['aktif', 'active', 'approved']))->count(),
                'terverifikasi' => $customers->filter(fn ($c) => str_contains(strtolower($c['status']), 'verif'))->count(),
                'baru' => $customers->filter(fn ($c) => in_array(strtolower($c['status']), ['baru', 'new']))->count(),
            ],
        ]);
    }

    /**
     * Halaman Review & Ulasan
     */
    public function review()
    {
        $state = $this->realtimeService->getState();
        $reviews = Order::whereNotNull('review_rating')->orderBy('reviewed_at', 'desc')->get();

        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? number_format((float) $reviews->avg('review_rating'), 1) : '0.0';
        $star5Count = $reviews->where('review_rating', 5)->count();
        $keluhanCount = $reviews->where('review_rating', '<=', 2)->count();

        return view('admin.review.index', [
            'stats' => $state['stats'] ?? [],
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'avgRating' => $avgRating,
            'star5Count' => $star5Count,
            'keluhanCount' => $keluhanCount,
        ]);
    }

    /**
     * Halaman Profil Admin
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Halaman Manajemen Layanan
     */
    public function layanan()
    {
        $state = $this->realtimeService->getState();

        return view('admin.layanan.index', [
            'stats' => $state['stats'] ?? [],
        ]);
    }

    /**
     * Halaman Konfirmasi Pekerjaan (Kesepakatan Mitra & Customer)
     */
    public function konfirmasiPekerjaan()
    {
        $state = $this->realtimeService->getState();
        $waitingOrders = Order::where('status', 'menunggu_konfirmasi_admin')->latest()->get();
        $completionOrders = Order::where('status', 'menunggu_konfirmasi_admin_selesai')
            ->latest('updated_at')
            ->get();

        return view('admin.konfirmasi.index', [
            'stats' => $state['stats'] ?? [],
            'waitingOrders' => $waitingOrders,
            'completionOrders' => $completionOrders,
        ]);
    }

    /**
     * Halaman Pengaduan / Tiket Laporan Masalah
     */
    public function pengaduan()
    {
        $state = $this->realtimeService->getState();

        return view('admin.pengaduan.index', [
            'stats' => $state['stats'] ?? [],
        ]);
    }

    /**
     * Halaman Monitoring Chat Percakapan
     */
    public function monitoringChat()
    {
        $state = $this->realtimeService->getState();
        $messages = ChatMessage::query()
            ->whereIn('sender_type', ['customer', 'mitra'])
            ->latest('id')
            ->get(['id', 'order_code', 'sender_type', 'sender_name', 'message', 'created_at'])
            ->reverse();
        $orderCodes = $messages->pluck('order_code')
            ->flatMap(function (string $code): array {
                $cleanCode = ltrim(trim($code), '#');

                return [$cleanCode, '#'.$cleanCode];
            })
            ->unique()
            ->values()
            ->all();
        $orderIds = $messages->pluck('order_code')
            ->filter(fn (string $code): bool => ctype_digit($code))
            ->map(fn (string $code): int => (int) $code)
            ->unique()
            ->values()
            ->all();
        $orders = Order::query()
            ->where(fn ($query) => $query->whereIn('code', $orderCodes)->orWhereIn('id', $orderIds))
            ->get([
                'id',
                'code',
                'customer_name',
                'mitra_name',
                'layanan',
                'status',
            ]);
        $ordersByCode = $orders->keyBy(fn (Order $order): string => ltrim($order->code, '#'));
        $ordersById = $orders->keyBy('id');

        $conversations = $messages
            ->groupBy(fn (ChatMessage $message): string => ltrim(trim($message->order_code), '#'))
            ->map(function (Collection $conversationMessages, string $orderCode) use ($ordersByCode, $ordersById): array {
                $order = $ordersByCode->get($orderCode);
                if (! $order && ctype_digit($orderCode)) {
                    $order = $ordersById->get((int) $orderCode);
                }

                $customerMessage = $conversationMessages->firstWhere('sender_type', 'customer');
                $mitraMessage = $conversationMessages->firstWhere('sender_type', 'mitra');
                $latestMessage = $conversationMessages->last();

                return [
                    'chat_id' => 'CHAT-'.$orderCode,
                    'order_code' => $order?->code ?: '#'.$orderCode,
                    'customer_name' => $order?->customer_name ?: $customerMessage?->sender_name ?: 'Customer',
                    'mitra_name' => $order?->mitra_name ?: $mitraMessage?->sender_name ?: 'Mitra',
                    'service' => $order?->layanan ?: 'Layanan SayaBantu',
                    'status' => $order?->status ?: 'Tidak tersedia',
                    'message_count' => $conversationMessages->count(),
                    'last_message' => $latestMessage->message,
                    'last_time' => $latestMessage->created_at->format('d M Y, H:i'),
                    'timestamp' => $latestMessage->created_at->timestamp,
                    'messages' => $conversationMessages->map(fn (ChatMessage $message): array => [
                        'sender' => $message->sender_name,
                        'role' => ucfirst($message->sender_type),
                        'text' => $message->message,
                        'time' => $message->created_at->format('d M Y, H:i'),
                    ])->values()->all(),
                ];
            })
            ->sortByDesc('timestamp')
            ->values();

        return view('admin.chat.index', [
            'stats' => $state['stats'] ?? [],
            'conversations' => $conversations,
            'conversationCount' => $conversations->count(),
            'customerMessageCount' => $messages->where('sender_type', 'customer')->count(),
            'mitraMessageCount' => $messages->where('sender_type', 'mitra')->count(),
        ]);
    }

    /**
     * Halaman Moderasi & Kepatuhan
     */
    public function moderasiKepatuhan()
    {
        $state = $this->realtimeService->getState();

        $kasusMitra = Mitra::whereIn('status', ['suspend', 'ditinjau', 'tolak', 'rejected', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $countAktif = $kasusMitra->count();
        $countPeringatan = 0;
        $countDibatasi = 0;
        $countSuspend = $kasusMitra->whereIn('status', ['suspend', 'tolak', 'rejected', 'ditolak'])->count();

        return view('admin.moderasi.index', [
            'stats' => $state['stats'] ?? [],
            'kasusMitra' => $kasusMitra,
            'countAktif' => $countAktif,
            'countPeringatan' => $countPeringatan,
            'countDibatasi' => $countDibatasi,
            'countSuspend' => $countSuspend,
        ]);
    }

    /**
     * Halaman Laporan & Analitik Platform
     */
    public function laporanAnalitik()
    {
        $state = $this->realtimeService->getState();

        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')->where('status', 'aktif')->count();
        $newCustomersThisMonth = User::where('role', 'customer')->where('created_at', '>=', now()->startOfMonth())->count();

        $totalMitras = Mitra::count();
        $verifiedMitras = Mitra::whereIn('status', ['aktif', 'approved', 'disetujui'])->count();
        $pendingMitras = Mitra::whereIn('status', ['verifikasi', 'menunggu', 'pending', 'pending_approval', 'menunggu_verifikasi'])->count();
        $newMitrasThisMonth = Mitra::where('created_at', '>=', now()->startOfMonth())->count();

        $totalSuruhan = Order::count();
        $selesaiSuruhan = Order::where('status', 'selesai')->count();
        $diprosesSuruhan = Order::whereIn('status', ['menunggu', 'menunggu_mitra', 'nego', 'menunggu_penawaran', 'siap_bayar', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi'])->count();
        $dibatalkanSuruhan = Order::whereIn('status', ['dibatalkan', 'konflik'])->count();

        $totalGMV = Order::whereIn('status', ['siap_bayar', 'terjadwal', 'otw', 'berjalan', 'selesai'])->sum('nominal');
        $komisiPlatform = (int) round($totalGMV * 0.10);

        $recentUsers = User::where('role', 'customer')->latest()->take(5)->get()->map(function ($u) {
            return [
                'name' => $u->name,
                'role_label' => 'Customer',
                'color' => 'bg-emerald-500',
                'time' => $u->created_at?->diffForHumans() ?? 'Baru saja',
                'created_at' => $u->created_at,
            ];
        });

        $recentMitras = Mitra::latest()->take(5)->get()->map(function ($m) {
            return [
                'name' => $m->name,
                'role_label' => 'Mitra '.($m->keahlian ?: 'Jasa'),
                'color' => 'bg-teal-500',
                'time' => $m->created_at?->diffForHumans() ?? 'Baru saja',
                'created_at' => $m->created_at,
            ];
        });

        $recentRegistrations = $recentUsers->concat($recentMitras)->sortByDesc('created_at')->values()->take(5);

        return view('admin.laporan.index', [
            'stats' => $state['stats'] ?? [],
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'totalMitras' => $totalMitras,
            'verifiedMitras' => $verifiedMitras,
            'pendingMitras' => $pendingMitras,
            'newMitrasThisMonth' => $newMitrasThisMonth,
            'totalSuruhan' => $totalSuruhan,
            'selesaiSuruhan' => $selesaiSuruhan,
            'diprosesSuruhan' => $diprosesSuruhan,
            'dibatalkanSuruhan' => $dibatalkanSuruhan,
            'totalGMV' => $totalGMV,
            'komisiPlatform' => $komisiPlatform,
            'recentRegistrations' => $recentRegistrations,
        ]);
    }

    /**
     * Halaman Pengaturan Platform
     */
    public function pengaturan()
    {
        return view('admin.pengaturan.index');
    }
}

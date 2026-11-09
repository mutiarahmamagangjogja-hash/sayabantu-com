<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Mitra;
use App\Models\Order;
use App\Services\AdminRealtimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Simpan pengajuan suruhan baru dari customer ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'layanan' => ['required', 'string', 'max:255'],
            'jadwal' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['nullable', 'string', 'max:100'],
            'jam' => ['nullable', 'string', 'max:50'],
            'alamat' => ['required', 'string'],
            'kota' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'string', 'max:100'],
            'biaya' => ['nullable', 'string', 'max:100'],
            'metode' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'max:50'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'durasi_cari' => ['nullable', 'string', 'max:100'],
            'durasi_menit' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'code' => ['nullable', 'string', 'max:50'],
        ], [
            'layanan.required' => 'Pilihan layanan suruhan wajib diisi.',
            'alamat.required' => 'Alamat lokasi penugasan wajib diisi.',
        ]);

        $user = Auth::user();
        $sessionAuth = session('customer_auth', []);

        $customerName = trim($validated['customer_name'] ?? ($user?->name ?? ($sessionAuth['name'] ?? 'Pelanggan SayaBantu')));
        $customerPhone = trim($validated['customer_phone'] ?? ($user?->phone ?? ($sessionAuth['phone'] ?? '0812-3456-7890')));

        // Jadwal penugasan
        $jadwal = $validated['jadwal'] ?? null;
        if (! $jadwal && ! empty($validated['tanggal'])) {
            $jadwal = $validated['tanggal'].(! empty($validated['jam']) ? ' • '.$validated['jam'] : '');
        }
        if (! $jadwal) {
            $jadwal = 'Hari ini • '.date('H:i').' WIB';
        }

        // Batas durasi pencarian mitra (Diinput oleh customer)
        $durasiMenit = (int) ($validated['durasi_menit'] ?? 60);
        if ($durasiMenit <= 0) {
            $durasiMenit = 60;
        }
        $durasiCari = trim($validated['durasi_cari'] ?? '');
        if (! $durasiCari) {
            $durasiCari = ($durasiMenit % 60 === 0 && $durasiMenit >= 60)
                ? ($durasiMenit / 60).' Jam'
                : $durasiMenit.' Menit';
        }

        // Budget / Biaya (Minimal Rp 10.000)
        $rawBiaya = $validated['budget'] ?? ($validated['biaya'] ?? '50.000');
        $nominal = (int) preg_replace('/[^0-9]/', '', $rawBiaya);
        if ($nominal <= 0) {
            $nominal = 50000;
        }
        if ($nominal < 10000) {
            $nominal = 10000;
        }
        $biayaFormatted = number_format($nominal, 0, ',', '.');

        $metode = $validated['metode'] ?? 'Langsung';
        $isNego = ($metode === 'Nego');
        $status = $isNego ? 'nego' : 'menunggu_mitra';
        $step = $isNego ? 5 : 4;

        // Kode pesanan unik
        $orderCode = $validated['code'] ?? null;
        if (! $orderCode || Order::where('code', $orderCode)->exists()) {
            do {
                $orderCode = '#SB-SURUH-'.random_int(1000, 9999);
            } while (Order::where('code', $orderCode)->exists());
        }
        if (! str_starts_with($orderCode, '#')) {
            $orderCode = '#'.$orderCode;
        }

        $order = Order::create([
            'code' => $orderCode,
            'user_id' => $user?->id,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'layanan' => $validated['layanan'],
            'jadwal' => $jadwal,
            'durasi_cari' => $durasiCari,
            'durasi_menit' => $durasiMenit,
            'alamat' => $validated['alamat'],
            'kota' => $validated['kota'] ?? null,
            'note' => $validated['note'] ?? null,
            'biaya' => $biayaFormatted,
            'nominal' => $nominal,
            'metode' => $metode,
            'gender' => $validated['gender'] ?? 'Bebas',
            'status' => $status,
            'step' => $step,
        ]);

        // Catat realtime ke admin
        try {
            app(AdminRealtimeService::class)->recordNewOrder($order);
        } catch (\Throwable $e) {
        }

        $cleanCode = ltrim($orderCode, '#');
        $redirectUrl = route('customer.pesanan.detail', ['kode' => $cleanCode]).'?'.http_build_query([
            'layanan' => $order->layanan,
            'jadwal' => $order->jadwal,
            'durasi_cari' => $order->durasi_cari,
            'durasi_menit' => $order->durasi_menit,
            'biaya' => $order->biaya,
            'total' => $order->biaya,
            'metode' => $order->metode,
            'status' => $order->status,
            'alamat' => $order->alamat,
            'kota' => $order->kota,
            'note' => $order->note,
            'gender' => $order->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan suruhan berhasil dibuat dan diteruskan ke Kebutuhan Mitra & Admin.',
            'order' => [
                'id' => $order->id,
                'code' => $order->code,
                'layanan' => $order->layanan,
                'jadwal' => $order->jadwal,
                'durasi_cari' => $order->durasi_cari,
                'durasi_menit' => $order->durasi_menit,
                'alamat' => $order->alamat,
                'kota' => $order->kota,
                'note' => $order->note,
                'budget' => $order->biaya,
                'biaya' => $order->biaya,
                'metode' => $order->metode,
                'status' => $order->status,
                'customerName' => $order->customer_name,
                'customerPhone' => $order->customer_phone,
                'createdAt' => $order->created_at->toISOString(),
            ],
            'redirect' => $redirectUrl,
        ]);
    }

    /**
     * Mengambil daftar suruhan yang aktif untuk halaman Kebutuhan Mitra.
     */
    public function kebutuhan(): JsonResponse
    {
        $orders = Order::whereNotIn('status', ['cancelled_by_customer', 'cancelled_by_admin', 'dibatalkan'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->code,
                    'code' => $o->code,
                    'layanan' => $o->layanan,
                    'jadwal' => $o->jadwal,
                    'alamat' => $o->alamat,
                    'kota' => $o->kota,
                    'note' => $o->note,
                    'budget' => $o->biaya,
                    'biaya' => $o->biaya,
                    'nominal' => $o->nominal,
                    'metode' => $o->metode,
                    'gender' => $o->gender,
                    'status' => $o->status,
                    'step' => $o->step,
                    'customerName' => $o->customer_name,
                    'customerPhone' => $o->customer_phone,
                    'mitraName' => $o->mitra_name,
                    'mitraPhone' => $o->mitra_phone,
                    'mitraId' => $o->mitra_id,
                    'selectedMitraId' => $o->mitra_id,
                    'selectedMitraName' => $o->mitra_name,
                    'agreed_price' => $o->agreed_price,
                    'agreed_by' => $o->agreed_by,
                    'nego_by' => $o->nego_by,
                    'escrow_status' => $o->escrow_status,
                    'mitra_fee' => $o->mitra_fee,
                    'escrow_released_at' => $o->escrow_released_at?->toISOString(),
                    'review_rating' => $o->review_rating,
                    'review_comment' => $o->review_comment,
                    'reviewed_at' => $o->reviewed_at?->toISOString(),
                    'createdAt' => $o->created_at?->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Mengambil daftar seluruh pesanan untuk admin.
     */
    public function adminList(Request $request): JsonResponse
    {
        $status = $request->input('status');

        $query = Order::orderBy('id', 'desc');

        if ($status && $status !== 'semua') {
            if ($status === 'menunggu') {
                $query->whereIn('status', ['menunggu', 'menunggu_mitra', 'nego', 'menunggu_penawaran', 'menunggu_konfirmasi_admin', 'menunggu_keputusan', 'waiting_for_partner']);
            } elseif ($status === 'diproses') {
                $query->whereIn('status', ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi', 'diproses', 'menunggu_konfirmasi_customer', 'menunggu_konfirmasi_admin_selesai']);
            } elseif ($status === 'selesai') {
                $query->where('status', 'selesai');
            } elseif ($status === 'konflik') {
                $query->whereIn('status', ['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer']);
            }
        }

        $orders = $query->get()->map(function ($o) {
            $statusMap = [
                'selesai' => ['label' => 'Selesai', 'class' => 'status-success', 'filter' => 'selesai'],
                'konflik' => ['label' => 'Konflik', 'class' => 'status-conflict', 'filter' => 'konflik'],
                'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'status-conflict', 'filter' => 'konflik'],
                'cancelled_by_admin' => ['label' => 'Dibatalkan Admin', 'class' => 'status-conflict', 'filter' => 'konflik'],
                'cancelled_by_customer' => ['label' => 'Dibatalkan Customer', 'class' => 'status-conflict', 'filter' => 'konflik'],
                'waiting_for_partner' => ['label' => 'Menunggu Mitra Baru', 'class' => 'status-waiting', 'filter' => 'menunggu'],
                'menunggu_pembayaran' => ['label' => 'Diproses', 'class' => 'status-warning', 'filter' => 'diproses'],
                'menunggu_konfirmasi_pembayaran' => ['label' => 'Verifikasi Pembayaran', 'class' => 'status-warning', 'filter' => 'diproses'],
                'siap_bayar' => ['label' => 'Diproses', 'class' => 'status-warning', 'filter' => 'diproses'],
                'terjadwal' => ['label' => 'Dikonfirmasi', 'class' => 'status-success', 'filter' => 'diproses'],
                'otw' => ['label' => 'Diproses', 'class' => 'status-process', 'filter' => 'diproses'],
                'berjalan' => ['label' => 'Diproses', 'class' => 'status-process', 'filter' => 'diproses'],
                'menunggu_konfirmasi_customer' => ['label' => 'Diproses', 'class' => 'status-process', 'filter' => 'diproses'],
                'menunggu_konfirmasi_admin_selesai' => ['label' => 'Konfirmasi Selesai', 'class' => 'status-success', 'filter' => 'diproses'],
                'menunggu_konfirmasi_admin' => ['label' => 'Menunggu', 'class' => 'status-waiting', 'filter' => 'menunggu'],
                'menunggu_keputusan' => ['label' => 'Menunggu', 'class' => 'status-waiting', 'filter' => 'menunggu'],
                'menunggu_mitra' => ['label' => 'Menunggu', 'class' => 'status-waiting', 'filter' => 'menunggu'],
                'nego' => ['label' => 'Menunggu', 'class' => 'status-waiting', 'filter' => 'menunggu'],
            ];
            $st = $statusMap[$o->status] ?? ['label' => 'Menunggu', 'class' => 'status-waiting', 'filter' => 'menunggu'];

            return [
                'id' => $o->code,
                'code' => $o->code,
                'layanan' => $o->layanan,
                'jadwal' => $o->jadwal,
                'alamat' => $o->alamat,
                'kota' => $o->kota,
                'note' => $o->note,
                'budget' => $o->biaya,
                'biaya' => $o->biaya,
                'agreed_price' => $o->agreed_price,
                'agreed_by' => $o->agreed_by,
                'nego_by' => $o->nego_by,
                'step' => $o->step,
                'metode' => $o->metode,
                'status' => $o->status,
                'statusLabel' => $st['label'],
                'statusClass' => $st['class'],
                'adminFilter' => $st['filter'],
                'customerName' => $o->customer_name,
                'customerPhone' => $o->customer_phone,
                'mitraName' => $o->mitra_name,
                'mitraId' => $o->mitra_id,
                'selectedMitraId' => $o->mitra_id,
                'selectedMitraName' => $o->mitra_name,
                'createdAt' => $o->created_at?->diffForHumans() ?? 'Baru saja',
                'escrow_status' => $o->escrow_status,
                'escrow_hold_reason' => $o->escrow_hold_reason,
                'escrow_released_at' => $o->escrow_released_at?->format('d M Y, H:i WIB'),
                'admin_fee' => $o->admin_fee ?: (int) round(($o->nominal ?: (int) preg_replace('/[^0-9]/', '', (string) $o->biaya)) * 0.10),
                'mitra_fee' => $o->mitra_fee ?: (int) round(($o->nominal ?: (int) preg_replace('/[^0-9]/', '', (string) $o->biaya)) * 0.90),
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
            'counts' => [
                'semua' => Order::count(),
                'menunggu' => Order::whereIn('status', ['menunggu', 'menunggu_mitra', 'nego', 'menunggu_penawaran', 'menunggu_konfirmasi_admin', 'menunggu_keputusan', 'waiting_for_partner'])->count(),
                'diproses' => Order::whereIn('status', ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi', 'diproses', 'menunggu_konfirmasi_customer', 'menunggu_konfirmasi_admin_selesai'])->count(),
                'selesai' => Order::where('status', 'selesai')->count(),
                'konflik' => Order::whereIn('status', ['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer'])->count(),
            ],
        ]);
    }

    /**
     * Mengambil detail satu pesanan berdasarkan kode atau ID.
     */
    public function show(string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $bids = [];
        if (! empty($order->mitra_name) || ! empty($order->mitra_id)) {
            $mitra = Mitra::query()
                ->where(function ($query) use ($order) {
                    if ($order->mitra_id) {
                        $query->where('mitra_id', $order->mitra_id);
                    }
                    if ($order->mitra_phone) {
                        $query->orWhere('phone', $order->mitra_phone);
                    }
                    if ($order->mitra_name) {
                        $query->orWhere('name', $order->mitra_name);
                    }
                })
                ->withCount(['completedOrders', 'reviewedOrders'])
                ->withAvg('reviewedOrders', 'review_rating')
                ->first();

            $bids[] = [
                'id' => 'BID-'.$order->id,
                'mitraId' => $order->mitra_id,
                'mitraName' => $order->mitra_name,
                'mitraPhone' => $order->mitra_phone,
                'rating' => $mitra?->reviewed_orders_avg_review_rating === null
                    ? null
                    : number_format((float) $mitra->reviewed_orders_avg_review_rating, 1),
                'reviewCount' => $mitra?->reviewed_orders_count ?? 0,
                'tugasSelesai' => $mitra?->completed_orders_count ?? 0,
                'spesialis' => $mitra?->keahlian,
                'avatar' => $mitra?->avatar,
                'kota' => $mitra?->kota,
                'harga' => $order->nominal,
                'hargaFormatted' => $order->biaya,
                'durasi' => 'Sesuai Permintaan',
                'status' => 'menunggu_keputusan',
                'nego_by' => $order->nego_by,
                'agreed_by' => $order->agreed_by,
                'agreed_price' => $order->agreed_price,
                'createdAt' => $order->updated_at?->toISOString() ?: now()->toISOString(),
            ];
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'code' => $order->code,
                'layanan' => $order->layanan,
                'jadwal' => $order->jadwal,
                'alamat' => $order->alamat,
                'kota' => $order->kota,
                'note' => $order->note,
                'budget' => $order->biaya,
                'biaya' => $order->biaya,
                'nominal' => $order->nominal,
                'metode' => $order->metode,
                'gender' => $order->gender,
                'status' => $order->status,
                'step' => $order->step,
                'customerName' => $order->customer_name,
                'customerPhone' => $order->customer_phone,
                'mitraName' => $order->mitra_name,
                'mitraPhone' => $order->mitra_phone,
                'mitraId' => $order->mitra_id,
                'nego_by' => $order->nego_by,
                'agreed_by' => $order->agreed_by,
                'agreed_price' => $order->agreed_price,
                'bids' => $bids,
                'latest_bid' => $bids[0] ?? null,
                'createdAt' => $order->created_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Customer memilih satu mitra dan menyepakati harga.
     */
    /**
     * Customer memilih satu mitra dan menyepakati harga penawaran Mitra (Langkah 5 -> Langkah 6).
     */
    public function pilihMitra(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($order->nego_by === 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Tawaran tawar balik Anda masih menunggu persetujuan dari pihak Mitra.',
            ], 422);
        }

        $validated = $request->validate([
            'mitra_id' => ['nullable', 'string', 'max:50'],
            'mitra_name' => ['nullable', 'string', 'max:255'],
            'biaya' => ['nullable', 'string', 'max:100'],
        ]);

        $mitraId = $validated['mitra_id'] ?? ($order->mitra_id ?: 'SB-7615');
        $mitraName = $validated['mitra_name'] ?? ($order->mitra_name ?: 'Kala');
        $biaya = $validated['biaya'] ?? $order->biaya;
        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $biaya);

        $order->update([
            'status' => 'menunggu_konfirmasi_admin',
            'step' => 6,
            'mitra_id' => $mitraId,
            'mitra_name' => $mitraName,
            'biaya' => $biaya,
            'nominal' => $nominal > 0 ? $nominal : $order->nominal,
            'agreed_by' => 'customer',
            'agreed_price' => $biaya,
        ]);

        $cleanCode = ltrim($order->code, '#');

        try {
            app(AdminRealtimeService::class)->recordNewOrder($order);
        } catch (\Throwable $e) {
        }

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'customer',
                'sender_name' => $order->customer_name ?: 'Customer',
                'message' => "Customer telah menyepakati harga Rp {$biaya} dari Mitra {$mitraName}. Menunggu verifikasi Admin.",
                'message_type' => 'system',
                'metadata' => [
                    'agreed_by' => 'customer',
                    'nominal' => $biaya,
                ],
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Mitra {$mitraName} berhasil dipilih dengan harga Rp {$biaya}. Menunggu konfirmasi Admin.",
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'step' => $order->step,
                'mitraName' => $order->mitra_name,
                'mitraId' => $order->mitra_id,
                'biaya' => $order->biaya,
                'agreed_by' => 'customer',
                'agreed_price' => $order->agreed_price,
            ],
        ]);
    }

    /**
     * Customer menawar balik harga (Nego Balik) ke Mitra.
     */
    public function customerNego(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $rawHarga = (string) $request->input('harga');
        $nominal = (int) preg_replace('/[^0-9]/', '', $rawHarga);
        if ($nominal <= 0) {
            $nominal = 50000;
        }
        $biayaFormatted = number_format($nominal, 0, ',', '.');
        $catatan = trim((string) $request->input('catatan')) ?: 'Penawaran tawar balik dari Customer.';

        $mitraName = $request->input('mitra_name') ?: ($request->input('mitra') ?: $order->mitra_name);
        $mitraId = $request->input('mitra_id') ?: $order->mitra_id;
        $mitraPhone = $request->input('mitra_phone') ?: $order->mitra_phone;

        $updateData = [
            'status' => 'nego',
            'step' => 5,
            'biaya' => $biayaFormatted,
            'nominal' => $nominal,
            'nego_by' => 'customer',
        ];
        if ($mitraName) {
            $updateData['mitra_name'] = $mitraName;
        }
        if ($mitraId) {
            $updateData['mitra_id'] = $mitraId;
        }
        if ($mitraPhone) {
            $updateData['mitra_phone'] = $mitraPhone;
        }

        $order->update($updateData);

        $cleanCode = ltrim($order->code, '#');

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'customer',
                'sender_name' => $order->customer_name ?: 'Customer',
                'message' => "Halo Mitra, saya mengajukan penawaran tawar balik sebesar Rp {$biayaFormatted}.",
                'message_type' => 'nego',
                'metadata' => [
                    'nominal' => $biayaFormatted,
                    'catatan' => $catatan,
                    'nego_by' => 'customer',
                ],
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Tawaran tawar balik Rp {$biayaFormatted} berhasil dikirim ke Mitra.",
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'step' => $order->step,
                'biaya' => $order->biaya,
                'nominal' => $order->nominal,
                'nego_by' => 'customer',
                'mitraName' => $order->mitra_name,
            ],
        ]);
    }

    /**
     * Mitra menyepakati penawaran tawar balik dari Customer (Langkah 5 -> Langkah 6).
     */
    public function mitraSepakatiNego(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $price = $request->input('biaya') ?: $order->biaya;
        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $price);

        $currentMitra = auth()->user()?->role === 'mitra' ? Mitra::where('user_id', auth()->id())->first() : null;
        $mitraId = $request->input('mitra_id') ?: ($currentMitra?->mitra_id ?: ($order->mitra_id ?: 'SB-7615'));
        $mitraName = $request->input('mitra_name') ?: ($currentMitra?->name ?: ($order->mitra_name ?: 'Kala'));
        $mitraPhone = $request->input('mitra_phone') ?: ($currentMitra?->phone ?: ($order->mitra_phone ?: '08128892509910'));

        $order->update([
            'status' => 'menunggu_konfirmasi_admin',
            'step' => 6,
            'mitra_id' => $mitraId,
            'mitra_name' => $mitraName,
            'mitra_phone' => $mitraPhone,
            'agreed_by' => 'mitra',
            'agreed_price' => $price,
            'biaya' => $price,
            'nominal' => $nominal > 0 ? $nominal : $order->nominal,
        ]);

        $cleanCode = ltrim($order->code, '#');

        try {
            app(AdminRealtimeService::class)->recordNewOrder($order);
        } catch (\Throwable $e) {
        }

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'mitra',
                'sender_name' => $order->mitra_name ?: 'Kala',
                'message' => "Mitra {$mitraName} telah memilih dan mengambil pesanan ini! Pesanan sekarang diteruskan ke Tim Admin SayaBantu untuk verifikasi & pembayaran.",
                'message_type' => 'system',
                'metadata' => [
                    'agreed_by' => 'mitra',
                    'nominal' => $price,
                ],
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->code} berhasil Anda pilih! Diteruskan ke konfirmasi Admin & Pembayaran.",
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'step' => $order->step,
                'biaya' => $order->biaya,
                'agreed_by' => 'mitra',
                'agreed_price' => $order->agreed_price,
            ],
        ]);
    }

    /**
     * Admin mengonfirmasi kesepakatan kerja antara Customer & Mitra (Langkah 6 -> Langkah 7).
     */
    public function adminConfirm(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $cleanCode = ltrim($order->code, '#');
        $isAlreadyPaid = in_array($order->status, ['terjadwal', 'otw', 'berjalan', 'selesai'])
            || $order->escrow_status === 'held'
            || (int) ($order->step ?? 0) >= 8;

        if ($isAlreadyPaid) {
            $newStatus = in_array($order->status, ['otw', 'berjalan', 'selesai']) ? $order->status : 'terjadwal';
            $newStep = max(8, (int) ($order->step ?? 8));
            $order->update([
                'status' => $newStatus,
                'step' => $newStep,
                'escrow_status' => 'held',
            ]);
        } else {
            $order->update([
                'status' => 'menunggu_pembayaran',
                'step' => 7,
            ]);
        }

        try {
            app(AdminRealtimeService::class)->confirmOrder($order->code);
        } catch (\Throwable $e) {
        }

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'admin',
                'sender_name' => 'Admin SayaBantu',
                'message' => $isAlreadyPaid
                    ? 'Pesanan & jadwal telah disahkan Admin!'
                    : 'Kesepakatan kerja telah diverifikasi dan disetujui Admin! Tahap pembayaran kini telah dibuka.',
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => $isAlreadyPaid
                ? 'Pesanan berhasil dikonfirmasi Admin (Jadwal Terbit).'
                : 'Kesepakatan berhasil dikonfirmasi Admin. Pembayaran telah dibuka untuk Customer.',
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'step' => $order->step,
            ],
        ]);
    }

    public function adminRequestRevision(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'menunggu_konfirmasi_admin') {
            return response()->json(['success' => false, 'message' => 'Hanya kesepakatan yang menunggu konfirmasi Admin yang dapat diminta revisi.'], 409);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $order->update([
            'status' => 'nego',
            'step' => 5,
            'agreed_by' => null,
            'agreed_price' => null,
        ]);

        ChatMessage::create([
            'order_code' => ltrim($order->code, '#'),
            'sender_type' => 'admin',
            'sender_name' => 'Admin SayaBantu',
            'message' => 'Admin meminta revisi kesepakatan: '.$validated['note'],
            'message_type' => 'system',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan revisi kesepakatan telah dikirim kepada Customer dan Mitra.',
            'order' => $order,
        ]);
    }

    public function adminRejectAgreement(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'menunggu_konfirmasi_admin') {
            return response()->json(['success' => false, 'message' => 'Hanya kesepakatan yang menunggu konfirmasi Admin yang dapat ditolak.'], 409);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $order->update([
            'status' => 'semua_ditolak',
            'step' => 0,
            'cancellation_reason' => $validated['note'],
            'cancelled_by' => 'admin',
        ]);

        ChatMessage::create([
            'order_code' => ltrim($order->code, '#'),
            'sender_type' => 'admin',
            'sender_name' => 'Admin SayaBantu',
            'message' => 'Admin menolak kesepakatan: '.$validated['note'],
            'message_type' => 'system',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kesepakatan telah ditolak dan Customer serta Mitra telah diberi informasi.',
            'order' => $order,
        ]);
    }

    /**
     * Mitra mengajukan penawaran harga (nego) untuk suatu pesanan.
     */
    public function ajukanPenawaran(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'harga' => ['required'],
            'durasi' => ['nullable', 'string', 'max:100'],
            'mitra_id' => ['nullable', 'string', 'max:50'],
            'mitra_name' => ['nullable', 'string', 'max:255'],
            'mitra_phone' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
        ]);

        $rawHarga = (string) $validated['harga'];
        $nominal = (int) preg_replace('/[^0-9]/', '', $rawHarga);
        if ($nominal <= 0) {
            $nominal = 85000;
        }
        $biayaFormatted = number_format($nominal, 0, ',', '.');

        $mitraName = trim((string) ($validated['mitra_name'] ?? 'Kala'));
        $mitraPhone = trim((string) ($validated['mitra_phone'] ?? '08128892509910'));
        $mitraId = trim((string) ($validated['mitra_id'] ?? 'SB-7615'));

        $order->update([
            'status' => 'menunggu_keputusan',
            'step' => 5,
            'biaya' => $biayaFormatted,
            'nominal' => $nominal,
            'mitra_id' => $mitraId,
            'mitra_name' => $mitraName,
            'mitra_phone' => $mitraPhone,
            'nego_by' => 'mitra',
        ]);

        $cleanCode = ltrim($order->code, '#');

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'mitra',
                'sender_name' => $mitraName,
                'message' => "Mitra {$mitraName} mengajukan penawaran harga: Rp {$biayaFormatted}.",
                'message_type' => 'nego',
                'metadata' => [
                    'nominal' => $biayaFormatted,
                    'nego_by' => 'mitra',
                ],
            ]);
        } catch (\Throwable $e) {
        }

        try {
            app(AdminRealtimeService::class)->updateOrderStatus($order->code, 'Menunggu');
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Tawaran harga nego Rp {$biayaFormatted} berhasil dikirim ke Customer.",
            'order' => [
                'code' => $order->code,
                'status' => $order->status,
                'step' => $order->step,
                'biaya' => $order->biaya,
                'nominal' => $order->nominal,
                'mitraName' => $order->mitra_name,
                'mitraPhone' => $order->mitra_phone,
                'mitraId' => $order->mitra_id,
                'nego_by' => 'mitra',
            ],
        ]);
    }

    /**
     * Admin membatalkan atau menolak pesanan (Skenario B)
     */
    public function adminCancel(Request $request, int|string $id): JsonResponse
    {
        $idClean = trim((string) $id);
        $cleanCode = str_replace('#', '', $idClean);

        $order = Order::where('id', $idClean)
            ->orWhere('code', $idClean)
            ->orWhere('code', '#'.$cleanCode)
            ->orWhere('code', 'LIKE', '%'.$cleanCode.'%')
            ->first();

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $reason = trim((string) $request->input('reason')) ?: 'Pesanan melanggar ketentuan atau terindikasi transaksi berisiko.';

        $wasHeld = in_array($order->status, ['siap_bayar', 'terjadwal', 'otw', 'berjalan', 'menunggu_pembayaran'], true) || $order->escrow_status === 'held';

        $order->update([
            'status' => 'cancelled_by_admin',
            'cancelled_by' => 'admin',
            'cancellation_reason' => $reason,
            'escrow_status' => $wasHeld ? 'refunded' : 'none',
            'refund_amount' => $wasHeld ? ($order->nominal ?: 85000) : 0,
        ]);

        try {
            app(AdminRealtimeService::class)->recordAdminCancelOrder($order, $reason);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->code} berhasil dibatalkan oleh Admin. Notifikasi dan pengembalian dana telah diproses.",
            'order' => $order,
        ]);
    }

    /**
     * Mitra menolak atau membatalkan tugas setelah kesepakatan (Skenario C Kasus 2)
     */
    public function mitraCancel(Request $request, string $code): JsonResponse
    {
        $idClean = trim($code);
        $cleanCode = str_replace('#', '', $idClean);

        $order = Order::where('code', $idClean)
            ->orWhere('code', '#'.$cleanCode)
            ->orWhere('id', $idClean)
            ->first();

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $reason = trim((string) $request->input('reason')) ?: 'Kendala darurat / sakit mendesak.';

        // Ambil data mitra dari sesi atau pesanan
        $session = $request->session()->get('mitra_auth');
        $phone = $session['phone'] ?? null;
        $mitraId = $session['mitra_id'] ?? null;

        $mitra = null;
        if ($phone || $mitraId) {
            $mitra = Mitra::where(function ($q) use ($phone, $mitraId) {
                if ($phone) {
                    $q->where('phone', $phone);
                }
                if ($mitraId) {
                    $q->orWhere('mitra_id', $mitraId);
                }
            })->first();
        }

        // Penalti skor performa kepada mitra secara otomatis
        if ($mitra) {
            $mitra->increment('penalty_score', 10);
        }

        $order->update([
            'status' => 'waiting_for_partner',
            'mitra_name' => null,
            'cancellation_reason' => "Mitra berhalangan hadir: {$reason}",
        ]);

        try {
            app(AdminRealtimeService::class)->recordMitraCancelOrder($order, $mitra, $reason);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembatalan tugas dicatat. Penalti performa telah diterapkan otomatis, dan suruhan kembali disiarkan untuk mitra lain.',
            'penalty_score' => $mitra?->penalty_score,
            'order' => $order,
        ]);
    }

    /**
     * Customer melakukan pembatalan suruhan (Skenario D)
     */
    public function customerCancel(Request $request, string $code): JsonResponse
    {
        $idClean = trim($code);
        $cleanCode = str_replace('#', '', $idClean);

        $order = Order::where('code', $idClean)
            ->orWhere('code', '#'.$cleanCode)
            ->orWhere('id', $idClean)
            ->first();

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $hasAgreement = in_array($order->status, ['siap_bayar', 'terjadwal', 'otw', 'berjalan', 'menunggu_pembayaran', 'menunggu_konfirmasi_admin'], true);
        $reason = trim((string) $request->input('reason')) ?: ($hasAgreement ? 'Perubahan rencana mendesak dari pelanggan.' : 'Dibatalkan oleh pelanggan sebelum kesepakatan mitra.');

        $wasHeld = $hasAgreement && ($order->escrow_status === 'held' || in_array($order->status, ['siap_bayar', 'terjadwal', 'otw', 'berjalan']));

        $order->update([
            'status' => 'cancelled_by_customer',
            'cancelled_by' => 'customer',
            'cancellation_reason' => $reason,
            'escrow_status' => $wasHeld ? 'refunded' : 'none',
            'refund_amount' => $wasHeld ? ($order->nominal ?: 85000) : 0,
        ]);

        try {
            app(AdminRealtimeService::class)->recordCustomerCancelOrder($order, $reason, $hasAgreement);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => $hasAgreement
                ? 'Pekerjaan berhasil dibatalkan. Pengembalian dana (refund) diproses sesuai regulasi platform.'
                : 'Pengajuan suruhan berhasil dibatalkan dan ditarik dari antrean mitra.',
            'has_agreement' => $hasAgreement,
            'order' => $order,
        ]);
    }

    /**
     * Customer menyelesaikan proses pembayaran (Tahap 6-7).
     */
    public function customerPay(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'metode' => ['required', 'string', 'max:100'],
        ]);
        if (in_array($order->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
            || $order->escrow_status === 'refunded') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat dikirim untuk pesanan yang telah dibatalkan.',
            ], 409);
        }

        if ($order->payment_verified_at !== null
            || in_array($order->escrow_status, ['held', 'released_to_mitra', 'released'], true)
            || in_array($order->status, ['terjadwal', 'otw', 'berjalan', 'selesai'], true)) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran pesanan ini sudah dikonfirmasi Admin.',
                'order' => $order,
            ]);
        }

        if ($order->payment_submitted_at !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan pembayaran sudah dikirim dan sedang menunggu konfirmasi Admin.',
                'order' => $order,
            ]);
        }

        $order->update([
            'status' => 'menunggu_konfirmasi_pembayaran',
            'step' => 7,
            'escrow_status' => 'holding',
            'payment_method' => $validated['metode'],
            'payment_submitted_at' => now(),
        ]);

        $cleanCode = ltrim($order->code, '#');

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'system',
                'sender_name' => 'Sistem SayaBantu',
                'message' => 'Customer telah melaporkan pembayaran via '.$validated['metode'].'. Menunggu verifikasi Admin.',
                'message_type' => 'system',
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Could not record customer payment message.', [
                'order_code' => $order->code,
                'exception' => $exception,
            ]);
        }

        try {
            app(AdminRealtimeService::class)->recordPaymentSubmission($order, $validated['metode']);
        } catch (\Throwable $exception) {
            Log::warning('Could not publish customer payment submission to the admin realtime state.', [
                'order_code' => $order->code,
                'exception' => $exception,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan pembayaran via '.$validated['metode'].' berhasil dikirim. Pembayaran akan masuk ke saldo Admin setelah dikonfirmasi.',
            'order' => $order,
        ]);
    }

    /**
     * Mitra memulai perjalanan menuju lokasi Customer (Tahap 9A).
     */
    public function mitraOtw(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'terjadwal' || $order->payment_verified_at === null || $order->escrow_status !== 'held') {
            return response()->json([
                'success' => false,
                'message' => 'Mitra hanya dapat memulai perjalanan setelah pembayaran diverifikasi Admin dan jadwal dikonfirmasi.',
            ], 409);
        }

        $order->update([
            'status' => 'otw',
            'step' => 9,
        ]);

        $cleanCode = ltrim($order->code, '#');
        $mitraName = $order->mitra_name ?: 'Mitra';

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'mitra',
                'sender_name' => $mitraName,
                'message' => "Mitra {$mitraName} sedang dalam perjalanan menuju lokasi Anda (OTW).",
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        try {
            app(AdminRealtimeService::class)->updateOrderStatus($order->code, 'Diproses');
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah: Mitra Sedang OTW.',
            'order' => $order,
        ]);
    }

    /**
     * Mitra tiba di lokasi dan memulai pengerjaan (Tahap 9B).
     */
    public function mitraKerja(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'otw') {
            return response()->json([
                'success' => false,
                'message' => 'Mitra hanya dapat memulai pekerjaan setelah status perjalanan menjadi OTW.',
            ], 409);
        }

        $order->update([
            'status' => 'berjalan',
            'step' => 10,
        ]);

        $cleanCode = ltrim($order->code, '#');
        $mitraName = $order->mitra_name ?: 'Mitra';

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'mitra',
                'sender_name' => $mitraName,
                'message' => "Mitra {$mitraName} telah tiba di lokasi dan sedang melaksanakan pekerjaan.",
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        try {
            app(AdminRealtimeService::class)->updateOrderStatus($order->code, 'Diproses');
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah: Sedang Dikerjakan.',
            'order' => $order,
        ]);
    }

    /**
     * Mitra menyatakan pekerjaan telah selesai (Tahap 9C).
     */
    public function mitraSelesai(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'berjalan') {
            return response()->json([
                'success' => false,
                'message' => 'Mitra hanya dapat menyatakan pekerjaan selesai setelah pekerjaan dimulai.',
            ], 409);
        }

        $order->update([
            'status' => 'menunggu_konfirmasi_customer',
            'step' => 10,
        ]);

        $cleanCode = ltrim($order->code, '#');
        $mitraName = $order->mitra_name ?: 'Mitra';

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'mitra',
                'sender_name' => $mitraName,
                'message' => "Mitra {$mitraName} menyatakan pekerjaan telah selesai. Silakan periksa hasil kerja di lokasi Anda.",
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        try {
            app(AdminRealtimeService::class)->updateOrderStatus($order->code, 'Diproses');
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan dinyatakan selesai oleh Mitra. Menunggu konfirmasi Customer.',
            'order' => $order,
        ]);
    }

    /**
     * Customer mengonfirmasi bahwa pekerjaan telah selesai sesuai kesepakatan (Tahap 10).
     */
    public function customerSelesai(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($order->status !== 'menunggu_konfirmasi_customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer hanya dapat mengonfirmasi pekerjaan yang telah dinyatakan selesai oleh Mitra.',
            ], 409);
        }

        $order->update([
            'status' => 'menunggu_konfirmasi_admin_selesai',
            'step' => 11,
        ]);

        $cleanCode = ltrim($order->code, '#');
        $customerName = $order->customer_name ?: 'Customer';

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'customer',
                'sender_name' => $customerName,
                'message' => 'Customer telah mengonfirmasi bahwa pekerjaan selesai dengan baik. Menunggu penutupan akhir & pencairan dana oleh Admin.',
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Could not record customer work completion message.', [
                'order_code' => $order->code,
                'exception' => $e,
            ]);
        }

        try {
            app(AdminRealtimeService::class)->recordCustomerWorkCompletion($order);
        } catch (\Throwable $exception) {
            Log::warning('Could not publish customer work completion to the admin dashboard.', [
                'order_code' => $order->code,
                'exception' => $exception,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan telah dikonfirmasi selesai oleh Customer. Menunggu verifikasi akhir Admin.',
            'order' => $order,
        ]);
    }

    /**
     * Admin melakukan konfirmasi akhir penyelesaian dan menutup pesanan (Tahap 11).
     */
    public function adminSelesai(Request $request, string $code): JsonResponse
    {
        $result = app(AdminRealtimeService::class)->confirmCompletion($code);
        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Pesanan belum memenuhi syarat untuk dikonfirmasi selesai.',
            ], $result['http_status'] ?? 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan dikonfirmasi selesai oleh Admin. Dana tetap ditahan sampai diteruskan ke Mitra.',
            'order' => $this->findOrder($code),
        ]);
    }

    /**
     * Customer memberikan ulasan dan rating untuk pesanan yang telah selesai (Tahap 11).
     */
    public function submitReview(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if (! in_array($order->status, ['menunggu_konfirmasi_admin_selesai', 'selesai'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan hanya dapat diberikan setelah Customer mengonfirmasi pekerjaan selesai.',
            ], 409);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'review_rating' => $validated['rating'],
            'review_comment' => $validated['comment'] ?? '',
            'reviewed_at' => now(),
        ]);

        $cleanCode = ltrim($order->code, '#');
        $customerName = $order->customer_name ?: 'Customer';
        $starText = str_repeat('★', (int) $validated['rating']);
        $reviewComment = ! empty($validated['comment']) ? " ('{$validated['comment']}')" : '';

        if ($order->mitra_id || $order->mitra_name || $order->mitra_phone) {
            try {
                $mitra = Mitra::where('mitra_id', $order->mitra_id)
                    ->orWhere('phone', $order->mitra_phone)
                    ->orWhere('name', $order->mitra_name)
                    ->first();
                if ($mitra) {
                    $avgRating = Order::where(function ($q) use ($mitra) {
                        $q->where('mitra_id', $mitra->mitra_id)
                            ->orWhere('mitra_name', $mitra->name);
                    })->whereNotNull('review_rating')->avg('review_rating');
                    if ($avgRating) {
                        $mitra->update(['rating' => round($avgRating, 1)]);
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'customer',
                'sender_name' => $customerName,
                'message' => "Customer {$customerName} memberikan ulasan {$starText} ({$validated['rating']}/5){$reviewComment}.",
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        try {
            $adminService = app(AdminRealtimeService::class);
            $state = $adminService->getState();
            $now = time();
            array_unshift($state['recent_activities'], [
                'id' => 'act-'.uniqid(),
                'title' => "Ulasan bintang {$validated['rating']}.0 masuk",
                'subtitle' => "Customer {$customerName}: \"".($validated['comment'] ?: 'Layanan sangat memuaskan').'"',
                'type' => 'review',
                'icon' => 'fa-star',
                'color_class' => 'bg-amber-50 text-amber-600',
                'time' => 'Baru saja',
                'timestamp' => $now,
            ]);
            $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);
            $adminService->saveState($state);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Ulasan dan penilaian berhasil dikirim. Terima kasih atas apresiasi Anda!',
            'order' => $order,
            'review' => [
                'rating' => $order->review_rating,
                'comment' => $order->review_comment,
                'reviewed_at' => $order->reviewed_at?->toIso8601String(),
                'mitra_name' => $order->mitra_name,
                'customer_name' => $order->customer_name,
            ],
        ]);
    }

    /**
     * Admin meneruskan dana escrow ke mitra (90% hak mitra, 10% fee platform SayaBantu).
     */
    public function adminReleaseEscrow(Request $request, string $code): JsonResponse
    {
        $order = $this->findOrder($code);
        if ($order === null) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if (in_array($order->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
            || $order->escrow_status === 'refunded') {
            return response()->json(['success' => false, 'message' => 'Dana pesanan yang dibatalkan atau dikembalikan tidak dapat diteruskan ke mitra.'], 409);
        }

        if ($order->payment_verified_at === null || $order->escrow_status !== 'held') {
            return response()->json(['success' => false, 'message' => 'Pembayaran harus dikonfirmasi Admin dan ditahan di escrow sebelum dana diteruskan ke mitra.'], 409);
        }

        if ($order->status !== 'selesai') {
            return response()->json(['success' => false, 'message' => 'Dana otomatis ditahan sampai Customer dan Admin mengonfirmasi pekerjaan selesai.'], 409);
        }

        $nom = $order->nominal > 0 ? $order->nominal : (int) preg_replace('/[^0-9]/', '', (string) $order->biaya);
        $adminFee = (int) round($nom * 0.10);
        $mitraFee = (int) round($nom * 0.90);

        $order->update([
            'escrow_status' => 'released_to_mitra',
            'escrow_hold_reason' => null,
            'escrow_released_at' => now(),
            'admin_fee' => $adminFee,
            'mitra_fee' => $mitraFee,
        ]);

        $cleanCode = ltrim($order->code, '#');
        try {
            ChatMessage::create([
                'order_code' => $cleanCode,
                'sender_type' => 'admin',
                'sender_name' => 'Admin Keuangan SayaBantu',
                'message' => 'Dana escrow sebesar Rp '.number_format($mitraFee, 0, ',', '.').' (90%) telah resmi disalurkan dan diteruskan ke rekening mitra.',
                'message_type' => 'system',
            ]);
        } catch (\Throwable $e) {
        }

        try {
            $adminService = app(AdminRealtimeService::class);
            $state = $adminService->getState();
            array_unshift($state['recent_activities'], [
                'id' => 'act-'.uniqid(),
                'title' => 'Dana Diteruskan ke Mitra',
                'subtitle' => "#{$cleanCode}: Rp ".number_format($mitraFee, 0, ',', '.')." disalurkan ke {$order->mitra_name}",
                'type' => 'payment',
                'icon' => 'fa-money-bill-transfer',
                'color_class' => 'bg-emerald-50 text-emerald-600',
                'time' => 'Baru saja',
                'timestamp' => time(),
            ]);
            $state['recent_activities'] = array_slice($state['recent_activities'], 0, 15);
            $adminService->saveState($state);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Dana sebesar Rp '.number_format($mitraFee, 0, ',', '.').' (90%) berhasil diteruskan ke Mitra. Saldo fee admin 10% sebesar Rp '.number_format($adminFee, 0, ',', '.').' telah tercatat.',
            'order' => $order,
        ]);
    }

    /**
     * Helper untuk menemukan pesanan berdasarkan kode atau ID.
     */
    private function findOrder(string $code): ?Order
    {
        $idClean = trim($code);
        $cleanCode = str_replace('#', '', $idClean);
        $numId = preg_replace('/[^0-9]/', '', $cleanCode);

        return Order::where('code', $idClean)
            ->orWhere('code', '#'.$cleanCode)
            ->orWhere('code', $cleanCode)
            ->orWhere('id', $idClean)
            ->when($cleanCode !== '', fn ($q) => $q->orWhere('code', 'like', "%{$cleanCode}%"))
            ->when($numId !== '', fn ($q) => $q->orWhere('code', 'like', "%{$numId}%"))
            ->first();
    }
}

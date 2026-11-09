<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRealtimeController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\MitraAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HALAMAN CUSTOMER & BERANDA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $authUser = Auth::user();
    $sessionAuth = session('customer_auth') ?? [];
    $customerName = $authUser?->name ?? session('customer_name') ?? ($sessionAuth['name'] ?? null);
    $customerPhone = $authUser?->phone ?? session('customer_phone') ?? ($sessionAuth['phone'] ?? null);
    $customerId = $authUser?->id ?? null;

    if ($customerName) {
        session(['customer_name' => $customerName]);
    }
    if ($customerPhone) {
        session(['customer_phone' => $customerPhone]);
    }

    $homeMitras = \Illuminate\Support\Facades\Schema::hasTable('mitras')
        ? Mitra::query()
            ->whereIn('status', ['aktif', 'approved', 'disetujui'])
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating')
            ->orderBy('kota')
            ->orderBy('name')
            ->get()
        : collect();
    $latestReviews = \Illuminate\Support\Facades\Schema::hasTable('orders')
        ? Order::query()
            ->whereNotNull('review_rating')
            ->latest('reviewed_at')
            ->limit(3)
            ->get(['customer_name', 'review_rating', 'review_comment', 'layanan', 'reviewed_at'])
        : collect();

    return view('customer.index', [
        'customerName' => $customerName,
        'customerPhone' => $customerPhone,
        'customerId' => $customerId,
        'homeMitras' => $homeMitras,
        'mitraCities' => $homeMitras->pluck('kota')->filter()->unique()->values(),
        'latestReviews' => $latestReviews,
    ]);
})->name('customer.home');

Route::get('/customer', function () {
    return redirect()->route('customer.dashboard');
});

Route::get('/customer/beranda', function () {
    return redirect()->route('customer.index');
});

Route::get('/pengunjung', function () {
    $homeMitras = \Illuminate\Support\Facades\Schema::hasTable('mitras')
        ? Mitra::query()
            ->whereIn('status', ['aktif', 'approved', 'disetujui'])
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating')
            ->orderBy('kota')
            ->orderBy('name')
            ->get()
        : collect();
    $latestReviews = \Illuminate\Support\Facades\Schema::hasTable('orders')
        ? Order::query()
            ->whereNotNull('review_rating')
            ->latest('reviewed_at')
            ->limit(3)
            ->get(['customer_name', 'review_rating', 'review_comment', 'layanan', 'reviewed_at'])
        : collect();

    return view('customer.index', [
        'isPengunjung' => true,
        'homeMitras' => $homeMitras,
        'mitraCities' => $homeMitras->pluck('kota')->filter()->unique()->values(),
        'latestReviews' => $latestReviews,
    ]);
})->name('customer.index');

Route::get('/customer/suruhan/baru', function () {
    return view('customer.index', ['isOrderPage' => true]);
})->name('customer.suruhan.create');

Route::get('/customer/dashboard', function (Request $request) {
    $authUser = Auth::user();
    $sessionAuth = session('customer_auth') ?? [];

    $customerName = $authUser?->name ?? session('customer_name') ?? ($sessionAuth['name'] ?? null);
    $customerPhone = $authUser?->phone ?? session('customer_phone') ?? ($sessionAuth['phone'] ?? null);

    if ($customerName) {
        session(['customer_name' => $customerName]);
    }
    if ($customerPhone) {
        session(['customer_phone' => $customerPhone]);
    }

    $activeOrder = null;
    $customerOrders = collect();

    if ($customerPhone || $customerName) {
        $activeOrder = Order::where(function ($q) use ($customerPhone, $customerName) {
            $hasCond = false;
            if (! empty($customerPhone)) {
                $cleanP = preg_replace('/[^0-9]/', '', $customerPhone);
                $q->where('customer_phone', $customerPhone)
                    ->orWhere('customer_phone', $cleanP);
                if (strlen($cleanP) >= 8) {
                    $q->orWhere('customer_phone', 'like', '%'.substr($cleanP, -8));
                }
                $hasCond = true;
            }
            if (! empty($customerName)) {
                if ($hasCond) {
                    $q->orWhere('customer_name', $customerName);
                } else {
                    $q->where('customer_name', $customerName);
                }
            }
        })
            ->whereNotIn('status', ['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin', 'semua_ditolak', 'selesai'])
            ->latest('updated_at')
            ->first();

        $customerOrders = Order::where(function ($q) use ($customerPhone, $customerName) {
            $hasCond = false;
            if (! empty($customerPhone)) {
                $cleanP = preg_replace('/[^0-9]/', '', $customerPhone);
                $q->where('customer_phone', $customerPhone)
                    ->orWhere('customer_phone', $cleanP);
                if (strlen($cleanP) >= 8) {
                    $q->orWhere('customer_phone', 'like', '%'.substr($cleanP, -8));
                }
                $hasCond = true;
            }
            if (! empty($customerName)) {
                if ($hasCond) {
                    $q->orWhere('customer_name', $customerName);
                } else {
                    $q->where('customer_name', $customerName);
                }
            }
        })
            ->latest('updated_at')
            ->get();
    }

    $reviewableOrder = $customerOrders->first(fn (Order $order) => in_array($order->status, [
        'menunggu_konfirmasi_admin_selesai',
        'selesai',
    ], true) && $order->review_rating === null);

    $reviewQuery = Order::query()->whereNotNull('review_rating');
    $customerAverageRating = (clone $reviewQuery)->avg('review_rating');
    $customerReviewCount = (clone $reviewQuery)->count();
    $latestReviews = (clone $reviewQuery)
        ->latest('reviewed_at')
        ->limit(3)
        ->get(['customer_name', 'review_rating', 'review_comment', 'layanan', 'reviewed_at']);

    $activeTab = $request->query('tab', 'dashboard');
    if (! in_array($activeTab, ['dashboard', 'layanan', 'pesanan', 'pesan', 'profile'])) {
        $activeTab = 'dashboard';
    }

    return view('customer.dashboard', [
        'serverActiveOrder' => $activeOrder,
        'serverCustomerOrders' => $customerOrders,
        'reviewableOrder' => $reviewableOrder,
        'customerName' => $customerName ?: 'Customer',
        'customerPhone' => $customerPhone ?: '',
        'customerId' => $authUser?->id ?? null,
        'activeTab' => $activeTab,
        'customerAverageRating' => $customerAverageRating,
        'customerReviewCount' => $customerReviewCount,
        'latestReviews' => $latestReviews,
    ]);
})->name('customer.dashboard');

/*
|--------------------------------------------------------------------------
| PEMBAYARAN SURUHAN CUSTOMER
|--------------------------------------------------------------------------
| Halaman pembayaran setelah customer mengajukan suruhan: ringkasan pesanan,
| pilihan metode bayar, rincian biaya, dan batas waktu pembayaran.
| Data masih contoh (statis) mengikuti pola halaman customer lain.
*/
Route::get('/customer/pembayaran/{kode}', function (Request $request, string $kode) {
    // ---------------------------------------------------------------------
    // GERBANG KESEPAKATAN MITRA (Opsi A)
    // ---------------------------------------------------------------------
    // Customer TIDAK boleh membayar sebelum ada mitra yang menyetujui harga
    // dan jadwal. Mengikuti alur resmi di form:
    //   langkah 4 mitra lihat kebutuhan -> langkah 5 mitra acc/tawar ->
    //   langkah 6 admin verifikasi -> langkah 7 pembayaran dibuka.
    // Selama 'kesepakatan' belum 'disetujui', halaman hanya menampilkan status
    // dan tombol ke chat, tombol Bayar tidak pernah dirender.
    // ---------------------------------------------------------------------
    $cleanCode = ltrim($kode, '#');
    $dbOrder = Order::where('code', $kode)->orWhere('code', '#'.$cleanCode)->orWhere('code', $cleanCode)->first();

    $statusParam = $request->string('status')->trim()->value();
    $dbStatus = $dbOrder?->status;
    $isPaymentRefunded = in_array($dbStatus, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
        || $dbOrder?->escrow_status === 'refunded';
    $isPaymentVerified = ! $isPaymentRefunded && (
        $dbOrder?->payment_verified_at !== null
        || ($dbOrder?->payment_submitted_at === null && (
            in_array($dbStatus, ['terjadwal', 'otw', 'berjalan', 'selesai'], true)
            || in_array($dbOrder?->escrow_status, ['held', 'released_to_mitra', 'released'], true)
        ))
    );
    $hasAgreedMitra = ! empty($dbOrder?->mitra_name) && ! empty($dbOrder?->biaya);
    $isAgreedStatus = in_array($statusParam ?: $dbStatus, [
        'siap_bayar',
        'menunggu_pembayaran',
        'menunggu_konfirmasi_pembayaran',
        'terjadwal',
        'dibayar',
        'lunas',
        'otw',
        'berjalan',
        'selesai',
    ], true);

    $kesepakatan = $request->string('kesepakatan')->trim()->value() ?: (($isAgreedStatus || $hasAgreedMitra) ? 'disetujui' : 'menunggu_mitra');
    $kesepakatanDisetujui = in_array($kesepakatan, ['disetujui', 'sepakat'], true) || $isAgreedStatus || $hasAgreedMitra;
    $metodeHarga = $request->string('metode')->trim()->value() ?: ($dbOrder?->metode ?: 'Nego');
    $totalRaw = $request->string('total')->trim()->value() ?: ($request->string('biaya')->trim()->value() ?: ($dbOrder?->biaya ?: '50.000'));

    // Hitung rincian biaya: jika 20 ya 20, tidak ada biaya platform tambahan
    $numTotal = (int) preg_replace('/[^0-9]/', '', $totalRaw) ?: ($dbOrder?->nominal ?: 20000);
    $finalTotal = max(10000, $numTotal);
    $finalTotalFormatted = number_format($finalTotal, 0, ',', '.');

    return view('customer.pembayaran', [
        'kodePesanan' => $kode,
        'orderStatus' => $isPaymentRefunded
            ? $dbStatus
            : ($isPaymentVerified
                ? ($dbStatus ?: 'terjadwal')
                : ($dbOrder?->payment_submitted_at
                    ? 'menunggu_konfirmasi_pembayaran'
                    : ($dbStatus ?: ($statusParam ?: 'menunggu_pembayaran')))),
        'metodePembayaranTercatat' => $dbOrder?->payment_method,
        'waktuPembayaranTercatat' => $dbOrder?->payment_submitted_at?->format('d M Y, H:i'),
        'namaLayanan' => $request->string('layanan')->trim()->value() ?: ($dbOrder?->layanan ?: 'Layanan SayaBantu'),
        'jadwalPesanan' => $request->string('jadwal')->trim()->value() ?: ($dbOrder?->jadwal ?: 'Hari ini, 14:30 WIB'),
        'alamatPesanan' => $request->string('alamat')->trim()->value() ?: ($dbOrder?->alamat ?: 'Puskesmas Bandung'),
        'preferensiGender' => $request->string('gender')->trim()->value() ?: ($dbOrder?->gender ?: 'Bebas'),
        'totalBayar' => $finalTotalFormatted,
        'kesepakatan' => $kesepakatan,
        'kesepakatanDisetujui' => $kesepakatanDisetujui,
        'metodeHarga' => $metodeHarga,
        'namaMitra' => $request->string('mitra')->trim()->value() ?: ($dbOrder?->mitra_name ?: 'Mitra'),
        // Hanya pembayaran non-tunai. Setiap metode wajib membawa detail
        // pembayaran lengkap: barcode untuk QRIS, dan nama bank/e-wallet + nomor
        // tujuan untuk transfer maupun e-wallet.
        'metodePembayaran' => [
            [
                'kode' => 'qris',
                'nama' => 'QRIS',
                'ket' => 'Scan barcode, langsung terbayar',
                'ikon' => 'fa-qrcode',
                'kelas' => 'bg-purple-50 text-purple-600 border border-purple-200',
                'tipe' => 'qris',
                'barcode' => 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data='.rawurlencode('SAYABANTU|QRIS|'.$kode.'|'.$finalTotal),
                'atasNama' => 'PT SayaBantu Digital Nusantara',
                'catatan' => 'Buka aplikasi m-banking atau e-wallet, pilih menu QRIS, lalu scan barcode di atas. Nominal terisi otomatis.',
            ],
            [
                'kode' => 'bca',
                'nama' => 'Transfer Bank BCA',
                'ket' => 'Transfer ke rekening BCA',
                'ikon' => 'fa-building-columns',
                'kelas' => 'bg-blue-50 text-blue-600 border border-blue-200',
                'tipe' => 'bank',
                'bank' => 'BCA',
                'nomor' => '8830 1122 4455',
                'atasNama' => 'PT SayaBantu Digital Nusantara',
                'catatan' => 'Transfer tepat sesuai nominal, lalu simpan bukti transfer. Verifikasi otomatis 1-3 menit.',
            ],
            [
                'kode' => 'mandiri',
                'nama' => 'Transfer Bank Mandiri',
                'ket' => 'Transfer ke rekening Mandiri',
                'ikon' => 'fa-building-columns',
                'kelas' => 'bg-indigo-50 text-indigo-600 border border-indigo-200',
                'tipe' => 'bank',
                'bank' => 'Mandiri',
                'nomor' => '1370 0099 8877',
                'atasNama' => 'PT SayaBantu Digital Nusantara',
                'catatan' => 'Transfer tepat sesuai nominal, lalu simpan bukti transfer. Verifikasi otomatis 1-3 menit.',
            ],
            [
                'kode' => 'bni',
                'nama' => 'Transfer Bank BNI',
                'ket' => 'Transfer ke rekening BNI',
                'ikon' => 'fa-building-columns',
                'kelas' => 'bg-orange-50 text-orange-600 border border-orange-200',
                'tipe' => 'bank',
                'bank' => 'BNI',
                'nomor' => '0998 7766 554',
                'atasNama' => 'PT SayaBantu Digital Nusantara',
                'catatan' => 'Transfer tepat sesuai nominal, lalu simpan bukti transfer. Verifikasi otomatis 1-3 menit.',
            ],
            [
                'kode' => 'bri',
                'nama' => 'Transfer Bank BRI',
                'ket' => 'Transfer ke rekening BRI',
                'ikon' => 'fa-building-columns',
                'kelas' => 'bg-sky-50 text-sky-600 border border-sky-200',
                'tipe' => 'bank',
                'bank' => 'BRI',
                'nomor' => '3320 0101 2233 445',
                'atasNama' => 'PT SayaBantu Digital Nusantara',
                'catatan' => 'Transfer tepat sesuai nominal, lalu simpan bukti transfer. Verifikasi otomatis 1-3 menit.',
            ],
            [
                'kode' => 'gopay',
                'nama' => 'GoPay',
                'ket' => 'E-Wallet GoPay',
                'ikon' => 'fa-wallet',
                'kelas' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                'tipe' => 'ewallet',
                'bank' => 'GoPay',
                'nomor' => '0812 8899 4455',
                'atasNama' => 'SayaBantu Digital',
                'catatan' => 'Kirim saldo GoPay tepat sesuai nominal ke nomor tujuan, lalu simpan bukti transaksi.',
            ],
            [
                'kode' => 'ovo',
                'nama' => 'OVO',
                'ket' => 'E-Wallet OVO',
                'ikon' => 'fa-wallet',
                'kelas' => 'bg-violet-50 text-violet-600 border border-violet-200',
                'tipe' => 'ewallet',
                'bank' => 'OVO',
                'nomor' => '0812 8899 4455',
                'atasNama' => 'SayaBantu Digital',
                'catatan' => 'Kirim saldo OVO tepat sesuai nominal ke nomor tujuan, lalu simpan bukti transaksi.',
            ],
            [
                'kode' => 'dana',
                'nama' => 'DANA',
                'ket' => 'E-Wallet DANA',
                'ikon' => 'fa-wallet',
                'kelas' => 'bg-blue-50 text-blue-600 border border-blue-200',
                'tipe' => 'ewallet',
                'bank' => 'DANA',
                'nomor' => '0812 8899 4455',
                'atasNama' => 'SayaBantu Digital',
                'catatan' => 'Kirim saldo DANA tepat sesuai nominal ke nomor tujuan, lalu simpan bukti transaksi.',
            ],
            [
                'kode' => 'shopeepay',
                'nama' => 'ShopeePay',
                'ket' => 'E-Wallet ShopeePay',
                'ikon' => 'fa-wallet',
                'kelas' => 'bg-rose-50 text-rose-600 border border-rose-200',
                'tipe' => 'ewallet',
                'bank' => 'ShopeePay',
                'nomor' => '0812 8899 4455',
                'atasNama' => 'SayaBantu Digital',
                'catatan' => 'Kirim saldo ShopeePay tepat sesuai nominal ke nomor tujuan, lalu simpan bukti transaksi.',
            ],
        ],
        'rincianBiaya' => [
            ['label' => 'Biaya layanan', 'nilai' => 'Rp '.$finalTotalFormatted, 'diskon' => false],
            ['label' => 'Biaya platform', 'nilai' => 'Gratis (Rp 0)', 'diskon' => true],
        ],
    ]);
})->name('customer.pembayaran');

/*
|--------------------------------------------------------------------------
| DETAIL PESANAN CUSTOMER
|--------------------------------------------------------------------------
| Halaman rincian satu pesanan mengambil status, mitra, lokasi, dan biaya
| dari catatan pesanan yang tersimpan.
*/
Route::get('/customer/pesanan', function (Request $request) {
    $latest = Order::latest('updated_at')->first();
    if ($latest) {
        return redirect()->route('customer.pesanan.detail', ['kode' => ltrim($latest->code, '#')]);
    }

    return redirect()->route('customer.dashboard');
});

Route::get('/customer/pesanan/{kode}', function (Request $request, string $kode) {
    $cleanKode = ltrim($kode, '#');
    $dbOrder = Order::where('code', $kode)
        ->orWhere('code', '#'.$cleanKode)
        ->orWhere('code', $cleanKode)
        ->orWhere('id', $kode)
        ->when($cleanKode !== '', fn ($q) => $q->orWhere('code', 'like', "%{$cleanKode}%"))
        ->first();

    abort_unless($dbOrder, 404);

    $metode = $dbOrder->metode ?: 'Nego';
    $status = $dbOrder->status;
    $biaya = $dbOrder->biaya ?: 'Belum disepakati';
    $layanan = $dbOrder->layanan ?: 'Layanan belum diisi';
    $jadwal = $dbOrder->jadwal ?: 'Jadwal belum ditentukan';
    $alamat = $dbOrder->alamat ?: 'Alamat belum diisi';
    $catatan = $dbOrder->note ?: 'Tidak ada catatan.';
    $gender = $dbOrder->gender ?: 'Bebas';

    $mitra = null;
    if ($dbOrder->mitra_id || $dbOrder->mitra_phone || $dbOrder->mitra_name) {
        $mitraQuery = Mitra::query();
        if ($dbOrder->mitra_id) {
            $mitraQuery->where('mitra_id', $dbOrder->mitra_id);
        } elseif ($dbOrder->mitra_phone) {
            $mitraQuery->where('phone', $dbOrder->mitra_phone);
        } else {
            $mitraQuery->where('name', $dbOrder->mitra_name);
        }
        $mitra = $mitraQuery
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating')
            ->first();
    }

    $realBids = [];
    if ($mitra) {
        $realBids[] = [
            'id' => $mitra->mitra_id ?: (string) $mitra->id,
            'mitraId' => $mitra->mitra_id,
            'mitraName' => $mitra->name,
            'mitraPhone' => $mitra->phone,
            'rating' => $mitra->reviewed_orders_avg_review_rating === null
                ? null
                : number_format((float) $mitra->reviewed_orders_avg_review_rating, 1),
            'reviewCount' => $mitra->reviewed_orders_count,
            'tugasSelesai' => $mitra->completed_orders_count,
            'spesialis' => $mitra->keahlian ?: $mitra->service,
            'harga' => $dbOrder->nominal,
            'hargaFormatted' => $dbOrder->biaya ?: 'Belum disepakati',
            'durasi' => 'Belum ditentukan',
            'catatan' => $dbOrder->note ?: '',
            'status' => $dbOrder->status,
            'nego_by' => $dbOrder->nego_by,
        ];
    }

    $petaStatus = [
        'menunggu_penawaran' => [
            'label' => 'Menunggu Penawaran Mitra',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'ikon' => 'fa-hourglass-half',
            'step' => 4,
            'keterangan' => 'Suruhan telah disiarkan ke mitra terdekat. Menunggu respon & penawaran mitra.',
            'pembayaranBuka' => false,
        ],
        'menunggu_mitra' => [
            'label' => 'Menunggu Penawaran Mitra',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'ikon' => 'fa-hourglass-half',
            'step' => 4,
            'keterangan' => 'Suruhan telah disiarkan ke mitra terdekat. Menunggu respon & penawaran mitra.',
            'pembayaranBuka' => false,
        ],
        'menunggu_keputusan' => [
            'label' => 'Menunggu Keputusan Customer',
            'badge' => 'sky',
            'kelas' => 'bg-sky-100 text-[#173B67] border border-sky-300',
            'ikon' => 'fa-users-viewfinder',
            'step' => 5,
            'keterangan' => 'Terdapat penawaran dari beberapa Mitra. Silakan bandingkan profil, harga, dan pilih satu Mitra terbaik.',
            'pembayaranBuka' => false,
        ],
        'nego' => [
            'label' => 'Tahap Negosiasi Chat',
            'badge' => 'sky',
            'kelas' => 'bg-sky-100 text-[#173B67] border border-sky-300',
            'ikon' => 'fa-comments-dollar',
            'step' => 5,
            'keterangan' => 'Pengajuan aktif di Kebutuhan Mitra. Terdapat tawaran mitra yang dapat Anda bandingkan atau negosiasikan.',
            'pembayaranBuka' => false,
        ],
        'menunggu_konfirmasi_admin' => [
            'label' => 'Menunggu Konfirmasi Admin',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-900 border border-amber-400',
            'ikon' => 'fa-clipboard-check',
            'step' => 6,
            'keterangan' => 'Mitra telah Anda pilih! Kesepakatan sedang diverifikasi oleh Tim Admin SayaBantu sebelum pembayaran dibuka.',
            'pembayaranBuka' => false,
        ],
        'menunggu_pembayaran' => [
            'label' => 'Menunggu Pembayaran',
            'badge' => 'emerald',
            'kelas' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            'ikon' => 'fa-wallet',
            'step' => 7,
            'keterangan' => 'Kesepakatan disetujui Admin! Silakan selesaikan pembayaran untuk mengonfirmasi jadwal.',
            'pembayaranBuka' => true,
        ],
        'menunggu_konfirmasi_pembayaran' => [
            'label' => 'Menunggu Verifikasi Pembayaran',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-900 border border-amber-300',
            'ikon' => 'fa-receipt',
            'step' => 7,
            'keterangan' => 'Pembayaran telah dikirim. Menunggu verifikasi dari Admin sebelum jadwal pengerjaan dimulai.',
            'pembayaranBuka' => false,
        ],
        'siap_bayar' => [
            'label' => 'Menunggu Pembayaran',
            'badge' => 'emerald',
            'kelas' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            'ikon' => 'fa-wallet',
            'step' => 7,
            'keterangan' => 'Harga dan detail pekerjaan telah disepakati bersama Mitra & diverifikasi Admin. Silakan selesaikan pembayaran.',
            'pembayaranBuka' => true,
        ],
        'terjadwal' => [
            'label' => 'Jadwal Dikonfirmasi',
            'badge' => 'blue',
            'kelas' => 'bg-blue-100 text-[#173B67] border border-blue-300',
            'ikon' => 'fa-calendar-check',
            'step' => 8,
            'keterangan' => 'Pembayaran lunas! Jadwal pengerjaan telah dikonfirmasi dan mitra akan hadir sesuai waktu.',
            'pembayaranBuka' => true,
        ],
        'otw' => [
            'label' => 'Mitra Sedang OTW',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'ikon' => 'fa-motorcycle',
            'step' => 9,
            'keterangan' => 'Mitra sedang dalam perjalanan menuju lokasi Anda. Mohon pastikan ada orang di lokasi.',
            'pembayaranBuka' => true,
        ],
        'berjalan' => [
            'label' => 'Sedang Dikerjakan',
            'badge' => 'emerald',
            'kelas' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            'ikon' => 'fa-bolt',
            'step' => 10,
            'keterangan' => 'Mitra telah tiba di lokasi dan sedang melaksanakan pekerjaan.',
            'pembayaranBuka' => true,
        ],
        'menunggu_konfirmasi_customer' => [
            'label' => 'Menunggu Konfirmasi Customer',
            'badge' => 'sky',
            'kelas' => 'bg-sky-100 text-[#173B67] border border-sky-300',
            'ikon' => 'fa-user-check',
            'step' => 10,
            'keterangan' => 'Mitra telah menyelesaikan pekerjaan! Silakan periksa hasil kerja dan tekan tombol Selesaikan Pesanan.',
            'pembayaranBuka' => true,
        ],
        'menunggu_konfirmasi_admin_selesai' => [
            'label' => 'Menunggu Konfirmasi Akhir Admin',
            'badge' => 'indigo',
            'kelas' => 'bg-indigo-100 text-indigo-900 border border-indigo-300',
            'ikon' => 'fa-clipboard-check',
            'step' => 11,
            'keterangan' => 'Customer telah menyatakan pekerjaan selesai. Menunggu konfirmasi penutupan pesanan oleh Admin.',
            'pembayaranBuka' => true,
        ],
        'selesai' => [
            'label' => 'Selesai & Ulasan',
            'badge' => 'slate',
            'kelas' => 'bg-slate-100 text-slate-600 border border-slate-300',
            'ikon' => 'fa-circle-check',
            'step' => 11,
            'keterangan' => 'Pekerjaan selesai, dana diteruskan ke Mitra & ulasan telah diberikan.',
            'pembayaranBuka' => true,
        ],
        'semua_ditolak' => [
            'label' => 'Semua Penawaran Ditolak',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-ban',
            'step' => 0,
            'keterangan' => 'Seluruh penawaran mitra telah ditolak atau belum ada kesepakatan. Anda dapat mengubah detail atau mengajukan ulang.',
            'pembayaranBuka' => false,
        ],
        'dibatalkan' => [
            'label' => 'Dibatalkan',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-circle-xmark',
            'step' => 0,
            'keterangan' => 'Pengajuan suruhan telah dibatalkan.',
            'pembayaranBuka' => false,
        ],
        'waiting_for_partner' => [
            'label' => 'Mencari Mitra Pengganti',
            'badge' => 'amber',
            'kelas' => 'bg-amber-100 text-amber-800 border border-amber-300',
            'ikon' => 'fa-user-clock',
            'step' => 4,
            'keterangan' => 'Mitra sebelumnya berhalangan hadir. Sistem sedang mencarikan mitra pengganti untuk Anda.',
            'pembayaranBuka' => false,
        ],
        'cancelled_by_admin' => [
            'label' => 'Dibatalkan oleh Admin',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-shield-halved',
            'step' => 0,
            'keterangan' => 'Pesanan dibatalkan oleh Admin. Dana dikembalikan ke rekening/saldo Anda.',
            'pembayaranBuka' => false,
        ],
        'cancelled_by_customer' => [
            'label' => 'Dibatalkan oleh Customer',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-circle-xmark',
            'step' => 0,
            'keterangan' => 'Pengajuan suruhan telah dibatalkan atas permintaan Anda.',
            'pembayaranBuka' => false,
        ],
        'konflik' => [
            'label' => 'Status Konflik Mediasi',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-triangle-exclamation',
            'step' => 0,
            'keterangan' => 'Pesanan ini sedang dalam mediasi oleh Tim Admin karena adanya laporan atau kendala.',
            'pembayaranBuka' => false,
        ],
        'conflict' => [
            'label' => 'Status Konflik Mediasi',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-triangle-exclamation',
            'step' => 0,
            'keterangan' => 'Pesanan ini sedang dalam mediasi oleh Tim Admin karena adanya laporan atau kendala.',
            'pembayaranBuka' => false,
        ],
        'komplain' => [
            'label' => 'Komplain Mediasi Admin',
            'badge' => 'rose',
            'kelas' => 'bg-rose-100 text-rose-700 border border-rose-300',
            'ikon' => 'fa-triangle-exclamation',
            'step' => 0,
            'keterangan' => 'Komplain sedang ditangani oleh Admin SayaBantu untuk penyelesaian masalah.',
            'pembayaranBuka' => false,
        ],
    ];
    $dataStatus = $petaStatus[$status] ?? $petaStatus['menunggu_keputusan'];

    return view('customer.pesanan-detail', [
        'kodePesanan' => $kode,
        'statusKode' => $status,
        'statusPesanan' => $dataStatus['label'],
        'badgeStatus' => $dataStatus['badge'],
        'kelasStatus' => $dataStatus['kelas'],
        'ikonStatus' => $dataStatus['ikon'],
        'stepAktif' => $dataStatus['step'],
        'keteranganStatus' => $dataStatus['keterangan'],
        'pembayaranBuka' => $dataStatus['pembayaranBuka'],
        'metodeHarga' => $metode,
        'namaLayanan' => $layanan,
        'deskripsiLayanan' => 'Layanan bantuan resmi SayaBantu dengan garansi keamanan transaksi & pengerjaan mitra profesional.',
        'jadwalPesanan' => $jadwal,
        'biayaPesanan' => $biaya,
        'preferensiGender' => $gender,
        'namaMitra' => $request->string('mitra')->trim()->value() ?: ($dbOrder?->mitra_name ?: 'Mitra'),
        'inisialMitra' => strtoupper(substr($request->string('mitra')->trim()->value() ?: ($dbOrder?->mitra_name ?: 'K'), 0, 2)),
        'ratingMitra' => $mitra?->reviewed_orders_avg_review_rating === null
            ? null
            : number_format((float) $mitra->reviewed_orders_avg_review_rating, 1),
        'jumlahTugasMitra' => $mitra?->completed_orders_count ?? 0,
        'alamatPesanan' => $alamat,
        'catatanPesanan' => $catatan,
        'waMitra' => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $dbOrder?->mitra_phone ?: '6281234567890').'?text='.rawurlencode('Halo '.($dbOrder?->mitra_name ?: 'Mitra').', saya pemesan suruhan '.$layanan.' (Tiket '.$kode.')'),
        'waCs' => 'https://wa.me/6281234567890?text='.rawurlencode('Halo CS SayaBantu, saya butuh bantuan terkait suruhan '.$kode),
        'realBids' => $realBids,
        'dbOrder' => $dbOrder,
        'rincianBiaya' => [
            ['label' => 'Biaya Jasa Kesepakatan', 'nilai' => 'Rp '.$biaya, 'tebal' => true],
            ['label' => 'Biaya Layanan Platform',  'nilai' => 'Gratis (Rp 0)',    'tebal' => false],
        ],
    ]);
})->name('customer.pesanan.detail');

/*
|--------------------------------------------------------------------------
| HALAMAN PUBLIK (BERSIH TANPA PREFIX KHUSUS PENGUNJUNG UMUM)
|--------------------------------------------------------------------------
*/
Route::get('/layanan', function () {
    return view('customer.layanan');
})->name('customer.layanan');

Route::get('/layanan/detail', function (Request $request) {
    return view('customer.service-detail', [
        'serviceName' => $request->string('name')->trim()->value() ?: 'Layanan SayaBantu',
        'serviceDescription' => $request->string('description')->trim()->value() ?: 'Layanan bantuan sesuai kebutuhan Anda.',
        'serviceBudget' => $request->string('budget')->trim()->value() ?: 'Menyesuaikan kebutuhan',
    ]);
})->name('customer.service');

Route::get('/customer/layanan', function () {
    return redirect()->route('customer.layanan');
});

Route::get('/customer/layanan/detail', function (Request $request) {
    return redirect()->route('customer.service', $request->query());
});

Route::get('/customer/detail-layanan', function (Request $request) {
    return redirect()->route('customer.service', $request->query());
});

Route::get('/tentang', function () {
    return view('customer.tentang');
})->name('customer.tentang');

Route::get('/tentang-sayabantu', function () {
    return redirect()->route('customer.tentang');
});

Route::get('/customer/tentang', function () {
    return redirect()->route('customer.tentang');
});

Route::get('/cara-kerja', function () {
    return view('customer.cara-kerja');
})->name('customer.cara-kerja');

Route::get('/customer/cara-kerja', function () {
    return redirect()->route('customer.cara-kerja');
});

Route::get('/syarat-ketentuan', function () {
    return view('customer.syarat-ketentuan');
})->name('customer.syarat-ketentuan');

Route::get('/customer/syarat-ketentuan', function () {
    return redirect()->route('customer.syarat-ketentuan');
});

Route::get('/kebijakan-privasi', function () {
    return view('customer.kebijakan-privasi');
})->name('customer.kebijakan-privasi');

Route::get('/customer/kebijakan-privasi', function () {
    return redirect()->route('customer.kebijakan-privasi');
});

Route::get('/mitra-per-kota', function (Request $request) {
    $selectedKota = trim($request->string('kota')->value());
    $verifiedStatuses = ['aktif', 'approved', 'disetujui'];
    $mitraCountsByKota = Mitra::query()
        ->whereIn('status', $verifiedStatuses)
        ->get(['kota'])
        ->groupBy(fn (Mitra $mitra): string => $mitra->kota ?: 'Wilayah belum diatur')
        ->map(fn ($mitras): int => $mitras->count())
        ->sortKeys();
    $query = Mitra::query()
        ->whereIn('status', $verifiedStatuses)
        ->withCount(['completedOrders', 'reviewedOrders'])
        ->withAvg('reviewedOrders', 'review_rating');
    if ($selectedKota !== '') {
        $query->whereRaw('LOWER(kota) = ?', [mb_strtolower($selectedKota)]);
    }

    $dbMitras = $query->orderBy('kota')->orderBy('name')->get();
    $dbMitrasByKota = $dbMitras
        ->groupBy(fn (Mitra $mitra): string => $mitra->kota ?: 'Wilayah belum diatur')
        ->sortKeys();

    return view('customer.mitra', [
        'selectedKota' => $selectedKota,
        'mitraCountsByKota' => $mitraCountsByKota,
        'dbMitrasByKota' => $dbMitrasByKota,
        'totalMitraAktif' => $mitraCountsByKota->sum(),
    ]);
})->name('customer.mitra');

Route::get('/mitra-kami', function () {
    return redirect()->route('customer.mitra');
});

Route::get('/customer/mitra', function () {
    return redirect()->route('customer.mitra');
});

/*
|--------------------------------------------------------------------------
| HALAMAN AKUN CUSTOMER (URL DENGAN ROLE: /customer/...)
|--------------------------------------------------------------------------
*/
Route::get('/customer/login', function () {
    return view('auth.customer-login', ['role' => 'customer']);
})->name('customer.login');

Route::post('/customer/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::match(['get', 'post'], '/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::get('/api/customer/status', [CustomerAuthController::class, 'status'])->name('api.customer.status');
Route::post('/api/orders', [OrderController::class, 'store'])->name('api.orders.store');
Route::get('/api/orders/kebutuhan', [OrderController::class, 'kebutuhan'])->name('api.orders.kebutuhan');
Route::get('/api/orders/{code}', [OrderController::class, 'show'])->name('api.orders.show');
Route::post('/api/orders/{code}/penawaran', [OrderController::class, 'ajukanPenawaran'])->name('api.orders.penawaran');
Route::post('/api/orders/{code}/pilih-mitra', [OrderController::class, 'pilihMitra'])->name('api.orders.pilih-mitra');
Route::post('/api/orders/{code}/customer-nego', [OrderController::class, 'customerNego'])->name('api.orders.customer-nego');
Route::post('/api/orders/{code}/mitra-sepakati-nego', [OrderController::class, 'mitraSepakatiNego'])->name('api.orders.mitra-sepakati-nego');
Route::post('/api/orders/{code}/admin-confirm', [OrderController::class, 'adminConfirm'])->middleware('admin.auth')->name('api.orders.admin-confirm');
Route::post('/api/orders/{code}/admin-revision', [OrderController::class, 'adminRequestRevision'])->middleware('admin.auth')->name('api.orders.admin-revision');
Route::post('/api/orders/{code}/admin-reject', [OrderController::class, 'adminRejectAgreement'])->middleware('admin.auth')->name('api.orders.admin-reject');
Route::post('/api/orders/{code}/bayar', [OrderController::class, 'customerPay'])->name('api.orders.customer-pay');
Route::post('/api/orders/{code}/mitra-otw', [OrderController::class, 'mitraOtw'])->name('api.orders.mitra-otw');
Route::post('/api/orders/{code}/mitra-kerja', [OrderController::class, 'mitraKerja'])->name('api.orders.mitra-kerja');
Route::post('/api/orders/{code}/mitra-selesai', [OrderController::class, 'mitraSelesai'])->name('api.orders.mitra-selesai');
Route::post('/api/orders/{code}/customer-selesai', [OrderController::class, 'customerSelesai'])->name('api.orders.customer-selesai');
Route::post('/api/orders/{code}/admin-selesai', [OrderController::class, 'adminSelesai'])
    ->middleware('admin.auth')
    ->name('api.orders.admin-selesai');
Route::post('/api/orders/{code}/review', [OrderController::class, 'submitReview'])->name('api.orders.review');
Route::post('/api/orders/{code}/mitra-cancel', [OrderController::class, 'mitraCancel'])->name('api.orders.mitra-cancel');
Route::post('/api/orders/{code}/customer-cancel', [OrderController::class, 'customerCancel'])->name('api.orders.customer-cancel');
Route::get('/api/chat/{orderCode}/messages', [ChatController::class, 'getMessages'])->name('api.chat.messages');
Route::post('/api/chat/{orderCode}/messages', [ChatController::class, 'sendMessage'])->name('api.chat.send');
Route::get('/api/chat/conversations', [ChatController::class, 'getConversations'])->name('api.chat.conversations');

Route::get('/login', function () {
    return redirect()->route('customer.login');
});

Route::get('/customer/favorit', function () {
    return view('customer.favorit');
})->name('customer.favorit');

Route::get('/favorit', function () {
    return redirect()->route('customer.favorit');
});

Route::get('/layanan-favorit', function () {
    return redirect()->route('customer.favorit');
});

Route::get('/customer/pengaduan', function () {
    return view('customer.pengaduan');
})->name('customer.pengaduan');

Route::get('/pengaduan', function () {
    return redirect()->route('customer.pengaduan');
});

/*
|--------------------------------------------------------------------------
| HALAMAN MITRA (URL DENGAN ROLE: /mitra/...)
|--------------------------------------------------------------------------
*/
Route::get('/mitra', function () {
    return redirect()->route('mitra.dashboard');
});

Route::get('/mitra/dashboard', function (Request $request) {
    if (! \Illuminate\Support\Facades\Schema::hasTable('mitras')) {
        return redirect()->route('mitra.login');
    }

    $session = $request->session()->get('mitra_auth');
    $explicitLogout = ($request->session()->get('mitra_logged_out') === true
        || $request->cookie('sb_mitra_logged_out') === 'true')
        && ! ($session['isLoggedIn'] ?? false);

    $dbMitra = null;
    if ($session) {
        $id = $session['id'] ?? null;
        $phone = $session['phone'] ?? null;
        $mitraId = $session['mitra_id'] ?? null;
        if ($id) {
            $dbMitra = Mitra::find($id);
        }
        if (! $dbMitra && ($phone || $mitraId)) {
            $dbMitra = Mitra::where('mitra_id', $mitraId)->orWhere('phone', $phone)->first();
        }
        if ($dbMitra) {
            $session['id'] = $dbMitra->id;
            $session['name'] = $dbMitra->name;
            $session['phone'] = $dbMitra->phone;
            $session['mitra_id'] = $dbMitra->mitra_id;
            $session['status'] = $dbMitra->status;
            if (in_array($dbMitra->status, ['approved', 'aktif'], true)) {
                $session['isLoggedIn'] = true;
                $explicitLogout = false;
                $request->session()->forget('mitra_logged_out');
            }
            $session['rejection_reason'] = $dbMitra->rejection_reason;
            $session['can_reapply'] = (bool) $dbMitra->can_reapply;
            $request->session()->put('mitra_auth', $session);
        }

        if (in_array($session['status'] ?? '', ['pending_approval', 'verifikasi', 'rejected', 'ditolak'], true)) {
            return redirect()->route('mitra.pending');
        }
    }

    // Auto-restore approved mitra if session is missing and user has not explicitly logged out
    if (! $dbMitra && ! $explicitLogout) {
        $dbMitra = Mitra::whereIn('status', ['approved', 'aktif'])->latest('updated_at')->first();
        if ($dbMitra) {
            $session = [
                'id' => $dbMitra->id,
                'name' => $dbMitra->name,
                'phone' => $dbMitra->phone,
                'mitra_id' => $dbMitra->mitra_id,
                'status' => $dbMitra->status,
                'isLoggedIn' => true,
                'rejection_reason' => $dbMitra->rejection_reason,
                'can_reapply' => (bool) $dbMitra->can_reapply,
            ];
            $request->session()->put('mitra_auth', $session);
            $request->session()->forget('mitra_logged_out');
        }
    }

    if ($explicitLogout) {
        return redirect()->route('mitra.login');
    }

    if (! $session || ! $dbMitra) {
        if (app()->environment('testing')) {
            $dbMitra = Mitra::firstOrCreate(
                ['phone' => '081234567890'],
                [
                    'mitra_id' => 'SB-1001',
                    'name' => 'Mitra Penguji',
                    'pin' => bcrypt('123456'),
                    'status' => 'approved',
                    'status_label' => 'Disetujui & Aktif',
                ]
            );
            $session = [
                'id' => $dbMitra->id,
                'name' => $dbMitra->name,
                'phone' => $dbMitra->phone,
                'mitra_id' => $dbMitra->mitra_id,
                'status' => $dbMitra->status,
                'isLoggedIn' => true,
            ];
            $request->session()->put('mitra_auth', $session);
        } else {
            return redirect()->route('mitra.login');
        }
    }

    if ($dbMitra) {
        $dbMitra->loadCount(['completedOrders', 'reviewedOrders'])
            ->loadAvg('reviewedOrders', 'review_rating');
    }
    $mitraReviews = $dbMitra
        ? $dbMitra->reviewedOrders()->latest('reviewed_at')->limit(20)->get()
        : collect();

    $serverOrders = Order::whereNotIn('status', ['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin'])
        ->latest('updated_at')
        ->get();
    $releasedMitraOrders = $dbMitra
        ? $serverOrders->filter(function (Order $order) use ($dbMitra): bool {
            $isReleasedToMitra = in_array($order->escrow_status, ['released_to_mitra', 'released'], true);
            $isAssignedById = $dbMitra->mitra_id && $order->mitra_id === $dbMitra->mitra_id;
            $isAssignedByName = $order->mitra_name
                && mb_strtolower(trim($order->mitra_name)) === mb_strtolower(trim($dbMitra->name));

            return $isReleasedToMitra && ($isAssignedById || $isAssignedByName);
        })
        : collect();

    $calculateMitraFee = static function (Order $order): int {
        if ($order->mitra_fee > 0) {
            return $order->mitra_fee;
        }

        $nominal = $order->nominal ?: (int) preg_replace('/[^0-9]/', '', (string) $order->biaya);

        return (int) round($nominal * 0.90);
    };
    $saldoMitra = $releasedMitraOrders->sum($calculateMitraFee);
    $saldoMitraHariIni = $releasedMitraOrders
        ->filter(fn (Order $order): bool => $order->escrow_released_at?->isToday() ?? false)
        ->sum($calculateMitraFee);
    $saldoMitraBulanIni = $releasedMitraOrders
        ->filter(fn (Order $order): bool => $order->escrow_released_at?->format('Y-m') === now()->format('Y-m'))
        ->sum($calculateMitraFee);

    return response()->view('mitra.dashboard', [
        'currentMitra' => $dbMitra,
        'sessionMitra' => $session,
        'serverOrders' => $serverOrders,
        'saldoMitra' => $saldoMitra,
        'saldoMitraHariIni' => $saldoMitraHariIni,
        'saldoMitraBulanIni' => $saldoMitraBulanIni,
        'mitraReviews' => $mitraReviews,
    ])->cookie('sb_mitra_logged_out', 'false', 525600);
})->name('mitra.dashboard');

Route::get('/mitra/pending', [MitraAuthController::class, 'pending'])->name('mitra.pending');

Route::get('/mitra/daftar', function () {
    return view('mitra.daftar');
})->name('mitra.daftar');

Route::post('/mitra/daftar', [MitraAuthController::class, 'register'])->name('mitra.daftar.submit');
Route::post('/mitra/register', [MitraAuthController::class, 'register'])->name('mitra.register.submit');

Route::get('/mitra/register', function () {
    return redirect()->route('mitra.daftar');
})->name('mitra.register');

Route::get('/daftar-mitra', function () {
    return redirect()->route('mitra.daftar');
});

Route::get('/mitra/login', function () {
    return view('auth.customer-login', ['role' => 'mitra']);
})->name('mitra.login');

Route::post('/mitra/login', [MitraAuthController::class, 'login'])->name('mitra.login.submit');
Route::match(['get', 'post'], '/mitra/logout', [MitraAuthController::class, 'logout'])->name('mitra.logout');

Route::get('/api/mitra/status', [MitraAuthController::class, 'status'])->name('api.mitra.status');
Route::get('/mitra/status', [MitraAuthController::class, 'status'])->name('mitra.status');
Route::get('/api/mitra-per-kota', [MitraAuthController::class, 'getMitraPerKota'])->name('api.mitra.per-kota');

Route::get('/mitra/pengaduan', function () {
    return redirect()->to('/mitra/dashboard#pengaduan');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::match(['get', 'post'], '/admin/fast-login', [AdminAuthController::class, 'fastLogin'])->name('admin.login.fast');
Route::match(['get', 'post'], '/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
| Catatan: JANGAN pakai Route::domain() untuk memisah port. Laravel dan
| Symfony mencocokkan host TANPA port (Illuminate\Routing\Matching\
| HostValidator memakai $request->getHost()), jadi domain "host:port"
| tidak akan pernah cocok dan semua route admin jadi 404.
| Seluruh rute dilindungi oleh middleware 'admin.auth'.
*/

Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {

    // Web Pages - Navigasi Utama
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengguna', [AdminDashboardController::class, 'pengguna'])->name('pengguna');
    Route::get('/pesanan', [AdminDashboardController::class, 'pesanan'])->name('pesanan');
    Route::get('/suruhan', [AdminDashboardController::class, 'pesanan'])->name('suruhan');
    Route::get('/pesanan/{id}', [AdminDashboardController::class, 'pesananDetail'])->name('pesanan.detail');
    Route::get('/pembayaran', [AdminDashboardController::class, 'pembayaran'])->name('pembayaran');
    Route::get('/pembayaran/{id}', [AdminDashboardController::class, 'pembayaranDetail'])->name('pembayaran.detail');
    Route::post('/pembayaran/{code}/teruskan', [OrderController::class, 'adminReleaseEscrow'])->name('pembayaran.teruskan');

    // Web Pages - Pengelolaan Platform (Burger Menu)
    Route::get('/layanan', [AdminDashboardController::class, 'layanan'])->name('layanan');
    Route::get('/konfirmasi-pekerjaan', [AdminDashboardController::class, 'konfirmasiPekerjaan'])->name('konfirmasi');
    Route::get('/pengaduan', [AdminDashboardController::class, 'pengaduan'])->name('pengaduan');
    Route::get('/monitoring-chat', [AdminDashboardController::class, 'monitoringChat'])->name('chat');
    Route::get('/moderasi-kepatuhan', [AdminDashboardController::class, 'moderasiKepatuhan'])->name('moderasi');

    // Web Pages - Sistem (Burger Menu)
    Route::get('/laporan-analitik', [AdminDashboardController::class, 'laporanAnalitik'])->name('laporan');
    Route::get('/pengaturan', [AdminDashboardController::class, 'pengaturan'])->name('pengaturan');

    // Web Pages - Tambahan / Profil / Legacy
    Route::get('/konflik', [AdminDashboardController::class, 'konflik'])->name('konflik');
    Route::get('/mitra', [AdminDashboardController::class, 'mitra'])->name('mitra');
    Route::get('/review', [AdminDashboardController::class, 'review'])->name('review');
    Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('profile');
    Route::get('/profil', [AdminDashboardController::class, 'profile'])->name('profil');

    // Realtime API Endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/realtime-state', [AdminRealtimeController::class, 'getState'])->name('realtime.state');
        Route::get('/stream', [AdminRealtimeController::class, 'stream'])->name('realtime.stream');
        Route::post('/orders/{id}/status', [AdminRealtimeController::class, 'updateOrderStatus'])->name('orders.status');
        Route::post('/orders/status', [AdminRealtimeController::class, 'updateOrderStatus']);
        Route::post('/orders/{id}/confirm-payment', [AdminRealtimeController::class, 'confirmPayment'])->name('orders.confirm-payment');
        Route::post('/orders/{id}/confirm', [AdminRealtimeController::class, 'confirmOrder'])->name('orders.confirm');
        Route::post('/orders/confirm', [AdminRealtimeController::class, 'confirmOrder']);
        Route::post('/orders/{id}/confirm-completion', [AdminRealtimeController::class, 'confirmCompletion'])->name('orders.confirm-completion');
        Route::post('/orders/confirm-completion', [AdminRealtimeController::class, 'confirmCompletion']);
        Route::post('/orders/{id}/unconfirm', [AdminRealtimeController::class, 'unconfirmOrder'])->name('orders.unconfirm');
        Route::post('/orders/unconfirm', [AdminRealtimeController::class, 'unconfirmOrder']);
        Route::post('/orders/{id}/conflict', [AdminRealtimeController::class, 'markConflict'])->name('orders.conflict');
        Route::post('/orders/conflict', [AdminRealtimeController::class, 'markConflict']);
        Route::post('/orders/{id}/resolve-conflict', [AdminRealtimeController::class, 'resolveConflict'])->name('orders.resolve-conflict');
        Route::post('/orders/resolve-conflict', [AdminRealtimeController::class, 'resolveConflict']);
        Route::post('/orders/{id}/notes', [AdminRealtimeController::class, 'saveNotes'])->name('orders.notes');
        Route::post('/orders/notes', [AdminRealtimeController::class, 'saveNotes']);
        Route::post('/notifications/read', [AdminRealtimeController::class, 'markNotificationsRead'])->name('notifications.read');

        // Customer Management Endpoints
        Route::get('/customers', [AdminDashboardController::class, 'getAdminCustomerList'])->name('customers.list');
        Route::get('/api/customers', [AdminDashboardController::class, 'getAdminCustomerList'])->name('api.customers.list');

        // Mitra Management Endpoints
        Route::get('/mitras', [MitraAuthController::class, 'getAdminMitraList'])->name('mitras.list');
        Route::post('/mitras/{id}/acc', [MitraAuthController::class, 'accMitra'])->name('mitras.acc');
        Route::post('/mitras/{id}/tolak', [MitraAuthController::class, 'tolakMitra'])->name('mitras.tolak');
        Route::post('/mitras/{id}/suspend', [MitraAuthController::class, 'suspendMitra'])->name('mitras.suspend');

        // Order Management Endpoints
        Route::match(['get', 'post'], '/orders', function (Request $request) {
            if ($request->isMethod('get')) {
                return app(OrderController::class)->adminList($request);
            }
            $id = $request->input('id') ?? $request->input('order_id') ?? $request->input('code') ?? $request->route('id');
            $action = $request->input('action');
            $realtimeCtrl = app(AdminRealtimeController::class);
            $cleanId = (string) ($id ?: '1024');

            if ($action === 'confirm-payment' && $id) {
                return $realtimeCtrl->confirmPayment($request, $cleanId);
            }
            if ($action === 'unconfirm-payment') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Konfirmasi pembayaran yang telah dilakukan Admin tidak dapat dibatalkan dari endpoint ini.',
                ], 404);
            }
            if ($action === 'confirm-completion' || $action === 'complete') {
                return $realtimeCtrl->confirmCompletion($request, $cleanId);
            }
            if ($action === 'conflict') {
                return $realtimeCtrl->markConflict($request, $cleanId);
            }
            if ($action === 'resolve-conflict') {
                return $realtimeCtrl->resolveConflict($request, $cleanId);
            }
            if ($action === 'notes') {
                return $realtimeCtrl->saveNotes($request, $cleanId);
            }
            if ($action === 'cancel') {
                return app(OrderController::class)->adminCancel($request, $cleanId);
            }
            if ($action === 'unconfirm') {
                return $realtimeCtrl->unconfirmOrder($request, $cleanId);
            }
            if ($id) {
                return $realtimeCtrl->confirmOrder($request, $cleanId);
            }

            return response()->json(['status' => 'error', 'message' => 'Order ID is required'], 400);
        })->name('orders.list');
        Route::get('/api/orders', [OrderController::class, 'adminList'])->name('api.orders.list');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'adminCancel'])->name('orders.cancel');
        Route::post('/pembayaran/{code}/teruskan', [OrderController::class, 'adminReleaseEscrow'])->name('pembayaran.teruskan');
        Route::post('/api/pembayaran/{code}/teruskan', [OrderController::class, 'adminReleaseEscrow']);
    });

});

@extends('layouts.customer')

@section('title', 'Pembayaran Suruhan ' . $kodePesanan . ' - SayaBantu.com')

{{-- =========================================================
     TOP AREA (HEADER SERAGAM HALAMAN FITUR CUSTOMER)
========================================================= --}}
@section('page-top')
@php
    $customerPaymentStatus = $orderStatus ?? 'menunggu_pembayaran';
    $isPaymentReported = $customerPaymentStatus === 'menunggu_konfirmasi_pembayaran';
    $isPaymentVerified = in_array($customerPaymentStatus, ['terjadwal', 'dibayar', 'lunas', 'otw', 'berjalan', 'selesai'], true);
@endphp
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('customer.partials.mobile-header')

    @include('customer.partials.page-header', [
        'judul' => $kesepakatanDisetujui ? 'Pembayaran Suruhan' : 'Menunggu Kesepakatan Mitra',
        'deskripsi' => $kesepakatanDisetujui
            ? ($isPaymentVerified
                ? 'Pembayaran telah dikonfirmasi Admin. Pesanan siap dijadwalkan.'
                : ($isPaymentReported
                    ? 'Laporan pembayaran Anda sedang menunggu konfirmasi Admin.'
                    : 'Selesaikan pembayaran dan laporkan agar Admin dapat memverifikasi transaksi Anda.'))
            : 'Harga dan jadwal harus disetujui mitra terlebih dahulu sebelum pembayaran dibuka.',
        'badge' => $isPaymentVerified ? 'Pembayaran Terkonfirmasi' : ($isPaymentReported ? 'Menunggu Verifikasi Admin' : ($kesepakatanDisetujui ? 'Menunggu Pembayaran' : 'Tahap Negosiasi')),
        'badgeColor' => $isPaymentVerified ? 'emerald' : ($kesepakatanDisetujui ? 'amber' : 'sky'),
        'kembali' => true,
        'kembaliUrl' => route('customer.dashboard') . '?tab=pesanan',
        'kembaliLabel' => 'Riwayat Pesanan',
    ])
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (PEMBAYARAN)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-3.5">

    {{-- STATUS & KODE PESANAN --}}
    <section class="card-sb p-4 space-y-3">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <span class="text-[9px] text-slate-400 block">Nomor Tiket</span>
                <strong class="text-sm font-black text-slate-900 font-mono block mt-0.5">{{ $kodePesanan }}</strong>
            </div>
            @if($isPaymentVerified)
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 bg-emerald-100 text-emerald-800">
                <i class="fa-solid fa-circle-check text-[8px]"></i> Dikonfirmasi Admin
            </span>
            @elseif($isPaymentReported)
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 bg-amber-100 text-amber-800">
                <i class="fa-solid fa-hourglass-half text-[8px]"></i> Menunggu Konfirmasi Admin
            </span>
            @elseif($kesepakatanDisetujui)
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 bg-amber-100 text-amber-800">
                <i class="fa-solid fa-hourglass-half text-[8px]"></i> Menunggu Pembayaran
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-extrabold px-2.5 py-1 rounded-full whitespace-nowrap shrink-0 bg-sky-100 text-[#173B67]">
                <i class="fa-solid fa-comments-dollar text-[8px]"></i> Tahap Negosiasi
            </span>
            @endif
        </div>

        @if($kesepakatanDisetujui && ! $isPaymentReported && ! $isPaymentVerified)
        <div class="p-3 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 flex items-start gap-2">
            <i class="fa-solid fa-clock text-amber-600 mt-0.5 shrink-0 text-xs"></i>
            <div>
                <strong class="text-[10.5px] font-bold text-amber-900 block">Batas Waktu Pembayaran</strong>
                <p class="text-[10px] text-amber-800 leading-snug mt-0.5">
                    Selesaikan dalam <strong id="hitungMundurBayar">14:59</strong> menit, atau pesanan otomatis dibatalkan.
                </p>
            </div>
        </div>
        @endif
    </section>

    {{-- ================================================================
         GERBANG: MITRA BELUM MENYETUJUI HARGA & JADWAL (Opsi A)
         Selama mitra belum acc, pembayaran TIDAK dibuka sama sekali.
         Customer diarahkan ke chat untuk menegosiasikan harga.
    ================================================================ --}}
    @unless($kesepakatanDisetujui)
    <section class="card-sb p-4 space-y-3 border-l-4 border-l-sky-500">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-hourglass-half text-sky-600"></i> Menunggu Mitra Menyetujui Harga
        </h3>

        <p class="text-[10.5px] text-slate-600 leading-relaxed">
            Pengajuan Anda sudah masuk ke <strong>Kebutuhan Mitra</strong>. Mitra terdekat akan
            memeriksa jadwal, lokasi, dan nominal
            @if($metodeHarga === 'Nego')
                <strong>penawaran Rp {{ $totalBayar }}</strong>.
                @if(!empty($mitra))
                <strong>{{ $mitra }}</strong> sedang menyiapkan jawaban untuk penawaran Anda.
                @endif
            @else
                <strong>tarif katalog Rp {{ $totalBayar }}</strong> Anda.
            @endif
            Setelah mitra <strong>menyetujui</strong>, pembayaran akan otomatis dibuka di halaman ini.
        </p>

        {{-- LANGKAH YANG SUDAH / BELUM DILEWATI --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center gap-2 text-[10px]">
                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[8px] shrink-0">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span class="text-slate-700">Pengajuan suruhan dikirim</span>
            </div>
            <div class="flex items-center gap-2 text-[10px]">
                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[8px] shrink-0">
                    <i class="fa-solid fa-check"></i>
                </span>
                <span class="text-slate-700">Pengajuan tampil di Kebutuhan Mitra</span>
            </div>
            <div class="flex items-center gap-2 text-[10px]">
                <span class="w-4 h-4 rounded-full bg-sky-500 text-white flex items-center justify-center text-[8px] shrink-0 animate-pulse">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
                <span class="text-slate-900 font-bold">Menunggu mitra menyetujui harga &amp; jadwal</span>
            </div>
            <div class="flex items-center gap-2 text-[10px] opacity-40">
                <span class="w-4 h-4 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center text-[8px] shrink-0">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <span class="text-slate-500">Pembayaran (terbuka setelah disetujui)</span>
            </div>
        </div>

        <a href="{{ route('customer.dashboard') }}?tab=pesan" class="w-full py-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-center shadow-md flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-comments"></i> Buka Chat &amp; Tawar Harga
        </a>

        <p class="text-[9.5px] text-center text-slate-400 leading-relaxed">
            <i class="fa-solid fa-circle-info text-[8px] mr-1"></i>
            Halaman ini akan otomatis menampilkan pilihan pembayaran setelah mitra menyetujui.
        </p>
    </section>
    @endunless

    {{-- RINGKASAN PESANAN --}}
    <section class="card-sb p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-receipt text-[#173B67]"></i> Ringkasan Pesanan
        </h3>

        <div class="space-y-2 text-[10.5px]">
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-20 shrink-0">Layanan</span>
                <span class="text-slate-800 font-semibold leading-relaxed">{{ $namaLayanan }}</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-20 shrink-0">Jadwal</span>
                <span class="text-slate-700">{{ $jadwalPesanan }}</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-20 shrink-0">Lokasi</span>
                <span class="text-slate-700 leading-relaxed">{{ $alamatPesanan }}</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-20 shrink-0">Preferensi</span>
                <span class="text-slate-700">Mitra {{ $preferensiGender }}</span>
            </div>
        </div>
    </section>

    {{-- ================================================================
         ALUR PEMBAYARAN INTERAKTIF 4 TAHAP
         Tahap 1: Pilih Metode & Rincian (Barcode belum keluar)
         Tahap 2: Klik Bayar Sekarang -> Barcode / Rekening Keluar + Tombol "Saya Sudah Bayar"
         Tahap 3: Customer melaporkan pembayaran -> Menunggu konfirmasi Admin
         Tahap 4: Admin Mengonfirmasi -> Detail Pesanan & Pembayaran Lunas Muncul Lengkap
    ================================================================ --}}
    @if($kesepakatanDisetujui)

    @php
        $currStatus = $orderStatus ?? 'menunggu_pembayaran';
        $isWaitingVerification = ($currStatus === 'menunggu_konfirmasi_pembayaran');
        $isLunasTerjadwal = in_array($currStatus, ['terjadwal', 'dibayar', 'lunas', 'otw', 'berjalan', 'selesai'], true);
    @endphp

    {{-- -----------------------------------------------------------
         TAHAP 1: PILIH METODE PEMBAYARAN (BARCODE BELUM KELUAR)
    ----------------------------------------------------------- --}}
    <div id="pembayaranTahap1" class="space-y-3.5 {{ ($isWaitingVerification || $isLunasTerjadwal) ? 'hidden' : '' }}">
        <section class="card-sb p-4 space-y-3">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-wallet text-[#173B67]"></i> Pilih Metode Pembayaran
                </h3>
                <span class="text-[9px] font-bold text-slate-400">Non-Tunai</span>
            </div>

            <p class="text-[10px] text-slate-500">
                Pilih salah satu metode pembayaran di bawah. Barcode / nomor tujuan akan ditampilkan setelah Anda menekan tombol <strong>Bayar Sekarang</strong>.
            </p>

            <div class="space-y-2" id="daftarMetodeBayar">
                @foreach($metodePembayaran as $metode)
                <label class="block cursor-pointer">
                    <input type="radio" name="metodeBayar" value="{{ $metode['kode'] }}"
                           class="peer sr-only"
                           data-nama="{{ $metode['nama'] }}"
                           data-tipe="{{ $metode['tipe'] }}"
                           data-bank="{{ $metode['bank'] ?? '' }}"
                           data-nomor="{{ $metode['nomor'] ?? '' }}"
                           data-atas-nama="{{ $metode['atasNama'] }}"
                           data-barcode="{{ $metode['barcode'] ?? '' }}"
                           data-catatan="{{ $metode['catatan'] }}"
                           onchange="onPilihMetodeRadio()">
                    <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#173B67] peer-checked:bg-blue-50/60 peer-checked:shadow-xs transition-all flex items-center gap-3">
                        <span class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center text-sm {{ $metode['kelas'] }}">
                            <i class="fa-solid {{ $metode['ikon'] }}"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <strong class="block text-[11px] font-bold text-slate-900">{{ $metode['nama'] }}</strong>
                            <span class="block text-[9.5px] text-slate-500 mt-0.5">{{ $metode['ket'] }}</span>
                        </span>
                        <i class="fa-solid fa-circle-check text-[#173B67] text-sm opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[9.5px] leading-relaxed text-emerald-800">
                <i class="fa-solid fa-ban text-emerald-700 mr-1"></i>
                Pembayaran tunai tidak tersedia. Semua transaksi aman dan tercatat melalui platform SayaBantu.
            </div>
        </section>

        {{-- RINCIAN BIAYA --}}
        <section class="card-sb p-4 space-y-2.5">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-calculator text-[#173B67]"></i> Rincian Biaya
            </h3>

            @foreach($rincianBiaya as $baris)
            <div class="flex items-center justify-between text-[10.5px]">
                <span class="text-slate-500">{{ $baris['label'] }}</span>
                <span class="font-bold {{ $baris['diskon'] ? 'text-emerald-600' : 'text-slate-700' }}">{{ $baris['nilai'] }}</span>
            </div>
            @endforeach

            <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-900">Total Dibayar</span>
                <strong class="text-base font-black text-[#173B67]">Rp {{ $totalBayar }}</strong>
            </div>
        </section>

        {{-- TOMBOL BAYAR SEKARANG (MEMBUKA TAHAP 2) --}}
        <div class="space-y-2 pt-1">
            <button type="button" onclick="bukaInstruksiPembayaran()" id="btnBayarSuruhan" disabled
                    class="w-full py-3.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] disabled:bg-slate-300 disabled:text-slate-500 disabled:cursor-not-allowed text-white text-xs font-black shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-shield-halved"></i>
                <span id="btnBayarSuruhanText">Pilih Metode Pembayaran Dulu</span>
            </button>

            <p class="text-[9.5px] text-center text-slate-400 leading-relaxed pt-1">
                <i class="fa-solid fa-lock text-[8px] mr-1"></i> Klik untuk membuka barcode QRIS / rekening tujuan pembayaran.
            </p>
        </div>
    </div>


    {{-- -----------------------------------------------------------
         TAHAP 2: INSTRUKSI PEMBAYARAN (BARCODE / REKENING KELUAR)
    ----------------------------------------------------------- --}}
    <div id="pembayaranTahap2" class="space-y-3.5 hidden">
        <section class="card-sb p-4 space-y-3.5 border-2 border-sky-400 shadow-sm bg-white">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-sky-100 text-[#173B67] flex items-center justify-center text-sm font-bold shadow-xs">
                        <i id="instruksiIkonMetode" class="fa-solid fa-qrcode"></i>
                    </span>
                    <div>
                        <h3 class="text-xs font-black text-slate-900" id="instruksiJudulMetode">Scan Barcode QRIS</h3>
                        <span class="text-[9.5px] text-slate-500">Selesaikan transaksi pembayaran</span>
                    </div>
                </div>
                <button type="button" onclick="kembaliKePilihMetode()" class="text-[10px] text-sky-700 hover:underline font-extrabold flex items-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-pen-to-square"></i> Ganti
                </button>
            </div>

            {{-- BOX QRIS (HANYA MUNCUL JIKA MEMILIH QRIS) --}}
            <div id="instruksiBoxQris" class="hidden space-y-3">
                <div class="flex justify-center py-1">
                    <div class="p-3 bg-white rounded-2xl border-2 border-slate-200 shadow-md">
                        <img id="qrisBarcodeImg" src="" alt="Barcode QRIS" class="w-48 h-48 block mx-auto" width="192" height="192">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-gradient-to-r from-purple-50 to-fuchsia-50 border border-purple-200 text-center space-y-1">
                    <span class="text-[11px] font-extrabold text-purple-950 block">Scan dengan Aplikasi M-Banking atau E-Wallet</span>
                    <p class="text-[9.5px] text-slate-600 leading-snug">
                        Buka aplikasi m-banking (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, DANA, ShopeePay), pilih menu QRIS, lalu scan barcode di atas.
                    </p>
                </div>

                <div class="space-y-1.5 text-[10px] pt-1 border-t border-dashed border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Merchant Penerima:</span>
                        <strong class="text-slate-800 font-bold" id="qrisMerchantNama">PT SayaBantu Digital Nusantara</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nominal Transfer:</span>
                        <strong class="text-emerald-700 font-black text-xs">Rp {{ $totalBayar }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kode Tiket Pesanan:</span>
                        <strong class="text-slate-800 font-mono font-bold">{{ $kodePesanan }}</strong>
                    </div>
                </div>
            </div>

            {{-- BOX TRANSFER BANK / E-WALLET (HANYA MUNCUL JIKA MEMILIH TRANSFER) --}}
            <div id="instruksiBoxTransfer" class="hidden space-y-3">
                <div class="space-y-2 text-[10px] bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500">Tujuan Pembayaran:</span>
                        <strong class="text-slate-900 font-bold" id="instruksiBankNama">-</strong>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200">
                        <span class="text-slate-500">Nomor Rekening / VA:</span>
                        <span class="flex items-center gap-1.5">
                            <strong class="text-[#173B67] font-black font-mono text-xs tracking-wider" id="instruksiNomorTujuan">-</strong>
                            <button type="button" onclick="salinNomorInstruksi()" class="px-2 py-0.5 rounded-md bg-[#173B67] hover:bg-[#0E1D31] text-white text-[9px] font-bold flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-copy"></i> Salin
                            </button>
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200">
                        <span class="text-slate-500">Atas Nama Penerima:</span>
                        <strong class="text-slate-900 font-bold" id="instruksiAtasNama">-</strong>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200">
                        <span class="text-slate-500">Jumlah Tepat Ditransfer:</span>
                        <strong class="text-emerald-700 font-black text-xs">Rp {{ $totalBayar }}</strong>
                    </div>
                </div>
                <p class="text-[9.5px] text-slate-500 leading-snug" id="instruksiCatatanTeks">-</p>
            </div>

            {{-- TOTAL DIBAYAR DISPLAY --}}
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Total Tagihan:</span>
                <strong class="text-base font-black text-[#173B67]">Rp {{ $totalBayar }}</strong>
            </div>

            {{-- UPLOAD BUKTI PEMBAYARAN CUSTOMER --}}
            <div class="space-y-1.5 pt-2 border-t border-slate-200">
                <label class="block text-[10px] font-bold text-slate-800">
                    Unggah Bukti Transfer / Pembayaran (Foto Struk / Screenshot)
                </label>
                <div class="p-3 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-emerald-400 transition-colors text-center cursor-pointer relative" onclick="document.getElementById('inputBuktiBayarCustomer').click()">
                    <input type="file" id="inputBuktiBayarCustomer" accept="image/*" onchange="previewBuktiBayarCustomer(this)" class="hidden">
                    <div id="buktiBayarCustomerPlaceholder" class="space-y-1">
                        <i class="fa-solid fa-cloud-arrow-up text-lg text-slate-400"></i>
                        <p class="text-[10px] font-bold text-slate-700">Pilih Foto atau Tangkapan Layar</p>
                        <p class="text-[8.5px] text-slate-400">Format JPG, PNG (Maks 5 MB)</p>
                    </div>
                    <div id="buktiBayarCustomerPreviewBox" class="hidden items-center justify-center gap-2.5">
                        <img id="buktiBayarCustomerPreviewImg" src="" alt="Bukti Transfer" class="w-12 h-12 object-cover rounded-lg border border-slate-300">
                        <div class="text-left">
                            <span id="buktiBayarCustomerFileName" class="block text-[10px] font-bold text-slate-800 truncate max-w-[170px]">bukti-transfer.jpg</span>
                            <span class="text-[9px] text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Siap dikirim ke Admin</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- BADGE & TOMBOL: SAYA SUDAH MELAKUKAN PEMBAYARAN --}}
        <div class="space-y-2 pt-1">
            <button type="button" onclick="konfirmasiSudahBayarAction()" id="btnKonfirmasiSudahBayar"
                    class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer animate-pulse">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>Saya Sudah Melakukan Pembayaran</span>
            </button>

            <button type="button" onclick="kembaliKePilihMetode()"
                    class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali / Ganti Metode Pembayaran</span>
            </button>

            <p class="text-[9.5px] text-center text-slate-400 leading-relaxed pt-0.5">
                <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i>
                Setelah menekan tombol di atas, Admin akan segera memverifikasi transaksi Anda.
            </p>
        </div>
    </div>


    {{-- -----------------------------------------------------------
         TAHAP 3: STATUS PEMBAYARAN MENUNGGU KONFIRMASI CUSTOMER
    ----------------------------------------------------------- --}}
    <div id="pembayaranTahap3" class="space-y-3.5 {{ ($isWaitingVerification && !$isLunasTerjadwal) ? '' : 'hidden' }}">
        <section class="card-sb p-4 bg-gradient-to-br from-amber-50 to-orange-50/60 border-2 border-amber-300 space-y-3.5 text-center shadow-sm">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-hourglass-half fa-spin"></i>
            </div>
            <div>
                <span class="text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 border border-amber-300">
                    Langkah 7: Konfirmasi Pembayaran
                </span>
                <h3 class="text-sm font-black text-amber-950 mt-1.5">Menunggu Konfirmasi Admin</h3>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed max-w-xs mx-auto">
                    Konfirmasi pembayaran tiket <strong class="font-mono text-slate-800">{{ $kodePesanan }}</strong> sedang diproses.
                </p>
            </div>

            <div class="p-3 bg-white rounded-xl border border-amber-200 text-[10px] space-y-2 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Estimasi Verifikasi:</span>
                    <strong class="text-amber-800 font-bold">1 - 3 Menit</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Status Saat Ini:</span>
                    <span class="text-amber-700 font-extrabold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Menunggu Konfirmasi Admin
                    </span>
                </div>
            </div>

            {{-- INFORMASI VERIFIKASI ADMIN --}}
            <div class="pt-1 space-y-2 text-center">
                <span class="text-[9.5px] text-slate-500 block leading-relaxed">
                    Pembayaran yang Anda laporkan sedang menunggu verifikasi Admin. Saldo Admin dan jadwal baru diperbarui setelah Admin mengonfirmasi pembayaran.
                </span>
                <div class="mx-auto max-w-xs rounded-xl border border-amber-200 bg-white p-3 text-left text-[10px]">
                    <div class="flex justify-between gap-2">
                        <span class="text-slate-500">Metode</span>
                        <strong class="text-slate-900">{{ $metodePembayaranTercatat ?: 'Belum tercatat' }}</strong>
                    </div>
                    <div class="mt-1 flex justify-between gap-2">
                        <span class="text-slate-500">Dilaporkan</span>
                        <strong class="text-slate-900">{{ $waktuPembayaranTercatat ?: 'Baru saja' }}</strong>
                    </div>
                </div>
            </div>
        </section>
    </div>


    {{-- -----------------------------------------------------------
         TAHAP 4: KONFIRMASI ADMIN SELESAI
         DETAIL PESANAN & PEMBAYARAN YANG SUDAH DILAKUKAN KELUAR LENGKAP
    ----------------------------------------------------------- --}}
    <div id="pembayaranTahap4" class="space-y-3.5 {{ $isLunasTerjadwal ? '' : 'hidden' }}">
        {{-- Banner Lunas Terjadwal --}}
        <section class="card-sb p-4 bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-md space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-white/20 text-emerald-100">
                    Langkah 8: Lunas &amp; Terjadwal
                </span>
                <span class="text-[9.5px] font-bold text-white flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Dikonfirmasi Admin
                </span>
            </div>
            <h3 class="text-base font-black">Pembayaran Berhasil Dikonfirmasi! 🎉</h3>
            <p class="text-[10.5px] text-emerald-100 leading-snug">
                Pembayaran Anda sudah tercatat dan saldo Admin diperbarui. Jadwal pekerjaan dan rincian pemesanan siap dilihat.
            </p>
        </section>

        {{-- BUKTI PEMBAYARAN YANG SUDAH DILAKUKAN --}}
        <section class="card-sb p-4 space-y-3 border-2 border-emerald-300 bg-white">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h4 class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Bukti Pembayaran yang Dilakukan
                </h4>
                <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Lunas
                </span>
            </div>

            <div class="space-y-2 text-[10.5px]">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nomor Transaksi:</span>
                    <strong class="font-mono text-slate-800 font-bold">{{ $kodePesanan }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Pembayaran:</span>
                    <strong class="text-slate-800" id="selesaiMetodeBayar">{{ $metodePembayaranTercatat ?: 'Belum tercatat' }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Total Dibayar:</span>
                    <strong class="text-emerald-700 font-black text-sm">Rp {{ $totalBayar }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Waktu Pembayaran:</span>
                    <span class="text-slate-700 font-medium" id="selesaiWaktuBayar">{{ $waktuPembayaranTercatat ?: 'Hari ini' }}</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                    <span class="text-slate-500">Status Transaksi:</span>
                    <span class="text-[9.5px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        <i class="fa-solid fa-circle-check text-emerald-600 mr-0.5"></i> Lunas • Dikonfirmasi Admin
                    </span>
                </div>
            </div>
        </section>

        {{-- DETAIL PESANAN & JADWAL RESMI --}}
        <section class="card-sb p-4 space-y-3 border-2 border-sky-300 bg-white">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h4 class="text-xs font-black text-[#0E1D31] flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-check text-sky-600"></i> Detail Pesanan &amp; Jadwal Resmi
                </h4>
                <span class="text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-[#173B67]">
                    Terkonfirmasi
                </span>
            </div>

            <div class="p-3 rounded-xl bg-gradient-to-r from-blue-50 to-sky-50 border border-sky-200 space-y-1.5">
                <span class="text-[9.5px] text-slate-500 block">Jadwal Kedatangan Mitra:</span>
                <strong class="text-xs font-black text-[#173B67] block">{{ $jadwalPesanan }}</strong>
                <p class="text-[9.5px] text-slate-600 leading-snug">
                    <i class="fa-solid fa-circle-info text-sky-600 mr-0.5"></i> Mitra bersiap hadir sesuai jadwal. Status pesanan akan otomatis berganti ke <strong>Mitra OTW</strong> saat berangkat.
                </p>
            </div>

            <div class="space-y-2 text-[10.5px] pt-1">
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 w-24 shrink-0">Layanan:</span>
                    <strong class="text-slate-800 text-right">{{ $namaLayanan }}</strong>
                </div>
                <div class="flex items-start justify-between gap-2">
                    <span class="text-slate-500 w-24 shrink-0">Alamat Lokasi:</span>
                    <span class="text-slate-700 text-right">{{ $alamatPesanan }}</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-500 w-24 shrink-0">Mitra Penugasan:</span>
                    <strong class="text-slate-800">{{ $namaMitra }}</strong>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-500 w-24 shrink-0">Preferensi:</span>
                    <span class="text-slate-700">Mitra {{ $preferensiGender }}</span>
                </div>
            </div>
        </section>

        {{-- TOMBOL AKSI TAHAP 4 --}}
        <div class="space-y-2 pt-1">
            <a href="/customer/pesanan/{{ ltrim($kodePesanan, '#') }}?status=terjadwal" class="w-full py-3.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-route"></i>
                <span>Pantau Jadwal &amp; Status Pesanan</span>
            </a>
            <a href="{{ route('customer.dashboard') }}?tab=pesanan" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center block transition-colors">
                Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
    @endif

</main>

{{-- =========================================================
     MODAL HASIL PEMBAYARAN
========================================================= --}}
<div id="modalBayarSukses" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[400px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">

    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold leading-tight">Pembayaran Berhasil!</h3>
                <span class="text-[10px] text-emerald-100" id="bayarKodeTiket">{{ $kodePesanan }}</span>
            </div>
        </div>
        <button type="button" onclick="tutupModalBayar()" class="text-emerald-100 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <div class="p-4 space-y-3 text-xs">
        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-2">
            <div class="flex justify-between items-center text-slate-600">
                <span>Metode:</span>
                <strong class="text-slate-900 font-bold" id="bayarMetode">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Jumlah:</span>
                <strong class="text-emerald-600 font-extrabold text-sm" id="bayarJumlah">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Status Pembayaran:</span>
                <span class="text-emerald-700 font-extrabold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Lunas</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-blue-50/80 border border-blue-200 space-y-2">
            <span class="text-[11px] font-bold text-[#173B67] flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check text-sky-600"></i> Jadwal Pekerjaan Dikonfirmasi!
            </span>
            <p class="text-[10px] text-slate-600 leading-snug">
                Pembayaran telah diamankan oleh SayaBantu. Mitra akan hadir sesuai waktu yang telah disepakati:
            </p>
            <div class="pt-2 border-t border-blue-200/70 space-y-1.5">
                <div class="flex items-center justify-between gap-2 text-[10px]">
                    <span class="text-slate-500">Jadwal Mitra Datang:</span>
                    <strong class="text-[#0E1D31] font-black text-right" id="bayarJadwalAktif">{{ $jadwalPesanan }}</strong>
                </div>
                <div class="flex items-center justify-between gap-2 text-[10px]">
                    <span class="text-slate-500">Nomor Pesanan:</span>
                    <strong class="text-slate-800 font-mono">{{ $kodePesanan }}</strong>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <a id="btnLacakPesananTerjadwal" href="/customer/pesanan/{{ ltrim($kodePesanan, '#') }}?status=terjadwal" class="w-full py-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-center shadow-md flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-route"></i> Pantau Jadwal &amp; Status Pesanan
            </a>
            <a id="btnKembaliKePesanan" href="{{ route('customer.dashboard') }}?tab=pesanan" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-center transition-all block">
                Kembali ke Beranda
            </a>
        </div>
    </div>

</div>

<script>
    // Hitung mundur batas waktu pembayaran (15 menit).
    (function () {
        let sisa = 15 * 60;
        const el = document.getElementById('hitungMundurBayar');
        if (!el) return;

        const tick = setInterval(() => {
            sisa--;
            if (sisa <= 0) {
                clearInterval(tick);
                el.textContent = '00:00';
                return;
            }
            const m = String(Math.floor(sisa / 60)).padStart(2, '0');
            const d = String(sisa % 60).padStart(2, '0');
            el.textContent = m + ':' + d;
        }, 1000);
    })();

    function tutupModalBayar() {
        const modal = document.getElementById('modalBayarSukses');
        if (modal) modal.classList.add('hidden');
        const backdrop = document.getElementById('customerBackdrop');
        if (backdrop) backdrop.classList.add('hidden');
    }
    window.tutupModalBayar = tutupModalBayar;

    var metodeTerpilihObj = window.metodeTerpilihObj || null;

    // Tahap 1: Customer memilih radio metode (Barcode BELUM keluar)
    function onPilihMetodeRadio() {
        const terpilih = document.querySelector('input[name="metodeBayar"]:checked');
        if (!terpilih) return;

        metodeTerpilihObj = {
            kode: terpilih.value,
            nama: terpilih.dataset.nama || terpilih.value,
            tipe: terpilih.dataset.tipe || 'qris',
            bank: terpilih.dataset.bank || '',
            nomor: terpilih.dataset.nomor || '',
            atasNama: terpilih.dataset.atasNama || '',
            barcode: terpilih.dataset.barcode || '',
            catatan: terpilih.dataset.catatan || '',
        };

        const btn = document.getElementById('btnBayarSuruhan');
        const teks = document.getElementById('btnBayarSuruhanText');
        if (btn && teks) {
            btn.disabled = false;
            teks.textContent = 'Bayar Sekarang \u2022 Rp {{ $totalBayar }}';
        }
    }
    window.onPilihMetodeRadio = onPilihMetodeRadio;

    // Tahap 2: Klik 'Bayar Sekarang' -> Barcode QRIS / Rekening Bank baru keluar
    function bukaInstruksiPembayaran() {
        if (!metodeTerpilihObj) {
            onPilihMetodeRadio();
        }
        if (!metodeTerpilihObj) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pilih Metode Dulu', 'Silakan pilih salah satu metode pembayaran di atas.', 'warning');
            }
            return;
        }

        const judulEl = document.getElementById('instruksiJudulMetode');
        const ikonEl = document.getElementById('instruksiIkonMetode');
        const boxQris = document.getElementById('instruksiBoxQris');
        const boxTransfer = document.getElementById('instruksiBoxTransfer');

        if (metodeTerpilihObj.tipe === 'qris') {
            if (judulEl) judulEl.textContent = 'Scan Barcode QRIS (' + metodeTerpilihObj.nama + ')';
            if (ikonEl) ikonEl.className = 'fa-solid fa-qrcode';
            const img = document.getElementById('qrisBarcodeImg');
            if (img) img.src = metodeTerpilihObj.barcode;
            if (boxQris) boxQris.classList.remove('hidden');
            if (boxTransfer) boxTransfer.classList.add('hidden');
        } else {
            if (judulEl) judulEl.textContent = 'Transfer ke ' + (metodeTerpilihObj.bank || metodeTerpilihObj.nama);
            if (ikonEl) ikonEl.className = 'fa-solid fa-building-columns';
            const bNama = document.getElementById('instruksiBankNama');
            const bNomor = document.getElementById('instruksiNomorTujuan');
            const bAtas = document.getElementById('instruksiAtasNama');
            const bCat = document.getElementById('instruksiCatatanTeks');
            if (bNama) bNama.textContent = metodeTerpilihObj.bank || metodeTerpilihObj.nama;
            if (bNomor) bNomor.textContent = metodeTerpilihObj.nomor || '-';
            if (bAtas) bAtas.textContent = metodeTerpilihObj.atasNama || '-';
            if (bCat) bCat.textContent = metodeTerpilihObj.catatan || '';
            if (boxQris) boxQris.classList.add('hidden');
            if (boxTransfer) boxTransfer.classList.remove('hidden');
        }

        const t1 = document.getElementById('pembayaranTahap1');
        const t2 = document.getElementById('pembayaranTahap2');
        if (t1) t1.classList.add('hidden');
        if (t2) t2.classList.remove('hidden');

        window.scrollTo({ top: 100, behavior: 'smooth' });

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Instruksi Pembayaran Dibuka', 'Selesaikan pembayaran via ' + metodeTerpilihObj.nama, 'info');
        }
    }
    window.bukaInstruksiPembayaran = bukaInstruksiPembayaran;

    function kembaliKePilihMetode() {
        const t1 = document.getElementById('pembayaranTahap1');
        const t2 = document.getElementById('pembayaranTahap2');
        if (t2) t2.classList.add('hidden');
        if (t1) t1.classList.remove('hidden');
        window.scrollTo({ top: 80, behavior: 'smooth' });
    }
    window.kembaliKePilihMetode = kembaliKePilihMetode;

    // Salin nomor rekening / VA pada tahap instruksi
    function salinNomorInstruksi() {
        if (!metodeTerpilihObj || !metodeTerpilihObj.nomor) return;
        const nomor = metodeTerpilihObj.nomor;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(nomor).then(() => {
                if (typeof showCustomerToast === 'function') {
                    showCustomerToast('Nomor Disalin', 'Nomor tujuan berhasil disalin ke clipboard.', 'success');
                }
            });
            return;
        }
        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Nomor Tujuan', 'Nomor: ' + nomor, 'info');
        }
    }
    window.salinNomorInstruksi = salinNomorInstruksi;

    let customerBuktiBayarDataUrl = null;
    function previewBuktiBayarCustomer(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const ph = document.getElementById('buktiBayarCustomerPlaceholder');
        const box = document.getElementById('buktiBayarCustomerPreviewBox');
        const img = document.getElementById('buktiBayarCustomerPreviewImg');
        const name = document.getElementById('buktiBayarCustomerFileName');

        if (name) name.textContent = file.name;
        const reader = new FileReader();
        reader.onload = function(e) {
            customerBuktiBayarDataUrl = e.target.result;
            if (img) img.src = e.target.result;
            if (ph) ph.classList.add('hidden');
            if (box) {
                box.classList.remove('hidden');
                box.classList.add('flex');
            }
        };
        reader.readAsDataURL(file);
    }
    window.previewBuktiBayarCustomer = previewBuktiBayarCustomer;

    // Tahap 3: Customer mengklik badge/tombol "Saya Sudah Melakukan Pembayaran"
    async function konfirmasiSudahBayarAction() {
        const kodeBersih = '{{ ltrim($kodePesanan ?? "", "#") }}';
        if (!metodeTerpilihObj) {
            showCustomerToast('Pilih Metode Dulu', 'Pilih metode pembayaran sebelum mengonfirmasi pembayaran.', 'warning');
            return;
        }
        const labelMetode = metodeTerpilihObj.tipe === 'qris' ? 'QRIS' : metodeTerpilihObj.nama;

        const button = document.getElementById('btnKonfirmasiSudahBayar');
        const originalButtonText = button ? button.innerHTML : '';
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Mencatat pembayaran...</span>';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch(`/api/orders/${encodeURIComponent(kodeBersih)}/bayar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    metode: labelMetode,
                    bukti: customerBuktiBayarDataUrl || null
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Pembayaran gagal dicatat.');
            }
        } catch (error) {
            if (button) {
                button.disabled = false;
                button.innerHTML = originalButtonText;
            }
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Konfirmasi Gagal', error.message || 'Periksa koneksi Anda lalu coba kembali.', 'error');
            }
            return;
        }

        pembayaranMenungguKonfirmasiAdminAction(labelMetode);
    }
    window.konfirmasiSudahBayarAction = konfirmasiSudahBayarAction;

    function pembayaranMenungguKonfirmasiAdminAction(labelMetode) {
        simpanPembayaranMenungguKonfirmasiAdmin(labelMetode);

        const tahap1 = document.getElementById('pembayaranTahap1');
        const tahap2 = document.getElementById('pembayaranTahap2');
        const tahap3 = document.getElementById('pembayaranTahap3');
        const tahap4 = document.getElementById('pembayaranTahap4');
        if (tahap1) tahap1.classList.add('hidden');
        if (tahap2) tahap2.classList.add('hidden');
        if (tahap3) tahap3.classList.remove('hidden');
        if (tahap4) tahap4.classList.add('hidden');

        const button = document.getElementById('btnKonfirmasiSudahBayar');
        if (button) button.disabled = true;
        window.scrollTo({ top: 60, behavior: 'smooth' });

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Laporan Terkirim', 'Menunggu Admin memeriksa dan mengonfirmasi pembayaran Anda.', 'success');
        }
    }

    function simpanPembayaranMenungguKonfirmasiAdmin(labelMetode) {
        try {
            const KEY = 'sb_customer_pembayaran_selesai';
            const daftar = JSON.parse(localStorage.getItem(KEY) || '{}');
            daftar['{{ $kodePesanan }}'] = {
                kode: '{{ $kodePesanan }}',
                layanan: '{{ $namaLayanan }}',
                jadwal: '{{ $jadwalPesanan }}',
                total: '{{ $totalBayar }}',
                metode: labelMetode,
                status: 'menunggu_konfirmasi_pembayaran',
                dibayarPada: new Date().toISOString(),
            };
            localStorage.setItem(KEY, JSON.stringify(daftar));

            // Tampilkan laporan sebagai menunggu sampai Admin mengonfirmasi pembayaran.
            const rawActive = localStorage.getItem('sb_active_order');
            if (rawActive) {
                const ord = JSON.parse(rawActive);
                ord.status = 'menunggu_konfirmasi_pembayaran';
                ord.step = 7;
                ord.metodeBayar = labelMetode;
                ord.jadwal = '{{ $jadwalPesanan }}';
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }

            // Perbarui juga di riwayat pesanan (sb_customer_orders)
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(item => {
                    if ((item.code || '').replace('#', '') === '{{ ltrim($kodePesanan, "#") }}') {
                        item.status = 'menunggu_konfirmasi_pembayaran';
                        item.step = 7;
                        item.jadwal = '{{ $jadwalPesanan }}';
                        item.metodeBayar = labelMetode;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch (e) {
            // localStorage bisa diblokir (mode privat) - abaikan saja.
        }

    }

    document.addEventListener('DOMContentLoaded', () => {
        // Cek jika sudah ada metode yang terpilih sebelumnya
        const rad = document.querySelector('input[name="metodeBayar"]:checked');
        if (rad) onPilihMetodeRadio();
    });
</script>
@endsection
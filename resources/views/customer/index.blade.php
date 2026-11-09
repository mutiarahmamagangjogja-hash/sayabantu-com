@extends('layouts.customer')

@section('title', !empty($isOrderPage) ? 'Buat Suruhan - SayaBantu.com' : 'SayaBantu.com | Layanan Suruhan & Jasa Terpercaya')

{{-- =========================================================
     TOP AREA (GRADIENT BLUR UNIFORM DENGAN HEADER DUAL-ROLE)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    
    {{-- DECORATION CIRCLES --}}
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- TOP NAVBAR CUSTOMER (SERAGAM DENGAN DASHBOARD) --}}
    @include('customer.partials.mobile-header')

    {{-- GREETING BANNER (SERAGAM DENGAN DASHBOARD CUSTOMER, TANPA CARD/BADGE SEARCH) --}}
    <section class="relative z-10 px-5 pt-3 pb-2 space-y-2.5">

        {{-- 1. TAMPILAN TAMU PUBLIK (DEFAULT) --}}
        <div id="guestGreetingCard" class="space-y-1">
            <h2 class="text-[19px] font-bold tracking-tight text-white leading-snug">
                {{ !empty($isOrderPage) ? 'Buat Suruhan Baru' : 'Halo, Selamat Datang! 👋' }}
            </h2>

            <p class="text-[11px] text-white/70 leading-relaxed">
                {{ !empty($isOrderPage) ? 'Isi kebutuhan dan pilih wilayah agar mitra yang sesuai dapat ditemukan.' : 'Prioritas penugasan mitra tercepat & layanan suruhan apa aja siap melayani Anda.' }}
            </p>
        </div>

        {{-- 2. TAMPILAN CUSTOMER TERVERIFIKASI (KETIKA LOGIN CUSTOMER) --}}
        <div id="vipGreetingCard" class="hidden space-y-1">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-[9px] font-bold text-sky-300">
                <i class="fa-solid fa-circle-check text-[8px]"></i>
                Customer Terverifikasi • <span id="vipUserCodeText">#CUST-{{ Auth::id() ?? $customerId ?? '101' }}</span>
            </div>

            <h2 class="text-[19px] font-bold tracking-tight text-white leading-snug">
                Halo, <span id="vipUserNameText" class="text-sky-300">{{ $customerName ?? session('customer_name') ?? Auth::user()?->name ?? 'Customer' }}</span>! 👋
            </h2>

            <p class="text-[11px] text-white/70 leading-relaxed">
                Prioritas penugasan mitra tercepat & layanan CS resmi aktif untuk akun Anda.
            </p>
        </div>

        {{-- 3. BADGE KHUSUS SAAT CUSTOMER MELIHAT HALAMAN PENGUNJUNG PUBLIK --}}
        <div id="pengunjungCustomerBadge" class="hidden items-center justify-between px-3 py-1.5 rounded-xl bg-sky-500/20 border border-sky-400/30 text-sky-200 text-[10px]">
            <div class="flex items-center gap-1.5 font-medium">
                <i class="fa-solid fa-globe text-sky-300"></i>
                <span>Mode Tampilan Pengunjung Publik</span>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="font-bold text-white bg-sky-600/70 hover:bg-sky-600 px-2 py-0.5 rounded-lg border border-sky-400/40 text-[9.5px] transition-colors">
                Ke Dashboard &raquo;
            </a>
        </div>

        </section>

</div>

@endsection


{{-- =========================================================
     CONTENT AREA
========================================================= --}}
@section('content')

<main class="content-under-gradient px-4 pt-3 space-y-3.5">




@if(empty($isOrderPage))
@php
/*
| Komponen ringkas katalog layanan untuk BERANDA.
| Layanan yang tersedia tidak memerlukan keahlian teknis khusus.
*/

$featuredServices = [
    ['name' => 'Full Home Cleaning', 'label' => 'Full Clean', 'icon' => 'fa-broom', 'color' => 'emerald', 'budget' => '85.000',
     'desc' => 'Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.'],
    ['name' => 'Basic Daily Cleaning', 'label' => 'Sapu & Pel', 'icon' => 'fa-spray-can-sparkles', 'color' => 'emerald', 'budget' => '50.000',
     'desc' => 'Sapu, pel lantai rutin, dan lap meja/kaca.'],
    ['name' => 'Bersihkan Kamar Mandi', 'label' => 'Kamar Mandi', 'icon' => 'fa-shower', 'color' => 'teal', 'budget' => '65.000',
     'desc' => 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.'],
    ['name' => 'Cuci Piring & Dapur Bersih', 'label' => 'Cuci Dapur', 'icon' => 'fa-sink', 'color' => 'cyan', 'budget' => '45.000',
     'desc' => 'Bantu cuci piring tumpuk, bersihkan kompor & wastafel.'],
    ['name' => 'Pindahan', 'label' => 'Pindahan', 'icon' => 'fa-boxes-packing', 'color' => 'blue', 'budget' => '150.000',
     'desc' => 'Bantu packing, angkat barang pindahan kos/rumah ke kendaraan.'],
    ['name' => 'Angkut Barang', 'label' => 'Angkut Barang', 'icon' => 'fa-dolly', 'color' => 'blue', 'budget' => '80.000',
    'desc' => 'Bantu angkat dan memindahkan barang rumah tangga.'],
    ['name' => 'Antar / Jemput', 'label' => 'Antar Barang', 'icon' => 'fa-motorcycle', 'color' => 'sky', 'budget' => '50.000',
     'desc' => 'Antar barang, dokumen, atau mengambil kebutuhan harian.'],
];

$featuredColors = [
    'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
    'teal' => 'bg-teal-50 text-teal-600 border-teal-100',
    'cyan' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
    'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
    'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
];
@endphp



{{-- =====================================================
     LAYANAN POPULER (LANGSUNG DITAMPILKAN DI DASHBOARD)
====================================================== --}}
<section id="layananKatalogSection" class="card-sb p-3.5 space-y-3">
    <div class="flex items-center justify-between px-1">
        <div>
            <h3 class="text-xs font-bold text-[#0E1D31] flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-[#173B67]"></i> Layanan Tersedia
            </h3>
            <p class="text-[9px] text-slate-400">Pekerjaan tanpa keahlian teknis khusus</p>
        </div>
        <a href="{{ route('customer.layanan') }}" class="text-[9px] text-[#173B67] font-bold bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100 hover:bg-blue-100 transition-colors">
            Lihat Semua »
        </a>
    </div>

    {{-- GRID 8 LAYANAN UNGGULAN --}}
    <div class="grid grid-cols-4 gap-2 text-center pt-1">
        @foreach($featuredServices as $svc)
            <button
                type="button"
                onclick="bukaDetailLayanan({{ Js::from($svc['name']) }})"
                class="flex flex-col items-center group active:scale-95 transition-transform"
            >
                <div class="w-12 h-12 rounded-xl border flex items-center justify-center text-lg shadow-xs group-hover:bg-[#173B67] group-hover:text-white group-hover:border-[#173B67] transition-colors {{ $featuredColors[$svc['color']] }}">
                    <i class="fa-solid {{ $svc['icon'] }}"></i>
                </div>
                <span class="text-[9.5px] font-semibold text-slate-700 mt-1 line-clamp-1">{{ $svc['label'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- CTA KE HALAMAN SEMUA LAYANAN --}}
    <a
        href="{{ route('customer.layanan') }}"
        class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#0E1D31] text-[11px] font-bold transition-colors"
    >
        <i class="fa-solid fa-grip"></i> Lihat Semua Layanan
    </a>
</section>
@endif


    {{-- =====================================================
         FORMULIR INTERAKTIF: BUAT SURUHAN
         (Dengan Penugasan Otomatis Sistem - Selalu Tampil di Dashboard Publik)
    ====================================================== --}}
    <section id="orderFormCard" class="card-sb p-4 sm:p-5 border-2 border-slate-200/90 shadow-sm bg-white rounded-2xl">

        {{-- HEADER FORM & STATUS --}}
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-6 h-6 rounded-lg bg-sky-100 text-[#173B67] flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-file-signature"></i>
                    </span>
                    <h3 class="text-sm font-extrabold text-[#0E1D31]">Form Ajukan Suruhan</h3>
                </div>
                <p class="text-[10px] text-slate-500 mt-0.5">Alur pengajuan resmi SayaBantu langsung ke Mitra</p>
            </div>
            <button
                type="button"
                onclick="togglePubAlurOverview()"
                class="text-[9.5px] font-bold px-2.5 py-1 rounded-full bg-sky-50 hover:bg-sky-100 text-[#173B67] border border-sky-200 transition-all flex items-center gap-1 cursor-pointer"
            >
                <i class="fa-solid fa-circle-info text-sky-600"></i>
                <span>11 Alur Layanan</span>
                <i id="pubAlurChevron" class="fa-solid fa-chevron-down text-[8px] transition-transform"></i>
            </button>
        </div>

        {{-- EXPANDABLE 11 ALUR LENGKAP --}}
        <div id="pubAlurOverviewBox" class="hidden mb-4 p-3.5 rounded-xl bg-gradient-to-br from-slate-50 to-sky-50/60 border border-sky-200/90 text-xs transition-all">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-sky-200/60">
                <span class="text-[11px] font-black text-[#0E1D31] flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-sky-600"></i> Alur Pengajuan Suruhan hingga Selesai
                </span>
                <span class="text-[9px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-full border border-sky-200">
                    Langkah 1 - 11
                </span>
            </div>
            <ol class="space-y-1.5 text-[10px] text-slate-700">
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">1</span>
                    <span><strong>Mengisi Form:</strong> Pilih layanan katalog, detail kebutuhan, waktu, lokasi &amp; info pendukung.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">2</span>
                    <span><strong>Menentukan Metode Harga:</strong> Pilih <em>Nego Harga</em> (sesuai rentang) atau <em>Harga Pas</em> (katalog).</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">3</span>
                    <span><strong>Memeriksa &amp; Mengirim:</strong> Review seluruh data lalu kirim ke platform.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-sky-700 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">4</span>
                    <span><strong>Mitra Melihat Kebutuhan:</strong> Tampil pada halaman Kebutuhan Mitra untuk diambil/ditawar.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">5</span>
                    <span><strong>Negosiasi / Setuju Harga:</strong> Negosiasi via chat (jika Nego) atau persetujuan Mitra (jika Pas).</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">6</span>
                    <span><strong>Konfirmasi Admin:</strong> Admin memverifikasi kesesuaian detail &amp; harga kesepakatan.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">7</span>
                    <span><strong>Pembayaran:</strong> Customer diarahkan ke pembayaran bergaransi aman platform.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">8</span>
                    <span><strong>Melaksanakan Pekerjaan:</strong> Mitra bekerja sesuai kesepakatan (status Berlangsung).</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">9</span>
                    <span><strong>Konfirmasi Selesai:</strong> Mitra tandai selesai, Customer memverifikasi hasil.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">10</span>
                    <span><strong>Penerusan Dana Mitra:</strong> Dana yang ditahan diteruskan ke saldo Mitra.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">11</span>
                    <span><strong>Memberikan Ulasan:</strong> Penilaian rating &amp; ulasan untuk apresiasi Mitra.</span>
                </li>
            </ol>
        </div>

        {{-- BADGE CUSTOMER JIKA SUDAH LOGIN --}}
        <div id="vipBadgeForm" class="hidden mb-3 p-2 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-between text-xs">
            <span class="text-[10px] font-bold text-sky-800 flex items-center gap-1">
                <i class="fa-solid fa-circle-check text-sky-600"></i> Akun Customer Terhubung
            </span>
            <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                Siap Pesan
            </span>
        </div>

        {{-- STEPPER WIZARD 3 TAHAP PENGISIAN --}}
        <div class="mb-4">
            <div class="grid grid-cols-3 gap-1 relative text-[9px] font-bold text-center">
                {{-- Step 1 Tab --}}
                <button
                    type="button"
                    id="btnPubStep1"
                    onclick="goToPubStep(1)"
                    class="pub-step-btn py-1.5 px-1 rounded-xl bg-[#0E1D31] text-white flex flex-col items-center justify-center gap-0.5 transition-all shadow-xs"
                >
                    <span class="flex items-center gap-1 font-extrabold">
                        <span id="badgePubStep1" class="w-3.5 h-3.5 rounded-full bg-white text-[#0E1D31] text-[8px] flex items-center justify-center font-bold">1</span>
                        <span class="truncate">Detail Form</span>
                    </span>
                    <span class="text-[8px] opacity-80 font-normal truncate">Kebutuhan &amp; Lokasi</span>
                </button>

                {{-- Step 2 Tab --}}
                <button
                    type="button"
                    id="btnPubStep2"
                    onclick="goToPubStep(2)"
                    class="pub-step-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-400 opacity-60 cursor-not-allowed flex flex-col items-center justify-center gap-0.5 transition-all"
                >
                    <span class="flex items-center gap-1 font-bold">
                        <span id="badgePubStep2" class="w-3.5 h-3.5 rounded-full bg-slate-200 text-slate-500 text-[8px] flex items-center justify-center font-bold">2</span>
                        <span class="truncate">Biaya Layanan</span>
                    </span>
                    <span class="text-[8px] opacity-70 font-normal truncate">Min. Rp 10.000</span>
                </button>

                {{-- Step 3 Tab --}}
                <button
                    type="button"
                    id="btnPubStep3"
                    onclick="goToPubStep(3)"
                    class="pub-step-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-400 opacity-60 cursor-not-allowed flex flex-col items-center justify-center gap-0.5 transition-all"
                >
                    <span class="flex items-center gap-1 font-bold">
                        <span id="badgePubStep3" class="w-3.5 h-3.5 rounded-full bg-slate-200 text-slate-500 text-[8px] flex items-center justify-center font-bold">3</span>
                        <span class="truncate">Periksa &amp; Kirim</span>
                    </span>
                    <span class="text-[8px] opacity-70 font-normal truncate">Kirim ke Mitra</span>
                </button>
            </div>
        </div>

        <form id="publicSuruhanForm" onsubmit="handleSuruhanSubmit(event)" class="text-xs">

            {{-- ====================================================
                 LANGKAH 1: MENGISI DETAIL FORM SURUHAN
            ==================================================== --}}
            <div id="pubStepSection1" class="space-y-3.5">

                <div class="p-2.5 rounded-xl bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 flex items-start gap-2">
                    <i class="fa-solid fa-pen-fancy text-sky-600 mt-0.5 shrink-0 text-sm"></i>
                    <div class="flex-1">
                        <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 1: Mengisi Form Ajukan Suruhan</strong>
                        <p class="text-[10px] text-slate-600 leading-snug">
                            Pilih layanan katalog, lengkapi instruksi kebutuhan, waktu pengerjaan, dan lokasi alamat penugasan.
                        </p>
                    </div>
                </div>

                {{-- PILIH LAYANAN KATALOG --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10.5px] font-bold text-slate-700">
                            1. Pilih Layanan Katalog <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9px] text-slate-400 font-medium">Layanan tanpa keahlian teknis khusus</span>
                    </div>
                    <select
                        id="formLayanan"
                        onchange="onPubServiceSelected(this.value)"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all cursor-pointer"
                        required
                    >
                        <option value="" disabled selected>-- Pilih salah satu layanan katalog --</option>
                        <optgroup label="🧹 Kebersihan">
                            <option value="Full Home Cleaning">Full Home Cleaning (Pembersihan Menyeluruh)</option>
                            <option value="Basic Daily Cleaning">Basic Daily Cleaning (Sapu &amp; Pel Rutin)</option>
                            <option value="Bersihkan Kamar Mandi">Bersihkan Kamar Mandi &amp; Kloset</option>
                            <option value="Cuci Piring & Dapur Bersih">Cuci Piring &amp; Dapur Bersih</option>
                        </optgroup>
                        <optgroup label="📦 Pindahan &amp; Angkut">
                            <option value="Pindahan">Pindahan Kos &amp; Rumah</option>
                            <option value="Angkut Barang">Angkut Barang Rumah Tangga</option>
                            <option value="Buang Sampah">Buang Sampah Rumah Tangga</option>
                        </optgroup>
                        <optgroup label="🛵 Antar, Belanja &amp; Antre">
                            <option value="Antar / Jemput">Antar / Jemput Barang atau Orang</option>
                            <option value="SanSu Food">SanSu Food (Belanja Pasar / Makanan)</option>
                            <option value="Titip Antre RS & Faskes">Titip Antre RS, Faskes &amp; Tiket</option>
                        </optgroup>
                        <optgroup label="🤝 Kegiatan Harian">
                            <option value="Nemenin Olahraga">Nemenin Olahraga (Jogging / Gym)</option>
                            <option value="Bantu Kegiatan Harian">Bantu Kegiatan Harian</option>
                        </optgroup>
                    </select>

                    {{-- PREVIEW LAYANAN TERPILIH (TANPA MENAMPILKAN HARGA) --}}
                    <div id="pubServicePreviewCard" class="hidden mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 text-xs transition-all">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span id="pubPreviewIcon" class="w-8 h-8 rounded-lg bg-sky-100 text-[#173B67] flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-broom"></i>
                                </span>
                                <div>
                                    <h4 id="pubPreviewTitle" class="text-xs font-bold text-slate-900 leading-tight">Full Home Cleaning</h4>
                                    <span id="pubPreviewCat" class="text-[9px] font-semibold text-sky-700">Kategori: Kebersihan</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[9.5px]">Mitra Standby</span>
                            </div>
                        </div>
                        <p id="pubPreviewDesc" class="text-[9.5px] text-slate-600 mt-1.5 leading-snug">
                            Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.
                        </p>
                    </div>
                </div>

                {{-- DETAIL KEBUTUHAN / INSTRUKSI PEKERJAAN --}}
                <div>
                    <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                        2. Detail Kebutuhan atau Pekerjaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="formNote"
                        rows="2"
                        placeholder="Contoh: Bersihkan 2 kamar tidur dan 1 kamar mandi. Sapu, pel lantai, dan buang sampah ke tempat pembuangan..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all"
                        required
                    ></textarea>
                    <div class="flex items-center gap-1.5 mt-1 overflow-x-auto pb-0.5 text-[9px]">
                        <span class="text-slate-400 shrink-0">Saran cepat:</span>
                        <button type="button" onclick="appendPubNote('Perlu penanganan teliti & bersih.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                            + Teliti &amp; bersih
                        </button>
                        <button type="button" onclick="appendPubNote('Peralatan sapu & pel sudah siap.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                            + Alat sudah siap
                        </button>
                        <button type="button" onclick="appendPubNote('Bawa tangga jika perlu.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                            + Bawa alat bantu
                        </button>
                    </div>
                </div>

                {{-- WAKTU PENGERJAAN --}}
                <div class="space-y-2">
                    <label class="block text-[10.5px] font-bold text-slate-700">
                        3. Waktu Pengerjaan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" onclick="setDatePreset('today', this)" class="btn-date-chip py-1.5 rounded-lg bg-[#0E1D31] text-white font-bold text-[10px] text-center transition-all cursor-pointer">
                            Hari Ini
                        </button>
                        <button type="button" onclick="setDatePreset('tomorrow', this)" class="btn-date-chip py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] text-center transition-all cursor-pointer">
                            Besok
                        </button>
                        <button type="button" onclick="setDatePreset('custom', this)" class="btn-date-chip py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] text-center transition-all cursor-pointer">
                            Pilih Tanggal
                        </button>
                    </div>

                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-6">
                            <input
                                type="date"
                                id="formTanggal"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none"
                                value="{{ date('Y-m-d') }}"
                                required
                            >
                        </div>
                        <div class="col-span-3">
                            <select id="formJam" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:outline-none" required>
                                @for($j = 7; $j <= 23; $j++)
                                    @php $jamStr = sprintf('%02d', $j); @endphp
                                    <option value="{{ $jamStr }}" {{ $j == 14 ? 'selected' : '' }}>{{ $jamStr }}:00</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-span-3">
                            <select id="formMenit" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:outline-none" required>
                                <option value="00">00 Mnt</option>
                                <option value="15">15 Mnt</option>
                                <option value="30" selected>30 Mnt</option>
                                <option value="45">45 Mnt</option>
                            </select>
                        </div>
                    </div>

                    {{-- BATAS WAKTU MENDAPATKAN MITRA (DIINPUT CUSTOMER SEPERTI GOJEK) --}}
                    <div id="pubTodayNoticeBox" class="p-3 rounded-2xl bg-gradient-to-br from-amber-50/90 via-orange-50/70 to-amber-100/40 border border-amber-200/90 shadow-2xs space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[11px] shrink-0 shadow-sm">
                                    <i class="fa-solid fa-stopwatch animate-pulse"></i>
                                </span>
                                <div>
                                    <span class="text-[10.5px] font-extrabold text-amber-950 block leading-tight">Batas Waktu Menemukan Mitra</span>
                                    <span class="text-[8.5px] text-amber-800/80">Tentukan berapa lama Anda ingin menunggu mitra</span>
                                </div>
                            </div>
                            <span id="pubDurasiPillBadge" class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-amber-200/90 text-amber-950 border border-amber-300 shadow-2xs">
                                1 Jam (60 Mnt)
                            </span>
                        </div>

                        {{-- Input Angka & Pilihan Satuan --}}
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input
                                    type="number"
                                    id="formDurasiAngka"
                                    min="5"
                                    max="720"
                                    value="1"
                                    oninput="syncPubDurasiInput()"
                                    class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:border-[#173B67] focus:outline-none transition-all"
                                    placeholder="Cth: 1"
                                    required
                                >
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-semibold pointer-events-none">Durasi</span>
                            </div>
                            <div class="w-28 shrink-0">
                                <select
                                    id="formDurasiSatuan"
                                    onchange="syncPubDurasiInput()"
                                    class="w-full px-2.5 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:border-[#173B67] focus:outline-none transition-all cursor-pointer"
                                >
                                    <option value="jam" selected>Jam</option>
                                    <option value="menit">Menit</option>
                                </select>
                            </div>
                        </div>

                        {{-- Quick Chip Presets --}}
                        <div>
                            <span class="text-[8.5px] text-amber-900/80 font-bold block mb-1">Pilihan Cepat Waktu:</span>
                            <div class="grid grid-cols-4 gap-1.5 text-[9.5px]">
                                <button type="button" onclick="setPubDurasiPreset(15, 'menit', this)" class="btn-pub-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                    15 Mnt
                                </button>
                                <button type="button" onclick="setPubDurasiPreset(30, 'menit', this)" class="btn-pub-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                    30 Mnt
                                </button>
                                <button type="button" onclick="setPubDurasiPreset(1, 'jam', this)" class="btn-pub-durasi-chip py-1.5 rounded-lg bg-[#0E1D31] text-white font-bold border border-slate-900 transition-colors text-center cursor-pointer">
                                    1 Jam
                                </button>
                                <button type="button" onclick="setPubDurasiPreset(2, 'jam', this)" class="btn-pub-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                    2 Jam
                                </button>
                            </div>
                        </div>

                        <p class="text-[9px] text-amber-900/90 leading-snug">
                            <i class="fa-solid fa-radar text-amber-700 mr-1"></i> Sistem akan memancarkan radar pencarian mitra di sekitar lokasi Anda sesuai batas waktu yang Anda tentukan.
                        </p>

                        {{-- Hidden Form Storage --}}
                        <input type="hidden" id="formDurasiCari" value="1 Jam">
                        <input type="hidden" id="formDurasiMenit" value="60">
                    </div>
                </div>

                {{-- WILAYAH DAN ALAMAT LOKASI --}}
                <div>
                    <label for="formKota" class="block text-[10.5px] font-bold text-slate-700 mb-1">
                        4. Wilayah Pekerjaan <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="formKota"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all"
                        required
                    >
                        <option value="" selected disabled>-- Pilih kota lokasi pekerjaan --</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Bogor">Bogor</option>
                        <option value="Depok">Depok</option>
                        <option value="Tangerang">Tangerang</option>
                        <option value="Bekasi">Bekasi</option>
                        <option value="Bandung">Bandung</option>
                        <option value="Solo">Solo</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                    </select>
                    <a
                        href="{{ route('customer.mitra') }}"
                        onclick="return searchMitraByArea(event)"
                        class="inline-flex items-center gap-1 mt-1.5 text-[9px] font-semibold text-sky-700 hover:text-sky-900"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Cari mitra di kota ini
                    </a>
                </div>

                <div>
                    <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                        5. Alamat Lokasi &amp; Patokan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="formAlamat"
                        rows="2"
                        placeholder="Contoh: Jl. Sudirman No. 12, RT 03/RW 02, Patokan samping minimarket seberang halte..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all"
                        required
                    ></textarea>
                </div>

                {{-- CATATAN TAMBAHAN (OPSIONAL) - GENDER BEBAS OTOMATIS TANPA BAGIAN GENDER DI UI --}}
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="fa-solid fa-note-sticky text-[#173B67]"></i> 6. Catatan Tambahan (Opsional)
                        </span>
                        <span class="text-[8.5px] text-slate-400">Instruksi Khusus</span>
                    </div>

                    {{-- Gender mitra otomatis bebas tanpa form gender --}}
                    <input type="hidden" name="formGender" id="formGenderVal" value="Bebas">

                    <div>
                        <input
                            type="text"
                            id="formPendukung"
                            placeholder="Catatan tambahan: cth. Parkir mudah, patokan dekat gapura..."
                            class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#173B67]"
                        >
                    </div>
                </div>

                {{-- TOMBOL LANJUT KE STEP 2 (DINONAKTIFKAN UNTUK PENGUNJUNG PUBLIK BELUM LOGIN) --}}
                <button
                    type="button"
                    id="btnPubNextStep2"
                    disabled
                    class="w-full py-2.5 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-2 pointer-events-none select-none transition-all"
                    title="Masuk akun Customer untuk melanjutkan pengajuan suruhan"
                >
                    <span>Lanjut: Tentukan Biaya Layanan</span>
                    <i class="fa-solid fa-lock text-[10px]"></i>
                </button>

                <p id="pubNext2Notice" class="text-[9.5px] text-center text-slate-400 mt-2">
                    <i class="fa-solid fa-lock text-[9px] mr-1"></i> Tombol dinonaktifkan. Anda perlu masuk sebagai Customer untuk melanjutkan pengajuan suruhan.
                </p>

            </div>{{-- /#pubStepSection1 --}}


            {{-- ====================================================
             LANGKAH 2: MENENTUKAN BIAYA LAYANAN
             (Langsung Input Nominal, Minimal Rp 10.000)
        ==================================================== --}}
        <div id="pubStepSection2" class="space-y-3.5 hidden">

            <div class="p-2.5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex items-start gap-2">
                <i class="fa-solid fa-coins text-[#173B67] mt-0.5 shrink-0 text-sm"></i>
                <div class="flex-1">
                    <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 2: Tentukan Biaya Layanan</strong>
                    <p class="text-[10px] text-slate-600 leading-snug">
                        Masukkan nominal biaya yang Anda sediakan untuk pekerjaan ini. Tarif minimal pemesanan adalah <strong>Rp 10.000</strong>.
                    </p>
                </div>
            </div>

            {{-- BOX INPUT NOMINAL BIAYA --}}
            <div class="p-3.5 rounded-xl bg-white border-2 border-slate-200 space-y-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="formBudget" class="text-xs font-black text-slate-900 block">
                            Nominal Biaya Jasa <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-500">Ketik nominal biaya yang Anda sediakan</span>
                    </div>
                    <span class="text-[9px] font-black text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        Minimal Rp 10.000
                    </span>
                </div>

                <div class="relative flex items-center">
                    <span class="absolute left-3.5 text-sm font-black text-slate-500">Rp</span>
                    <input
                        type="text"
                        id="formBudget"
                        onkeyup="formatRupiahSimple(this);"
                        placeholder="50.000"
                        class="w-full pl-11 pr-3.5 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm font-black text-slate-900 focus:bg-white focus:border-[#173B67] focus:ring-2 focus:ring-[#173B67]/20 focus:outline-none transition-all"
                        required
                    >
                </div>

                {{-- Alert jika < 10.000 --}}
                <div id="pubBudgetMinAlert" class="hidden p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-[10px] flex items-center gap-1.5 font-bold">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>Biaya minimal pemesanan adalah Rp 10.000. Mohon masukkan nominal Rp 10.000 atau lebih.</span>
                </div>

                {{-- Preset chips nominal --}}
                <div class="space-y-1.5">
                    <span class="text-[9px] text-slate-400 font-semibold block">Pilihan Cepat Nominal:</span>
                    <div class="grid grid-cols-4 gap-1.5 text-[10px]">
                        <button type="button" onclick="setPubQuickNominal(20000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 20.000
                        </button>
                        <button type="button" onclick="setPubQuickNominal(50000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 50.000
                        </button>
                        <button type="button" onclick="setPubQuickNominal(75000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 75.000
                        </button>
                        <button type="button" onclick="setPubQuickNominal(100000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 100.000
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                    <span><i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>Garansi Transaksi Aman</span>
                    <span class="font-bold text-slate-700">Langsung diteruskan ke mitra</span>
                </div>
            </div>

            {{-- Hidden input sync kompatibilitas --}}
            <input type="hidden" id="formNego" value="Langsung">

            {{-- Dummy hidden cards untuk memelihara compat fungsi lama jika ada --}}
            <div class="hidden" id="pubCardNego"></div>
            <div class="hidden" id="pubCardPas"></div>
            <div class="hidden" id="pubBoxNego"></div>
            <div class="hidden" id="pubBoxPas"></div>

            {{-- TOMBOL NAVIGASI STEP 2 --}}
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToPubStep(1)"
                    class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali</span>
                </button>
                <button
                    type="button"
                    id="btnPubNextStep3"
                    disabled
                    class="py-2.5 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-1.5 pointer-events-none select-none transition-all"
                >
                    <span>Lanjut: Periksa Data</span>
                    <i class="fa-solid fa-lock text-[10px]"></i>
                </button>
            </div>

        </div>{{-- /#pubStepSection2 --}}


            {{-- ====================================================
                 LANGKAH 3: MEMERIKSA DAN MENGIRIM PENGAJUAN
            ==================================================== --}}
            <div id="pubStepSection3" class="space-y-3.5 hidden">

                <div class="p-2.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex items-start gap-2">
                    <i class="fa-solid fa-clipboard-check text-emerald-600 mt-0.5 shrink-0 text-sm"></i>
                    <div class="flex-1">
                        <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 3: Memeriksa dan Mengirim Pengajuan</strong>
                        <p class="text-[10px] text-slate-600 leading-snug">
                            Periksa kembali seluruh detail kebutuhan, alamat, jadwal, dan harga sebelum dikirimkan ke Mitra.
                        </p>
                    </div>
                </div>

                {{-- KARTU REVIEW RINGKASAN --}}
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ringkasan Pengajuan</span>
                        <button type="button" onclick="goToPubStep(1)" class="text-[9px] font-bold text-[#173B67] hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-pen text-[8px]"></i> Ubah
                        </button>
                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[9px] text-slate-400 block">Layanan Terpilih:</span>
                            <strong id="reviewPubLayanan" class="text-xs font-bold text-slate-900">-</strong>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[9px] text-slate-400 block">Status Penugasan:</span>
                            <span id="reviewPubMetodeBadge" class="text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                Siap Disiarkan
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[9px] text-slate-400 block">Jadwal Pengerjaan:</span>
                            <span id="reviewPubWaktu" class="text-[11px] font-semibold text-slate-800">-</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block">Batas Cari Mitra:</span>
                            <span id="reviewPubDurasi" class="text-[11px] font-bold text-amber-700">1 Jam</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] text-slate-400 block">Alamat &amp; Patokan Lokasi:</span>
                        <p id="reviewPubAlamat" class="text-[11px] text-slate-800 leading-snug">-</p>
                    </div>

                    <div>
                        <span class="text-[9px] text-slate-400 block">Detail Kebutuhan:</span>
                        <p id="reviewPubNote" class="text-[11px] text-slate-700 italic bg-white p-2 rounded-lg border border-slate-200/80 leading-snug">-</p>
                    </div>

                    {{-- Info Pendukung (Catatan) - Gender tidak ditampilkan di UI --}}
                    <div class="pt-1 text-[10px] text-slate-600 flex items-center justify-between">
                        <span id="reviewPubGender" class="hidden">Bebas</span>
                        <span id="reviewPubPendukungWrapper" class="text-slate-500 truncate max-w-full"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="text-[9.5px] font-bold text-slate-700 block">Total Biaya Suruhan:</span>
                            <span id="reviewPubPriceNote" class="text-[8.5px] text-slate-400">Minimal pemesanan Rp 10.000</span>
                        </div>
                        <strong id="reviewPubBudget" class="text-sm font-black text-emerald-700">Rp -</strong>
                    </div>
                </div>

                {{-- INFO TAHAP BERIKUTNYA --}}
                <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 space-y-1.5 text-[10px] text-slate-700">
                    <span class="text-[10.5px] font-bold text-[#173B67] flex items-center gap-1.5">
                        <i class="fa-solid fa-bullhorn text-sky-600"></i> Apa yang terjadi setelah dikirim?
                    </span>
                    <p class="text-[9.5px] text-slate-600 leading-relaxed">
                        Pengajuan Anda langsung masuk ke halaman <strong>Kebutuhan Mitra</strong> (Tahap 4). Mitra terdekat dapat melihat detail pekerjaan dan mengambil atau mengajukan penawaran.
                    </p>
                </div>

                {{-- SYARAT & KETENTUAN --}}
                <div class="pt-1 flex items-start gap-2">
                    <input type="checkbox" id="checkTerms" onchange="toggleFormSubmit(this.checked)" class="w-4 h-4 mt-0.5 rounded text-[#173B67] focus:ring-[#173B67] border-slate-300 cursor-pointer">
                    <label for="checkTerms" class="text-[10px] text-slate-600 leading-tight cursor-pointer">
                        Saya telah memeriksa seluruh data di atas dan menyetujui pengajuan suruhan untuk ditampilkan kepada Mitra SayaBantu.
                    </label>
                </div>

                {{-- TOMBOL AKSI STEP 3 --}}
                <div class="grid grid-cols-12 gap-2 pt-1">
                    <button
                        type="button"
                        onclick="goToPubStep(2)"
                        class="col-span-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali</span>
                    </button>
                    <button
                        type="submit"
                        id="btnSubmitSuruhan"
                        disabled
                        class="col-span-8 py-3 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-2 pointer-events-none select-none transition-all"
                        title="Masuk akun Customer untuk mengajukan suruhan"
                    >
                        <i class="fa-solid fa-lock text-[10px]"></i>
                        <span>Kirim Pengajuan (Perlu Login)</span>
                    </button>
                </div>

                <p id="pubSubmitNotice" class="text-[9.5px] text-center text-slate-400">
                    <i class="fa-solid fa-lock text-[9px] mr-1"></i> Tombol aksi dinonaktifkan. Anda perlu masuk sebagai Customer untuk mengajukan suruhan.
                </p>

            </div>{{-- /#pubStepSection3 --}}

        </form>

    </section>


    {{-- =====================================================
         4. TESTIMONI PELANGGAN
    ====================================================== --}}
    @if(empty($isOrderPage))
    <section class="card-sb p-3.5 space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold text-[#0E1D31]">Ulasan Pelanggan Terakhir</h3>
            <span class="text-[10px] text-slate-500 font-semibold">Dari pesanan berulasan</span>
        </div>

        @forelse(($latestReviews ?? collect()) as $review)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 text-xs space-y-1">
                <div class="flex justify-between items-center gap-2">
                    <strong class="font-bold text-slate-900 text-[11px]">{{ $review->customer_name ?: 'Customer' }}</strong>
                    <span class="text-[10px] text-amber-500">{{ str_repeat('★', (int) $review->review_rating) }}{{ str_repeat('☆', 5 - (int) $review->review_rating) }}</span>
                </div>
                @if($review->review_comment)
                    <p class="text-[11px] text-slate-700 italic">{{ $review->review_comment }}</p>
                @endif
                <span class="text-[9px] text-slate-500 block">Layanan: {{ $review->layanan }}</span>
            </div>
        @empty
            <p class="p-3 bg-slate-50 rounded-xl text-[10px] text-slate-600">Belum ada ulasan pelanggan.</p>
        @endforelse
    </section>
    @endif


    {{-- =====================================================
         5. FOOTER PUBLIK
    ====================================================== --}}
    <footer class="pt-2 pb-6 text-center text-[10px] text-slate-400 space-y-3">
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-slate-500 font-semibold">
            <a href="{{ route('customer.index') }}" class="hover:text-slate-900">Beranda</a>
            <span class="text-slate-300">•</span>
            <a href="{{ route('customer.layanan') }}" class="hover:text-slate-900">Layanan</a>
            <span class="text-slate-300">•</span>
            <a href="{{ route('customer.mitra') }}" class="hover:text-slate-900">Mitra Kami</a>
            <span class="text-slate-300">•</span>
            <a href="{{ route('customer.tentang') }}" class="hover:text-slate-900">Tentang</a>
        </div>

        <p>© 2026 <strong>SayaBantu.com</strong> • Solusi Suruhan Apa Aja Siap Melayani</p>
    </footer>

    @include('customer.partials.gojek-radar-modal')

</main>

@endsection

@push('scripts')
<script>
    // Smooth scroll to order form
    function scrollToOrderForm() {
        const orderPagePath = new URL(@json(route('customer.suruhan.create')), window.location.origin).pathname;
        if (window.location.pathname !== orderPagePath) {
            window.location.assign(orderPagePath);
            return;
        }

        const formEl = document.getElementById('orderFormCard');
        if (formEl) {
            formEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            formEl.classList.add('ring-2', 'ring-[#173B67]');
            setTimeout(() => formEl.classList.remove('ring-2', 'ring-[#173B67]'), 1500);
        }
    }

    function searchMitraByArea(event) {
        const kota = document.getElementById('formKota')?.value;
        if (!kota) {
            event.preventDefault();
            alert('Pilih wilayah pekerjaan terlebih dahulu.');
            return false;
        }

        event.currentTarget.href = `{{ route('customer.mitra') }}?kota=${encodeURIComponent(kota)}`;
        return true;
    }

    // Smooth scroll to Layanan catalog
    function scrollToLayananList() {
        const el = document.getElementById('layananKatalogSection');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            el.classList.add('ring-2', 'ring-[#173B67]');
            setTimeout(() => el.classList.remove('ring-2', 'ring-[#173B67]'), 1500);
        }
    }

    // Filter Service Catalog Categories
    function filterServiceCatalog(cat, btn) {
        document.querySelectorAll('.btn-cat-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
        btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');

        const items = document.querySelectorAll('.service-cat-item');
        items.forEach(item => {
            const itemCat = item.getAttribute('data-cat');
            if (cat === 'all' || itemCat === cat) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Filter Bocah SayaBantu by Region
    function filterBocahRegion(region, btn) {
        document.querySelectorAll('.btn-region-chip').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
        btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');

        const cards = document.querySelectorAll('.bocah-card-item');
        cards.forEach(card => {
            const cardReg = card.getAttribute('data-region');
            if (region === 'all' || cardReg === region) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // =========================================================
    //  GUEST GUARD — Semua aksi interaktif butuh login
    //  Jika belum login → tampilkan popup login, batal aksi.
    //  Jika sudah login → jalankan callback (opsional).
    // =========================================================
    function requireLogin(callback, role) {
        const user = getCustomerAuth();
        if (!user || !user.isLoggedIn) {
            // Tentukan role default sesuai konteks (customer)
            const targetRole = role || 'customer';
            switchLoginRole(targetRole);
            openModal('modalLoginCustomer');
            showCustomerToast(
                'Login Diperlukan',
                'Silakan masuk atau daftar akun untuk melanjutkan.',
                'warning'
            );
            return false;
        }
        if (typeof callback === 'function') callback();
        return true;
    }

    // Buka detail layanan (menggunakan modal detail modern Screen 2)
    // Pengunjung wajib masuk akun terlebih dahulu: langsung diarahkan ke halaman login (/customer/login)
    function bukaDetailLayanan(serviceName) {
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            const redirectUrl = encodeURIComponent(window.location.pathname + window.location.search);
            const serviceParam = encodeURIComponent(serviceName || '');
            window.location.href = "{{ route('customer.login') }}?redirect=" + redirectUrl + "&layanan=" + serviceParam;
            return;
        }

        if (typeof openModernServiceDetail === 'function') {
            openModernServiceDetail(serviceName);
        } else {
            const q = (serviceName || '').trim();
            const target = q ? `/layanan?q=${encodeURIComponent(q)}` : '/layanan';
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        }
    }

    // Filter kategori di bagian Layanan Rekomendasi (Home Service)
    function filterHomeServiceCategory(cat, btn) {
        document.querySelectorAll('.btn-home-pill').forEach(b => {
            b.classList.remove('bg-[#173B67]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
        btn.classList.add('bg-[#173B67]', 'text-white', 'font-bold');

        document.querySelectorAll('.home-service-card').forEach(card => {
            const c = card.getAttribute('data-hcat');
            if (cat === 'all' || c === cat) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Nama lama dipertahankan agar tautan lama tidak rusak.
    function selectServiceToForm(serviceName) {
        bukaDetailLayanan(serviceName);
    }

    // Date chips
    function setDatePreset(type, btn) {
        if (!isCustomerLoggedIn()) {
            promptGuestLoginOrder();
            return;
        }
        document.querySelectorAll('.btn-date-chip').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white');
            b.classList.add('bg-slate-100', 'text-slate-700');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700');
        btn.classList.add('bg-[#0E1D31]', 'text-white');

        const input = document.getElementById('formTanggal');
        const noticeBox = document.getElementById('pubTodayNoticeBox');
        const today = new Date();
        if (type === 'today') {
            input.value = today.toISOString().split('T')[0];
            if (noticeBox) noticeBox.classList.remove('hidden');
        } else if (type === 'tomorrow') {
            const tmrw = new Date(today);
            tmrw.setDate(tmrw.getDate() + 1);
            input.value = tmrw.toISOString().split('T')[0];
            if (noticeBox) noticeBox.classList.remove('hidden');
        } else {
            input.focus();
            if (noticeBox) noticeBox.classList.remove('hidden');
        }
    }

    // Sync input batas durasi cari mitra dari customer (Publik)
    function syncPubDurasiInput() {
        const angkaEl = document.getElementById('formDurasiAngka');
        const satuanEl = document.getElementById('formDurasiSatuan');
        const badgeEl = document.getElementById('pubDurasiPillBadge');
        const hiddenCari = document.getElementById('formDurasiCari');
        const hiddenMenit = document.getElementById('formDurasiMenit');
        const revDurasi = document.getElementById('reviewPubDurasi');

        let angka = parseInt(angkaEl ? angkaEl.value : 1) || 1;
        if (angka < 1) angka = 1;
        const satuan = (satuanEl ? satuanEl.value : 'jam').toLowerCase();

        let totalMenit = (satuan === 'jam') ? (angka * 60) : angka;
        let label = (satuan === 'jam') ? `${angka} Jam` : `${angka} Menit`;
        let badgeText = (satuan === 'jam') ? `${angka} Jam (${totalMenit} Mnt)` : `${angka} Menit`;

        if (badgeEl) badgeEl.innerText = badgeText;
        if (hiddenCari) hiddenCari.value = label;
        if (hiddenMenit) hiddenMenit.value = totalMenit;
        if (revDurasi) revDurasi.innerText = label;
    }
    window.syncPubDurasiInput = syncPubDurasiInput;

    function setPubDurasiPreset(angka, satuan, btn) {
        const angkaEl = document.getElementById('formDurasiAngka');
        const satuanEl = document.getElementById('formDurasiSatuan');
        if (angkaEl) angkaEl.value = angka;
        if (satuanEl) satuanEl.value = satuan;

        document.querySelectorAll('.btn-pub-durasi-chip').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white');
            b.classList.add('bg-white/90', 'text-amber-900');
        });
        if (btn) {
            btn.classList.remove('bg-white/90', 'text-amber-900');
            btn.classList.add('bg-[#0E1D31]', 'text-white');
        }

        syncPubDurasiInput();
    }
    window.setPubDurasiPreset = setPubDurasiPreset;

    // =========================================================
    //  KATALOG DATA RESMI UNTUK ALUR PENGAJUAN SURUHAN (PUBLIK)
    // =========================================================
    const PUB_CATALOG_DATA = {
        'Full Home Cleaning': { name: 'Full Home Cleaning', category: 'Kebersihan', desc: 'Pembersihan rumah menyeluruh meliputi kamar tidur, ruang tamu, lantai dan debu.', catalogPrice: '85.000', minPrice: '70.000', maxPrice: '120.000', icon: 'fa-broom' },
        'Basic Daily Cleaning': { name: 'Basic Daily Cleaning', category: 'Kebersihan', desc: 'Sapu, pel lantai rutin, dan lap meja/kaca ruangan.', catalogPrice: '50.000', minPrice: '40.000', maxPrice: '75.000', icon: 'fa-spray-can-sparkles' },
        'Bersihkan Kamar Mandi': { name: 'Bersihkan Kamar Mandi', category: 'Kebersihan', desc: 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.', catalogPrice: '65.000', minPrice: '50.000', maxPrice: '90.000', icon: 'fa-shower' },
        'Cuci Piring & Dapur Bersih': { name: 'Cuci Piring & Dapur Bersih', category: 'Kebersihan', desc: 'Bantu cuci piring tumpuk, kompor bersih & wastafel kinclong.', catalogPrice: '45.000', minPrice: '35.000', maxPrice: '70.000', icon: 'fa-sink' },
        'Pindahan': { name: 'Pindahan Kos & Rumah', category: 'Pindahan & Angkut', desc: 'Bantu packing, angkat muatan barang pindahan kos/rumah ke kendaraan.', catalogPrice: '150.000', minPrice: '120.000', maxPrice: '250.000', icon: 'fa-boxes-packing' },
        'Angkut Barang': { name: 'Angkut Barang / Beban Berat', category: 'Pindahan & Angkut', desc: 'Angkat lemari, kulkas, kasur, atau tata ulang perabotan.', catalogPrice: '80.000', minPrice: '60.000', maxPrice: '130.000', icon: 'fa-dolly' },
        'Buang Sampah': { name: 'Buang Sampah / Puing', category: 'Pindahan & Angkut', desc: 'Buang puing renovasi, sampah daun taman, atau barang rongsok.', catalogPrice: '40.000', minPrice: '35.000', maxPrice: '70.000', icon: 'fa-trash-can' },
        'Antar / Jemput': { name: 'Antar / Jemput Barang & Dokumen', category: 'Antar, Belanja & Antre', desc: 'Antar barang, dokumen penting atau ambil laundry kilat.', catalogPrice: '50.000', minPrice: '35.000', maxPrice: '80.000', icon: 'fa-motorcycle' },
        'SanSu Food': { name: 'SanSu Food (Belanja Pasar / Makanan)', category: 'Antar, Belanja & Antre', desc: 'Belanja sayur segar subuh pasar tradisional atau supermarket.', catalogPrice: '45.000', minPrice: '35.000', maxPrice: '75.000', icon: 'fa-basket-shopping' },
        'Titip Antre RS & Faskes': { name: 'Titip Antre RS, Faskes & Tiket', category: 'Antar, Belanja & Antre', desc: 'Ambilkan nomor antrean dokter, faskes, atau antre tiket event.', catalogPrice: '60.000', minPrice: '45.000', maxPrice: '90.000', icon: 'fa-hourglass-half' },
        'Nemenin Olahraga': { name: 'Nemenin Olahraga (Jogging / Gym)', category: 'Khusus & Lainnya', desc: 'Teman jogging pagi, gym, gowes atau jaga stand toko.', catalogPrice: '60.000', minPrice: '45.000', maxPrice: '90.000', icon: 'fa-person-running' },
        'Bantu Kegiatan Harian': { name: 'Bantu Kegiatan Harian', category: 'Kegiatan Harian', desc: 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.', catalogPrice: '60.000', minPrice: '45.000', maxPrice: '90.000', icon: 'fa-handshake' }
    };

    let pubCurrentStep = 1;
    let pubCurrentPricingMethod = 'Nego';

    function togglePubAlurOverview() {
        const box = document.getElementById('pubAlurOverviewBox');
        const chev = document.getElementById('pubAlurChevron');
        if (!box) return;
        const isHidden = box.classList.contains('hidden');
        if (isHidden) {
            box.classList.remove('hidden');
            if (chev) chev.classList.add('rotate-180');
        } else {
            box.classList.add('hidden');
            if (chev) chev.classList.remove('rotate-180');
        }
    }

    function appendPubNote(text) {
        const noteEl = document.getElementById('formNote');
        if (!noteEl) return;
        if (noteEl.value.trim() === '') {
            noteEl.value = text;
        } else {
            noteEl.value = noteEl.value.trim() + ' ' + text;
        }
        noteEl.focus();
    }

    function onPubServiceSelected(serviceName) {
        const data = PUB_CATALOG_DATA[serviceName] || {
            name: serviceName,
            category: 'Layanan SayaBantu',
            desc: 'Layanan bantuan tugas cepat SayaBantu.',
            catalogPrice: '85.000',
            minPrice: '60.000',
            maxPrice: '120.000',
            icon: 'fa-handshake'
        };

        const previewCard = document.getElementById('pubServicePreviewCard');
        if (previewCard) {
            previewCard.classList.remove('hidden');
            const titleEl = document.getElementById('pubPreviewTitle');
            const catEl = document.getElementById('pubPreviewCat');
            const descEl = document.getElementById('pubPreviewDesc');
            const iconEl = document.getElementById('pubPreviewIcon');

            if (titleEl) titleEl.innerText = data.name;
            if (catEl) catEl.innerText = 'Kategori: ' + data.category;
            if (descEl) descEl.innerText = data.desc;
            if (iconEl) iconEl.innerHTML = `<i class="fa-solid ${data.icon}"></i>`;
        }

        const negoRangeNotice = document.getElementById('pubNegoRangeNotice');
        if (negoRangeNotice) {
            negoRangeNotice.innerText = `Rp ${data.minPrice} - Rp ${data.maxPrice}`;
        }
        const pasLabel = document.getElementById('pubPasServiceLabel');
        const pasDisplay = document.getElementById('pubPasPriceDisplay');
        if (pasLabel) pasLabel.innerText = data.name;
        if (pasDisplay) pasDisplay.innerText = 'Rp ' + data.catalogPrice;

        const btnMin = document.getElementById('pubBtnPresetMin');
        const btnMid = document.getElementById('pubBtnPresetMid');
        const btnMax = document.getElementById('pubBtnPresetMax');
        if (btnMin) btnMin.innerText = 'Min: Rp ' + data.minPrice;
        if (btnMid) btnMid.innerText = 'Rekomendasi (' + data.catalogPrice + ')';
        if (btnMax) btnMax.innerText = 'Maks: Rp ' + data.maxPrice;

        const budgetInput = document.getElementById('formBudget');
        if (budgetInput) {
            if (pubCurrentPricingMethod === 'Pas' || !budgetInput.value) {
                budgetInput.value = data.catalogPrice;
            }
        }
    }

    function selectPubPricingMethod(method) {
        pubCurrentPricingMethod = method;
        const selNego = document.getElementById('formNego');
        if (selNego) selNego.value = method;

        const cardNego = document.getElementById('pubCardNego');
        const cardPas = document.getElementById('pubCardPas');
        const dotNego = document.getElementById('pubDotNego');
        const dotPas = document.getElementById('pubDotPas');
        const boxNego = document.getElementById('pubBoxNego');
        const boxPas = document.getElementById('pubBoxPas');
        const budgetInput = document.getElementById('formBudget');

        const serviceName = (document.getElementById('formLayanan') || {}).value;
        const catalogInfo = PUB_CATALOG_DATA[serviceName] || { catalogPrice: '85.000' };

        if (method === 'Nego') {
            if (cardNego) cardNego.className = 'p-3 rounded-xl border-2 border-[#173B67] bg-blue-50/50 cursor-pointer transition-all relative flex flex-col justify-between';
            if (cardPas) cardPas.className = 'p-3 rounded-xl border-2 border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all relative flex flex-col justify-between';
            if (dotNego) {
                dotNego.innerHTML = '<span class="w-2 h-2 rounded-full bg-[#173B67]"></span>';
                dotNego.className = 'w-4 h-4 rounded-full border-2 border-[#173B67] flex items-center justify-center';
            }
            if (dotPas) {
                dotPas.innerHTML = '<span class="w-2 h-2 rounded-full bg-transparent"></span>';
                dotPas.className = 'w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center';
            }
            if (boxNego) boxNego.classList.remove('hidden');
            if (boxPas) boxPas.classList.add('hidden');
        } else {
            if (cardNego) cardNego.className = 'p-3 rounded-xl border-2 border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all relative flex flex-col justify-between';
            if (cardPas) cardPas.className = 'p-3 rounded-xl border-2 border-emerald-600 bg-emerald-50/50 cursor-pointer transition-all relative flex flex-col justify-between';
            if (dotNego) {
                dotNego.innerHTML = '<span class="w-2 h-2 rounded-full bg-transparent"></span>';
                dotNego.className = 'w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center';
            }
            if (dotPas) {
                dotPas.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-600"></span>';
                dotPas.className = 'w-4 h-4 rounded-full border-2 border-emerald-600 flex items-center justify-center';
            }
            if (boxNego) boxNego.classList.add('hidden');
            if (boxPas) boxPas.classList.remove('hidden');
            if (budgetInput) budgetInput.value = catalogInfo.catalogPrice;
        }
    }

    function setPubNegoPreset(presetType) {
        const serviceName = (document.getElementById('formLayanan') || {}).value;
        const catalogInfo = PUB_CATALOG_DATA[serviceName] || { minPrice: '70.000', catalogPrice: '85.000', maxPrice: '120.000' };
        const budgetInput = document.getElementById('formBudget');
        if (!budgetInput) return;

        if (presetType === 'min') {
            budgetInput.value = catalogInfo.minPrice;
        } else if (presetType === 'mid') {
            budgetInput.value = catalogInfo.catalogPrice;
        } else if (presetType === 'max') {
            budgetInput.value = catalogInfo.maxPrice;
        }
    }

    // Helper cek status login Customer untuk memblokir aksi form
    function isCustomerLoggedIn() {
        const user = (typeof getCustomerAuth === 'function') ? getCustomerAuth() : null;
        return !!(user && user.isLoggedIn && user.role === 'customer');
    }

    function goToPubStep(step) {
        if (!isCustomerLoggedIn() && step > 1) {
            return;
        }
        if (step > pubCurrentStep) {
            if (pubCurrentStep === 1 && !validatePubStep1()) return;
            if (pubCurrentStep === 2 && !validatePubStep2()) return;
        }
        renderPubStep(step);
    }

    function nextPubStep(targetStep) {
        if (!isCustomerLoggedIn() && targetStep > 1) {
            return;
        }
        if (pubCurrentStep === 1 && !validatePubStep1()) return;
        if (pubCurrentStep === 2 && !validatePubStep2()) return;
        renderPubStep(targetStep);
    }

    function validatePubStep1() {
        const layanan = (document.getElementById('formLayanan') || {}).value;
        if (!layanan) {
            showCustomerToast('Pilih Layanan', 'Silakan pilih layanan katalog yang Anda perlukan.', 'warning');
            const el = document.getElementById('formLayanan');
            if (el) el.focus();
            return false;
        }
        const note = (document.getElementById('formNote') || {}).value;
        if (!note || !note.trim()) {
            showCustomerToast('Detail Kebutuhan', 'Mohon isi detail pekerjaan atau instruksi suruhan Anda (minimal 5 karakter).', 'warning');
            const el = document.getElementById('formNote');
            if (el) el.focus();
            return false;
        }
        const tanggal = (document.getElementById('formTanggal') || {}).value;
        if (!tanggal) {
            showCustomerToast('Pilih Tanggal', 'Silakan tentukan tanggal pengerjaan suruhan.', 'warning');
            return false;
        }
        const alamat = (document.getElementById('formAlamat') || {}).value;
        if (!alamat || !alamat.trim()) {
            showCustomerToast('Alamat Lokasi', 'Mohon masukkan alamat lengkap dan patokan lokasi pengerjaan.', 'warning');
            const el = document.getElementById('formAlamat');
            if (el) el.focus();
            return false;
        }
        return true;
    }

    function setPubQuickNominal(nominal) {
        const el = document.getElementById('formBudget');
        if (!el) return;
        el.value = Number(nominal).toLocaleString('id-ID');
        el.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
        const alertEl = document.getElementById('pubBudgetMinAlert');
        if (alertEl) alertEl.classList.add('hidden');
    }
    window.setPubQuickNominal = setPubQuickNominal;

    function validatePubStep2() {
        const el = document.getElementById('formBudget');
        const budget = (el || {}).value;
        const rawBudgetNum = parseInt((budget || '').replace(/[^0-9]/g, '')) || 0;
        const alertEl = document.getElementById('pubBudgetMinAlert');

        if (!budget || budget.trim() === '' || rawBudgetNum < 10000) {
            if (alertEl) alertEl.classList.remove('hidden');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Biaya Minimal', 'Nominal biaya suruhan minimal adalah Rp 10.000.', 'warning');
            } else {
                alert('Nominal biaya suruhan minimal adalah Rp 10.000.');
            }
            if (el) {
                el.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                el.focus();
                el.addEventListener('input', () => {
                    const currentVal = parseInt(el.value.replace(/[^0-9]/g, '')) || 0;
                    if (currentVal >= 10000) {
                        el.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                        if (alertEl) alertEl.classList.add('hidden');
                    }
                });
            }
            return false;
        }
        if (alertEl) alertEl.classList.add('hidden');
        return true;
    }

    function renderPubStep(step) {
        pubCurrentStep = step;

        const sec1 = document.getElementById('pubStepSection1');
        const sec2 = document.getElementById('pubStepSection2');
        const sec3 = document.getElementById('pubStepSection3');

        if (sec1) sec1.classList.toggle('hidden', step !== 1);
        if (sec2) sec2.classList.toggle('hidden', step !== 2);
        if (sec3) sec3.classList.toggle('hidden', step !== 3);

        for (let i = 1; i <= 3; i++) {
            const btn = document.getElementById('btnPubStep' + i);
            const badge = document.getElementById('badgePubStep' + i);
            if (!btn || !badge) continue;

            if (i === step) {
                btn.className = 'pub-step-btn py-1.5 px-1 rounded-xl bg-[#0E1D31] text-white flex flex-col items-center justify-center gap-0.5 transition-all shadow-xs';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-white text-[#0E1D31] text-[8px] flex items-center justify-center font-black';
                badge.innerHTML = i.toString();
            } else if (i < step) {
                btn.className = 'pub-step-btn py-1.5 px-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex flex-col items-center justify-center gap-0.5 transition-all';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-emerald-600 text-white text-[8px] flex items-center justify-center font-bold';
                badge.innerHTML = '<i class="fa-solid fa-check text-[7px]"></i>';
            } else {
                btn.className = 'pub-step-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex flex-col items-center justify-center gap-0.5 transition-all';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-slate-300 text-slate-700 text-[8px] flex items-center justify-center font-bold';
                badge.innerHTML = i.toString();
            }
        }

        if (step === 3) {
            populatePubFormReview();
        }

        const card = document.getElementById('orderFormCard');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function populatePubFormReview() {
        const layanan = (document.getElementById('formLayanan') || {}).value || '-';
        const tanggal = (document.getElementById('formTanggal') || {}).value || '-';
        const jam = (document.getElementById('formJam') || {}).value || '14';
        const menit = (document.getElementById('formMenit') || {}).value || '30';
        const alamat = (document.getElementById('formAlamat') || {}).value || '-';
        const note = (document.getElementById('formNote') || {}).value || '-';
        const budget = (document.getElementById('formBudget') || {}).value || '0';
        const gender = (document.querySelector('input[name="formGender"]:checked') || {}).value || 'Bebas';
        const pendukung = (document.getElementById('formPendukung') || {}).value || '';

        const revLay = document.getElementById('reviewPubLayanan');
        const revWkt = document.getElementById('reviewPubWaktu');
        const revAlm = document.getElementById('reviewPubAlamat');
        const revNot = document.getElementById('reviewPubNote');
        const revBgt = document.getElementById('reviewPubBudget');
        const revGdr = document.getElementById('reviewPubGender');
        const revPen = document.getElementById('reviewPubPendukungWrapper');
        const revMetBadge = document.getElementById('reviewPubMetodeBadge');
        const revPriceNote = document.getElementById('reviewPubPriceNote');
        const revDurasi = document.getElementById('reviewPubDurasi');

        if (revLay) revLay.innerText = layanan;
        if (revWkt) revWkt.innerText = `${tanggal} • Pukul ${jam}:${menit} WIB`;
        if (revAlm) revAlm.innerText = alamat;
        if (revNot) revNot.innerText = note;
        if (revGdr) revGdr.innerText = gender;
        if (revPen) revPen.innerText = pendukung ? `Catatan: ${pendukung}` : '';

        const durasiVal = (document.getElementById('formDurasiCari') || {}).value || '1 Jam';
        if (revDurasi) revDurasi.innerText = durasiVal;

        const rawBudgetNum = parseInt(budget.replace(/[^0-9]/g, '')) || 0;
        const finalBudgetNum = Math.max(10000, rawBudgetNum);
        if (revBgt) revBgt.innerText = 'Rp ' + finalBudgetNum.toLocaleString('id-ID');

        if (revMetBadge) {
            revMetBadge.innerText = 'Siap Disiarkan';
            revMetBadge.className = 'text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
        }
        if (revPriceNote) revPriceNote.innerText = 'Minimal pemesanan Rp 10.000';
    }

    // Format Rupiah Simple
    function formatRupiahSimple(el) {
        let number_string = el.value.replace(/[^,\d]/g, '').toString();
        let split = number_string.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        el.value = rupiah;
    }

    // Toggle submit button (tombol aksi tidak bisa di-klik jika belum login)
    function toggleFormSubmit(checked) {
        const btn = document.getElementById('btnSubmitSuruhan');
        if (!btn) return;

        if (!isCustomerLoggedIn()) {
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'pointer-events-none', 'bg-slate-200', 'text-slate-400');
            btn.classList.remove('bg-[#0E1D31]', 'hover:bg-[#173B67]', 'text-white');
            return;
        }

        if (checked) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none', 'bg-slate-200', 'text-slate-400');
            btn.classList.add('bg-[#0E1D31]', 'text-white');
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>Kirim Pengajuan Suruhan</span>';
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Handle Form Submit (Requires Login Identity!)
    function handleSuruhanSubmit(e) {
        e.preventDefault();

        if (!isCustomerLoggedIn()) {
            let uFix = (typeof getCustomerAuth === 'function' ? getCustomerAuth() : null);
            if (!uFix) {
                try {
                    localStorage.removeItem('sb_customer_logged_out');
                    // Keep session as is without forcing dummy name
                } catch(e) {}
            }
        }

        const user = (typeof getCustomerAuth === 'function' ? getCustomerAuth() : null) || { name: 'Customer', phone: '' };
        const tanggal = document.getElementById('formTanggal').value;
        const jam = document.getElementById('formJam').value;
        const menit = document.getElementById('formMenit').value;
        const layanan = document.getElementById('formLayanan').value;
        const alamat = document.getElementById('formAlamat').value;
        const kota = document.getElementById('formKota').value;
        const note = document.getElementById('formNote').value;
        const budget = document.getElementById('formBudget').value;
        const durasiCari = (document.getElementById('formDurasiCari') || {}).value || '1 Jam';
        const durasiMenit = parseInt((document.getElementById('formDurasiMenit') || {}).value) || 60;

        const rawBudgetNum = parseInt((budget || '').replace(/[^0-9]/g, '')) || 0;
        if (rawBudgetNum < 10000) {
            goToPubStep(2);
            validatePubStep2();
            return;
        }

        const finalBudgetString = rawBudgetNum.toLocaleString('id-ID');

        const btn = document.getElementById('btnSubmitSuruhan');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyiarkan ke Mitra Terdekat...';
        }

        const randCode = '#SB-SURUH-' + Math.floor(1000 + Math.random() * 9000);
        const targetStatus = 'menunggu_mitra';
        const genderVal = 'Bebas';
        const cleanCode = randCode.replace('#', '');

        const orderData = {
            code: randCode,
            layanan: layanan,
            tanggal: tanggal,
            jam: `${jam}:${menit} WIB`,
            jadwal: `${tanggal} • ${jam}:${menit} WIB`,
            durasi_cari: durasiCari,
            durasi_menit: durasiMenit,
            alamat: alamat,
            kota: kota,
            note: note,
            budget: finalBudgetString,
            biaya: finalBudgetString,
            original_budget: finalBudgetString,
            metode: 'Langsung',
            gender: genderVal,
            status: targetStatus,
            step: 4,
            customer_name: user.name || 'Customer',
            customer_phone: user.phone || '',
            customerName: user.name || 'Customer',
            customerPhone: user.phone || '',
            createdAt: new Date().toISOString()
        };

        const todayIso = new Date().toISOString().split('T')[0];
        const isTodayOrder = (tanggal === todayIso) || (tanggal.toLowerCase().includes('hari ini'));

        const q = new URLSearchParams({
            layanan: layanan,
            jadwal: `${tanggal} • ${jam}:${menit} WIB`,
            durasi_cari: durasiCari,
            durasi_menit: durasiMenit,
            biaya: finalBudgetString,
            metode: 'Langsung',
            status: targetStatus,
            alamat: alamat,
            kota: kota,
            note: note,
            gender: genderVal,
        });

        const targetOrderUrl = `/customer/pesanan/${cleanCode}?${q.toString()}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/api/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(orderData)
        })
        .then(r => r.json())
        .then(res => {
            try {
                localStorage.setItem('sb_active_order', JSON.stringify(orderData));
                let orders = [];
                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) orders = JSON.parse(rawList);
                orders.unshift(orderData);
                localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                window.dispatchEvent(new Event('storage'));
            } catch(e) {}

            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pengajuan Berhasil!', 'Mencari mitra di sekitar lokasi Anda...', 'success');
            }

            const finalUrl = res.redirect || targetOrderUrl;
            if (isTodayOrder) {
                openGojekSearchModal(orderData, finalUrl);
            } else {
                if (typeof spaNavigateTo === 'function') {
                    spaNavigateTo(finalUrl);
                } else {
                    window.location.href = finalUrl;
                }
            }
        })
        .catch(() => {
            try {
                localStorage.setItem('sb_active_order', JSON.stringify(orderData));
                let orders = [];
                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) orders = JSON.parse(rawList);
                orders.unshift(orderData);
                localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                window.dispatchEvent(new Event('storage'));
            } catch(e) {}

            if (isTodayOrder) {
                openGojekSearchModal(orderData, targetOrderUrl);
            } else {
                if (typeof spaNavigateTo === 'function') {
                    spaNavigateTo(targetOrderUrl);
                } else {
                    window.location.href = targetOrderUrl;
                }
            }
        });
    }

    let pubRadarInterval = null;
    let pubTickerInterval = null;

    function openGojekSearchModal(order, redirectUrl) {
        const modal = document.getElementById('sbGojekSearchModal');
        if (!modal) {
            window.location.href = redirectUrl;
            return;
        }

        const codeEl = document.getElementById('radarOrderCode');
        const layEl = document.getElementById('radarOrderLayanan');
        const bgtEl = document.getElementById('radarOrderBiaya');
        const btnGo = document.getElementById('btnRadarGoToOrder');

        if (codeEl) codeEl.innerText = order.code || '#SB-SURUH-....';
        if (layEl) layEl.innerText = order.layanan || 'Layanan Suruhan';
        if (bgtEl) bgtEl.innerText = 'Rp ' + (order.biaya || '0');
        if (btnGo) {
            btnGo.onclick = function() {
                closeGojekSearchModal();
                if (typeof spaNavigateTo === 'function') {
                    spaNavigateTo(redirectUrl);
                } else {
                    window.location.href = redirectUrl;
                }
            };
        }

        // Ambil durasi pencarian dari input customer (default 60 menit)
        const durasiMenit = parseInt(order.durasi_menit) || 60;
        const durasiCari = order.durasi_cari || (durasiMenit + ' Menit');
        let totalSeconds = durasiMenit * 60;
        const maxSeconds = Math.max(1, durasiMenit * 60);

        const labelEl = document.getElementById('radarCountdownLabel');
        if (labelEl) labelEl.innerText = durasiCari;

        const descDurationEl = document.getElementById('radarCountdownDescDuration');
        if (descDurationEl) descDurationEl.innerText = durasiCari;

        const timerEl = document.getElementById('radarCountdownTimer');
        const barEl = document.getElementById('radarCountdownBar');

        if (pubRadarInterval) clearInterval(pubRadarInterval);
        pubRadarInterval = setInterval(() => {
            totalSeconds--;
            if (totalSeconds < 0) {
                totalSeconds = 0;
                clearInterval(pubRadarInterval);
            }
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            const strMin = mins < 10 ? '0' + mins : mins;
            const strSec = secs < 10 ? '0' + secs : secs;
            if (timerEl) timerEl.innerText = `${strMin}:${strSec}`;
            if (barEl) {
                const pct = Math.max(0, (totalSeconds / maxSeconds) * 100);
                barEl.style.width = pct + '%';
            }
        }, 1000);

        const tickerMessages = [
            'Menyiarkan suruhan ke mitra aktif dalam radius 5 km...',
            'Mitra terdekat sedang memeriksa penugasan Anda...',
            'Menunggu konfirmasi penerimaan dari mitra...',
            `Batas waktu pencarian ${durasiCari} sesuai pilihan Anda...`
        ];
        let tickerIdx = 0;
        const tickerEl = document.getElementById('sbRadarTicker');
        if (pubTickerInterval) clearInterval(pubTickerInterval);
        pubTickerInterval = setInterval(() => {
            tickerIdx = (tickerIdx + 1) % tickerMessages.length;
            if (tickerEl) {
                tickerEl.style.opacity = '0';
                setTimeout(() => {
                    tickerEl.innerText = tickerMessages[tickerIdx];
                    tickerEl.style.opacity = '1';
                }, 200);
            }
        }, 3200);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('sb-scroll-locked');
    }
    window.openGojekSearchModal = openGojekSearchModal;

    function closeGojekSearchModal() {
        const modal = document.getElementById('sbGojekSearchModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.body.classList.remove('sb-scroll-locked');
        if (pubRadarInterval) clearInterval(pubRadarInterval);
        if (pubTickerInterval) clearInterval(pubTickerInterval);
    }
    window.closeGojekSearchModal = closeGojekSearchModal;

    // Sinkronisasi status autentikasi form publik
    function updatePublicFormAuthState() {
        const loggedIn = isCustomerLoggedIn();
        const btnNext2 = document.getElementById('btnPubNextStep2');
        const noteNext2 = document.getElementById('pubNext2Notice');
        const btnNext3 = document.getElementById('btnPubNextStep3');
        const btnSubmit = document.getElementById('btnSubmitSuruhan');
        const submitNotice = document.getElementById('pubSubmitNotice');
        const btnStep2 = document.getElementById('btnPubStep2');
        const btnStep3 = document.getElementById('btnPubStep3');

        if (loggedIn) {
            if (btnNext2) {
                btnNext2.disabled = false;
                btnNext2.className = 'w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer';
                btnNext2.innerHTML = '<span>Lanjut: Tentukan Biaya Layanan</span><i class="fa-solid fa-arrow-right text-[10px]"></i>';
                btnNext2.onclick = function() { nextPubStep(2); };
                btnNext2.removeAttribute('title');
            }
            if (noteNext2) noteNext2.classList.add('hidden');

            if (btnNext3) {
                btnNext3.disabled = false;
                btnNext3.className = 'py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer';
                btnNext3.innerHTML = '<span>Lanjut: Periksa Data</span><i class="fa-solid fa-arrow-right text-[10px]"></i>';
                btnNext3.onclick = function() { nextPubStep(3); };
            }

            if (btnStep2) {
                btnStep2.classList.remove('opacity-60', 'cursor-not-allowed', 'text-slate-400');
                btnStep2.classList.add('hover:bg-slate-200', 'text-slate-500');
            }
            if (btnStep3) {
                btnStep3.classList.remove('opacity-60', 'cursor-not-allowed', 'text-slate-400');
                btnStep3.classList.add('hover:bg-slate-200', 'text-slate-500');
            }
            if (submitNotice) submitNotice.classList.add('hidden');
        } else {
            if (btnNext2) {
                btnNext2.disabled = true;
                btnNext2.className = 'w-full py-2.5 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-2 pointer-events-none select-none transition-all';
                btnNext2.innerHTML = '<span>Lanjut: Menentukan Metode Harga</span><i class="fa-solid fa-lock text-[10px]"></i>';
                btnNext2.onclick = null;
                btnNext2.title = 'Masuk akun Customer untuk melanjutkan pengajuan suruhan';
            }
            if (noteNext2) noteNext2.classList.remove('hidden');

            if (btnNext3) {
                btnNext3.disabled = true;
                btnNext3.className = 'py-2.5 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-1.5 pointer-events-none select-none transition-all';
                btnNext3.innerHTML = '<span>Lanjut: Periksa Data</span><i class="fa-solid fa-lock text-[10px]"></i>';
                btnNext3.onclick = null;
            }

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.className = 'col-span-8 py-3 rounded-xl bg-slate-200 text-slate-400 font-extrabold text-xs shadow-none cursor-not-allowed flex items-center justify-center gap-2 pointer-events-none select-none transition-all';
            }

            if (btnStep2) {
                btnStep2.classList.add('opacity-60', 'cursor-not-allowed', 'text-slate-400');
                btnStep2.classList.remove('hover:bg-slate-200');
            }
            if (btnStep3) {
                btnStep3.classList.add('opacity-60', 'cursor-not-allowed', 'text-slate-400');
                btnStep3.classList.remove('hover:bg-slate-200');
            }
            if (submitNotice) submitNotice.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePublicFormAuthState();
    });
</script>
@endpush

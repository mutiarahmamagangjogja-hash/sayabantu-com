@extends('layouts.customer')

@section('title', 'Dashboard Customer - SayaBantu.com')

{{-- =========================================================
     TOP AREA CUSTOMER (GRADIENT BLUR DUAL-LAYER)
========================================================= --}}
@section('page-top')

<script>
    (function() {
        try {
            localStorage.removeItem('sb_customer_logged_out');
            const raw = localStorage.getItem('sb_customer_auth');
            let u = raw ? JSON.parse(raw) : null;
            if (!u || !u.isLoggedIn || u.role !== 'customer') {
                const sName = @json($customerName !== 'Customer' ? $customerName : null);
                const sPhone = @json($customerPhone ?: null);
                if (sName) {
                    u = {
                        isLoggedIn: true,
                        name: sName,
                        phone: sPhone || '',
                        role: 'customer'
                    };
                    localStorage.setItem('sb_customer_auth', JSON.stringify(u));
                }
            }
        } catch (e) {}
    })();
</script>

<div class="customer-top-area">
    
    {{-- DECORATION CIRCLES --}}
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- TOP NAVBAR CUSTOMER --}}
    <header class="relative z-10 px-5 pt-4">
        <div class="flex items-center justify-between w-full">
            
            {{-- SISI KIRI: BURGER MENU + BRAND --}}
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    onclick="openCustomerDrawer()" 
                    class="header-icon shrink-0" 
                    aria-label="Menu"
                    title="Menu Navigasi"
                >
                    <i class="fa-solid fa-bars text-[16px]"></i>
                </button>

                <div class="flex items-center gap-1.5">
                    <h1 class="text-[17px] font-bold tracking-tight text-white leading-none">
                        SayaBantu<span class="text-white">.com</span>
                    </h1>
                    <span class="text-[8.5px] font-black uppercase px-1.5 py-0.5 rounded bg-sky-500/25 text-sky-200 border border-sky-400/30">
                        Customer
                    </span>
                </div>
            </div>

            {{-- SISI KANAN: SEARCH -> NOTIFIKASI -> PROFILE --}}
            <div class="flex items-center gap-1.5">

                {{-- 1. IKON SEARCH (hanya tampil di tab Dashboard/Layanan) --}}
                <button
                    type="button"
                    id="custHeaderSearchBtn"
                    onclick="toggleCustDashboardSearch()"
                    class="header-icon shrink-0"
                    aria-label="Pencarian"
                    title="Cari Layanan"
                >
                    <i class="fa-solid fa-magnifying-glass text-[14px]"></i>
                </button>

                {{-- 2. IKON NOTIFIKASI SESUDAH SEARCH --}}
                <button
                    type="button"
                    onclick="openModal('modalNotifCustomer')"
                    class="header-icon shrink-0 relative"
                    aria-label="Notifikasi"
                    title="Pemberitahuan"
                >
                    <i class="fa-regular fa-bell text-[14px]"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-[#0E1D31]"></span>
                </button>

                {{-- 3. TOMBOL PROFIL CUSTOMER (BUKA POPUP PROFIL SINGKAT) --}}
                <button
                    type="button"
                    onclick="openCustomerQuickProfile()"
                    class="header-icon shrink-0 !p-0.5 overflow-hidden ring-1 ring-white/30"
                    aria-label="Profil Akun"
                    title="Profil Saya"
                >
                    <div class="w-full h-full rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-bold text-xs flex items-center justify-center" id="custHeaderAvatar">
                        {{ strtoupper(substr(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'C'), 0, 1)) }}
                    </div>
                </button>

            </div>

        </div>

        {{-- SEARCH DROPDOWN DI TOPBAR --}}
        <div id="custSearchDropdown" class="hidden mt-3 relative z-20">
            <div class="bg-white rounded-2xl p-2.5 shadow-xl border border-slate-200 space-y-2">
                <div class="relative">
                    <input
                        type="text"
                        id="custDashboardSearchInput"
                        oninput="filterLiveCustomerServices(this.value)"
                        placeholder="Cari layanan misal: Bersih Rumah, Pindahan..."
                        class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <div id="custSearchResultsBox" class="hidden max-h-48 overflow-y-auto space-y-1 text-xs divide-y divide-slate-100">
                    {{-- Hasil pencarian live --}}
                </div>
            </div>
        </div>

    </header>

    @php
        $tabHeaderConfig = [
            'layanan' => ['title' => 'Layanan & Jasa', 'desc' => 'Pilih layanan untuk langsung mengisi formulir pesanan.'],
            'pesanan' => ['title' => 'Pesanan Saya', 'desc' => 'Pantau status dan riwayat seluruh suruhan Anda.'],
            'pesan' => ['title' => 'Pesan & Chat', 'desc' => 'Hubungi mitra dan tim CS SayaBantu.'],
            'profile' => ['title' => 'Profile', 'desc' => 'Kelola data akun, alamat, dan pengaturan Anda.'],
        ];
        $currentHeader = $tabHeaderConfig[$activeTab ?? 'dashboard'] ?? null;
    @endphp

    {{-- GREETING BANNER CUSTOMER (HANYA TAMPIL DI TAB DASHBOARD) --}}
    <section id="custGreetingBanner" class="relative z-10 px-5 pt-3 pb-2 space-y-2.5 {{ ($activeTab ?? 'dashboard') === 'dashboard' ? '' : 'hidden' }}">
        <div class="space-y-1">
            <h2 class="text-[19px] font-bold tracking-tight text-white leading-snug">
                Halo, <span id="custGreetingName" class="text-sky-300">{{ ($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer') }}</span>! 👋
            </h2>

            <p class="text-[11px] text-white/70 leading-relaxed">
                Prioritas penugasan mitra tercepat & layanan suruhan apa aja siap melayani Anda.
            </p>
        </div>
    </section>

    {{-- PAGE HEADER DINAMIS PER TAB (JUDUL + DESKRIPSI HALAMAN) --}}
    <section id="custTabPageHeader" class="relative z-10 px-5 pt-6 pb-3 {{ $currentHeader ? '' : 'hidden' }}">
        <h1 id="custTabHeaderTitle" class="text-[20px] font-bold tracking-tight text-white leading-tight">
            {{ $currentHeader['title'] ?? 'Layanan & Jasa' }}
        </h1>
        <p id="custTabHeaderDesc" class="mt-1 text-[10px] text-white/70 leading-relaxed">
            {{ $currentHeader['desc'] ?? 'Pilih layanan untuk langsung mengisi formulir pesanan.' }}
        </p>
    </section>

</div>

@endsection


{{-- =========================================================
     CONTENT AREA (4 TAB LENGKAP)
========================================================= --}}
@section('content')

<main class="content-under-gradient px-4 pt-0 space-y-3.5" data-extra-pull="0">

    {{-- =====================================================
         TAB 1: DASHBOARD CUSTOMER
         Sesuai permintaan:
         - Rekomendasi Layanan
         - Pesanan Aktif
         - Pesanan Terakhir
         - Review atau Penilaian
         - TIDAK ADA SALDO
    ====================================================== --}}
    <div id="viewCustDashboard" class="{{ ($activeTab ?? 'dashboard') === 'dashboard' ? '' : 'hidden' }} space-y-3.5">

        @if($reviewableOrder)
            <section class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 shadow-xs">
                <div class="flex items-start gap-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs font-bold text-amber-950">
                            {{ $reviewableOrder->status === 'selesai' ? 'Bagaimana pengalaman Anda?' : 'Konfirmasi pekerjaan dan beri ulasan' }}
                        </h3>
                        <p class="text-[10px] text-amber-900 mt-1">
                            {{ $reviewableOrder->code }} · {{ $reviewableOrder->layanan }}
                            @if($reviewableOrder->status === 'menunggu_konfirmasi_admin_selesai')
                                — ulasan dapat diberikan setelah Anda mengonfirmasi pekerjaan selesai.
                            @else
                                — berikan rating dan ulasan untuk {{ $reviewableOrder->mitra_name ?: 'Mitra' }}.
                            @endif
                        </p>
                        <a href="{{ route('customer.pesanan.detail', ltrim($reviewableOrder->code, '#')) }}" class="inline-flex mt-2 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[9.5px] font-bold">
                            {{ $reviewableOrder->status === 'selesai' ? 'Beri Ulasan' : 'Buka Konfirmasi Pekerjaan' }}
                        </a>
                    </div>
                </div>
            </section>
        @endif

        {{-- 1. PESANAN AKTIF --}}
        @php
            $hasActiveOrder = !empty($serverActiveOrder) && !in_array($serverActiveOrder->status, ['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin', 'semua_ditolak', 'selesai']);
        @endphp
        <section id="sectionPesananAktif" class="card-sb p-3.5 bg-gradient-to-br from-slate-50 to-emerald-50/50 border-l-4 border-l-emerald-500 border-slate-200 shadow-xs space-y-3" data-ada-pesanan="{{ $hasActiveOrder ? 'true' : 'false' }}">
            <div id="kartuPesananAktif" class="space-y-3 {{ $hasActiveOrder ? '' : 'hidden' }}" style="cursor: pointer;" onclick="openActiveOrderDetail()" title="Buka detail pesanan aktif">
                <div class="flex items-center justify-between">
                    <span id="custActiveOrderBadge" class="text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="custActiveOrderBadgeText">1 Pesanan Sedang Berjalan</span>
                    </span>
                    <span id="custActiveOrderCode" class="text-[10px] font-bold text-slate-800 font-mono">{{ $serverActiveOrder?->code ?? '' }}</span>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h4 id="custActiveOrderTitle" class="text-xs font-bold text-slate-900">{{ $serverActiveOrder?->layanan ?? '' }}</h4>
                        <strong id="custActiveOrderPrice" class="text-xs font-black text-slate-900">Rp {{ $serverActiveOrder?->agreed_price ?: ($serverActiveOrder?->biaya ?: '0') }}</strong>
                    </div>
                    <p class="text-[10px] text-slate-600 mt-0.5">
                        Mitra: <strong id="custActiveOrderMitra" class="text-slate-800">{{ $serverActiveOrder?->mitra_name ?: 'Mitra SayaBantu' }}</strong>
                    </p>
                    <span class="text-[9.5px] text-slate-500 block mt-0.5">
                        <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> <span id="custActiveOrderAddress">{{ $serverActiveOrder?->alamat ?: '-' }}</span>
                    </span>
                    {{-- JADWAL AKTIF: kapan & jam berapa mitra datang --}}
                    <span id="custActiveOrderJadwal" class="{{ $serverActiveOrder?->jadwal ? 'flex' : 'hidden' }} items-center gap-1.5 mt-2 px-2.5 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-800">
                        <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                        <span>Mitra datang: <strong id="custActiveOrderJadwalTeks">{{ $serverActiveOrder?->jadwal ?: '-' }}</strong></span>
                    </span>
                </div>

                {{-- PROGRESS STEPPER PESANAN AKTIF (5 TAHAP TERPISAH) --}}
                <div id="custActiveOrderStepper" class="py-1">
                    <div class="flex items-center justify-between text-[7.5px] font-bold text-slate-500 relative">
                        <div class="absolute left-2 right-2 top-2 h-0.5 bg-slate-200 -z-0"></div>
                        <div id="custActiveStepperBar" class="absolute left-2 w-full top-2 h-0.5 bg-emerald-500 -z-0 transition-all duration-300"></div>

                        <div class="flex flex-col items-center gap-1 z-10">
                            <span id="stepDot1" class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs">✓</span>
                            <span id="stepLabel1" class="text-emerald-700">Dibuat</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <span id="stepDot2" class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs">✓</span>
                            <span id="stepLabel2" class="text-emerald-700">Nego</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <span id="stepDot3" class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs">✓</span>
                            <span id="stepLabel3" class="text-emerald-700">Bayar</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <span id="stepDot4" class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs">✓</span>
                            <span id="stepLabel4" class="text-emerald-700">Jadwal</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 z-10">
                            <span id="stepDot5" class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs animate-pulse">5</span>
                            <span id="stepLabel5" class="text-emerald-700 font-extrabold">Bekerja</span>
                        </div>
                    </div>
                </div>

                <div id="custActiveOrderActions" class="flex items-center gap-2 pt-1 border-t border-slate-200/60">
                    <button type="button" id="custActiveOrderBtnMain" onclick="event.stopPropagation(); handleCustActiveOrderMainAction()" class="flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                        <i id="custActiveOrderBtnMainIcon" class="fa-solid fa-comments text-xs"></i>
                        <span id="custActiveOrderBtnMainText">Chat Mitra</span>
                    </button>
                    <button type="button" id="custActiveOrderBtnDetail" onclick="event.stopPropagation(); handleCustActiveOrderDetailAction()" class="py-1.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10.5px] font-bold transition-colors">
                        Detail / Lacak
                    </button>
                </div>
            </div>{{-- /#kartuPesananAktif --}}

            {{-- EMPTY STATE: TIDAK ADA PESANAN BERJALAN --}}
            <div id="kartuPesananKosong" class="space-y-3 {{ $hasActiveOrder ? 'hidden' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base shrink-0">
                        <i class="fa-regular fa-calendar-check"></i>
                    </span>
                    <div class="min-w-0">
                        <h4 class="text-xs font-bold text-slate-900">Belum Ada Pesanan Berjalan</h4>
                        <p class="text-[10px] text-slate-500 leading-snug mt-0.5">
                            Yuk buat suruhan pertama Anda. Sistem akan otomatis menugaskan mitra terdekat.
                        </p>
                    </div>
                </div>
                <a href="{{ route('customer.suruhan.create') }}" class="w-full py-2 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                    <i class="fa-solid fa-plus"></i> Buat Suruhan Sekarang
                </a>
            </div>{{-- /#kartuPesananKosong --}}
        </section>

        {{-- 2. REKOMENDASI LAYANAN --}}
        <section class="space-y-2">
            <div class="flex items-center justify-between rounded-2xl bg-white/85 backdrop-blur-md px-3 py-2 shadow-xs border border-white/60">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-fire text-orange-500"></i> Layanan Populer
                    </h3>
                    <p class="text-[9.5px] text-slate-500">Paling banyak dipesan &amp; mitra selalu siaga di area Anda</p>
                </div>
                <button type="button" onclick="switchCustomerTab('layanan')" class="text-[10px] font-bold text-sky-600 hover:underline">
                    Lihat Semua »
                </button>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                {{-- Rekomendasi 1 --}}
                <div class="card-sb p-2.5 space-y-1.5 border border-slate-200 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openModernServiceDetail('Full Home Cleaning')">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-broom"></i>
                        </span>
                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                            Paling Laris
                        </span>
                    </div>
                    <div>
                        <strong class="text-[11px] font-bold text-slate-900 block truncate">Full Home Cleaning</strong>
                        <p class="text-[9px] text-slate-500 line-clamp-1">Sapu, pel, bereskan kamar & debu</p>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[9.5px] font-bold text-emerald-700 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[8px]"></i> Mitra Siaga</span>
                        <button type="button" onclick="event.stopPropagation(); pesanCepat('Full Home Cleaning', '')" class="px-2.5 py-1 rounded-lg bg-[#0E1D31] hover:bg-[#173B67] text-white text-[9px] font-bold transition-colors cursor-pointer">
                            Pesan Cepat
                        </button>
                    </div>
                </div>

                {{-- Rekomendasi 2 --}}
                <div class="card-sb p-2.5 space-y-1.5 border border-slate-200 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openModernServiceDetail('Bersihkan Kamar Mandi')">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-shower"></i>
                        </span>
                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-teal-100 text-teal-800">
                            Bersih Kilat
                        </span>
                    </div>
                    <div>
                        <strong class="text-[11px] font-bold text-slate-900 block truncate">Kamar Mandi Kinclong</strong>
                        <p class="text-[9px] text-slate-500 line-clamp-1">Kuras bak, kerak lantai & kloset</p>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[9.5px] font-bold text-emerald-700 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[8px]"></i> Mitra Siaga</span>
                        <button type="button" onclick="event.stopPropagation(); pesanCepat('Bersihkan Kamar Mandi', '')" class="px-2.5 py-1 rounded-lg bg-[#0E1D31] hover:bg-[#173B67] text-white text-[9px] font-bold transition-colors cursor-pointer">
                            Pesan Cepat
                        </button>
                    </div>
                </div>

                {{-- Rekomendasi 3 --}}
                <div class="card-sb p-2.5 space-y-1.5 border border-slate-200 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openModernServiceDetail('Pindahan')">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </span>
                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-800">
                            Mitra Kuat
                        </span>
                    </div>
                    <div>
                        <strong class="text-[11px] font-bold text-slate-900 block truncate">Pindahan Kos &amp; Rumah</strong>
                            <p class="text-[9px] text-slate-500 line-clamp-1">Bantu packing dan pindahkan barang</p>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[9.5px] font-bold text-emerald-700 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[8px]"></i> Mitra Siaga</span>
                        <button type="button" onclick="event.stopPropagation(); pesanCepat('Pindahan Kos & Rumah', '')" class="px-2.5 py-1 rounded-lg bg-[#0E1D31] hover:bg-[#173B67] text-white text-[9px] font-bold transition-colors cursor-pointer">
                            Pesan Cepat
                        </button>
                    </div>
                </div>

                {{-- Rekomendasi 4 --}}
                <div class="card-sb p-2.5 space-y-1.5 border border-slate-200 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openModernServiceDetail('Antar / Jemput')">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-motorcycle"></i>
                        </span>
                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-sky-100 text-sky-800">
                            Antar Barang
                        </span>
                    </div>
                    <div>
                        <strong class="text-[11px] font-bold text-slate-900 block truncate">Antar / Jemput</strong>
                        <p class="text-[9px] text-slate-500 line-clamp-1">Antar barang dan dokumen</p>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[9.5px] font-bold text-emerald-700 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[8px]"></i> Mitra Siaga</span>
                        <button type="button" onclick="event.stopPropagation(); pesanCepat('Antar / Jemput', '')" class="px-2.5 py-1 rounded-lg bg-[#0E1D31] hover:bg-[#173B67] text-white text-[9px] font-bold transition-colors cursor-pointer">
                            Pesan Cepat
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- =========================================================
             FORM BUAT SURUHAN BARU (IDENTIK DENGAN BERANDA PUBLIK)
             Sudah login sebagai customer, jadi form langsung siap pakai.
        ========================================================= --}}
        @include('customer.partials.order-form')
        @include('customer.partials.gojek-radar-modal')

        {{-- 4. REVIEW ATAU PENILAIAN (DIPINDAHKAN KE PALING BAWAH) --}}
        <section class="space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-yellow-400"></i> Review &amp; Penilaian Layanan
                    </h3>
                    <p class="text-[9.5px] text-slate-500">Penilaian nyata dari pelanggan SayaBantu terverifikasi</p>
                </div>
                @if($reviewableOrder)
                    <a href="{{ route('customer.pesanan.detail', ltrim($reviewableOrder->code, '#')) }}" class="px-2.5 py-1 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold text-[9.5px] flex items-center gap-1 shadow-xs transition-transform active:scale-95">
                        <i class="fa-solid fa-pen-to-square"></i> Beri Ulasan
                    </a>
                @endif
            </div>

            {{-- SUMMARY RATING CARD --}}
            <div class="card-sb p-3 bg-gradient-to-br from-white to-amber-50/30 border border-amber-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-center px-2">
                        @if($customerReviewCount > 0 && $customerAverageRating !== null)
                            <span class="text-2xl font-black text-slate-900 block leading-none">{{ number_format((float) $customerAverageRating, 1) }}</span>
                            <div class="text-yellow-400 text-xs mt-0.5">★</div>
                        @else
                            <span class="text-xs font-black text-slate-900 block">Belum ada</span>
                            <div class="text-slate-400 text-[9px] mt-0.5">ulasan</div>
                        @endif
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div class="text-[10px] space-y-0.5">
                        <strong class="font-bold text-slate-800 block">Penilaian dari pekerjaan selesai</strong>
                        <p class="text-slate-500 text-[9px]">{{ number_format($customerReviewCount) }} ulasan tercatat</p>
                    </div>
                </div>
            </div>

            {{-- FEED REVIEW --}}
            <div class="space-y-2" id="customerReviewList">
                @forelse($latestReviews as $review)
                    <article class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs space-y-1">
                        <div class="flex items-center justify-between gap-2">
                            <strong class="text-[10px] text-slate-800">{{ $review->customer_name ?: 'Customer' }}</strong>
                            <span class="text-yellow-400 text-[10px]">{{ str_repeat('★', (int) $review->review_rating) }}</span>
                        </div>
                        <p class="text-[10px] text-slate-600 leading-snug">{{ $review->review_comment ?: 'Tidak ada komentar tertulis.' }}</p>
                        <span class="text-[8.5px] text-slate-400">{{ $review->layanan ?: 'Layanan' }} · {{ $review->reviewed_at?->format('d M Y') ?: 'Tanggal tidak tersedia' }}</span>
                    </article>
                @empty
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-1">
                        <i class="fa-regular fa-comments text-slate-300 text-xl block mx-auto mb-1"></i>
                        <p class="text-[10px] font-bold text-slate-700">Belum Ada Ulasan</p>
                        <p class="text-[9px] text-slate-400">Ulasan pekerjaan selesai akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    {{-- =====================================================
         TAB 2: KATALOG LAYANAN LENGKAP CUSTOMER
         (TAMPILAN IDENTIK DENGAN HALAMAN PENGUNJUNG PUBLIK)
    ====================================================== --}}
    <div id="viewCustLayanan" class="{{ ($activeTab ?? 'dashboard') === 'layanan' ? '' : 'hidden' }} space-y-3.5">

        @php
            $services = [
                ['cat' => 'bersih', 'name' => 'Full Home Cleaning', 'label' => 'Full Home Cleaning', 'icon' => 'fa-broom', 'color' => 'emerald', 'budget' => '85.000',
                 'desc' => 'Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.'],
                ['cat' => 'bersih', 'name' => 'Basic Daily Cleaning', 'label' => 'Basic Daily Cleaning', 'icon' => 'fa-spray-can-sparkles', 'color' => 'emerald', 'budget' => '50.000',
                 'desc' => 'Sapu, pel lantai rutin, dan lap meja/kaca.'],
                ['cat' => 'bersih', 'name' => 'Bersihkan Kamar Mandi', 'label' => 'Bersihkan Kamar Mandi', 'icon' => 'fa-shower', 'color' => 'teal', 'budget' => '65.000',
                 'desc' => 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.'],
                ['cat' => 'bersih', 'name' => 'Cuci Piring & Dapur Bersih', 'label' => 'Cuci Piring & Dapur', 'icon' => 'fa-sink', 'color' => 'cyan', 'budget' => '45.000',
                 'desc' => 'Bantu cuci piring tumpuk, bersihkan kompor & wastafel.'],
                ['cat' => 'angkut', 'name' => 'Pindahan', 'label' => 'Pindahan Kos & Rumah', 'icon' => 'fa-boxes-packing', 'color' => 'blue', 'budget' => '150.000',
                 'desc' => 'Bantu packing, angkat barang pindahan kos/rumah ke kendaraan.'],
                ['cat' => 'angkut', 'name' => 'Angkut Barang', 'label' => 'Angkut Barang Rumah Tangga', 'icon' => 'fa-dolly', 'color' => 'blue', 'budget' => '80.000',
                 'desc' => 'Bantu angkat dan memindahkan barang rumah tangga.'],
                ['cat' => 'angkut', 'name' => 'Buang Sampah', 'label' => 'Buang Sampah Rumah Tangga', 'icon' => 'fa-trash-can', 'color' => 'slate', 'budget' => '40.000',
                 'desc' => 'Bantu membuang sampah rumah tangga ke tempat pembuangan.'],
                ['cat' => 'antar', 'name' => 'Antar / Jemput', 'label' => 'Antar / Jemput Barang', 'icon' => 'fa-motorcycle', 'color' => 'sky', 'budget' => '50.000',
                 'desc' => 'Antar barang, dokumen penting, atau ambil laundry kilat.'],
                ['cat' => 'antar', 'name' => 'SanSu Food', 'label' => 'Belanja Pasar Subuh', 'icon' => 'fa-basket-shopping', 'color' => 'amber', 'budget' => '45.000',
                 'desc' => 'Belanja sayur ke pasar tradisional subuh atau supermarket.'],
                ['cat' => 'antar', 'name' => 'Titip Antre RS & Faskes', 'label' => 'Titip Antre RS & Faskes', 'icon' => 'fa-hourglass-half', 'color' => 'purple', 'budget' => '60.000',
                 'desc' => 'Ambilkan nomor antrean dokter, faskes, atau antre tiket event.'],
                ['cat' => 'khusus', 'name' => 'Nemenin Olahraga', 'label' => 'Nemenin Olahraga', 'icon' => 'fa-person-running', 'color' => 'orange', 'budget' => '60.000',
                 'desc' => 'Temani jogging pagi, gym, bersepeda atau jaga stand toko.'],
                ['cat' => 'khusus', 'name' => 'Bantu Kegiatan Harian', 'label' => 'Bantu Kegiatan Harian', 'icon' => 'fa-handshake', 'color' => 'navy', 'budget' => '60.000',
                 'desc' => 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.'],
            ];

            $catLabels = [
                'all' => 'Semua',
                'bersih' => 'Kebersihan',
                'angkut' => 'Pindah & Angkut',
                'antar' => 'Antar & Belanja',
                'khusus' => 'Kegiatan Harian',
            ];

            $colorMap = [
                'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'teal' => 'bg-teal-50 text-teal-600 border-teal-100',
                'cyan' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
                'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
                'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
                'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
                'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
                'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                'pink' => 'bg-pink-50 text-pink-600 border-pink-100',
                'stone' => 'bg-stone-100 text-stone-700 border-stone-200',
                'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
                'navy' => 'bg-slate-900 text-sky-400 border-slate-700',
            ];
        @endphp

        {{-- KATALOG LAYANAN CARD --}}
        <section id="layananKatalogSection" class="card-sb p-3.5 space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h3 class="text-xs font-bold text-[#0E1D31] flex items-center gap-1.5">
                        <i class="fa-solid fa-list-check text-[#173B67]"></i> Katalog Layanan &amp; Jasa
                    </h3>
                    <p class="text-[9px] text-slate-400">Seluruh layanan suruhan SayaBantu</p>
                </div>
                <button type="button" onclick="filterCustLayananPageByCat('all')" id="jumlahLayananCust" class="text-[9px] text-[#173B67] font-bold bg-blue-50 hover:bg-blue-100 transition-colors px-2.5 py-0.5 rounded-full border border-blue-100 cursor-pointer focus:outline-none" title="Lihat Semua Kategori">
                    16 Kategori
                </button>
            </div>

            {{-- KATEGORI CHIP TAB LAYANAN --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
                <button type="button" onclick="filterCustLayananPageByCat('all', this)" class="btn-cust-cat-filter px-2.5 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 transition-all">
                    Semua
                </button>
                @foreach($catLabels as $key => $label)
                    @if($key !== 'all')
                        <button type="button" onclick="filterCustLayananPageByCat('{{ $key }}', this)" class="btn-cust-cat-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                            {{ $label }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- DAFTAR LENGKAP 16 LAYANAN (DENGAN TAMPILAN PERSIS HALAMAN PUBLIK) --}}
            <div class="space-y-2.5" id="layananCustGrid">
                @foreach($services as $svc)
                    <div
                        class="layanan-cust-item rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden transition-all"
                        data-cat="{{ $svc['cat'] }}"
                        data-budget="{{ $svc['budget'] }}"
                        data-name="{{ $svc['name'] }}"
                        data-search="{{ strtolower($svc['label'] . ' ' . $svc['name'] . ' ' . $svc['desc']) }}"
                    >
                        {{-- BARIS RINGKAS: diklik untuk toggle detail & harga --}}
                        <button
                            type="button"
                            onclick="toggleCustServiceDetail(this)"
                            class="w-full text-left p-3 flex items-start gap-3 hover:bg-white active:scale-[0.99] transition-all"
                            aria-expanded="false"
                        >
                            <div class="w-11 h-11 shrink-0 rounded-xl border flex items-center justify-center text-base {{ $colorMap[$svc['color']] }}">
                                <i class="fa-solid {{ $svc['icon'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $svc['label'] }}</h4>
                                    <span class="text-[9.5px] font-bold text-[#173B67] bg-blue-50 px-2 py-0.5 rounded-full shrink-0 border border-blue-100">Rp {{ $svc['budget'] }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 leading-snug mt-0.5 line-clamp-2">{{ $svc['desc'] }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 self-center shrink-0 transition-transform duration-200 service-chevron"></i>
                        </button>

                        {{-- DETAIL + HARGA (TERBUKA KETIKA DIKLIK) --}}
                        <div class="service-detail hidden px-3 pb-3 space-y-2.5">
                            <div class="p-3 rounded-xl bg-white border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9.5px] font-bold text-slate-500 uppercase tracking-wide">Estimasi Tarif</span>
                                    <span class="text-sm font-extrabold text-[#173B67]">Rp {{ $svc['budget'] }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-[9.5px] text-slate-500">
                                    <span><i class="fa-solid fa-shield-check text-emerald-600"></i> Mitra terverifikasi</span>
                                    <span><i class="fa-solid fa-clock text-sky-600"></i> Siap 24 jam</span>
                                </div>
                            </div>

                            <div class="text-[10px] leading-relaxed text-slate-600">
                                <strong class="text-slate-800">Detail:</strong> {{ $svc['desc'] }}
                            </div>

                            <button
                                type="button"
                                onclick='pesanCepat(@json($svc["name"]), @json($svc["budget"]))'
                                class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-[11px] font-bold transition-colors shadow-xs"
                            >
                                <i class="fa-solid fa-paper-plane mr-1"></i> Pesan Layanan Ini
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- EMPTY STATE --}}
            <div id="layananCustKosong" class="hidden text-center py-8">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg mx-auto mb-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <p class="text-xs font-bold text-slate-700">Layanan tidak ditemukan</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Coba kata kunci lain, atau hubungi CS kami.</p>
            </div>
        </section>

        {{-- INFO PENUGASAN OTOMATIS --}}
        <section class="card-sb p-3.5">
            <div class="p-2.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-start gap-2 text-[10.5px]">
                <i class="fa-solid fa-wand-magic-sparkles text-sky-600 mt-0.5 shrink-0"></i>
                <p class="text-slate-700 leading-snug">
                    <strong>Penugasan Otomatis:</strong> Anda tidak perlu memilih mitra. Setelah mengajukan layanan, sistem cerdas kami akan otomatis mencocokkan mitra terdekat dengan rating terbaik.
                </p>
            </div>
        </section>

    </div>

    {{-- =====================================================
         TAB 3: RIWAYAT & PELACAKAN PESANAN CUSTOMER
    ====================================================== --}}
    <div id="viewCustPesanan" class="{{ ($activeTab ?? 'dashboard') === 'pesanan' ? '' : 'hidden' }} space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-emerald-600"></i> Riwayat &amp; Pelacakan Pesanan
                </h3>
                <p class="text-[9.5px] text-white">Pantau progres pekerjaan mitra secara live</p>
            </div>
        </div>

        {{-- SEARCH ORDER CODE --}}
        <div class="flex items-center gap-1.5">
            <input type="text" id="custOrderFilterInput" onkeydown="if(event.key==='Enter'){event.preventDefault();lacakTiketCustomer();}" placeholder="Masukkan nomor tiket misal: #SB-SURUH-9012" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#173B67] focus:outline-none">
            <button type="button" onclick="lacakTiketCustomer()" class="px-3.5 py-2 bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5 shrink-0 shadow-xs active:scale-95">
                <i class="fa-solid fa-location-crosshairs text-[10px]"></i>
                <span>Lacak</span>
            </button>
        </div>

        {{-- FILTER STATUS PESANAN --}}
        <div class="flex items-center justify-center gap-1.5 text-[10px]">
            <button type="button" onclick="filterOrderTab('all', this)" id="btnFilterOrderAll" data-status="all" class="btn-order-tab-filter px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white">Semua (0)</button>
            <button type="button" onclick="filterOrderTab('aktif', this)" id="btnFilterOrderAktif" data-status="aktif" class="btn-order-tab-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700">Sedang Aktif (0)</button>
            <button type="button" onclick="filterOrderTab('selesai', this)" id="btnFilterOrderSelesai" data-status="selesai" class="btn-order-tab-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700">Selesai (0)</button>
        </div>

        <div class="space-y-2.5" id="customerOrderListItems">
            <div id="custDynamicOrdersBox" class="space-y-2.5"></div>

            {{-- EMPTY STATE: BENAR-BENAR KOSONG TANPA DATA DUMMY INPUT --}}
            <div id="custOrdersEmptyState" class="card-sb p-6 text-center space-y-3 bg-white border border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto text-xl shadow-xs">
                    <i class="fa-regular fa-clipboard"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="text-xs font-bold text-slate-800">Belum Ada Riwayat Pesanan</h4>
                    <p class="text-[10px] text-slate-400 max-w-xs mx-auto">
                        Suruhan yang Anda buat akan tercatat di sini dan dapat Anda lacak perkembangannya secara langsung.
                    </p>
                </div>
                <a href="{{ route('customer.suruhan.create') }}" class="px-4 py-2 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-[10px] font-bold shadow-xs transition-colors cursor-pointer inline-flex items-center">
                    <i class="fa-solid fa-plus mr-1"></i> Buat Suruhan Sekarang
                </a>
            </div>
        </div>
    </div>

    {{-- =====================================================
         TAB 4: PESAN / CHAT CUSTOMER (KOMUNIKASI DENGAN MITRA)
    ====================================================== --}}
    <div id="viewCustPesan" class="{{ ($activeTab ?? 'dashboard') === 'pesan' ? '' : 'hidden' }} space-y-3.5">

        {{-- ======= CHAT LIST VIEW (Daftar Percakapan) ======= --}}
        <div id="custChatListView">
            <section class="card-sb p-3.5 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-comments text-sky-600"></i>
                            <span>Percakapan Saya</span>
                        </h3>
                        <p class="text-[9px] text-slate-400 mt-0.5">Koordinasi langsung dengan mitra yang mengerjakan suruhan Anda</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2.5 py-1 rounded-full bg-[#0E1D31] text-white border border-white/20 whitespace-nowrap shrink-0">
                        <i class="fa-solid fa-comments"></i> Komunikasi Layanan
                    </span>
                </div>

                <div id="custChatRoomListDynamic" class="space-y-2"></div>

                {{-- EMPTY STATE: BENAR-BENAR KOSONG TANPA PERCAKAPAN PERCONTOHAN --}}
                <div id="custChatEmptyState" class="p-6 text-center space-y-2.5 bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto text-lg shadow-xs">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-xs font-bold text-slate-800">Belum Ada Percakapan</h4>
                        <p class="text-[10px] text-slate-400 max-w-xs mx-auto">
                            Percakapan dengan mitra akan otomatis muncul saat Anda membuat suruhan atau bernegosiasi harga.
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- ======= CHAT DETAIL VIEW (Percakapan Aktif + Input Ketik) ======= --}}
        <div id="custChatDetailView" class="hidden" style="min-height: calc(100vh - 200px);">

            {{-- HEADER CHAT --}}
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                <button type="button" onclick="closeCustChatRoom()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer active:scale-95 transition-all shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="relative shrink-0">
                    <div id="custChatAvatarDetail" class="w-10 h-10 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">BS</div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <strong id="custChatNameDetail" class="text-xs font-extrabold text-slate-900 block truncate">Mitra SayaBantu</strong>
                    <span id="custChatTaskDetail" class="text-[9px] text-sky-600 font-semibold block">Suruhan #SB-8821 • Full Home Cleaning</span>
                </div>
            </div>

            {{-- KARTU KESEPAKATAN HARGA & NEGOSIASI (DEAL PROPOSAL CARD) --}}
            <div id="custChatNegotiationCard" class="hidden my-2.5 p-3 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-sky-300 shadow-xs space-y-2">
                <div class="flex items-center justify-between pb-1.5 border-b border-sky-200/80">
                    <span class="text-[9.5px] font-black uppercase tracking-wider text-[#173B67] flex items-center gap-1.5">
                        <i class="fa-solid fa-handshake"></i> Negosiasi Harga Suruhan
                    </span>
                    <span id="custChatDealStatusBadge" class="text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        Sedang Nego
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="min-w-0 pr-2">
                        <strong id="custChatDealTitle" class="text-[11px] font-extrabold text-slate-900 block truncate">Full Home Cleaning</strong>
                        <span class="text-[9.5px] text-slate-500">Tiket: <span id="custChatDealCode" class="font-mono font-bold text-slate-700">#SB-SURUH-9012</span></span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[8.5px] text-slate-400 block">Nominal Tawaran:</span>
                        <strong id="custChatDealPrice" class="text-sm font-black text-emerald-700">Rp 85.000</strong>
                    </div>
                </div>

                {{-- Action Box 1: Masih Tahap Negosiasi (Mitra yang menyetujui, Customer ganti-ganti harga) --}}
                <div id="custChatDealActionBox" class="pt-2 border-t border-sky-200/60 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="ubahTawaranChatPrompt()" class="w-full py-2 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10.5px] flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Ganti / Tawar Harga Baru</span>
                        </button>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-tight">
                        <i class="fa-solid fa-circle-info text-sky-600 mr-0.5"></i> Customer hanya mengajukan tawaran. <strong>Mitra yang berhak menyetujui</strong> kesepakatan harga sebelum pembayaran dibuka.
                    </p>
                </div>

                {{-- Action Box 2: Kesepakatan Berhasil -> Buka Kunci Bayar --}}
                <div id="custChatDealSuccessBox" class="hidden pt-2 border-t border-emerald-200 space-y-1.5">
                    <div class="flex items-center gap-1.5 text-[9.5px] font-bold text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Disetujui Mitra &amp; Dikonfirmasi Admin! Pembayaran telah dibuka:</span>
                    </div>
                    <a id="custChatBtnBayarSekarang" href="#" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs text-center flex items-center justify-center gap-2 shadow-sm transition-all animate-pulse cursor-pointer">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Lanjut ke Pembayaran (Rp <span id="custChatDealFinalPrice">85.000</span>)</span>
                    </a>
                </div>
            </div>

            {{-- CHAT BUBBLES AREA --}}
            <div id="custChatBubblesContainer" class="py-4 space-y-3 overflow-y-auto" style="max-height: calc(100vh - 340px); min-height: 320px;">
                {{-- Date Separator --}}
                <div class="flex items-center gap-2 px-2">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Hari Ini</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <div id="custChatLoadingIndicator" class="text-center py-6 text-slate-400 text-xs">
                    <i class="fa-solid fa-spinner fa-spin mr-1 text-sky-600"></i> Memuat percakapan...
                </div>
            </div>

            {{-- INPUT AREA: KETIK PESAN --}}
            <div class="sticky bottom-0 bg-[#F7F9FC] pt-2 pb-1 border-t border-slate-200">
                <form onsubmit="sendCustChatMessage(event)" class="flex items-end gap-2">
                    <button type="button" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm cursor-pointer shrink-0 transition-colors">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            id="custChatInputField"
                            placeholder="Ketik pesan..."
                            autocomplete="off"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-white border border-slate-200 focus:border-[#173B67] focus:ring-2 focus:ring-sky-100 text-xs outline-none transition-all pr-10"
                        >
                        <button type="button" onclick="if(typeof showCustomerToast === 'function') showCustomerToast('Emoji', 'Pilih emoji dari keyboard perangkat Anda.', 'info');" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="fa-regular fa-face-smile text-sm"></i>
                        </button>
                    </div>
                    <button
                        type="submit"
                        class="w-9 h-9 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white flex items-center justify-center text-sm cursor-pointer shrink-0 shadow-sm transition-all active:scale-90"
                    >
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- =====================================================
         TAB 5: PROFILE CUSTOMER (TIDAK USAH ADA SALDO!)
    ====================================================== --}}
    <div id="viewCustProfile" class="{{ ($activeTab ?? 'dashboard') === 'profile' ? '' : 'hidden' }} space-y-3.5">

        {{-- 1. INFORMASI PROFIL --}}
        <div class="card-sb p-3.5 space-y-3 border border-slate-200">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Informasi Profil</h3>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-black text-lg flex items-center justify-center shadow-md" id="profileDetailAvatar">
                    {{ strtoupper(substr(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'C'), 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs font-bold text-slate-900 truncate" id="profileDetailName">{{ ($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer') }}</h4>
                    </div>
                    <p class="text-[10px] text-slate-500 truncate" id="profileDetailEmail">{{ Auth::user()?->email ?? (strtolower(str_replace(' ', '', ($customerName && $customerName !== 'Customer' ? $customerName : (Auth::user()?->name ?? 'customer')))) . '@customer.sayabantu.com') }}</p>
                    <p class="text-[10px] text-slate-500" id="profileDetailPhone">{{ $customerPhone ?: (Auth::user()?->phone ?? '') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[9px] text-slate-400 block">Nama Lengkap</span>
                    <strong class="text-[11px] font-bold text-slate-900 block truncate" id="profileFieldName">{{ ($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer') }}</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[9px] text-slate-400 block">Nomor Telepon</span>
                    <strong class="text-[11px] font-bold text-slate-900 block truncate" id="profileFieldPhone">{{ $customerPhone ?: (Auth::user()?->phone ?? '') }}</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 col-span-2">
                    <span class="text-[9px] text-slate-400 block">Email</span>
                    <strong class="text-[11px] font-bold text-slate-900 block truncate" id="profileFieldEmail">{{ Auth::user()?->email ?? (strtolower(str_replace(' ', '', ($customerName && $customerName !== 'Customer' ? $customerName : (Auth::user()?->name ?? 'customer')))) . '@customer.sayabantu.com') }}</strong>
                </div>
            </div>

            <button type="button" onclick="showCustomerToast('Edit Profil', 'Form edit foto, nama, email, dan nomor telepon akan tersedia di sini.', 'info')" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profil
            </button>
        </div>

        {{-- 2. ALAMAT --}}
        <div class="card-sb p-3.5 space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat</h3>
                <button type="button" onclick="showCustomerToast('Tambah Alamat', 'Form tambah alamat baru akan tersedia di sini.', 'info')" class="text-[9.5px] text-sky-600 font-bold hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-plus"></i> Tambah Alamat
                </button>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-start gap-2.5 text-xs">
                    <i class="fa-solid fa-location-dot text-rose-500 mt-1 shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <strong class="text-slate-900 font-bold text-[11px]">Rumah Utama</strong>
                            <span class="text-[8.5px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-sky-700 border border-sky-200">Utama</span>
                        </div>
                        <p class="text-[10px] text-slate-500 leading-snug">Jl. Sudirman No. 12, Kel. Karet Tengsin, Tanah Abang, Jakarta Pusat 10220</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-1 border-t border-slate-200/70">
                    <button type="button" onclick="showCustomerToast('Edit Alamat', 'Form ubah alamat akan tersedia di sini.', 'info')" class="flex-1 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-pen text-[9px]"></i> Edit
                    </button>
                    <button type="button" onclick="showCustomerToast('Hapus Alamat', 'Konfirmasi hapus alamat akan ditampilkan di sini.', 'warning')" class="flex-1 py-1.5 rounded-lg bg-white border border-rose-200 text-[10px] font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                        <i class="fa-solid fa-trash text-[9px]"></i> Hapus
                    </button>
                </div>
            </div>
        </div>

        {{-- 3. KEAMANAN AKUN --}}
        <div class="card-sb p-2 space-y-1">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2.5 pt-1.5 pb-0.5">Keamanan Akun</h3>

            <button type="button" onclick="showCustomerToast('Ubah Password', 'Form ubah password akan tersedia di sini.', 'info')" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-lock text-slate-600 text-sm w-4"></i>
                    <span>Ubah Password</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <button type="button" onclick="showCustomerToast('Ubah Email', 'Form ubah email akan tersedia di sini.', 'info')" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-envelope text-sky-600 text-sm w-4"></i>
                    <span>Ubah Email</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <button type="button" onclick="showCustomerToast('Ubah Nomor Telepon', 'Form ubah nomor telepon akan tersedia di sini.', 'info')" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-mobile-screen-button text-indigo-600 text-sm w-4"></i>
                    <span>Ubah Nomor Telepon</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <div class="flex items-center justify-between p-2.5 rounded-xl text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-sm w-4"></i>
                    <span>Verifikasi Akun</span>
                </span>
                <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">● Terverifikasi</span>
            </div>
        </div>

        {{-- 4. PENGATURAN --}}
        <div class="card-sb p-2 space-y-1">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2.5 pt-1.5 pb-0.5">Pengaturan</h3>

            <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800 text-xs">
                    <span role="img" aria-label="Notifikasi Pesanan">🔔</span>
                    <span>Notifikasi Pesanan</span>
                </span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked onchange="toggleCustSetting('Notifikasi Pesanan', this.checked)" class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173B67]"></div>
                </label>
            </div>

        </div>

        {{-- 5. BANTUAN --}}
        <div class="card-sb p-2 space-y-1">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2.5 pt-1.5 pb-0.5">Butuh Bantuan?</h3>

            <a href="{{ route('customer.pengaduan') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-headset text-sky-600 text-sm w-4"></i>
                    <span>Pusat Bantuan / Layanan Bantuan CS</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>

            <a href="{{ route('customer.cara-kerja') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-xs">
                <span class="flex items-center gap-2.5 font-semibold text-slate-800">
                    <i class="fa-solid fa-circle-question text-sky-600 text-sm w-4"></i>
                    <span>FAQ / Cara Kerja SayaBantu</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>
        </div>

        {{-- 6. KELUAR --}}
        <button type="button" onclick="logoutUserAction()" class="w-full py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center gap-2 transition-colors border border-red-200">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
        </button>
    </div>

</main>

@endsection

@section('modals')
@endsection

@push('scripts')
<script>
    // =========================================================
    //  CUSTOMER DASHBOARD CONTROLLER
    // =========================================================

    // Auth & Init
    document.addEventListener('DOMContentLoaded', function() {
        initCustomerDashboardAuth();

        // Sinkronkan status tombol submit form suruhan dengan checkbox syarat.
        // Mencegah tombol tetap nonaktif bila checkbox syarat sudah tercentang
        // (mis. saat browser memulihkan state form / back-navigation).
        const chkTerms = document.getElementById('custCheckTerms');
        if (chkTerms) {
            toggleCustFormSubmit(chkTerms.checked);
        }

        // Muat pesanan tersimpan customer (kartu beranda & riwayat)
        if (typeof loadSavedCustomerOrders === 'function') {
            loadSavedCustomerOrders();
        } else {
            aturPesananAktifCustomer(false);
        }

        // Cek URL query ?tab= dan ?room=
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        const room = urlParams.get('room');
        const mitraParam = urlParams.get('mitra') || '';
        if (tab && ['dashboard', 'layanan', 'pesanan', 'pesan', 'profile'].includes(tab)) {
            switchCustomerTab(tab);
            if (tab === 'pesan' && room && typeof openCustChatRoom === 'function') {
                setTimeout(() => openCustChatRoom(room, mitraParam), 150);
            }
        } else {
            switchCustomerTab('dashboard');
        }


        // Cek URL query ?layanan= atau dari localStorage (Layanan Favorit)
        const layananParam = urlParams.get('layanan') || localStorage.getItem('selected_service_name');
        if (layananParam) {
            localStorage.removeItem('selected_service_name');
            setTimeout(() => {
                const orderCard = document.getElementById('custOrderFormCard');
                if (orderCard) {
                    orderCard.scrollIntoView({ behavior: 'smooth' });
                }
                const selectLayanan = document.getElementById('custFormLayanan');
                if (selectLayanan) {
                    for (let i = 0; i < selectLayanan.options.length; i++) {
                        if (selectLayanan.options[i].value.toLowerCase().includes(layananParam.toLowerCase()) ||
                            selectLayanan.options[i].text.toLowerCase().includes(layananParam.toLowerCase())) {
                            selectLayanan.selectedIndex = i;
                            break;
                        }
                    }
                }
                if (typeof showCustomerToast === 'function') {
                    showCustomerToast('Layanan Favorit Dipilih ❤️', `Formulir siap untuk pesanan "${layananParam}"`, 'info');
                }
            }, 600);
        }
    });

    function initCustomerDashboardAuth() {
        try {
            const cleanKey = 'sb_clean_reset_v9';
            if (!localStorage.getItem(cleanKey)) {
                localStorage.removeItem('sb_active_order');
                localStorage.removeItem('sb_customer_orders');
                localStorage.removeItem('sb_customer_pembayaran_selesai');
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const k = localStorage.key(i);
                    if (k && (k.startsWith('sb_chat_') || k.startsWith('sb_cached_') || k.startsWith('sb_order_'))) {
                        localStorage.removeItem(k);
                    }
                }
                localStorage.setItem(cleanKey, 'true');
            }
        } catch (e) {}

        const serverName = @json($customerName !== 'Customer' ? $customerName : null);
        const serverPhone = @json($customerPhone ?: null);
        const serverEmail = @json(Auth::user()?->email ?? null);

        let user = (typeof getCustomerAuth === 'function') ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            try {
                const raw = localStorage.getItem('sb_customer_auth');
                if (raw) user = JSON.parse(raw);
            } catch (e) {}
        }

        if (serverName) {
            user = {
                isLoggedIn: true,
                id: @json(Auth::id() ?? $customerId ?? null),
                name: serverName,
                phone: serverPhone || (user ? user.phone : ''),
                email: serverEmail || (user ? user.email : ''),
                role: 'customer'
            };
            try { 
                localStorage.removeItem('sb_customer_logged_out');
                localStorage.setItem('sb_customer_auth', JSON.stringify(user)); 
            } catch(e) {}
        }

        const currentCustName = (user && user.name) ? user.name : (serverName || 'Customer');
        const currentCustPhone = (user && user.phone) ? user.phone : (serverPhone || '');
        const emailVal = (user && user.email) ? user.email : (serverEmail || (currentCustName !== 'Customer' ? (currentCustName.toLowerCase().replace(/\s+/g, '') + '@email.com') : ''));

        const nameEls = ['custGreetingName', 'profileDetailName', 'profileFieldName', 'profDetailName', 'profCustomerName', 'vipUserNameText'];
        nameEls.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = currentCustName;
        });

        const phoneEls = ['profileDetailPhone', 'profileFieldPhone', 'profDetailPhone', 'profCustomerPhone'];
        phoneEls.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = currentCustPhone;
        });

        ['profileDetailEmail', 'profileFieldEmail', 'profDetailEmail'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerText = emailVal;
        });

        const initial = currentCustName ? currentCustName.charAt(0).toUpperCase() : 'C';
        const avatarEl = document.getElementById('custHeaderAvatar');
        if (avatarEl && initial) {
            avatarEl.innerText = initial;
        }
        const profAvatar = document.getElementById('profAvatarInitial');
        if (profAvatar && initial) {
            profAvatar.innerText = initial;
        }
        const mobileAvatar = document.getElementById('mobileHeaderAvatarInitial');
        if (mobileAvatar && initial) {
            mobileAvatar.innerText = initial;
        }
    }

    // Konten page header untuk tiap tab (judul + deskripsi).
    // Tab dashboard TIDAK pakai page header karena sudah ada banner sapaan.
    const CUST_TAB_PAGE_HEADERS = {
        layanan: { title: 'Layanan & Jasa', desc: 'Pilih layanan untuk langsung mengisi formulir pesanan.' },
        pesanan: { title: 'Pesanan Saya', desc: 'Pantau status dan riwayat seluruh suruhan Anda.' },
        pesan: { title: 'Pesan & Chat', desc: 'Hubungi mitra dan tim CS SayaBantu.' },
        profile: { title: 'Profile', desc: 'Kelola data akun, alamat, dan pengaturan Anda.' },
    };

    function updateCustTabPageHeader(tabName) {
        const section = document.getElementById('custTabPageHeader');
        const cfg = CUST_TAB_PAGE_HEADERS[tabName];

        // Tab Beranda tidak butuh page header (sudah ada banner sapaan).
        if (!section || !cfg) {
            if (section) section.classList.add('hidden');
            return;
        }

        section.classList.remove('hidden');

        const titleEl = document.getElementById('custTabHeaderTitle');
        const descEl = document.getElementById('custTabHeaderDesc');

        if (titleEl) titleEl.innerText = cfg.title;
        if (descEl) descEl.innerText = cfg.desc;
    }

    // Tab Switching (Dashboard, Layanan, Pesanan, Pesan, Profile)
    function switchCustomerTab(tabName) {
        const tabs = ['dashboard', 'layanan', 'pesanan', 'pesan', 'profile'];
        if (!tabs.includes(tabName)) tabName = 'dashboard';

        // Toggle Views
        tabs.forEach(t => {
            const viewId = 'viewCust' + t.charAt(0).toUpperCase() + t.slice(1);
            const viewEl = document.getElementById(viewId);
            if (viewEl) {
                if (t === tabName) {
                    viewEl.classList.remove('hidden');
                } else {
                    viewEl.classList.add('hidden');
                }
            }



            // Bottom Nav Sync (desktop layout)
            const botBtn = document.getElementById('custNav' + t.charAt(0).toUpperCase() + t.slice(1));
            if (botBtn) {
                if (t === tabName) {
                    botBtn.classList.add('nav-active');
                } else {
                    botBtn.classList.remove('nav-active');
                }
            }

            // Bottom Nav Sync (mobile layout partial)
            const mobBtn = document.getElementById('mCustNav' + t.charAt(0).toUpperCase() + t.slice(1));
            if (mobBtn) {
                const aktif = 'text-sky-600 font-extrabold';
                const biasa = 'text-slate-500 hover:text-slate-900 font-semibold';
                const iconWrap = mobBtn.querySelector('div');

                if (t === tabName) {
                    mobBtn.classList.remove(...biasa.split(' '));
                    mobBtn.classList.add(...aktif.split(' '));
                    if (iconWrap) iconWrap.classList.add('bg-sky-50', 'text-sky-600');
                } else {
                    mobBtn.classList.remove(...aktif.split(' '));
                    mobBtn.classList.add(...biasa.split(' '));
                    if (iconWrap) iconWrap.classList.remove('bg-sky-50', 'text-sky-600');
                }
            }
        });

        // Greeting banner hanya tampil di tab Dashboard, disembunyikan di fitur lain.
        const greetingBanner = document.getElementById('custGreetingBanner');
        if (greetingBanner) {
            greetingBanner.classList.toggle('hidden', tabName !== 'dashboard');
        }

        // Page header (judul + deskripsi) berganti mengikuti tab aktif
        updateCustTabPageHeader(tabName);

        // Saat masuk tab Pesanan, sinkronisasi daftar pesanan
        if (tabName === 'pesanan') {
            if (typeof loadSavedCustomerOrders === 'function') {
                loadSavedCustomerOrders();
            }
            if (typeof filterOrderTab === 'function') {
                const activeBtn = document.querySelector('.btn-order-tab-filter.bg-\\[\\#0E1D31\\]') || document.getElementById('btnFilterOrderAll');
                filterOrderTab(activeBtn ? (activeBtn.getAttribute('data-status') || 'all') : 'all', activeBtn);
            }
        }

        // Saat masuk tab Pesan, sinkronisasi daftar percakapan aktif dari API
        if (tabName === 'pesan') {
            const list = document.getElementById('custChatListView');
            const detail = document.getElementById('custChatDetailView');
            if (!currentCustChatRoom) {
                if (list) list.classList.remove('hidden');
                if (detail) detail.classList.add('hidden');
            } else {
                if (list) list.classList.add('hidden');
                if (detail) detail.classList.remove('hidden');
            }
            if (typeof syncCustomerChatListFromApi === 'function') {
                syncCustomerChatListFromApi();
            }
        }

        // Ikon search header hanya relevan untuk mencari layanan.
        // Sembunyikan di tab Pesanan & Profile agar tidak ada dua kolom
        // pencarian yang tampak bersamaan (search layanan vs lacak tiket).
        const searchBtn = document.getElementById('custHeaderSearchBtn');
        if (searchBtn) {
            const searchTabAktif = (tabName === 'dashboard' || tabName === 'layanan');
            searchBtn.classList.toggle('hidden', !searchTabAktif);

            // Tutup dropdown search saat pindah ke tab yang tidak butuh search.
            if (!searchTabAktif) {
                const drop = document.getElementById('custSearchDropdown');
                if (drop) drop.classList.add('hidden');
            }
        }

        // Update URL query tanpa reload (hanya jika memang berada di halaman dashboard)
        if (window.location.pathname.includes('/customer/dashboard')) {
            const newUrl = window.location.pathname + '?tab=' + encodeURIComponent(tabName);
            window.history.replaceState({ tab: tabName }, '', newUrl);
        }

        // Scroll to top of content seketika tanpa delay
        window.scrollTo(0, 0);

        if (typeof syncGradientEkor === 'function') {
            syncGradientEkor();
        }
    }

    // Alias untuk layout bottom navigation
    window.switchCustomerNavTab = function(tab) {
        const isDashboardUrl = window.location.pathname.includes('/customer/dashboard');
        const viewEl = document.getElementById('viewCust' + tab.charAt(0).toUpperCase() + tab.slice(1));
        if (isDashboardUrl && viewEl) {
            switchCustomerTab(tab);
            try {
                const u = new URL(window.location.href);
                u.searchParams.set('tab', tab);
                window.history.pushState({ spa: true, url: u.href }, '', u.href);
            } catch(e) {}
        } else {
            if (typeof window.clearPesananDetailTimers === 'function') {
                window.clearPesananDetailTimers();
            }
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo("{{ route('customer.dashboard') }}?tab=" + encodeURIComponent(tab));
            } else {
                window.location.href = "{{ route('customer.dashboard') }}?tab=" + encodeURIComponent(tab);
            }
        }
    };

    // =========================================================
    //   CHAT CUSTOMER: Buka/Tutup Percakapan & Kirim Pesan Terintegrasi
    //   Mitra = lawan bicara (incoming), Customer = pengirim (outgoing)
    // =========================================================

    let currentCustChatRoom = null;
    let custChatPollInterval = null;
    let custLastMessagesJson = '';

    function openCustChatRoom(orderCode, mitraName = '', serviceName = '', initialPrice = '') {
        const cleanCode = (orderCode || '').replace('#', '').trim() || 'SB-SURUH-7592';
        currentCustChatRoom = cleanCode;
        custLastMessagesJson = ''; // Reset json cache agar render pesan terbaru seketika

        // Ambil data pesanan dari storage lokal atau parameter
        let activeOrder = null;
        try {
            const rawActive = localStorage.getItem('sb_active_order');
            if (rawActive) {
                const parsed = JSON.parse(rawActive);
                if ((parsed.code || '').replace('#', '') === cleanCode) {
                    activeOrder = parsed;
                }
            }
            if (!activeOrder) {
                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    const list = JSON.parse(rawList);
                    activeOrder = list.find(o => (o.code || '').replace('#', '') === cleanCode);
                }
            }
        } catch(e) {}

        const finalMitraName = mitraName || (activeOrder && (activeOrder.mitraName || activeOrder.mitra)) || 'Salsanih';
        const finalServiceName = serviceName || (activeOrder && activeOrder.layanan) || 'Full Home Cleaning';
        const finalPrice = initialPrice || (activeOrder && (activeOrder.nego_price || activeOrder.budget || activeOrder.biaya)) || '75.000';
        const initials = finalMitraName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();

        // Update header detail chat
        const nameEl = document.getElementById('custChatNameDetail');
        if (nameEl) nameEl.innerText = finalMitraName;
        const taskEl = document.getElementById('custChatTaskDetail');
        if (taskEl) taskEl.innerText = `Suruhan #${cleanCode} • ${finalServiceName}`;
        const avatarEl = document.getElementById('custChatAvatarDetail');
        if (avatarEl) {
            avatarEl.innerText = initials;
            avatarEl.className = 'w-10 h-10 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm';
        }

        // Update Kartu Negosiasi Header
        updateCustNegotiationCard(cleanCode, finalServiceName, finalPrice, activeOrder?.status || 'nego');

        // Tampilkan view detail chat, sembunyikan daftar percakapan
        document.getElementById('custChatListView')?.classList.add('hidden');
        document.getElementById('custChatDetailView')?.classList.remove('hidden');

        // Muat pesan dari cache lokal dulu agar responsif
        const cached = localStorage.getItem('sb_chat_' + cleanCode);
        if (cached) {
            try {
                const parsedCached = JSON.parse(cached);
                if (Array.isArray(parsedCached) && parsedCached.length > 0) {
                    renderCustChatBubbles(parsedCached, true);
                }
            } catch(e) {}
        }

        // Ambil pesan terbaru dari API backend
        loadAndRenderCustChatMessages(cleanCode, true);

        // Pasang interval polling sinkronisasi real-time (setiap 1.5 detik)
        if (custChatPollInterval) clearInterval(custChatPollInterval);
        custChatPollInterval = setInterval(() => {
            if (currentCustChatRoom === cleanCode) {
                loadAndRenderCustChatMessages(cleanCode, false);
            }
        }, 1500);

        // Fokuskan field input
        const inp = document.getElementById('custChatInputField');
        if (inp) setTimeout(() => inp.focus(), 250);
    }
    window.openCustChatRoom = openCustChatRoom;

    function closeCustChatRoom() {
        if (custChatPollInterval) {
            clearInterval(custChatPollInterval);
            custChatPollInterval = null;
        }
        currentCustChatRoom = null;
        document.getElementById('custChatDetailView')?.classList.add('hidden');
        document.getElementById('custChatListView')?.classList.remove('hidden');
        if (typeof loadSavedCustomerOrders === 'function') {
            loadSavedCustomerOrders();
        }
    }
    window.closeCustChatRoom = closeCustChatRoom;

    function updateCustNegotiationCard(orderCode, serviceName, priceStr, status) {
        const negoCard = document.getElementById('custChatNegotiationCard');
        if (!negoCard) return;

        window.currentCustChatStatus = status || 'nego';

        // Jika pesanan sudah lewat tahap kesepakatan harga dan masuk jadwal / pengerjaan
        const isCompletedOrOngoing = ['dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(status);
        if (isCompletedOrOngoing) {
            negoCard.classList.add('hidden');
            return;
        }

        negoCard.classList.remove('hidden');
        const dealTitle = document.getElementById('custChatDealTitle');
        if (dealTitle) dealTitle.textContent = serviceName;
        const dealCode = document.getElementById('custChatDealCode');
        if (dealCode) dealCode.textContent = '#' + orderCode;
        const dealPrice = document.getElementById('custChatDealPrice');
        if (dealPrice) dealPrice.textContent = 'Rp ' + priceStr;

        const isDealApproved = ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_admin'].includes(status);

        if (isDealApproved) {
            const dealBadge = document.getElementById('custChatDealStatusBadge');
            if (dealBadge) {
                dealBadge.textContent = 'Disetujui Mitra';
                dealBadge.className = 'text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
            }
            document.getElementById('custChatDealActionBox')?.classList.add('hidden');
            document.getElementById('custChatDealSuccessBox')?.classList.remove('hidden');
            const finalPriceEl = document.getElementById('custChatDealFinalPrice');
            if (finalPriceEl) finalPriceEl.textContent = priceStr;

            const btnBayar = document.getElementById('custChatBtnBayarSekarang');
            if (btnBayar) {
                btnBayar.href = `/customer/pembayaran/${orderCode}?layanan=${encodeURIComponent(serviceName)}&total=${encodeURIComponent(priceStr)}&biaya=${encodeURIComponent(priceStr)}&kesepakatan=disetujui`;
            }
        } else {
            const dealBadge = document.getElementById('custChatDealStatusBadge');
            if (dealBadge) {
                dealBadge.textContent = 'Sedang Nego';
                dealBadge.className = 'text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200';
            }
            document.getElementById('custChatDealActionBox')?.classList.remove('hidden');
            document.getElementById('custChatDealSuccessBox')?.classList.add('hidden');
        }
    }
    window.updateCustNegotiationCard = updateCustNegotiationCard;

    function loadAndRenderCustChatMessages(orderCode, autoScroll = false) {
        if (!orderCode) return;
        fetch(`/api/chat/${orderCode}/messages?role=customer`)
            .then(res => res.json())
            .then(data => {
                if (data && data.success && Array.isArray(data.messages)) {
                    const currentJson = JSON.stringify(data.messages);
                    if (currentJson !== custLastMessagesJson) {
                        custLastMessagesJson = currentJson;
                        localStorage.setItem('sb_chat_' + orderCode, currentJson);
                        renderCustChatBubbles(data.messages, autoScroll);
                    }
                    if (data.order) {
                        const price = data.order.biaya || data.order.nominal || '95.000';
                        const status = data.order.status || 'nego';
                        updateCustNegotiationCard(orderCode, data.order.layanan || 'Suruhan Jasa', price, status);
                        if (data.order.mitra_name) {
                            const nameEl = document.getElementById('custChatNameDetail');
                            if (nameEl) nameEl.innerText = data.order.mitra_name;
                            const avatarEl = document.getElementById('custChatAvatarDetail');
                            if (avatarEl) avatarEl.innerText = data.order.mitra_name.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase();
                        }
                        if (data.order.layanan) {
                            const taskEl = document.getElementById('custChatTaskDetail');
                            if (taskEl) taskEl.innerText = `Suruhan #${orderCode} • ${data.order.layanan}`;
                        }
                    }
                }
            })
            .catch(() => {});
    }

    function renderCustChatBubbles(messages, autoScroll = false) {
        const container = document.getElementById('custChatBubblesContainer');
        if (!container) return;

        // Cek apakah posisi scroll berada di dekat dasar sebelum konten diganti
        const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 120;
        const shouldScroll = autoScroll || isNearBottom;

        let html = `
            <div class="flex items-center gap-2 px-2">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Hari Ini</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>
        `;

        messages.forEach(msg => {
            const isCust = (msg.sender === 'customer');
            const isSystemOrAdmin = (msg.sender === 'system' || msg.sender === 'admin');
            const timeStr = msg.time || (msg.timestamp ? new Date(msg.timestamp * 1000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'Baru saja');
            const avatar = msg.sender_avatar || (isCust ? 'TI' : 'SA');
            const cleanText = (msg.text || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const nominal = msg.metadata?.nominal || '';
            const senderName = msg.sender_name || (isCust ? 'Saya' : 'Mitra SayaBantu');

            // 1. PEMBERITAHUAN SISTEM / ADMIN (Pill Center Pengumuman)
            if (isSystemOrAdmin) {
                const isAdmin = (msg.sender === 'admin');
                html += `
                    <div class="my-2 px-3 py-2 rounded-xl ${isAdmin ? 'bg-blue-50/90 border border-blue-200' : 'bg-slate-50 border border-slate-200'} text-center max-w-[92%] mx-auto shadow-2xs space-y-1" style="animation: fadeInUp 0.2s ease-out;">
                        <div class="inline-flex items-center gap-1.5 text-[9px] font-black ${isAdmin ? 'text-blue-800' : 'text-slate-700'}">
                            <i class="fa-solid ${isAdmin ? 'fa-shield-halved text-blue-600' : 'fa-bell text-amber-500'}"></i>
                            <span>${senderName}</span>
                        </div>
                        <p class="text-[10.5px] text-slate-700 leading-snug font-medium">${cleanText}</p>
                        <span class="text-[8px] text-slate-400 block">${timeStr}</span>
                    </div>
                `;
                return;
            }

            // 2. PESAN NEGOSIASI HARGA
            if (msg.type === 'nego') {
                if (isCust) {
                    html += `
                        <div class="flex items-end gap-2 max-w-[85%] ml-auto flex-row-reverse" style="animation: fadeInUp 0.25s ease-out;">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-sky-400 to-[#173B67] text-white text-[8px] font-bold flex items-center justify-center shrink-0">${avatar}</div>
                            <div class="text-right">
                                <div class="p-3 rounded-2xl rounded-br-md bg-[#173B67] text-white shadow-sm space-y-1">
                                    <div class="flex items-center justify-end gap-1 text-[9px] font-bold text-sky-200">
                                        <i class="fa-solid fa-handshake"></i> Tawaran Harga Anda
                                    </div>
                                    <p class="text-sm font-black text-amber-300">Rp ${nominal}</p>
                                    <p class="text-[10.5px] text-white/90 leading-relaxed">${cleanText}</p>
                                    <span class="text-[8px] text-sky-200 block pt-1 border-t border-white/10">Menunggu persetujuan Mitra...</span>
                                </div>
                                <span class="text-[8px] text-slate-400 mt-0.5 block mr-1">${timeStr} <i class="fa-solid fa-check-double text-sky-400"></i></span>
                            </div>
                        </div>
                    `;
                } else {
                    const currentStatus = window.currentCustChatStatus || '';
                    const isDealFinalized = ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_admin', 'dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(currentStatus);

                    html += `
                        <div class="flex items-end gap-2 max-w-[85%]" style="animation: fadeInUp 0.25s ease-out;">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white text-[8px] font-bold flex items-center justify-center shrink-0">${avatar}</div>
                            <div>
                                <span class="text-[9px] font-bold text-sky-800 ml-1 mb-0.5 block">${senderName}</span>
                                <div class="p-3 rounded-2xl rounded-bl-md bg-white border-2 ${isDealFinalized ? 'border-slate-200' : 'border-sky-400'} shadow-sm space-y-2">
                                    <div class="flex items-center justify-between gap-2 border-b ${isDealFinalized ? 'border-slate-100' : 'border-sky-100'} pb-1">
                                        <span class="text-[9px] font-black text-[#173B67] flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-sky-600"></i> Penawaran Harga dari Mitra
                                        </span>
                                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded ${isDealFinalized ? 'bg-slate-100 text-slate-500' : 'bg-sky-100 text-sky-800'}">${isDealFinalized ? 'Selesai' : 'Tawaran Mitra'}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 block">Nominal yang diajukan:</span>
                                        <strong class="text-base font-black ${isDealFinalized ? 'text-slate-700' : 'text-emerald-600'}">Rp ${nominal}</strong>
                                    </div>
                                    <p class="text-[11px] text-slate-700 leading-relaxed">${cleanText}</p>
                                    ${!isDealFinalized ? `
                                        <div class="pt-2 border-t border-slate-100 flex items-center gap-1.5">
                                            <button type="button" onclick="custSepakatiTawaranMitra('${nominal}')" class="flex-1 py-1.5 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] flex items-center justify-center gap-1 shadow-xs cursor-pointer active:scale-95">
                                                <i class="fa-solid fa-check"></i> Setujui Rp ${nominal}
                                            </button>
                                            <button type="button" onclick="ubahTawaranChatPrompt()" class="py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[9.5px] cursor-pointer">
                                                Tawar Balik
                                            </button>
                                        </div>
                                    ` : `
                                        <div class="pt-1.5 border-t border-slate-100 flex items-center gap-1 text-[8.5px] font-bold text-slate-400">
                                            <i class="fa-solid fa-check-double text-emerald-500"></i>
                                            <span>Kesepakatan harga telah tercapai</span>
                                        </div>
                                    `}
                                </div>
                                <span class="text-[8px] text-slate-400 mt-0.5 block ml-1">${timeStr}</span>
                            </div>
                        </div>
                    `;
                }
            // 3. KESEPAKATAN HARGA DISETUJUI
            } else if (msg.type === 'deal_accepted') {
                html += `
                    <div class="p-3.5 my-2 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-400 shadow-xs space-y-2 text-center" style="animation: fadeInUp 0.25s ease-out;">
                        <div class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-800">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> KESEPAKATAN HARGA DISETUJUI!
                        </div>
                        <p class="text-[11px] text-emerald-900 leading-snug">
                            Kedua pihak menyepakati nominal <strong>Rp ${nominal || '85.000'}</strong>. Pembayaran telah dibuka dan siap diproses!
                        </p>
                        <div class="pt-1 flex justify-center">
                            <a href="/customer/pembayaran/${currentCustChatRoom}?total=${encodeURIComponent(nominal || '85.000')}&biaya=${encodeURIComponent(nominal || '85.000')}&kesepakatan=disetujui" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-md transition-all active:scale-95 animate-pulse">
                                <i class="fa-solid fa-credit-card"></i> Lanjut ke Pembayaran Sekarang
                            </a>
                        </div>
                        <span class="text-[8px] text-emerald-600 block">${timeStr}</span>
                    </div>
                `;
            // 4. PESAN REGULER DUA ARAH (CUSTOMER <-> MITRA)
            } else {
                if (isCust) {
                    html += `
                        <div class="flex items-end gap-2 max-w-[85%] ml-auto flex-row-reverse" style="animation: fadeInUp 0.25s ease-out;">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-sky-400 to-[#173B67] text-white text-[8px] font-bold flex items-center justify-center shrink-0">${avatar}</div>
                            <div class="text-right">
                                <div class="p-2.5 rounded-2xl rounded-br-md bg-[#173B67] text-white shadow-sm">
                                    <p class="text-[11px] leading-relaxed break-words">${cleanText}</p>
                                </div>
                                <span class="text-[8px] text-slate-400 mt-0.5 block mr-1">${timeStr} <i class="fa-solid fa-check-double text-sky-400"></i></span>
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="flex items-end gap-2 max-w-[85%]" style="animation: fadeInUp 0.25s ease-out;">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white text-[8px] font-bold flex items-center justify-center shrink-0">${avatar}</div>
                            <div>
                                <span class="text-[9px] font-bold text-sky-800 ml-1 mb-0.5 block">${senderName}</span>
                                <div class="p-2.5 rounded-2xl rounded-bl-md bg-white border border-slate-200 shadow-xs">
                                    <p class="text-[11px] text-slate-800 leading-relaxed break-words">${cleanText}</p>
                                </div>
                                <span class="text-[8px] text-slate-400 mt-0.5 block ml-1">${timeStr}</span>
                            </div>
                        </div>
                    `;
                }
            }
        });

        container.innerHTML = html;
        if (shouldScroll) {
            setTimeout(() => { container.scrollTop = container.scrollHeight; }, 60);
        }
    }

    function sendCustChatMessage(e) {
        e.preventDefault();
        const inp = document.getElementById('custChatInputField');
        const text = inp ? inp.value.trim() : '';
        if (!text || !currentCustChatRoom) return;

        const code = currentCustChatRoom;
        inp.value = '';

        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        let authUser = null;
        try {
            const rawAuth = localStorage.getItem('sb_customer_auth');
            if (rawAuth) authUser = JSON.parse(rawAuth);
        } catch(err) {}

        const senderName = authUser?.name || @json($customerName !== 'Customer' ? $customerName : (Auth::user()?->name ?? 'Customer'));
        const senderAvatar = senderName.substring(0, 2).toUpperCase() || 'CU';

        const localMsg = {
            id: Date.now(),
            order_code: code,
            sender: 'customer',
            sender_name: senderName,
            sender_avatar: senderAvatar,
            text: text,
            type: 'text',
            time: timeStr,
            timestamp: Math.floor(Date.now() / 1000),
            is_read: false
        };

        // Simpan ke storage lokal
        let list = [];
        try {
            const raw = localStorage.getItem('sb_chat_' + code);
            if (raw) list = JSON.parse(raw);
        } catch(err) {}
        list.push(localMsg);
        localStorage.setItem('sb_chat_' + code, JSON.stringify(list));
        renderCustChatBubbles(list, true);

        // Kirim ke API Backend
        fetch(`/api/chat/${code}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sender: 'customer',
                sender_name: senderName,
                sender_avatar: senderAvatar,
                text: text,
                type: 'text'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                loadAndRenderCustChatMessages(code, true);
            }
        })
        .catch(() => {});
    }
    window.sendCustChatMessage = sendCustChatMessage;

    function ubahTawaranChatPrompt() {
        const baru = prompt('Masukkan nominal penawaran harga Anda (contoh: 75.000):');
        if (!baru) return;
        const cleanNum = parseInt(baru.replace(/[^0-9]/g, '')) || 0;
        if (cleanNum <= 0) {
            alert('Nominal harga tidak valid.');
            return;
        }
        const formatted = cleanNum.toLocaleString('id-ID');
        const inp = document.getElementById('custChatInputField');
        if (inp) {
            inp.value = `Saya ingin menawar harga menjadi Rp ${formatted}. Bagaimana?`;
            inp.focus();
        }
        const dealPriceEl = document.getElementById('custChatDealPrice');
        if (dealPriceEl) {
            dealPriceEl.innerText = 'Rp ' + formatted;
        }
        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) {
                const ord = JSON.parse(raw);
                ord.budget = formatted;
                ord.biaya = formatted;
                ord.nego_price = formatted;
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }
        } catch(e) {}
        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Tawaran Diperbarui', `Tawaran Anda menjadi Rp ${formatted}. Tekan kirim di chat!`, 'info');
        }
    }
    window.ubahTawaranChatPrompt = ubahTawaranChatPrompt;

    function custSepakatiTawaranMitra(nominal) {
        if (!confirm(`Sepakati harga Rp ${nominal} dari Mitra? Suruhan akan diteruskan ke konfirmasi Admin.`)) return;
        const cleanCode = currentCustChatRoom || 'SB-SURUH-9012';
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch(`/api/orders/${cleanCode}/customer-nego`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ harga: nominal, catatan: 'Kesepakatan harga dari chat' })
            }).catch(() => {});

            const raw = localStorage.getItem('sb_active_order');
            if (raw) {
                const ord = JSON.parse(raw);
                ord.status = 'menunggu_konfirmasi_admin';
                ord.step = 6;
                ord.budget = nominal;
                ord.biaya = nominal;
                ord.agreed_price = nominal;
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }
        } catch(e) {}

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Disepakati!', `Harga Rp ${nominal} telah disepakati. Diteruskan ke konfirmasi Admin.`, 'success');
        }
        setTimeout(() => {
            const targetUrl = `/customer/pesanan/${cleanCode}?status=menunggu_konfirmasi_admin&biaya=${nominal}`;
            if (typeof spaNavigateTo === 'function') spaNavigateTo(targetUrl); else window.location.href = targetUrl;
        }, 200);
    }
    window.custSepakatiTawaranMitra = custSepakatiTawaranMitra;

    function bukaModalBuatSuruhan() {
        switchCustomerTab('dashboard');
        setTimeout(() => {
            const formSection = document.getElementById('customerOrderFormSection') || document.getElementById('custFormLayanan')?.closest('section');
            if (formSection) {
                formSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.getElementById('custFormLayanan')?.focus();
            }
        }, 150);
    }
    window.bukaModalBuatSuruhan = bukaModalBuatSuruhan;

    // Sinkronisasi daftar percakapan aktif Customer langsung dari API Backend
    async function syncCustomerChatListFromApi() {
        const container = document.getElementById('custChatRoomListDynamic');
        const emptyState = document.getElementById('custChatEmptyState');
        if (!container) return;

        try {
            const res = await fetch('/api/chat/conversations?role=customer');
            const data = await res.json();
            if (data && data.success && Array.isArray(data.conversations) && data.conversations.length > 0) {
                if (emptyState) emptyState.classList.add('hidden');
                container.innerHTML = data.conversations.map(conv => {
                    const initials = conv.partner_avatar || (conv.partner_name || 'M').substring(0, 2).toUpperCase();
                    const cleanCode = conv.order_code;
                    const dispCode = conv.display_code || ('#' + cleanCode);
                    const partnerName = conv.partner_name || 'Mitra SayaBantu';
                    const serviceName = conv.service || 'Suruhan Jasa';
                    const lastMsg = conv.last_message || 'Koordinasi tugas dengan mitra.';
                    const lastTime = conv.last_time || 'Baru saja';
                    const unreadBadge = conv.unread > 0 
                        ? `<span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white font-extrabold text-[8.5px]">${conv.unread}</span>` 
                        : '';
                    const price = conv.biaya || '85.000';

                    return `
                        <div class="p-3 rounded-xl bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50/40 transition-all cursor-pointer active:scale-[0.98] shadow-2xs flex items-start gap-3"
                             onclick="openCustChatRoom('${cleanCode}', '${partnerName.replace(/'/g, "\\'")}', '${serviceName.replace(/'/g, "\\'")}', '${price}')">
                            <div class="relative shrink-0">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                    ${initials}
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <strong class="text-xs font-extrabold text-slate-900 truncate">${partnerName}</strong>
                                    <span class="text-[9px] text-slate-400 shrink-0 font-medium">${lastTime}</span>
                                </div>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[8.5px] font-bold text-sky-700 truncate">${dispCode} • ${serviceName}</span>
                                    ${unreadBadge}
                                </div>
                                <p class="text-[10.5px] ${conv.unread > 0 ? 'font-bold text-slate-900' : 'text-slate-600'} truncate mt-0.5">${lastMsg}</p>
                            </div>
                        </div>
                    `;
                }).join('');
                return;
            }
        } catch(e) {}

        // Fallback jika API belum ada data
        if (typeof loadSavedCustomerOrders === 'function') {
            loadSavedCustomerOrders();
        }
    }
    window.syncCustomerChatListFromApi = syncCustomerChatListFromApi;

    // Toggle preferensi di section Pengaturan.
    // Saat ini hanya memberi konfirmasi; hubungkan ke API preferensi bila ada.
    function toggleCustSetting(label, aktif) {
        showCustomerToast('Pengaturan', `${label} ${aktif ? 'diaktifkan' : 'dinonaktifkan'}.`, aktif ? 'success' : 'info');
    }

    // Buka popup profil singkat dari tombol avatar "B" di header.
    // Menampilkan ringkasan (nama, email, telepon) + shortcut ke profil lengkap.
    function openCustomerQuickProfile() {
        let user = null;
        try {
            const raw = localStorage.getItem('sb_customer_auth');
            if (raw) user = JSON.parse(raw);
        } catch (e) {}

        if (user && user.isLoggedIn) {
            const currentCustName = user.name || @json(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer'));
            const currentCustPhone = user.phone || @json($customerPhone ?: (Auth::user()?->phone ?? ''));
            const emailVal = user.email || @json(Auth::user()?->email ?? '') || (currentCustName !== 'Customer' ? (currentCustName.toLowerCase().replace(/\s+/g, '') + '@email.com') : '');
            ['profDetailEmail'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerText = emailVal;
            });
            ['profDetailName', 'profCustomerName'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerText = currentCustName;
            });
            ['profDetailPhone', 'profCustomerPhone'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerText = currentCustPhone;
            });
            const profAvatar = document.getElementById('profAvatarInitial');
            if (profAvatar && currentCustName) {
                profAvatar.innerText = currentCustName.charAt(0).toUpperCase();
            }
        }

        openModal('modalProfileCustomer');
    }
    window.openCustomerQuickProfile = openCustomerQuickProfile;

    // =========================================================
    //  KEADAAN PESANAN BERJALAN (ada / kosong)
    //  Dipakai supaya dashboard tidak menampilkan pesanan palsu
    //  ketika customer belum punya pesanan berjalan.
    //
    //  Cara pakai:
    //    aturPesananAktifCustomer(true)   -> tampilkan kartu pesanan
    //    aturPesananAktifCustomer(false)  -> tampilkan ajakan (empty state)
    //  Nanti tinggal dipanggil dari data pesanan asli (API/server).
    // =========================================================
    function aturPesananAktifCustomer(adaPesanan) {
        const section = document.getElementById('sectionPesananAktif');
        const kartu = document.getElementById('kartuPesananAktif');
        const kosong = document.getElementById('kartuPesananKosong');
        if (!section || !kartu || !kosong) return;

        section.dataset.adaPesanan = adaPesanan ? 'true' : 'false';

        kartu.classList.toggle('hidden', !adaPesanan);
        kosong.classList.toggle('hidden', adaPesanan);

        // Empty state tidak butuh garis & gradient hijau (nuansa pesanan berjalan).
        section.classList.toggle('border-l-emerald-500', adaPesanan);
        section.classList.toggle('border-l-slate-300', !adaPesanan);
        section.classList.toggle('from-emerald-50/50', adaPesanan);
        section.classList.toggle('from-sky-50/50', !adaPesanan);

        if (typeof syncGradientEkor === 'function') {
            setTimeout(syncGradientEkor, 30);
        }
    }
    window.aturPesananAktifCustomer = aturPesananAktifCustomer;

    // =========================================================
    //  PERSISTENSI & RENDERING PESANAN CUSTOMER (LOCALSTORAGE)
    // =========================================================
    function loadSavedCustomerOrders() {
        let currentAuth = (typeof getCustomerAuth === 'function') ? getCustomerAuth() : null;
        const srvName = @json($customerName !== 'Customer' ? $customerName : null);
        const srvPhone = @json($customerPhone ?: null);
        if (!currentAuth || !currentAuth.name) {
            if (srvName) {
                currentAuth = {
                    isLoggedIn: true,
                    name: srvName,
                    phone: srvPhone || '',
                    role: 'customer'
                };
                try {
                    localStorage.setItem('sb_customer_auth', JSON.stringify(currentAuth));
                } catch(e) {}
            }
        }
        const currentPhone = currentAuth ? String(currentAuth.phone || '').replace(/[^0-9]/g, '') : (srvPhone ? String(srvPhone).replace(/[^0-9]/g, '') : '');
        const currentName = currentAuth ? (currentAuth.name || '').trim().toLowerCase() : (srvName ? srvName.trim().toLowerCase() : '');

        const inactiveStatuses = ['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin', 'semua_ditolak', 'selesai'];

        // Bersihkan data dummy percontohan legacy jika ada tersimpan
        (function purgeLegacyCustomerDummyOrders() {
            try {
                const legacyCodes = ['#SB-SURUH-9012', '#SB-SURUH-8841', '#SB-SURUH-8720', '#SB-8821', '#SB-9048', 'SB-SURUH-9012', 'SB-SURUH-8841', 'SB-SURUH-8720'];
                const rawActive = localStorage.getItem('sb_active_order');
                if (rawActive) {
                    const act = JSON.parse(rawActive);
                    if (act && legacyCodes.includes(act.code || '')) {
                        localStorage.removeItem('sb_active_order');
                    }
                }
                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    let list = JSON.parse(rawList);
                    if (Array.isArray(list)) {
                        const filtered = list.filter(o => !legacyCodes.includes(o.code || ''));
                        if (filtered.length !== list.length) {
                            localStorage.setItem('sb_customer_orders', JSON.stringify(filtered));
                        }
                    }
                }
            } catch(e) {}
        })();

        let allOrders = [];
        try {
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) allOrders = JSON.parse(rawList);
        } catch(e) {}

        // SINKRONISASI SERVER DATABASE (Single Source of Truth)
        const serverCustomerOrders = @json($serverCustomerOrders ?? []);
        if (Array.isArray(serverCustomerOrders) && serverCustomerOrders.length > 0) {
            serverCustomerOrders.forEach(srv => {
                const srvPhone = String(srv.customer_phone || '').replace(/[^0-9]/g, '');
                const srvName = (srv.customer_name || '').trim().toLowerCase();
                const matchPhone = srvPhone && currentPhone && (srvPhone === currentPhone || srvPhone.endsWith(currentPhone.slice(-8)) || currentPhone.endsWith(srvPhone.slice(-8)));
                const matchName = srvName && currentName && (srvName === currentName || srvName.includes(currentName) || currentName.includes(srvName));
                const isMine = matchPhone || matchName;

                if (isMine) {
                    const srvCode = (srv.code || '').replace('#', '').trim();
                    const formatted = {
                        id: srv.id,
                        code: srv.code,
                        status: srv.status || 'nego',
                        step: srv.step || 1,
                        layanan: srv.layanan || 'Suruhan Jasa',
                        jadwal: srv.jadwal || '',
                        alamat: srv.alamat || '',
                        note: srv.note || '',
                        budget: srv.biaya || '',
                        biaya: srv.biaya || '',
                        nominal: srv.nominal || 0,
                        metode: srv.metode || 'Nego',
                        mitraName: srv.mitra_name || '',
                        mitraPhone: srv.mitra_phone || '',
                        mitraId: srv.mitra_id || '',
                        agreed_price: srv.agreed_price || '',
                        agreed_by: srv.agreed_by || '',
                        customerName: srv.customer_name || '',
                        customerPhone: srv.customer_phone || ''
                    };
                    const exIdx = allOrders.findIndex(o => (o.code || o.id || '').replace('#', '').trim() === srvCode);
                    if (exIdx !== -1) {
                        allOrders[exIdx] = { ...allOrders[exIdx], ...formatted };
                    } else {
                        allOrders.push(formatted);
                    }

                    // Bersihkan dari daftar pembayaran selesai jika di server sudah selesai / dibatalkan
                    if (inactiveStatuses.includes(srv.status)) {
                        try {
                            const rawBayar = localStorage.getItem('sb_customer_pembayaran_selesai') || '{}';
                            const daftarBayar = JSON.parse(rawBayar);
                            if (daftarBayar[srvCode] || daftarBayar['#' + srvCode]) {
                                delete daftarBayar[srvCode];
                                delete daftarBayar['#' + srvCode];
                                localStorage.setItem('sb_customer_pembayaran_selesai', JSON.stringify(daftarBayar));
                            }
                        } catch(e) {}
                    }
                }
            });
            try {
                localStorage.setItem('sb_customer_orders', JSON.stringify(allOrders));
            } catch(e) {}
        }

        // Filter riwayat pesanan agar hanya menampilkan pesanan milik akun saat ini
        if (currentAuth && Array.isArray(allOrders)) {
            allOrders = allOrders.filter(ord => {
                const ordPhone = String(ord.customerPhone || ord.phone || '').replace(/[^0-9]/g, '');
                const ordName = (ord.customerName || ord.nama || '').trim().toLowerCase();
                const hasP = ordPhone && currentPhone && (ordPhone === currentPhone || ordPhone.endsWith(currentPhone.slice(-8)) || currentPhone.endsWith(ordPhone.slice(-8)));
                const hasN = ordName && currentName && (ordName === currentName || ordName.includes(currentName) || currentName.includes(ordName));
                return hasP || hasN;
            });
        }

        // Muat activeOrder dari localStorage dan validasi ketat dengan database dan kepemilikan akun
        let activeOrder = null;
        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) activeOrder = JSON.parse(raw);
        } catch(e) {}

        if (activeOrder) {
            // Validasi apakah activeOrder memang milik user yang sedang aktif login!
            const actPhone = String(activeOrder.customerPhone || activeOrder.phone || '').replace(/[^0-9]/g, '');
            const actName = (activeOrder.customerName || activeOrder.nama || '').trim().toLowerCase();
            const isOwner = (actPhone && currentPhone && (actPhone === currentPhone || actPhone.endsWith(currentPhone.slice(-8)) || currentPhone.endsWith(actPhone.slice(-8)))) ||
                            (actName && currentName && (actName === currentName || actName.includes(currentName) || currentName.includes(actName)));

            if (!isOwner) {
                activeOrder = null;
                try { localStorage.removeItem('sb_active_order'); } catch(e) {}
            }
        }

        if (activeOrder && activeOrder.code) {
            const actCode = (activeOrder.code || '').replace('#', '').trim();
            const matchingOrder = allOrders.find(o => (o.code || o.id || '').replace('#', '').trim() === actCode);
            if (matchingOrder) {
                activeOrder = { ...activeOrder, ...matchingOrder };
                if (inactiveStatuses.includes(activeOrder.status)) {
                    activeOrder = null;
                    try { localStorage.removeItem('sb_active_order'); } catch(e) {}
                }
            } else if (inactiveStatuses.includes(activeOrder.status)) {
                activeOrder = null;
                try { localStorage.removeItem('sb_active_order'); } catch(e) {}
            }
        }

        // Ambil pesanan aktif nyata dari allOrders jika activeOrder null
        if (!activeOrder) {
            const foundActive = allOrders.find(o => !inactiveStatuses.includes(o.status));
            if (foundActive) {
                activeOrder = { ...foundActive };
                try { localStorage.setItem('sb_active_order', JSON.stringify(activeOrder)); } catch(e) {}
            }
        }

        // Cek hasil pembayaran terbaru HANYA jika pesanannya memang belum selesai di database
        if (!activeOrder) {
            try {
                const rawBayar = localStorage.getItem('sb_customer_pembayaran_selesai') || '{}';
                const daftarBayar = JSON.parse(rawBayar);
                const kodeBayar = Object.keys(daftarBayar).pop();
                if (kodeBayar) {
                    const cleanKodeBayar = kodeBayar.replace('#', '').trim();
                    const checkDb = allOrders.find(o => (o.code || o.id || '').replace('#', '').trim() === cleanKodeBayar);
                    if (!checkDb || !inactiveStatuses.includes(checkDb.status)) {
                        const pBayar = daftarBayar[kodeBayar];
                        activeOrder = {
                            code: '#' + cleanKodeBayar,
                            layanan: pBayar.layanan || (checkDb ? checkDb.layanan : 'Suruhan Jasa'),
                            budget: pBayar.total || (checkDb ? checkDb.budget : ''),
                            alamat: (checkDb ? checkDb.alamat : '') || '',
                            jadwal: pBayar.jadwal || (checkDb ? checkDb.jadwal : ''),
                            status: (checkDb && checkDb.status) ? checkDb.status : 'terjadwal',
                            step: (checkDb && checkDb.step) ? checkDb.step : 8,
                            metode: (checkDb ? checkDb.metode : 'Nego'),
                            mitraName: (checkDb ? checkDb.mitraName : ''),
                            customerPhone: currentPhone,
                            customerName: currentName
                        };
                        try {
                            localStorage.setItem('sb_active_order', JSON.stringify(activeOrder));
                        } catch(e) {}
                    }
                }
            } catch(e) {}
        }

        // Tampilkan kartu aktif atau sembunyikan sesuai status aktual
        if (activeOrder && activeOrder.code && !inactiveStatuses.includes(activeOrder.status)) {
            renderCustomerActiveOrder(activeOrder);
            aturPesananAktifCustomer(true);
        } else {
            activeOrder = null;
            try { localStorage.removeItem('sb_active_order'); } catch(e) {}
            aturPesananAktifCustomer(false);
        }

        // Render riwayat pesanan di tab Pesanan
        renderCustomerOrderHistory(allOrders);

        // Render daftar percakapan aktif di tab Pesan
        renderCustomerChatList(allOrders, activeOrder);

        // Polling realtime ke /api/orders/kebutuhan (Sinkronisasi Otomatis Antar Role)
        if (!window.__fetchingCustomerActiveOrder) {
            window.__fetchingCustomerActiveOrder = true;
            fetch('/api/orders/kebutuhan')
                .then(r => r.json())
                .then(data => {
                    window.__fetchingCustomerActiveOrder = false;
                    if (data && data.success && Array.isArray(data.orders)) {
                        let hasNewOrUpdated = false;
                        const myOrders = data.orders.filter(o => {
                            const ordPhone = String(o.customerPhone || o.customer_phone || '').replace(/[^0-9]/g, '');
                            const ordName = (o.customerName || o.customer_name || '').trim().toLowerCase();
                            const matchPhone = ordPhone && currentPhone && (ordPhone === currentPhone || ordPhone.endsWith(currentPhone.slice(-8)) || currentPhone.endsWith(ordPhone.slice(-8)));
                            const matchName = ordName && currentName && (ordName === currentName || ordName.includes(currentName) || currentName.includes(ordName));
                            return matchPhone || matchName;
                        });

                        myOrders.forEach(mo => {
                            const moCode = (mo.code || mo.id || '').replace('#', '').trim();
                            const exIdx = allOrders.findIndex(ex => (ex.code || ex.id || '').replace('#', '').trim() === moCode);
                            if (exIdx !== -1) {
                                if (allOrders[exIdx].status !== mo.status || allOrders[exIdx].step !== mo.step || allOrders[exIdx].mitraName !== mo.mitraName || allOrders[exIdx].alamat !== mo.alamat) {
                                    allOrders[exIdx] = { ...allOrders[exIdx], ...mo };
                                    hasNewOrUpdated = true;
                                }
                            } else {
                                allOrders.unshift(mo);
                                hasNewOrUpdated = true;
                            }

                            if (inactiveStatuses.includes(mo.status)) {
                                try {
                                    const rawBayar = localStorage.getItem('sb_customer_pembayaran_selesai') || '{}';
                                    const daftarBayar = JSON.parse(rawBayar);
                                    if (daftarBayar[moCode] || daftarBayar['#' + moCode]) {
                                        delete daftarBayar[moCode];
                                        delete daftarBayar['#' + moCode];
                                        localStorage.setItem('sb_customer_pembayaran_selesai', JSON.stringify(daftarBayar));
                                    }
                                } catch(e) {}
                            }
                        });

                        const myActive = myOrders.find(o => !inactiveStatuses.includes(o.status));
                        if (myActive) {
                            if (!activeOrder || activeOrder.status !== myActive.status || activeOrder.code !== myActive.code || activeOrder.step !== myActive.step || activeOrder.mitraName !== myActive.mitraName) {
                                activeOrder = myActive;
                                try { localStorage.setItem('sb_active_order', JSON.stringify(myActive)); } catch(e) {}
                                renderCustomerActiveOrder(myActive);
                                aturPesananAktifCustomer(true);
                            }
                        } else {
                            if (activeOrder) {
                                activeOrder = null;
                                try { localStorage.removeItem('sb_active_order'); } catch(e) {}
                                aturPesananAktifCustomer(false);
                            }
                        }

                        if (hasNewOrUpdated) {
                            try { localStorage.setItem('sb_customer_orders', JSON.stringify(allOrders)); } catch(e) {}
                            renderCustomerOrderHistory(allOrders);
                            renderCustomerChatList(allOrders, activeOrder);
                        }
                    }
                })
                .catch(() => { window.__fetchingCustomerActiveOrder = false; });
        }
    }
    window.loadSavedCustomerOrders = loadSavedCustomerOrders;

    if (!window._sbCustDashboardStorageBound) {
        window._sbCustDashboardStorageBound = true;
        window.addEventListener('storage', function(e) {
            if (!e.key || e.key === 'sb_customer_orders' || e.key === 'sb_active_order' || e.key.includes('sb_order_bids_')) {
                if (typeof loadSavedCustomerOrders === 'function') loadSavedCustomerOrders();
            }
        });
        window.addEventListener('sb_order_bid_submitted', function() {
            if (typeof loadSavedCustomerOrders === 'function') loadSavedCustomerOrders();
        });
    }

    if (window._sbCustDashboardPollInterval) {
        clearInterval(window._sbCustDashboardPollInterval);
    }
    window._sbCustDashboardPollInterval = setInterval(function() {
        if (!window.location.pathname.includes('/customer/dashboard')) {
            if (window._sbCustDashboardPollInterval) {
                clearInterval(window._sbCustDashboardPollInterval);
                window._sbCustDashboardPollInterval = null;
            }
            return;
        }
        if (typeof loadSavedCustomerOrders === 'function') loadSavedCustomerOrders();
    }, 3000);

    function renderCustomerActiveOrder(order) {
        if (!order) return;
        const codeEl = document.getElementById('custActiveOrderCode');
        const titleEl = document.getElementById('custActiveOrderTitle');
        const priceEl = document.getElementById('custActiveOrderPrice');
        const addressEl = document.getElementById('custActiveOrderAddress');
        const badgeText = document.getElementById('custActiveOrderBadgeText');
        const badgeEl = document.getElementById('custActiveOrderBadge');
        const btnMain = document.getElementById('custActiveOrderBtnMain');
        const btnMainText = document.getElementById('custActiveOrderBtnMainText');
        const btnMainIcon = document.getElementById('custActiveOrderBtnMainIcon');
        const stepperBar = document.getElementById('custActiveStepperBar');

        if (codeEl) codeEl.textContent = order.code || '#SB-SURUH-9012';
        if (titleEl) titleEl.textContent = order.layanan || 'Full Home Cleaning';
        if (priceEl) {
            const isConfirmedOrBeyond = ['menunggu_pembayaran', 'siap_bayar', 'terjadwal', 'dibayar', 'otw', 'berjalan', 'selesai'].includes(order.status || '');
            if (order.nego_price && !isConfirmedOrBeyond) {
                priceEl.innerHTML = `<span class="text-xs text-slate-400 line-through mr-1 font-normal">Rp ${order.original_budget || order.budget || '85.000'}</span><span class="text-xs font-black text-amber-700">Rp ${order.nego_price}</span> <span class="text-[8px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 px-1 py-0.2 rounded ml-1 uppercase inline-block animate-pulse">Tawaran Nego</span>`;
            } else {
                const finalBiaya = order.agreed_price || order.budget || order.biaya || '85.000';
                priceEl.textContent = 'Rp ' + finalBiaya;
            }
        }
        if (addressEl) addressEl.textContent = order.alamat || 'depok lenteng agung';

        const mitraEl = document.getElementById('custActiveOrderMitra');
        if (mitraEl) {
            const namaMitra = (order.mitraName || order.mitra_name || order.mitra || order.selectedMitraName || '').trim();
            mitraEl.textContent = (namaMitra && !namaMitra.toLowerCase().includes('menunggu')) ? namaMitra : 'Menunggu Mitra...';
        }

        // JADWAL AKTIF: tampilkan kapan & jam berapa mitra datang.
        // Muncul untuk pesanan yang sudah dibayar (terjadwal), otw, atau sedang berjalan.
        const jadwalBox = document.getElementById('custActiveOrderJadwal');
        const jadwalTeks = document.getElementById('custActiveOrderJadwalTeks');
        const jadwalPesanan = order.jadwal || (order.tanggal ? (order.tanggal + (order.waktu ? ' ' + order.waktu : '')) : '');
        if (jadwalBox && jadwalTeks) {
            const tampilJadwal = !!jadwalPesanan && ['terjadwal', 'otw', 'dibayar', 'siap_bayar', 'berjalan'].includes(order.status || '');
            if (tampilJadwal) {
                jadwalTeks.textContent = jadwalPesanan;
                jadwalBox.classList.remove('hidden');
                jadwalBox.classList.add('flex');
            } else {
                jadwalBox.classList.add('hidden');
                jadwalBox.classList.remove('flex');
            }
        }

        const status = order.status || 'nego';

        // Update indikator 5 titik stepper
        const dot1 = document.getElementById('stepDot1');
        const dot2 = document.getElementById('stepDot2');
        const dot3 = document.getElementById('stepDot3');
        const dot4 = document.getElementById('stepDot4');
        const dot5 = document.getElementById('stepDot5');
        const lbl1 = document.getElementById('stepLabel1');
        const lbl2 = document.getElementById('stepLabel2');
        const lbl3 = document.getElementById('stepLabel3');
        const lbl4 = document.getElementById('stepLabel4');
        const lbl5 = document.getElementById('stepLabel5');

        function updateStepPoint(dot, lbl, state, labelText) {
            if (!dot || !lbl) return;
            if (state === 'done') {
                dot.className = 'w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs';
                dot.textContent = '✓';
                lbl.className = 'text-emerald-700 font-bold';
            } else if (state === 'active') {
                dot.className = 'w-4 h-4 rounded-full bg-[#173B67] text-white flex items-center justify-center text-[7.5px] font-bold shadow-xs animate-pulse ring-2 ring-sky-300';
                dot.textContent = '●';
                lbl.className = 'text-[#173B67] font-black';
            } else {
                dot.className = 'w-4 h-4 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center text-[7.5px]';
                dot.textContent = labelText;
                lbl.className = 'text-slate-400';
            }
        }

        if (status === 'menunggu_penawaran') {
            if (badgeText) badgeText.textContent = 'Menunggu Penawaran Mitra';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 flex items-center gap-1.5 border border-amber-200';
            if (btnMainText) btnMainText.textContent = 'Lihat Penawaran Masuk';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-hourglass-half text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '20%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'active', '2');
            updateStepPoint(dot3, lbl3, 'pending', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'nego' || status === 'menunggu_keputusan') {
            if (order.nego_price) {
                if (badgeText) badgeText.textContent = `Tawaran Nego Masuk (Rp ${order.nego_price})`;
                if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1.5 animate-pulse';
                if (btnMainText) btnMainText.textContent = `Lihat Tawaran Nego (Rp ${order.nego_price})`;
                if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            } else {
                if (badgeText) badgeText.textContent = 'Pemilihan Mitra (3 Penawaran)';
                if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#173B67] flex items-center gap-1.5';
                if (btnMainText) btnMainText.textContent = 'Bandingkan & Pilih Mitra';
                if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            }
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-scale-balanced text-xs';
            if (stepperBar) stepperBar.style.width = '35%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'active', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'menunggu_konfirmasi_admin') {
            if (badgeText) badgeText.textContent = 'Menunggu Konfirmasi Admin';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-900 flex items-center gap-1.5 border border-indigo-200';
            if (btnMainText) btnMainText.textContent = 'Lihat Verifikasi Kesepakatan';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-shield-halved text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-indigo-700 hover:bg-indigo-600 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '45%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'active', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'menunggu_mitra') {
            if (badgeText) badgeText.textContent = 'Menunggu Konfirmasi Mitra';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 flex items-center gap-1.5';
            if (btnMainText) btnMainText.textContent = 'Lihat Detail & Status';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-hourglass-half text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '25%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'active', '2');
            updateStepPoint(dot3, lbl3, 'pending', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'semua_ditolak' || status === 'dibatalkan') {
            if (badgeText) badgeText.textContent = 'Kesepakatan Batal / Ditolak';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 flex items-center gap-1.5 border border-rose-300';
            if (btnMainText) btnMainText.textContent = 'Ajukan Kembali Suruhan';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-rotate-right text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '0%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'pending', '2');
            updateStepPoint(dot3, lbl3, 'pending', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'siap_bayar' || status === 'menunggu_pembayaran') {
            const targetNominal = order.agreed_price || order.budget || order.biaya || '43.000';
            if (badgeText) badgeText.textContent = 'Kesepakatan Disetujui Admin • Siap Bayar';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5 animate-pulse';
            if (btnMainText) btnMainText.textContent = 'Bayar Sekarang (Rp ' + targetNominal + ')';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-credit-card text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[10.5px] font-black text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors animate-pulse cursor-pointer';
            if (stepperBar) stepperBar.style.width = '50%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'active', '3');
            updateStepPoint(dot4, lbl4, 'pending', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'terjadwal' || status === 'dibayar') {
            // Setelah pembayaran lunas: keluar jadwal pengerjaan mitra
            if (badgeText) badgeText.textContent = 'Lunas • Jadwal Dikonfirmasi';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-blue-100 text-[#173B67] flex items-center gap-1.5 border border-blue-200';
            if (btnMainText) btnMainText.textContent = 'Lihat Detail Jadwal';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-calendar-check text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '75%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'done', '3');
            updateStepPoint(dot4, lbl4, 'active', '4');
            updateStepPoint(dot5, lbl5, 'pending', '5');
        } else if (status === 'otw') {
            // Mitra dalam perjalanan
            if (badgeText) badgeText.textContent = 'Mitra Sedang OTW ke Lokasi 🛵';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 flex items-center gap-1.5 border border-amber-300 animate-pulse';
            if (btnMainText) btnMainText.textContent = 'Chat Mitra OTW';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-comments text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '95%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'done', '3');
            updateStepPoint(dot4, lbl4, 'done', '4');
            updateStepPoint(dot5, lbl5, 'active', '5');
        } else if (status === 'berjalan') {
            if (badgeText) badgeText.textContent = 'Pekerjaan Sedang Dilakukan';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 flex items-center gap-1.5';
            if (btnMainText) btnMainText.textContent = 'Chat Mitra';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-comments text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white text-[10.5px] font-bold text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '95%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'done', '3');
            updateStepPoint(dot4, lbl4, 'done', '4');
            updateStepPoint(dot5, lbl5, 'done', '5');
        } else if (status === 'selesai') {
            if (badgeText) badgeText.textContent = 'Pekerjaan Selesai';
            if (badgeEl) badgeEl.className = 'text-[9.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 flex items-center gap-1.5';
            if (btnMainText) btnMainText.textContent = 'Beri Ulasan Mitra';
            if (btnMainIcon) btnMainIcon.className = 'fa-solid fa-star text-yellow-400 text-xs';
            if (btnMain) btnMain.className = 'flex-1 py-1.5 px-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 text-[10.5px] font-black text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors cursor-pointer';
            if (stepperBar) stepperBar.style.width = '100%';
            updateStepPoint(dot1, lbl1, 'done', '1');
            updateStepPoint(dot2, lbl2, 'done', '2');
            updateStepPoint(dot3, lbl3, 'done', '3');
            updateStepPoint(dot4, lbl4, 'done', '4');
            updateStepPoint(dot5, lbl5, 'done', '5');
        }
    }

    // Klik di mana saja pada kartu pesanan aktif membuka detail/lacak pesanan
    function openActiveOrderDetail() {
        handleCustActiveOrderDetailAction();
    }
    window.openActiveOrderDetail = openActiveOrderDetail;

    function handleCustActiveOrderMainAction() {
        let order = null;
        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) order = JSON.parse(raw);
        } catch(e) {}
        if (!order) return;

        const status = order.status || 'nego';
        const cleanCode = (order.code || '').replace('#', '').trim() || 'SB-SURUH-7592';
        if (status === 'nego') {
            switchCustomerTab('pesan');
            openCustChatRoom(cleanCode, order.mitraName || order.mitra || 'Salsanih', order.layanan || 'Suruhan Jasa', order.budget || order.biaya || '75.000');
        } else if (status === 'siap_bayar' || status === 'menunggu_pembayaran') {
            const q = new URLSearchParams({
                layanan: order.layanan || '',
                total: order.budget || order.agreed_price || '',
                biaya: order.budget || order.agreed_price || '',
                alamat: order.alamat || '',
                jadwal: order.jadwal || '',
                kesepakatan: 'disetujui'
            });
            const targetUrl = `/customer/pembayaran/${(order.code || 'SB-SURUH-0000').replace('#', '')}?${q.toString()}`;
            if (typeof spaNavigateTo === 'function') spaNavigateTo(targetUrl); else window.location.href = targetUrl;
        } else if (status === 'terjadwal') {
            handleCustActiveOrderDetailAction();
        } else if (status === 'otw' || status === 'berjalan') {
            switchCustomerTab('pesan');
            openCustChatRoom(cleanCode, order.mitraName || order.mitra || 'Mitra SayaBantu', order.layanan || 'Suruhan Jasa', order.budget || order.biaya || '');
        } else {
            handleCustActiveOrderDetailAction();
        }
    }

    function handleCustActiveOrderDetailAction() {
        let order = null;
        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) order = JSON.parse(raw);
        } catch(e) {}
        if (!order) {
            const targetUrl = '/customer/pesanan/SB-SURUH-9012?status=berjalan&layanan=Full%20Home%20Cleaning&biaya=85.000';
            if (typeof spaNavigateTo === 'function') spaNavigateTo(targetUrl); else window.location.href = targetUrl;
            return;
        }

        const q = new URLSearchParams({
            status: order.status || 'nego',
            layanan: order.layanan || '',
            biaya: order.nego_price || order.budget || '',
            metode: order.metode || 'Nego',
            alamat: order.alamat || '',
            note: order.note || '',
            jadwal: order.waktu ? (order.tanggal + ' • ' + order.waktu) : ''
        });
        const targetUrl = `/customer/pesanan/${(order.code || 'SB-SURUH-0000').replace('#', '')}?${q.toString()}`;
        if (typeof spaNavigateTo === 'function') spaNavigateTo(targetUrl); else window.location.href = targetUrl;
    }

    function renderCustomerOrderHistory(orders) {
        const container = document.getElementById('customerOrderListItems');
        if (!container) return;

        let customBox = document.getElementById('custDynamicOrdersBox');
        if (!customBox) {
            customBox = document.createElement('div');
            customBox.id = 'custDynamicOrdersBox';
            customBox.className = 'space-y-2.5';
            container.insertBefore(customBox, container.firstChild);
        }

        const emptyState = document.getElementById('custOrdersEmptyState');

        if (!orders || orders.length === 0) {
            customBox.innerHTML = '';
            if (emptyState) emptyState.classList.remove('hidden');
            if (document.getElementById('btnFilterOrderAll')) document.getElementById('btnFilterOrderAll').textContent = 'Semua (0)';
            if (document.getElementById('btnFilterOrderAktif')) document.getElementById('btnFilterOrderAktif').textContent = 'Sedang Aktif (0)';
            if (document.getElementById('btnFilterOrderSelesai')) document.getElementById('btnFilterOrderSelesai').textContent = 'Selesai (0)';
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        const allCount = orders.length;
        const aktifCount = orders.filter(o => o.status !== 'selesai' && o.status !== 'dibatalkan' && o.status !== 'semua_ditolak').length;
        const selesaiCount = orders.filter(o => o.status === 'selesai').length;

        if (document.getElementById('btnFilterOrderAll')) document.getElementById('btnFilterOrderAll').textContent = `Semua (${allCount})`;
        if (document.getElementById('btnFilterOrderAktif')) document.getElementById('btnFilterOrderAktif').textContent = `Sedang Aktif (${aktifCount})`;
        if (document.getElementById('btnFilterOrderSelesai')) document.getElementById('btnFilterOrderSelesai').textContent = `Selesai (${selesaiCount})`;

        customBox.innerHTML = orders.map(ord => {
            const isNego = ord.status === 'nego' || ord.status === 'menunggu_keputusan';
            const isMenungguPenawaran = ord.status === 'menunggu_penawaran' || ord.status === 'menunggu_mitra';
            const isMenungguAdmin = ord.status === 'menunggu_konfirmasi_admin';
            const isSiapBayar = ord.status === 'siap_bayar' || ord.status === 'menunggu_pembayaran';
            const isBerjalan = ord.status === 'berjalan' || ord.status === 'otw';
            const isTerjadwal = ord.status === 'terjadwal' || ord.status === 'dibayar';
            const isDitolak = ord.status === 'semua_ditolak' || ord.status === 'dibatalkan';
            const isSelesai = ord.status === 'selesai';

            let badgeHtml = '';
            let borderCls = 'border-slate-200';
            if (isMenungguPenawaran) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">● Menunggu Mitra</span>';
                borderCls = 'border-l-4 border-l-amber-500 border-slate-200';
            } else if (isNego) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-sky-100 text-[#173B67] border border-sky-200">● Pemilihan Mitra</span>';
                borderCls = 'border-l-4 border-l-sky-500 border-slate-200';
            } else if (isMenungguAdmin) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-900 border border-indigo-200">● Verifikasi Admin</span>';
                borderCls = 'border-l-4 border-l-indigo-500 border-slate-200';
            } else if (isSiapBayar) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 animate-pulse border border-emerald-200">● Siap Bayar</span>';
                borderCls = 'border-l-4 border-l-emerald-500 border-slate-200';
            } else if (isTerjadwal) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-blue-100 text-[#173B67] border border-blue-200">● Jadwal Terkonfirmasi</span>';
                borderCls = 'border-l-4 border-l-blue-500 border-slate-200';
            } else if (isBerjalan) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">● Sedang Berjalan</span>';
                borderCls = 'border-l-4 border-l-emerald-500 border-slate-200';
            } else if (isDitolak) {
                badgeHtml = '<span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">● Kesepakatan Batal</span>';
                borderCls = 'border-l-4 border-l-rose-500 border-slate-200';
            } else {
                badgeHtml = '<span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">Selesai</span>';
            }

            const codeClean = (ord.code || '').replace('#', '').trim();
            const displayBiaya = ord.nego_price || ord.budget || ord.biaya || '';
            const detailUrl = `/customer/pesanan/${codeClean}?status=${ord.status || 'menunggu_penawaran'}&layanan=${encodeURIComponent(ord.layanan || '')}&biaya=${encodeURIComponent(displayBiaya)}&metode=${encodeURIComponent(ord.metode || 'Nego')}&alamat=${encodeURIComponent(ord.alamat || '')}&note=${encodeURIComponent(ord.note || '')}`;
            const bayarUrl = `/customer/pembayaran/${codeClean}?layanan=${encodeURIComponent(ord.layanan || '')}&total=${encodeURIComponent(displayBiaya)}&biaya=${encodeURIComponent(displayBiaya)}&alamat=${encodeURIComponent(ord.alamat || '')}&kesepakatan=disetujui&status=menunggu_pembayaran`;

            return `
                <div onclick="bukaDetailPesanan('${codeClean}', '${ord.status || ''}', '${(ord.layanan || '').replace(/'/g, "\\'")}', '${displayBiaya}', '${(ord.alamat || '').replace(/'/g, "\\'")}')" class="card-sb p-3 ${borderCls} space-y-2 order-status-item bg-white shadow-xs rounded-xl cursor-pointer hover:shadow-md transition-all" data-status="${ord.status === 'selesai' ? 'selesai' : 'aktif'}">
                    <div class="flex items-center justify-between">
                        ${badgeHtml}
                        <span class="text-[9.5px] font-mono font-bold text-slate-700">${ord.code || ''}</span>
                    </div>
                    <div>
                        <div class="flex justify-between items-center">
                            <strong class="text-xs font-bold text-slate-900">${ord.layanan || ''}</strong>
                            ${ord.nego_price ? `
                                <div class="text-right">
                                    <span class="text-[9px] text-slate-400 line-through mr-1 font-normal">Rp ${ord.budget || ord.biaya || ''}</span>
                                    <strong class="text-xs font-black text-amber-700">Rp ${ord.nego_price}</strong>
                                    <span class="block text-[7.5px] font-bold text-amber-800">Tawaran Nego</span>
                                </div>
                            ` : `
                                <strong class="text-xs font-black text-emerald-700">Rp ${ord.budget || ord.biaya || ''}</strong>
                            `}
                        </div>
                        <p class="text-[10px] text-slate-500 mt-0.5 truncate">${ord.alamat ? ('<i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> ' + ord.alamat) : 'Alamat Penugasan'}</p>
                    </div>
                    <div class="flex items-center gap-1.5 pt-1.5 border-t border-slate-100">
                        ${isSiapBayar ? `
                            <a href="${bayarUrl}" onclick="event.stopPropagation();" class="flex-1 py-1.5 px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] text-center transition-colors shadow-xs">
                                <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                            </a>
                        ` : isNego || isMenungguPenawaran ? `
                            <a href="${detailUrl}" onclick="event.stopPropagation();" class="flex-1 py-1.5 px-2.5 rounded-lg ${ord.nego_price ? 'bg-amber-600 hover:bg-amber-500 text-white font-bold' : 'bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold'} text-[10px] text-center transition-colors">
                                <i class="fa-solid ${ord.nego_price ? 'fa-bolt' : 'fa-scale-balanced'}"></i> ${ord.nego_price ? `Lihat Tawaran Nego (Rp ${ord.nego_price})` : 'Bandingkan / Pilih Mitra'}
                            </a>
                        ` : isMenungguAdmin ? `
                            <a href="${detailUrl}" onclick="event.stopPropagation();" class="flex-1 py-1.5 px-2.5 rounded-lg bg-indigo-700 hover:bg-indigo-600 text-white font-bold text-[10px] text-center transition-colors">
                                <i class="fa-solid fa-shield-halved"></i> Cek Verifikasi Admin
                            </a>
                        ` : isDitolak ? `
                            <a href="${detailUrl}" onclick="event.stopPropagation();" class="flex-1 py-1.5 px-2.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-[10px] text-center transition-colors">
                                <i class="fa-solid fa-rotate-right"></i> Ajukan Kembali
                            </a>
                        ` : isSelesai ? `
                            <button type="button" onclick="event.stopPropagation(); bukaDetailPesanan('${codeClean}', 'selesai', '${(ord.layanan || '').replace(/'/g, "\\'")}', '${displayBiaya}', '${(ord.alamat || '').replace(/'/g, "\\'")}')" class="flex-1 py-1.5 px-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-[10px] text-center transition-colors flex items-center justify-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-star text-[9.5px]"></i> Beri Ulasan
                            </button>
                        ` : `
                            <button type="button" onclick="event.stopPropagation(); switchCustomerTab('pesan'); openCustChatRoom('${codeClean}', '${(ord.mitraName || ord.mitra_name || ord.mitra || 'Mitra SayaBantu').replace(/'/g, "\\'")}', '${(ord.layanan || 'Suruhan Jasa').replace(/'/g, "\\'")}', '${ord.budget || ord.biaya || ''}');" class="flex-1 py-1.5 px-2.5 rounded-lg bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-[10px] text-center transition-colors">
                                <i class="fa-solid fa-comments"></i> Chat Mitra
                            </button>
                        `}
                        <button type="button" onclick="event.stopPropagation(); bukaDetailPesanan('${codeClean}', '${ord.status || ''}', '${(ord.layanan || '').replace(/'/g, "\\'")}', '${displayBiaya}', '${(ord.alamat || '').replace(/'/g, "\\'")}')" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-[#0E1D31] hover:text-white text-slate-700 font-bold text-[10px] transition-all cursor-pointer flex items-center gap-1 shadow-2xs active:scale-95">
                            <i class="fa-solid ${isSelesai ? 'fa-receipt' : 'fa-location-crosshairs'} text-[9px]"></i> ${isSelesai ? 'Rincian' : 'Lacak'}
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        // Terapkan filter tab aktif jika pengguna sedang memilih filter tertentu
        const activeFilterBtn = document.querySelector('.btn-order-tab-filter.bg-\\[\\#0E1D31\\]');
        if (activeFilterBtn) {
            const currentStatus = activeFilterBtn.getAttribute('data-status') || 'all';
            if (currentStatus !== 'all') {
                filterOrderTab(currentStatus, activeFilterBtn);
            }
        }
    }

    function renderCustomerChatList(orders = [], activeOrder = null) {
        const container = document.getElementById('custChatRoomListDynamic');
        const emptyState = document.getElementById('custChatEmptyState');
        if (!container) return;

        const chatOrders = [];
        if (activeOrder && activeOrder.code) {
            chatOrders.push(activeOrder);
        }
        if (Array.isArray(orders)) {
            orders.forEach(o => {
                if (o && o.code && !chatOrders.some(x => (x.code || '').replace('#','') === (o.code || '').replace('#',''))) {
                    if (['nego', 'menunggu_keputusan', 'menunggu_pembayaran', 'siap_bayar', 'menunggu_konfirmasi_admin', 'dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(o.status || '')) {
                        chatOrders.push(o);
                    }
                }
            });
        }

        if (chatOrders.length === 0) {
            container.innerHTML = '';
            if (emptyState) emptyState.classList.remove('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        container.innerHTML = chatOrders.map((ord, idx) => {
            const mitraName = ord.mitraName || ord.mitra || 'Salsanih';
            const initials = mitraName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            const serviceName = ord.layanan || 'Suruhan Jasa';
            const code = ord.code || '#SB';
            const cleanCode = (code || '').replace('#', '').trim();
            const price = ord.budget || ord.biaya || ord.nominal || '95.000';
            return `
                <div class="p-3 rounded-xl bg-sky-50/60 border border-sky-200 flex items-start gap-3 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openCustChatRoom('${cleanCode}', '${mitraName.replace(/'/g, "\\'")}', '${serviceName.replace(/'/g, "\\'")}', '${price}')">
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">${initials}</div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <strong class="text-xs font-extrabold text-slate-900 truncate">${mitraName}</strong>
                            <span class="text-[9px] text-sky-600 font-bold">Aktif</span>
                        </div>
                        <span class="text-[8.5px] font-semibold text-sky-700 block">${code} • ${serviceName}</span>
                        <p class="text-[10px] text-slate-700 truncate mt-0.5 font-semibold">Koordinasi pelaksanaan suruhan dan kesepakatan harga.</p>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Cross-tab real-time storage sync listener
    window.addEventListener('storage', function(e) {
        if (!e.key) return;
        if (currentCustChatRoom && e.key === 'sb_chat_' + currentCustChatRoom) {
            try {
                const msgs = JSON.parse(e.newValue || '[]');
                if (Array.isArray(msgs)) renderCustChatBubbles(msgs, true);
            } catch(err) {}
        }
        if (e.key === 'sb_active_order' || e.key === 'sb_customer_orders') {
            loadSavedCustomerOrders();
            if (currentCustChatRoom) {
                loadAndRenderCustChatMessages(currentCustChatRoom, false);
            }
        }
    });

    // Toggle Search di Topbar
    function toggleCustDashboardSearch() {
        const drop = document.getElementById('custSearchDropdown');
        if (!drop) return;
        drop.classList.toggle('hidden');
        if (!drop.classList.contains('hidden')) {
            const input = document.getElementById('custDashboardSearchInput');
            if (input) input.focus();
        }
    }

    // Live search catalog
    const DASHBOARD_SERVICES = [
        { name: 'Full Home Cleaning', budget: '85.000', icon: 'fa-broom', desc: 'Pembersihan rumah menyeluruh kamar, lantai & debu.' },
        { name: 'Basic Daily Cleaning', budget: '50.000', icon: 'fa-spray-can-sparkles', desc: 'Sapu, pel rutin & lap meja/kaca kinclong.' },
        { name: 'Bersihkan Kamar Mandi', budget: '65.000', icon: 'fa-shower', desc: 'Kuras bak & sikat kerak lantai kamar mandi.' },
        { name: 'Cuci Piring & Dapur', budget: '45.000', icon: 'fa-sink', desc: 'Bantu cuci piring tumpuk & kompor bersih.' },
        { name: 'Pindahan Kos & Rumah', budget: '150.000', icon: 'fa-boxes-packing', desc: 'Bantu packing & angkat muatan barang pindahan.' },
        { name: 'Angkut Barang Rumah Tangga', budget: '80.000', icon: 'fa-dolly', desc: 'Bantu angkat dan memindahkan barang rumah tangga.' },
        { name: 'Buang Sampah Rumah Tangga', budget: '40.000', icon: 'fa-trash-can', desc: 'Bantu membuang sampah rumah tangga.' },
        { name: 'Antar / Jemput Barang', budget: '50.000', icon: 'fa-motorcycle', desc: 'Antar barang, dokumen penting atau laundry kilat.' },
        { name: 'Belanja Pasar Subuh', budget: '45.000', icon: 'fa-basket-shopping', desc: 'Belanja sayur segar subuh pasar tradisional.' },
        { name: 'Titip Antre RS & Faskes', budget: '60.000', icon: 'fa-hourglass-half', desc: 'Ambil nomor antrean dokter atau tiket event.' },
        { name: 'Nemenin Olahraga', budget: '60.000', icon: 'fa-person-running', desc: 'Teman jogging, gym, gowes atau jaga stand.' },
        { name: 'Bantu Kegiatan Harian', budget: '60.000', icon: 'fa-handshake', desc: 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.' }
    ];

    function filterLiveCustomerServices(query) {
        const q = (query || '').toLowerCase().trim();
        const box = document.getElementById('custSearchResultsBox');
        if (!box) return;

        if (!q) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }

        const matches = DASHBOARD_SERVICES.filter(s => (s.name + ' ' + s.desc).toLowerCase().includes(q));
        if (matches.length === 0) {
            box.innerHTML = '<div class="p-3 text-center text-slate-400">Tidak menemukan layanan yang cocok.</div>';
        } else {
            box.innerHTML = matches.map(s => `
                <div class="p-2 flex items-center justify-between hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-sky-50 text-sky-600 flex items-center justify-center text-[10px]">
                            <i class="fa-solid ${s.icon}"></i>
                        </span>
                        <div>
                            <strong class="text-slate-900 block text-[11px]">${s.name}</strong>
                            <span class="text-[9px] text-slate-400">Rp ${s.budget}</span>
                        </div>
                    </div>
                    <button type="button" onclick="toggleCustDashboardSearch(); pesanCepat('${s.name}', '${s.budget}')" class="px-2 py-0.5 rounded bg-[#0E1D31] text-white font-bold text-[9px]">
                        Pesan
                    </button>
                </div>
            `).join('');
        }
        box.classList.remove('hidden');
    }

    // Pesan Cepat: membuka dialog konfirmasi dan scroll ke pemesanan
    function pesanCepat(serviceName, budget) {
        const user = (typeof getCustomerAuth === 'function' ? getCustomerAuth() : null) || { name: @json(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer')), phone: @json($customerPhone ?: (Auth::user()?->phone ?? '')) };

        const modal = document.getElementById('modalKonfirmasiOrder');
        if (modal) {
            const randCode = '#SB-SURUH-' + Math.floor(1000 + Math.random() * 9000);
            const dateStr = new Date().toISOString().split('T')[0];

            const elCode = document.getElementById('confOrderCode');
            const elLay = document.getElementById('confLayanan');
            const elWkt = document.getElementById('confWaktu');
            const elBgt = document.getElementById('confBudget');
            const elPem = document.getElementById('confPemesan');
            const elNot = document.getElementById('confNote');

            if (elCode) elCode.innerText = randCode;
            if (elLay) elLay.innerText = serviceName;
            if (elWkt) elWkt.innerText = dateStr + ' (Langsung Ditugaskan)';
            if (elBgt) elBgt.innerText = 'Rp ' + budget + ' (Sesuai Tarif)';
            if (elPem) elPem.innerText = (user.name || @json(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer'))) + ' (' + (user.phone || @json($customerPhone ?: (Auth::user()?->phone ?? ''))) + ')';
            if (elNot) elNot.innerText = 'depok lenteng agung (Pesan Cepat Reguler)';

            if (typeof openModal === 'function') {
                openModal('modalKonfirmasiOrder');
            }
        } else {
            showCustomerToast('Pesan Layanan', `Membuat pesanan ${serviceName} (Rp ${budget})...`, 'success');
        }
    }

    // Toggle expand/collapse detail layanan di tab customer (persis seperti di halaman publik)
    function toggleCustServiceDetail(btn) {
        const item = btn.closest('.layanan-cust-item');
        if (!item) return;
        const detail = item.querySelector('.service-detail');
        const chevron = item.querySelector('.service-chevron');
        if (!detail) return;

        const willOpen = detail.classList.contains('hidden');

        // Tutup kartu lain supaya rapi
        document.querySelectorAll('.layanan-cust-item .service-detail').forEach(d => {
            if (d !== detail) d.classList.add('hidden');
        });
        document.querySelectorAll('.layanan-cust-item button[aria-expanded]').forEach(b => {
            if (b !== btn) b.setAttribute('aria-expanded', 'false');
        });
        document.querySelectorAll('.layanan-cust-item .service-chevron').forEach(c => {
            if (c !== chevron) c.classList.remove('rotate-180');
        });

        detail.classList.toggle('hidden', !willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        if (chevron) chevron.classList.toggle('rotate-180', willOpen);
    }

    // Filter kategori di tab layanan customer (persis halaman publik)
    function filterCustLayananPageByCat(cat, btn) {
        document.querySelectorAll('.btn-cust-cat-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        if (btn) {
            btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
        } else {
            const firstBtn = document.querySelector('.btn-cust-cat-filter');
            if (firstBtn) {
                firstBtn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
                firstBtn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
            }
        }

        window.__custLayananCat = cat;
        applyCustLayananFilter();
    }
    window.filterCustLayananPageByCat = filterCustLayananPageByCat;
    window.applyCustLayananFilter = applyCustLayananFilter;
    window.toggleCustServiceDetail = toggleCustServiceDetail;
    window.filterLiveCustomerServices = filterLiveCustomerServices;

    // Kolom pencarian di dalam kartu katalog dihapus (sudah ada di header),
    // jadi filter cukup berdasarkan kategori.
    function applyCustLayananFilter() {
        const cat = window.__custLayananCat || 'all';
        const items = document.querySelectorAll('.layanan-cust-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemCat = item.getAttribute('data-cat') || '';
            const matchesCat = (cat === 'all' || itemCat === cat);

            if (matchesCat) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const empty = document.getElementById('layananCustKosong');
        if (empty) empty.classList.toggle('hidden', visibleCount > 0);

        const countEl = document.getElementById('jumlahLayananCust');
        if (countEl) countEl.innerText = `${visibleCount} Kategori`;
    }

    // Filter Pesanan di Tab Pesanan
    function filterOrderTab(status, btn) {
        document.querySelectorAll('.btn-order-tab-filter').forEach(b => {
            b.className = 'btn-order-tab-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700';
        });
        if (btn) {
            btn.className = 'btn-order-tab-filter px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white';
        } else {
            const matchBtn = document.querySelector(`.btn-order-tab-filter[data-status="${status}"]`) || document.getElementById('btnFilterOrderAll');
            if (matchBtn) matchBtn.className = 'btn-order-tab-filter px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white';
        }

        const items = document.querySelectorAll('.order-status-item');
        items.forEach(it => {
            const itemStatus = it.getAttribute('data-status');
            it.style.display = (status === 'all' || itemStatus === status) ? 'block' : 'none';
        });
    }
    window.filterOrderTab = filterOrderTab;

    // Fungsi pelacakan & pembukaan detail alur pesanan dari tab Pesanan / tombol Lacak
    function bukaDetailPesanan(code, status, layanan, biaya, alamat) {
        const cleanCode = String(code || '').replace('#', '').trim() || 'SB-SURUH-3819';
        let queryStatus = status || '';
        let queryLayanan = layanan || '';
        let queryBiaya = biaya || '';
        let queryAlamat = alamat || '';
        let queryMetode = 'Nego';
        let queryJadwal = '';

        try {
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                const item = list.find(o => String(o.code || o.id || '').replace('#', '').trim() === cleanCode);
                if (item) {
                    if (!queryStatus) queryStatus = item.status || '';
                    if (!queryLayanan) queryLayanan = item.layanan || '';
                    if (!queryBiaya) queryBiaya = item.nego_price || item.budget || item.biaya || item.agreed_price || '';
                    if (!queryAlamat) queryAlamat = item.alamat || '';
                    if (item.metode) queryMetode = item.metode;
                    if (item.jadwal) queryJadwal = item.jadwal;
                }
            }
            if (!queryStatus) {
                const rawAct = localStorage.getItem('sb_active_order');
                if (rawAct) {
                    const act = JSON.parse(rawAct);
                    if (act && String(act.code || act.id || '').replace('#', '').trim() === cleanCode) {
                        queryStatus = act.status || '';
                        if (!queryLayanan) queryLayanan = act.layanan || '';
                        if (!queryBiaya) queryBiaya = act.nego_price || act.budget || act.biaya || act.agreed_price || '';
                        if (!queryAlamat) queryAlamat = act.alamat || '';
                        if (act.metode) queryMetode = act.metode;
                        if (act.jadwal) queryJadwal = act.jadwal;
                    }
                }
            }
        } catch(e) {}

        const q = new URLSearchParams({
            status: queryStatus || 'berjalan',
            layanan: queryLayanan || 'Full Home Cleaning',
            biaya: queryBiaya || '75.000',
            alamat: queryAlamat || 'depok lenteng agung',
            metode: queryMetode,
            jadwal: queryJadwal || '2026-11-02 • 14:30 WIB'
        });

        const targetUrl = `/customer/pesanan/${encodeURIComponent(cleanCode)}?${q.toString()}`;
        if (typeof spaNavigateTo === 'function') {
            spaNavigateTo(targetUrl);
        } else {
            window.location.href = targetUrl;
        }
    }
    window.bukaDetailPesanan = bukaDetailPesanan;

    function lacakTiketCustomer() {
        const input = document.getElementById('custOrderFilterInput');
        let code = (input ? input.value : '').trim().toUpperCase().replace('#', '');
        if (!code) {
            let act = null;
            try { act = JSON.parse(localStorage.getItem('sb_active_order')); } catch(e) {}
            if (act && (act.code || act.id)) {
                code = String(act.code || act.id).replace('#', '').trim();
            } else {
                const firstItem = document.querySelector('.order-status-item');
                if (firstItem) {
                    const match = firstItem.innerText.match(/#?(SB-SURUH-\d+)/i);
                    if (match) code = match[1];
                }
            }
            if (!code) code = 'SB-SURUH-3819';
        }

        bukaDetailPesanan(code);
    }
    window.lacakTiketCustomer = lacakTiketCustomer;

    // Tambah review baru ke live review list
    function addCustomerReviewToList(name, service, comment, rating) {
        const list = document.getElementById('customerReviewList');
        if (!list) return;

        const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        const card = document.createElement('div');
        card.className = 'card-sb p-3 border border-amber-300 bg-amber-50/20 text-xs space-y-1.5 animate-fadeIn';
        card.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-bold text-[10px] flex items-center justify-center">
                        ${name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <strong class="text-[11px] font-bold text-slate-900 block leading-none">${name}</strong>
                        <span class="text-[8.5px] text-emerald-600 font-bold">Baru Saja • Ulasan Anda</span>
                    </div>
                </div>
                <span class="text-[10px] font-bold"><span class="text-yellow-400">${stars}</span> ${rating}.0</span>
            </div>
            <p class="text-[10.5px] text-slate-800 leading-snug italic">
                "${comment}"
            </p>
            <div class="flex items-center justify-between text-[9px] text-slate-400 pt-1 border-t border-slate-100">
                <span>Layanan: <strong>${service}</strong></span>
                <span class="text-emerald-700 font-semibold">● Terverifikasi</span>
            </div>
        `;
        list.prepend(card);
    }
    // =========================================================
    //  FORM BUAT SURUHAN BARU (DASHBOARD CUSTOMER)
    //  Fungsi paralel dengan beranda publik, tapi memakai prefix
    //  "cust" supaya tidak bentrok dengan index.blade.php.
    // =========================================================

    // =========================================================
    //  KATALOG DATA RESMI UNTUK ALUR PENGAJUAN SURUHAN
    // =========================================================
    const SB_CATALOG_DATA = {
        'Full Home Cleaning': {
            name: 'Full Home Cleaning',
            category: 'Kebersihan',
            desc: 'Pembersihan rumah menyeluruh meliputi kamar tidur, ruang tamu, lantai dan debu.',
            catalogPrice: '85.000',
            minPrice: '70.000',
            maxPrice: '120.000',
            icon: 'fa-broom'
        },
        'Basic Daily Cleaning': {
            name: 'Basic Daily Cleaning',
            category: 'Kebersihan',
            desc: 'Sapu, pel lantai rutin, dan lap meja/kaca ruangan.',
            catalogPrice: '50.000',
            minPrice: '40.000',
            maxPrice: '75.000',
            icon: 'fa-spray-can-sparkles'
        },
        'Bersihkan Kamar Mandi': {
            name: 'Bersihkan Kamar Mandi',
            category: 'Kebersihan',
            desc: 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.',
            catalogPrice: '65.000',
            minPrice: '50.000',
            maxPrice: '90.000',
            icon: 'fa-shower'
        },
        'Cuci Piring & Dapur Bersih': {
            name: 'Cuci Piring & Dapur Bersih',
            category: 'Kebersihan',
            desc: 'Bantu cuci piring tumpuk, kompor bersih & wastafel kinclong.',
            catalogPrice: '45.000',
            minPrice: '35.000',
            maxPrice: '70.000',
            icon: 'fa-sink'
        },
        'Pindahan': {
            name: 'Pindahan Kos & Rumah',
            category: 'Pindahan & Angkut',
            desc: 'Bantu packing, angkat muatan barang pindahan kos/rumah ke kendaraan.',
            catalogPrice: '150.000',
            minPrice: '120.000',
            maxPrice: '250.000',
            icon: 'fa-boxes-packing'
        },
        'Angkut Barang': {
            name: 'Angkut Barang / Beban Berat',
            category: 'Pindahan & Angkut',
            desc: 'Angkat lemari, kulkas, kasur, atau tata ulang perabotan.',
            catalogPrice: '80.000',
            minPrice: '60.000',
            maxPrice: '130.000',
            icon: 'fa-dolly'
        },
        'Buang Sampah': {
            name: 'Buang Sampah / Puing',
            category: 'Pindahan & Angkut',
            desc: 'Buang puing renovasi, sampah daun taman, atau barang rongsok.',
            catalogPrice: '40.000',
            minPrice: '35.000',
            maxPrice: '70.000',
            icon: 'fa-trash-can'
        },
        'Antar / Jemput': {
            name: 'Antar / Jemput Barang & Dokumen',
            category: 'Antar, Belanja & Antre',
            desc: 'Antar barang, dokumen penting atau ambil laundry kilat.',
            catalogPrice: '50.000',
            minPrice: '35.000',
            maxPrice: '80.000',
            icon: 'fa-motorcycle'
        },
        'SanSu Food': {
            name: 'SanSu Food (Belanja Pasar / Makanan)',
            category: 'Antar, Belanja & Antre',
            desc: 'Belanja sayur segar subuh pasar tradisional atau supermarket.',
            catalogPrice: '45.000',
            minPrice: '35.000',
            maxPrice: '75.000',
            icon: 'fa-basket-shopping'
        },
        'Titip Antre RS & Faskes': {
            name: 'Titip Antre RS, Faskes & Tiket',
            category: 'Antar, Belanja & Antre',
            desc: 'Ambilkan nomor antrean dokter, faskes, atau antre tiket event.',
            catalogPrice: '60.000',
            minPrice: '45.000',
            maxPrice: '90.000',
            icon: 'fa-hourglass-half'
        },
        'Nemenin Olahraga': {
            name: 'Nemenin Olahraga (Jogging / Gym)',
            category: 'Kegiatan Harian',
            desc: 'Teman jogging pagi, gym, gowes atau jaga stand toko.',
            catalogPrice: '60.000',
            minPrice: '45.000',
            maxPrice: '90.000',
            icon: 'fa-person-running'
        },
        'Bantu Kegiatan Harian': {
            name: 'Bantu Kegiatan Harian',
            category: 'Kegiatan Harian',
            desc: 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.',
            catalogPrice: '60.000',
            minPrice: '45.000',
            maxPrice: '90.000',
            icon: 'fa-handshake'
        }
    };

    let custCurrentStep = 1;
    let custCurrentPricingMethod = 'Nego'; // 'Nego' | 'Pas'

    // Toggle 11 Alur Explanation
    function toggleCustAlurOverview() {
        const box = document.getElementById('custAlurOverviewBox');
        const chev = document.getElementById('custAlurChevron');
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

    // Append quick note to textarea
    function appendCustNote(text) {
        const noteEl = document.getElementById('custFormNote');
        if (!noteEl) return;
        if (noteEl.value.trim() === '') {
            noteEl.value = text;
        } else {
            noteEl.value = noteEl.value.trim() + ' ' + text;
        }
        noteEl.focus();
    }

    // Service selection handler
    function onCustServiceSelected(serviceName) {
        const data = SB_CATALOG_DATA[serviceName] || {
            name: serviceName,
            category: 'Layanan SayaBantu',
            desc: 'Layanan bantuan tugas cepat SayaBantu.',
            catalogPrice: '85.000',
            minPrice: '60.000',
            maxPrice: '120.000',
            icon: 'fa-handshake'
        };

        // Update preview card in Step 1 (tanpa menampilkan harga layanan)
        const previewCard = document.getElementById('custServicePreviewCard');
        if (previewCard) {
            previewCard.classList.remove('hidden');
            const titleEl = document.getElementById('custPreviewTitle');
            const catEl = document.getElementById('custPreviewCat');
            const descEl = document.getElementById('custPreviewDesc');
            const iconEl = document.getElementById('custPreviewIcon');

            if (titleEl) titleEl.innerText = data.name;
            if (catEl) catEl.innerText = 'Kategori: ' + data.category;
            if (descEl) descEl.innerText = data.desc;
            if (iconEl) iconEl.innerHTML = `<i class="fa-solid ${data.icon}"></i>`;
        }

        // Update Step 2 info
        const negoRangeNotice = document.getElementById('custNegoRangeNotice');
        if (negoRangeNotice) {
            negoRangeNotice.innerText = `Rp ${data.minPrice} - Rp ${data.maxPrice}`;
        }
        const pasLabel = document.getElementById('custPasServiceLabel');
        const pasDisplay = document.getElementById('custPasPriceDisplay');
        if (pasLabel) pasLabel.innerText = data.name;
        if (pasDisplay) pasDisplay.innerText = 'Rp ' + data.catalogPrice;

        // Update presets buttons
        const btnMin = document.getElementById('btnPresetMin');
        const btnMid = document.getElementById('btnPresetMid');
        const btnMax = document.getElementById('btnPresetMax');
        if (btnMin) btnMin.innerText = 'Min: Rp ' + data.minPrice;
        if (btnMid) btnMid.innerText = 'Rekomendasi (' + data.catalogPrice + ')';
        if (btnMax) btnMax.innerText = 'Maks: Rp ' + data.maxPrice;

        // Auto fill budget input if empty or in 'Pas' mode
        const budgetInput = document.getElementById('custFormBudget');
        if (budgetInput) {
            if (custCurrentPricingMethod === 'Pas' || !budgetInput.value) {
                budgetInput.value = data.catalogPrice;
            }
        }
    }

    // Select pricing method: 'Nego' | 'Pas'
    function selectCustPricingMethod(method) {
        custCurrentPricingMethod = method;
        const selNego = document.getElementById('custFormNego');
        if (selNego) selNego.value = method;

        const cardNego = document.getElementById('cardMetodeNego');
        const cardPas = document.getElementById('cardMetodePas');
        const dotNego = document.getElementById('dotMetodeNego');
        const dotPas = document.getElementById('dotMetodePas');
        const boxNego = document.getElementById('boxDetailNego');
        const boxPas = document.getElementById('boxDetailPas');
        const budgetInput = document.getElementById('custFormBudget');

        const serviceName = (document.getElementById('custFormLayanan') || {}).value;
        const catalogInfo = SB_CATALOG_DATA[serviceName] || { catalogPrice: '85.000' };

        if (method === 'Nego') {
            if (cardNego) {
                cardNego.className = 'p-3 rounded-xl border-2 border-[#173B67] bg-blue-50/50 cursor-pointer transition-all relative flex flex-col justify-between';
            }
            if (cardPas) {
                cardPas.className = 'p-3 rounded-xl border-2 border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all relative flex flex-col justify-between';
            }
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
            // Pas
            if (cardNego) {
                cardNego.className = 'p-3 rounded-xl border-2 border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all relative flex flex-col justify-between';
            }
            if (cardPas) {
                cardPas.className = 'p-3 rounded-xl border-2 border-emerald-600 bg-emerald-50/50 cursor-pointer transition-all relative flex flex-col justify-between';
            }
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

            if (budgetInput) {
                budgetInput.value = catalogInfo.catalogPrice;
            }
        }
        syncCustBudgetReview();
    }

    // Set budget preset in Nego mode
    function setCustNegoPreset(presetType) {
        const serviceName = (document.getElementById('custFormLayanan') || {}).value;
        const catalogInfo = SB_CATALOG_DATA[serviceName] || { minPrice: '70.000', catalogPrice: '85.000', maxPrice: '120.000' };
        const budgetInput = document.getElementById('custFormBudget');
        if (!budgetInput) return;

        if (presetType === 'min') {
            budgetInput.value = catalogInfo.minPrice;
        } else if (presetType === 'mid') {
            budgetInput.value = catalogInfo.catalogPrice;
        } else if (presetType === 'max') {
            budgetInput.value = catalogInfo.maxPrice;
        }
        syncCustBudgetReview();
    }

    // Sync budget review text
    function syncCustBudgetReview() {
        // Will be refreshed in populateCustFormReview()
    }

    // Step navigation
    function goToCustStep(step) {
        if (step > custCurrentStep) {
            // Validate intermediate steps before jumping forward
            if (custCurrentStep === 1 && !validateCustStep1()) return;
            if (custCurrentStep === 2 && !validateCustStep2()) return;
        }
        renderCustStep(step);
    }

    function nextCustStep(targetStep) {
        if (custCurrentStep === 1 && !validateCustStep1()) return;
        if (custCurrentStep === 2 && !validateCustStep2()) return;
        renderCustStep(targetStep);
    }
    window.nextCustStep = nextCustStep;

    function prevCustStep(targetStep) {
        renderCustStep(targetStep);
    }
    window.prevCustStep = prevCustStep;

    function goToCustStep(step) {
        if (step > custCurrentStep) {
            if (custCurrentStep === 1 && !validateCustStep1()) return;
            if (custCurrentStep === 2 && !validateCustStep2()) return;
        }
        renderCustStep(step);
    }
    window.goToCustStep = goToCustStep;

    function validateCustStep1() {
        const elLayanan = document.getElementById('custFormLayanan');
        const layanan = (elLayanan || {}).value;
        if (!layanan) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pilih Layanan', 'Silakan pilih salah satu layanan katalog yang tersedia.', 'warning');
            } else {
                alert('Silakan pilih salah satu layanan katalog yang tersedia.');
            }
            if (elLayanan) {
                elLayanan.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                elLayanan.focus();
                elLayanan.addEventListener('change', () => {
                    elLayanan.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                }, { once: true });
            }
            return false;
        }

        const elNote = document.getElementById('custFormNote');
        const note = (elNote || {}).value;
        if (!note || !note.trim()) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Detail Kebutuhan', 'Mohon isi detail pekerjaan atau instruksi suruhan Anda.', 'warning');
            } else {
                alert('Mohon isi detail pekerjaan atau instruksi suruhan Anda.');
            }
            if (elNote) {
                elNote.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                elNote.focus();
                elNote.addEventListener('input', () => {
                    elNote.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                }, { once: true });
            }
            return false;
        }

        const elTanggal = document.getElementById('custFormTanggal');
        if (elTanggal && !elTanggal.value) {
            elTanggal.value = new Date().toISOString().split('T')[0];
        }

        const elAlamat = document.getElementById('custFormAlamat');
        const alamat = (elAlamat || {}).value;
        if (!alamat || !alamat.trim()) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Alamat Lokasi', 'Mohon masukkan alamat lengkap atau patokan lokasi pengerjaan.', 'warning');
            } else {
                alert('Mohon masukkan alamat lengkap atau patokan lokasi pengerjaan.');
            }
            if (elAlamat) {
                elAlamat.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                elAlamat.focus();
                elAlamat.addEventListener('input', () => {
                    elAlamat.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                }, { once: true });
            }
            return false;
        }

        return true;
    }
    window.validateCustStep1 = validateCustStep1;

    function setCustQuickNominal(nominal) {
        const elBudget = document.getElementById('custFormBudget');
        if (!elBudget) return;
        elBudget.value = Number(nominal).toLocaleString('id-ID');
        elBudget.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
        const alertEl = document.getElementById('custBudgetMinAlert');
        if (alertEl) alertEl.classList.add('hidden');
        syncCustBudgetReview();
    }
    window.setCustQuickNominal = setCustQuickNominal;

    function validateCustStep2() {
        const elBudget = document.getElementById('custFormBudget');
        const budget = (elBudget || {}).value;
        const rawBudgetNum = parseInt((budget || '').replace(/[^0-9]/g, '')) || 0;
        const alertEl = document.getElementById('custBudgetMinAlert');

        if (!budget || budget.trim() === '' || rawBudgetNum < 10000) {
            if (alertEl) alertEl.classList.remove('hidden');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Biaya Minimal', 'Nominal biaya suruhan minimal adalah Rp 10.000.', 'warning');
            } else {
                alert('Nominal biaya suruhan minimal adalah Rp 10.000.');
            }
            if (elBudget) {
                elBudget.classList.add('border-rose-500', 'ring-2', 'ring-rose-200');
                elBudget.focus();
                elBudget.addEventListener('input', () => {
                    const currentVal = parseInt(elBudget.value.replace(/[^0-9]/g, '')) || 0;
                    if (currentVal >= 10000) {
                        elBudget.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200');
                        if (alertEl) alertEl.classList.add('hidden');
                    }
                });
            }
            return false;
        }
        if (alertEl) alertEl.classList.add('hidden');
        return true;
    }
    window.validateCustStep2 = validateCustStep2;

    function renderCustStep(step) {
        custCurrentStep = step;

        const sec1 = document.getElementById('custStepSection1');
        const sec2 = document.getElementById('custStepSection2');
        const sec3 = document.getElementById('custStepSection3');

        if (sec1) sec1.classList.toggle('hidden', step !== 1);
        if (sec2) sec2.classList.toggle('hidden', step !== 2);
        if (sec3) sec3.classList.toggle('hidden', step !== 3);

        // Update Stepper Top Indicators
        for (let i = 1; i <= 3; i++) {
            const btn = document.getElementById('btnStepIndicator' + i);
            const badge = document.getElementById('badgeStepNumber' + i);
            if (!btn || !badge) continue;

            if (i === step) {
                // Active
                btn.className = 'step-indicator-btn py-1.5 px-1 rounded-xl bg-[#0E1D31] text-white flex flex-col items-center justify-center gap-0.5 transition-all shadow-xs';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-white text-[#0E1D31] text-[8px] flex items-center justify-center font-black';
                badge.innerHTML = i.toString();
            } else if (i < step) {
                // Completed
                btn.className = 'step-indicator-btn py-1.5 px-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 flex flex-col items-center justify-center gap-0.5 transition-all';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-emerald-600 text-white text-[8px] flex items-center justify-center font-bold';
                badge.innerHTML = '<i class="fa-solid fa-check text-[7px]"></i>';
            } else {
                // Inactive upcoming
                btn.className = 'step-indicator-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex flex-col items-center justify-center gap-0.5 transition-all';
                badge.className = 'w-3.5 h-3.5 rounded-full bg-slate-300 text-slate-700 text-[8px] flex items-center justify-center font-bold';
                badge.innerHTML = i.toString();
            }
        }

        // When entering Step 3, populate review fields
        if (step === 3) {
            populateCustFormReview();
            const chk = document.getElementById('custCheckTerms');
            if (chk) {
                toggleCustFormSubmit(chk.checked);
            }
        }

        // Scroll smoothly to form header
        const card = document.getElementById('custOrderFormCard');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    window.renderCustStep = renderCustStep;

    // Populate review summary card in Step 3
    function populateCustFormReview() {
        const layanan = (document.getElementById('custFormLayanan') || {}).value || '-';
        const tanggal = (document.getElementById('custFormTanggal') || {}).value || '-';
        const jam = (document.getElementById('custFormJam') || {}).value || '14';
        const menit = (document.getElementById('custFormMenit') || {}).value || '30';
        const alamat = (document.getElementById('custFormAlamat') || {}).value || '-';
        const note = (document.getElementById('custFormNote') || {}).value || '-';
        const budget = (document.getElementById('custFormBudget') || {}).value || '0';
        const gender = (document.querySelector('input[name="custFormGender"]') || {}).value || 'Bebas';
        const pendukung = (document.getElementById('custFormPendukung') || {}).value || '';

        const revLay = document.getElementById('reviewCustLayanan');
        const revWkt = document.getElementById('reviewCustWaktu');
        const revAlm = document.getElementById('reviewCustAlamat');
        const revNot = document.getElementById('reviewCustNote');
        const revBgt = document.getElementById('reviewCustBudget');
        const revGdr = document.getElementById('reviewCustGender');
        const revPen = document.getElementById('reviewCustPendukungWrapper');
        const revMetBadge = document.getElementById('reviewCustMetodeBadge');
        const revPriceNote = document.getElementById('reviewCustPriceNote');
        const revDurasi = document.getElementById('reviewCustDurasi');

        if (revLay) revLay.innerText = layanan;
        if (revWkt) revWkt.innerText = `${tanggal} • Pukul ${jam}:${menit} WIB`;
        if (revAlm) revAlm.innerText = alamat;
        if (revNot) revNot.innerText = note;
        if (revGdr) revGdr.innerText = gender;
        if (revPen) {
            revPen.innerText = pendukung ? `Catatan: ${pendukung}` : '';
        }

        const durasiVal = (document.getElementById('custFormDurasiCari') || {}).value || '1 Jam';
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

    // Sync input batas durasi cari mitra dari customer
    function syncCustDurasiInput() {
        const angkaEl = document.getElementById('custFormDurasiAngka');
        const satuanEl = document.getElementById('custFormDurasiSatuan');
        const badgeEl = document.getElementById('custDurasiPillBadge');
        const hiddenCari = document.getElementById('custFormDurasiCari');
        const hiddenMenit = document.getElementById('custFormDurasiMenit');
        const revDurasi = document.getElementById('reviewCustDurasi');

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
    window.syncCustDurasiInput = syncCustDurasiInput;

    function setCustDurasiPreset(angka, satuan, btn) {
        const angkaEl = document.getElementById('custFormDurasiAngka');
        const satuanEl = document.getElementById('custFormDurasiSatuan');
        if (angkaEl) angkaEl.value = angka;
        if (satuanEl) satuanEl.value = satuan;

        document.querySelectorAll('.btn-cust-durasi-chip').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white');
            b.classList.add('bg-white/90', 'text-amber-900');
        });
        if (btn) {
            btn.classList.remove('bg-white/90', 'text-amber-900');
            btn.classList.add('bg-[#0E1D31]', 'text-white');
        }

        syncCustDurasiInput();
    }
    window.setCustDurasiPreset = setCustDurasiPreset;

    // Date chips preset
    function setCustDatePreset(type, btn) {
        document.querySelectorAll('.btn-cust-date-chip').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-semibold');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-semibold');
        btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');

        const input = document.getElementById('custFormTanggal');
        const noticeBox = document.getElementById('custTodayNoticeBox');
        if (!input) return;
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

    // Toggle tombol submit berdasarkan checkbox syarat & ketentuan
    function toggleCustFormSubmit(checked) {
        const btn = document.getElementById('custBtnSubmitSuruhan');
        if (!btn) return;
        if (checked) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('cursor-pointer');
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('cursor-pointer');
        }
    }
    window.toggleCustFormSubmit = toggleCustFormSubmit;

    // Submit suruhan dari dashboard customer
    function handleCustSuruhanSubmit(e) {
        if (e && e.preventDefault) e.preventDefault();

        const chk = document.getElementById('custCheckTerms');
        if (chk && !chk.checked) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Persetujuan Diperlukan', 'Harap centang persetujuan syarat dan ketentuan terlebih dahulu.', 'warning');
            } else {
                alert('Harap centang persetujuan syarat dan ketentuan terlebih dahulu.');
            }
            if (chk.focus) chk.focus();
            return;
        }

        let user = (typeof getCustomerAuth === 'function') ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            try {
                const raw = localStorage.getItem('sb_customer_auth');
                if (raw) user = JSON.parse(raw);
            } catch(e) {}
            if (!user || !user.name || user.role === 'mitra') {
                user = {
                    isLoggedIn: true,
                    name: @json(($customerName && $customerName !== 'Customer') ? $customerName : (Auth::user()?->name ?? 'Customer')),
                    phone: @json($customerPhone ?: (Auth::user()?->phone ?? '')),
                    role: 'customer'
                };
            }
            user.isLoggedIn = true;
            user.role = 'customer';
            try {
                localStorage.removeItem('sb_customer_logged_out');
                localStorage.setItem('sb_customer_auth', JSON.stringify(user));
            } catch(e) {}
        }

        const tanggal = document.getElementById('custFormTanggal').value;
        const jam = document.getElementById('custFormJam').value;
        const menit = document.getElementById('custFormMenit').value;
        const layanan = document.getElementById('custFormLayanan').value;
        const alamat = document.getElementById('custFormAlamat').value;
        const note = document.getElementById('custFormNote').value;
        const budget = document.getElementById('custFormBudget').value;
        const durasiCari = (document.getElementById('custFormDurasiCari') || {}).value || '1 Jam';
        const durasiMenit = parseInt((document.getElementById('custFormDurasiMenit') || {}).value) || 60;

        const rawBudgetNum = parseInt((budget || '').replace(/[^0-9]/g, '')) || 0;
        if (rawBudgetNum < 10000) {
            goToCustStep(2);
            validateCustStep2();
            return;
        }

        const finalBudgetString = rawBudgetNum.toLocaleString('id-ID');

        const btn = document.getElementById('custBtnSubmitSuruhan');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyiarkan ke Mitra Terdekat...';
        }

        const randCode = '#SB-SURUH-' + Math.floor(1000 + Math.random() * 9000);
        const targetStatus = 'menunggu_mitra';
        const genderVal = 'Bebas';

        const orderData = {
            code: randCode,
            layanan: layanan,
            tanggal: tanggal,
            jam: `${jam}:${menit} WIB`,
            jadwal: `${tanggal} • ${jam}:${menit} WIB`,
            durasi_cari: durasiCari,
            durasi_menit: durasiMenit,
            alamat: alamat,
            note: note,
            budget: finalBudgetString,
            biaya: finalBudgetString,
            rawBudget: rawBudgetNum.toLocaleString('id-ID'),
            metode: 'Langsung',
            gender: genderVal,
            customer_name: user.name,
            customer_phone: user.phone,
            customerName: user.name,
            customerPhone: user.phone,
            status: targetStatus,
            step: 4,
            createdAt: new Date().toISOString()
        };

        const todayIso = new Date().toISOString().split('T')[0];
        const isTodayOrder = (tanggal === todayIso) || (tanggal.toLowerCase().includes('hari ini'));

        const postToServer = () => {
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
                } catch(e) {}

                if (typeof showCustomerToast === 'function') {
                    showCustomerToast('Pengajuan Berhasil!', 'Mencari mitra di sekitar lokasi Anda...', 'success');
                }

                const redirectUrl = res.redirect || `/customer/pesanan/${randCode.replace('#', '')}?${new URLSearchParams({
                    layanan: layanan,
                    jadwal: `${tanggal} • ${jam}:${menit} WIB`,
                    durasi_cari: durasiCari,
                    durasi_menit: durasiMenit,
                    biaya: finalBudgetString,
                    total: finalBudgetString,
                    metode: 'Langsung',
                    status: targetStatus,
                    alamat: alamat,
                    note: note,
                    gender: genderVal,
                }).toString()}`;

                if (isTodayOrder) {
                    openGojekSearchModal(orderData, redirectUrl);
                } else {
                    if (typeof spaNavigateTo === 'function') {
                        spaNavigateTo(redirectUrl);
                    } else {
                        window.location.href = redirectUrl;
                    }
                }
            })
            .catch(() => {
                // Fallback jika offline/koneksi
                try {
                    localStorage.setItem('sb_active_order', JSON.stringify(orderData));
                    let orders = [];
                    const rawList = localStorage.getItem('sb_customer_orders');
                    if (rawList) orders = JSON.parse(rawList);
                    orders.unshift(orderData);
                    localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                } catch(e) {}

                const fallbackUrl = `/customer/pesanan/${randCode.replace('#', '')}?${new URLSearchParams({
                    layanan: layanan,
                    jadwal: `${tanggal} • ${jam}:${menit} WIB`,
                    durasi_cari: durasiCari,
                    durasi_menit: durasiMenit,
                    biaya: finalBudgetString,
                    total: finalBudgetString,
                    metode: 'Langsung',
                    status: targetStatus,
                    alamat: alamat,
                    note: note,
                    gender: genderVal
                }).toString()}`;

                if (isTodayOrder) {
                    openGojekSearchModal(orderData, fallbackUrl);
                } else {
                    if (typeof spaNavigateTo === 'function') {
                        spaNavigateTo(fallbackUrl);
                    } else {
                        window.location.href = fallbackUrl;
                    }
                }
            });
        };

        postToServer();
    }
    window.handleCustSuruhanSubmit = handleCustSuruhanSubmit;

    let custRadarInterval = null;
    let custTickerInterval = null;

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

        if (custRadarInterval) clearInterval(custRadarInterval);
        custRadarInterval = setInterval(() => {
            totalSeconds--;
            if (totalSeconds < 0) {
                totalSeconds = 0;
                clearInterval(custRadarInterval);
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

        // Cycling ticker messages
        const tickerMessages = [
            'Menyiarkan suruhan ke mitra aktif dalam radius 5 km...',
            'Mitra terdekat sedang memeriksa penugasan Anda...',
            'Menunggu konfirmasi penerimaan dari mitra...',
            `Batas waktu pencarian ${durasiCari} sesuai pilihan Anda...`
        ];
        let tickerIdx = 0;
        const tickerEl = document.getElementById('sbRadarTicker');
        if (custTickerInterval) clearInterval(custTickerInterval);
        custTickerInterval = setInterval(() => {
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
        if (custRadarInterval) clearInterval(custRadarInterval);
        if (custTickerInterval) clearInterval(custTickerInterval);
    }
    window.closeGojekSearchModal = closeGojekSearchModal;
</script>
@endpush

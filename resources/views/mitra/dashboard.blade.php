@extends('layouts.mitra')

@section('title', 'SayaBantu - Portal Mitra SayaBantu.com')

@php
    $currentMitra = $currentMitra ?? null;
    $mitraAuthName = $currentMitra?->name ?? (session('mitra_auth.name') ?? 'Mitra');
    $mitraAuthId = $currentMitra?->mitra_id ?: (session('mitra_auth.mitra_id') ?: ($currentMitra?->id ? 'SB-' . str_pad($currentMitra->id, 4, '0', STR_PAD_LEFT) : (session('mitra_auth.id') ? 'SB-' . str_pad(session('mitra_auth.id'), 4, '0', STR_PAD_LEFT) : 'SB-1001')));
    $mitraAuthPhone = $currentMitra?->phone ?? (session('mitra_auth.phone') ?? '');
    $mitraAuthEmail = $currentMitra?->email ?: (session('mitra_auth.email') ?? '');
    $mitraAuthInitial = strtoupper(substr(trim($mitraAuthName), 0, 1));
    $mitraReviews = $mitraReviews ?? collect();
    $mitraCompletedOrders = $mitraCompletedOrders ?? collect();
    $mitraAuthRating = $currentMitra?->reviewed_orders_avg_review_rating === null
        ? ($mitraReviews->count() > 0 ? number_format((float) $mitraReviews->avg('review_rating'), 1) : null)
        : number_format((float) $currentMitra->reviewed_orders_avg_review_rating, 1);
    $mitraAuthReviewCount = max((int) ($currentMitra?->reviewed_orders_count ?? 0), $mitraReviews->count());
    $mitraAuthTasks = max((int) ($currentMitra?->completed_orders_count ?? 0), $mitraCompletedOrders->count(), (int) ($currentMitra?->tasks ?? 0));
    $mitraAuthArea = $currentMitra?->area ? ($currentMitra->kota ? $currentMitra->kota . ' (' . $currentMitra->area . ')' : $currentMitra->area) : ($currentMitra?->kota ?: 'Wilayah belum diatur');
    $mitraAuthKeahlian = $currentMitra?->keahlian ?: ($currentMitra?->service ?: 'Belum ada layanan');
@endphp

{{-- =========================================================
     1. HEADER MITRA (WARNA PERSIS CUSTOMER: NAVY & SKY BLUE)
     ☰ Burger Menu | SayaBantu | 🔍 Search | 🔔 Notifikasi | 👤 Profil
========================================================= --}}
@section('page-top')
<script>
    window.CURRENT_MITRA_DATA = {
        name: @json($mitraAuthName),
        id: @json($mitraAuthId),
        phone: @json($mitraAuthPhone),
        email: @json($mitraAuthEmail),
        initial: @json($mitraAuthInitial),
        rating: @json($mitraAuthRating),
        reviewCount: @json($mitraAuthReviewCount),
        tasks: @json($mitraAuthTasks),
        area: @json($mitraAuthArea),
        specialty: @json($mitraAuthKeahlian),
        status: @json($currentMitra?->status ?? (session('mitra_auth.status') ?? 'aktif'))
    };

    (function() {
        try {
            localStorage.removeItem('sb_mitra_logged_out');
            const serverProfile = window.CURRENT_MITRA_DATA;
            let u = {
                ...(serverProfile || {}),
                isLoggedIn: true,
                role: 'mitra',
                status: serverProfile?.status || 'approved'
            };
            localStorage.setItem('sb_mitra_auth', JSON.stringify(u));

            // Bersihkan sb_customer_auth jika pernah tertular sesi mitra
            try {
                const custRaw = localStorage.getItem('sb_customer_auth');
                if (custRaw) {
                    const c = JSON.parse(custRaw);
                    if (c && c.role === 'mitra') {
                        localStorage.removeItem('sb_customer_auth');
                    }
                }
            } catch(e) {}

            // Validasi status secara realtime ke database SQLite
            const phoneOrId = u.phone || u.id || u.mitra_id;
            if (phoneOrId) {
                fetch(`/api/mitra/status?identifier=${encodeURIComponent(phoneOrId)}`)
                    .then(r => r.json())
                    .then(res => {
                        if (res && res.status) {
                            if (res.status === 'verifikasi') {
                                u.status = 'verifikasi';
                                u.isLoggedIn = false;
                                localStorage.setItem('sb_mitra_auth', JSON.stringify(u));
                                alert("Pendaftaran Akun Mitra Anda Masih Menunggu Verifikasi & ACC Admin!\n\nAkun Anda belum disetujui oleh admin. Anda baru dapat mengakses Portal Mitra setelah disetujui & di-ACC oleh admin SayaBantu.");
                                window.location.replace("{{ route('mitra.login') }}");
                            } else if (res.status === 'aktif' || res.status === 'approved') {
                                u.status = 'aktif';
                                u.isLoggedIn = true;
                                localStorage.setItem('sb_mitra_auth', JSON.stringify(u));
                            } else if (res.status === 'suspend' || res.status === 'ditolak') {
                                u.isLoggedIn = false;
                                localStorage.setItem('sb_mitra_auth', JSON.stringify(u));
                                alert("Akun mitra Anda saat ini dinonaktifkan / suspend oleh admin.");
                                window.location.replace("{{ route('mitra.login') }}");
                            }
                        }
                    })
                    .catch(() => {});
            }
        } catch (e) {}
    })();
</script>
<div class="mitra-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- BAGIAN PALING ATAS: HEADER UTAMA --}}
    <header class="relative z-20 px-5 pt-4">
        <div class="flex items-center justify-between w-full">

            {{-- KIRI: ☰ BURGER MENU & BRAND --}}
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="openMitraBurgerDrawer()"
                    class="header-icon shrink-0"
                    title="Menu Fitur Tambahan Mitra"
                    aria-label="Burger Menu"
                >
                    <i class="fa-solid fa-bars text-[16px]"></i>
                </button>

                <div class="flex items-center gap-1.5">
                    <h1 class="text-[17px] font-bold tracking-tight text-white leading-none">
                        SayaBantu<span class="text-white">.com</span>
                    </h1>
                    <span class="text-[8.5px] font-black uppercase px-1.5 py-0.5 rounded bg-sky-500/25 text-sky-200 border border-sky-400/30">
                        Mitra
                    </span>
                </div>
            </div>

            {{-- KANAN: 🔍 SEARCH | 🔔 NOTIFIKASI | 👤 PROFIL --}}
            <div class="flex items-center gap-1.5">
                {{-- 🔍 SEARCH: MENCARI KEBUTUHAN / PEKERJAAN --}}
                <button
                    type="button"
                    onclick="openMitraSearchModal()"
                    class="header-icon shrink-0"
                    title="Cari Kebutuhan / Pekerjaan"
                    aria-label="Search Kebutuhan"
                >
                    <i class="fa-solid fa-magnifying-glass text-[14px]"></i>
                </button>

                {{-- 🔔 NOTIFIKASI --}}
                <button
                    type="button"
                    onclick="openMitraNotifModal()"
                    class="header-icon shrink-0 relative"
                    title="Notifikasi Mitra"
                    aria-label="Notifikasi"
                >
                    <i class="fa-regular fa-bell text-[14px]"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-[#0E1D31]"></span>
                </button>

                {{-- 👤 PROFIL: POPUP PROFIL SINGKAT MITRA --}}
                <button
                    type="button"
                    onclick="openMitraQuickProfile()"
                    class="header-icon shrink-0 !p-0.5 overflow-hidden ring-1 ring-white/30"
                    title="Profil Singkat Mitra"
                    aria-label="Profil Mitra"
                >
                    <div id="headerMitraAvatarInitial" class="w-full h-full rounded-full bg-gradient-to-tr from-sky-400 to-[#173B67] text-white font-black text-xs flex items-center justify-center">
                        {{ $mitraAuthInitial }}
                    </div>
                </button>
            </div>

        </div>
    </header>

    {{-- GREETING & STATUS STANDBY AREA (HANYA TAMPIL DI TAB DASHBOARD) --}}
    <section id="mitraGreetingBanner" class="relative z-10 px-5 pt-3 pb-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[9px] font-bold text-sky-200">
                    <i class="fa-solid fa-shield-halved text-[8px]"></i> Mitra Terverifikasi • <span id="dashMitraIdBadge">{{ $mitraAuthId }}</span>
                </span>
                <h1 class="mt-1.5 text-[18px] font-extrabold tracking-tight text-white leading-tight">
                    Halo, <span class="text-sky-300" id="dashMitraName">{{ $mitraAuthName }}</span> 👋
                </h1>
            </div>

            {{-- TOGGLE STATUS STANDBY / ONLINE --}}
            <button
                type="button"
                id="btnToggleStatusMitra"
                onclick="toggleStatusMitraStandby()"
                class="shrink-0 py-1.5 px-3 rounded-full bg-emerald-500 text-white font-extrabold text-[10px] shadow-sm flex items-center gap-1.5 border border-emerald-300/40 transition-all active:scale-95 cursor-pointer"
            >
                <span id="dotStatusMitra" class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span id="textStatusMitra">Online (Siap Kerja)</span>
            </button>
        </div>
        <p class="mt-2 text-[10px] text-white/80 leading-relaxed">
            Pantau kebutuhan suruhan masuk, pilih customer yang cocok, dan selesaikan pekerjaan.
        </p>
    </section>

    {{-- DYNAMIC TAB HEADER BANNER (TAMPIL DI TAB KEBUTUHAN, PEKERJAAN, PROFIL, SALDO, DLL SUPAYA GRADIENT SELALU UTUH & SERAGAM) --}}
    <section id="mitraTabHeaderBanner" class="relative z-10 px-5 pt-3 pb-6 hidden space-y-1">
        <div class="flex items-center justify-between gap-2">
            <h1 class="text-[18px] font-extrabold tracking-tight text-white leading-tight min-w-0" id="mitraTabTitle">
                Kebutuhan Masuk
            </h1>
            <span class="inline-flex items-center text-[9px] font-bold px-2.5 py-1 rounded-full border bg-sky-500/20 text-white border-sky-400/40 whitespace-nowrap shrink-0" id="mitraTabBadge">
                5 Permintaan Baru
            </span>
        </div>
        <p class="text-[10px] text-white/80 leading-relaxed" id="mitraTabDesc">
            Pilih kebutuhan customer yang cocok dengan keahlian Anda, lalu ambil pekerjaan secara langsung tanpa perlu tawar harga.
        </p>
    </section>

</div>
@endsection

{{-- =========================================================
     2. CONTENT AREA (4 TAB UTAMA + 6 HALAMAN LENGKAP BURGER MENU)
========================================================= --}}
@section('content')
<main id="mitraMainContent" class="content-under-gradient px-4 pt-2 pb-8">

    {{-- =========================================================================
         VIEW 1: 🏠 DASHBOARD MITRA
         Status Mitra | Saldo Mitra | Kebutuhan Spesialisasi | Penawaran Diproses | Pekerjaan Berlangsung | Ringkasan Selesai
    ========================================================================= --}}
    <div id="viewMitraDashboard" class="space-y-3.5">

        {{-- SALDO MITRA & PENARIKAN DANA --}}
        <section class="card-sb p-4 bg-gradient-to-br from-[#0E1D31] via-[#17314A] to-[#173B67] text-white border-0 shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-sky-400/10 blur-lg pointer-events-none"></div>
            
            <div class="relative z-10 flex items-start justify-between">
                <div>
                    <span class="text-[9.5px] font-bold text-sky-200 uppercase tracking-wider block">Saldo Mitra Aktif</span>
                    <strong id="mitraHeroSaldoText" class="text-2xl font-black text-white block mt-0.5 tracking-tight">Rp {{ number_format($saldoMitra ?? 0, 0, ',', '.') }}</strong>
                    <span id="mitraHeroSaldoSub" class="text-[9px] text-sky-200/80 font-medium block mt-0.5">
                        <i class="fa-solid fa-arrow-trend-up text-emerald-400 mr-0.5"></i> {{ number_format($saldoMitra ?? 0, 0, ',', '.') }} dari dana yang telah diteruskan
                    </span>
                </div>

                <div class="flex flex-col gap-1.5 shrink-0">
                    <button
                        type="button"
                        onclick="switchMitraNav('saldo')"
                        class="px-3 py-1.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] active:scale-95 text-white text-[10px] font-black shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer border border-white/20"
                    >
                        <i class="fa-solid fa-wallet text-[9px] text-white"></i>
                        <span class="text-white">Tarik Dana</span>
                    </button>
                    <button
                        type="button"
                        onclick="switchMitraNav('saldo')"
                        class="px-3 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-white text-[9px] font-bold border border-white/15 transition-all text-center cursor-pointer"
                    >
                        <span class="text-white">Riwayat Saldo</span>
                    </button>
                </div>
            </div>
        </section>

        {{-- STATUS PEKERJAAN BERLANGSUNG --}}
        <div id="mitraBerandaOngoingBox">
            <section id="mitraBerandaEmptyOngoing" class="card-sb p-3.5 bg-slate-50/70 border border-slate-200/90 text-center space-y-2">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                    <i class="fa-solid fa-person-digging"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-700">Belum Ada Pekerjaan Berlangsung</h4>
                    <p class="text-[9.5px] text-slate-400">Pekerjaan aktif yang sedang Anda kerjakan akan muncul di sini.</p>
                </div>
            </section>
            <div id="mitraBerandaOngoingCard" class="hidden"></div>
        </div>

        {{-- PENAWARAN YANG SEDANG DIPROSES --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-hourglass-half text-amber-500"></i>
                        <span>Penawaran Sedang Diproses</span>
                    </h3>
                    <p class="text-[9px] text-slate-400">Menunggu konfirmasi dan persetujuan dari customer</p>
                </div>
                <span id="mitraBadgePenawaranCount" class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                    0 Menunggu
                </span>
            </div>

            <div id="mitraBerandaOffersList" class="space-y-2"></div>
            <div id="mitraBerandaEmptyOffers" class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-center text-[10px] text-slate-400">
                Belum ada penawaran yang diajukan.
            </div>
        </section>

        {{-- KEBUTUHAN SESUAI SPESIALISASI MITRA --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-wand-magic-sparkles text-[#173B67]"></i>
                        <span>Kebutuhan Sesuai Spesialisasi</span>
                    </h3>
                    <p class="text-[9px] text-slate-400">Suruhan baru terdekat yang cocok dengan keahlian Anda</p>
                </div>
                <button
                    type="button"
                    onclick="switchMitraNav('kebutuhan')"
                    class="text-[9.5px] font-bold text-[#173B67] hover:underline cursor-pointer"
                >
                    Lihat Semua →
                </button>
            </div>

            <div class="space-y-2">
                {{-- WADAH SURUHAN REAL-TIME DARI CUSTOMER --}}
                <div id="mitraDashboardDynamicOrdersList" class="space-y-2"></div>

                <div id="mitraBerandaEmptyKebutuhan" class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                    <i class="fa-solid fa-clipboard-check text-slate-300 text-sm"></i>
                    <p class="text-[10px] font-bold text-slate-600">Belum Ada Kebutuhan Baru</p>
                    <p class="text-[9px] text-slate-400">Permintaan suruhan baru dari customer akan muncul di sini.</p>
                </div>
            </div>
        </section>

        {{-- RINGKASAN PEKERJAAN YANG TELAH SELESAI --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-simple text-[#173B67]"></i>
                <span>Ringkasan Pekerjaan Selesai</span>
            </h3>

            <div class="grid grid-cols-3 gap-2 text-center">
                <button type="button" onclick="showMitraStatDetail('tuntas')" title="Detail pekerjaan tuntas"
                    class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 cursor-pointer hover:border-sky-300 hover:shadow-xs active:scale-[0.97] transition-all">
                    <strong id="statRingkasanTotalTuntas" class="text-sm font-black text-slate-900 block">{{ $mitraAuthTasks }}</strong>
                    <span class="text-[8.5px] text-slate-400 block mt-0.5">Total Tuntas</span>
                </button>
                <button type="button" onclick="showMitraStatDetail('rating')" title="Detail ulasan pelanggan"
                    class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-100 cursor-pointer hover:border-amber-400 hover:shadow-xs active:scale-[0.97] transition-all">
                    <strong id="statRingkasanRating" class="text-sm font-black text-amber-600 block">
                        {{ $mitraAuthRating !== null ? $mitraAuthRating : '-' }} <span class="text-yellow-400">★</span>
                    </strong>
                    <span id="statRingkasanUlasanCount" class="text-[8.5px] text-amber-800/80 block mt-0.5">{{ $mitraAuthReviewCount }} Ulasan</span>
                </button>
                <button type="button" onclick="showMitraStatDetail('penyelesaian')" title="Detail tingkat penyelesaian"
                    class="p-2.5 rounded-xl bg-sky-50/70 border border-sky-100 cursor-pointer hover:border-sky-400 hover:shadow-xs active:scale-[0.97] transition-all">
                    <strong id="statRingkasanPenyelesaian" class="text-sm font-black text-[#173B67] block">{{ $mitraAuthTasks > 0 ? '100%' : '0%' }}</strong>
                    <span class="text-[8.5px] text-slate-500 block mt-0.5">Penyelesaian</span>
                </button>
            </div>
        </section>

        {{-- RINGKASAN TRANSAKSI --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt text-[#173B67]"></i>
                        <span>Ringkasan Transaksi</span>
                    </h3>
                    <p class="text-[9px] text-slate-400">Daftar transaksi dan pendapatan dari pekerjaan yang selesai</p>
                </div>
                <button
                    type="button"
                    onclick="switchMitraNav('saldo')"
                    class="text-[9.5px] font-bold text-[#173B67] hover:underline cursor-pointer"
                >
                    Riwayat Saldo →
                </button>
            </div>

            <div id="mitraDashboardTransaksiList" class="space-y-2">
                @forelse($mitraCompletedOrders as $trxOrder)
                    @php
                        $nom = $trxOrder->mitra_fee ?: (int) round(($trxOrder->nominal ?: 20000) * 0.9);
                    @endphp
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-sky-300 transition-all flex items-start justify-between gap-2.5">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-[#173B67]">{{ $trxOrder->code }}</span>
                                <span class="text-[10px] font-bold text-slate-800 truncate">{{ $trxOrder->layanan }}</span>
                            </div>
                            <p class="text-[9px] text-slate-500">Customer: <strong class="text-slate-700">{{ $trxOrder->customer_name ?: 'Customer' }}</strong> • {{ $trxOrder->jadwal ?: ($trxOrder->updated_at ? $trxOrder->updated_at->format('d M Y, H:i') : '') }}</p>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if(in_array($trxOrder->escrow_status, ['released_to_mitra', 'released'], true))
                                    <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                        <i class="fa-solid fa-check-double text-[8px]"></i> Dana Diteruskan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200">
                                        <i class="fa-solid fa-clock text-[8px]"></i> Selesai (Dana di Escrow)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[8.5px] text-slate-400 block">Pendapatan</span>
                            <strong class="text-xs font-black text-emerald-600 block">Rp {{ number_format($nom, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <div id="mitraDashboardEmptyTransaksi" class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                        <i class="fa-solid fa-receipt text-slate-300 text-sm"></i>
                        <p class="text-[10px] font-bold text-slate-600">Belum Ada Transaksi</p>
                        <p class="text-[9px] text-slate-400">Ringkasan transaksi pekerjaan selesai akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ULASAN DARI CUSTOMER --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-amber-500"></i>
                        <span>Ulasan dari Customer</span>
                    </h3>
                    <p class="text-[9px] text-slate-400">Penilaian dan kepuasan pelanggan atas layanan Anda</p>
                </div>
                <button
                    type="button"
                    onclick="switchMitraNav('ulasan')"
                    class="text-[9.5px] font-bold text-[#173B67] hover:underline cursor-pointer"
                >
                    Lihat Semua →
                </button>
            </div>

            <div id="mitraDashboardUlasanList" class="space-y-2">
                @forelse($mitraReviews as $rev)
                    <div class="p-3 rounded-xl bg-amber-50/40 border border-amber-200/70 hover:border-amber-400 transition-all space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-[#173B67] text-white text-[9px] font-bold flex items-center justify-center">
                                    {{ strtoupper(substr($rev->customer_name ?: 'C', 0, 1)) }}
                                </div>
                                <strong class="text-[10px] font-bold text-slate-800">{{ $rev->customer_name ?: 'Customer' }}</strong>
                            </div>
                            <span class="text-amber-500 text-[10px] font-bold flex items-center gap-0.5">
                                {{ str_repeat('★', (int) $rev->review_rating) }}{{ str_repeat('☆', 5 - (int) $rev->review_rating) }}
                                <span class="ml-1 text-[9px] text-slate-600 font-semibold">{{ number_format((float) $rev->review_rating, 1) }}</span>
                            </span>
                        </div>
                        @if($rev->review_comment)
                            <p class="text-[10px] text-slate-700 italic bg-white/70 p-2 rounded-lg border border-amber-100/60 leading-relaxed">
                                "{{ $rev->review_comment }}"
                            </p>
                        @endif
                        <div class="flex items-center justify-between text-[8.5px] text-slate-400">
                            <span>{{ $rev->layanan }}</span>
                            <span>{{ $rev->reviewed_at ? $rev->reviewed_at->format('d M Y, H:i') : '' }}</span>
                        </div>
                    </div>
                @empty
                    <div id="mitraDashboardEmptyUlasan" class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                        <i class="fa-solid fa-star text-slate-300 text-sm"></i>
                        <p class="text-[10px] font-bold text-slate-600">Belum Ada Ulasan</p>
                        <p class="text-[9px] text-slate-400">Ulasan dan rating dari customer akan muncul di sini setelah pekerjaan selesai.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    {{-- =========================================================================
         VIEW 2: 📋 KEBUTUHAN (FITUR UTAMA MITRA)
         Daftar Kebutuhan Layanan Customer | Filter Spesialisasi & Lokasi | Detail & Ajukan Penawaran
    ========================================================================= --}}
    <div id="viewMitraKebutuhan" class="hidden space-y-3.5">

        @include('mitra.partials.page-header', [
            'judul' => 'Kebutuhan Masuk',
            'deskripsi' => 'Pilih kebutuhan customer yang cocok dengan keahlian Anda, lalu ambil pekerjaan secara langsung tanpa perlu tawar harga.',
            'badge' => '0 Permintaan Baru',
            'badgeColor' => 'sky',
        ])

        {{-- FILTER KATEGORI SPESIALISASI --}}
        <div class="space-y-1.5">
            <div class="flex items-center justify-between text-[9px] px-1 text-white font-bold uppercase">
                <span class="text-white">Filter Spesialisasi</span>
                <span id="labelJumlahKebutuhan" class="text-white">0 Kebutuhan Tersedia</span>
            </div>
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
                <button type="button" onclick="filterKebutuhanCat('all', this)" class="btn-keb-filter px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 shadow-2xs cursor-pointer">
                    Semua
                </button>
                <button type="button" onclick="filterKebutuhanCat('cleaning', this)" class="btn-keb-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 cursor-pointer">
                    Kebersihan
                </button>
                <button type="button" onclick="filterKebutuhanCat('angkut', this)" class="btn-keb-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 cursor-pointer">
                    Pindahan &amp; Angkut
                </button>
                <button type="button" onclick="filterKebutuhanCat('kurir', this)" class="btn-keb-filter px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 cursor-pointer">
                    Belanja &amp; Kurir
                </button>
            </div>
        </div>

        {{-- LIST KEBUTUHAN ITEM CARDS --}}
        <div class="space-y-2.5" id="containerKebutuhanList">
            {{-- WADAH SURUHAN REAL-TIME DARI CUSTOMER --}}
            <div id="mitraDynamicCustomerOrdersList" class="space-y-2.5"></div>

            {{-- EMPTY STATE: BENAR-BENAR KOSONG TANPA PERCONTOHAN --}}
            <div id="mitraEmptyKebutuhanList" class="p-6 text-center space-y-2.5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto text-xl shadow-xs">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Belum Ada Kebutuhan Customer</h4>
                    <p class="text-[10px] text-slate-400 max-w-xs mx-auto mt-0.5">
                        Suruhan yang diposting oleh customer akan muncul di sini secara real-time. Anda dapat langsung mengajukan harga penawaran.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================================
         VIEW 3: 🛠️ PEKERJAAN MITRA (5 STATUS LENGKAP)
         Penawaran | Dikonfirmasi | Berlangsung | Selesai | Dibatalkan
    ========================================================================= --}}
    <div id="viewMitraPekerjaan" class="hidden space-y-3.5">

        @include('mitra.partials.page-header', [
            'judul' => 'Pekerjaan Mitra',
            'deskripsi' => 'Pantau seluruh siklus pekerjaan Anda mulai dari penawaran hingga selesai.',
            'badge' => 'Kelola Tugas',
            'badgeColor' => 'sky',
        ])

        {{-- 5 FILTER STATUS PEKERJAAN --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
            <button
                type="button"
                onclick="filterStatusPekerjaan('penawaran', this)"
                class="btn-status-pek px-2.5 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 shadow-2xs cursor-pointer"
            >
                Penawaran (0)
            </button>
            <button
                type="button"
                onclick="filterStatusPekerjaan('dikonfirmasi', this)"
                class="btn-status-pek px-2.5 py-1 rounded-full font-medium bg-white/10 text-white/90 border border-white/20 shrink-0 hover:bg-white/20 cursor-pointer"
            >
                Dikonfirmasi (0)
            </button>
            <button
                type="button"
                onclick="filterStatusPekerjaan('berlangsung', this)"
                class="btn-status-pek px-2.5 py-1 rounded-full font-medium bg-white/10 text-white/90 border border-white/20 shrink-0 hover:bg-white/20 cursor-pointer"
            >
                Berlangsung (0)
            </button>
            <button
                type="button"
                onclick="filterStatusPekerjaan('selesai', this)"
                class="btn-status-pek px-2.5 py-1 rounded-full font-medium bg-white/10 text-white/90 border border-white/20 shrink-0 hover:bg-white/20 cursor-pointer"
            >
                Selesai (0)
            </button>
            <button
                type="button"
                onclick="filterStatusPekerjaan('dibatalkan', this)"
                class="btn-status-pek px-2.5 py-1 rounded-full font-medium bg-white/10 text-white/90 border border-white/20 shrink-0 hover:bg-white/20 cursor-pointer"
            >
                Dibatalkan (0)
            </button>
        </div>

        {{-- 1. KONTEN STATUS: PENAWARAN --}}
        <div id="sectionPekPenawaran" class="pek-status-group space-y-2.5">
            <div class="p-2.5 rounded-xl bg-white/10 border border-white/20 text-[10px] font-semibold text-white flex items-center gap-2">
                <i class="fa-solid fa-clock text-white shrink-0"></i>
                <span>Penawaran yang telah diajukan dan masih menunggu konfirmasi customer.</span>
            </div>

            {{-- WADAH PENAWARAN DARI CUSTOMER --}}
            <div id="mitraDynamicPekPenawaranList" class="space-y-2"></div>

            <div id="emptySectionPenawaran" class="p-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                <i class="fa-solid fa-paper-plane text-slate-300 text-base"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Penawaran</p>
                <p class="text-[9px] text-slate-400">Penawaran harga yang Anda ajukan kepada customer akan tampil di sini.</p>
            </div>
        </div>

        {{-- 2. KONTEN STATUS: DIKONFIRMASI --}}
        <div id="sectionPekDikonfirmasi" class="pek-status-group hidden space-y-2.5">
            <div class="p-2.5 rounded-xl bg-sky-50/70 border border-sky-200 text-[10px] text-sky-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-sky-600 shrink-0"></i>
                <span>Pekerjaan telah disepakati dan dikonfirmasi. Siap untuk dikerjakan.</span>
            </div>

            {{-- WADAH PEKERJAAN DIKONFIRMASI CUSTOMER --}}
            <div id="mitraDynamicPekDikonfirmasiList" class="space-y-2"></div>

            <div id="emptySectionDikonfirmasi" class="p-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                <i class="fa-solid fa-calendar-check text-slate-300 text-base"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Pekerjaan Dikonfirmasi</p>
                <p class="text-[9px] text-slate-400">Pekerjaan yang telah disepakati dan siap dikerjakan akan tampil di sini.</p>
            </div>
        </div>

        {{-- 3. KONTEN STATUS: BERLANGSUNG --}}
        <div id="sectionPekBerlangsung" class="pek-status-group hidden space-y-2.5">
            <div class="p-2.5 rounded-xl bg-sky-50/70 border border-sky-200 text-[10px] text-sky-900 flex items-center gap-2">
                <i class="fa-solid fa-person-digging text-[#173B67] shrink-0"></i>
                <span>Pekerjaan sedang aktif dikerjakan saat ini. Update status Anda secara berkala.</span>
            </div>

            {{-- WADAH PEKERJAAN BERLANGSUNG CUSTOMER --}}
            <div id="mitraDynamicPekBerlangsungList" class="space-y-2"></div>

            <div id="emptySectionBerlangsung" class="p-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                <i class="fa-solid fa-person-digging text-slate-300 text-base"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Pekerjaan Berlangsung</p>
                <p class="text-[9px] text-slate-400">Pekerjaan yang sedang Anda kerjakan saat ini akan tampil di sini.</p>
            </div>
        </div>

        {{-- 4. KONTEN STATUS: SELESAI --}}
        <div id="sectionPekSelesai" class="pek-status-group hidden space-y-2.5">
            {{-- WADAH PEKERJAAN SELESAI CUSTOMER --}}
            <div id="mitraDynamicPekSelesaiList" class="space-y-2"></div>

            <div id="emptySectionSelesai" class="p-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                <i class="fa-solid fa-circle-check text-slate-300 text-base"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Pekerjaan Selesai</p>
                <p class="text-[9px] text-slate-400">Riwayat tugas yang berhasil Anda selesaikan akan tercatat di sini.</p>
            </div>
        </div>

        {{-- 5. KONTEN STATUS: DIBATALKAN --}}
        <div id="sectionPekDibatalkan" class="pek-status-group hidden space-y-2.5">
            {{-- WADAH PEKERJAAN DIBATALKAN CUSTOMER --}}
            <div id="mitraDynamicPekDibatalkanList" class="space-y-2"></div>

            <div id="emptySectionDibatalkan" class="p-5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                <i class="fa-solid fa-ban text-slate-300 text-base"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Pekerjaan Dibatalkan</p>
                <p class="text-[9px] text-slate-400">Riwayat pekerjaan yang dibatalkan akan tampil di sini.</p>
            </div>
        </div>

    </div>

    {{-- =========================================================================
         VIEW 4: 👤 PROFIL MITRA
         Foto Profil | Nama | Kontak | Spesialisasi | Keahlian | Deskripsi Diri | Pengalaman | Lokasi | Rating | Pengaturan
    ========================================================================= --}}
    <div id="viewMitraProfil" class="hidden space-y-3.5">

        @include('mitra.partials.page-header', [
            'judul' => 'Profil & Spesialisasi',
            'deskripsi' => 'Kelola data diri, keahlian, dan pengalaman agar mudah ditemukan customer.',
            'badge' => 'Akun Mitra',
            'badgeColor' => 'emerald',
        ])

        {{-- KARTU PROFIL UTAMA --}}
        <section class="card-sb p-4 bg-white border border-slate-200/90 shadow-xs space-y-3">
            <div class="flex items-center gap-3.5">
                <div class="relative shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-400 to-[#0E1D31] text-white font-black text-2xl flex items-center justify-center shadow-md border-2 border-white" id="tabMitraAvatarInitial">
                        {{ $mitraAuthInitial }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white text-[9px]" title="Terverifikasi">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-sm font-black text-slate-900 truncate" id="tabMitraName">{{ $mitraAuthName }}</h3>
                        <span class="px-1.5 py-0.2 rounded bg-sky-100 text-[#173B67] text-[8.5px] font-extrabold font-mono">MITRA AKTIF</span>
                    </div>
                    <p class="text-[10.5px] text-slate-500 font-medium" id="tabMitraId">SayaBantu {{ $mitraAuthId }}</p>
                    <div class="flex items-center gap-1 text-[10px] font-bold mt-0.5">
                        @if($mitraAuthRating !== null)
                            <span><span class="text-yellow-400">★</span> {{ $mitraAuthRating }}</span>
                        @else
                            <span class="text-slate-500">Belum ada ulasan</span>
                        @endif
                        <span class="text-slate-400 font-normal text-[9px]">({{ $mitraAuthReviewCount }} ulasan)</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-2.5 border-t border-slate-100 text-center">
                <div class="p-2 rounded-xl bg-slate-50">
                    <strong class="text-xs font-black text-slate-900 block" id="tabMitraTasks">{{ $mitraAuthTasks }}</strong>
                    <span class="text-[8.5px] text-slate-400 block mt-0.5">Tugas Selesai</span>
                </div>
                <div class="p-2 rounded-xl bg-slate-50">
                    <strong class="text-[10px] font-bold text-slate-600 block">Belum ada data</strong>
                    <span class="text-[8.5px] text-slate-400 block mt-0.5">Tingkat Respon</span>
                </div>
                <div class="p-2 rounded-xl bg-slate-50">
                    <strong class="text-[10px] font-bold text-slate-600 block">Belum ada data</strong>
                    <span class="text-[8.5px] text-slate-400 block mt-0.5">Penyelesaian</span>
                </div>
            </div>
        </section>

        {{-- INFORMASI KONTAK & VERIFIKASI --}}
        <section class="card-sb p-3.5 space-y-2 text-[10px]">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Informasi Kontak &amp; Status</h4>
            <div class="space-y-1.5">
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                    <span class="text-slate-500"><i class="fa-solid fa-phone text-sky-600 w-4"></i> No. Handphone</span>
                    <strong class="text-slate-800" id="tabMitraPhone">{{ $mitraAuthPhone }}</strong>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                    <span class="text-slate-500"><i class="fa-regular fa-envelope text-sky-600 w-4"></i> Email</span>
                    <strong class="text-slate-800" id="tabMitraEmail">{{ $mitraAuthEmail }}</strong>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50/70 border border-emerald-100">
                    <span class="text-emerald-800"><i class="fa-solid fa-id-card text-emerald-600 w-4"></i> Status Identitas</span>
                    <strong class="text-emerald-700">✓ e-KTP &amp; SKCK Terverifikasi</strong>
                </div>
            </div>
        </section>

        {{-- SPESIALISASI & KEAHLIAN --}}
        <section class="card-sb p-3.5 space-y-2">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Spesialisasi &amp; Keahlian</h4>
            <div class="flex flex-wrap gap-1.5 text-[10px]">
                <span class="px-2.5 py-1 rounded-full bg-sky-50 text-[#173B67] border border-sky-200 font-semibold">{{ $mitraAuthKeahlian }}</span>
            </div>
        </section>

        {{-- DESKRIPSI DIRI & BIO --}}
        <section class="card-sb p-3.5 space-y-1.5 text-[10px]">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Deskripsi Diri</h4>
            <p class="text-slate-700 leading-relaxed" id="tabMitraBio">
                {{ $mitraAuthKeahlian }} · {{ $mitraAuthArea }}
            </p>
        </section>

        {{-- PENGALAMAN & ALAMAT OPERASIONAL --}}
        <section class="card-sb p-3.5 space-y-2 text-[10px]">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengalaman &amp; Wilayah Kerja</h4>
            <div class="space-y-1.5">
                <div class="p-2 rounded-lg bg-slate-50 flex items-start gap-2">
                    <i class="fa-solid fa-briefcase text-[#173B67] mt-0.5"></i>
                    <div>
                        <strong class="text-slate-900 block">Pengalaman 4+ Tahun</strong>
                        <span class="text-slate-500 text-[9px]">Berpengalaman di bidang cleaning service, logistik, dan servis perabotan rumah tangga.</span>
                    </div>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 flex items-start gap-2">
                    <i class="fa-solid fa-location-dot text-rose-500 mt-0.5"></i>
                    <div>
                        <strong class="text-slate-900 block" id="tabMitraArea">{{ $mitraAuthArea }}</strong>
                        <span class="text-slate-500 text-[9px]">Radius operasional mitra terdaftar di wilayah kerja SayaBantu.</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- AKSES CEPAT MENU PENDUKUNG DARI PROFIL --}}
        <section class="card-sb p-2 space-y-1 text-xs">
            <button
                type="button"
                onclick="switchMitraNav('pengaturan')"
                class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-slate-800 font-semibold cursor-pointer"
            >
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-gear text-slate-500 w-4"></i>
                    <span>Buka Halaman Pengaturan</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <button
                type="button"
                onclick="switchMitraNav('ulasan')"
                class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-slate-800 font-semibold cursor-pointer"
            >
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-star text-yellow-400 w-4"></i>
                    <span>Buka Halaman Ulasan Saya</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <button
                type="button"
                onclick="switchMitraNav('bantuan')"
                class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors text-slate-800 font-semibold cursor-pointer"
            >
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-question text-sky-600 w-4"></i>
                    <span>Buka Pusat Bantuan &amp; SOP</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </button>

            <button
                type="button"
                onclick="logoutMitraAction()"
                class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-rose-50 transition-colors text-rose-600 font-bold cursor-pointer"
            >
                <span class="flex items-center gap-2.5">
                    <i class="fa-solid fa-right-from-bracket w-4"></i>
                    <span>Keluar dari Akun Mitra</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-rose-300"></i>
            </button>
        </section>

    </div>

    {{-- =========================================================================
         HALAMAN LENGKAP 1: 💰 SALDO & PENDAPATAN (BUKAN POPUP, TAPI HALAMAN UTUH)
    ========================================================================= --}}
    <div id="viewMitraSaldo" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Saldo & Pendapatan',
            'deskripsi' => 'Pantau penghasilan Anda dan ajukan penarikan dana ke rekening.',
            'badge' => 'Keuangan Mitra',
            'badgeColor' => 'sky',
        ])

        {{-- CARD UTAMA SALDO --}}
        <section class="card-sb p-4 bg-gradient-to-br from-[#0E1D31] via-[#17314A] to-[#173B67] text-white border-0 shadow-md space-y-3">
            <span class="text-[9.5px] font-bold text-sky-200 uppercase tracking-wider block">Saldo Tersedia untuk Ditarik</span>
            <strong id="mitraSaldoTersediaText" class="text-3xl font-black text-white block tracking-tight">Rp {{ number_format($saldoMitra ?? 0, 0, ',', '.') }}</strong>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/10 text-center">
                <div class="p-2 rounded-xl bg-white/5">
                    <span class="text-[8.5px] text-sky-200 block">Hari Ini</span>
                    <strong id="mitraSaldoHariIniText" class="text-xs font-bold text-white block mt-0.5">+Rp {{ number_format($saldoMitraHariIni ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div class="p-2 rounded-xl bg-white/5">
                    <span class="text-[8.5px] text-sky-200 block">Bulan Ini</span>
                    <strong id="mitraSaldoBulanIniText" class="text-xs font-bold text-white block mt-0.5">Rp {{ number_format($saldoMitraBulanIni ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </section>

        {{-- FORMULIR PENARIKAN DANA LENGKAP --}}
        <section class="card-sb p-3.5 space-y-3">
            <div class="border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-transfer text-[#173B67]"></i>
                    <span>Tarik Dana ke Rekening / E-Wallet</span>
                </h3>
                <p class="text-[9px] text-slate-400">Pencairan diproses otomatis setiap hari kerja</p>
            </div>

            <form onsubmit="handleTarikDanaSubmit(event)" class="space-y-2.5 text-[10px]">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nominal Penarikan (Rp)</label>
                    <input type="number" max="1450000" min="50000" value="" placeholder="Masukkan nominal penarikan" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold outline-none focus:bg-white focus:border-[#173B67]">
                    <span class="text-[8.5px] text-slate-400 mt-0.5 block">Minimal penarikan Rp 50.000</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tujuan Pencairan</label>
                    <select class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs outline-none focus:bg-white focus:border-[#173B67]">
                        <option id="optBankBCA">BCA - 1234567890 ({{ $mitraAuthName }})</option>
                        <option id="optBankMandiri">Bank Mandiri - 9876543210 ({{ $mitraAuthName }})</option>
                        <option id="optBankBRI">BRI - 5544332211 ({{ $mitraAuthName }})</option>
                        <option id="optEwalletGoPay">GoPay - {{ $mitraAuthPhone }}</option>
                        <option id="optEwalletDANA">DANA - {{ $mitraAuthPhone }}</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10.5px] transition-all cursor-pointer shadow-xs">
                    Ajukan Penarikan Dana Sekarang
                </button>
            </form>
        </section>

        {{-- RIWAYAT TRANSAKSI LENGKAP --}}
        <section class="card-sb p-3.5 space-y-2.5">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-slate-500"></i>
                <span>Riwayat Transaksi &amp; Pendapatan</span>
            </h3>

            <div class="space-y-2 text-[10px]" id="mitraTransactionList">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                    <i class="fa-solid fa-receipt text-slate-300 text-xl block mx-auto mb-1"></i>
                    <p class="text-[10px] font-bold text-slate-700">Belum Ada Riwayat Transaksi</p>
                    <p class="text-[9px] text-slate-400">Pendapatan dan riwayat penarikan dana akan tercatat di sini.</p>
                </div>
            </div>
        </section>
    </div>

    {{-- =========================================================================
         HALAMAN LENGKAP 2: 💬 PESAN CUSTOMER (HALAMAN INTERAKTIF LENGKAP)
         - Daftar percakapan (chat rooms)
         - Detail chat view dengan bubble pesan dan input ketik
    ========================================================================= --}}
    <div id="viewMitraPesan" class="hidden space-y-3.5">

        @include('mitra.partials.page-header', [
            'judul' => 'Pesan & Komunikasi',
            'deskripsi' => 'Koordinasi dengan customer terkait suruhan yang sedang berjalan.',
            'badge' => 'Chat Customer',
            'badgeColor' => 'sky',
        ])

        {{-- ======= CHAT LIST VIEW (Daftar Percakapan) ======= --}}
        <div id="chatListView">
            <section class="card-sb p-3.5 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-comments text-sky-600"></i>
                            <span>Percakapan Customer</span>
                        </h3>
                        <p class="text-[9px] text-slate-400 mt-0.5">Pesan terkait koordinasi suruhan yang sedang berjalan</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2.5 py-1 rounded-full bg-[#0E1D31] text-white border border-white/20 whitespace-nowrap shrink-0">
                        <i class="fa-solid fa-comments"></i> Komunikasi Layanan
                    </span>
                </div>

                <div id="mitraChatRoomListDynamic" class="space-y-2"></div>

                {{-- EMPTY STATE CHAT: KOSONG TANPA PERCAKAPAN PERCONTOHAN --}}
                <div id="mitraChatEmptyState" class="p-6 text-center space-y-2.5 bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mx-auto text-lg shadow-xs">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-xs font-bold text-slate-800">Belum Ada Percakapan</h4>
                        <p class="text-[10px] text-slate-400 max-w-xs mx-auto">
                            Percakapan dengan customer akan muncul saat Anda mengajukan penawaran atau terpilih mengerjakan suruhan.
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- ======= CHAT DETAIL VIEW (Percakapan Aktif + Input Ketik) ======= --}}
        <div id="chatDetailView" class="hidden" style="min-height: calc(100vh - 200px);">

            {{-- HEADER CHAT --}}
            <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                <button type="button" onclick="closeChatRoom()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer active:scale-95 transition-all shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="relative shrink-0">
                    <div id="chatAvatarDetail" class="w-10 h-10 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">RD</div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <strong id="chatNameDetail" class="text-xs font-extrabold text-slate-900 block truncate">Customer SayaBantu</strong>
                    <span id="chatTaskDetail" class="text-[9px] text-sky-600 font-semibold block">Tugas #8821 • Full Home Cleaning</span>
                </div>
            </div>

            {{-- KARTU KESEPAKATAN HARGA & NEGOSIASI (PORTAL MITRA) --}}
            <div id="mitraChatNegotiationCard" class="hidden my-2.5 p-3 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-sky-300 shadow-xs space-y-2">
                <div class="flex items-center justify-between pb-1.5 border-b border-sky-200/80">
                    <span class="text-[9.5px] font-black uppercase tracking-wider text-[#173B67] flex items-center gap-1.5">
                        <i class="fa-solid fa-handshake"></i> Negosiasi Harga Bersama Customer
                    </span>
                    <span id="mitraChatDealStatusBadge" class="text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        Sedang Nego
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="min-w-0 pr-2">
                        <strong id="mitraChatDealTitle" class="text-[11px] font-extrabold text-slate-900 block truncate">Full Home Cleaning</strong>
                        <span class="text-[9.5px] text-slate-500">Tiket: <span id="mitraChatDealCode" class="font-mono font-bold text-slate-700">#SB-SURUH-9012</span></span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[8.5px] text-slate-400 block">Nominal Tawaran:</span>
                        <strong id="mitraChatDealPrice" class="text-sm font-black text-emerald-700">Rp 85.000</strong>
                    </div>
                </div>

                {{-- Action Box Mitra: Setujui Tawaran atau Tawar Balik --}}
                <div id="mitraChatDealActionBox" class="pt-2 border-t border-sky-200/60 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="mitraSetujuiHargaDariChat()" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10.5px] flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer active:scale-95">
                            <i class="fa-solid fa-check"></i>
                            <span id="labelBtnSetujuiMitra">Setujui Tawaran Harga</span>
                        </button>
                        <button type="button" onclick="mitraBukaNegoDariChat()" class="py-2 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10px] transition-colors cursor-pointer shrink-0">
                            <i class="fa-solid fa-pen-to-square"></i> Tawar Balik
                        </button>
                    </div>
                    <p class="text-[9px] text-slate-500 leading-tight">
                        <i class="fa-solid fa-circle-info text-sky-600 mr-0.5"></i> Menyetujui harga akan mengunci kesepakatan dan membuka link pembayaran Customer.
                    </p>
                </div>

                <div id="mitraChatDealSuccessBox" class="hidden pt-2 border-t border-emerald-200 space-y-1">
                    <div class="flex items-center gap-1.5 text-[9.5px] font-bold text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Harga Rp <span id="mitraChatDealFinalPrice">85.000</span> telah disetujui! Menunggu pembayaran Customer.</span>
                    </div>
                </div>
            </div>

            {{-- CHAT BUBBLES AREA --}}
            <div id="chatBubblesContainer" class="py-4 space-y-3 overflow-y-auto" style="max-height: calc(100vh - 340px); min-height: 320px;">
                {{-- Date Separator --}}
                <div class="flex items-center gap-2 px-2">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Hari Ini</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <div id="mitraChatLoadingIndicator" class="text-center py-6 text-slate-400 text-xs">
                    <i class="fa-solid fa-spinner fa-spin mr-1 text-sky-600"></i> Memuat percakapan...
                </div>
            </div>

            {{-- INPUT AREA: KETIK PESAN --}}
            <div class="sticky bottom-0 bg-[#F7F9FC] pt-2 pb-1 border-t border-slate-200">
                <form onsubmit="sendChatMessage(event)" class="flex items-end gap-2">
                    <button type="button" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm cursor-pointer shrink-0 transition-colors">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            id="chatInputField"
                            placeholder="Ketik pesan..."
                            autocomplete="off"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-white border border-slate-200 focus:border-[#173B67] focus:ring-2 focus:ring-sky-100 text-xs outline-none transition-all pr-10"
                        >
                        <button type="button" onclick="showMitraToast('Emoji', 'Pilih emoji dari keyboard perangkat Anda.', 'info')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
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

    {{-- =========================================================================
         HALAMAN LENGKAP 3: ⭐ ULASAN SAYA (BUKAN POPUP, TAPI HALAMAN UTUH)
    ========================================================================= --}}
    <div id="viewMitraUlasan" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Ulasan Saya',
            'deskripsi' => 'Lihat penilaian dan ulasan customer untuk menjaga reputasi Anda.',
            'badge' => 'Reputasi Mitra',
            'badgeColor' => 'amber',
        ])

        {{-- SKOR UTAMA REPUTASI --}}
        <section class="card-sb p-4 bg-gradient-to-r from-amber-50/70 via-white to-amber-50/40 border border-amber-200 text-center space-y-1">
            <span class="text-[9px] font-bold text-amber-800 uppercase tracking-wider block">Rating Kepuasan Mitra</span>
            <strong class="text-3xl font-black text-amber-600 block">
                {{ $mitraAuthRating ?? 'Belum ada' }}
                @if($mitraAuthRating !== null)
                    <span class="text-yellow-400">★</span>
                @endif
            </strong>
            <p class="text-[10px] text-slate-600">Berdasarkan <strong>{{ $mitraAuthReviewCount }} ulasan pelanggan</strong>.</p>
        </section>

        {{-- DAFTAR ULASAN LENGKAP --}}
        <section class="card-sb p-3.5 space-y-3">
            <div class="space-y-2.5" id="mitraFullUlasanList">
                @forelse(($mitraReviews ?? collect()) as $review)
                    <article class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <strong class="text-[10px] font-bold text-slate-900">{{ $review->customer_name ?: 'Customer' }}</strong>
                            <span class="text-amber-500 text-[10px]">{{ str_repeat('★', (int) $review->review_rating) }}{{ str_repeat('☆', 5 - (int) $review->review_rating) }}</span>
                        </div>
                        @if($review->review_comment)
                            <p class="text-[10px] text-slate-700">{{ $review->review_comment }}</p>
                        @endif
                        <span class="text-[9px] text-slate-500">{{ $review->layanan }}{{ $review->reviewed_at ? ' • '.$review->reviewed_at->format('d M Y') : '' }}</span>
                    </article>
                @empty
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center space-y-1">
                    <i class="fa-solid fa-star text-slate-300 text-xl block mx-auto mb-1"></i>
                    <p class="text-[10px] font-bold text-slate-700">Belum Ada Ulasan</p>
                    <p class="text-[9px] text-slate-400">Ulasan dari pelanggan setelah pekerjaan selesai akan muncul di sini.</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- =========================================================================
         HALAMAN LENGKAP 4: 📊 PERFORMA MITRA (BUKAN POPUP, TAPI HALAMAN UTUH)
    ========================================================================= --}}
    <div id="viewMitraPerforma" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Performa & Kinerja',
            'deskripsi' => 'Pantau tingkat keandalan, penyelesaian tugas, dan naik level mitra.',
            'badge' => 'Kinerja Mitra',
            'badgeColor' => 'slate',
        ])

        <section class="card-sb p-4 space-y-3">
            <p class="text-[10px] text-slate-600">Ringkasan ini dihitung dari transaksi dan ulasan yang tersimpan di akun Anda.</p>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl bg-slate-50 p-2">
                    <strong class="block text-sm font-black text-slate-900">{{ $mitraAuthTasks }}</strong>
                    <span class="text-[8.5px] text-slate-500">Pekerjaan selesai</span>
                </div>
                <div class="rounded-xl bg-slate-50 p-2">
                    <strong class="block text-sm font-black text-amber-600">{{ $mitraAuthRating ?? '—' }}</strong>
                    <span class="text-[8.5px] text-slate-500">Rating rata-rata</span>
                </div>
                <div class="rounded-xl bg-slate-50 p-2">
                    <strong class="block text-sm font-black text-emerald-700">Rp {{ number_format((int) ($saldoMitra ?? 0), 0, ',', '.') }}</strong>
                    <span class="text-[8.5px] text-slate-500">Dana diteruskan</span>
                </div>
            </div>
        </section>
    </div>

    {{-- =========================================================================
         HALAMAN LENGKAP 5: ❓ BANTUAN & PANDUAN (BUKAN POPUP, TAPI HALAMAN UTUH)
    ========================================================================= --}}
    <div id="viewMitraBantuan" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Bantuan & Panduan',
            'deskripsi' => 'Temukan panduan penggunaan portal dan hubungi CS bila menemui kendala.',
            'badge' => 'Pusat Bantuan',
            'badgeColor' => 'emerald',
        ])

        {{-- KONTAK DARURAT CS ADMIN 24 JAM --}}
        <section class="card-sb p-4 bg-gradient-to-r from-sky-50 via-blue-50 to-white border border-sky-200 space-y-2">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-headset text-3xl text-sky-600"></i>
                <div>
                    <h3 class="text-xs font-black text-slate-900">Bantuan CS Admin SayaBantu 24 Jam</h3>
                    <p class="text-[9.5px] text-slate-600">Hubungi kami jika mengalami kendala saat bertugas di lapangan.</p>
                </div>
            </div>
            <button
                type="button"
                onclick="switchMitraNav('pengaduan')"
                class="block w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10.5px] text-center shadow-xs transition-colors cursor-pointer"
            >
                <i class="fa-solid fa-headset mr-1 text-xs"></i> Hubungi Layanan Bantuan &amp; Mediasi
            </button>
        </section>

        {{-- PANDUAN STANDAR PELAYANAN (SOP) --}}
        <section class="card-sb p-3.5 space-y-2 text-[10px]">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Panduan SOP Pelayanan SayaBantu</h4>
            <div class="space-y-1.5 text-slate-700">
                <div class="p-2.5 rounded-xl bg-slate-50 flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#173B67] text-white flex items-center justify-center font-bold text-[9px] shrink-0">1</span>
                    <p class="leading-relaxed"><strong>Berpakaian Rapi &amp; Atribut Resmi:</strong> Gunakan pakaian bersih dan selalu kenakan ID card resmi SayaBantu saat bertugas.</p>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#173B67] text-white flex items-center justify-center font-bold text-[9px] shrink-0">2</span>
                    <p class="leading-relaxed"><strong>Konfirmasi Kedatangan:</strong> Beri kabar kepada pemesan via Chat Aplikasi paling lambat 15 menit sebelum tiba di lokasi.</p>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-[#173B67] text-white flex items-center justify-center font-bold text-[9px] shrink-0">3</span>
                    <p class="leading-relaxed"><strong>Etika &amp; Keramahan:</strong> Ucapkan salam, minta izin sebelum memindahkan barang, dan pastikan pekerjaan diteliti ulang sebelum pamit.</p>
                </div>
            </div>
        </section>
    </div>

    {{-- =========================================================================
         HALAMAN LENGKAP 6: ⚙️ PENGATURAN AKUN (BUKAN POPUP, TAPI HALAMAN UTUH)
    ========================================================================= --}}
    <div id="viewMitraPengaturan" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Pengaturan Akun',
            'deskripsi' => 'Atur preferensi notifikasi, keamanan, dan konfigurasi akun mitra Anda.',
            'badge' => 'Konfigurasi',
            'badgeColor' => 'rose',
        ])

        <section class="card-sb p-3.5 space-y-3 text-[10px]">
            <div>
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-gear text-slate-600"></i>
                    <span>Preferensi &amp; Notifikasi Akun</span>
                </h3>
                <p class="text-[9px] text-slate-400">Atur preferensi kerja sesuai kenyamanan Anda</p>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50">
                    <div>
                        <strong class="text-slate-800 block">Notifikasi Suara Pesanan Masuk</strong>
                        <span class="text-[8.5px] text-slate-400">Bunyi dering ketika ada tugas baru di sekitarmu</span>
                    </div>
                    <input type="checkbox" checked class="accent-[#173B67] w-4 h-4 cursor-pointer">
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50">
                    <div>
                        <strong class="text-slate-800 block">Terima Tugas di Luar Jam Normal</strong>
                        <span class="text-[8.5px] text-slate-400">Suruhan subuh atau malam hari</span>
                    </div>
                    <input type="checkbox" class="accent-[#173B67] w-4 h-4 cursor-pointer">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Maksimal Radius Jarak Suruhan</label>
                    <select class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs outline-none">
                        <option>Radius hingga 5 km</option>
                        <option selected>Radius hingga 15 km (Direkomendasikan)</option>
                        <option>Radius hingga 25 km</option>
                    </select>
                </div>
            </div>

            <button type="button" onclick="showMitraToast('Tersimpan', 'Pengaturan preferensi akun berhasil disimpan.', 'success')" class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10.5px] transition-all cursor-pointer shadow-xs">
                Simpan Preferensi
            </button>
        </section>
    </div>

    {{-- =========================================================
         HALAMAN BURGER 5: ⚠️ PUSAT PENGADUAN & MEDIASI MITRA
    ========================================================= --}}
    <div id="viewMitraPengaduan" class="hidden space-y-3.5">
        @include('mitra.partials.page-header', [
            'judul' => 'Pusat Pengaduan Mitra',
            'deskripsi' => 'Laporkan kendala tugas, pelanggaran customer, dan pantau penyelesaian mediasi dari Admin.',
            'badge' => 'Mediasi Terpercaya',
            'badgeColor' => 'rose',
        ])

        {{-- 1. CARD HERO AJUKAN KELUHAN MITRA --}}
        <section class="card-sb p-4 bg-gradient-to-br from-[#0E1D31] via-[#17314A] to-[#173B67] text-white border-0 shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-rose-500/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[9px] font-extrabold uppercase tracking-wider">
                            <i class="fa-solid fa-shield-halved text-[8px]"></i> Perlindungan Mitra
                        </span>
                        <h2 class="text-sm font-extrabold text-white mt-1.5 leading-snug">
                            Kendala Tugas atau Pelanggaran Customer?
                        </h2>
                        <p class="text-[10px] text-white/80 leading-relaxed mt-0.5">
                            Laporkan pembatalan sepihak, lokasi berbahaya, atau penolakan bayar. Tim Mediasi Admin akan menginvestigasi bukti secara objektif.
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-rose-400 text-lg shrink-0 shadow-inner">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>

                {{-- TOMBOL UTAMA AJUKAN KELUHAN --}}
                <button
                    type="button"
                    onclick="openModalAjukanKeluhanMitra()"
                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border border-rose-400/30"
                >
                    <i class="fa-solid fa-bullhorn text-[11px]"></i>
                    <span>Ajukan Keluhan Mitra Baru</span>
                </button>
            </div>
        </section>

        {{-- 2. RINGKASAN STATUS PENGADUAN MITRA --}}
        <section class="grid grid-cols-4 gap-2 text-center text-xs">
            <div class="p-2.5 rounded-2xl bg-white border border-slate-200/80 shadow-xs cursor-pointer hover:border-slate-300 transition-all" onclick="filterMitraComplaints('all', this)">
                <span class="block text-sm font-black text-slate-900" id="statMitraTotal">0</span>
                <span class="block text-[8.5px] text-slate-500 mt-0.5 font-semibold">Total</span>
            </div>
            <div class="p-2.5 rounded-2xl bg-white border border-rose-200/80 shadow-xs cursor-pointer hover:border-rose-300 transition-all" onclick="filterMitraComplaints('baru', this)">
                <span class="block text-sm font-black text-rose-600" id="statMitraBaru">0</span>
                <span class="block text-[8.5px] text-rose-700 mt-0.5 font-semibold">Menunggu</span>
            </div>
            <div class="p-2.5 rounded-2xl bg-white border border-amber-200/80 shadow-xs cursor-pointer hover:border-amber-300 transition-all" onclick="filterMitraComplaints('proses', this)">
                <span class="block text-sm font-black text-amber-600" id="statMitraProses">0</span>
                <span class="block text-[8.5px] text-amber-700 mt-0.5 font-semibold">Pemeriksaan</span>
            </div>
            <div class="p-2.5 rounded-2xl bg-white border border-emerald-200/80 shadow-xs cursor-pointer hover:border-emerald-300 transition-all" onclick="filterMitraComplaints('selesai', this)">
                <span class="block text-sm font-black text-emerald-600" id="statMitraSelesai">0</span>
                <span class="block text-[8.5px] text-emerald-700 mt-0.5 font-semibold">Selesai</span>
            </div>
        </section>

        {{-- 3. FILTER TABS & DAFTAR --}}
        <section class="space-y-2">
            <div class="flex items-center justify-between px-0.5">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-[#173B67]"></i> Riwayat Pengaduan &amp; Mediasi
                </h3>
                <span class="text-[9.5px] text-slate-400 font-medium" id="lblMitraComplaintCount">
                    Memuat riwayat...
                </span>
            </div>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
                <button type="button" onclick="filterMitraComplaints('all', this)" class="btn-tab-mitra-comp px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 shadow-2xs">
                    Semua
                </button>
                <button type="button" onclick="filterMitraComplaints('baru', this)" class="btn-tab-mitra-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    Menunggu Pemeriksaan
                </button>
                <button type="button" onclick="filterMitraComplaints('proses', this)" class="btn-tab-mitra-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    Dalam Penyelidikan
                </button>
                <button type="button" onclick="filterMitraComplaints('selesai', this)" class="btn-tab-mitra-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                    Selesai
                </button>
            </div>
        </section>

        {{-- 4. DAFTAR KARTU PENGADUAN MITRA --}}
        <section class="space-y-2.5" id="mitraComplaintList">
            {{-- Diisi secara dinamis oleh JavaScript --}}
        </section>

        {{-- EMPTY STATE KETIKA BELUM ADA PENGADUAN --}}
        <div id="emptyMitraComplaints" class="hidden card-sb p-6 text-center space-y-2.5 bg-white border border-slate-200/80">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto border border-emerald-100">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-900">Tidak Ada Laporan Kendala</h4>
                <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed max-w-xs mx-auto">
                    Seluruh penugasan berjalan kondusif. Jika mengalami masalah di lapangan, ajukan aduan resmi agar hak Anda terlindungi.
                </p>
            </div>
            <button
                type="button"
                onclick="openModalAjukanKeluhanMitra()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0E1D31] text-white font-bold text-[10px] hover:bg-[#173B67] transition-colors"
            >
                <i class="fa-solid fa-plus text-[9px]"></i> Buat Pengaduan
            </button>
        </div>

        {{-- INFORMASI PANDUAN PERLINDUNGAN MITRA --}}
        <section class="card-sb p-3.5 bg-slate-50 border border-slate-200/70 text-[10px] text-slate-600 space-y-2">
            <strong class="text-slate-800 font-bold block flex items-center gap-1.5">
                <i class="fa-solid fa-handshake-angle text-sky-600"></i> Standar Perlindungan &amp; SOP Mediasi Mitra
            </strong>
            <ul class="space-y-1.5 text-[9.5px] leading-relaxed">
                <li class="flex items-start gap-1.5">
                    <span class="text-sky-600 font-bold">1.</span>
                    <span><strong>Kompensasi Pembatalan:</strong> Jika Customer membatalkan sepihak setelah Mitra berada di lokasi, Anda berhak menerima kompensasi biaya operasional.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="text-sky-600 font-bold">2.</span>
                    <span><strong>Keselamatan &amp; Keamanan:</strong> Mitra berhak menolak permintaan di luar deskripsi suruhan yang berpotensi melanggar hukum atau membahayakan fisik.</span>
                </li>
                <li class="flex items-start gap-1.5">
                    <span class="text-sky-600 font-bold">3.</span>
                    <span><strong>Dukungan Darurat 24 Jam:</strong> Tim Dispatcher Admin siap merespon klarifikasi langsung untuk melindungi integritas rating dan akun Anda.</span>
                </li>
            </ul>
        </section>
    </div>

</main>

{{-- =========================================================
     3. BURGER MENU MITRA (DRAWER STRICTLY SCOPED DALAM FRAME MOBILE APP)
     ⭐ Ulasan Saya | 📊 Performa Mitra | ❓ Bantuan | ⚙️ Pengaturan | 🚪 Keluar
========================================================= --}}
<div id="drawerMitraOverlay" class="fixed inset-0 z-[120] pointer-events-none opacity-0 invisible transition-all duration-300" aria-hidden="true">

    {{-- BACKDROP CLICK --}}
    <div id="mitraDrawerBackdrop" onclick="closeMitraBurgerDrawer()" class="absolute inset-0 bg-black/60 backdrop-blur-[2px] pointer-events-auto" style="touch-action: none;"></div>

    {{-- CONTAINER SCOPED DI DALAM MOBILE APP (MAX 430px) --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none overflow-hidden">

        <aside id="drawerMitraPanel" class="absolute left-0 top-0 bottom-0 w-[82%] max-w-[340px] bg-[#0E1D31] text-white pointer-events-auto flex flex-col shadow-2xl -translate-x-full border-r border-white/10 z-10 transition-transform duration-300" style="overscroll-behavior: contain;">

            {{-- TOP DRAWER: HEADER BRAND + PROFIL SINGKAT MITRA --}}
            <div class="p-4 bg-gradient-to-b from-[#173B67]/50 to-transparent border-b border-white/10 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#173B67] border border-sky-400/30 flex items-center justify-center text-white font-bold text-xs shadow-md">
                            SB
                        </div>
                        <h3 class="text-sm font-bold tracking-tight text-white flex items-center gap-1 leading-none">
                            SayaBantu<span class="text-white">.com</span>
                        </h3>
                    </div>
                    <button type="button" onclick="closeMitraBurgerDrawer()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-sky-400 to-[#173B67] text-white font-black text-lg flex items-center justify-center shadow-md border border-white/20" id="drawerMitraAvatarInitial">
                        {{ $mitraAuthInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs font-black text-white truncate" id="drawerMitraName">{{ $mitraAuthName }}</h3>
                        <p class="text-[9.5px] text-sky-200" id="drawerMitraIdBadge">{{ $mitraAuthId }} • SayaBantu</p>
                        <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-emerald-400 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online Standby
                        </span>
                    </div>
                </div>
            </div>

            {{-- MENU PENDUKUNG BURGER MITRA (MEMBUKA HALAMAN LENGKAP) --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-1 text-xs">
                <div class="text-[8.5px] font-bold uppercase text-slate-400 px-3 py-1 tracking-wider">
                    Fitur Tambahan Mitra
                </div>

                {{-- 1. ⭐ Ulasan Saya --}}
                <button
                    type="button"
                    data-burger-nav="ulasan"
                    onclick="closeMitraBurgerDrawer(); switchMitraNav('ulasan');"
                    class="btn-burger-mitra w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-white/10 text-slate-200 hover:text-white font-semibold transition-all text-left cursor-pointer"
                >
                    <span class="w-7 h-7 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-star text-yellow-400"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="leading-none text-white font-bold">Ulasan Saya</div>
                        <span class="text-[9px] text-slate-400 font-normal">
                            {{ $mitraAuthRating !== null ? $mitraAuthRating.' ★' : 'Belum ada ulasan' }}
                            ({{ $mitraAuthReviewCount }} ulasan)
                        </span>
                    </div>
                </button>

                {{-- 2. 📊 Performa Mitra --}}
                <button
                    type="button"
                    data-burger-nav="performa"
                    onclick="closeMitraBurgerDrawer(); switchMitraNav('performa');"
                    class="btn-burger-mitra w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-white/10 text-slate-200 hover:text-white font-semibold transition-all text-left cursor-pointer"
                >
                    <span class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="leading-none text-white font-bold">Performa Mitra</div>
                        <span class="text-[9px] text-slate-400 font-normal">Aktivitas &amp; keandalan</span>
                    </div>
                </button>

                {{-- 3. ❓ Bantuan --}}
                <button
                    type="button"
                    data-burger-nav="bantuan"
                    onclick="closeMitraBurgerDrawer(); switchMitraNav('bantuan');"
                    class="btn-burger-mitra w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-white/10 text-slate-200 hover:text-white font-semibold transition-all text-left cursor-pointer"
                >
                    <span class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-circle-question"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="leading-none text-white font-bold">Bantuan &amp; Panduan</div>
                        <span class="text-[9px] text-slate-400 font-normal">SOP &amp; Pusat Bantuan 24 Jam</span>
                    </div>
                </button>

                {{-- 4. ⚙️ Pengaturan --}}
                <button
                    type="button"
                    data-burger-nav="pengaturan"
                    onclick="closeMitraBurgerDrawer(); switchMitraNav('pengaturan');"
                    class="btn-burger-mitra w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-white/10 text-slate-200 hover:text-white font-semibold transition-all text-left cursor-pointer"
                >
                    <span class="w-7 h-7 rounded-lg bg-slate-700 text-slate-300 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-gear"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="leading-none text-white font-bold">Pengaturan</div>
                        <span class="text-[9px] text-slate-400 font-normal">Akun &amp; notifikasi</span>
                    </div>
                </button>

                {{-- 5. ⚠️ Pusat Pengaduan & Mediasi --}}
                <button
                    type="button"
                    data-burger-nav="pengaduan"
                    onclick="closeMitraBurgerDrawer(); switchMitraNav('pengaduan');"
                    class="btn-burger-mitra w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-white/10 text-slate-200 hover:text-white font-semibold transition-all text-left cursor-pointer"
                >
                    <span class="w-7 h-7 rounded-lg bg-rose-500/20 text-rose-300 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="leading-none text-white font-bold">Pusat Pengaduan</div>
                        <span class="text-[9px] text-slate-400 font-normal">Lapor kendala tugas & mediasi admin</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[8.5px] font-bold">
                        Mediasi
                    </span>
                </button>
            </div>

            {{-- 5. 🚪 KELUAR AKUN MITRA --}}
            <div class="p-3 border-t border-white/10">
                <button
                    type="button"
                    onclick="logoutMitraAction()"
                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/20 font-bold text-xs transition-colors cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Akun Mitra</span>
                </button>
            </div>

        </aside>

    </div>
</div>

{{-- =========================================================
     4. MODAL-MODAL INTERAKTIF MITRA (SEARCH, NOTIFIKASI, AJUKAN PENAWARAN)
========================================================= --}}

{{-- MODAL 1: 🔍 SEARCH KEBUTUHAN & PEKERJAAN --}}
<div id="modalMitraSearch" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-start justify-center p-4 pt-12">
    <div class="w-full max-w-sm bg-white rounded-2xl p-4 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-magnifying-glass text-[#173B67]"></i>
                <span>Cari Kebutuhan / Pekerjaan</span>
            </h3>
            <button type="button" onclick="closeModalMitra('modalMitraSearch')" class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div>
            <input
                type="text"
                id="inputCariKebutuhan"
                oninput="liveSearchKebutuhan(this.value)"
                placeholder="Ketik kata kunci: cleaning, pindahan, antar barang..."
                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:border-[#173B67] focus:bg-white text-xs outline-none transition-all"
            >
        </div>

        <div id="hasilSearchMitra" class="space-y-1 text-xs max-h-52 overflow-y-auto">
            <p class="text-[10px] text-slate-400 text-center py-3">Ketik kata kunci di atas untuk mencari kebutuhan suruhan.</p>
        </div>
    </div>
</div>

{{-- MODAL 2: 🔔 NOTIFIKASI MITRA --}}
<div id="modalMitraNotif" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-white rounded-2xl p-4 shadow-xl space-y-3 max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-regular fa-bell text-[#173B67]"></i>
                <span>Notifikasi Mitra</span>
            </h3>
            <button type="button" onclick="closeModalMitra('modalMitraNotif')" class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto text-xs pr-1">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-1">
                <i class="fa-regular fa-bell text-slate-300 text-xl"></i>
                <p class="text-[10px] font-bold text-slate-700">Belum Ada Notifikasi</p>
                <p class="text-[9px] text-slate-500">Notifikasi transaksi akan tampil di sini setelah ada aktivitas pada pesanan Anda.</p>
            </div>
        </div>

        <button type="button" onclick="closeModalMitra('modalMitraNotif')" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition-colors cursor-pointer">
            Tutup
        </button>
    </div>
</div>

{{-- MODAL 3: ✍️ AJUKAN PENAWARAN KE CUSTOMER --}}
<div id="modalAjukanPenawaran" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-white rounded-2xl p-4 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <div>
                <h3 class="text-xs font-bold text-slate-900">Ajukan Penawaran Layanan</h3>
                <p id="labelModalPenawaranJudul" class="text-[9.5px] text-slate-400 truncate">Nama Kebutuhan</p>
            </div>
            <button type="button" onclick="closeModalMitra('modalAjukanPenawaran')" class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[10px] space-y-0.5">
            <div class="flex justify-between">
                <span class="text-slate-500">Pemesan:</span>
                <strong id="labelModalPenawaranCustomer" class="text-slate-800">Ibu Customer</strong>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-500">Budget Customer:</span>
                <strong id="labelModalPenawaranBudget" class="text-[#173B67] font-bold text-xs">Rp 0</strong>
            </div>
        </div>

        {{-- OPSI 1: TOMBOL CEPAT SEPAKATI HARGA BUDGET CUSTOMER --}}
        <div class="p-2.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-300 flex items-center justify-between gap-2 shadow-2xs">
            <div class="min-w-0">
                <span class="text-[9.5px] font-bold text-emerald-950 block leading-tight">Cocok dengan tarif Customer?</span>
                <span class="text-[8.5px] text-emerald-700">Sepakati langsung & teruskan ke Admin</span>
            </div>
            <button
                type="button"
                id="btnModalSepakatiHargaLangsung"
                onclick="mitraModalLangsungSepakati()"
                class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-black text-[10px] shrink-0 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <i class="fa-solid fa-check"></i>
                <span id="labelBtnModalSepakatiText">Sepakati Harga</span>
            </button>
        </div>

        {{-- PEMISAH OPSI --}}
        <div class="relative flex py-0.5 items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-2 text-[8.5px] font-bold text-slate-400 uppercase tracking-wider">Atau Tawar Harga Lain</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <form id="formKirimPenawaran" onsubmit="handleKirimPenawaranSubmit(event)" class="space-y-2.5 text-[10px]">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nominal Harga Penawaran Anda (Rp)</label>
                <input
                    type="number"
                    id="inputHargaPenawaran"
                    required
                    placeholder="Contoh: 85000"
                    class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 focus:border-[#173B67] text-xs font-bold outline-none"
                >
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Estimasi Durasi Pengerjaan</label>
                <select id="selectDurasiPenawaran" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 focus:border-[#173B67] text-xs outline-none">
                    <option value="1 Jam">1 Jam Pengerjaan</option>
                    <option value="2 Jam" selected>2 Jam Pengerjaan</option>
                    <option value="3 Jam">3 Jam Pengerjaan</option>
                    <option value="Setengah Hari">Setengah Hari (4-5 Jam)</option>
                    <option value="1 Hari Penuh">1 Hari Penuh</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan / Pesan untuk Customer</label>
                <textarea
                    id="textareaCatatanPenawaran"
                    rows="2"
                    placeholder="Contoh: Siap datang tepat waktu membawa peralatan lengkap."
                    class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 focus:border-[#173B67] text-xs outline-none"
                ></textarea>
            </div>

            <div class="pt-1 flex items-center gap-2">
                <button
                    type="button"
                    onclick="closeModalMitra('modalAjukanPenawaran')"
                    class="py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] transition-all cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btnSubmitPenawaran"
                    class="flex-1 py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10.5px] shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span id="btnSubmitPenawaranText">Kirim Penawaran Nego ke Customer</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3.5: 📋 DETAIL LENGKAP KEBUTUHAN SURUHAN MITRA --}}
<div id="modalDetailKebutuhanMitra" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-white rounded-2xl p-4 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <div>
                <span class="text-[8.5px] font-extrabold uppercase px-1.5 py-0.2 rounded bg-sky-100 text-sky-800">Detail Kebutuhan</span>
                <h3 id="detKebJudul" class="text-xs font-bold text-slate-900 mt-0.5">Judul Kebutuhan</h3>
            </div>
            <button type="button" onclick="closeModalMitra('modalDetailKebutuhanMitra')" class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[10px] space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500">Pemesan:</span>
                <strong id="detKebCustomer" class="text-slate-800">-</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Lokasi:</span>
                <span id="detKebLokasi" class="text-slate-700 font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Waktu Pelaksanaan:</span>
                <span id="detKebWaktu" class="text-sky-700 font-semibold">-</span>
            </div>
            <div class="flex justify-between border-t border-slate-200/60 pt-1 mt-1">
                <span class="text-slate-500">Budget Customer:</span>
                <strong id="detKebBudget" class="text-[#173B67] text-xs font-black">-</strong>
            </div>
        </div>

        <div class="space-y-1">
            <span class="text-[10px] font-bold text-slate-700">Deskripsi Tugas:</span>
            <p id="detKebDesc" class="text-[10px] text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed"></p>
        </div>

        <div class="pt-1 flex items-center gap-2">
            <button type="button" onclick="closeModalMitra('modalDetailKebutuhanMitra')" class="w-1/3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] cursor-pointer">
                Tutup
            </button>
            <button type="button" id="btnDetKebAjukan" class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10.5px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer active:scale-95 transition-all">
                <i class="fa-solid fa-user-check text-[10px]"></i> Pilih Customer Ini
            </button>
        </div>
    </div>
</div>

{{-- MODAL 4: 👤 PROFIL SINGKAT MITRA (DIBUKA DARI TOMBOL DI HEADER) --}}
<div id="modalMitraQuickProfile" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80">

    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-sky-400 to-[#173B67] text-white font-black text-base flex items-center justify-center shadow-md border-2 border-white/30" id="quickMitraAvatarInitial">
                    {{ $mitraAuthInitial }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-bold leading-tight truncate" id="quickMitraName">{{ $mitraAuthName }}</h3>
                    <span class="text-[10px] text-slate-300" id="quickMitraIdBadge">SayaBantu {{ $mitraAuthId }}</span>
                </div>
            </div>
            <button type="button" onclick="closeModalMitra('modalMitraQuickProfile')" class="text-slate-300 hover:text-white p-1" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="flex items-center gap-2.5 mt-2.5">
            <span class="text-[9px] font-semibold text-emerald-300">● Mitra Terverifikasi</span>
            <span class="text-[9px] font-bold text-white">
                @if($mitraAuthRating !== null)
                    <span class="text-yellow-400">★</span> {{ $mitraAuthRating }}
                @else
                    Belum ada ulasan
                @endif
                ({{ $mitraAuthReviewCount }} ulasan)
            </span>
        </div>
    </div>

    <div class="p-4 space-y-3 text-xs">

        {{-- IDENTITAS SINGKAT --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i>
                <span class="font-semibold">Nama Lengkap</span>
                <strong class="ml-auto text-slate-900 font-bold truncate" id="quickMitraFullName">{{ $mitraAuthName }}</strong>
            </div>
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-phone text-sky-600 w-4 text-center"></i>
                <span class="font-semibold">No. Handphone</span>
                <strong class="ml-auto text-slate-900 font-bold" id="quickMitraPhone">{{ $mitraAuthPhone }}</strong>
            </div>
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-regular fa-envelope text-sky-600 w-4 text-center"></i>
                <span class="font-semibold">Email</span>
                <strong class="ml-auto text-slate-900 font-bold truncate" id="quickMitraEmail">{{ $mitraAuthEmail }}</strong>
            </div>
        </div>

        {{-- RINGKASAN PERFORMA --}}
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                <strong class="text-xs font-black text-slate-900 block" id="quickMitraTasks">{{ $mitraAuthTasks }}</strong>
                <span class="text-[8.5px] text-slate-400 block mt-0.5">Tugas Selesai</span>
            </div>
            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                <strong class="text-[10px] font-bold text-slate-600 block">Belum ada data</strong>
                <span class="text-[8.5px] text-slate-400 block mt-0.5">Tingkat Respon</span>
            </div>
            <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                <strong class="text-[10px] font-bold text-slate-600 block">Belum ada data</strong>
                <span class="text-[8.5px] text-slate-400 block mt-0.5">Penyelesaian</span>
            </div>
        </div>

        {{-- STATUS SALDO --}}
        <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
            <span class="text-emerald-800 font-semibold"><i class="fa-solid fa-wallet text-emerald-600 w-4"></i> Saldo Tersedia</span>
            <strong class="text-emerald-700 font-black" id="quickMitraSaldo">Rp 0</strong>
        </div>
        </div>

        {{-- TOMBOL PROFIL LENGKAP --}}
        <button
            type="button"
            onclick="closeModalMitra('modalMitraQuickProfile'); switchMitraNav('profil');"
            class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
        >
            <i class="fa-solid fa-id-card"></i> Lihat Profil Lengkap
        </button>

        <div class="pt-1 border-t border-slate-100">
            <button
                type="button"
                onclick="closeModalMitra('modalMitraQuickProfile'); logoutMitraAction();"
                class="w-full py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer"
            >
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar dari Akun Mitra
            </button>
        </div>

    </div>

</div>

{{-- =========================================================
     MODAL 5: 📢 FORMULIR AJUKAN KELUHAN MITRA
========================================================= --}}
<div id="modalAjukanKeluhanMitra" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white">Formulir Pengaduan Mitra</h3>
                    <p class="text-[9.5px] text-sky-200 mt-0.5">Sampaikan laporan kendala penugasan &amp; perlindungan hak</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAjukanKeluhanMitra()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- FORM BODY --}}
        <form id="formPengaduanMitra" onsubmit="submitPengaduanMitra(event)" class="p-4 space-y-3 overflow-y-auto text-xs flex-1">

            {{-- 1. PEMILIHAN SURUHAN / PEKERJAAN BERMASALAH --}}
            <div class="space-y-1">
                <label for="pilihSuruhanMitra" class="block text-[11px] font-bold text-slate-800">
                    Pilih Pekerjaan / Suruhan yang Bermasalah <span class="text-rose-500">*</span>
                </label>
                <select
                    id="pilihSuruhanMitra"
                    onchange="onSelectMitraSuruhanPengaduan(this)"
                    required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                    <option value="">-- Pilih dari riwayat tugas Anda --</option>
                    {{-- Diisi secara dinamis oleh JS dari sb_customer_orders --}}
                    <option value="manual">Tulis nomor order / customer secara manual...</option>
                </select>
                <div id="wrapManualOrderMitra" class="hidden mt-1.5">
                    <input
                        type="text"
                        id="inputManualOrderMitra"
                        placeholder="Contoh: #SB-SURUH-8821 atau sebutkan nama pekerjaan..."
                        class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#173B67]"
                    >
                </div>
            </div>

            {{-- 2. PIHAK TERLAPOR (CUSTOMER) --}}
            <div class="space-y-1">
                <label for="inputNamaCustomerTerlapor" class="block text-[11px] font-bold text-slate-800">
                    Pihak Terlapor (Nama Customer / Pengguna) <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="inputNamaCustomerTerlapor"
                    required
                    placeholder="Contoh: Dimas Pratama (Customer)"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
            </div>

            {{-- 3. KATEGORI KELUHAN MITRA --}}
            <div class="space-y-1">
                <label for="pilihKategoriKeluhanMitra" class="block text-[11px] font-bold text-slate-800">
                    Kategori Kendala / Keluhan <span class="text-rose-500">*</span>
                </label>
                <select
                    id="pilihKategoriKeluhanMitra"
                    required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                    <option value="">-- Pilih Kategori Kendala --</option>
                    <option value="Customer Tidak Merespon / Menghilang">Customer Tidak Merespon / Menghilang saat Tiba</option>
                    <option value="Alamat Palsu / Lokasi Berbahaya">Alamat Palsu / Lokasi Tidak Aman</option>
                    <option value="Penolakan Bayar / Pembatalan Sepihak">Penolakan Bayar / Pembatalan Sepihak</option>
                    <option value="Permintaan di Luar Kesepakatan SOP">Permintaan Tugas di Luar Kesepakatan SOP</option>
                    <option value="Perlakuan Kasar / Intimidasi">Perlakuan Kasar / Intimidasi dari Customer</option>
                    <option value="Kendala Teknis Aplikasi">Kendala Teknis Platform / Aplikasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            {{-- 4. DESKRIPSI KRONOLOGI KEJADIAN --}}
            <div class="space-y-1">
                <label for="inputKronologiMitra" class="block text-[11px] font-bold text-slate-800">
                    Deskripsi Kronologi Kejadian <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="inputKronologiMitra"
                    rows="3"
                    required
                    minlength="10"
                    placeholder="Jelaskan kronologi kejadian secara runtut: jam tiba di lokasi, respon customer, dan kendala yang dihadapi..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67] leading-relaxed resize-none"
                ></textarea>
                <span class="text-[9.5px] text-slate-400 block">Jelaskan fakta secara jujur dan objektif untuk mempermudah investigasi admin.</span>
            </div>

            {{-- 5. BUKTI PENDUKUNG --}}
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-800">
                    Bukti Pendukung (Foto Lokasi / Chat / Screenshot)
                </label>
                <div class="p-3 border-2 border-dashed border-slate-200 hover:border-slate-300 rounded-xl text-center bg-slate-50/50 cursor-pointer transition-colors relative">
                    <input
                        type="file"
                        id="inputFileBuktiMitra"
                        accept="image/*"
                        onchange="handlePreviewBuktiMitra(this)"
                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                    >
                    <div id="buktiPreviewPlaceholderMitra" class="space-y-1">
                        <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-lg"></i>
                        <p class="text-[10px] text-slate-600 font-semibold">Klik untuk unggah foto bukti pendukung</p>
                        <p class="text-[8.5px] text-slate-400">JPG, PNG maksimal 5MB</p>
                    </div>
                    <div id="buktiPreviewActiveMitra" class="hidden flex items-center justify-center gap-2 text-emerald-600 font-bold text-[10.5px]">
                        <i class="fa-solid fa-file-circle-check text-base"></i>
                        <span id="namaFileBuktiMitra">bukti-foto.jpg</span>
                    </div>
                </div>
            </div>

            {{-- 6. PERNYATAAN KEBENARAN DATA --}}
            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[10px] text-amber-900 flex items-start gap-2">
                <input type="checkbox" id="checkIntegritasMitra" required class="mt-0.5 accent-[#173B67] rounded w-3.5 h-3.5 cursor-pointer">
                <label for="checkIntegritasMitra" class="cursor-pointer leading-tight">
                    Saya menyatakan informasi dan kronologi di atas adalah benar sesuai kejadian nyata di lapangan.
                </label>
            </div>

            {{-- BUTTONS --}}
            <div class="pt-2 flex items-center gap-2">
                <button
                    type="button"
                    onclick="closeModalAjukanKeluhanMitra()"
                    class="w-1/3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btnKirimPengaduanMitra"
                    class="w-2/3 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 active:scale-[0.98] text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim Laporan Mediasi</span>
                </button>
            </div>

        </form>

    </div>
</div>

{{-- =========================================================
     MODAL 6: 🔍 DETAIL & TIMELINE PENGADUAN MITRA
========================================================= --}}
<div id="modalDetailPengaduanMitra" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-[#0E1D31] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white flex items-center gap-1.5">
                        <span id="detMitraTiketId">#ADU-000</span>
                        <span id="detMitraTiketBadge" class="text-[8.5px] font-extrabold px-2 py-0.2 rounded-full bg-rose-500/20 text-rose-300 border border-rose-400/30">
                            Menunggu
                        </span>
                    </h3>
                    <p class="text-[9.5px] text-sky-200 mt-0.5" id="detMitraTiketKategori">Kategori Pengaduan</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailPengaduanMitra()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- DETAIL BODY --}}
        <div class="p-4 space-y-3.5 overflow-y-auto text-xs flex-1">

            {{-- RINGKASAN SURUHAN & PIHAK TERLAPOR --}}
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-[10px]">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Nomor Suruhan:</span>
                    <strong class="text-slate-900" id="detMitraTiketOrder">-</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Customer Terlapor:</span>
                    <strong class="text-rose-700" id="detMitraTiketReported">-</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Waktu Dilaporkan:</span>
                    <span class="text-slate-700" id="detMitraTiketDate">-</span>
                </div>
            </div>

            {{-- KRONOLOGI KEJADIAN --}}
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-900 block flex items-center gap-1">
                    <i class="fa-solid fa-file-lines text-[#173B67]"></i> Kronologi yang Dilaporkan
                </span>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-[10px] leading-relaxed whitespace-pre-wrap" id="detMitraTiketChronology">
                    -
                </div>
            </div>

            {{-- BUKTI PENDUKUNG --}}
            <div class="space-y-1" id="detMitraTiketEvidenceWrap">
                <span class="text-[11px] font-bold text-slate-900 block flex items-center gap-1">
                    <i class="fa-solid fa-paperclip text-sky-600"></i> Bukti Pendukung
                </span>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[10px] text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-image text-slate-400 text-base"></i>
                    <span id="detMitraTiketEvidenceText" class="truncate flex-1">-</span>
                </div>
            </div>

            {{-- TIMELINE PERKEMBANGAN PENANGANAN ADMIN --}}
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <span class="text-[11px] font-extrabold text-slate-900 block flex items-center gap-1.5">
                    <i class="fa-solid fa-timeline text-emerald-600"></i> Perkembangan Penanganan Kasus
                </span>

                <div class="relative pl-5 space-y-3 before:absolute before:left-2 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200" id="detMitraTiketTimeline">
                    {{-- Diisi secara dinamis --}}
                </div>
            </div>

            {{-- KARTU STATUS TINDAK LANJUT MEDIASI MITRA (DINAMIS SESUAI STATUS) --}}
            <div id="detMitraActionBanner" class="p-3.5 rounded-2xl border space-y-2 text-xs hidden">
                {{-- Diisi dinamis oleh JavaScript --}}
            </div>

            {{-- CATATAN HASIL MEDIASI DARI ADMIN --}}
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 space-y-1 text-[10px] hidden" id="detMitraTiketAdminDecisionBox">
                <strong class="text-emerald-900 font-bold block flex items-center gap-1">
                    <i class="fa-solid fa-clipboard-check text-emerald-600"></i> Catatan Hasil Mediasi
                </strong>
                <p class="text-emerald-800 leading-relaxed" id="detMitraTiketAdminDecisionText">-</p>
            </div>

            {{-- KONTAK ESKALASI DISPATCHER --}}
            <div class="pt-2">
                <div class="w-full py-2 px-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-[10px] text-center flex items-center justify-center gap-1.5 border border-slate-200">
                    <i class="fa-solid fa-shield-halved text-sky-600 text-xs"></i> Tim Mediasi Admin Memproses Tiket Otomatis
                </div>
            </div>

        </div>

    </div>
</div>

{{-- =========================================================
     MODAL 7: RUANG MEDIASI 3-PIHAK KHUSUS MITRA
========================================================= --}}
<div id="modalRuangMediasiMitra" class="fixed inset-0 z-[160] hidden items-center justify-center p-4 bg-black/65 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[92vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold leading-tight text-white" id="mitraRoomTitle">Ruang Mediasi</h3>
                        <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-teal-500/30 text-teal-200 border border-teal-400/30 animate-pulse">
                            Aktif
                        </span>
                    </div>
                    <p class="text-[9.5px] text-sky-200 mt-0.5" id="mitraRoomSub">Fasilitasi mediasi 3-pihak resmi SayaBantu</p>
                </div>
            </div>
            <button type="button" onclick="closeRuangMediasiMitraModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- DAFTAR PIHAK TERLIBAT (3 PIHAK) --}}
        <div class="px-4 py-2 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-[9.5px] shrink-0">
            <div class="flex items-center gap-1.5 text-teal-700 font-bold">
                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                <span>Admin (Fasilitator)</span>
            </div>
            <div class="flex items-center gap-1.5 text-sky-800 font-bold">
                <i class="fa-solid fa-user text-[10px]"></i>
                <span id="mitraRoomCustLabel">Customer Pemesan</span>
            </div>
            <div class="flex items-center gap-1.5 text-rose-700 font-bold">
                <i class="fa-solid fa-helmet-safety text-[10px]"></i>
                <span id="mitraRoomSelfLabel">Anda (Mitra)</span>
            </div>
        </div>

        {{-- AREA CHAT & KARTU USULAN (SCROLLABLE) --}}
        <div class="p-4 space-y-3.5 overflow-y-auto text-xs flex-1 bg-slate-50/50" id="mitraMediationScrollArea">

            {{-- KARTU USULAN PENYELESAIAN DAMAI DARI ADMIN --}}
            <div id="mitraProposalCard" class="hidden p-3.5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-300 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between pb-1.5 border-b border-emerald-200">
                    <span class="text-[10.5px] font-black text-emerald-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-scale-balanced text-emerald-600"></i>
                        USULAN KESEPAKATAN DAMAI (MUFAKAT)
                    </span>
                    <span id="mitraProposalBadge" class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        Menunggu Persetujuan
                    </span>
                </div>

                <div class="text-[10px] text-emerald-950 bg-white/80 p-2.5 rounded-xl border border-emerald-200 leading-relaxed space-y-1">
                    <strong class="font-bold text-slate-800 block text-[9.5px]">Poin Kesepakatan yang Ditawarkan:</strong>
                    <p id="mitraProposalTermsText" class="leading-relaxed">
                        Pekerjaan diselesaikan sesuai rincian awal tanpa pemungutan biaya di luar kesepakatan aplikasi.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[9px] pt-1">
                    <div class="p-2 rounded-xl bg-white/80 border border-emerald-100 flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Customer:</span>
                        <span id="mitraProposalCustStatus" class="font-bold text-slate-600">Menunggu</span>
                    </div>
                    <div class="p-2 rounded-xl bg-white/80 border border-emerald-100 flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Mitra (Anda):</span>
                        <span id="mitraProposalSelfStatus" class="font-bold text-amber-600">Menunggu</span>
                    </div>
                </div>

                {{-- TOMBOL AKSI MITRA TERHADAP USULAN --}}
                <div class="pt-1 flex items-center gap-2" id="mitraProposalActionButtons">
                    <button
                        type="button"
                        onclick="mitraApproveProposal()"
                        class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-extrabold text-[10.5px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Setujui Usulan (Mitra)</span>
                    </button>
                    <button
                        type="button"
                        onclick="mitraRequestProposalChange()"
                        class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-300 cursor-pointer transition-all"
                    >
                        Minta Perubahan
                    </button>
                </div>

                <div id="mitraProposalAgreedNotice" class="hidden p-2 rounded-xl bg-emerald-600 text-white text-[10px] text-center font-bold">
                    <i class="fa-solid fa-circle-check mr-1"></i> Anda telah menyetujui poin usulan penyelesaian ini.
                </div>
            </div>

            {{-- DAFTAR PESAN MEDIASI 3-PIHAK --}}
            <div class="space-y-2.5" id="mitraMediationMessagesList">
                {{-- Diisi secara dinamis oleh JS --}}
            </div>

        </div>

        {{-- CHAT INPUT BAR --}}
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="mitraMediationInputText"
                    placeholder="Tulis penjelasan atau respon mediasi..."
                    onkeydown="if(event.key==='Enter') sendMitraMediationMessage()"
                    class="flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                <button
                    type="button"
                    onclick="sendMitraMediationMessage()"
                    class="py-2 px-4 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] active:scale-95 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all cursor-pointer shrink-0"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim</span>
                </button>
            </div>
            <span class="text-[8.5px] text-slate-400 mt-1 block">Seluruh komunikasi dicatat resmi untuk akuntabilitas mediasi.</span>
        </div>

    </div>
</div>

{{-- =========================================================
     MODAL 8: UNGGAH BUKTI TAMBAHAN OLEH MITRA
========================================================= --}}
<div id="modalUploadBuktiMitra" class="fixed inset-0 z-[160] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-amber-600 to-yellow-600 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-circle-question"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white">Unggah Bukti Tambahan Mitra</h3>
                    <p class="text-[9.5px] text-amber-100 mt-0.5">Permintaan klarifikasi bukti oleh Tim Investigasi Admin</p>
                </div>
            </div>
            <button type="button" onclick="closeUploadBuktiMitraModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formUploadBuktiMitra" onsubmit="submitUploadBuktiMitra(event)" class="p-4 space-y-3.5 text-xs overflow-y-auto">
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 space-y-1 text-[10px] text-amber-900">
                <strong class="font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-info text-amber-600"></i> Petunjuk Admin:
                </strong>
                <p id="mitraReqEvidenceInstructionsText" class="leading-relaxed">-</p>
                <div class="pt-1 text-[9px] text-amber-700 font-semibold" id="mitraReqEvidenceDeadlineText">
                    Batas Waktu: -
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-800">
                    Pilih File Foto Bukti Pekerjaan / Nota <span class="text-rose-500">*</span>
                </label>
                <div class="p-3.5 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-amber-400 transition-colors text-center cursor-pointer" onclick="document.getElementById('inputBuktiTambahanMitraFile').click()">
                    <input
                        type="file"
                        id="inputBuktiTambahanMitraFile"
                        accept="image/*"
                        required
                        onchange="previewBuktiTambahanMitra(this)"
                        class="hidden"
                    >
                    <div id="phBuktiTambahanMitra">
                        <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                        <p class="text-[10.5px] font-bold text-slate-700 mt-1">Pilih Dokumen Bukti</p>
                        <p class="text-[9px] text-slate-400">Format JPG, PNG (Maks 5 MB)</p>
                    </div>
                    <div id="actBuktiTambahanMitra" class="hidden items-center justify-center gap-2">
                        <i class="fa-solid fa-file-image text-emerald-600 text-lg"></i>
                        <div class="text-left">
                            <span id="nameBuktiTambahanMitra" class="block text-[10px] font-bold text-slate-800 truncate max-w-[200px]">file.jpg</span>
                            <span class="text-[9px] text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Siap diunggah</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label for="inputCatatanBuktiTambahanMitra" class="block text-[11px] font-bold text-slate-800">
                    Keterangan Klarifikasi Mitra <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="inputCatatanBuktiTambahanMitra"
                    rows="3"
                    required
                    placeholder="Jelaskan kondisi riil di lapangan dan dasar bukti ini..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 leading-relaxed resize-none"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeUploadBuktiMitraModal()"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 active:scale-95 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim Bukti ke Admin</span>
                </button>
            </div>
        </form>

    </div>
</div>

{{-- =========================================================
     MODAL 9: ⚠️ PEMBATALAN TUGAS OLEH MITRA (SKENARIO C)
========================================================= --}}
<div id="modalBatalMitra" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- HEADER --}}
        <div class="p-4 bg-gradient-to-r from-rose-700 to-rose-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white">Batalkan Tugas Penugasan</h3>
                    <p class="text-[9.5px] text-rose-200 mt-0.5">Skenario Pembatalan Mitra Pasca-Kesepakatan</p>
                </div>
            </div>
            <button type="button" onclick="closeModalBatalMitra()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-4 space-y-3 overflow-y-auto text-xs flex-1">
            <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-[10px] text-rose-800 leading-relaxed">
                <strong><i class="fa-solid fa-circle-exclamation mr-1"></i> Perhatian:</strong> Pembatalan tugas setelah kesepakatan akan dikenakan <strong>penalti performa otomatis (-10 poin)</strong>. Pesanan akan dikembalikan ke status pencarian mitra pengganti agar pelanggan tetap terlayani.
            </div>

            <div class="space-y-1">
                <span class="block text-[11px] font-bold text-slate-800">
                    Alasan Pembatalan Mendesak <span class="text-rose-500">*</span>
                </span>
                <p class="text-[9.5px] text-slate-500">Pilih salah satu alasan darurat berikut:</p>

                <div class="space-y-2 mt-2">
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9.5px] text-slate-700">
                        <input type="radio" name="mitraCancelReasonPreset" value="Kendala darurat / sakit mendesak" checked onchange="toggleMitraCustomCancelReason(false)">
                        <span>Kendala darurat / sakit mendesak</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9.5px] text-slate-700">
                        <input type="radio" name="mitraCancelReasonPreset" value="Kendaraan mogok / kendala transportasi di perjalanan" onchange="toggleMitraCustomCancelReason(false)">
                        <span>Kendaraan mogok / kendala transportasi</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9.5px] text-slate-700">
                        <input type="radio" name="mitraCancelReasonPreset" value="Lokasi pengerjaan tidak sesuai dengan deskripsi awal aplikasi" onchange="toggleMitraCustomCancelReason(false)">
                        <span>Lokasi tidak sesuai deskripsi awal</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9.5px] text-slate-700">
                        <input type="radio" name="mitraCancelReasonPreset" value="custom" onchange="toggleMitraCustomCancelReason(true)">
                        <span>Tuliskan alasan lainnya...</span>
                    </label>
                </div>

                <div id="mitraCustomCancelReasonWrap" class="mt-2 hidden">
                    <textarea
                        id="mitraCustomCancelReasonInput"
                        rows="2"
                        placeholder="Jelaskan kendala darurat Anda secara jelas..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 leading-relaxed resize-none"
                    ></textarea>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeModalBatalMitra()"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer"
                >
                    Kembali
                </button>
                <button
                    type="button"
                    id="btnSubmitBatalMitra"
                    onclick="submitBatalMitraAction()"
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-ban text-[10px]"></i>
                    <span>Konfirmasi Batalkan</span>
                </button>
            </div>
        </div>

    </div>
</div>

@endsection

@section('modals')
{{-- MODAL DETAIL STATISTIK MITRA --}}
<div id="mitraStatDetailModal" class="sb-overlay-backdrop fixed inset-0 z-[220] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closeMitraStatDetail()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div class="relative w-full bg-white pointer-events-auto rounded-t-[26px] shadow-2xl flex flex-col max-h-[80vh] overflow-hidden">
            <div class="pt-3 pb-1 shrink-0"><div class="w-12 h-1 bg-gray-300 rounded-full mx-auto"></div></div>
            <div class="px-5 pb-3 border-b border-gray-100 flex items-start justify-between gap-3 shrink-0">
                <div class="min-w-0">
                    <h3 id="mitraStatDetailTitle" class="text-sm font-bold text-gray-900 truncate">Ringkasan Pekerjaan</h3>
                    <p id="mitraStatDetailSubtitle" class="text-[10px] text-gray-400 truncate">Detail statistik mitra</p>
                </div>
                <button type="button" onclick="closeMitraStatDetail()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center shrink-0 cursor-pointer" aria-label="Tutup">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div id="mitraStatDetailBody" class="flex-1 overflow-y-auto px-5 py-4 space-y-2.5 text-xs"></div>
            <div class="p-4 border-t border-gray-100 shrink-0">
                <button type="button" onclick="closeMitraStatDetail()" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold text-xs transition-all cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // State Mitra
    let isMitraStandby = true;

    // =========================================================
    //  DETAIL RINGKASAN STATISTIK MITRA (klik kartu statistik)
    // =========================================================
    function showMitraStatDetail(kind) {
        var titles = {
            tuntas: ['Total Pekerjaan Tuntas', 'Pekerjaan yang telah selesai dikerjakan'],
            rating: ['Rating & Ulasan', 'Penilaian dari pelanggan yang dilayani'],
            penyelesaian: ['Tingkat Penyelesaian', 'Perbandingan pekerjaan selesai vs diterima']
        };
        var picked = titles[kind] || titles.tuntas;
        var totalEl = document.getElementById('statRingkasanTotalTuntas');
        var ratingEl = document.getElementById('statRingkasanRating');
        var pctEl = document.getElementById('statRingkasanPenyelesaian');

        var rows = [
            ['Total Pekerjaan Tuntas', (totalEl ? totalEl.textContent.trim() : '0') + ' pekerjaan'],
            ['Rating Rata-rata', ratingEl ? ratingEl.textContent.trim() : '-'],
            ['Tingkat Penyelesaian', pctEl ? pctEl.textContent.trim() : '0%'],
            ['Sumber Data', 'Riwayat pesanan & ulasan di platform'],
            ['Periode', 'Sejak akun mitra aktif']
        ];

        var modal = document.getElementById('mitraStatDetailModal');
        if (!modal) {
            if (typeof showToast === 'function') {
                showToast(picked[0], rows.map(function (r) { return r[0] + ': ' + r[1]; }).join(' \u2022 '), 'info');
            }
            return;
        }

        var titleEl = document.getElementById('mitraStatDetailTitle');
        var subEl = document.getElementById('mitraStatDetailSubtitle');
        var bodyEl = document.getElementById('mitraStatDetailBody');
        if (titleEl) titleEl.textContent = picked[0];
        if (subEl) subEl.textContent = picked[1];
        if (bodyEl) {
            bodyEl.innerHTML = rows.map(function (row) {
                return '<div class="flex items-start justify-between gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">' +
                    '<span class="text-slate-500 shrink-0">' + row[0] + '</span>' +
                    '<span class="font-semibold text-slate-900 text-right">' + row[1] + '</span>' +
                    '</div>';
            }).join('');
        }

        // .is-open adalah satu-satunya penanda terbuka; kelas pengunci
        // Tailwind (.invisible/.opacity-0/.pointer-events-none) wajib dilepas.
        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open');
        modal.removeAttribute('aria-hidden');
    }
    window.showMitraStatDetail = showMitraStatDetail;

    function closeMitraStatDetail() {
        var modal = document.getElementById('mitraStatDetailModal');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.classList.add('invisible', 'opacity-0', 'pointer-events-none');
        modal.setAttribute('aria-hidden', 'true');
    }
    window.closeMitraStatDetail = closeMitraStatDetail;

    // Toggle Status Online / Standby
    function toggleStatusMitraStandby() {
        isMitraStandby = !isMitraStandby;
        const btn = document.getElementById('btnToggleStatusMitra');
        const text = document.getElementById('textStatusMitra');
        const dot = document.getElementById('dotStatusMitra');

        if (isMitraStandby) {
            btn.className = "shrink-0 py-1.5 px-3 rounded-full bg-emerald-500 text-white font-extrabold text-[10px] shadow-sm flex items-center gap-1.5 border border-emerald-300/40 transition-all active:scale-95 cursor-pointer";
            text.innerText = "Online (Siap Kerja)";
            dot.className = "w-2 h-2 rounded-full bg-white animate-pulse";
        } else {
            btn.className = "shrink-0 py-1.5 px-3 rounded-full bg-slate-700 text-slate-200 font-extrabold text-[10px] shadow-sm flex items-center gap-1.5 border border-white/20 transition-all active:scale-95 cursor-pointer";
            text.innerText = "Offline (Istirahat)";
            dot.className = "w-2 h-2 rounded-full bg-slate-400";
        }
    }

    // Popup profil singkat mitra (tombol profil di header)
    function openMitraQuickProfile() {
        if (typeof syncAllMitraProfileElements === 'function') {
            syncAllMitraProfileElements();
        }
        openModalMitra('modalMitraQuickProfile');
    }

    function lockMitraBodyScroll() {
        document.documentElement.classList.add('overflow-hidden', 'sb-scroll-locked');
        document.body.classList.add('overflow-hidden', 'sb-scroll-locked');
    }

    function unlockMitraBodyScroll() {
        document.documentElement.classList.remove('overflow-hidden', 'sb-scroll-locked');
        document.body.classList.remove('overflow-hidden', 'sb-scroll-locked');
    }

    // Burger Menu Drawer Open / Close (Scoped inside 430px frame)
    function openMitraBurgerDrawer() {
        const overlay = document.getElementById('drawerMitraOverlay');
        const panel = document.getElementById('drawerMitraPanel');
        if (overlay && panel) {
            overlay.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            panel.classList.remove('-translate-x-full');
            lockMitraBodyScroll();
        }
    }

    function closeMitraBurgerDrawer() {
        const overlay = document.getElementById('drawerMitraOverlay');
        const panel = document.getElementById('drawerMitraPanel');
        if (overlay && panel) {
            overlay.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            panel.classList.add('-translate-x-full');
            unlockMitraBodyScroll();
        }
    }

    // Cegah background touchmove pada backdrop mitra & bind tombol Escape
    document.addEventListener('DOMContentLoaded', function() {
        const mBackdrop = document.getElementById('mitraDrawerBackdrop');
        if (mBackdrop) {
            mBackdrop.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, { passive: false });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const overlay = document.getElementById('drawerMitraOverlay');
            if (overlay && !overlay.classList.contains('invisible')) {
                closeMitraBurgerDrawer();
            }
        }
    });

    // Modal Helpers
    function openModalMitra(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModalMitra(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('hidden');
    }

    function openMitraSearchModal() {
        openModalMitra('modalMitraSearch');
        const inp = document.getElementById('inputCariKebutuhan');
        if (inp) setTimeout(() => inp.focus(), 200);
    }

    function openMitraNotifModal() {
        openModalMitra('modalMitraNotif');
    }

    // Buka Modal Ajukan Penawaran
    function bukaModalAjukanPenawaran(judul, budget, cust, area) {
        document.getElementById('labelModalPenawaranJudul').innerText = judul;
        document.getElementById('labelModalPenawaranCustomer').innerText = cust + ' (' + area + ')';
        document.getElementById('labelModalPenawaranBudget').innerText = budget;

        const sepakatiBtn = document.getElementById('labelBtnModalSepakatiText');
        if (sepakatiBtn) {
            sepakatiBtn.innerText = 'Sepakati ' + budget;
        }

        const rawPrice = budget.replace(/[^0-9]/g, '');
        const inputHarga = document.getElementById('inputHargaPenawaran');
        if (inputHarga) {
            inputHarga.value = rawPrice || 75000;
        }

        openModalMitra('modalAjukanPenawaran');
    }

    function getCurrentMitraProfile() {
        const serverData = window.CURRENT_MITRA_DATA || {};
        let profile = {
            id: serverData.id || '',
            name: serverData.name || 'Mitra',
            phone: serverData.phone || '',
            email: serverData.email || '',
            rating: serverData.rating ?? null,
            tugas: String(serverData.tasks ?? '0'),
            spesialis: serverData.specialty || 'Belum ada layanan',
            area: serverData.area || 'Wilayah belum diatur'
        };
        try {
            const rawAuth = localStorage.getItem('sb_mitra_auth');
            if (rawAuth) {
                const u = JSON.parse(rawAuth);
                if (u && (u.name || '').toLowerCase().match(/bambang|budi|hendra|dimas|slamet/)) {
                    u.name = profile.name;
                    u.phone = profile.phone;
                    u.id = profile.id;
                    u.email = profile.email;
                    localStorage.setItem('sb_mitra_auth', JSON.stringify(u));
                }
            }
            const rawM = localStorage.getItem('sb_mitra_registration');
            if (rawM) {
                const m = JSON.parse(rawM);
                if (m && (m.nama || m.name || '').toLowerCase().match(/bambang|budi|hendra|dimas|slamet/)) {
                    m.nama = profile.name;
                    m.whatsapp = profile.phone;
                    m.id = profile.id;
                    localStorage.setItem('sb_mitra_registration', JSON.stringify(m));
                }
            }
        } catch(e) {}
        return profile;
    }
    window.getCurrentMitraProfile = getCurrentMitraProfile;

    // Handle Submit Penawaran
    async function handleKirimPenawaranSubmit(e) {
        e.preventDefault();
        const rawHarga = document.getElementById('inputHargaPenawaran').value;
        const durasi = document.getElementById('selectDurasiPenawaran').value;
        const btn = document.getElementById('btnSubmitPenawaran');

        const currentMitra = getCurrentMitraProfile();
        const rawHargaNum = parseInt(String(rawHarga).replace(/[^0-9]/g, '')) || 85000;
        const hargaFormatted = rawHargaNum.toLocaleString('id-ID');

        const activeCode = window.__activeCustomerOrderCode;
        const cleanCode = (activeCode || '').replace('#', '');

        // Jika nominal penawaran sama persis dengan budget customer, perlakukan sebagai kesepakatan langsung
        const budgetEl = document.getElementById('labelModalPenawaranBudget');
        const budgetRawNum = budgetEl ? parseInt(budgetEl.innerText.replace(/[^0-9]/g, '')) || 0 : 0;
        if (budgetRawNum > 0 && rawHargaNum === budgetRawNum) {
            closeModalMitra('modalAjukanPenawaran');
            mitraLangsungSepakatiHargaCustomer(activeCode, hargaFormatted);
            return;
        }

        const bidData = {
            id: 'BID-' + Date.now(),
            mitraId: currentMitra.id,
            mitraName: currentMitra.name,
            mitraPhone: currentMitra.phone,
            rating: currentMitra.rating,
            tugasSelesai: currentMitra.tugas,
            spesialis: currentMitra.spesialis,
            harga: rawHargaNum,
            hargaFormatted: hargaFormatted,
            durasi: durasi,
            catatan: 'Siap hadir tepat waktu, pengerjaan rapi & bergaransi kepuasan pelanggan.',
            status: 'menunggu_keputusan',
            createdAt: new Date().toISOString()
        };

        btn.disabled = true;
        btn.innerText = 'Mengirim Penawaran...';

        if (!cleanCode) {
            showMitraToast('Penawaran Gagal', 'Pesanan tidak ditemukan. Muat ulang halaman dan coba lagi.', 'error');
            btn.disabled = false;
            btn.innerText = 'Kirim Penawaran ke Customer';
            return;
        }

        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(cleanCode)}/penawaran`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    harga: rawHargaNum,
                    durasi: durasi,
                    mitra_id: currentMitra.id,
                    mitra_name: currentMitra.name,
                    mitra_phone: currentMitra.phone,
                    catatan: bidData.catatan
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Penawaran belum berhasil dikirim.');
            }

            updateCustomerOrderStatus(activeCode, 'menunggu_keputusan', 5, bidData);
            window.__activeCustomerOrderCode = null;
            loadMitraCustomerOrders();
            showMitraToast('Penawaran Terkirim', data.message || `Penawaran Rp ${hargaFormatted} berhasil dikirim ke Customer.`, 'success');
            switchMitraNav('pekerjaan');
            filterStatusPekerjaan('penawaran', document.querySelectorAll('.btn-status-pek')[0]);
            closeModalMitra('modalAjukanPenawaran');
        } catch (error) {
            showMitraToast('Penawaran Gagal', error.message || 'Silakan coba lagi.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Kirim Penawaran ke Customer';
        }
    }

    // Handle Tarik Dana
    function handleTarikDanaSubmit(e) {
        e.preventDefault();
        showMitraToast('Penarikan Diproses', 'Permintaan tarik dana berhasil dikirim! Saldo akan masuk ke rekening Anda.', 'success');
        switchMitraNav('dashboard');
    }

    // Filter Kebutuhan Kategori
    function filterKebutuhanCat(cat, btn) {
        document.querySelectorAll('.btn-keb-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
        btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');

        let count = 0;
        document.querySelectorAll('.kebutuhan-card').forEach(card => {
            const cCat = card.getAttribute('data-cat');
            if (cat === 'all' || cCat === cat) {
                card.classList.remove('hidden');
                count++;
            } else {
                card.classList.add('hidden');
            }
        });

        const lbl = document.getElementById('labelJumlahKebutuhan');
        if (lbl) lbl.innerText = count + ' Kebutuhan Tersedia';
    }

    // Live Search Kebutuhan
    function liveSearchKebutuhan(keyword) {
        keyword = keyword.toLowerCase().trim();
        const container = document.getElementById('hasilSearchMitra');

        if (!keyword) {
            container.innerHTML = '<p class="text-[10px] text-slate-400 text-center py-3">Ketik kata kunci di atas untuk mencari kebutuhan suruhan.</p>';
            return;
        }

        let html = '';
        let found = 0;
        document.querySelectorAll('.kebutuhan-card').forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const textContent = card.innerText.toLowerCase();

            if (title.includes(keyword) || textContent.includes(keyword)) {
                found++;
                const cardTitle = card.querySelector('h3') ? card.querySelector('h3').innerText : 'Kebutuhan';
                const cardBudget = card.querySelector('strong.text-[#173B67]') ? card.querySelector('strong.text-[#173B67]').innerText : 'Rp -';

                html += `
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between cursor-pointer hover:bg-sky-50 transition-colors" onclick="closeModalMitra('modalMitraSearch'); switchMitraNav('kebutuhan');">
                        <div class="min-w-0 flex-1">
                            <strong class="text-slate-900 block truncate text-[10.5px]">${cardTitle}</strong>
                            <span class="text-[9px] text-slate-400">Cocok dengan pencarian</span>
                        </div>
                        <strong class="text-[10px] text-[#173B67] shrink-0 font-bold">${cardBudget}</strong>
                    </div>
                `;
            }
        });

        if (found === 0) {
            container.innerHTML = '<p class="text-[10px] text-slate-400 text-center py-3">Tidak ada kebutuhan yang cocok dengan "' + keyword + '".</p>';
        } else {
            container.innerHTML = html;
        }
    }

    // Filter Status Pekerjaan (5 Status)
    function filterStatusPekerjaan(status, btn) {
        document.querySelectorAll('.btn-status-pek').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold', 'border', 'border-transparent');
            b.classList.add('bg-white/10', 'text-white/90', 'font-medium', 'border', 'border-white/20');
        });
        if (btn) {
            btn.classList.remove('bg-white/10', 'text-white/90', 'font-medium', 'border-white/20');
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold', 'border', 'border-transparent');
        }

        document.querySelectorAll('.pek-status-group').forEach(grp => grp.classList.add('hidden'));

        if (status === 'penawaran') {
            document.getElementById('sectionPekPenawaran').classList.remove('hidden');
        } else if (status === 'dikonfirmasi') {
            document.getElementById('sectionPekDikonfirmasi').classList.remove('hidden');
        } else if (status === 'berlangsung') {
            document.getElementById('sectionPekBerlangsung').classList.remove('hidden');
        } else if (status === 'selesai') {
            document.getElementById('sectionPekSelesai').classList.remove('hidden');
        } else if (status === 'dibatalkan') {
            document.getElementById('sectionPekDibatalkan').classList.remove('hidden');
        }
    }

    // Aksi Selesaikan Pekerjaan
    function tandaiPekerjaanSelesai(layanan, harga) {
        showMitraToast('Pekerjaan Selesai!', `Pekerjaan "${layanan}" selesai. Komisi sebesar Rp ${harga} telah otomatis ditambahkan ke Saldo Mitra Anda.`, 'success');
        if (typeof loadMitraCustomerOrders === 'function') loadMitraCustomerOrders();
    }

    // Logout Mitra
    function logoutMitraAction() {
        localStorage.setItem('sb_mitra_logged_out', 'true');
        localStorage.removeItem('sb_mitra_auth');
        window.location.href = "{{ route('mitra.logout') }}";
    }

    // Lihat Detail Kebutuhan
    function lihatDetailKebutuhan(judul, budget, cust, lokasi, waktu, desc) {
        const modal = document.getElementById('modalDetailKebutuhanMitra');
        if (modal) {
            const elJudul = document.getElementById('detKebJudul');
            const elCust = document.getElementById('detKebCustomer');
            const elLokasi = document.getElementById('detKebLokasi');
            const elWaktu = document.getElementById('detKebWaktu');
            const elBudget = document.getElementById('detKebBudget');
            const elDesc = document.getElementById('detKebDesc');
            const btnAjukan = document.getElementById('btnDetKebAjukan');

            if (elJudul) elJudul.innerText = judul || 'Kebutuhan Suruhan';
            if (elCust) elCust.innerText = cust || '-';
            if (elLokasi) elLokasi.innerText = lokasi || '-';
            if (elWaktu) elWaktu.innerText = waktu || 'Segera';
            if (elBudget) elBudget.innerText = budget || 'Rp -';
            if (elDesc) elDesc.innerText = desc || 'Tidak ada catatan tambahan.';

            if (btnAjukan) {
                btnAjukan.onclick = function() {
                    closeModalMitra('modalDetailKebutuhanMitra');
                    mitraPilihCustomer(window.__activeDetailOrderCode || window.__activeCustomerOrderCode || judul, budget);
                };
            }
            openModalMitra('modalDetailKebutuhanMitra');
            return;
        }

        alert(
            "📋 DETAIL KEBUTUHAN:\n\n" +
            "• Layanan: " + judul + "\n" +
            "• Pemesan: " + cust + "\n" +
            "• Lokasi: " + lokasi + "\n" +
            "• Waktu: " + waktu + "\n" +
            "• Budget: " + budget + "\n\n" +
            "Keterangan: " + desc
        );
    }

    // =========================================================
    //   CHAT MITRA: Buka/Tutup Percakapan & Kirim Pesan Terintegrasi
    //   Customer = lawan bicara (incoming), Mitra = pengirim (outgoing)
    // =========================================================

    let currentChatRoom = null;
    let mitraChatPollInterval = null;
    let mitraLastMessagesJson = '';

    function openChatRoom(orderCode, custName = '', serviceName = '', initialPrice = '') {
        const cleanCode = (orderCode || '').replace('#', '').trim() || 'SB-SURUH-7592';
        currentChatRoom = cleanCode;
        mitraLastMessagesJson = ''; // Reset json cache agar render pesan terbaru seketika

        // Ambil data pesanan dari storage lokal
        let foundOrder = null;
        try {
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                foundOrder = list.find(o => (o.code || '').replace('#', '') === cleanCode);
            }
            if (!foundOrder) {
                const rawActive = localStorage.getItem('sb_active_order');
                if (rawActive) {
                    const parsed = JSON.parse(rawActive);
                    if ((parsed.code || '').replace('#', '') === cleanCode) foundOrder = parsed;
                }
            }
        } catch(e) {}

        const finalCustName = custName || (foundOrder && (foundOrder.customerName || foundOrder.customer)) || 'Pelanggan SayaBantu';
        const finalServiceName = serviceName || (foundOrder && foundOrder.layanan) || 'Suruhan Jasa';
        const finalPrice = initialPrice || (foundOrder && (foundOrder.nego_price || foundOrder.budget || foundOrder.biaya)) || '95.000';
        const initials = finalCustName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();

        // Update header detail chat
        const nameEl = document.getElementById('chatNameDetail');
        if (nameEl) nameEl.innerText = finalCustName;
        const taskEl = document.getElementById('chatTaskDetail');
        if (taskEl) taskEl.innerText = `Tugas #${cleanCode} • ${finalServiceName}`;
        const avatarEl = document.getElementById('chatAvatarDetail');
        if (avatarEl) {
            avatarEl.innerText = initials;
            avatarEl.className = 'w-10 h-10 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm';
        }

        // Update Kartu Negosiasi Header
        updateMitraNegotiationCard(cleanCode, finalServiceName, finalPrice, foundOrder?.status || 'nego');

        // Tampilkan view detail chat, sembunyikan list
        document.getElementById('chatListView')?.classList.add('hidden');
        document.getElementById('chatDetailView')?.classList.remove('hidden');

        // Muat pesan dari cache lokal
        const cached = localStorage.getItem('sb_chat_' + cleanCode);
        if (cached) {
            try {
                const parsedCached = JSON.parse(cached);
                if (Array.isArray(parsedCached) && parsedCached.length > 0) {
                    renderMitraChatBubbles(parsedCached, true);
                }
            } catch(e) {}
        }

        // Ambil pesan terbaru dari API backend
        loadAndRenderMitraChatMessages(cleanCode, true);

        // Pasang polling real-time sinkronisasi (setiap 1.5 detik)
        if (mitraChatPollInterval) clearInterval(mitraChatPollInterval);
        mitraChatPollInterval = setInterval(() => {
            if (currentChatRoom === cleanCode) {
                loadAndRenderMitraChatMessages(cleanCode, false);
            }
        }, 1500);

        // Fokuskan input
        const inp = document.getElementById('chatInputField');
        if (inp) setTimeout(() => inp.focus(), 250);
    }
    window.openChatRoom = openChatRoom;

    function closeChatRoom() {
        if (mitraChatPollInterval) {
            clearInterval(mitraChatPollInterval);
            mitraChatPollInterval = null;
        }
        currentChatRoom = null;
        document.getElementById('chatDetailView')?.classList.add('hidden');
        document.getElementById('chatListView')?.classList.remove('hidden');
        if (typeof loadMitraCustomerOrders === 'function') {
            loadMitraCustomerOrders();
        }
    }
    window.closeChatRoom = closeChatRoom;

    function updateMitraNegotiationCard(orderCode, serviceName, priceStr, status) {
        const negoCard = document.getElementById('mitraChatNegotiationCard');
        if (!negoCard) return;

        window.currentMitraChatStatus = status || 'nego';

        // Jika pesanan sudah lewat tahap kesepakatan harga dan masuk jadwal / pengerjaan
        const isCompletedOrOngoing = ['dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(status);
        if (isCompletedOrOngoing) {
            negoCard.classList.add('hidden');
            return;
        }

        negoCard.classList.remove('hidden');
        const dealTitle = document.getElementById('mitraChatDealTitle');
        if (dealTitle) dealTitle.textContent = serviceName;
        const dealCode = document.getElementById('mitraChatDealCode');
        if (dealCode) dealCode.textContent = '#' + orderCode;
        const dealPrice = document.getElementById('mitraChatDealPrice');
        if (dealPrice) dealPrice.textContent = 'Rp ' + priceStr;

        const isDealApproved = ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_admin'].includes(status);

        if (isDealApproved) {
            const dealBadge = document.getElementById('mitraChatDealStatusBadge');
            if (dealBadge) {
                dealBadge.textContent = 'Disetujui Mitra';
                dealBadge.className = 'text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
            }
            document.getElementById('mitraChatDealActionBox')?.classList.add('hidden');
            document.getElementById('mitraChatDealSuccessBox')?.classList.remove('hidden');
            const finalPriceEl = document.getElementById('mitraChatDealFinalPrice');
            if (finalPriceEl) finalPriceEl.textContent = priceStr;
        } else {
            const dealBadge = document.getElementById('mitraChatDealStatusBadge');
            if (dealBadge) {
                dealBadge.textContent = 'Sedang Nego';
                dealBadge.className = 'text-[8.5px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200';
            }
            document.getElementById('mitraChatDealActionBox')?.classList.remove('hidden');
            document.getElementById('mitraChatDealSuccessBox')?.classList.add('hidden');
        }
    }
    window.updateMitraNegotiationCard = updateMitraNegotiationCard;

    function loadAndRenderMitraChatMessages(orderCode, autoScroll = false) {
        if (!orderCode) return;
        fetch(`/api/chat/${orderCode}/messages?role=mitra`)
            .then(res => res.json())
            .then(data => {
                if (data && data.success && Array.isArray(data.messages)) {
                    const currentJson = JSON.stringify(data.messages);
                    if (currentJson !== mitraLastMessagesJson) {
                        mitraLastMessagesJson = currentJson;
                        localStorage.setItem('sb_chat_' + orderCode, currentJson);
                        renderMitraChatBubbles(data.messages, autoScroll);
                    }
                    if (data.order) {
                        const price = data.order.biaya || data.order.nominal || '95.000';
                        const status = data.order.status || 'nego';
                        updateMitraNegotiationCard(orderCode, data.order.layanan || 'Suruhan Jasa', price, status);
                        if (data.order.customer_name) {
                            const nameEl = document.getElementById('chatNameDetail');
                            if (nameEl) nameEl.innerText = data.order.customer_name;
                            const avatarEl = document.getElementById('chatAvatarDetail');
                            if (avatarEl) avatarEl.innerText = data.order.customer_name.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase();
                        }
                        if (data.order.layanan) {
                            const taskEl = document.getElementById('chatTaskDetail');
                            if (taskEl) taskEl.innerText = `Tugas #${orderCode} • ${data.order.layanan}`;
                        }
                    }
                }
            })
            .catch(() => {});
    }

    function renderMitraChatBubbles(messages, autoScroll = false) {
        const container = document.getElementById('chatBubblesContainer');
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
            const isMitra = (msg.sender === 'mitra');
            const isSystemOrAdmin = (msg.sender === 'system' || msg.sender === 'admin');
            const timeStr = msg.time || (msg.timestamp ? new Date(msg.timestamp * 1000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'Baru saja');
            const avatar = msg.sender_avatar || (isMitra ? 'SA' : 'TI');
            const cleanText = (msg.text || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            const nominal = msg.metadata?.nominal || '';
            const senderName = msg.sender_name || (isMitra ? 'Saya' : 'Customer');

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
                if (isMitra) {
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
                                    <span class="text-[8px] text-sky-200 block pt-1 border-t border-white/10">Menunggu respon Customer...</span>
                                </div>
                                <span class="text-[8px] text-slate-400 mt-0.5 block mr-1">${timeStr} <i class="fa-solid fa-check-double text-sky-400"></i></span>
                            </div>
                        </div>
                    `;
                } else {
                    const currentStatus = window.currentMitraChatStatus || '';
                    const isDealFinalized = ['siap_bayar', 'menunggu_pembayaran', 'menunggu_konfirmasi_admin', 'dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(currentStatus);

                    html += `
                        <div class="flex items-end gap-2 max-w-[85%]" style="animation: fadeInUp 0.25s ease-out;">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white text-[8px] font-bold flex items-center justify-center shrink-0">${avatar}</div>
                            <div>
                                <span class="text-[9px] font-bold text-sky-800 ml-1 mb-0.5 block">${senderName}</span>
                                <div class="p-3 rounded-2xl rounded-bl-md bg-white border-2 ${isDealFinalized ? 'border-slate-200' : 'border-amber-400'} shadow-sm space-y-2">
                                    <div class="flex items-center justify-between gap-2 border-b ${isDealFinalized ? 'border-slate-100' : 'border-amber-100'} pb-1">
                                        <span class="text-[9px] font-black text-[#173B67] flex items-center gap-1">
                                            <i class="fa-solid fa-handshake text-amber-600"></i> Tawaran dari Customer
                                        </span>
                                        <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded ${isDealFinalized ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-800'}">
                                            ${isDealFinalized ? 'Selesai' : 'Menunggu Anda'}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 block">Customer mengajukan nominal:</span>
                                        <strong class="text-base font-black ${isDealFinalized ? 'text-slate-700' : 'text-emerald-600'}">Rp ${nominal}</strong>
                                    </div>
                                    <p class="text-[11px] text-slate-700 leading-relaxed">${cleanText}</p>
                                    ${!isDealFinalized ? `
                                        <div class="pt-2 border-t border-slate-100 flex items-center gap-1.5">
                                            <button type="button" onclick="mitraSetujuiHargaDariChat('${nominal}')" class="flex-1 py-1.5 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] flex items-center justify-center gap-1 shadow-xs cursor-pointer active:scale-95">
                                                <i class="fa-solid fa-check"></i> Setujui Harga Rp ${nominal}
                                            </button>
                                            <button type="button" onclick="mitraBukaNegoDariChat()" class="py-1.5 px-2 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-[9.5px] cursor-pointer">
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
            // 3. KESEPAKATAN HARGA TELAH DISETUJUI
            } else if (msg.type === 'deal_accepted') {
                html += `
                    <div class="p-3.5 my-2 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-400 shadow-xs space-y-1.5 text-center" style="animation: fadeInUp 0.25s ease-out;">
                        <div class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-800">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> KESEPAKATAN HARGA TELAH DISETUJUI!
                        </div>
                        <p class="text-[11px] text-emerald-900 leading-snug">
                            Harga final disepakati sebesar <strong>Rp ${nominal || '85.000'}</strong>. Link pembayaran telah dibuka untuk Customer.
                        </p>
                        <span class="text-[8px] text-emerald-600 block">${timeStr}</span>
                    </div>
                `;
            // 4. PESAN REGULER DUA ARAH (MITRA <-> CUSTOMER)
            } else {
                if (isMitra) {
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

    function sendChatMessage(e) {
        e.preventDefault();
        const inp = document.getElementById('chatInputField');
        const text = inp ? inp.value.trim() : '';
        if (!text || !currentChatRoom) return;

        const code = currentChatRoom;
        inp.value = '';

        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { name: window.CURRENT_MITRA_DATA?.name || 'Salsanih' };
        const myName = currentMitra.name || window.CURRENT_MITRA_DATA?.name || 'Salsanih';
        const myAvatar = myName.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase() || 'SA';
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        const localMsg = {
            id: Date.now(),
            order_code: code,
            sender: 'mitra',
            sender_name: myName,
            sender_avatar: myAvatar,
            text: text,
            type: 'text',
            time: timeStr,
            timestamp: Math.floor(Date.now() / 1000),
            is_read: false
        };

        let list = [];
        try {
            const raw = localStorage.getItem('sb_chat_' + code);
            if (raw) list = JSON.parse(raw);
        } catch(err) {}
        list.push(localMsg);
        localStorage.setItem('sb_chat_' + code, JSON.stringify(list));
        renderMitraChatBubbles(list, true);

        fetch(`/api/chat/${code}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sender: 'mitra',
                sender_name: myName,
                sender_avatar: myAvatar,
                text: text,
                type: 'text'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                loadAndRenderMitraChatMessages(code, true);
            }
        })
        .catch(() => {});
    }
    window.sendChatMessage = sendChatMessage;

    // Sinkronisasi daftar percakapan aktif Mitra langsung dari API Backend
    async function syncMitraChatListFromApi() {
        const container = document.getElementById('mitraChatRoomListDynamic');
        const emptyState = document.getElementById('mitraChatEmptyState');
        if (!container) return;

        try {
            const res = await fetch('/api/chat/conversations?role=mitra');
            const data = await res.json();
            if (data && data.success && Array.isArray(data.conversations) && data.conversations.length > 0) {
                if (emptyState) emptyState.classList.add('hidden');
                container.innerHTML = data.conversations.map(conv => {
                    const initials = conv.partner_avatar || (conv.partner_name || 'C').substring(0, 2).toUpperCase();
                    const cleanCode = conv.order_code;
                    const dispCode = conv.display_code || ('#' + cleanCode);
                    const partnerName = conv.partner_name || 'Pelanggan SayaBantu';
                    const serviceName = conv.service || 'Suruhan Jasa';
                    const lastMsg = conv.last_message || 'Koordinasi tugas dengan customer.';
                    const lastTime = conv.last_time || 'Baru saja';
                    const unreadBadge = conv.unread > 0 
                        ? `<span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white font-extrabold text-[8.5px]">${conv.unread}</span>` 
                        : '';
                    const price = conv.biaya || '85.000';

                    return `
                        <div class="p-3 rounded-xl bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50/40 transition-all cursor-pointer active:scale-[0.98] shadow-2xs flex items-start gap-3"
                             onclick="openChatRoom('${cleanCode}', '${partnerName.replace(/'/g, "\\'")}', '${serviceName.replace(/'/g, "\\'")}', '${price}')">
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

        if (typeof loadMitraCustomerOrders === 'function') {
            loadMitraCustomerOrders();
        }
    }
    window.syncMitraChatListFromApi = syncMitraChatListFromApi;

    function mitraSetujuiHargaDariChat(nominalStr = '') {
        if (!currentChatRoom) return;
        const code = currentChatRoom;

        let nominal = nominalStr;
        if (!nominal) {
            const el = document.getElementById('mitraChatDealPrice');
            nominal = el ? el.innerText.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '95.000';
        }
        const cleanNominal = nominal.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Update status order di storage lokal
        try {
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(o => {
                    if ((o.code || '').replace('#', '') === code) {
                        o.budget = cleanNominal;
                        o.status = 'siap_bayar';
                        o.step = 7;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
            const rawActive = localStorage.getItem('sb_active_order');
            if (rawActive) {
                const act = JSON.parse(rawActive);
                if ((act.code || '').replace('#', '') === code) {
                    act.budget = cleanNominal;
                    act.status = 'siap_bayar';
                    act.step = 7;
                    localStorage.setItem('sb_active_order', JSON.stringify(act));
                }
            }
        } catch(err) {}

        updateMitraNegotiationCard(code, 'Suruhan Jasa', cleanNominal, 'siap_bayar');

        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { name: window.CURRENT_MITRA_DATA?.name || 'Kala' };
        const text = `Halo Kak, penawaran harga Rp ${cleanNominal} saya setujui ya! Jadwal dan pekerjaan sudah cocok. Silakan lanjut ke pembayaran agar siap dijadwalkan 👍`;

        fetch(`/api/chat/${code}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sender: 'mitra',
                sender_name: currentMitra.name || window.CURRENT_MITRA_DATA?.name || 'Kala',
                text: text,
                type: 'deal_accepted',
                metadata: { nominal: cleanNominal, status: 'siap_bayar' }
            })
        })
        .then(res => res.json())
        .then(() => {
            loadAndRenderMitraChatMessages(code, true);
        })
        .catch(() => {});

        showMitraToast('Harga Disetujui', `Tawaran harga Rp ${cleanNominal} berhasil disetujui!`, 'success');
    }
    window.mitraSetujuiHargaDariChat = mitraSetujuiHargaDariChat;

    function mitraBukaNegoDariChat() {
        if (!currentChatRoom) return;
        const code = currentChatRoom;
        const nominal = prompt('Masukkan nominal penawaran harga tawar balik Anda (misal: 95.000):');
        if (!nominal) return;
        const cleanNominal = nominal.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        if (!cleanNominal) return;

        try {
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(o => {
                    if ((o.code || '').replace('#', '') === code) {
                        o.budget = cleanNominal;
                        o.status = 'nego';
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch(err) {}

        updateMitraNegotiationCard(code, 'Suruhan Jasa', cleanNominal, 'nego');

        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { name: window.CURRENT_MITRA_DATA?.name || 'Kala' };
        const text = `Bagaimana jika harganya disepakati Rp ${cleanNominal}? Saya jamin pengerjaan rapi dan bergaransi kepuasan pelanggan.`;

        fetch(`/api/chat/${code}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sender: 'mitra',
                sender_name: currentMitra.name || window.CURRENT_MITRA_DATA?.name || 'Kala',
                text: text,
                type: 'nego',
                metadata: { nominal: cleanNominal }
            })
        })
        .then(res => res.json())
        .then(() => {
            loadAndRenderMitraChatMessages(code, true);
        })
        .catch(() => {});

        showMitraToast('Tawar Balik Terkirim', `Tawaran balik Rp ${cleanNominal} diajukan ke Customer.`, 'info');
    }
    window.mitraBukaNegoDariChat = mitraBukaNegoDariChat;

    // Cross-tab real-time storage sync listener untuk portal Mitra
    window.addEventListener('storage', function(e) {
        if (!e.key) return;
        if (currentChatRoom && e.key === 'sb_chat_' + currentChatRoom) {
            try {
                const msgs = JSON.parse(e.newValue || '[]');
                if (Array.isArray(msgs)) renderMitraChatBubbles(msgs, true);
            } catch(err) {}
        }
        if (e.key === 'sb_customer_orders' || e.key === 'sb_active_order') {
            loadMitraCustomerOrders(false);
            if (currentChatRoom) {
                loadAndRenderMitraChatMessages(currentChatRoom, false);
            }
        }
    });

    // =========================================================================
    //  INTEGRASI REAL-TIME: PENGAJUAN SURUHAN CUSTOMER KE PORTAL MITRA
    // =========================================================================

    @if(isset($serverOrders) && count($serverOrders) > 0)
    try {
        const initialServerOrders = @json($serverOrders);
        if (Array.isArray(initialServerOrders) && initialServerOrders.length > 0) {
            let localList = [];
            try {
                const r = localStorage.getItem('sb_customer_orders');
                if (r) localList = JSON.parse(r);
            } catch(e) {}
            initialServerOrders.forEach(srv => {
                const cleanCode = (srv.code || srv.id || '').replace('#', '');
                const mapped = {
                    id: srv.code || ('#SB-SURUH-' + srv.id),
                    code: srv.code || ('#SB-SURUH-' + srv.id),
                    layanan: srv.layanan || 'Suruhan',
                    customerName: srv.customer_name || srv.customerName || 'Customer',
                    customerPhone: srv.customer_phone || srv.customerPhone || '-',
                    budget: srv.agreed_price || srv.biaya || '50.000',
                    biaya: srv.biaya || srv.agreed_price || '50.000',
                    nominal: srv.nominal || 50000,
                    escrow_status: srv.escrow_status || null,
                    mitra_fee: srv.mitra_fee || 0,
                    escrow_released_at: srv.escrow_released_at || null,
                    agreed_price: srv.agreed_price || null,
                    agreed_by: srv.agreed_by || null,
                    nego_by: srv.nego_by || null,
                    status: srv.status,
                    step: srv.step,
                    selectedMitraId: srv.mitra_id || null,
                    selectedMitraName: srv.mitra_name || null,
                    mitraName: srv.mitra_name || null,
                    mitraId: srv.mitra_id || null,
                    alamat: srv.alamat || '',
                    jadwal: srv.jadwal || '',
                    note: srv.note || '',
                    metode: srv.metode || 'Nego',
                    gender: srv.gender || 'Bebas',
                };
                const idx = localList.findIndex(o => (o.code || o.id || '').replace('#', '') === cleanCode);
                if (idx >= 0) {
                    localList[idx] = { ...localList[idx], ...mapped };
                } else {
                    localList.unshift(mapped);
                }
            });
            try { localStorage.setItem('sb_customer_orders', JSON.stringify(localList)); } catch(e) {}
        }
    } catch(e) {}
    @endif

    function loadMitraCustomerOrders(skipFetch = false) {
        if (!skipFetch) {
            fetch('/api/orders/kebutuhan')
                .then(r => r.json())
                .then(data => {
                    if (data && data.success && Array.isArray(data.orders)) {
                        let localOrders = [];
                        try {
                            const raw = localStorage.getItem('sb_customer_orders');
                            if (raw) localOrders = JSON.parse(raw);
                        } catch(e) {}

                        const localMap = {};
                        if (Array.isArray(localOrders)) {
                            localOrders.forEach(o => {
                                const k = (o.code || o.id || '').replace('#', '');
                                if (k) localMap[k] = o;
                            });
                        }

                        // Data dari database server adalah sumber rincian suruhan yang valid & terintegrasi real
                        const syncedOrders = data.orders.map(dbOrd => {
                            const cleanCode = (dbOrd.code || dbOrd.id || '').replace('#', '');
                            const existing = localMap[cleanCode] || {};
                            return {
                                ...existing,
                                ...dbOrd,
                                id: dbOrd.code || dbOrd.id,
                                code: dbOrd.code,
                                layanan: dbOrd.layanan,
                                customerName: dbOrd.customerName || dbOrd.customer_name || existing.customerName || 'Customer',
                                customerPhone: dbOrd.customerPhone || dbOrd.customer_phone || existing.customerPhone || '-',
                                budget: dbOrd.budget || dbOrd.biaya,
                                biaya: dbOrd.biaya || dbOrd.budget,
                                nominal: dbOrd.nominal || existing.nominal || 50000,
                                alamat: dbOrd.alamat || existing.alamat || '',
                                jadwal: dbOrd.jadwal || existing.jadwal || '',
                                note: dbOrd.note || existing.note || '',
                                metode: dbOrd.metode || existing.metode || 'Nego',
                                gender: dbOrd.gender || existing.gender || 'Bebas',
                                status: dbOrd.status,
                                step: dbOrd.step,
                                selectedMitraId: dbOrd.selectedMitraId || dbOrd.mitraId || dbOrd.mitra_id || existing.selectedMitraId || null,
                                selectedMitraName: dbOrd.selectedMitraName || dbOrd.mitraName || dbOrd.mitra_name || existing.selectedMitraName || null,
                                mitraName: dbOrd.mitraName || dbOrd.mitra_name || existing.mitraName || null,
                                mitraId: dbOrd.mitraId || dbOrd.mitra_id || existing.mitraId || null,
                                agreed_price: dbOrd.agreed_price || existing.agreed_price || null,
                                agreed_by: dbOrd.agreed_by || existing.agreed_by || null,
                                nego_by: dbOrd.nego_by || existing.nego_by || null,
                                escrow_status: dbOrd.escrow_status || null,
                                mitra_fee: dbOrd.mitra_fee || 0,
                                escrow_released_at: dbOrd.escrow_released_at || null,
                                review_rating: dbOrd.review_rating || existing.review_rating || null,
                                review_comment: dbOrd.review_comment || existing.review_comment || null,
                                reviewed_at: dbOrd.reviewed_at || existing.reviewed_at || null,
                                rejectedMitras: existing.rejectedMitras || []
                            };
                        });

                        try {
                            localStorage.setItem('sb_customer_orders', JSON.stringify(syncedOrders));
                        } catch(e) {}
                        loadMitraCustomerOrders(true);
                    }
                })
                .catch(() => {});
        }

        let orders = [];
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) orders = JSON.parse(raw);
        } catch(e) {}

        const containerKeb = document.getElementById('mitraDynamicCustomerOrdersList');
        const containerDash = document.getElementById('mitraDashboardDynamicOrdersList');
        const containerPenawaran = document.getElementById('mitraDynamicPekPenawaranList');
        const containerDikonfirmasi = document.getElementById('mitraDynamicPekDikonfirmasiList');
        const containerBerlangsung = document.getElementById('mitraDynamicPekBerlangsungList');

        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: 'SB-7615', name: 'Kala' };
        const myMitraId = currentMitra.id;
        const myName = currentMitra.name || 'Kala';

        // Suruhan masuk: tampilkan hanya jika belum ditolak oleh mitra ini dan belum diambil mitra lain
        const pendingOrders = orders.filter(o => {
            const isPendingStatus = (o.status === 'menunggu_penawaran' || o.status === 'menunggu_mitra' || o.status === 'nego');
            const rejectedByMe = (o.rejectedMitras && Array.isArray(o.rejectedMitras) && (o.rejectedMitras.includes(myMitraId) || o.rejectedMitras.includes('MTR-001')));
            const notAssignedToOther = (!o.selectedMitraId || o.selectedMitraId === myMitraId || o.selectedMitraId === 'MTR-001');
            return isPendingStatus && !rejectedByMe && notAssignedToOther;
        });

        // Penawaran: tampilkan jika dalam tahap negosiasi/pemilihan customer atau menunggu konfirmasi admin atau menunggu pembayaran
        const penawaranOrders = orders.filter(o => {
            const isMyNego = (o.status === 'nego' && (o.mitra_name === myName || o.mitraName === myName || o.mitra_id === myMitraId || o.mitraId === myMitraId || o.nego_by === 'customer'));
            return o.status === 'nego_diajukan' || o.status === 'menunggu_keputusan' || o.status === 'menunggu_konfirmasi_admin' || o.status === 'menunggu_pembayaran' || o.status === 'menunggu_konfirmasi_pembayaran' || isMyNego;
        });

        // Dikonfirmasi: pekerjaan terjadwal/lunas yang resmi ditetapkan untuk Mitra ini
        const dikonfirmasiOrders = orders.filter(o => {
            const isAssignedToMe = (
                !o.selectedMitraId ||
                o.selectedMitraId === myMitraId ||
                o.mitraId === myMitraId ||
                o.mitra_id === myMitraId ||
                o.selectedMitraId === 'MTR-001' ||
                (o.mitra_name && (o.mitra_name === myName || o.mitra_name.toLowerCase() === myName.toLowerCase())) ||
                (o.mitraName && (o.mitraName === myName || o.mitraName.toLowerCase() === myName.toLowerCase())) ||
                !o.mitra_name
            );
            return (o.status === 'terjadwal' || o.status === 'dibayar' || o.status === 'lunas') && isAssignedToMe;
        });

        // Berlangsung: pekerjaan aktif serta pekerjaan yang sedang menunggu konfirmasi Customer / Admin agar tidak hilang dari Mitra
        const berlangsungOrders = orders.filter(o => {
            const isAssignedToMe = (
                !o.selectedMitraId ||
                o.selectedMitraId === myMitraId ||
                o.mitraId === myMitraId ||
                o.mitra_id === myMitraId ||
                o.selectedMitraId === 'MTR-001' ||
                (o.mitra_name && (o.mitra_name === myName || o.mitra_name.toLowerCase() === myName.toLowerCase())) ||
                (o.mitraName && (o.mitraName === myName || o.mitraName.toLowerCase() === myName.toLowerCase())) ||
                !o.mitra_name
            );
            const isBerlangsungStatus = (
                o.status === 'otw' ||
                o.status === 'berjalan' ||
                o.status === 'menunggu_konfirmasi_customer' ||
                o.status === 'menunggu_konfirmasi_admin_selesai'
            );
            return isBerlangsungStatus && isAssignedToMe;
        });

        const totalPending = pendingOrders.length;
        const badgeTab = document.getElementById('mitraTabBadge');
        if (badgeTab) {
            badgeTab.innerText = totalPending + ' Permintaan Baru';
        }
        const labelJumlahKebutuhan = document.getElementById('labelJumlahKebutuhan');
        if (labelJumlahKebutuhan) {
            labelJumlahKebutuhan.innerText = totalPending + ' Kebutuhan Tersedia';
        }

        // Tab Kebutuhan
        const emptyKeb = document.getElementById('mitraEmptyKebutuhanList');
        if (containerKeb) {
            if (pendingOrders.length === 0) {
                containerKeb.innerHTML = '';
                if (emptyKeb) emptyKeb.classList.remove('hidden');
            } else {
                if (emptyKeb) emptyKeb.classList.add('hidden');
                containerKeb.innerHTML = pendingOrders.map(o => createMitraOrderCardHtml(o, 'kebutuhan')).join('');
            }
        }

        // Tab Beranda - Kebutuhan Spesialisasi
        const emptyDashKeb = document.getElementById('mitraBerandaEmptyKebutuhan');
        if (containerDash) {
            if (pendingOrders.length === 0) {
                containerDash.innerHTML = '';
                if (emptyDashKeb) emptyDashKeb.classList.remove('hidden');
            } else {
                if (emptyDashKeb) emptyDashKeb.classList.add('hidden');
                containerDash.innerHTML = pendingOrders.slice(0, 2).map(o => createMitraOrderCardHtml(o, 'dashboard')).join('');
            }
        }

        // Tab Beranda - Ongoing Job
        const emptyOngoing = document.getElementById('mitraBerandaEmptyOngoing');
        const cardOngoing = document.getElementById('mitraBerandaOngoingCard');
        if (berlangsungOrders.length > 0) {
            if (emptyOngoing) emptyOngoing.classList.add('hidden');
            if (cardOngoing) {
                cardOngoing.classList.remove('hidden');
                cardOngoing.innerHTML = createMitraPekCardHtml(berlangsungOrders[0], 'berlangsung');
            }
        } else if (dikonfirmasiOrders.length > 0) {
            if (emptyOngoing) emptyOngoing.classList.add('hidden');
            if (cardOngoing) {
                cardOngoing.classList.remove('hidden');
                cardOngoing.innerHTML = createMitraPekCardHtml(dikonfirmasiOrders[0], 'dikonfirmasi');
            }
        } else {
            if (emptyOngoing) emptyOngoing.classList.remove('hidden');
            if (cardOngoing) cardOngoing.classList.add('hidden');
        }

        // Tab Beranda - Offers
        const emptyOffers = document.getElementById('mitraBerandaEmptyOffers');
        const listOffers = document.getElementById('mitraBerandaOffersList');
        const badgeOffers = document.getElementById('mitraBadgePenawaranCount');
        if (penawaranOrders.length > 0) {
            if (emptyOffers) emptyOffers.classList.add('hidden');
            if (listOffers) {
                listOffers.innerHTML = penawaranOrders.slice(0, 2).map(o => createMitraPekCardHtml(o, 'penawaran')).join('');
            }
            if (badgeOffers) badgeOffers.innerText = penawaranOrders.length + ' Menunggu';
        } else {
            if (emptyOffers) emptyOffers.classList.remove('hidden');
            if (listOffers) listOffers.innerHTML = '';
            if (badgeOffers) badgeOffers.innerText = '0 Menunggu';
        }

        // Tab Pekerjaan Filter Buttons:
        const btnPenawaran = document.querySelectorAll('.btn-status-pek')[0];
        const btnDikonfirmasi = document.querySelectorAll('.btn-status-pek')[1];
        const btnBerlangsung = document.querySelectorAll('.btn-status-pek')[2];
        const btnSelesai = document.querySelectorAll('.btn-status-pek')[3];
        const btnDibatalkan = document.querySelectorAll('.btn-status-pek')[4];

        const isAssignedToMePek = (o) => (
            !o.selectedMitraId ||
            o.selectedMitraId === myMitraId ||
            o.mitraId === myMitraId ||
            o.mitra_id === myMitraId ||
            o.selectedMitraId === 'MTR-001' ||
            (o.mitra_name && (o.mitra_name === myName || o.mitra_name.toLowerCase() === myName.toLowerCase())) ||
            (o.mitraName && (o.mitraName === myName || o.mitraName.toLowerCase() === myName.toLowerCase())) ||
            !o.mitra_name
        );

        const selesaiOrders = orders.filter(o => o.status === 'selesai' && isAssignedToMePek(o));
        const dibatalkanOrders = orders.filter(o => (o.status === 'dibatalkan' || o.status === 'semua_ditolak' || o.status === 'cancelled_by_admin' || o.status === 'cancelled_by_customer') && isAssignedToMePek(o));

        const releasedOrders = orders.filter(o => {
            if (!['released_to_mitra', 'released'].includes(o.escrow_status)) return false;

            const matchesMitraId = [o.selectedMitraId, o.mitraId, o.mitra_id]
                .filter(Boolean)
                .some(id => String(id) === String(myMitraId));
            const matchesMitraName = [o.selectedMitraName, o.mitraName, o.mitra_name]
                .filter(Boolean)
                .some(name => String(name).trim().toLowerCase() === myName.trim().toLowerCase());

            return matchesMitraId || matchesMitraName;
        });
        const getReleasedMitraFee = order => {
            const mitraFee = parseInt(String(order.mitra_fee || '0').replace(/[^0-9]/g, ''), 10) || 0;
            if (mitraFee > 0) return mitraFee;

            const nominal = parseInt(String(order.nominal || order.agreed_price || order.budget || order.biaya || '0').replace(/[^0-9]/g, ''), 10) || 0;
            return Math.round(nominal * 0.9);
        };
        const totalIncome = releasedOrders.reduce((total, order) => total + getReleasedMitraFee(order), 0);
        const currentDate = new Date();
        const totalIncomeToday = releasedOrders.reduce((total, order) => {
            const releasedAt = order.escrow_released_at ? new Date(order.escrow_released_at) : null;
            const isReleasedToday = releasedAt
                && releasedAt.getFullYear() === currentDate.getFullYear()
                && releasedAt.getMonth() === currentDate.getMonth()
                && releasedAt.getDate() === currentDate.getDate();

            return total + (isReleasedToday ? getReleasedMitraFee(order) : 0);
        }, 0);
        const totalIncomeThisMonth = releasedOrders.reduce((total, order) => {
            const releasedAt = order.escrow_released_at ? new Date(order.escrow_released_at) : null;
            const isReleasedThisMonth = releasedAt
                && releasedAt.getFullYear() === currentDate.getFullYear()
                && releasedAt.getMonth() === currentDate.getMonth();

            return total + (isReleasedThisMonth ? getReleasedMitraFee(order) : 0);
        }, 0);
        const elHeroSaldo = document.getElementById('mitraHeroSaldoText');
        const elHeroSub = document.getElementById('mitraHeroSaldoSub');
        const elSaldoTersedia = document.getElementById('mitraSaldoTersediaText');
        const elSaldoHariIni = document.getElementById('mitraSaldoHariIniText');
        const elSaldoBulanIni = document.getElementById('mitraSaldoBulanIniText');
        if (elHeroSaldo) {
            elHeroSaldo.textContent = 'Rp ' + totalIncome.toLocaleString('id-ID');
        }
        if (elSaldoTersedia) {
            elSaldoTersedia.textContent = 'Rp ' + totalIncome.toLocaleString('id-ID');
        }
        if (elSaldoHariIni) {
            elSaldoHariIni.textContent = '+Rp ' + totalIncomeToday.toLocaleString('id-ID');
        }
        if (elSaldoBulanIni) {
            elSaldoBulanIni.textContent = 'Rp ' + totalIncomeThisMonth.toLocaleString('id-ID');
        }
        if (elHeroSub) {
            elHeroSub.innerHTML = `<i class="fa-solid fa-arrow-trend-up text-emerald-400 mr-0.5"></i> Rp ${totalIncome.toLocaleString('id-ID')} dari dana yang telah diteruskan`;
        }

        if (btnPenawaran) btnPenawaran.textContent = `Penawaran (${penawaranOrders.length})`;
        if (btnDikonfirmasi) btnDikonfirmasi.textContent = `Dikonfirmasi (${dikonfirmasiOrders.length})`;
        if (btnBerlangsung) btnBerlangsung.textContent = `Berlangsung (${berlangsungOrders.length})`;
        if (btnSelesai) btnSelesai.textContent = `Selesai (${selesaiOrders.length})`;
        if (btnDibatalkan) btnDibatalkan.textContent = `Dibatalkan (${dibatalkanOrders.length})`;

        // Update Ringkasan Statistik Pekerjaan Selesai
        const reviewedOrders = selesaiOrders.filter(o => o.review_rating !== null && o.review_rating !== undefined && Number(o.review_rating) > 0);
        const totalTuntasCount = selesaiOrders.length;
        const totalReviewedCount = reviewedOrders.length;
        let avgRatingVal = 0;
        if (totalReviewedCount > 0) {
            avgRatingVal = (reviewedOrders.reduce((sum, o) => sum + parseFloat(o.review_rating), 0) / totalReviewedCount).toFixed(1);
        } else if (window.CURRENT_MITRA_DATA && window.CURRENT_MITRA_DATA.rating) {
            avgRatingVal = Number(window.CURRENT_MITRA_DATA.rating).toFixed(1);
        }

        const totalAssignedOrders = penawaranOrders.length + dikonfirmasiOrders.length + berlangsungOrders.length + selesaiOrders.length;
        const pctPenyelesaian = totalAssignedOrders > 0
            ? Math.round((totalTuntasCount / totalAssignedOrders) * 100)
            : (totalTuntasCount > 0 ? 100 : 0);

        const statTuntasEl = document.getElementById('statRingkasanTotalTuntas');
        const statRatingEl = document.getElementById('statRingkasanRating');
        const statUlasanCountEl = document.getElementById('statRingkasanUlasanCount');
        const statPenyelesaianEl = document.getElementById('statRingkasanPenyelesaian');

        if (statTuntasEl) {
            statTuntasEl.textContent = totalTuntasCount;
        }
        if (statRatingEl) {
            if (avgRatingVal > 0) {
                statRatingEl.innerHTML = `${avgRatingVal} <span class="text-yellow-400">★</span>`;
            } else {
                statRatingEl.innerHTML = `- <span class="text-yellow-400">★</span>`;
            }
        }
        if (statUlasanCountEl) {
            statUlasanCountEl.textContent = `${totalReviewedCount} Ulasan`;
        }
        if (statPenyelesaianEl) {
            statPenyelesaianEl.textContent = `${pctPenyelesaian}%`;
        }

        // Update Ringkasan Transaksi di Tab Dashboard
        const dashTrxContainer = document.getElementById('mitraDashboardTransaksiList');
        if (dashTrxContainer) {
            if (selesaiOrders.length === 0) {
                dashTrxContainer.innerHTML = `
                    <div id="mitraDashboardEmptyTransaksi" class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                        <i class="fa-solid fa-receipt text-slate-300 text-sm"></i>
                        <p class="text-[10px] font-bold text-slate-600">Belum Ada Transaksi</p>
                        <p class="text-[9px] text-slate-400">Ringkasan transaksi pekerjaan selesai akan muncul di sini.</p>
                    </div>
                `;
            } else {
                dashTrxContainer.innerHTML = selesaiOrders.map(o => {
                    const nom = o.mitra_fee || Math.round((parseInt(String(o.nominal || o.budget || o.biaya || '20000').replace(/[^0-9]/g, ''), 10) || 20000) * 0.9);
                    const isReleased = ['released_to_mitra', 'released'].includes(o.escrow_status);
                    const code = o.code || o.id || '#SB';
                    const cust = o.customerName || o.customer_name || 'Customer';
                    const srv = o.layanan || 'Suruhan';
                    const time = o.jadwal || (o.reviewed_at ? new Date(o.reviewed_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : 'Selesai');
                    return `
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-sky-300 transition-all flex items-start justify-between gap-2.5">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-[#173B67]">${code}</span>
                                    <span class="text-[10px] font-bold text-slate-800 truncate">${srv}</span>
                                </div>
                                <p class="text-[9px] text-slate-500">Customer: <strong class="text-slate-700">${cust}</strong> • ${time}</p>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    ${isReleased ? `
                                        <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            <i class="fa-solid fa-check-double text-[8px]"></i> Dana Diteruskan
                                        </span>
                                    ` : `
                                        <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200">
                                            <i class="fa-solid fa-clock text-[8px]"></i> Selesai (Dana di Escrow)
                                        </span>
                                    `}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[8.5px] text-slate-400 block">Pendapatan</span>
                                <strong class="text-xs font-black text-emerald-600 block">Rp ${nom.toLocaleString('id-ID')}</strong>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        // Update Ulasan dari Customer di Tab Dashboard
        const dashUlasanContainer = document.getElementById('mitraDashboardUlasanList');
        if (dashUlasanContainer) {
            if (reviewedOrders.length === 0) {
                dashUlasanContainer.innerHTML = `
                    <div id="mitraDashboardEmptyUlasan" class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center space-y-1">
                        <i class="fa-solid fa-star text-slate-300 text-sm"></i>
                        <p class="text-[10px] font-bold text-slate-600">Belum Ada Ulasan</p>
                        <p class="text-[9px] text-slate-400">Ulasan dan rating dari customer akan muncul di sini setelah pekerjaan selesai.</p>
                    </div>
                `;
            } else {
                dashUlasanContainer.innerHTML = reviewedOrders.map(rev => {
                    const cust = rev.customerName || rev.customer_name || 'Customer';
                    const initial = cust.charAt(0).toUpperCase();
                    const rating = parseInt(rev.review_rating, 10) || 5;
                    const stars = '★'.repeat(rating) + '☆'.repeat(Math.max(0, 5 - rating));
                    const comment = rev.review_comment ? rev.review_comment.trim() : '';
                    const srv = rev.layanan || 'Suruhan';
                    const dateStr = rev.reviewed_at ? new Date(rev.reviewed_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '';
                    return `
                        <div class="p-3 rounded-xl bg-amber-50/40 border border-amber-200/70 hover:border-amber-400 transition-all space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-[#173B67] text-white text-[9px] font-bold flex items-center justify-center">
                                        ${initial}
                                    </div>
                                    <strong class="text-[10px] font-bold text-slate-800">${cust}</strong>
                                </div>
                                <span class="text-amber-500 text-[10px] font-bold flex items-center gap-0.5">
                                    <span>${stars}</span>
                                    <span class="ml-1 text-[9px] text-slate-600 font-semibold">${rating.toFixed(1)}</span>
                                </span>
                            </div>
                            ${comment ? `
                                <p class="text-[10px] text-slate-700 italic bg-white/70 p-2 rounded-lg border border-amber-100/60 leading-relaxed">
                                    "${comment}"
                                </p>
                            ` : ''}
                            <div class="flex items-center justify-between text-[8.5px] text-slate-400">
                                <span>${srv}</span>
                                <span>${dateStr}</span>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        // Update Halaman Lengkap Ulasan Saya
        const fullUlasanList = document.getElementById('mitraFullUlasanList');
        if (fullUlasanList && reviewedOrders.length > 0) {
            fullUlasanList.innerHTML = reviewedOrders.map(rev => {
                const cust = rev.customerName || rev.customer_name || 'Customer';
                const rating = parseInt(rev.review_rating, 10) || 5;
                const stars = '★'.repeat(rating) + '☆'.repeat(Math.max(0, 5 - rating));
                const comment = rev.review_comment ? rev.review_comment.trim() : '';
                const srv = rev.layanan || 'Suruhan';
                const dateStr = rev.reviewed_at ? new Date(rev.reviewed_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '';
                return `
                    <article class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <strong class="text-[10px] font-bold text-slate-900">${cust}</strong>
                            <span class="text-amber-500 text-[10px]">${stars}</span>
                        </div>
                        ${comment ? `<p class="text-[10px] text-slate-700">${comment}</p>` : ''}
                        <span class="text-[9px] text-slate-500">${srv}${dateStr ? ' • ' + dateStr : ''}</span>
                    </article>
                `;
            }).join('');
        }

        // Update Riwayat Transaksi di Tab Dompet
        const dompetTrxList = document.getElementById('mitraTransactionList');
        if (dompetTrxList && selesaiOrders.length > 0) {
            dompetTrxList.innerHTML = selesaiOrders.map(o => {
                const nom = o.mitra_fee || Math.round((parseInt(String(o.nominal || o.budget || o.biaya || '20000').replace(/[^0-9]/g, ''), 10) || 20000) * 0.9);
                const isReleased = ['released_to_mitra', 'released'].includes(o.escrow_status);
                const code = o.code || o.id || '#SB';
                const cust = o.customerName || o.customer_name || 'Customer';
                const srv = o.layanan || 'Suruhan';
                const time = o.jadwal || (o.reviewed_at ? new Date(o.reviewed_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : 'Selesai');
                return `
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start justify-between gap-2.5">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-[#173B67]">${code}</span>
                                <span class="text-[10px] font-bold text-slate-800 truncate">${srv}</span>
                            </div>
                            <p class="text-[9px] text-slate-500">Customer: <strong class="text-slate-700">${cust}</strong> • ${time}</p>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                ${isReleased ? `
                                    <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                        <i class="fa-solid fa-check-double text-[8px]"></i> Dana Diteruskan
                                    </span>
                                ` : `
                                    <span class="inline-flex items-center gap-1 text-[8.5px] font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200">
                                        <i class="fa-solid fa-clock text-[8px]"></i> Selesai (Dana di Escrow)
                                    </span>
                                `}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[8.5px] text-slate-400 block">Pendapatan</span>
                            <strong class="text-xs font-black text-emerald-600 block">Rp ${nom.toLocaleString('id-ID')}</strong>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Toggle empty states in Tab Pekerjaan
        const emptyPen = document.getElementById('emptySectionPenawaran');
        if (containerPenawaran) {
            if (penawaranOrders.length === 0) {
                containerPenawaran.innerHTML = '';
                if (emptyPen) emptyPen.classList.remove('hidden');
            } else {
                if (emptyPen) emptyPen.classList.add('hidden');
                containerPenawaran.innerHTML = penawaranOrders.map(o => createMitraPekCardHtml(o, 'penawaran')).join('');
            }
        }

        const emptyDiko = document.getElementById('emptySectionDikonfirmasi');
        if (containerDikonfirmasi) {
            if (dikonfirmasiOrders.length === 0) {
                containerDikonfirmasi.innerHTML = '';
                if (emptyDiko) emptyDiko.classList.remove('hidden');
            } else {
                if (emptyDiko) emptyDiko.classList.add('hidden');
                containerDikonfirmasi.innerHTML = dikonfirmasiOrders.map(o => createMitraPekCardHtml(o, 'dikonfirmasi')).join('');
            }
        }

        // Otomatis arahkan tab aktif ke Dikonfirmasi jika Penawaran telah lunas / resmi terjadwal
        const secPenawaran = document.getElementById('sectionPekPenawaran');
        if (secPenawaran && !secPenawaran.classList.contains('hidden') && penawaranOrders.length === 0 && dikonfirmasiOrders.length > 0) {
            if (typeof filterStatusPekerjaan === 'function' && btnDikonfirmasi) {
                filterStatusPekerjaan('dikonfirmasi', btnDikonfirmasi);
            }
        }

        const emptyBer = document.getElementById('emptySectionBerlangsung');
        if (containerBerlangsung) {
            if (berlangsungOrders.length === 0) {
                containerBerlangsung.innerHTML = '';
                if (emptyBer) emptyBer.classList.remove('hidden');
            } else {
                if (emptyBer) emptyBer.classList.add('hidden');
                containerBerlangsung.innerHTML = berlangsungOrders.map(o => createMitraPekCardHtml(o, 'berlangsung')).join('');
            }
        }

        const emptySel = document.getElementById('emptySectionSelesai');
        const containerSelesai = document.getElementById('mitraDynamicPekSelesaiList');
        if (containerSelesai) {
            if (selesaiOrders.length === 0) {
                containerSelesai.innerHTML = '';
                if (emptySel) emptySel.classList.remove('hidden');
            } else {
                if (emptySel) emptySel.classList.add('hidden');
                containerSelesai.innerHTML = selesaiOrders.map(o => createMitraPekCardHtml(o, 'selesai')).join('');
            }
        }

        const emptyDib = document.getElementById('emptySectionDibatalkan');
        const containerDibatalkan = document.getElementById('mitraDynamicPekDibatalkanList');
        if (containerDibatalkan) {
            if (dibatalkanOrders.length === 0) {
                containerDibatalkan.innerHTML = '';
                if (emptyDib) emptyDib.classList.remove('hidden');
            } else {
                if (emptyDib) emptyDib.classList.add('hidden');
                containerDibatalkan.innerHTML = dibatalkanOrders.map(o => createMitraPekCardHtml(o, 'dibatalkan')).join('');
            }
        }

        // Tab Obrolan
        renderMitraChatList(orders);
    }

    function renderMitraChatList(orders = []) {
        const container = document.getElementById('mitraChatRoomListDynamic');
        const emptyState = document.getElementById('mitraChatEmptyState');
        if (!container) return;

        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: 'SB-7615' };
        const myMitraId = currentMitra.id;
        const myName = (currentMitra.name || window.CURRENT_MITRA_DATA?.name || 'Kala').toLowerCase();
        const chatOrders = (orders || []).filter(o => {
            const isAssigned = (!o.selectedMitraId || o.selectedMitraId === myMitraId || o.selectedMitraId === 'MTR-001' || (o.mitraName && o.mitraName.toLowerCase().includes(myName)) || (o.mitra && o.mitra.toLowerCase().includes(myName)));
            return isAssigned && ['nego', 'menunggu_keputusan', 'menunggu_pembayaran', 'siap_bayar', 'menunggu_konfirmasi_admin', 'dikonfirmasi', 'terjadwal', 'otw', 'berjalan', 'sedang_dikerjakan', 'selesai'].includes(o.status || '');
        });

        if (chatOrders.length === 0) {
            container.innerHTML = '';
            if (emptyState) emptyState.classList.remove('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        container.innerHTML = chatOrders.map((ord, idx) => {
            const custName = ord.customerName || 'Customer SayaBantu';
            const initials = custName.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            const serviceName = ord.layanan || 'Suruhan Jasa';
            const code = ord.code || '#SB';
            const cleanCode = (code || '').replace('#', '').trim();
            const price = ord.budget || ord.biaya || ord.nominal || '95.000';
            return `
                <div class="p-3 rounded-xl bg-sky-50/60 border border-sky-200 flex items-start gap-3 hover:border-sky-400 transition-all cursor-pointer active:scale-[0.98]" onclick="openChatRoom('${cleanCode}', '${custName.replace(/'/g, "\\'")}', '${serviceName.replace(/'/g, "\\'")}', '${price}')">
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">${initials}</div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <strong class="text-xs font-extrabold text-slate-900 truncate">${custName}</strong>
                            <span class="text-[9px] text-sky-600 font-bold">Aktif</span>
                        </div>
                        <span class="text-[8.5px] font-semibold text-sky-700 block">${code} • ${serviceName}</span>
                        <p class="text-[10px] text-slate-700 truncate mt-0.5 font-semibold">Koordinasi pelaksanaan tugas &amp; negosiasi dengan customer.</p>
                    </div>
                </div>
            `;
        }).join('');
    }

    function createMitraOrderCardHtml(order, viewType) {
        const isHargaPas = (order.metode === 'Pas');
        const budgetFormatted = order.budget ? ('Rp ' + order.budget) : 'Rp 85.000';
        const kode = order.code || '#SB-SURUH-BARU';
        const namaCust = order.customerName || 'Customer SayaBantu';
        const phone = (order.customerPhone || '6281234567890').replace(/[^0-9]/g, '');
        const myName = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala'));

        if (viewType === 'dashboard') {
            return `
                <div class="p-3 rounded-xl bg-gradient-to-r from-sky-50 to-indigo-50/50 border-2 border-sky-300 shadow-xs space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full bg-blue-600 text-white animate-pulse">
                                Baru dari Customer
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                            <p class="text-[9px] text-slate-500 mt-0.5">Pemesan: <strong class="text-slate-800">${namaCust}</strong> • ${order.alamat || 'Jakarta'}</p>
                            ${order.note ? `<p class="text-[8.5px] text-slate-600 italic mt-0.5 line-clamp-1"><i class="fa-solid fa-note-sticky text-amber-500 mr-1"></i>"${order.note}"</p>` : ''}
                            ${(order.gender && order.gender !== 'Bebas') ? `<span class="inline-block mt-0.5 text-[8px] font-bold px-1.5 py-0.2 rounded bg-purple-100 text-purple-800"><i class="fa-solid fa-venus-mars mr-0.5"></i>Mitra: ${order.gender}</span>` : ''}
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[8px] text-slate-400 block">${isHargaPas ? 'Tarif Katalog' : 'Budget Customer'}</span>
                            <strong class="text-xs font-black text-[#173B67]">${budgetFormatted}</strong>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-sky-100 text-[9.5px]">
                        <span class="text-slate-500 font-medium"><i class="fa-regular fa-clock mr-1 text-sky-600"></i> ${order.jadwal || order.waktu || 'Hari ini'}</span>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" onclick="tolakSuruhanMitra('${kode}')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 text-[9px] font-bold border border-slate-200 transition-all cursor-pointer whitespace-nowrap" title="Tolak / Lewatkan">
                                <i class="fa-solid fa-xmark"></i> Lewatkan
                            </button>
                            <button type="button" onclick="mitraPilihCustomer('${kode}', '${order.budget || order.biaya || '50.000'}')" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[9px] transition-all shadow-xs cursor-pointer flex items-center gap-1 active:scale-95 whitespace-nowrap" title="Langsung pilih customer ini">
                                <i class="fa-solid fa-user-check"></i> Pilih
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        return `
            <article class="card-sb p-3.5 space-y-2.5 border-2 border-sky-300 bg-gradient-to-r from-sky-50/60 to-white shadow-xs">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full bg-blue-600 text-white animate-pulse">
                                Baru dari Customer
                            </span>
                            <span class="text-[9px] font-mono text-[#173B67] font-bold">${kode}</span>
                        </div>
                        <h3 class="text-xs font-extrabold text-slate-900 leading-snug">${order.layanan}</h3>
                        <p class="text-[9.5px] text-slate-600 mt-0.5">Pemesan: <strong class="text-slate-800">${namaCust}</strong> • Telp: <span class="text-slate-700 font-mono">${order.customerPhone || '-'}</span></p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[8px] text-slate-400 block">Biaya Customer</span>
                        <strong class="text-xs font-black text-[#173B67] block mt-0.5">${budgetFormatted}</strong>
                        <span class="text-[8px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Siap Dipilih</span>
                    </div>
                </div>

                <div class="p-2 rounded-xl bg-white border border-slate-200/80 space-y-1 text-[9.5px] text-slate-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock text-sky-600 w-3.5 text-center"></i>
                        <span><strong>Waktu Pelaksanaan:</strong> ${order.jadwal || order.waktu || 'Hari ini'}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-rose-500 w-3.5 text-center"></i>
                        <span><strong>Lokasi:</strong> ${order.alamat || '-'}</span>
                    </div>
                    ${order.note ? `
                    <div class="flex items-start gap-2 pt-1 border-t border-slate-100">
                        <i class="fa-solid fa-note-sticky text-amber-500 w-3.5 text-center mt-0.5"></i>
                        <span class="text-slate-700"><strong>Catatan Customer:</strong> "${order.note}"</span>
                    </div>
                    ` : ''}
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-slate-100 gap-1.5">
                    <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="py-1.5 px-2.5 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 font-bold text-[9.5px] flex items-center gap-1 transition-colors shrink-0 whitespace-nowrap cursor-pointer">
                        <i class="fa-solid fa-comments text-sky-600"></i> Chat Customer
                    </button>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" onclick="tolakSuruhanMitra('${kode}')" class="py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 border border-slate-200 font-bold text-[9.5px] transition-all cursor-pointer whitespace-nowrap" title="Tolak suruhan ini (tetap ditawarkan ke mitra lain)">
                            <i class="fa-solid fa-xmark mr-0.5"></i> Lewatkan
                        </button>
                        <button type="button" onclick="mitraPilihCustomer('${kode}', '${order.budget || order.biaya || '50.000'}')" class="py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-[10px] shadow-sm transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 whitespace-nowrap" title="Langsung pilih customer ini">
                            <i class="fa-solid fa-user-check"></i> Pilih Customer
                        </button>
                    </div>
                </div>
            </article>
        `;
    }

    function createMitraPekCardHtml(order, statusTab) {
        const budgetFormatted = order.budget ? ('Rp ' + order.budget) : 'Rp 85.000';
        const kode = order.code || '#SB-SURUH-XXXX';
        const namaCust = order.customerName || 'Customer SayaBantu';
        const phone = (order.customerPhone || '6281234567890').replace(/[^0-9]/g, '');
        const myName = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala'));

        if (statusTab === 'penawaran') {
            const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: (window.CURRENT_MITRA_DATA?.id || 'SB-7615') };
            const myId = currentMitra.id;
            const isMenungguAdmin = (order.status === 'menunggu_konfirmasi_admin');
            const isMenungguBayar = (order.status === 'menunggu_pembayaran' || order.status === 'siap_bayar');
            const isMenungguVerifikasiBayar = (order.status === 'menunggu_konfirmasi_pembayaran');
            const isDealDone = ['terjadwal', 'dibayar', 'lunas', 'otw', 'berjalan', 'selesai'].includes(order.status);
            const isAssignedToOther = (order.selectedMitraId && order.selectedMitraId !== myId && order.selectedMitraName && order.selectedMitraName !== myName);
            // Hanya benar-benar disepakati / dipilih jika sudah masuk tahap persetujuan kesepakatan resmi
            const isAssignedToMe = (isMenungguAdmin || isMenungguBayar || isMenungguVerifikasiBayar || isDealDone) && (order.selectedMitraId === myId || order.selectedMitraName === myName || order.mitra_name === myName || order.mitraName === myName);
            const isCustomerNego = (order.nego_by === 'customer' && !isMenungguAdmin && !isMenungguBayar && !isMenungguVerifikasiBayar && !isDealDone);

            return `
                <div class="card-sb p-3.5 border ${isAssignedToOther ? 'border-slate-300 bg-slate-50/80' : (isAssignedToMe || isMenungguAdmin || isMenungguBayar || isMenungguVerifikasiBayar) ? 'border-emerald-300 bg-emerald-50/40' : 'border-sky-300 bg-white'} space-y-2.5 shadow-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[8.5px] font-bold px-2 py-0.5 rounded-full uppercase ${
                                isAssignedToOther ? 'bg-slate-200 text-slate-700' :
                                isMenungguVerifikasiBayar ? 'bg-amber-100 text-amber-900 border border-amber-300' :
                                isMenungguBayar ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' :
                                (isAssignedToMe && isMenungguAdmin) ? 'bg-emerald-100 text-emerald-800' :
                                isCustomerNego ? 'bg-amber-100 text-amber-900 border border-amber-300' :
                                isAssignedToMe ? 'bg-emerald-100 text-emerald-800' :
                                'bg-blue-100 text-[#173B67]'
                            }">
                                ${
                                    isAssignedToOther ? 'Customer Telah Memilih Mitra Lain' :
                                    isMenungguVerifikasiBayar ? 'Pembayaran Customer Sedang Diverifikasi Admin' :
                                    isMenungguBayar ? 'Kesepakatan Disetujui Admin • Menunggu Pembayaran Customer' :
                                    isMenungguAdmin ? 'Kesepakatan Tercapai • Menunggu Konfirmasi Admin' :
                                    isCustomerNego ? 'Customer Menawar Balik' :
                                    isAssignedToMe ? 'Tawaran Dipilih Customer' :
                                    'Menunggu Keputusan Customer'
                                }
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                        </div>
                        <div class="text-right shrink-0">
                            ${(isMenungguAdmin || isMenungguBayar || isMenungguVerifikasiBayar) ? `
                                <span class="text-[8px] text-emerald-700 font-bold block">Tarif Kesepakatan:</span>
                                <strong class="text-xs font-black text-emerald-800">Rp ${(!String(order.agreed_price || '').includes('undefined') && order.agreed_price) ? order.agreed_price : (order.biaya || order.budget || '43.000')}</strong>
                            ` : isCustomerNego ? `
                                <span class="text-[8px] text-amber-700 font-bold block">Tawaran Balik Customer:</span>
                                <strong class="text-xs font-black text-amber-700">Rp ${order.biaya || order.budget || '50.000'}</strong>
                            ` : (order.nego_price && !String(order.nego_price).toLowerCase().includes('undefined') && !String(order.nego_price).toLowerCase().includes('null')) ? `
                                <span class="text-[8px] text-amber-700 font-bold block">Tawaran Nego Anda:</span>
                                <strong class="text-xs font-black text-amber-700">Rp ${String(order.nego_price || order.agreed_price || order.biaya || '43.000').replace(/^Rp\s*/i, '')}</strong>
                                <span class="text-[8px] text-slate-400 block line-through">Budget: ${budgetFormatted}</span>
                            ` : `
                                <span class="text-[8px] text-slate-400 block">Budget Customer:</span>
                                <strong class="text-xs font-black text-[#173B67]">${budgetFormatted}</strong>
                            `}
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-[9.5px] text-slate-600 space-y-1">
                        <p><i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Pemesan: <strong>${namaCust}</strong></p>
                        <p><i class="fa-regular fa-clock text-sky-600 mr-1.5"></i> Jadwal: <strong>${order.jadwal || order.waktu || 'Hari ini'}</strong></p>
                        ${isAssignedToOther ? `
                            <div class="p-2 rounded-lg bg-slate-100 text-[9px] text-slate-600 border border-slate-200 leading-relaxed">
                                <i class="fa-solid fa-circle-info text-slate-500 mr-1"></i> Customer telah memilih Mitra lain untuk pekerjaan ini. Penawaran Anda ditutup sehingga tidak terjadi penerimaan ganda.
                            </div>
                        ` : isMenungguVerifikasiBayar ? `
                            <div class="p-2 rounded-lg bg-amber-50 text-[9px] text-amber-900 border border-amber-200 leading-relaxed">
                                <i class="fa-solid fa-hourglass-half text-amber-600 mr-1"></i> Customer telah melakukan pembayaran <strong>Rp ${order.agreed_price || order.biaya || '43.000'}</strong>. Admin sedang memverifikasi sebelum jadwal resmi diterbitkan.
                            </div>
                        ` : isMenungguBayar ? `
                            <div class="p-2 rounded-lg bg-emerald-100/70 text-[9px] text-emerald-900 border border-emerald-200 leading-relaxed">
                                <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Admin telah menyetujui kesepakatan harga <strong>Rp ${order.agreed_price || order.biaya || '43.000'}</strong>! Menunggu Customer menyelesaikan pembayaran.
                            </div>
                        ` : isMenungguAdmin ? `
                            <div class="p-2 rounded-lg bg-emerald-100/70 text-[9px] text-emerald-900 border border-emerald-200 leading-relaxed">
                                <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Kesepakatan harga Rp ${(!String(order.agreed_price || '').includes('undefined') && order.agreed_price) ? order.agreed_price : (order.biaya || order.budget || '43.000')} telah disepakati! Menunggu verifikasi konfirmasi Admin sebelum tahap pembayaran.
                            </div>
                        ` : isCustomerNego ? `
                            <div class="p-2 rounded-lg bg-amber-50 text-[9px] text-amber-900 border border-amber-200 leading-relaxed">
                                <i class="fa-solid fa-comments-dollar text-amber-600 mr-1"></i> Customer mengajukan tawaran balik sebesar <strong>Rp ${order.biaya || order.budget || '50.000'}</strong>. Anda dapat menyepakatinya untuk meneruskan ke Admin atau bernegosiasi lagi.
                            </div>
                        ` : `
                            <div class="p-2 rounded-lg bg-sky-50 text-[9px] text-sky-900 border border-sky-100 leading-relaxed">
                                <i class="fa-solid fa-scale-balanced text-sky-600 mr-1"></i> Tawaran telah terkirim. Customer sedang membandingkan profil &amp; penawaran mitra sebelum memilih 1 Mitra resmi.
                            </div>
                        `}
                    </div>

                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                        ${!isAssignedToOther && !isMenungguAdmin && !isMenungguBayar && !isMenungguVerifikasiBayar ? (
                            isCustomerNego ? `
                                <button type="button" onclick="mitraSepakatiTawaranCustomer('${kode}', '${order.biaya || order.budget || '50.000'}')" class="flex-1 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] text-center flex items-center justify-center gap-1 shadow-sm transition-all cursor-pointer">
                                    <i class="fa-solid fa-check"></i> Sepakati (Rp ${order.biaya || order.budget || '50.000'})
                                </button>
                                <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="py-1.5 px-3 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[9.5px] flex items-center justify-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-comments"></i> Chat Aplikasi
                                </button>
                            ` : `
                                <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="w-full py-1.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[10px] text-center flex items-center justify-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-comments text-xs"></i> Chat Aplikasi
                                </button>
                            `
                        ) : isMenungguVerifikasiBayar ? `
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[9.5px] text-amber-800 font-bold py-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hourglass-half text-amber-500 animate-spin"></i> Menunggu Verifikasi Admin...
                                </span>
                                <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="py-1 px-3 rounded-lg bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[9px] flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-comments"></i> Chat
                                </button>
                            </div>
                        ` : isMenungguBayar ? `
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[9.5px] text-emerald-800 font-bold py-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-hourglass-half text-amber-500 animate-spin"></i> Menunggu Pembayaran Customer...
                                </span>
                                <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="py-1 px-3 rounded-lg bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[9px] flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-comments"></i> Chat
                                </button>
                            </div>
                        ` : isMenungguAdmin ? `
                            <span class="text-[9.5px] text-emerald-800 font-bold py-1 flex items-center gap-1"><i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Menunggu Konfirmasi Admin</span>
                        ` : `
                            <span class="text-[9px] text-slate-400 italic py-1">Tawaran Selesai (Mitra Lain Terpilih)</span>
                        `}
                    </div>
                </div>
            `;
        }

        if (statusTab === 'dikonfirmasi') {
            const isSiapBayar = (order.status === 'siap_bayar');
            return `
                <div class="card-sb p-3.5 border border-sky-300 bg-white space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] font-bold ${isSiapBayar ? 'text-amber-700 bg-amber-100' : 'text-blue-700 bg-blue-100'} px-2 py-0.5 rounded-full uppercase">
                                ${isSiapBayar ? 'Menunggu Pembayaran Customer' : 'Jadwal Dikonfirmasi (Lunas)'}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                        </div>
                        <strong class="text-xs font-black text-[#173B67]">${budgetFormatted}</strong>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 text-[9.5px] text-slate-600 space-y-1">
                        <p><i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Pemesan: <strong>${namaCust}</strong> (${order.customerPhone || '-'})</p>
                        <p><i class="fa-regular fa-clock text-sky-600 mr-1.5"></i> Jadwal: <strong>${order.jadwal || order.waktu || 'Hari ini'}</strong></p>
                        <p><i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> Alamat: <strong>${order.alamat || '-'}</strong></p>
                    </div>

                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                        <button type="button" onclick="bukaModalBatalMitra('${kode}')" class="py-1.5 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9.5px] cursor-pointer transition-colors" title="Batalkan tugas ini (penalti performa berlaku)">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500 mr-1"></i> Batal
                        </button>
                        <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="flex-1 py-1.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[10px] text-center flex items-center justify-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-comments text-xs"></i> Chat Aplikasi
                        </button>
                        <button type="button" onclick="mulaiOtwCustomer('${kode}')" class="py-1.5 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-[10px] cursor-pointer shrink-0">
                            <i class="fa-solid fa-motorcycle mr-1"></i> Mulai OTW
                        </button>
                    </div>
                </div>
            `;
        }

        if (statusTab === 'berlangsung') {
            const isOtw = (order.status === 'otw');
            const isMenungguCustomer = (order.status === 'menunggu_konfirmasi_customer');
            const isMenungguAdmin = (order.status === 'menunggu_konfirmasi_admin_selesai');

            const borderClass = isOtw ? 'border-l-amber-500' : (isMenungguAdmin ? 'border-l-indigo-600' : (isMenungguCustomer ? 'border-l-sky-500' : 'border-l-emerald-600'));
            const badgeClass = isOtw ? 'text-amber-800 bg-amber-100' : (isMenungguAdmin ? 'text-indigo-900 bg-indigo-100 border border-indigo-300' : (isMenungguCustomer ? 'text-sky-900 bg-sky-100 border border-sky-300' : 'text-emerald-800 bg-emerald-100'));
            const badgeText = isOtw ? 'Mitra Sedang OTW' : (isMenungguAdmin ? 'Menunggu Konfirmasi Admin' : (isMenungguCustomer ? 'Menunggu Konfirmasi Customer' : 'Pekerjaan Sedang Dikerjakan'));

            return `
                <div class="card-sb p-3.5 border-l-4 ${borderClass} border-slate-200 bg-white space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] font-extrabold ${badgeClass} px-2 py-0.5 rounded-full uppercase">
                                ${badgeText}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                        </div>
                        <strong class="text-xs font-black text-[#173B67]">${budgetFormatted}</strong>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 text-[9.5px] text-slate-600 space-y-1">
                        <p><i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Pemesan: <strong>${namaCust}</strong></p>
                        <p><i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> Lokasi: <strong>${order.alamat || '-'}</strong></p>
                    </div>

                    ${isMenungguAdmin ? `
                        <div class="p-2.5 rounded-xl bg-indigo-50/80 border border-indigo-200 text-[9px] text-indigo-950 flex items-start gap-2">
                            <i class="fa-solid fa-clipboard-check text-indigo-600 mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed">Pekerjaan telah dinyatakan selesai. <strong>Menunggu konfirmasi verifikasi Admin</strong> untuk pencairan dana escrow ke saldo Anda.</span>
                        </div>
                    ` : isMenungguCustomer ? `
                        <div class="p-2.5 rounded-xl bg-sky-50/80 border border-sky-200 text-[9px] text-sky-950 flex items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-sky-600 mt-0.5 shrink-0"></i>
                            <span class="leading-relaxed">Pekerjaan telah diselesaikan Mitra. <strong>Menunggu Customer memeriksa &amp; mengonfirmasi hasil pekerjaan</strong>.</span>
                        </div>
                    ` : ''}

                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                        ${(!isMenungguAdmin && !isMenungguCustomer) ? `
                            <button type="button" onclick="bukaModalBatalMitra('${kode}')" class="py-1.5 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9.5px] cursor-pointer transition-colors" title="Batalkan tugas darurat (penalti performa berlaku)">
                                <i class="fa-solid fa-triangle-exclamation text-rose-500 mr-1"></i> Batalkan
                            </button>
                        ` : ''}
                        <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="flex-1 py-1.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[10px] text-center flex items-center justify-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-comments text-xs"></i> Chat Aplikasi
                        </button>
                        ${isOtw ? `
                            <button type="button" onclick="mulaiKerjakanCustomer('${kode}')" class="py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] cursor-pointer">
                                <i class="fa-solid fa-location-dot mr-1"></i> Tiba &amp; Kerjakan
                            </button>
                        ` : isMenungguAdmin ? `
                            <div class="py-1.5 px-3 rounded-xl bg-indigo-100 text-indigo-900 border border-indigo-200 font-bold text-[9.5px] flex items-center gap-1 shrink-0">
                                <i class="fa-solid fa-hourglass-half text-indigo-600 animate-pulse"></i> Ditinjau Admin
                            </div>
                        ` : isMenungguCustomer ? `
                            <div class="py-1.5 px-3 rounded-xl bg-sky-100 text-sky-900 border border-sky-200 font-bold text-[9.5px] flex items-center gap-1 shrink-0">
                                <i class="fa-solid fa-clock text-sky-600"></i> Menunggu Customer
                            </div>
                        ` : `
                            <button type="button" onclick="selesaikanPekerjaanCustomer('${kode}', '${order.budget || '85.000'}')" class="py-1.5 px-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-[10px] cursor-pointer">
                                <i class="fa-solid fa-circle-check mr-1"></i> Selesaikan
                            </button>
                        `}
                    </div>
                </div>
            `;
        }

        if (statusTab === 'selesai') {
            const rStars = parseInt(order.review_rating || '0', 10);
            const starsText = rStars > 0 ? ('★'.repeat(rStars) + '☆'.repeat(5 - rStars)) : '';
            return `
                <div class="card-sb p-3.5 border-l-4 border-l-emerald-600 border-slate-200 bg-white space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full uppercase border border-emerald-200">
                                Selesai &amp; Dana Diteruskan
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                        </div>
                        <strong class="text-xs font-black text-emerald-700">${budgetFormatted}</strong>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 text-[9.5px] text-slate-600 space-y-1">
                        <p><i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Pemesan: <strong>${namaCust}</strong></p>
                        <p><i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> Lokasi: <strong>${order.alamat || '-'}</strong></p>
                        <p><i class="fa-regular fa-clock text-sky-600 mr-1.5"></i> Jadwal: <strong>${order.jadwal || order.waktu || 'Telah Selesai'}</strong></p>
                    </div>

                    ${rStars > 0 ? `
                        <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80 text-[9.5px] text-amber-900 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-[9px] text-amber-800">Ulasan &amp; Rating Customer:</span>
                                <span class="text-amber-500 text-[10px] font-bold">${starsText} (${rStars}.0)</span>
                            </div>
                            ${order.review_comment ? `<p class="italic text-slate-700 leading-snug">"${order.review_comment}"</p>` : ''}
                        </div>
                    ` : ''}

                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 gap-2">
                        <span class="text-[8.5px] font-bold text-emerald-700 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Dana Masuk ke Saldo Mitra
                        </span>
                        <button type="button" onclick="switchMitraNav('pesan'); openChatRoom('${(kode || '').replace('#', '')}', '${namaCust.replace(/'/g, "\\'")}', '${(order.layanan || '').replace(/'/g, "\\'")}', '${order.budget || ''}')" class="py-1.5 px-3 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[9.5px] flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-comments text-xs"></i> Chat
                        </button>
                    </div>
                </div>
            `;
        }

        if (statusTab === 'dibatalkan') {
            return `
                <div class="card-sb p-3.5 border-l-4 border-l-rose-500 border-slate-200 bg-white space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] font-extrabold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-full uppercase border border-rose-200">
                                Pesanan Dibatalkan
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1">${order.layanan}</h4>
                        </div>
                        <span class="text-xs font-bold text-slate-400 line-through">${budgetFormatted}</span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 text-[9.5px] text-slate-600 space-y-1">
                        <p><i class="fa-solid fa-user text-slate-400 mr-1.5"></i> Pemesan: <strong>${namaCust}</strong></p>
                        <p><i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> Lokasi: <strong>${order.alamat || '-'}</strong></p>
                    </div>
                </div>
            `;
        }

        return '';
    }

    async function terimaPekerjaanCustomer(orderCode, agreedPrice) {
        const currentMitra = getCurrentMitraProfile();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(String(orderCode || '').replace('#', ''))}/mitra-sepakati-nego`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    biaya: agreedPrice,
                    mitra_id: currentMitra.id,
                    mitra_name: currentMitra.name
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Pekerjaan belum berhasil diterima.');
            }

            updateCustomerOrderStatus(orderCode, 'menunggu_konfirmasi_admin', 6, {
                mitraId: currentMitra.id,
                mitraName: currentMitra.name,
                budget: agreedPrice,
                harga: agreedPrice,
                agreed_by: 'mitra'
            });
            showMitraToast('Pekerjaan Diterima', data.message, 'success');
            loadMitraCustomerOrders();
            switchMitraNav('pekerjaan');
            filterStatusPekerjaan('penawaran', document.querySelectorAll('.btn-status-pek')[0]);
        } catch (error) {
            showMitraToast('Gagal Menerima Pekerjaan', error.message || 'Silakan coba lagi.', 'error');
        }
    }

    function tolakSuruhanMitra(orderCode) {
        const myMitraId = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().id : (window.CURRENT_MITRA_DATA?.id || 'SB-7615'));
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) {
                const orders = JSON.parse(raw);
                const found = orders.find(o => (o.code || '').replace('#', '') === orderCode.replace('#', ''));
                if (found) {
                    found.rejectedMitras = found.rejectedMitras || [];
                    if (!found.rejectedMitras.includes(myMitraId)) {
                        found.rejectedMitras.push(myMitraId);
                    }
                    localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                }
            }
        } catch(e) {}

        showMitraToast('Suruhan Dilewati', `Suruhan ${orderCode} telah Anda lewati.`, 'info');
        loadMitraCustomerOrders();
    }

    let __mitraOrderToCancel = null;

    function bukaModalBatalMitra(orderCode) {
        __mitraOrderToCancel = orderCode;
        const modal = document.getElementById('modalBatalMitra');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.bukaModalBatalMitra = bukaModalBatalMitra;

    function closeModalBatalMitra() {
        __mitraOrderToCancel = null;
        const modal = document.getElementById('modalBatalMitra');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeModalBatalMitra = closeModalBatalMitra;

    function toggleMitraCustomCancelReason(showCustom) {
        const wrap = document.getElementById('mitraCustomCancelReasonWrap');
        if (wrap) {
            wrap.classList.toggle('hidden', !showCustom);
            if (showCustom) {
                document.getElementById('mitraCustomCancelReasonInput')?.focus();
            }
        }
    }
    window.toggleMitraCustomCancelReason = toggleMitraCustomCancelReason;

    async function submitBatalMitraAction() {
        if (!__mitraOrderToCancel) return;
        const btn = document.getElementById('btnSubmitBatalMitra');
        const selectedRadio = document.querySelector('input[name="mitraCancelReasonPreset"]:checked');
        let reason = selectedRadio ? selectedRadio.value : '';
        if (reason === 'custom') {
            reason = document.getElementById('mitraCustomCancelReasonInput')?.value.trim();
        }
        if (!reason) {
            showMitraToast('Alasan Diperlukan', 'Silakan pilih atau isi alasan darurat pembatalan tugas.', 'warning');
            return;
        }

        if (btn) btn.disabled = true;

        const cleanCode = __mitraOrderToCancel.replace('#', '');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const res = await fetch(`/api/orders/${cleanCode}/mitra-cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason })
            });
            const data = await res.json();

            // Update local state sb_customer_orders
            try {
                const raw = localStorage.getItem('sb_customer_orders');
                if (raw) {
                    const orders = JSON.parse(raw);
                    const found = orders.find(o => (o.code || '').replace('#', '') === cleanCode);
                    if (found) {
                        found.status = 'waiting_for_partner';
                        found.selectedMitraId = null;
                        found.mitraName = null;
                        found.cancellation_reason = `Mitra berhalangan hadir: ${reason}`;
                        localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                    }
                }
            } catch(e) {}

            closeModalBatalMitra();
            showMitraToast('Tugas Dibatalkan', `Tugas ${__mitraOrderToCancel} berhasil dibatalkan.`, 'warning');
            loadMitraCustomerOrders();
            switchMitraNav('pekerjaan');
            const btnDibatalkan = document.querySelectorAll('.btn-status-pek')[4];
            if (btnDibatalkan) {
                filterStatusPekerjaan('dibatalkan', btnDibatalkan);
            }
        } catch(err) {
            console.error(err);
            showMitraToast('Gagal', 'Terjadi kendala saat membatalkan tugas.', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    }
    window.submitBatalMitraAction = submitBatalMitraAction;

    let toastMitraTimeout = null;
    function showMitraToast(title, msg, type = 'info') {
        let toast = document.getElementById('mitraToast');
        if (!toast) {
            const wrap = document.createElement('div');
            wrap.id = 'mitraToast';
            wrap.className = 'fixed top-5 left-1/2 -translate-x-1/2 max-w-[360px] w-full px-4 z-50 pointer-events-none transition-all duration-300 opacity-0 -translate-y-4';
            wrap.innerHTML = `
                <div class="bg-slate-900/95 text-white backdrop-blur-md rounded-2xl p-3 shadow-xl border border-white/20 flex items-center gap-3">
                    <div id="mitraToastIcon" class="w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs">
                        <i class="fa-solid fa-info"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5 id="mitraToastTitle" class="text-xs font-bold leading-tight truncate">Info</h5>
                        <p id="mitraToastMessage" class="text-[11px] text-slate-300 leading-snug mt-0.5 truncate">Pesan informasi</p>
                    </div>
                </div>
            `;
            document.body.appendChild(wrap);
            toast = wrap;
        }
        const titleEl = document.getElementById('mitraToastTitle');
        const msgEl = document.getElementById('mitraToastMessage');
        const iconEl = document.getElementById('mitraToastIcon');

        if (titleEl) titleEl.innerText = title;
        if (msgEl) msgEl.innerText = msg;

        if (iconEl) {
            if (type === 'success') {
                iconEl.className = 'w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 text-xs';
                iconEl.innerHTML = '<i class="fa-solid fa-check"></i>';
            } else if (type === 'warning') {
                iconEl.className = 'w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-xs';
                iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
            } else if (type === 'error') {
                iconEl.className = 'w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 text-xs';
                iconEl.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            } else {
                iconEl.className = 'w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs';
                iconEl.innerHTML = '<i class="fa-solid fa-info"></i>';
            }
        }

        toast.classList.remove('opacity-0', '-translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');

        clearTimeout(toastMitraTimeout);
        toastMitraTimeout = setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', '-translate-y-4');
        }, 3000);
    }
    window.showMitraToast = showMitraToast;

    async function mitraSepakatiTawaranCustomer(orderCode, agreedPrice) {
        const cleanCode = (orderCode || '').replace('#', '');
        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: 'SB-7615', name: 'Kala' };

        if (!confirm(`Sepakati tawaran harga Customer sebesar Rp ${agreedPrice}?\n\nSetelah Anda sepakati, berkas kesepakatan akan diteruskan ke Tim Admin SayaBantu untuk verifikasi.`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(cleanCode)}/mitra-sepakati-nego`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    biaya: agreedPrice,
                    mitra_id: currentMitra.id,
                    mitra_name: currentMitra.name
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Kesepakatan belum berhasil dikirim ke Admin.');
            }

            updateCustomerOrderStatus(orderCode, 'menunggu_konfirmasi_admin', 6, {
                selectedMitraId: currentMitra.id,
                mitraName: currentMitra.name,
                budget: agreedPrice,
                biaya: agreedPrice,
                agreed_by: 'mitra'
            });
            loadMitraCustomerOrders();
            switchMitraNav('pekerjaan');
            filterStatusPekerjaan('penawaran', document.querySelectorAll('.btn-status-pek')[0]);
            showMitraToast('Kesepakatan Disepakati', data.message, 'success');
        } catch (error) {
            showMitraToast('Gagal', error.message || 'Gagal menyepakati tawaran. Coba lagi.', 'error');
        }
    }
    window.mitraSepakatiTawaranCustomer = mitraSepakatiTawaranCustomer;

    async function mitraPilihCustomer(orderCode, priceStr) {
        const cleanCode = (orderCode || '').replace('#', '');
        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: 'SB-7615', name: 'Kala' };
        const cleanPrice = String(priceStr || '').replace(/[^0-9]/g, '') || '50000';
        const formattedPrice = parseInt(cleanPrice, 10).toLocaleString('id-ID');

        if (!confirm(`Pilih pesanan ${orderCode} dari customer ini?\n\nPesanan akan langsung Anda ambil dan diteruskan ke Tim Admin SayaBantu untuk verifikasi & konfirmasi pembayaran.`)) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(cleanCode)}/mitra-sepakati-nego`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    biaya: formattedPrice,
                    mitra_id: currentMitra.id,
                    mitra_name: currentMitra.name
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal memilih customer.');
            }

            updateCustomerOrderStatus(orderCode, 'menunggu_konfirmasi_admin', 6, {
                mitraId: currentMitra.id,
                mitraName: currentMitra.name,
                budget: formattedPrice,
                harga: formattedPrice,
                agreed_by: 'mitra'
            });

            showMitraToast('Customer Berhasil Dipilih!', `Anda telah memilih pesanan ${orderCode}. Menunggu verifikasi Admin.`, 'success');
            loadMitraCustomerOrders();
            switchMitraNav('pekerjaan');
            filterStatusPekerjaan('penawaran', document.querySelectorAll('.btn-status-pek')[0]);
        } catch (error) {
            showMitraToast('Gagal Memilih Customer', error.message || 'Silakan coba lagi.', 'error');
        }
    }
    window.mitraPilihCustomer = mitraPilihCustomer;

    function bukaModalAjukanPenawaranCustomer(code, judul, budget, cust, area) {
        mitraPilihCustomer(code, budget);
    }
    window.bukaModalAjukanPenawaranCustomer = bukaModalAjukanPenawaranCustomer;

    function mitraLangsungSepakatiHargaCustomer(orderCode, priceStr) {
        mitraPilihCustomer(orderCode, priceStr);
    }
    window.mitraLangsungSepakatiHargaCustomer = mitraLangsungSepakatiHargaCustomer;

    function mitraModalLangsungSepakati() {
        const orderCode = window.__activeCustomerOrderCode;
        const budgetEl = document.getElementById('labelModalPenawaranBudget');
        const budgetStr = budgetEl ? budgetEl.innerText : '50.000';
        closeModalMitra('modalAjukanPenawaran');
        mitraPilihCustomer(orderCode, budgetStr);
    }
    window.mitraModalLangsungSepakati = mitraModalLangsungSepakati;

    async function mulaiOtwCustomer(orderCode) {
        if (!await updateCustomerOrderStatus(orderCode, 'otw', 9)) return;
        showMitraToast('Mitra OTW', 'Status diubah ke OTW. Customer menerima notifikasi Anda sedang menuju lokasi.', 'success');
        loadMitraCustomerOrders();
        switchMitraNav('pekerjaan');
        filterStatusPekerjaan('berlangsung', document.querySelectorAll('.btn-status-pek')[2]);
    }

    async function mulaiKerjakanCustomer(orderCode) {
        if (!await updateCustomerOrderStatus(orderCode, 'berjalan', 10)) return;
        showMitraToast('Sedang Dikerjakan', 'Status diubah: Mitra telah tiba dan mulai melaksanakan tugas.', 'success');
        loadMitraCustomerOrders();
    }

    async function selesaikanPekerjaanCustomer(orderCode, nominalStr) {
        if (!await updateCustomerOrderStatus(orderCode, 'menunggu_konfirmasi_customer', 10)) return;
        showMitraToast('Pekerjaan Selesai', 'Pekerjaan dinyatakan selesai. Menunggu konfirmasi Customer & Admin.', 'success');
        loadMitraCustomerOrders();
        switchMitraNav('pekerjaan');
        filterStatusPekerjaan('berlangsung', document.querySelectorAll('.btn-status-pek')[2]);
    }

    window.mulaiOtwCustomer = mulaiOtwCustomer;
    window.mulaiKerjakanCustomer = mulaiKerjakanCustomer;
    window.selesaikanPekerjaanCustomer = selesaikanPekerjaanCustomer;

    async function updateCustomerOrderStatus(orderCode, newStatus, stepNumber, bidData = null) {
        if (!orderCode) return false;
        const cleanCode = orderCode.replace('#', '');
        const currentMitra = typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile() : { id: window.CURRENT_MITRA_DATA?.id || 'SB-7615', name: window.CURRENT_MITRA_DATA?.name || 'Kala', phone: window.CURRENT_MITRA_DATA?.phone || '08128892509910' };

        let apiEndpoint = null;
        if (newStatus === 'otw') apiEndpoint = `/api/orders/${encodeURIComponent(cleanCode)}/mitra-otw`;
        else if (newStatus === 'berjalan') apiEndpoint = `/api/orders/${encodeURIComponent(cleanCode)}/mitra-kerja`;
        else if (newStatus === 'menunggu_konfirmasi_customer') apiEndpoint = `/api/orders/${encodeURIComponent(cleanCode)}/mitra-selesai`;

        if (apiEndpoint) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Status pekerjaan belum berhasil diperbarui.');
                }
            } catch (error) {
                showMitraToast('Status Gagal Diperbarui', error.message || 'Silakan coba lagi.', 'error');
                return false;
            }
        }

        let orders = [];
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) orders = JSON.parse(raw);
            const found = orders.find(o => (o.code || o.id || '').replace('#', '') === cleanCode);
            if (found) {
                found.status = newStatus;
                found.step = stepNumber;
                if (bidData) {
                    found.mitraName = bidData.mitraName || currentMitra.name;
                    found.mitraPhone = bidData.mitraPhone || currentMitra.phone;
                    found.mitraId = bidData.mitraId || currentMitra.id;
                    found.nego_price = bidData.hargaFormatted || String(bidData.harga);
                    found.latest_bid = bidData;
                    found.bids = found.bids || [];
                    const bidIdx = found.bids.findIndex(b => (b.mitraId || b.id) === (bidData.mitraId || currentMitra.id));
                    if (bidIdx >= 0) {
                        found.bids[bidIdx] = bidData;
                    } else {
                        found.bids.unshift(bidData);
                    }
                } else if (!found.mitraName) {
                    found.mitraName = currentMitra.name;
                    found.mitraPhone = currentMitra.phone;
                }
                localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
            }

            const rawActive = localStorage.getItem('sb_active_order');
            if (rawActive) {
                const act = JSON.parse(rawActive);
                if ((act.code || act.id || '').replace('#', '') === cleanCode) {
                    act.status = newStatus;
                    act.step = stepNumber;
                    if (bidData) {
                        act.mitraName = bidData.mitraName || currentMitra.name;
                        act.mitraPhone = bidData.mitraPhone || currentMitra.phone;
                        act.mitraId = bidData.mitraId || currentMitra.id;
                        act.nego_price = bidData.hargaFormatted || String(bidData.harga);
                        act.latest_bid = bidData;
                        act.bids = act.bids || [];
                        const bidIdx = act.bids.findIndex(b => (b.mitraId || b.id) === (bidData.mitraId || currentMitra.id));
                        if (bidIdx >= 0) {
                            act.bids[bidIdx] = bidData;
                        } else {
                            act.bids.unshift(bidData);
                        }
                    } else if (!act.mitraName) {
                        act.mitraName = currentMitra.name;
                        act.mitraPhone = currentMitra.phone;
                    }
                    localStorage.setItem('sb_active_order', JSON.stringify(act));
                }
            }

            // Simpan juga ke antrean penawaran spesifik tiket
            if (bidData) {
                let orderBids = [];
                try {
                    const rawBids = localStorage.getItem('sb_order_bids_' + cleanCode);
                    if (rawBids) orderBids = JSON.parse(rawBids);
                } catch(e) {}
                const existIdx = orderBids.findIndex(b => (b.mitraId || b.id) === (bidData.mitraId || currentMitra.id));
                if (existIdx >= 0) {
                    orderBids[existIdx] = bidData;
                } else {
                    orderBids.unshift(bidData);
                }
                localStorage.setItem('sb_order_bids_' + cleanCode, JSON.stringify(orderBids));
            }

            // Broadcast perubahan ke jendela/tab Customer
            window.dispatchEvent(new Event('storage'));
            try {
                window.dispatchEvent(new CustomEvent('sb_order_bid_submitted', { detail: { orderCode: cleanCode, bidData } }));
            } catch(e) {}

        } catch(e) {
            showMitraToast('Status Gagal Diperbarui', 'Data pesanan di perangkat tidak dapat diperbarui.', 'error');
            return false;
        }
        return true;
    }
    window.updateCustomerOrderStatus = updateCustomerOrderStatus;

    // =========================================================
    //  PUSAT PENGADUAN & MEDIASI MITRA
    // =========================================================
    window.__currentMitraCompFilter = 'all';
    let currentMitraActiveComplaint = null;

    // Bersihkan data dummy lama (hanya sekali jika tersisa)
    (function cleanupMitraDummyComplaints() {
        try {
            const key = 'sb_clean_reset_v9';
            if (!localStorage.getItem(key)) {
                localStorage.removeItem('sb_active_order');
                localStorage.removeItem('sb_mitra_orders');
                localStorage.removeItem('sb_complaints');
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const k = localStorage.key(i);
                    if (k && (k.startsWith('sb_chat_') || k.startsWith('sb_cached_') || k.startsWith('sb_order_'))) {
                        localStorage.removeItem(k);
                    }
                }
                localStorage.setItem(key, 'true');
            }
        } catch(e) {}
    })();

    function getMitraStoredComplaints() {
        try {
            const raw = localStorage.getItem('sb_complaints');
            return raw ? JSON.parse(raw) : [];
        } catch(e) {
            return [];
        }
    }

    function saveMitraStoredComplaints(complaints) {
        try {
            localStorage.setItem('sb_complaints', JSON.stringify(complaints));
        } catch(e) {}
    }

    function renderMitraComplaints() {
        const allComplaints = getMitraStoredComplaints();
        const list = allComplaints.filter(c => {
            const isReporter = c.reporterRole === 'mitra';
            const repd = (c.reportedName || '').toLowerCase();
            const rep = (c.reporterName || '').toLowerCase();
            const myNameLower = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'kala')).toLowerCase();
            return isReporter || repd.includes(myNameLower) || repd.includes('mitra') || rep.includes(myNameLower);
        });

        const activeList = list.length > 0 ? list : allComplaints;

        const total = activeList.length;
        const baru = activeList.filter(c => c.status === 'baru').length;
        const proses = activeList.filter(c => ['proses', 'menunggu_bukti', 'bukti_dikirim', 'dalam_mediasi'].includes(c.status)).length;
        const selesai = activeList.filter(c => ['selesai', 'ditolak', 'mediasi_tidak_tercapai', 'menunggu_keputusan_admin'].includes(c.status)).length;

        const statTotalEl = document.getElementById('statMitraTotal');
        const statBaruEl = document.getElementById('statMitraBaru');
        const statProsesEl = document.getElementById('statMitraProses');
        const statSelesaiEl = document.getElementById('statMitraSelesai');
        if (statTotalEl) statTotalEl.innerText = total;
        if (statBaruEl) statBaruEl.innerText = baru;
        if (statProsesEl) statProsesEl.innerText = proses;
        if (statSelesaiEl) statSelesaiEl.innerText = selesai;

        const lblCount = document.getElementById('lblMitraComplaintCount');
        if (lblCount) lblCount.innerText = `${activeList.length} Pengaduan`;

        const container = document.getElementById('mitraComplaintList');
        const emptyState = document.getElementById('emptyMitraComplaints');
        if (!container) return;

        const filter = window.__currentMitraCompFilter || 'all';
        const filtered = activeList.filter(c => {
            if (filter === 'all') return true;
            if (filter === 'baru') return c.status === 'baru';
            if (filter === 'proses') return ['proses', 'menunggu_bukti', 'bukti_dikirim', 'dalam_mediasi'].includes(c.status);
            if (filter === 'selesai') return ['selesai', 'ditolak', 'mediasi_tidak_tercapai', 'menunggu_keputusan_admin'].includes(c.status);
            return true;
        });

        if (filtered.length === 0) {
            container.innerHTML = '';
            if (emptyState) emptyState.classList.remove('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        let html = '';
        filtered.forEach(c => {
            const statusConfig = {
                'baru': {
                    label: 'Menunggu Pemeriksaan',
                    badgeClass: 'bg-rose-50 text-rose-700 border-rose-200',
                    iconClass: 'fa-clock text-rose-500'
                },
                'proses': {
                    label: 'Dalam Penyelidikan',
                    badgeClass: 'bg-amber-50 text-amber-700 border-amber-200',
                    iconClass: 'fa-magnifying-glass text-amber-500'
                },
                'menunggu_bukti': {
                    label: 'Perlu Bukti Tambahan',
                    badgeClass: 'bg-orange-50 text-orange-800 border-orange-200',
                    iconClass: 'fa-file-circle-question text-orange-500'
                },
                'bukti_dikirim': {
                    label: 'Bukti Terkirim',
                    badgeClass: 'bg-sky-50 text-sky-800 border-sky-200',
                    iconClass: 'fa-cloud-arrow-up text-sky-500'
                },
                'dalam_mediasi': {
                    label: 'Dalam Mediasi 3-Pihak',
                    badgeClass: 'bg-teal-50 text-teal-800 border-teal-300',
                    iconClass: 'fa-handshake text-teal-600'
                },
                'mediasi_tidak_tercapai': {
                    label: 'Mediasi Tidak Tercapai',
                    badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
                    iconClass: 'fa-handshake text-amber-600'
                },
                'menunggu_keputusan_admin': {
                    label: 'Mediasi Tidak Tercapai',
                    badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
                    iconClass: 'fa-handshake text-amber-600'
                },
                'selesai': {
                    label: 'Selesai Dimediasi',
                    badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    iconClass: 'fa-circle-check text-emerald-500'
                },
                'ditolak': {
                    label: 'Ditolak',
                    badgeClass: 'bg-slate-100 text-slate-700 border-slate-200',
                    iconClass: 'fa-ban text-slate-400'
                }
            }[c.status] || {
                label: 'Diproses',
                badgeClass: 'bg-sky-50 text-sky-700 border-sky-200',
                iconClass: 'fa-clock text-sky-500'
            };

            const isPelaporMitra = c.reporterRole === 'mitra';
            const peranLabel = isPelaporMitra ? 'Diajukan oleh Anda (Mitra)' : 'Laporan dari Customer';
            const peranBadge = isPelaporMitra ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-rose-50 text-rose-700 border-rose-200';
            const isMediationActive = c.status === 'dalam_mediasi';

            html += `
                <article class="card-sb p-3.5 space-y-2.5 bg-white border ${isMediationActive ? 'border-teal-400 ring-2 ring-teal-400/20 shadow-md' : 'border-slate-200/90 shadow-2xs'} hover:border-[#173B67] transition-all cursor-pointer" onclick="openDetailPengaduanMitra('${c.id}')">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-mono font-bold text-[10px] text-[#173B67] bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                                    ${c.id}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold border ${peranBadge}">
                                    ${peranLabel}
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 truncate mt-1">${c.category}</h4>
                            <p class="text-[9.5px] text-slate-500">
                                Suruhan: <strong class="text-slate-700 font-mono">${c.orderId || '-'}</strong> • Terlapor: <strong class="text-slate-700">${c.reportedName || '-'}</strong>
                            </p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border shrink-0 ${statusConfig.badgeClass}">
                            ${statusConfig.label}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50/80 border border-slate-100 text-[10px] text-slate-600 line-clamp-2 leading-relaxed">
                        ${c.chronology || c.summary}
                    </div>

                    ${isMediationActive ? `
                    <div class="p-2.5 rounded-xl bg-teal-50 border border-teal-200 text-[9.5px] text-teal-900 flex items-center justify-between gap-2">
                        <span class="flex items-center gap-1.5 font-bold">
                            <i class="fa-solid fa-comments text-teal-600"></i> Ruang Mediasi 3-Pihak Terbuka
                        </span>
                        <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-teal-600 text-white shadow-2xs">
                            Buka Dialog
                        </span>
                    </div>
                    ` : ''}

                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[9.5px]">
                        <span class="text-slate-400 flex items-center gap-1">
                            <i class="fa-regular ${statusConfig.iconClass}"></i> ${c.date}
                        </span>
                        <span class="text-[#173B67] font-bold flex items-center gap-1">
                            <span>${isMediationActive ? 'Masuk Mediasi' : 'Pantau Penanganan'}</span>
                            <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </span>
                    </div>
                </article>
            `;
        });

        container.innerHTML = html;
    }

    function filterMitraComplaints(status, btn) {
        window.__currentMitraCompFilter = status;
        document.querySelectorAll('.btn-tab-mitra-comp').forEach(t => {
            t.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            t.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        if (btn) {
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
            btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
        }
        renderMitraComplaints();
    }

    function openModalAjukanKeluhanMitra() {
        populateMitraOrderOptions();
        const modal = document.getElementById('modalAjukanKeluhanMitra');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalAjukanKeluhanMitra() {
        const modal = document.getElementById('modalAjukanKeluhanMitra');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function populateMitraOrderOptions() {
        const select = document.getElementById('pilihSuruhanMitra');
        if (!select) return;

        while (select.options.length > 2) {
            select.remove(1);
        }

        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) {
                const orders = JSON.parse(raw);
                orders.forEach(o => {
                    const opt = document.createElement('option');
                    const code = o.code || '#ORDER';
                    const customer = o.customerName || 'Customer';
                    const layanan = o.layanan || 'Suruhan';
                    opt.value = `${code} | ${customer} | ${layanan}`;
                    opt.textContent = `${code} - ${layanan} (Pemesan: ${customer})`;
                    select.insertBefore(opt, select.lastElementChild);
                });
            }
        } catch(e) {}
    }
    window.updateCustomerOrderStatus = updateCustomerOrderStatus;

    function onSelectMitraSuruhanPengaduan(selectEl) {
        const val = selectEl.value;
        const wrap = document.getElementById('wrapManualOrderMitra');
        const inputCust = document.getElementById('inputNamaCustomerTerlapor');

        if (val === 'manual') {
            if (wrap) wrap.classList.remove('hidden');
            if (inputCust && !inputCust.value) inputCust.value = '';
        } else if (val) {
            if (wrap) wrap.classList.add('hidden');
            const parts = val.split('|').map(s => s.trim());
            if (parts.length >= 2 && inputCust) {
                inputCust.value = parts[1] + ' (Customer)';
            }
        } else {
            if (wrap) wrap.classList.add('hidden');
        }
    }

    function handlePreviewBuktiMitra(input) {
        if (input.files && input.files[0]) {
            const ph = document.getElementById('buktiPreviewPlaceholderMitra');
            const act = document.getElementById('buktiPreviewActiveMitra');
            const txt = document.getElementById('namaFileBuktiMitra');
            if (ph) ph.classList.add('hidden');
            if (act) act.classList.remove('hidden');
            if (txt) txt.innerText = input.files[0].name;
        }
    }

    function submitPengaduanMitra(e) {
        e.preventDefault();
        const selSuruhan = document.getElementById('pilihSuruhanMitra').value;
        const manualSuruhan = document.getElementById('inputManualOrderMitra') ? document.getElementById('inputManualOrderMitra').value.trim() : '';
        const orderId = selSuruhan === 'manual' ? (manualSuruhan || '#ORDER-MANUAL') : (selSuruhan.split('|')[0] || '#SB-SURUH').trim();

        const reported = document.getElementById('inputNamaCustomerTerlapor').value.trim() || 'Customer Pemesan';
        const category = document.getElementById('pilihKategoriKeluhanMitra').value;
        const chron = document.getElementById('inputKronologiMitra').value.trim();
        const fileInput = document.getElementById('inputFileBuktiMitra');
        const evidenceName = (fileInput && fileInput.files && fileInput.files[0]) ? fileInput.files[0].name : 'Foto kendala di lokasi & riwayat chat';

        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' • ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const newId = '#ADU-' + Math.floor(100 + Math.random() * 900);

        const newComplaint = {
            id: newId,
            orderId: orderId,
            reporterName: (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala')),
            reporterRole: 'mitra',
            reportedName: reported,
            category: category,
            summary: chron.length > 80 ? chron.slice(0, 80) + '...' : chron,
            chronology: chron,
            evidence: evidenceName,
            status: 'baru',
            adminNote: '',
            date: dateStr,
            timeline: [
                { stage: 'Pengaduan Mitra Diterima', time: dateStr, desc: 'Laporan kendala tugas berhasil dicatat di sistem terpusat SayaBantu.' },
                { stage: 'Menunggu Penelaahan Tim Mediasi Admin', time: dateStr, desc: 'Admin akan memeriksa kronologi kejadian & rekaman koordinasi.' }
            ]
        };

        const existing = getMitraStoredComplaints();
        existing.unshift(newComplaint);
        saveMitraStoredComplaints(existing);

        document.getElementById('formPengaduanMitra').reset();
        const ph = document.getElementById('buktiPreviewPlaceholderMitra');
        const act = document.getElementById('buktiPreviewActiveMitra');
        if (ph) ph.classList.remove('hidden');
        if (act) act.classList.add('hidden');

        closeModalAjukanKeluhanMitra();
        renderMitraComplaints();

        showMitraToast('Pengaduan Diajukan', `Laporan ${newId} berhasil dikirimkan ke Tim Mediasi Admin.`, 'success');
    }

    function openDetailPengaduanMitra(id) {
        const list = getMitraStoredComplaints();
        const comp = list.find(c => c.id === id);
        if (!comp) return;

        currentMitraActiveComplaint = comp;

        document.getElementById('detMitraTiketId').innerText = comp.id;
        document.getElementById('detMitraTiketKategori').innerText = comp.category;
        document.getElementById('detMitraTiketOrder').innerText = comp.orderId || '-';
        document.getElementById('detMitraTiketReported').innerText = comp.reportedName || '-';
        document.getElementById('detMitraTiketDate').innerText = comp.date;
        document.getElementById('detMitraTiketChronology').innerText = comp.chronology || comp.summary;
        document.getElementById('detMitraTiketEvidenceText').innerText = comp.evidence || 'Tidak ada berkas bukti yang disertakan';

        const badge = document.getElementById('detMitraTiketBadge');
        if (badge) {
            const labels = {
                'baru': 'Menunggu',
                'proses': 'Pemeriksaan',
                'menunggu_bukti': 'Perlu Bukti',
                'bukti_dikirim': 'Bukti Dikirim',
                'dalam_mediasi': 'Mediasi Aktif',
                'mediasi_tidak_tercapai': 'Mediasi Tidak Tercapai',
                'menunggu_keputusan_admin': 'Mediasi Tidak Tercapai',
                'selesai': 'Selesai',
                'ditolak': 'Ditolak'
            };
            const classes = {
                'baru': 'bg-rose-500/20 text-rose-300 border border-rose-400/30',
                'proses': 'bg-amber-500/20 text-amber-300 border border-amber-400/30',
                'menunggu_bukti': 'bg-orange-500/20 text-orange-300 border border-orange-400/30',
                'bukti_dikirim': 'bg-sky-500/20 text-sky-300 border border-sky-400/30',
                'dalam_mediasi': 'bg-teal-500/20 text-teal-300 border border-teal-400/30',
                'mediasi_tidak_tercapai': 'bg-amber-500/20 text-amber-300 border border-amber-400/30',
                'menunggu_keputusan_admin': 'bg-amber-500/20 text-amber-300 border border-amber-400/30',
                'selesai': 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30',
                'ditolak': 'bg-slate-500/20 text-slate-300 border border-slate-400/30'
            };
            badge.className = `text-[8.5px] font-extrabold px-2 py-0.5 rounded-full ${classes[comp.status] || 'bg-slate-500/20 text-slate-300 border border-slate-400/30'}`;
            badge.innerText = labels[comp.status] || 'Diproses';
        }

        // Render Action Banner Dinamis untuk Mitra
        const actBanner = document.getElementById('detMitraActionBanner');
        if (actBanner) {
            if (comp.status === 'menunggu_bukti') {
                const instructions = comp.evidenceRequest?.instructions || 'Admin meminta bukti tambahan untuk klarifikasi pengerjaan.';
                const deadline = comp.evidenceRequest?.deadline || 'Segera';
                actBanner.className = 'p-3.5 rounded-2xl bg-amber-50 border border-amber-300 space-y-2 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <div class="space-y-0.5">
                        <strong class="text-amber-900 font-bold flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-file-circle-question text-amber-600"></i> Klarifikasi Bukti Diminta Admin
                        </strong>
                        <p class="text-[10px] text-amber-800 leading-relaxed">${instructions}</p>
                        <span class="text-[9px] text-amber-700 font-semibold block mt-1">Batas Waktu: ${deadline}</span>
                    </div>
                    <button
                        type="button"
                        onclick="openUploadBuktiMitraModal()"
                        class="w-full py-2 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-[10.5px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Unggah Bukti Tambahan Mitra</span>
                    </button>
                `;
            } else if (comp.status === 'bukti_dikirim') {
                actBanner.className = 'p-3 rounded-2xl bg-sky-50 border border-sky-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-sky-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-circle-check text-sky-600"></i> Bukti Tambahan Telah Dikirim
                    </strong>
                    <p class="text-[10px] text-sky-800 leading-relaxed">
                        Dokumen klarifikasi Anda telah masuk ke sistem dan sedang diteliti oleh Tim Mediasi Admin.
                    </p>
                `;
            } else if (comp.status === 'dalam_mediasi') {
                actBanner.className = 'p-3.5 rounded-2xl bg-teal-50 border border-teal-300 space-y-2 text-xs shadow-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <div class="space-y-0.5">
                        <strong class="text-teal-900 font-extrabold flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-handshake text-teal-600"></i> Sesi Mediasi 3-Pihak Sedang Berlangsung
                        </strong>
                        <p class="text-[10px] text-teal-800 leading-relaxed">
                            Admin SayaBantu telah membuka ruang mediasi. Sampaikan penjelasan objektif dan tinjau poin usulan penyelesaian bersama Customer.
                        </p>
                    </div>
                    <button
                        type="button"
                        onclick="openRuangMediasiMitraModal()"
                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-700 to-emerald-600 hover:from-teal-600 hover:to-emerald-500 text-white font-extrabold text-[11px] shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-comments text-xs"></i>
                        <span>Masuk ke Ruang Mediasi Khusus</span>
                    </button>
                `;
            } else if (comp.status === 'mediasi_tidak_tercapai' || comp.status === 'menunggu_keputusan_admin') {
                actBanner.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-amber-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-handshake text-amber-600"></i> Mediasi Tidak Mencapai Kesepakatan
                    </strong>
                    <p class="text-[10px] text-amber-800 leading-relaxed">
                        Admin telah mencatat hasil mediasi. Keputusan atas usulan tetap berada pada Customer dan Mitra.
                    </p>
                `;
            } else if (comp.status === 'selesai') {
                actBanner.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-emerald-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Kasus Telah Ditutup Resmi
                    </strong>
                    <p class="text-[10px] text-emerald-800 leading-relaxed">
                        Kesepakatan damai berhasil dicapai dan seluruh komitmen telah tuntas.
                    </p>
                    <button
                        type="button"
                        onclick="openRuangMediasiMitraModal()"
                        class="mt-1 text-[10px] font-bold text-teal-700 hover:text-teal-900 underline flex items-center gap-1"
                    >
                        <i class="fa-solid fa-clock-rotate-left"></i> Lihat Catatan Ruang Mediasi
                    </button>
                `;
            } else if (comp.status === 'ditolak') {
                actBanner.className = 'p-3 rounded-2xl bg-slate-100 border border-slate-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-slate-800 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-ban text-slate-500"></i> Pengaduan Telah Ditutup / Ditolak
                    </strong>
                    <p class="text-[10px] text-slate-600 leading-relaxed">
                        ${comp.rejectionReason || 'Laporan diselesaikan secara administratif oleh Admin.'}
                    </p>
                `;
            } else {
                actBanner.classList.add('hidden');
            }
        }

        const tlWrap = document.getElementById('detMitraTiketTimeline');
        if (tlWrap) {
            let tlHtml = '';
            const stages = comp.timeline && comp.timeline.length ? comp.timeline : [
                { stage: 'Pengaduan Tercatat', time: comp.date, desc: 'Laporan keluhan tercatat di sistem.' }
            ];
            stages.forEach((s, idx) => {
                const isLast = idx === stages.length - 1;
                tlHtml += `
                    <div class="relative">
                        <div class="absolute -left-[17px] top-1 w-2.5 h-2.5 rounded-full ${isLast ? 'bg-rose-500 ring-4 ring-rose-100' : 'bg-slate-400'}"></div>
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-900 block leading-tight">${s.stage}</span>
                            <span class="text-[8.5px] text-slate-400 font-mono block">${s.time}</span>
                            <p class="text-[9.5px] text-slate-600 leading-relaxed">${s.desc}</p>
                        </div>
                    </div>
                `;
            });
            tlWrap.innerHTML = tlHtml;
        }

        const decBox = document.getElementById('detMitraTiketAdminDecisionBox');
        const decText = document.getElementById('detMitraTiketAdminDecisionText');
        if (decBox && decText) {
            if (comp.adminNote && comp.adminNote.trim() !== '') {
                decBox.classList.remove('hidden');
                decText.innerText = comp.adminNote;
            } else {
                decBox.classList.add('hidden');
            }
        }

        const modal = document.getElementById('modalDetailPengaduanMitra');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeDetailPengaduanMitra() {
        const modal = document.getElementById('modalDetailPengaduanMitra');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // =========================================================
    // RUANG MEDIASI MITRA
    // =========================================================
    function openRuangMediasiMitraModal() {
        if (!currentMitraActiveComplaint) return;
        const comp = currentMitraActiveComplaint;

        document.getElementById('mitraRoomTitle').innerText = `Ruang Mediasi ${comp.id}`;
        document.getElementById('mitraRoomSub').innerText = `Suruhan: ${comp.orderId || '-'} • Kategori: ${comp.category}`;
        document.getElementById('mitraRoomCustLabel').innerText = comp.reporterRole === 'mitra' ? (comp.reportedName || 'Customer') : comp.reporterName;
        document.getElementById('mitraRoomSelfLabel').innerText = `Anda (${comp.reporterRole === 'mitra' ? comp.reporterName : (comp.reportedName || 'Mitra')})`;

        renderMitraMediationMessages();
        renderMitraProposalCard();

        const modal = document.getElementById('modalRuangMediasiMitra');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        setTimeout(() => {
            const sc = document.getElementById('mitraMediationScrollArea');
            if (sc) sc.scrollTop = sc.scrollHeight;
        }, 100);
    }
    window.openRuangMediasiMitraModal = openRuangMediasiMitraModal;

    function closeRuangMediasiMitraModal() {
        const modal = document.getElementById('modalRuangMediasiMitra');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeRuangMediasiMitraModal = closeRuangMediasiMitraModal;

    function renderMitraMediationMessages() {
        const comp = currentMitraActiveComplaint;
        const listEl = document.getElementById('mitraMediationMessagesList');
        if (!listEl || !comp) return;

        let messages = (comp.mediation && comp.mediation.messages) ? comp.mediation.messages : [];
        if (messages.length === 0) {
            const defaultDirections = comp.mediationDirections || 'Kedua pihak diminta menjelaskan kronologi secara objektif.';
            messages = [
                {
                    sender: 'Admin SayaBantu (Fasilitator)',
                    role: 'admin',
                    text: `Mediasi resmi dibuka untuk tiket ${comp.id}. Arahan: "${defaultDirections}"`,
                    time: comp.date || 'Hari ini'
                }
            ];
            if (!comp.mediation) comp.mediation = {};
            comp.mediation.messages = messages;
        }

        listEl.innerHTML = messages.map(m => {
            if (m.role === 'admin') {
                return `
                    <div class="p-2.5 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-slate-800 space-y-1 max-w-[94%] shadow-2xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-teal-800">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> ${m.sender}
                            </span>
                            <span class="text-slate-400 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-slate-700 leading-relaxed">${m.text}</p>
                    </div>
                `;
            } else if (m.role === 'mitra') {
                return `
                    <div class="ml-auto p-2.5 rounded-2xl bg-[#0E1D31] text-white space-y-1 max-w-[88%] shadow-xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-sky-200">
                            <span>Anda (Mitra)</span>
                            <span class="opacity-70 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-white leading-relaxed">${m.text}</p>
                    </div>
                `;
            } else {
                return `
                    <div class="mr-auto p-2.5 rounded-2xl bg-white border border-slate-200 text-slate-800 space-y-1 max-w-[88%] shadow-xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-rose-700">
                            <span>${m.sender || 'Customer'}</span>
                            <span class="text-slate-400 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-slate-700 leading-relaxed">${m.text}</p>
                    </div>
                `;
            }
        }).join('');
    }

    function renderMitraProposalCard() {
        const comp = currentMitraActiveComplaint;
        const card = document.getElementById('mitraProposalCard');
        if (!card || !comp) return;

        if (comp.status === 'dalam_mediasi' || (comp.mediation && comp.mediation.proposal)) {
            card.classList.remove('hidden');

            const proposal = (comp.mediation && comp.mediation.proposal) ? comp.mediation.proposal : {
                title: 'Usulan Penyelesaian Damai',
                terms: 'Mitra bersedia menyelesaikan pekerjaan dan mematuhi kesepakatan harga aplikasi tanpa penambahan biaya sepihak.',
                customerApproved: false,
                mitraApproved: false
            };

            document.getElementById('mitraProposalTermsText').innerText = proposal.terms;

            const custStatusEl = document.getElementById('mitraProposalCustStatus');
            const selfStatusEl = document.getElementById('mitraProposalSelfStatus');
            const badgeEl = document.getElementById('mitraProposalBadge');
            const btnWrap = document.getElementById('mitraProposalActionButtons');
            const notice = document.getElementById('mitraProposalAgreedNotice');

            if (proposal.customerApproved) {
                custStatusEl.innerText = 'Disetujui';
                custStatusEl.className = 'font-bold text-emerald-600';
            } else {
                custStatusEl.innerText = 'Menunggu';
                custStatusEl.className = 'font-bold text-slate-500';
            }

            if (proposal.mitraApproved) {
                selfStatusEl.innerText = 'Disetujui';
                selfStatusEl.className = 'font-bold text-emerald-600';
                if (btnWrap) btnWrap.classList.add('hidden');
                if (notice) notice.classList.remove('hidden');
            } else {
                selfStatusEl.innerText = 'Menunggu';
                selfStatusEl.className = 'font-bold text-amber-600';
                if (btnWrap) btnWrap.classList.remove('hidden');
                if (notice) notice.classList.add('hidden');
            }

            if (proposal.customerApproved && proposal.mitraApproved) {
                badgeEl.innerText = 'Kesepakatan Tercapai';
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
            } else {
                badgeEl.innerText = 'Menunggu Persetujuan';
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
            }
        } else {
            card.classList.add('hidden');
        }
    }

    function sendMitraMediationMessage() {
        const input = document.getElementById('mitraMediationInputText');
        const text = (input ? input.value : '').trim();
        if (!text || !currentMitraActiveComplaint) return;

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getMitraStoredComplaints();
        const found = list.find(c => c.id === currentMitraActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            if (!found.mediation.messages) found.mediation.messages = [];
            const currentMitraName = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala'));
            found.mediation.messages.push({
                sender: currentMitraName + ' (Mitra)',
                role: 'mitra',
                text: text,
                time: timeStr
            });
            saveMitraStoredComplaints(list);
            currentMitraActiveComplaint = found;
        }

        input.value = '';
        renderMitraMediationMessages();

        const sc = document.getElementById('mitraMediationScrollArea');
        if (sc) sc.scrollTop = sc.scrollHeight;
    }
    window.sendMitraMediationMessage = sendMitraMediationMessage;

    function mitraApproveProposal() {
        if (!currentMitraActiveComplaint) return;
        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getMitraStoredComplaints();
        const found = list.find(c => c.id === currentMitraActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            if (!found.mediation.proposal) {
                found.mediation.proposal = {
                    title: 'Usulan Kesepakatan Damai',
                    terms: 'Pekerjaan diselesaikan sesuai rincian awal tanpa pemungutan biaya tambahan sepihak.',
                    customerApproved: false,
                    mitraApproved: false
                };
            }
            found.mediation.proposal.mitraApproved = true;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Mitra Menyetujui Usulan Mufakat',
                time: timeStr,
                desc: 'Mitra telah menyetujui poin usulan penyelesaian damai yang difasilitasi Admin.'
            });

            const currentMitraName = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala'));
            found.mediation.messages = found.mediation.messages || [];
            found.mediation.messages.push({
                sender: currentMitraName + ' (Mitra)',
                role: 'mitra',
                text: 'Saya dari pihak Mitra menyetujui poin usulan kesepakatan damai ini.',
                time: now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            });

            saveMitraStoredComplaints(list);
            currentMitraActiveComplaint = found;
        }

        renderMitraProposalCard();
        renderMitraMediationMessages();
        renderMitraComplaints();

        showMitraToast('Persetujuan Disimpan', 'Persetujuan kesepakatan mediasi berhasil disimpan.', 'success');
    }
    window.mitraApproveProposal = mitraApproveProposal;

    function mitraRequestProposalChange() {
        const note = prompt('Tuliskan poin usulan yang ingin disesuaikan dari pihak Mitra:');
        if (!note || !currentMitraActiveComplaint) return;

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getMitraStoredComplaints();
        const found = list.find(c => c.id === currentMitraActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            const currentMitraName = (typeof getCurrentMitraProfile === 'function' ? getCurrentMitraProfile().name : (window.CURRENT_MITRA_DATA?.name || 'Kala'));
            found.mediation.messages = found.mediation.messages || [];
            found.mediation.messages.push({
                sender: currentMitraName + ' (Mitra)',
                role: 'mitra',
                text: `[Permintaan Penyesuaian Usulan Mitra]: "${note}"`,
                time: timeStr
            });
            saveMitraStoredComplaints(list);
            currentMitraActiveComplaint = found;
        }

        renderMitraMediationMessages();
        const sc = document.getElementById('mitraMediationScrollArea');
        if (sc) sc.scrollTop = sc.scrollHeight;
    }
    window.mitraRequestProposalChange = mitraRequestProposalChange;

    // =========================================================
    // UPLOAD BUKTI TAMBAHAN MITRA
    // =========================================================
    function openUploadBuktiMitraModal() {
        if (!currentMitraActiveComplaint) return;
        const comp = currentMitraActiveComplaint;

        const instText = comp.evidenceRequest?.instructions || 'Harap sertakan bukti klarifikasi pekerjaan.';
        const deadText = comp.evidenceRequest?.deadline || '24 jam kerja';

        document.getElementById('mitraReqEvidenceInstructionsText').innerText = instText;
        document.getElementById('mitraReqEvidenceDeadlineText').innerText = `Batas Waktu: ${deadText}`;

        document.getElementById('formUploadBuktiMitra').reset();
        document.getElementById('phBuktiTambahanMitra').classList.remove('hidden');
        document.getElementById('actBuktiTambahanMitra').classList.add('hidden');

        const modal = document.getElementById('modalUploadBuktiMitra');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.openUploadBuktiMitraModal = openUploadBuktiMitraModal;

    function closeUploadBuktiMitraModal() {
        const modal = document.getElementById('modalUploadBuktiMitra');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeUploadBuktiMitraModal = closeUploadBuktiMitraModal;

    function previewBuktiTambahanMitra(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('nameBuktiTambahanMitra').innerText = file.name;
            document.getElementById('phBuktiTambahanMitra').classList.add('hidden');
            document.getElementById('actBuktiTambahanMitra').classList.remove('hidden');
            document.getElementById('actBuktiTambahanMitra').classList.add('flex');
        }
    }
    window.previewBuktiTambahanMitra = previewBuktiTambahanMitra;

    function submitUploadBuktiMitra(e) {
        e.preventDefault();
        if (!currentMitraActiveComplaint) return;

        const fileInput = document.getElementById('inputBuktiTambahanMitraFile');
        const fileName = (fileInput.files && fileInput.files[0]) ? fileInput.files[0].name : 'bukti_mitra.jpg';
        const notes = document.getElementById('inputCatatanBuktiTambahanMitra').value.trim();

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getMitraStoredComplaints();
        const found = list.find(c => c.id === currentMitraActiveComplaint.id);
        if (found) {
            found.status = 'bukti_dikirim';
            found.evidence = `${found.evidence || ''} | Mitra: ${fileName} ("${notes}")`;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Bukti Klarifikasi Diunggah oleh Mitra',
                time: timeStr,
                desc: `Mitra melampirkan berkas [${fileName}]. Keterangan: "${notes}"`
            });
            saveMitraStoredComplaints(list);
            currentMitraActiveComplaint = found;
        }

        closeUploadBuktiMitraModal();
        openDetailPengaduanMitra(currentMitraActiveComplaint.id);
        renderMitraComplaints();

        showMitraToast('Bukti Terkirim', 'Bukti klarifikasi Mitra berhasil dikirimkan ke Admin SayaBantu.', 'success');
    }
    window.submitUploadBuktiMitra = submitUploadBuktiMitra;

    function syncAllMitraProfileElements() {
        const m = (typeof getCurrentMitraProfile === 'function') ? getCurrentMitraProfile() : {
            id: window.CURRENT_MITRA_DATA?.id || '',
            name: window.CURRENT_MITRA_DATA?.name || 'Mitra',
            phone: window.CURRENT_MITRA_DATA?.phone || '',
            email: window.CURRENT_MITRA_DATA?.email || '',
            rating: window.CURRENT_MITRA_DATA?.rating ?? null,
            tugas: String(window.CURRENT_MITRA_DATA?.tasks ?? '0'),
            spesialis: window.CURRENT_MITRA_DATA?.specialty || 'Belum ada layanan',
            area: window.CURRENT_MITRA_DATA?.area || 'Wilayah belum diatur'
        };
        const initial = (m.name || 'K').trim().charAt(0).toUpperCase();

        const idsToUpdate = {
            'dashMitraName': m.name,
            'dashMitraIdBadge': m.id,
            'headerMitraAvatarInitial': initial,
            'quickMitraAvatarInitial': initial,
            'quickMitraName': m.name,
            'quickMitraIdBadge': 'SayaBantu ' + m.id,
            'quickMitraFullName': m.name,
            'quickMitraPhone': m.phone,
            'quickMitraEmail': m.email || (m.name.toLowerCase().replace(/[^a-z0-9]/g, '') + '.mitra@sayabantu.com'),
            'drawerMitraAvatarInitial': initial,
            'drawerMitraName': m.name,
            'drawerMitraIdBadge': m.id + ' • SayaBantu',
            'tabMitraAvatarInitial': initial,
            'tabMitraName': m.name,
            'tabMitraId': 'SayaBantu ' + m.id,
            'tabMitraPhone': m.phone,
            'tabMitraEmail': m.email || (m.name.toLowerCase().replace(/[^a-z0-9]/g, '') + '.mitra@sayabantu.com'),
        };

        for (const [id, val] of Object.entries(idsToUpdate)) {
            const el = document.getElementById(id);
            if (el) el.innerText = val;
        }

        const bioEl = document.getElementById('tabMitraBio');
        if (bioEl) {
            bioEl.innerText = `Halo! Saya ${m.name}, mitra profesional SayaBantu yang berdedikasi tinggi. Selalu mengutamakan kejujuran, ketepatan waktu, dan kerapian kerja. Siap melayani pembersihan rumah, perbaikan perabot, dan kebutuhan suruhan harian Anda.`;
        }

        const optBCA = document.getElementById('optBankBCA');
        if (optBCA) optBCA.innerText = `BCA - 1234567890 (${m.name})`;
        const optMandiri = document.getElementById('optBankMandiri');
        if (optMandiri) optMandiri.innerText = `Bank Mandiri - 9876543210 (${m.name})`;
        const optBRI = document.getElementById('optBankBRI');
        if (optBRI) optBRI.innerText = `BRI - 5544332211 (${m.name})`;
        const optGoPay = document.getElementById('optEwalletGoPay');
        if (optGoPay) optGoPay.innerText = `GoPay - ${m.phone}`;
        const optDANA = document.getElementById('optEwalletDANA');
        if (optDANA) optDANA.innerText = `DANA - ${m.phone}`;
    }
    window.syncAllMitraProfileElements = syncAllMitraProfileElements;

    document.addEventListener('DOMContentLoaded', function() {
        syncAllMitraProfileElements();

        loadMitraCustomerOrders();
        setInterval(() => loadMitraCustomerOrders(false), 2500);
        renderMitraComplaints();
        if (window.location.hash === '#pengaduan') {
            switchMitraNav('pengaduan');
        }
    });
</script>
@endpush

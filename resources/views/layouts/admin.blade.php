<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - SayaBantu.com')</title>

    {{-- Google Font Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Admin Unified Components CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin-components.css') }}">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0E1D31',
                        navySoft: '#17314A',
                        page: '#F7F9FC',
                    }
                }
            }
        }
    </script>

    <style>
        /* =====================================================
           GLOBAL RESET & MOBILE FRAME
        ====================================================== */
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        html.sb-scroll-locked,
        body.sb-scroll-locked {
            overflow: hidden !important;
            overscroll-behavior: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #CBD5E1;
            color: #111827;
        }

        /* Safety layer: Pastikan modal & overlay tertutup tidak pernah membekukan klik halaman */
        .sb-overlay-backdrop {
            pointer-events: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        .sb-overlay-backdrop.pointer-events-auto,
        .sb-overlay-backdrop.active-modal {
            pointer-events: auto !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .pointer-events-none { pointer-events: none !important; }
        .pointer-events-auto { pointer-events: auto !important; }
        .invisible { visibility: hidden !important; }
        .visible { visibility: visible !important; }
        .opacity-0 { opacity: 0 !important; }
        .opacity-100 { opacity: 1 !important; }

        a { text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }
        ::-webkit-scrollbar { width: 0; height: 0; }

        .mobile-app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            position: relative;
            overflow-x: hidden;
            background-color: #F7F9FC;
            background-image:
                radial-gradient(circle at 92% 4%, rgba(255, 255, 255, 0.10), transparent 27%),
                radial-gradient(circle at 0% 42%, rgba(75, 110, 140, 0.20), transparent 32%),
                radial-gradient(circle at 100% 58%, rgba(112, 143, 164, 0.15), transparent 30%),
                linear-gradient(
                    180deg,
                    #0E1D31 0%,
                    #0E1D31 24%,
                    #11263D 38%,
                    #17334C 52%,
                    #294D65 65%,
                    #5F7C8E 77%,
                    #A7BAC6 87%,
                    #D9E3E9 94%,
                    #EDF2F6 97%,
                    #F7F9FC 100%
                );
            background-size: 100% 15cm;
            background-repeat: no-repeat;
            box-shadow: 0 0 50px rgba(15, 23, 42, 0.20);
            padding-bottom: 76px;
        }

        /* =====================================================
           STANDARISASI TOP AREA SAYABANTU
           Mengalir natural di atas gradien 15cm mobile-app
        ====================================================== */
        .admin-top-area,
        .pesanan-top-area,
        .pembayaran-top-area,
        .profile-top-area,
        .customer-top-area,
        .sb-admin-gradient {
            position: relative;
            overflow: visible;
            color: white;
            padding-bottom: 0;
            background: transparent;
        }

        .admin-top-area > * {
            position: relative;
            z-index: 1;
        }

        /* Dekorasi Lingkaran Header */
        .header-circle-one {
            position: absolute;
            width: 190px;
            height: 190px;
            right: -105px;
            top: -120px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.045);
            pointer-events: none;
        }

        .header-circle-two {
            position: absolute;
            width: 135px;
            height: 135px;
            left: -80px;
            top: 120px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.025);
            pointer-events: none;
        }

        /* Header Elemen */
        .header-content {
            position: relative;
            z-index: 10;
        }

        .header-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            color: rgba(255, 255, 255, 0.92);
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(8px);
            transition: all 0.18s ease;
        }

        .header-icon:hover {
            background: rgba(255, 255, 255, 0.20);
        }

        .header-icon:active {
            transform: scale(0.92);
        }

        .profile-admin {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            color: white;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            transition: all 0.18s ease;
        }

        .profile-admin:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .profile-admin:active {
            transform: scale(0.92);
        }

        .notification-dot {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #FBBF24;
            border: 2px solid #0E1D31;
            box-shadow: 0 0 8px rgba(251, 191, 36, 0.7);
        }

        /* =====================================================
           UNIFIED CONTENT WRAPPER
        ====================================================== */
        .admin-content-area {
            position: relative;
            z-index: 20;
            padding: 0 20px 96px;
            margin-top: 0;
        }

        /* Stat cards di dashboard */
        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 11px;
            min-height: 125px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 16px rgba(15, 23, 42, 0.07);
            transition: all 0.18s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.10);
        }

        .stat-card:active {
            transform: scale(0.985);
        }

        .stat-decoration {
            position: absolute;
            right: -22px;
            top: -22px;
            width: 72px;
            height: 72px;
            border-radius: 9999px;
            opacity: 0.75;
        }

        .icon-box {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .icon-blue { color: #2563EB; background: #EEF5FF; }
        .icon-green { color: #16A34A; background: #ECFDF3; }
        .icon-amber { color: #D97706; background: #FFF7DB; }
        .icon-red { color: #DC2626; background: #FEF2F2; }

        .admin-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.045);
        }

        .pesanan-card {
            background: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, 0.065);
            overflow: hidden;
            transition: all 0.18s ease;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 8px;
            font-weight: 600;
        }

        .status-success { color: #15803D; background: #ECFDF3; }
        .status-warning { color: #B45309; background: #FFF7DB; }
        .status-danger { color: #B91C1C; background: #FEF2F2; }

        /* =====================================================
           BOTTOM NAVIGATION
        ====================================================== */
        .bottom-navigation {
            position: fixed;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            z-index: 40;
            padding: 6px 10px 8px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-top: 1px solid #E5E7EB;
            box-shadow: 0 -5px 22px rgba(15, 23, 42, 0.055);
        }

        .bottom-navigation-inner {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }

        .bottom-nav-item {
            min-height: 50px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border-radius: 15px;
            color: #9CA3AF;
            transition: all 0.18s ease;
        }

        .bottom-nav-item i { font-size: 15px; }
        .bottom-nav-item span { font-size: 9px; line-height: 1; font-weight: 500; }
        .bottom-nav-item:hover { color: #6B7280; }
        .bottom-nav-item:active { transform: scale(0.94); }

        .bottom-nav-item.nav-active {
            color: #173B67;
            background: #EFF6FF;
        }

        .bottom-nav-item.nav-active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 22px;
            height: 3px;
            border-radius: 0 0 6px 6px;
            background: #173B67;
        }
    </style>

    @stack('styles')

    {{-- AUTO-PURGE SCRIPT TO ENSURE CLEAN ZERO START --}}
    <script>
        (function autoCleanDummyData() {
            try {
                const CLEAN_KEY = "sb_zero_reset_v8";
                if (!localStorage.getItem(CLEAN_KEY)) {
                    localStorage.removeItem("sb_customer_orders");
                    localStorage.removeItem("sb_active_order");
                    localStorage.removeItem("sb_mitra_orders");
                    localStorage.removeItem("sb_mitra_bids");
                    localStorage.removeItem("sb_complaints");
                    localStorage.removeItem("sb_customer_pembayaran_selesai");
                    localStorage.setItem(CLEAN_KEY, "true");
                }
            } catch(e) {}
        })();
    </script>

    {{-- =========================================================
         INSTANT SPA NAVIGATION ENGINE ADMIN (ZERO LOADING & ZERO RELOAD)
         - Bebas reload & tanpa loading spinner browser
         - Hanya #sbAdminContent yang diganti, header/drawer/modal/bottom nav
           tetap hidup sehingga tidak ada kedip saat pindah fitur
         - In-memory DOM cache untuk transisi instan
         - Prefetch seluruh fitur admin di latar belakang
         Implementasi ada di public/js/admin-spa.js
    ========================================================== --}}
    <script src="{{ asset('js/admin-spa.js') }}" data-layout-script="true"></script>

</head>

<body>

<div class="mobile-app">

    {{-- 1. UNIFIED GRADIENT TOP AREA (SAMA RATA DI SEMUA HALAMAN) --}}
    <div class="admin-top-area">
        <div class="header-circle-one"></div>
        <div class="header-circle-two"></div>

        {{-- Modular Uniform Admin Header: Burger | SayaBantu.com | Bell | Avatar --}}
        @include('layouts.partials.admin-header')

        {{-- Mulai area yang diganti saat pindah fitur (tanpa reload) --}}
        <div id="sbAdminContent">

        {{-- Sub-header: Tombol Kembali di bawah burger menu, Judul & Sub-judul --}}
        @php
            $backUrl = trim($__env->yieldContent('back-url'));
            $backText = trim($__env->yieldContent('back-text')) ?: 'Kembali ke Dashboard';
            $pageTitle = trim($__env->yieldContent('page-title'));
            $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
            $headerGapStyle = trim($__env->yieldContent('header-gap-style')) ?: '0.6cm';
        @endphp

        @if($backUrl !== '' || $pageTitle !== '')
            <div class="px-5 pb-1 relative z-10" style="padding-top: {{ $headerGapStyle }};">
                @if($backUrl !== '' && !str_contains($backUrl, 'dashboard') && !str_contains(strtolower($backText), 'dashboard') && !str_contains(strtolower($backText), 'beranda'))
                    <a
                        href="{{ $backUrl }}"
                        onclick="handleAdminBackNavigation(event, '{{ $backUrl }}')"
                        class="inline-flex items-center gap-1.5 text-[10px] font-medium text-white/75 hover:text-white transition-colors mb-2 cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-left text-[9px]"></i>
                        <span>{!! $backText !!}</span>
                    </a>
                @endif

                @if($pageTitle !== '')
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 pr-1">
                            <h2 class="text-[20px] font-bold tracking-tight text-white leading-tight">
                                {!! $pageTitle !!}
                            </h2>
                            @if($pageSubtitle !== '')
                                <p class="text-[10px] text-white/70 mt-0.5 leading-snug">
                                    {!! $pageSubtitle !!}
                                </p>
                            @endif
                        </div>
                        @yield('page-badge')
                    </div>
                @endif
            </div>
        @endif

        @yield('hero')
        @yield('page-hero')
        @yield('page-top')

    {{-- 2. UNIFIED CONTENT AREA (SATU WRAPPER UTAMA) --}}
    <main class="admin-content-area">
        @yield('content')
    </main>

        </div>{{-- /#sbAdminContent --}}
    </div>

</div>

{{-- Navigasi Bawah (Persis 4 Menu: Dashboard, Pengguna, Suruhan, Pembayaran) --}}
@if(trim($__env->yieldContent('hide-bottom-nav')) !== 'true')
<nav class="bottom-navigation">
    <div class="bottom-navigation-inner">
        <a href="{{ route('admin.dashboard') }}" onclick="event.preventDefault(); if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.dashboard') }}'); } else { window.location.href = '{{ route('admin.dashboard') }}'; }" class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}" title="Dashboard Admin">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.pengguna') }}" onclick="event.preventDefault(); if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.pengguna') }}'); } else { window.location.href = '{{ route('admin.pengguna') }}'; }" class="bottom-nav-item {{ (request()->routeIs('admin.pengguna*') || request()->routeIs('admin.mitra*')) ? 'nav-active' : '' }}" title="Kelola Pengguna">
            <i class="fa-solid fa-users"></i>
            <span>Pengguna</span>
        </a>

        <a href="{{ route('admin.pesanan') }}" onclick="event.preventDefault(); if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.pesanan') }}'); } else { window.location.href = '{{ route('admin.pesanan') }}'; }" class="bottom-nav-item {{ (request()->routeIs('admin.pesanan*') || request()->routeIs('admin.suruhan*')) ? 'nav-active' : '' }}" title="Suruhan Layanan">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>Suruhan</span>
        </a>

        <a href="{{ route('admin.pembayaran') }}" onclick="event.preventDefault(); if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.pembayaran') }}'); } else { window.location.href = '{{ route('admin.pembayaran') }}'; }" class="bottom-nav-item {{ request()->routeIs('admin.pembayaran*') ? 'nav-active' : '' }}" title="Data Pembayaran">
            <i class="fa-solid fa-credit-card"></i>
            <span>Pembayaran</span>
        </a>
    </div>
</nav>
@endif

{{-- MODALS & INTERACTIVE OVERLAYS --}}
@include('layouts.partials.admin-header-modals')
<div id="adminDynamicModals">
    @yield('modals')
    @stack('modals')
</div>

{{-- REALTIME CLIENT SCRIPT --}}
<script src="{{ asset('js/admin-realtime.js') }}"></script>

@stack('scripts')

</body>
</html>

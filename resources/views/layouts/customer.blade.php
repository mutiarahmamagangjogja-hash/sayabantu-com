<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'SayaBantu.com | Layanan Suruhan & Jasa Terpercaya')</title>
    
    <meta name="description" content="SayaBantu.com - Solusi Jasa, Tukang, dan Suruhan Terpercaya. Suruh apa aja siap melayani sepenuh hati 24 jam.">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- FONT INTER (PERSIS ADMIN) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- KOMPONEN MODAL BERSAMA (dipakai juga oleh panel admin) --}}
    <link rel="stylesheet" href="{{ asset('css/admin-components.css') }}">

    {{-- TAILWIND --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0E1D31',
                        navySoft: '#17314A',
                        primary: '#173B67',
                        page: '#F7F9FC',
                    }
                }
            }
        }
    </script>

    <style>
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
            background: #E5E7EB;
            color: #111827;
        }

        a {
            text-decoration: none;
        }

        button {
            font-family: inherit;
            cursor: pointer;
        }

        ::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        /* =====================================================
           MOBILE APP FRAME (PERSIS SEPERTI HALAMAN ADMIN)
        ====================================================== */
        .mobile-app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            position: relative;
            overflow-x: hidden;
            background: #F7F9FC;
            box-shadow: 0 0 40px rgba(15,23,42,0.14);
            padding-bottom: 92px;
        }

        /* =====================================================
           CUSTOMER TOP AREA (GRADIENT TURUN SAMPAI SETENGAH KARTU KATALOG)
           - Gradient ditulis langsung sebagai background.
           - Area ini diberi padding bawah ekstra (+120px) supaya
             ekor gradient turun melewati judul & setengah bagian
             atas kartu katalog layanan.
           - Konten di atasnya ditarik naik dengan margin negatif
             agar tinggi layout tidak ikut membengkak.
        ====================================================== */
        .customer-top-area,
        .mitra-top-area {
            position: relative;
            overflow: visible;
            color: white;
            min-height: 15cm;
            height: 15cm;
            padding-bottom: 0;
            background:
                radial-gradient(circle at 92% 4%, rgba(255,255,255,0.10), transparent 27%),
                radial-gradient(circle at 0% 42%, rgba(75,110,140,0.20), transparent 32%),
                radial-gradient(circle at 100% 58%, rgba(112,143,164,0.15), transparent 30%),
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
        }

        /* Pastikan isi header tetap di atas lapisan gradient */
        .customer-top-area > *,
        .mitra-top-area > * {
            position: relative;
            z-index: 1;
        }

        .header-circle-one {
            position: absolute;
            width: 190px;
            height: 190px;
            right: -105px;
            top: -120px;
            border-radius: 9999px;
            background: rgba(255,255,255,0.045);
            pointer-events: none;
        }

        .header-circle-two {
            position: absolute;
            width: 135px;
            height: 135px;
            left: -80px;
            top: 120px;
            border-radius: 9999px;
            background: rgba(255,255,255,0.025);
            pointer-events: none;
        }

        .header-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            color: rgba(255,255,255,0.92);
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.10);
            transition: all 0.18s ease;
        }

        .header-icon:hover {
            background: rgba(255,255,255,0.16);
        }

        .header-icon.hidden {
            display: none !important;
        }

        .notification-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #EF4444;
            box-shadow: 0 0 0 2px #0E1D31;
        }

        /* =====================================================
           BOTTOM NAVIGATION (PERSIS SEPERTI HALAMAN ADMIN)
        ====================================================== */
        .bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 430px;
            z-index: 40;
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226,232,240,0.9);
            box-shadow: 0 -4px 20px rgba(15,23,42,0.06);
        }

        .bottom-navigation-inner {
            display: flex;
            align-items: center;
            justify-content: space-around;
            height: 60px;
            padding: 0 8px;
        }

        .bottom-nav-item {
            flex: 1;
            min-height: 50px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            margin: 0 2px;
            border-radius: 15px;
            color: #9CA3AF;
            font-size: 9.5px;
            font-weight: 500;
            transition: all 0.18s ease;
        }

        .bottom-nav-item i {
            font-size: 15px;
        }

        .bottom-nav-item:hover { color: #6B7280; }
        .bottom-nav-item:active { transform: scale(0.94); }

        .bottom-nav-item.nav-active {
            color: #173B67;
            font-weight: 700;
            background: #EFF6FF;
        }

        .bottom-nav-item.nav-active i {
            color: #173B67;
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

        /* Floating Center Suruh Button */
        .btn-center-suruh {
            width: 42px;
            height: 42px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #173B67, #0E1D31);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-top: -18px;
            box-shadow: 0 6px 16px rgba(23,59,103,0.35);
            border: 3px solid #FFFFFF;
            transition: all 0.2s ease;
        }

        .btn-center-suruh:hover {
            transform: scale(1.05);
        }

        /* Card styles */
        .card-sb {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(15,23,42,0.03);
        }

        /* Konten utama ditarik naik menutupi bagian bawah header yang ber-gradient.
           Nilai margin-top di-set otomatis oleh JS (syncGradientEkor),
           supaya kartu pertama selalu rapat ke header tanpa ruang kosong. */
        .content-under-gradient {
            margin-top: -32px;
            position: relative;
            z-index: 20;
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

    <script>
        window.getCustomerAuth = function() {
            try {
                const raw = localStorage.getItem('sb_customer_auth');
                if (raw) return JSON.parse(raw);
            } catch(e) {}
            return null;
        };
        window.setCustomerAuth = function(user) {
            try {
                if (!user) {
                    localStorage.removeItem('sb_customer_auth');
                } else {
                    localStorage.setItem('sb_customer_auth', JSON.stringify(user));
                }
            } catch(e) {}
        };
        window.openModernServiceDetail = function(serviceName) {
            const name = (serviceName || '').trim();
            const select = document.getElementById('custFormLayanan');
            if (select) {
                let found = false;
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].text.toLowerCase().includes(name.toLowerCase()) ||
                        select.options[i].value.toLowerCase().includes(name.toLowerCase())) {
                        select.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found && name) {
                    select.value = name;
                }
                select.dispatchEvent(new Event('change'));
                const formSection = document.getElementById('customerOrderFormSection') || select.closest('section') || select;
                formSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (typeof showCustomerToast === 'function') {
                    showCustomerToast('Layanan Dipilih', `Layanan ${name} telah dipilih pada formulir di bawah.`, 'info');
                }
                return;
            }
            const target = `/layanan?q=${encodeURIComponent(name)}`;
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        };

        let custToastTimeout = null;
        window.showCustomerToast = function(title, msg, type = 'info') {
            let toast = document.getElementById('custGlobalToast');
            if (!toast) {
                const wrap = document.createElement('div');
                wrap.id = 'custGlobalToast';
                wrap.className = 'fixed top-5 left-1/2 -translate-x-1/2 max-w-[360px] w-full px-4 z-[9999] pointer-events-none transition-all duration-300 opacity-0 -translate-y-4';
                wrap.innerHTML = `
                    <div class="bg-slate-900/95 text-white backdrop-blur-md rounded-2xl p-3 shadow-xl border border-white/20 flex items-center gap-3">
                        <div id="custToastIcon" class="w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs">
                            <i class="fa-solid fa-info"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 id="custToastTitle" class="text-xs font-bold leading-tight truncate">Info</h5>
                            <p id="custToastMessage" class="text-[11px] text-slate-300 leading-snug mt-0.5 truncate">Pesan informasi</p>
                        </div>
                    </div>
                `;
                document.body.appendChild(wrap);
                toast = wrap;
            }
            const titleEl = document.getElementById('custToastTitle');
            const msgEl = document.getElementById('custToastMessage');
            const iconEl = document.getElementById('custToastIcon');

            if (titleEl) titleEl.innerText = title;
            if (msgEl) msgEl.innerText = msg;

            if (iconEl) {
                if (type === 'success') {
                    iconEl.className = 'w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 text-xs';
                    iconEl.innerHTML = '<i class="fa-solid fa-check"></i>';
                } else if (type === 'warning') {
                    iconEl.className = 'w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-xs';
                    iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                } else if (type === 'error' || type === 'danger') {
                    iconEl.className = 'w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 text-xs';
                    iconEl.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                } else {
                    iconEl.className = 'w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs';
                    iconEl.innerHTML = '<i class="fa-solid fa-info"></i>';
                }
            }

            toast.classList.remove('opacity-0', '-translate-y-4');
            toast.classList.add('opacity-100', 'translate-y-0');

            if (custToastTimeout) clearTimeout(custToastTimeout);
            custToastTimeout = setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', '-translate-y-4');
            }, 3000);
        };

        window.logoutUserAction = function() {
            localStorage.setItem('sb_customer_logged_out', 'true');
            localStorage.removeItem('sb_customer_auth');
            localStorage.removeItem('sb_active_order');
            window.location.href = "{{ route('customer.logout') }}";
        };
    </script>

    @if(Auth::check())
    <script>
        try {
            localStorage.removeItem('sb_customer_logged_out');
            const serverAuth = {
                isLoggedIn: true,
                id: @json(Auth::id()),
                name: @json(Auth::user()->name),
                phone: @json(Auth::user()->phone),
                email: @json(Auth::user()->email),
                role: @json(Auth::user()->role ?? 'customer')
            };
            localStorage.setItem('sb_customer_auth', JSON.stringify(serverAuth));
        } catch(e) {}
    </script>
    @endif

</head>
<body @if(request()->routeIs('customer.index') || request()->is('pengunjung*') || ($isPengunjung ?? false)) data-is-pengunjung="true" @endif>

<div class="mobile-app">

    {{-- TOP AREA GRADIENT --}}
    @yield('page-top')

    {{-- MAIN CONTENT --}}
    @yield('content')

</div>

{{-- MODAL KHUSUS HALAMAN (mis. modal ulasan/nilai mitra) --}}
<div id="customerDynamicModals">
    @yield('modals')
    @stack('modals')
</div>

{{-- =====================================================
{{-- =====================================================
     BOTTOM NAVIGATION (GUEST / PENGUNJUNG VS CUSTOMER LOGIN)
====================================================== --}}
<nav class="bottom-navigation">
    {{-- 1. PENGUNJUNG (GUEST): BERANDA, LAYANAN, MITRA, TENTANG --}}
    <div class="bottom-navigation-inner" id="bottomNavGuest">
        <a href="{{ route('customer.index') }}" id="guestNavDashboard" class="bottom-nav-item {{ request()->routeIs('customer.home') || request()->routeIs('customer.index') ? 'nav-active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>

        <a href="{{ route('customer.layanan') }}" id="guestNavLayanan" class="bottom-nav-item {{ request()->routeIs('customer.layanan') || request()->routeIs('customer.service') ? 'nav-active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Layanan</span>
        </a>

        <a href="{{ route('customer.mitra') }}" id="guestNavMitra" class="bottom-nav-item {{ request()->routeIs('customer.mitra') ? 'nav-active' : '' }}">
            <i class="fa-solid fa-users"></i>
            <span>Mitra</span>
        </a>

        <a href="{{ route('customer.tentang') }}" id="guestNavTentang" class="bottom-nav-item {{ request()->routeIs('customer.tentang') ? 'nav-active' : '' }}">
            <i class="fa-solid fa-circle-info"></i>
            <span class="text-[8.5px] leading-tight text-center">Tentang SayaBantu</span>
        </a>
    </div>

    {{-- 2. CUSTOMER YANG SUDAH LOGIN (DASHBOARD): BERANDA, LAYANAN, PESANAN, PROFILE --}}
    <div class="bottom-navigation-inner hidden" id="bottomNavCustomer">
        <button type="button" onclick="switchCustomerNavTab('dashboard')" id="custNavDashboard" class="bottom-nav-item {{ request()->routeIs('customer.home') || request()->routeIs('customer.index') || (request()->routeIs('customer.dashboard') && (!request()->has('tab') || request('tab') == 'dashboard')) ? 'nav-active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </button>

        <button type="button" onclick="switchCustomerNavTab('layanan')" id="custNavLayanan" class="bottom-nav-item {{ request()->routeIs('customer.layanan') || request()->routeIs('customer.service') || (request()->routeIs('customer.dashboard') && request('tab') == 'layanan') ? 'nav-active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Layanan</span>
        </button>

        <button type="button" onclick="switchCustomerNavTab('pesan')" id="custNavPesan" class="bottom-nav-item relative {{ request()->routeIs('customer.dashboard') && request('tab') == 'pesan' ? 'nav-active' : '' }}">
            <i class="fa-regular fa-comment-dots"></i>
            <span class="absolute top-1.5 right-3.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span class="absolute top-1.5 right-3.5 w-2 h-2 rounded-full bg-rose-500"></span>
            <span>Pesan</span>
        </button>

        <button type="button" onclick="switchCustomerNavTab('pesanan')" id="custNavPesanan" class="bottom-nav-item relative {{ request()->routeIs('customer.dashboard') && request('tab') == 'pesanan' ? 'nav-active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span class="absolute top-1.5 right-3.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span class="absolute top-1.5 right-3.5 w-2 h-2 rounded-full bg-rose-500"></span>
            <span>Pesanan</span>
        </button>

        <button type="button" onclick="switchCustomerNavTab('profile')" id="custNavProfile" class="bottom-nav-item {{ request()->routeIs('customer.dashboard') && request('tab') == 'profile' ? 'nav-active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </button>
    </div>
</nav>

{{-- BURGER DRAWER MENU --}}
@include('customer.partials.customer-drawer')

{{-- MODALS & POPUPS (LOGIN MODAL, ORDER MODAL, TOAST) --}}
@include('customer.partials.customer-modals')

<script data-layout-script="true">
    // =========================================================
    //  JARAK GRADIENT OTOMATIS (semua halaman customer)
    //  - Standarisasi seragam seluruh platform: batas gradient 15cm
    //  - Kartu pertama selalu rapat rapi ke konten header (gap 12px)
    // =========================================================
    function syncGradientEkor() {
        const top = document.querySelector('.customer-top-area');
        const content = document.querySelector('.content-under-gradient');
        if (!top || !content) return;

        const targetHeightPx = Math.round(15 * 37.795); // ~567px

        top.style.height = targetHeightPx + 'px';
        top.style.minHeight = targetHeightPx + 'px';
        top.style.paddingBottom = '0px';

        const kids = [...top.children].filter(el => {
            const c = el.className || '';
            return typeof c === 'string' && !c.includes('header-circle');
        });

        const topRect = top.getBoundingClientRect();
        let lastKidBottom = topRect.top;
        kids.forEach(k => {
            const rect = k.getBoundingClientRect();
            if (rect.bottom > lastKidBottom) lastKidBottom = rect.bottom;
        });

        const headerContentHeight = Math.max(0, lastKidBottom - topRect.top);
        const desiredContentTop = headerContentHeight + 12;
        const pullUp = targetHeightPx - desiredContentTop;

        content.style.marginTop = '-' + Math.max(0, pullUp) + 'px';
    }

    // =========================================================
    //  INSTANT SPA NAVIGATION ENGINE (ZERO LOADING & ZERO DELAY)
    //  - Tidak ada putaran browser tab (no browser spinner)
    //  - Konten berganti seketika (0ms) dari in-memory DOM cache
    //  - URL di address bar tetap sinkron persis sesuai halaman
    // =========================================================
    const sbDomCache = new Map();

    function toCustomerLocalUrl(url) {
        if (!url || typeof url !== 'string') return null;
        try {
            const u = new URL(url, window.location.origin);
            const appHosts = ['127.0.0.1', 'localhost', window.location.hostname];
            if (appHosts.includes(u.hostname) || u.origin === window.location.origin) {
                u.protocol = window.location.protocol;
                u.host = window.location.host;
            }
            return u;
        } catch(e) {
            return null;
        }
    }
    window.toCustomerLocalUrl = toCustomerLocalUrl;

    function normalizeCustomerPath(url) {
        try {
            const u = toCustomerLocalUrl(url) || new URL(url, window.location.origin);
            return u.pathname + (u.search || '');
        } catch(e) {
            return url;
        }
    }

    function isInternalCustomerRoute(url) {
        try {
            const u = toCustomerLocalUrl(url);
            if (!u) return false;
            if (u.origin !== window.location.origin) return false;
            const p = u.pathname;
            if (p.startsWith('/admin') || p === '/mitra' || p.startsWith('/mitra/') || p.startsWith('/api') || p.startsWith('/storage')) return false;
            return true;
        } catch(e) {
            return false;
        }
    }

    function updateBottomNavActiveState(urlObj) {
        if (!urlObj) {
            try {
                urlObj = new URL(window.location.href);
            } catch(e) {
                return;
            }
        }
        const path = urlObj.pathname;
        const tab = urlObj.searchParams.get('tab') || 'dashboard';

        // 1. Bottom Nav Guest (Dashboard, Layanan, Mitra, Tentang)
        const guestItems = document.querySelectorAll('#bottomNavGuest .bottom-nav-item');
        guestItems.forEach(item => {
            const rawHref = item.getAttribute('href') || '';
            let itemPath = '/';
            try {
                itemPath = new URL(rawHref, window.location.origin).pathname;
            } catch(e) {}

            let isActive = false;
            if (path === '/' || path === '/pengunjung' || path === '' || path === '/customer/beranda') {
                isActive = (item.id === 'guestNavDashboard' || itemPath === '/' || itemPath === '/pengunjung' || itemPath === '');
            } else if (path.includes('/layanan')) {
                isActive = (item.id === 'guestNavLayanan' || itemPath.includes('/layanan'));
            } else if (path.includes('/mitra') || path.includes('/mitra-per-kota')) {
                isActive = (item.id === 'guestNavMitra' || itemPath.includes('/mitra'));
            } else if (path.includes('/tentang')) {
                isActive = (item.id === 'guestNavTentang' || itemPath.includes('/tentang'));
            }
            item.classList.toggle('nav-active', isActive);
        });

        // 2. Bottom Nav Customer
        const isDashboard = path.includes('/customer/dashboard');
        document.querySelectorAll('#bottomNavCustomer .bottom-nav-item').forEach(el => el.classList.remove('nav-active'));
        if (isDashboard) {
            const idMap = {
                'dashboard': 'custNavDashboard',
                'layanan': 'custNavLayanan',
                'pesan': 'custNavPesan',
                'pesanan': 'custNavPesanan',
                'profile': 'custNavProfile'
            };
            const activeBtn = document.getElementById(idMap[tab] || 'custNavDashboard');
            if (activeBtn) activeBtn.classList.add('nav-active');
        }
    }

    window.invalidateCustomerCache = function(pattern = null) {
        if (!pattern) {
            sbDomCache.clear();
        } else {
            for (const k of sbDomCache.keys()) {
                if (k.includes(pattern)) sbDomCache.delete(k);
            }
        }
    };

    async function spaNavigateTo(url, pushState = true, forceFresh = false) {
        try {
            const targetUrl = toCustomerLocalUrl(url) || new URL(url, window.location.origin);

            // Batas role: Halaman Mitra dan Admin memiliki layout tersendiri (wajib navigasi penuh)
            if (!isInternalCustomerRoute(targetUrl.href)) {
                window.location.href = targetUrl.href;
                return;
            }

            // Hentikan timer polling halaman sebelumnya (mis. interval pesanan detail)
            if (typeof window.clearPesananDetailTimers === 'function') {
                window.clearPesananDetailTimers();
            }
            window._sbIsNavigatingStatus = false;

            // Tutup drawer & modal jika terbuka
            if (typeof closeCustomerDrawer === 'function') closeCustomerDrawer();
            if (typeof closeAllCustomerModals === 'function') closeAllCustomerModals();

            // Khusus: Jika sudah di /customer/dashboard dan tujuan juga dashboard dengan tab
            if (!forceFresh && window.location.pathname.includes('/customer/dashboard') && targetUrl.pathname.includes('/customer/dashboard')) {
                const tab = targetUrl.searchParams.get('tab') || 'dashboard';
                if (typeof switchCustomerTab === 'function') {
                    switchCustomerTab(tab);
                    const room = targetUrl.searchParams.get('room');
                    const mitraParam = targetUrl.searchParams.get('mitra') || '';
                    if (tab === 'pesan' && room && typeof openCustChatRoom === 'function') {
                        setTimeout(() => openCustChatRoom(room, mitraParam), 150);
                    }
                    if (pushState) window.history.pushState({ spa: true, url: targetUrl.href }, '', targetUrl.href);
                    updateBottomNavActiveState(targetUrl);
                    return;
                }
            }

            // Dapatkan HTML dari memory cache atau fetch seketika.
            // Halaman dinamis pesanan & pembayaran tidak memakai cache usang agar sinkronisasi real-time.
            const isDynamicFlow = targetUrl.pathname.includes('/customer/pembayaran') || targetUrl.pathname.includes('/customer/pesanan');
            const targetNorm = normalizeCustomerPath(targetUrl.href);
            let htmlText = (!forceFresh && !isDynamicFlow) ? (sbDomCache.get(targetUrl.href) || sbDomCache.get(targetNorm)) : null;
            if (!htmlText) {
                const res = await fetch(targetUrl.href, {
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) {
                    window.location.href = targetUrl.href;
                    return;
                }
                htmlText = await res.text();
                if (!isDynamicFlow) {
                    sbDomCache.set(targetUrl.href, htmlText);
                    sbDomCache.set(targetNorm, htmlText);
                }
            }

            // Parse HTML baru
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');

            const newApp = doc.querySelector('.mobile-app');
            const curApp = document.querySelector('.mobile-app');

            if (!newApp || !curApp) {
                window.location.href = targetUrl.href;
                return;
            }

            // 1. Update Title Dokumen
            if (doc.title) document.title = doc.title;

            // 2. Update Browser URL (History State)
            if (pushState) {
                window.history.pushState({ spa: true, url: targetUrl.href }, '', targetUrl.href);
            }

            // 3. Swap Konten Mobile App Seketika (0ms)
            curApp.innerHTML = newApp.innerHTML;

            // 4. Update Status Bottom Bar
            updateBottomNavActiveState(targetUrl);

            // 5. Scroll Langsung ke Puncak (Tanpa Animasi Smooth yang Delay)
            window.scrollTo(0, 0);

            // 6. Jalankan script halaman baru secara global agar function deklarasi dapat diakses oleh onclick
            function runScriptSafely(code) {
                if (!code || !code.trim()) return;
                try {
                    (0, eval)(code);
                } catch(e) {
                    try {
                        const fn = new Function(code);
                        fn.call(window);
                    } catch(e2) {
                        console.warn('Script execution notice:', e);
                    }
                }
            }

            // Eksekusi script dalam .mobile-app baru
            Array.from(curApp.getElementsByTagName('script')).forEach(s => {
                runScriptSafely(s.textContent);
            });

            // Eksekusi script khusus halaman baru dari stack scripts
            Array.from(doc.getElementsByTagName('script')).forEach(s => {
                if (!s.hasAttribute('data-layout-script') && s.parentNode === doc.body) {
                    runScriptSafely(s.textContent);
                }
            });

            // 7. Picu event DOMContentLoaded & sync tampilan
            window._sbIsNavigatingStatus = false;
            try {
                document.dispatchEvent(new Event('DOMContentLoaded'));
            } catch(e) {}

            if (typeof syncGradientEkor === 'function') syncGradientEkor();
            if (typeof refreshCustomerAuthUI === 'function') refreshCustomerAuthUI();
            if (typeof updateHeaderAuthUI === 'function') updateHeaderAuthUI();
            if (typeof updateDrawerProfileUI === 'function') updateDrawerProfileUI();
            if (typeof syncDrawerActiveNav === 'function') syncDrawerActiveNav(targetUrl.pathname);

            // Jika masuk ke dashboard dengan query param ?tab=, buka tab terkait
            if (targetUrl.pathname.includes('/customer/dashboard')) {
                const tab = targetUrl.searchParams.get('tab') || 'dashboard';
                if (typeof switchCustomerTab === 'function') {
                    switchCustomerTab(tab);
                }
                const room = targetUrl.searchParams.get('room');
                const mitraParam = targetUrl.searchParams.get('mitra') || '';
                if (tab === 'pesan' && room && typeof openCustChatRoom === 'function') {
                    setTimeout(() => openCustChatRoom(room, mitraParam), 150);
                }
            }
        } catch(err) {
            console.error('SPA navigation warning:', err);
            // Fallback navigasi langsung jika fetch / swap SPA mengalami kendala
            try {
                const fallbackUrl = (toCustomerLocalUrl(url) || new URL(url, window.location.origin)).href;
                window.location.href = fallbackUrl;
            } catch(e) {
                window.location.href = url;
            }
        }
    }
    window.spaNavigateTo = spaNavigateTo;

    // Intercept semua klik tautan internal customer agar 0ms tanpa reload
    document.addEventListener('click', function(e) {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const a = e.target.closest('a');
        if (!a) return;

        const href = a.getAttribute('href');
        if (!href) return;

        if (href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:')) return;
        if (a.getAttribute('target') === '_blank' || a.hasAttribute('download')) return;

        const localU = toCustomerLocalUrl(a.href);
        const url = localU || new URL(a.href, window.location.origin);
        if (!isInternalCustomerRoute(url.href)) return;

        // Hash pada halaman yang sama
        if (url.pathname === window.location.pathname && url.hash) {
            const targetEl = document.querySelector(url.hash);
            if (targetEl) {
                e.preventDefault();
                targetEl.scrollIntoView({ behavior: 'auto' });
                return;
            }
        }

        e.preventDefault();
        spaNavigateTo(url.href, true);
    });

    // Handle tombol Back/Forward browser seketika tanpa reload
    window.addEventListener('popstate', function(e) {
        spaNavigateTo(window.location.href, false);
    });

    // Navigasi Bottom Bar Tab Customer & Integrasi Alur
    function switchCustomerNavTab(tab) {
        const isDashboardUrl = window.location.pathname.includes('/customer/dashboard');
        const viewEl = document.getElementById('viewCust' + tab.charAt(0).toUpperCase() + tab.slice(1));

        // Jika benar-benar berada di halaman dashboard DAN elemen tab view tersedia di DOM
        if (isDashboardUrl && viewEl && typeof switchCustomerTab === 'function') {
            switchCustomerTab(tab);
            try {
                const u = new URL(window.location.href);
                u.searchParams.set('tab', tab);
                window.history.pushState({ spa: true, url: u.href }, '', u.href);
            } catch(e) {}
            return;
        }

        // Jika sedang di halaman detail pesanan atau halaman lainnya, bersihkan timer halaman lalu navigasi
        if (typeof window.clearPesananDetailTimers === 'function') {
            window.clearPesananDetailTimers();
        }

        let user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        const explicitlyLoggedOut = localStorage.getItem('sb_customer_logged_out') === 'true';
        const isPengunjungMode = window.location.pathname === '/pengunjung' || 
                                 window.location.pathname.includes('/pengunjung') || 
                                 document.body.getAttribute('data-is-pengunjung') === 'true';

        const isCust = user && user.isLoggedIn && user.role === 'customer';

        if (isCust) {
            let targetNavUrl = "{{ route('customer.dashboard') }}?tab=" + encodeURIComponent(tab);
            if (tab === 'pesan' && window.location.pathname.includes('/customer/pesanan/')) {
                const pathParts = window.location.pathname.split('/').filter(Boolean);
                const orderCodeParam = pathParts[pathParts.length - 1];
                if (orderCodeParam) {
                    targetNavUrl += '&room=' + encodeURIComponent(orderCodeParam);
                }
            }
            spaNavigateTo(targetNavUrl);
        } else {
            if (tab === 'dashboard') {
                spaNavigateTo("{{ route('customer.index') }}");
            } else if (tab === 'layanan') {
                spaNavigateTo("{{ route('customer.layanan') }}");
            } else {
                if (typeof openModal === 'function') {
                    if (typeof switchLoginRole === 'function') switchLoginRole('customer');
                    openModal('modalLoginCustomer');
                    if (typeof showCustomerToast === 'function') {
                        const labelTab = { pesanan: 'Pesanan', pesan: 'Pesan & Chat', profile: 'Profile' }[tab] || 'Pesanan';
                        showCustomerToast('Perlu Masuk', 'Silakan masuk untuk mengakses menu ' + labelTab + '.', 'info');
                    }
                } else {
                    spaNavigateTo("{{ route('customer.login') }}");
                }
            }
        }
    }
    window.switchCustomerNavTab = switchCustomerNavTab;

    // Prefetch instan pada touchstart (mobile) & hover (desktop) serta prefetch otomatis halaman penting
    function prefetchCustomerUrl(url) {
        if (!url || typeof url !== 'string') return;
        try {
            const u = new URL(url, window.location.origin);
            if (!isInternalCustomerRoute(u.href)) return;
            const norm = normalizeCustomerPath(u.href);
            if (sbDomCache.has(u.href) || sbDomCache.has(norm)) return;

            fetch(u.href, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    sbDomCache.set(u.href, html);
                    sbDomCache.set(norm, html);
                })
                .catch(() => {});
        } catch(e) {}
    }
    window.prefetchCustomerUrl = prefetchCustomerUrl;

    function prefetchCoreCustomerRoutes() {
        // Cache dokumen saat ini jika belum
        const curNorm = normalizeCustomerPath(window.location.href);
        if (!sbDomCache.has(window.location.href)) {
            sbDomCache.set(window.location.href, document.documentElement.outerHTML);
            sbDomCache.set(curNorm, document.documentElement.outerHTML);
        }

        const coreRoutes = [
            "{{ route('customer.home') }}",
            "{{ route('customer.layanan') }}",
            "{{ route('customer.mitra') }}",
            "{{ route('customer.tentang') }}",
            "{{ route('customer.cara-kerja') }}",
            "{{ route('customer.syarat-ketentuan') }}",
            "{{ route('customer.kebijakan-privasi') }}",
            "{{ route('customer.favorit') }}",
            "{{ route('customer.pengaduan') }}",
            "{{ route('customer.dashboard') }}"
        ];

        // Prefetch mulus berurutan cepat (35ms) agar server PHP lokal tetap responsif seketika
        let i = 0;
        coreRoutes.forEach(url => {
            setTimeout(() => prefetchCustomerUrl(url), i * 35);
            i++;
        });
    }
    window.prefetchCoreCustomerRoutes = prefetchCoreCustomerRoutes;

    (function() {
        document.addEventListener('touchstart', function(e) {
            const a = e.target.closest('a[href]');
            if (a && a.href) prefetchCustomerUrl(a.href);
        }, { passive: true });

        let hoverTimer = null;
        document.addEventListener('mouseover', function(e) {
            const a = e.target.closest('a[href]');
            if (!a || !a.href) return;
            hoverTimer = setTimeout(() => {
                prefetchCustomerUrl(a.href);
            }, 80);
        }, { passive: true });

        document.addEventListener('mouseout', function(e) {
            if (hoverTimer) clearTimeout(hoverTimer);
        }, { passive: true });
    })();

    window.addEventListener('load', function() {
        syncGradientEkor();
        updateBottomNavActiveState();
        if (typeof syncDrawerActiveNav === 'function') syncDrawerActiveNav();
        prefetchCoreCustomerRoutes();
    });
    window.addEventListener('resize', syncGradientEkor);
    document.addEventListener('DOMContentLoaded', function() {
        syncGradientEkor();
        updateBottomNavActiveState();
        if (typeof syncDrawerActiveNav === 'function') syncDrawerActiveNav();
        prefetchCoreCustomerRoutes();
    });
</script>

@stack('scripts')

</body>
</html>

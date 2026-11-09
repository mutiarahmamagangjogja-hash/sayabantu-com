<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Portal Mitra SayaBantu.com')</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function() {
            try {
                // Bersihkan sb_customer_auth jika tertular data mitra
                const custRaw = localStorage.getItem('sb_customer_auth');
                if (custRaw) {
                    const c = JSON.parse(custRaw);
                    if (c && c.role === 'mitra') {
                        localStorage.removeItem('sb_customer_auth');
                    }
                }
            } catch(e) {}
        })();
    </script>

    {{-- FONT INTER --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- KOMPONEN MODAL BERSAMA (dipakai juga oleh panel admin & customer) --}}
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
                        amber: '#FBBF24',
                        page: '#F7F9FC',
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body { margin: 0; padding: 0; min-height: 100%; }
        html.sb-scroll-locked, body.sb-scroll-locked {
            overflow: hidden !important;
            overscroll-behavior: none !important;
        }
        body { font-family: 'Inter', sans-serif; background: #E5E7EB; color: #111827; }
        a { text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }
        ::-webkit-scrollbar { width: 0; height: 0; }

        .mobile-app {
            width: 100%; max-width: 430px; min-height: 100vh;
            margin: 0 auto; position: relative; overflow-x: hidden;
            background: #F7F9FC; box-shadow: 0 0 40px rgba(15,23,42,0.14);
            padding-bottom: 84px;
        }

        /* GRADIENT PERSIS SEPERTI CUSTOMER & ADMIN (STANDARISASI 15 CM) */
        .mitra-top-area {
            position: relative; overflow: visible; color: white;
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

        .mitra-top-area > * { position: relative; z-index: 1; }

        .header-circle-one {
            position: absolute; width: 190px; height: 190px; right: -105px; top: -120px;
            border-radius: 9999px; background: rgba(255,255,255,0.06); pointer-events: none;
        }

        .header-circle-two {
            position: absolute; width: 135px; height: 135px; left: -80px; top: 120px;
            border-radius: 9999px; background: rgba(255,255,255,0.035); pointer-events: none;
        }

        /* TOMBOL IKON HEADER PERSIS CUSTOMER */
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

        /* BOTTOM NAVBAR PERSIS SEPERTI CUSTOMER */
        .bottom-navigation {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 430px; z-index: 40;
            background: rgba(255,255,255,0.97); backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226,232,240,0.9);
            box-shadow: 0 -4px 20px rgba(15,23,42,0.06);
        }

        .bottom-navigation-inner {
            display: flex; align-items: center; justify-content: space-around;
            height: 60px; padding: 0 8px;
        }

        .bottom-nav-item {
            flex: 1; min-height: 50px; position: relative;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 3px; margin: 0 2px; border-radius: 14px;
            color: #9CA3AF; font-size: 9.5px; font-weight: 500; transition: all 0.18s ease;
        }
        .bottom-nav-item i { font-size: 15px; }
        .bottom-nav-item:hover { color: #4B5563; }
        .bottom-nav-item:active { transform: scale(0.93); }
        .bottom-nav-item.nav-active {
            color: #173B67; font-weight: 700; background: #EFF6FF;
        }
        .bottom-nav-item.nav-active i { color: #173B67; }
        .bottom-nav-item.nav-active::before {
            content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 22px; height: 3px; border-radius: 0 0 6px 6px; background: #173B67;
        }

        .card-sb {
            background: #FFFFFF; border: 1px solid #E2E8F0;
            border-radius: 16px; box-shadow: 0 2px 8px rgba(15,23,42,0.03);
        }

        .content-under-gradient {
            margin-top: -32px; position: relative; z-index: 20;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')

    {{-- AUTO-PURGE SCRIPT TO ENSURE CLEAN ZERO START --}}
    <script>
        (function autoCleanDummyData() {
            try {
                const CLEAN_KEY = "sb_zero_reset_v9";
                if (!localStorage.getItem(CLEAN_KEY)) {
                    localStorage.removeItem("sb_customer_orders");
                    localStorage.removeItem("sb_active_order");
                    localStorage.removeItem("sb_mitra_orders");
                    localStorage.removeItem("sb_mitra_bids");
                    localStorage.removeItem("sb_complaints");
                    localStorage.removeItem("sb_customer_pembayaran_selesai");
                    ['sb_mitra_registration'].forEach(k => {
                        const raw = localStorage.getItem(k);
                        if (raw && (raw.toLowerCase().includes('bambang') || raw.toLowerCase().includes('budi') || raw.toLowerCase().includes('hendra'))) {
                            localStorage.removeItem(k);
                        }
                    });
                    localStorage.setItem(CLEAN_KEY, "true");
                }
            } catch(e) {}
        })();
    </script>

    @if(session('mitra_auth'))
    <script>
        try {
            const serverMitraAuth = @json(session('mitra_auth'));
            localStorage.setItem('sb_mitra_auth', JSON.stringify(serverMitraAuth));
            const rawCust = localStorage.getItem('sb_customer_auth');
            if (rawCust) {
                const c = JSON.parse(rawCust);
                if (c && c.role === 'mitra') localStorage.removeItem('sb_customer_auth');
            }
        } catch(e) {}
    </script>
    @endif

</head>
<body>

<div class="mobile-app">
    @yield('page-top')
    @yield('content')
</div>

{{-- =========================================================
     NAVIGASI UTAMA (BOTTOM NAVBAR MITRA)
     Dashboard | Kebutuhan | Pesan | Pekerjaan | Profil
========================================================= --}}
@if(trim($__env->yieldContent('hide-bottom-nav')) !== 'true')
<nav class="bottom-navigation" id="mitraBottomNav">
    <div class="bottom-navigation-inner">
        <button type="button" onclick="switchMitraNav('dashboard')" id="mitraNavDashboard" class="bottom-nav-item nav-active">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </button>

        <button type="button" onclick="switchMitraNav('kebutuhan')" id="mitraNavKebutuhan" class="bottom-nav-item relative">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Kebutuhan</span>
        </button>

        <button type="button" onclick="switchMitraNav('pesan')" id="mitraNavPesan" class="bottom-nav-item relative">
            <i class="fa-regular fa-comment-dots"></i>
            <span class="absolute top-0.5 right-1.5 min-w-[16px] h-4 rounded-full bg-rose-500 text-[7.5px] font-black text-white flex items-center justify-center px-1">3</span>
            <span>Pesan</span>
        </button>

        <button type="button" onclick="switchMitraNav('pekerjaan')" id="mitraNavPekerjaan" class="bottom-nav-item relative">
            <i class="fa-solid fa-briefcase"></i>
            <span class="absolute top-1.5 right-2 w-1.5 h-1.5 rounded-full bg-amber-400"></span>
            <span>Pekerjaan</span>
        </button>

        <button type="button" onclick="switchMitraNav('profil')" id="mitraNavProfil" class="bottom-nav-item">
            <i class="fa-solid fa-user"></i>
            <span>Profil</span>
        </button>
    </div>
</nav>
@endif

<div id="mitraDynamicModals">
    @yield('modals')
    @stack('modals')
</div>

<script data-layout-script="true">
    // =========================================================
    //  INSTANT TAB SWITCHING & SPA ENGINE PORTAL MITRA
    //  - 0ms Tab Switching di /mitra/dashboard (11 view in-memory)
    //  - Instant window.scrollTo(0, 0) tanpa delay smooth scroll
    //  - Sinkronisasi URL address bar (/mitra/dashboard?tab=...)
    //  - SPA Swapper antar halaman mitra (/mitra/daftar, dsb)
    // =========================================================
    const mitraDomCache = new Map();

    function closeAllMitraModals() {
        // Jangan menutup modal yang baru saja dibuka oleh halaman ini
        if (typeof window.adminModalCloseAllowed === 'function' && !window.adminModalCloseAllowed()) return;

        document.querySelectorAll('.sb-overlay-backdrop').forEach(m => {
            m.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            m.classList.remove('opacity-100', 'pointer-events-auto');
            const card = m.querySelector('.transform');
            if (card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
        });
        document.documentElement.classList.remove('sb-scroll-locked');
        document.body.classList.remove('sb-scroll-locked');
    }
    window.closeAllMitraModals = closeAllMitraModals;

    function toMitraLocalUrl(url) {
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
    window.toMitraLocalUrl = toMitraLocalUrl;

    function isInternalMitraRoute(url) {
        try {
            const u = toMitraLocalUrl(url);
            if (!u) return false;
            if (u.origin !== window.location.origin) return false;
            // SPA hanya aktif ketika berpindah tab di dalam /mitra/dashboard
            if (window.location.pathname.includes('/mitra/dashboard') && (u.pathname === '/mitra' || u.pathname === '/mitra/dashboard')) {
                return true;
            }
            return false;
        } catch(e) {
            return false;
        }
    }

    function switchMitraNav(tabName, updateUrl = true) {
        // Tutup modal / popup yang mungkin terbuka
        closeAllMitraModals();

        // Daftar semua view (4 tab utama + 7 halaman burger menu)
        const allViews = [
            'dashboard', 'kebutuhan', 'pekerjaan', 'profil',
            'saldo', 'pesan', 'ulasan', 'performa', 'bantuan', 'pengaturan', 'pengaduan'
        ];
        const mainNavTabs = ['dashboard', 'kebutuhan', 'pesan', 'pekerjaan', 'profil'];

        if (!allViews.includes(tabName)) tabName = 'dashboard';

        // Sembunyikan semua view, tampilkan view yang dituju
        allViews.forEach(v => {
            const elId = 'viewMitra' + v.charAt(0).toUpperCase() + v.slice(1);
            const el = document.getElementById(elId);
            if (el) {
                el.classList.toggle('hidden', v !== tabName);
            }
        });

        // Update Banner di Area Gradient Header (Gradient selalu aktif & seragam)
        const greetingBanner = document.getElementById('mitraGreetingBanner');
        const tabBanner = document.getElementById('mitraTabHeaderBanner');
        const tabTitle = document.getElementById('mitraTabTitle');
        const tabDesc = document.getElementById('mitraTabDesc');
        const tabBadge = document.getElementById('mitraTabBadge');

        const tabMetadata = {
            kebutuhan: { title: 'Kebutuhan Masuk', desc: 'Pilih kebutuhan customer yang cocok dengan keahlian Anda, lalu ajukan harga penawaran terbaik.', badge: 'Kebutuhan Customer', badgeClass: 'bg-sky-500/20 text-white border-sky-400/40' },
            pekerjaan: { title: 'Pekerjaan Saya', desc: 'Pantau pekerjaan aktif, perbarui status OTW, dan konfirmasi penyelesaian tugas.', badge: 'Tugas Mitra', badgeClass: 'bg-amber-500/20 text-amber-200 border-amber-400/40' },
            pesan: { title: 'Pesan & Chat Customer', desc: 'Komunikasi langsung dengan customer terkait koordinasi penugasan.', badge: 'Chat Aktif', badgeClass: 'bg-rose-500/20 text-rose-200 border-rose-400/40' },
            profil: { title: 'Profil Mitra', desc: 'Kelola identitas, spesialisasi keahlian, dan reputasi Mitra SayaBantu.', badge: 'Mitra Terverifikasi', badgeClass: 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40' },
            saldo: { title: 'Dompet & Saldo Mitra', desc: 'Pantau riwayat penghasilan dan ajukan pencairan dana ke rekening Anda.', badge: 'Rp 0', badgeClass: 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40' },
            ulasan: { title: 'Ulasan & Penilaian', desc: 'Penilaian dan testimoni kepuasan dari pelanggan yang telah Anda bantu.', badge: {{ Js::from(($mitraAuthRating ?? null) !== null ? $mitraAuthRating.' ★ ('.($mitraAuthReviewCount ?? 0).' Ulasan)' : 'Belum ada ulasan') }}, badgeClass: 'bg-amber-500/20 text-amber-200 border-amber-400/40' },
            performa: { title: 'Performa & Statistik', desc: 'Tingkat keberhasilan tugas, kecepatan respon, dan reputasi kerja Anda.', badge: 'Level 3 Unggul', badgeClass: 'bg-sky-500/20 text-sky-200 border-sky-400/40' },
            bantuan: { title: 'Pusat Bantuan Mitra', desc: 'Pertanyaan seputar aplikasi dan bantuan darurat dispatch support.', badge: 'Bantuan 24 Jam', badgeClass: 'bg-sky-500/20 text-sky-200 border-sky-400/40' },
            pengaturan: { title: 'Pengaturan Akun', desc: 'Atur preferensi notifikasi, keamanan sandi, dan status operasional.', badge: 'Terverifikasi', badgeClass: 'bg-slate-500/20 text-slate-200 border-slate-400/40' },
            pengaduan: { title: 'Pusat Pengaduan Mitra', desc: 'Laporkan kendala tugas, pelanggaran customer, dan pantau status mediasi admin.', badge: 'Mediasi Terpercaya', badgeClass: 'bg-rose-500/20 text-rose-200 border-rose-400/40' }
        };

        if (tabName === 'dashboard') {
            if (greetingBanner) greetingBanner.classList.remove('hidden');
            if (tabBanner) tabBanner.classList.add('hidden');
        } else {
            if (greetingBanner) greetingBanner.classList.add('hidden');
            if (tabBanner) {
                tabBanner.classList.remove('hidden');
                const meta = tabMetadata[tabName] || { title: tabName, desc: '', badge: '', badgeClass: '' };
                if (tabTitle) tabTitle.innerText = meta.title;
                if (tabDesc) tabDesc.innerText = meta.desc;
                if (tabBadge) {
                    tabBadge.innerText = meta.badge;
                    tabBadge.className = 'inline-flex items-center text-[9px] font-bold px-2.5 py-1 rounded-full border whitespace-nowrap shrink-0 ' + meta.badgeClass;
                }
            }
        }

        // Update nav active status
        mainNavTabs.forEach(t => {
            const navBtn = document.getElementById('mitraNav' + t.charAt(0).toUpperCase() + t.slice(1));
            if (navBtn) {
                navBtn.classList.toggle('nav-active', t === tabName);
            }
        });

        // Tandai menu burger yang sedang aktif (halaman penanda)
        document.querySelectorAll('.btn-burger-mitra').forEach(b => {
            const isActive = b.dataset.burgerNav === tabName;
            b.classList.toggle('bg-sky-500/20', isActive);
            b.classList.toggle('border-sky-400/40', isActive);
            b.classList.toggle('text-white', isActive);
            b.classList.toggle('font-bold', isActive);
            b.classList.toggle('text-slate-200', !isActive);
            b.classList.toggle('font-semibold', !isActive);
        });

        // Sinkronisasi view chat saat membuka tab pesan
        if (tabName === 'pesan') {
            const chatList = document.getElementById('chatListView');
            const chatDetail = document.getElementById('chatDetailView');
            if (!window.currentChatRoom) {
                if (chatList) chatList.classList.remove('hidden');
                if (chatDetail) chatDetail.classList.add('hidden');
            } else {
                if (chatList) chatList.classList.add('hidden');
                if (chatDetail) chatDetail.classList.remove('hidden');
            }
            if (typeof syncMitraChatListFromApi === 'function') {
                syncMitraChatListFromApi();
            }
        }

        // Sinkronisasi URL ke browser address bar tanpa refresh
        if (updateUrl && window.location.pathname.includes('/mitra/dashboard')) {
            const targetUrl = tabName === 'dashboard' ? '{{ route("mitra.dashboard") }}' : '{{ route("mitra.dashboard") }}?tab=' + encodeURIComponent(tabName);
            window.history.pushState({ mitraTab: tabName, url: targetUrl }, '', targetUrl);
        }

        // Instant scroll ke atas tanpa delay smooth scroll
        window.scrollTo(0, 0);

        // Sinkronisasi batas gradien 15 cm setelah perubahan view
        syncMitraGradientEkor();
        if (window.requestAnimationFrame) {
            requestAnimationFrame(syncMitraGradientEkor);
        }
    }
    window.switchMitraNav = switchMitraNav;

    // SPA Engine antar halaman Mitra (/mitra/daftar <-> /mitra/dashboard)
    async function mitraSpaNavigateTo(url, pushState = true) {
        try {
            const targetUrl = new URL(url, window.location.origin);

            // Jika tujuan adalah tab di dashboard dan saat ini sudah di dashboard
            if (window.location.pathname.includes('/mitra/dashboard') && targetUrl.pathname.includes('/mitra/dashboard')) {
                const tab = targetUrl.searchParams.get('tab') || 'dashboard';
                switchMitraNav(tab, pushState);
                return;
            }

            let htmlText = mitraDomCache.get(targetUrl.href);
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
                mitraDomCache.set(targetUrl.href, htmlText);
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');

            const newApp = doc.querySelector('.mobile-app');
            const curApp = document.querySelector('.mobile-app');

            if (!newApp || !curApp) {
                window.location.href = targetUrl.href;
                return;
            }

            if (doc.title) document.title = doc.title;

            closeAllMitraModals();
            curApp.innerHTML = newApp.innerHTML;

            const newModals = doc.querySelector('#mitraDynamicModals');
            const curModals = document.querySelector('#mitraDynamicModals');
            if (curModals && newModals) {
                curModals.innerHTML = newModals.innerHTML;
            } else if (curModals && !newModals) {
                curModals.innerHTML = '';
            }

            window.scrollTo(0, 0);

            // Jalankan script halaman baru dengan aman
            function runScriptSafely(code) {
                if (!code || !code.trim()) return;
                try {
                    const fn = new Function(code);
                    fn.call(window);
                } catch(e) {
                    console.warn('Mitra script execution notice:', e);
                }
            }

            Array.from(curApp.getElementsByTagName('script')).forEach(s => {
                runScriptSafely(s.textContent);
            });

            Array.from(doc.getElementsByTagName('script')).forEach(s => {
                if (!s.hasAttribute('data-layout-script') && s.parentNode === doc.body) {
                    runScriptSafely(s.textContent);
                }
            });

            if (typeof openMitraTabFromLocation === 'function') {
                openMitraTabFromLocation();
            }

            syncMitraGradientEkor();

            // Jika menuju ke dashboard dengan tab tertentu
            if (targetUrl.pathname.includes('/mitra/dashboard')) {
                const tab = targetUrl.searchParams.get('tab') || 'dashboard';
                switchMitraNav(tab, false);
            }
        } catch(err) {
            console.error('Mitra SPA navigation warning:', err);
            if (!document.querySelector('.mobile-app')) {
                window.location.href = url;
            }
        }
    }
    window.mitraSpaNavigateTo = mitraSpaNavigateTo;

    // Intercept semua klik tautan internal Mitra
    document.addEventListener('click', function(e) {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const a = e.target.closest('a');
        if (!a) return;

        const href = a.getAttribute('href');
        if (!href) return;

        if (href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:')) return;
        if (a.getAttribute('target') === '_blank' || a.hasAttribute('download')) return;

        const url = new URL(a.href, window.location.origin);
        if (!isInternalMitraRoute(url.href)) return;

        // Jika tujuan adalah /mitra atau /mitra/dashboard
        if (url.pathname === '/mitra' || url.pathname === '/mitra/dashboard') {
            const tab = url.searchParams.get('tab') || (url.hash ? url.hash.replace('#', '') : 'dashboard');
            if (window.location.pathname.includes('/mitra/dashboard')) {
                e.preventDefault();
                closeAllMitraModals();
                switchMitraNav(tab, true);
                return;
            }
        }

        e.preventDefault();
        mitraSpaNavigateTo(url.href, true);
    });

    // Handle tombol Back/Forward browser
    window.addEventListener('popstate', function(e) {
        if (window.location.pathname.includes('/mitra/dashboard')) {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab') || 'dashboard';
            switchMitraNav(tab, false);
        } else {
            mitraSpaNavigateTo(window.location.href, false);
        }
    });

    // Prefetch pada touchstart (mobile) & hover (desktop)
    (function() {
        function prefetchMitraUrl(url) {
            if (!url || typeof url !== 'string') return;
            try {
                const u = new URL(url, window.location.origin);
                if (!isInternalMitraRoute(u.href)) return;
                if (mitraDomCache.has(u.href)) return;

                fetch(u.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => mitraDomCache.set(u.href, html))
                    .catch(() => {});
            } catch(e) {}
        }

        document.addEventListener('touchstart', function(e) {
            const a = e.target.closest('a[href]');
            if (a && a.href) prefetchMitraUrl(a.href);
        }, { passive: true });

        let hoverTimer = null;
        document.addEventListener('mouseover', function(e) {
            const a = e.target.closest('a[href]');
            if (!a || !a.href) return;
            hoverTimer = setTimeout(() => {
                prefetchMitraUrl(a.href);
            }, 80);
        }, { passive: true });

        document.addEventListener('mouseout', function(e) {
            if (hoverTimer) clearTimeout(hoverTimer);
        }, { passive: true });
    })();

    // Standarisasi batas gradien 15 cm untuk semua fitur Mitra
    function syncMitraGradientEkor() {
        const top = document.querySelector('.mitra-top-area');
        const content = document.querySelector('.content-under-gradient, #mitraMainContent, main');
        if (!top || !content) return;

        // 15 cm dalam piksel standar web CSS (1 cm = 37.7952px)
        const targetHeightPx = Math.round(15 * 37.795); // 567px
        top.style.height = targetHeightPx + 'px';
        top.style.minHeight = targetHeightPx + 'px';
        top.style.paddingBottom = '0px';

        // Filter ornamen dekorasi di background dan elemen tersembunyi
        const kids = [...top.children].filter(el => {
            const c = el.className || '';
            if (typeof c === 'string' && c.includes('header-circle')) return false;
            return el.offsetParent !== null;
        });

        const topRect = top.getBoundingClientRect();
        let lastKidBottom = topRect.top;
        kids.forEach(k => {
            const rect = k.getBoundingClientRect();
            if (rect.bottom > lastKidBottom) {
                lastKidBottom = rect.bottom;
            }
        });

        // Konten utama diletakkan rapi 12px di bawah konten header terdalam
        const headerContentHeight = Math.max(0, lastKidBottom - topRect.top);
        const desiredContentTop = headerContentHeight + 12;
        const pullUp = targetHeightPx - desiredContentTop;

        content.style.marginTop = '-' + Math.max(0, pullUp) + 'px';
    }

    // Auto-buka tab saat halaman pertama kali dimuat jika ada query ?tab= atau hash #
    function openMitraTabFromLocation() {
        if (window.location.pathname.includes('/mitra/dashboard')) {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab') || (window.location.hash ? window.location.hash.replace('#', '') : null);
            if (tab && tab !== 'dashboard') {
                switchMitraNav(tab, false);
                const room = params.get('room');
                const cust = params.get('customer') || '';
                if (tab === 'pesan' && room && typeof openChatRoom === 'function') {
                    setTimeout(() => openChatRoom(room, cust), 150);
                }
            }
        }
    }
    window.openMitraTabFromLocation = openMitraTabFromLocation;

    document.addEventListener('DOMContentLoaded', function() {
        syncMitraGradientEkor();
        openMitraTabFromLocation();
    });
    window.addEventListener('load', syncMitraGradientEkor);
    window.addEventListener('resize', syncMitraGradientEkor);
</script>

@stack('scripts')
</body>
</html>


{{-- =====================================================
     CUSTOMER BURGER MENU DRAWER (SLIDE-IN DARI KIRI)
====================================================== --}}
<div id="customerBurgerModal" class="fixed inset-0 z-[120] pointer-events-none opacity-0 invisible transition-all duration-300" aria-hidden="true">
    
    {{-- BACKDROP CLICK --}}
    <div id="customerDrawerBackdrop" onclick="closeCustomerDrawer()" class="absolute inset-0 bg-black/60 backdrop-blur-[2px] pointer-events-auto" style="touch-action: none;"></div>

    {{-- DRAWER CONTAINER DALAM MOBILE APP FRAME --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none overflow-hidden">
        
        <div id="customerBurgerPanel" class="absolute left-0 top-0 bottom-0 w-[82%] max-w-[340px] bg-[#0E1D31] text-white pointer-events-auto flex flex-col shadow-2xl -translate-x-full border-r border-white/10 z-10 transition-transform duration-300" style="overscroll-behavior: contain;">
            
            {{-- DRAWER HEADER --}}
            <div class="p-4 border-b border-white/10 flex items-center justify-between bg-gradient-to-b from-[#173B67]/40 to-transparent">
                <a href="javascript:void(0)" onclick="handleDrawerHomeClick()" class="flex items-center gap-2.5 group cursor-pointer" title="Menuju Beranda / Dashboard">
                    <div class="w-8 h-8 rounded-xl bg-[#173B67] border border-sky-400/30 flex items-center justify-center text-white font-bold text-xs shadow-md group-hover:scale-105 transition-transform">
                        SB
                    </div>
                    <div>
                        <h3 class="text-sm font-bold tracking-tight text-white flex items-center gap-1 leading-none">
                            SayaBantu<span class="text-white">.com</span>
                        </h3>
                        <p class="text-[10px] text-white/70 mt-0.5">Suruh Apa Aja Siap</p>
                    </div>
                </a>
                <button type="button" onclick="closeCustomerDrawer()" class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 active:scale-95 text-white/80 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- KARTU STATUS USER / PROFIL --}}
            <div class="px-4 py-3.5 bg-white/[0.04] border-b border-white/5" id="drawerProfileBox">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-bold text-sm flex items-center justify-center shadow-md border border-white/20" id="drawerAvatarInitial">
                            @if(Auth::check())
                                <span class="font-black">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#0E1D31]"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-white truncate" id="drawerUserName">{{ Auth::check() ? Auth::user()->name : 'Pengunjung Umum' }}</h4>
                        <p class="text-[10px] text-white/80 truncate" id="drawerUserStatus">{{ Auth::check() ? '⭐ Member Terverifikasi' : 'Akses Publik Layanan' }}</p>
                        <div class="mt-1 hidden" id="drawerUserActionBtn"></div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR MENU NAVIGASI --}}
            <div class="flex-1 overflow-y-auto px-3 py-3 text-xs">

                @php
                    $infoBases = 'drawer-nav-item w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition-all text-left border relative';
                    $infoAktif = 'bg-[#173B67] text-white border-sky-400/50 font-bold shadow-sm drawer-item-active';
                    $infoBiasa = 'hover:bg-white/10 text-white/90 hover:text-white border-transparent';
                @endphp

                {{-- ===== 1. MENU KHUSUS PENGUNJUNG UMUM (DEFAULT GUEST) ===== --}}
                <div id="drawerMenuGuest" class="space-y-1.5">
                    <span class="px-3 text-[9px] font-bold tracking-wider text-sky-400 uppercase block mb-1.5">
                        Informasi &amp; Panduan
                    </span>


                    {{-- CARA KERJA --}}
                    <a href="{{ route('customer.cara-kerja') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.cara-kerja') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.cara-kerja') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Cara Kerja">📖</span>
                        <span class="flex-1">Cara Kerja</span>
                        @if(request()->routeIs('customer.cara-kerja'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>

                    {{-- SYARAT & KETENTUAN --}}
                    <a href="{{ route('customer.syarat-ketentuan') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.syarat-ketentuan') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.syarat-ketentuan') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Syarat dan Ketentuan">📄</span>
                        <span class="flex-1">Syarat &amp; Ketentuan</span>
                        @if(request()->routeIs('customer.syarat-ketentuan'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>

                    {{-- KEBIJAKAN PRIVASI --}}
                    <a href="{{ route('customer.kebijakan-privasi') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.kebijakan-privasi') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.kebijakan-privasi') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Kebijakan Privasi">🔒</span>
                        <span class="flex-1">Kebijakan Privasi</span>
                        @if(request()->routeIs('customer.kebijakan-privasi'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>
                </div>

                {{-- ===== 2. MENU CUSTOMER YANG TELAH LOGIN ===== --}}
                <div id="drawerMenuCustomer" class="hidden space-y-1.5">
                    <span class="px-3 text-[9px] font-bold tracking-wider text-sky-400 uppercase block mb-1.5">
                        Menu Akun
                    </span>

                    {{-- LAYANAN FAVORIT --}}
                    <a href="{{ route('customer.favorit') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.favorit') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.favorit') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Favorit">❤️</span>
                        <span class="flex-1">Layanan Favorit</span>
                        @if(request()->routeIs('customer.favorit'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>

                    {{-- PUSAT PENGADUAN (CUSTOMER) --}}
                    <a href="{{ route('customer.pengaduan') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.pengaduan') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.pengaduan') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Pengaduan">⚠️</span>
                        <span class="flex-1">Pusat Pengaduan</span>
                        @if(request()->routeIs('customer.pengaduan'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @else
                            <span class="text-[8.5px] font-bold px-1.5 py-0.2 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">Mediasi</span>
                        @endif
                    </a>

                    {{-- BANTUAN --}}
                    <a href="{{ route('customer.pengaduan') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.pengaduan') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.pengaduan') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Bantuan">❓</span>
                        <span class="flex-1">Pusat Bantuan CS</span>
                    </a>

                    <div class="h-px bg-white/10 my-2.5"></div>

                    <span class="px-3 text-[9px] font-bold tracking-wider text-sky-400 uppercase block mb-1.5">
                        Informasi &amp; Legal
                    </span>


                    {{-- CARA KERJA --}}
                    <a href="{{ route('customer.cara-kerja') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.cara-kerja') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.cara-kerja') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Cara Kerja">📖</span>
                        <span class="flex-1">Cara Kerja</span>
                        @if(request()->routeIs('customer.cara-kerja'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>

                    {{-- SYARAT & KETENTUAN --}}
                    <a href="{{ route('customer.syarat-ketentuan') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.syarat-ketentuan') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.syarat-ketentuan') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Syarat dan Ketentuan">📄</span>
                        <span class="flex-1">Syarat &amp; Ketentuan</span>
                        @if(request()->routeIs('customer.syarat-ketentuan'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>

                    {{-- KEBIJAKAN PRIVASI --}}
                    <a href="{{ route('customer.kebijakan-privasi') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.kebijakan-privasi') }}')" class="{{ $infoBases }} {{ request()->routeIs('customer.kebijakan-privasi') ? $infoAktif : $infoBiasa }}">
                        <span class="w-4 text-center shrink-0" role="img" aria-label="Kebijakan Privasi">🔒</span>
                        <span class="flex-1">Kebijakan Privasi</span>
                        @if(request()->routeIs('customer.kebijakan-privasi'))
                            <span class="drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0">
                                <i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif
                            </span>
                        @endif
                    </a>
                </div>

            </div>

            {{-- DRAWER FOOTER (TANPA TOMBOL MASUK UNTUK PENGUNJUNG) --}}
            <div class="p-3.5 border-t border-white/10 bg-black/20 text-center text-[10px] text-white/70 space-y-2">
                <div id="drawerFooterAction" class="hidden"></div>
                <p>© 2026 SayaBantu.com • Jasa &amp; Suruhan Terpercaya</p>
            </div>

        </div>

    </div>

</div>

<script data-layout-script="true">
    function lockCustomerBodyScroll() {
        document.documentElement.classList.add('overflow-hidden', 'sb-scroll-locked');
        document.body.classList.add('overflow-hidden', 'sb-scroll-locked');
    }

    function unlockCustomerBodyScroll() {
        document.documentElement.classList.remove('overflow-hidden', 'sb-scroll-locked');
        document.body.classList.remove('overflow-hidden', 'sb-scroll-locked');
    }

    function syncDrawerActiveNav(currentPath) {
        if (!currentPath) {
            try {
                currentPath = window.location.pathname;
            } catch(e) {
                currentPath = '';
            }
        }

        const allDrawerLinks = document.querySelectorAll('#customerBurgerPanel .drawer-nav-item');
        allDrawerLinks.forEach(link => {
            const rawHref = link.getAttribute('href') || '';
            let linkPath = '';
            try {
                linkPath = new URL(rawHref, window.location.origin).pathname;
            } catch(e) {}

            let isActive = false;
            if (linkPath && linkPath !== '/' && linkPath !== '/customer' && currentPath.includes(linkPath)) {
                isActive = true;
            } else if (linkPath === '/' && (currentPath === '/' || currentPath === '/pengunjung' || currentPath === '')) {
                isActive = true;
            }

            // Hapus indikator badge lama jika ada
            const existingBadge = link.querySelector('.drawer-active-indicator');
            if (existingBadge) existingBadge.remove();

            if (isActive) {
                link.classList.add('bg-[#173B67]', 'text-white', 'border-sky-400/50', 'font-bold', 'shadow-sm', 'drawer-item-active');
                link.classList.remove('text-white/90', 'border-transparent');

                const badge = document.createElement('span');
                badge.className = 'drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0';
                badge.innerHTML = '<i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif';
                link.appendChild(badge);
            } else {
                link.classList.remove('bg-[#173B67]', 'text-white', 'border-sky-400/50', 'font-bold', 'shadow-sm', 'drawer-item-active');
                link.classList.add('text-white/90', 'border-transparent');
            }
        });
    }
    window.syncDrawerActiveNav = syncDrawerActiveNav;

    function handleDrawerNavClick(e, url) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Segera beri visual aktif seketika pada item yang diklik
        const targetA = (e && e.currentTarget) ? e.currentTarget : null;
        if (targetA) {
            const allItems = document.querySelectorAll('#customerBurgerPanel .drawer-nav-item');
            allItems.forEach(item => {
                item.classList.remove('bg-[#173B67]', 'text-white', 'border-sky-400/50', 'font-bold', 'shadow-sm', 'drawer-item-active');
                item.classList.add('text-white/90', 'border-transparent');
                const badge = item.querySelector('.drawer-active-indicator');
                if (badge) badge.remove();
            });
            targetA.classList.add('bg-[#173B67]', 'text-white', 'border-sky-400/50', 'font-bold', 'shadow-sm', 'drawer-item-active');
            targetA.classList.remove('text-white/90', 'border-transparent');
            const badge = document.createElement('span');
            badge.className = 'drawer-active-indicator inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-500/25 text-sky-200 border border-sky-400/40 shrink-0';
            badge.innerHTML = '<i class="fa-solid fa-circle-check text-[8.5px] text-sky-400"></i> Aktif';
            targetA.appendChild(badge);
        }

        closeCustomerDrawer();

        if (typeof spaNavigateTo === 'function') {
            spaNavigateTo(url, true);
        } else {
            window.location.href = url;
        }
    }
    window.handleDrawerNavClick = handleDrawerNavClick;

    function openCustomerDrawer() {
        const modal = document.getElementById('customerBurgerModal');
        const panel = document.getElementById('customerBurgerPanel');
        if (modal && panel) {
            modal.classList.remove('pointer-events-none', 'opacity-0', 'invisible');
            panel.classList.remove('-translate-x-full');
            lockCustomerBodyScroll();
            updateDrawerProfileUI();
            syncDrawerActiveNav();
            if (typeof prefetchCoreCustomerRoutes === 'function') {
                prefetchCoreCustomerRoutes();
            }
        }
    }

    function closeCustomerDrawer() {
        const modal = document.getElementById('customerBurgerModal');
        const panel = document.getElementById('customerBurgerPanel');
        if (modal && panel) {
            panel.classList.add('-translate-x-full');
            modal.classList.add('opacity-0');
            unlockCustomerBodyScroll();
            setTimeout(() => {
                modal.classList.add('pointer-events-none', 'invisible');
            }, 250);
        }
    }
    window.openCustomerDrawer = openCustomerDrawer;
    window.closeCustomerDrawer = closeCustomerDrawer;

    function updateDrawerProfileUI() {
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        const isPengunjung = window.location.pathname === '/pengunjung' || 
                             window.location.pathname.includes('/pengunjung') || 
                             document.body.getAttribute('data-is-pengunjung') === 'true';

        const avatarEl = document.getElementById('drawerAvatarInitial');
        const nameEl = document.getElementById('drawerUserName');
        const statusEl = document.getElementById('drawerUserStatus');
        const actionBtn = document.getElementById('drawerUserActionBtn');
        const guestMenu = document.getElementById('drawerMenuGuest');
        const custMenu = document.getElementById('drawerMenuCustomer');
        const footer = document.getElementById('drawerFooterAction');

        const isUserLoggedIn = !!(user && user.isLoggedIn);

        if (isPengunjung) {
            // Tampilan Drawer saat di mode pengunjung publik
            if (isUserLoggedIn && user.role === 'customer') {
                const initial = user.name ? user.name.charAt(0).toUpperCase() : 'C';
                if (avatarEl) avatarEl.innerHTML = `<span class="font-black">${initial}</span>`;
                if (nameEl) nameEl.innerText = user.name;
                if (statusEl) statusEl.innerHTML = `<span class="text-sky-400 font-semibold">⭐ Mode Pengunjung Publik</span>`;
                if (actionBtn) {
                    actionBtn.classList.remove('hidden');
                    actionBtn.innerHTML = `
                        <div class="flex items-center justify-between gap-2 mt-1">
                            <a href="{{ route('customer.dashboard') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.dashboard') }}')" class="text-[9.5px] text-sky-300 hover:text-white font-bold bg-[#173B67] px-2.5 py-1 rounded-lg border border-sky-400/40 inline-flex items-center gap-1">
                                <i class="fa-solid fa-gauge text-[9px]"></i> Buka Dashboard
                            </a>
                        </div>
                    `;
                }
                if (guestMenu) guestMenu.classList.remove('hidden');
                if (custMenu) custMenu.classList.add('hidden');
                if (footer) {
                    footer.classList.remove('hidden');
                    footer.innerHTML = `
                        <a href="{{ route('customer.dashboard') }}" onclick="handleDrawerNavClick(event, '{{ route('customer.dashboard') }}')" class="w-full py-2 bg-sky-600/30 hover:bg-sky-600/50 text-sky-200 border border-sky-500/40 font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5 mb-2 text-xs">
                            <i class="fa-solid fa-gauge"></i> Ke Dashboard Customer
                        </a>
                        <button type="button" onclick="closeCustomerDrawer(); logoutUserAction();" class="w-full py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5 text-xs">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar dari Akun
                        </button>
                    `;
                }
            } else {
                if (avatarEl) avatarEl.innerHTML = `<i class="fa-solid fa-user"></i>`;
                if (nameEl) nameEl.innerText = 'Pengunjung Umum';
                if (statusEl) statusEl.innerText = 'Akses Publik Layanan';
                if (actionBtn) {
                    actionBtn.classList.remove('hidden');
                    actionBtn.innerHTML = `
                        <button type="button" onclick="closeCustomerDrawer(); openModal('modalLoginCustomer');" class="mt-1 text-[9.5px] text-sky-300 hover:text-white font-bold bg-[#173B67] px-2.5 py-1 rounded-lg border border-sky-400/40 inline-flex items-center gap-1">
                            <i class="fa-solid fa-right-to-bracket text-[9px]"></i> Masuk Akun
                        </button>
                    `;
                }
                if (guestMenu) guestMenu.classList.remove('hidden');
                if (custMenu) custMenu.classList.add('hidden');
                if (footer) {
                    footer.classList.add('hidden');
                    footer.innerHTML = '';
                }
            }
            return;
        }

        if (isUserLoggedIn) {
            // Tampilan Drawer Ketika Login
            if (user.role === 'mitra') {
                if (avatarEl) avatarEl.innerHTML = `<i class="fa-solid fa-helmet-safety text-emerald-300"></i>`;
                if (nameEl) nameEl.innerText = user.name;
                if (statusEl) statusEl.innerHTML = `<span class="text-emerald-400 font-semibold">● Mitra Aktif</span>`;
            } else {
                const initial = user.name ? user.name.charAt(0).toUpperCase() : 'C';
                if (avatarEl) avatarEl.innerHTML = `<span class="font-black">${initial}</span>`;
                if (nameEl) nameEl.innerText = user.name;
                if (statusEl) statusEl.innerHTML = `<span class="text-sky-400 font-semibold">⭐ Member Terverifikasi</span>`;
            }
            if (actionBtn) {
                actionBtn.classList.remove('hidden');
                actionBtn.innerHTML = `
                    <div class="flex items-center justify-between gap-2 mt-1">
                        <span class="text-[9.5px] text-slate-300 font-mono">${user.phone || ''}</span>
                        <button type="button" onclick="closeCustomerDrawer(); switchCustomerNavTab('profile');" class="text-[9px] text-sky-400 hover:text-sky-300 font-bold underline">
                            Lihat Profil
                        </button>
                    </div>
                `;
            }
            if (guestMenu) guestMenu.classList.add('hidden');
            if (custMenu) custMenu.classList.remove('hidden');

            if (footer) {
                footer.classList.remove('hidden');
                footer.innerHTML = `
                    <button type="button" onclick="closeCustomerDrawer(); logoutUserAction();" class="w-full py-2.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar dari Akun
                    </button>
                `;
            }
        } else {
            // Tampilan Pengunjung Umum: identitas sederhana, tanpa tombol masuk di drawer
            if (avatarEl) avatarEl.innerHTML = `<i class="fa-solid fa-user"></i>`;
            if (nameEl) nameEl.innerText = 'Pengunjung Umum';
            if (statusEl) statusEl.innerText = 'Akses Publik Layanan';
            if (actionBtn) {
                actionBtn.classList.add('hidden');
                actionBtn.innerHTML = '';
            }

            // Menu Pengunjung: hanya Cara Kerja, Syarat & Ketentuan, Kebijakan Privasi
            if (guestMenu) guestMenu.classList.remove('hidden');
            if (custMenu) custMenu.classList.add('hidden');

            // Footer Pengunjung: TANPA tombol masuk
            if (footer) {
                footer.classList.add('hidden');
                footer.innerHTML = '';
            }
        }
    }

    function handleDrawerHomeClick() {
        closeCustomerDrawer();
        const isPengunjung = window.location.pathname === '/pengunjung' || 
                             window.location.pathname.includes('/pengunjung') || 
                             document.body.getAttribute('data-is-pengunjung') === 'true';

        if (isPengunjung) {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo("{{ route('customer.index') }}");
            } else {
                window.location.href = "{{ route('customer.index') }}";
            }
            return;
        }

        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (user && user.isLoggedIn && user.role === 'customer') {
            if (window.location.pathname.includes('/customer/dashboard')) {
                if (typeof switchCustomerTab === 'function') {
                    switchCustomerTab('dashboard');
                }
            } else if (typeof spaNavigateTo === 'function') {
                spaNavigateTo("{{ route('customer.dashboard') }}");
            } else {
                window.location.href = "{{ route('customer.dashboard') }}";
            }
        } else {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo("{{ route('customer.index') }}");
            } else {
                window.location.href = "{{ route('customer.index') }}";
            }
        }
    }

    // Cegah background touchmove pada backdrop & bind tombol Escape
    document.addEventListener('DOMContentLoaded', function() {
        const custBackdrop = document.getElementById('customerDrawerBackdrop');
        if (custBackdrop) {
            custBackdrop.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, { passive: false });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('customerBurgerModal');
            if (modal && !modal.classList.contains('invisible')) {
                closeCustomerDrawer();
            }
        }
    });
</script>

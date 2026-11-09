{{-- =========================================================================
     SAYABANTU.COM - UNIFIED ADMIN HEADER COMPONENT (PERSIS SCREENSHOT)
     Header seragam di SELURUH halaman admin:
     [Burger] SayaBantu.com ---------------------- [Bell Notifikasi] [Avatar A]
     Tanpa tambahan status LIVE, tanpa judul halaman di baris ini.
========================================================================== --}}

<header class="relative z-10 px-5 pt-4">
    <div class="header-content flex items-center justify-between">
        
        {{-- Sisi Kiri: Burger Menu + Brand SayaBantu.com --}}
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                class="header-icon shrink-0 cursor-pointer"
                aria-label="Menu Navigasi"
                onclick="if(typeof window.openBurgerModal === 'function') { window.openBurgerModal(); } else if(typeof window.openSbBurger === 'function') { window.openSbBurger(); }"
                title="Buka Navigasi"
            >
                <i class="fa-solid fa-bars text-[16px]"></i>
            </button>

            <h1 class="text-[17px] font-bold tracking-tight text-white leading-none">
                SayaBantu.com
            </h1>
        </div>

        {{-- Sisi Kanan: Notifikasi (dengan dot) + Avatar "A" Lingkaran --}}
        <div class="flex items-center gap-2 shrink-0">
            
            {{-- Notification Bell Button --}}
            <button
                type="button"
                id="sbNotifHeaderBtn"
                class="header-icon relative shrink-0 cursor-pointer"
                aria-label="Notifikasi Realtime"
                onclick="if(typeof window.openNotificationModal === 'function') { window.openNotificationModal(); } else if(typeof window.openSbNotifSheet === 'function') { window.openSbNotifSheet(); }"
                title="Pusat Notifikasi"
            >
                <i class="fa-regular fa-bell text-[16px]"></i>
                <span id="sbNotifBadge" class="notification-dot" style="display: none;"></span>
                {{-- Indikator sinkron realtime (diperbarui oleh admin-realtime.js) --}}
                <span id="sbLiveIndicator" class="hidden absolute -bottom-1 left-1/2 -translate-x-1/2"></span>
            </button>

            {{-- Avatar Profile "A" Circle Button --}}
            <button
                type="button"
                id="sbAvatarHeaderBtn"
                class="profile-admin shrink-0 cursor-pointer"
                aria-label="Profil Admin"
                onclick="if(typeof window.openProfileQuickModal === 'function') { window.openProfileQuickModal(); } else if(typeof window.openSbAvatarMenu === 'function') { window.openSbAvatarMenu(); }"
                title="Menu Akun Admin"
            >
                A
            </button>

        </div>

    </div>
</header>

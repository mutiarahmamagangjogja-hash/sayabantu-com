{{-- =========================================================================
     SAYABANTU.COM - GLOBAL HEADER MODALS & INTERACTIVE OVERLAYS
     Burger Menu Drawer, Panel Notifikasi, Menu Avatar Circle "A",
     Modal Ganti Sandi, Modal Konfirmasi Keluar, & Toast Notifications
========================================================================== --}}

<style>
    /* Transisi Halus Modal & Drawer */
    .sb-overlay-backdrop {
        transition: opacity 0.22s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.22s;
    }
    .sb-drawer-panel {
        transition: transform 0.26s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sb-sheet-panel {
        transition: transform 0.24s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.24s;
    }
    .sb-toast {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .sb-pulse-green {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6);
        animation: sb-pulse 2s infinite;
    }
    @keyframes sb-pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6); }
        70% { box-shadow: 0 0 0 7px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }
    #sbBurgerPanel {
        overscroll-behavior: contain;
    }
</style>

{{-- =====================================================
     1. BURGER MENU DRAWER (SLIDE-IN DARI KIRI)
====================================================== --}}
<div id="sbBurgerModal" class="sb-overlay-backdrop fixed inset-0 z-[120] pointer-events-none opacity-0 invisible" aria-hidden="true">
    {{-- Backdrop Hitam Semi-Transparan --}}
    <div class="sb-backdrop-click absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>

    {{-- Kontainer Drawer Samping di Dalam Ukuran Mobile Frame --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none overflow-hidden">
        <div id="sbBurgerPanel" class="sb-drawer-panel absolute left-0 top-0 bottom-0 w-[82%] max-w-[340px] bg-[#0E1D31] text-white pointer-events-auto flex flex-col shadow-2xl -translate-x-full border-r border-white/10 z-10">
            
            {{-- Header Drawer --}}
            <div class="p-5 border-b border-white/10 flex items-center justify-between bg-gradient-to-b from-[#173B67]/40 to-transparent">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#173B67] border border-sky-400/30 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        <i class="fa-solid fa-shield-halved text-sky-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold tracking-tight text-white flex items-center gap-1.5 leading-none">
                            SayaBantu<span class="text-white">.com</span>
                        </h3>
                        <p class="text-[10px] text-gray-400 mt-1 font-medium">Panel Kendali Admin</p>
                    </div>
                </div>
                <button type="button" class="sb-close-btn w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 active:scale-95 text-gray-300 hover:text-white flex items-center justify-center transition-colors" data-target="sbBurgerModal" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Kartu Admin Ringkas --}}
            <div class="px-5 py-4 bg-white/[0.03] border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#173B67] to-[#1E4E8C] text-white font-bold text-base flex items-center justify-center shadow-lg border border-sky-400/40">
                            A
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-[#0E1D31] sb-pulse-green"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-sm font-bold text-white truncate">Admin SayaBantu</h4>
                            <i class="fa-solid fa-circle-check text-sky-400 text-[11px]" title="Terverifikasi"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 truncate">admin@sayabantu.com</p>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-[9px] text-gray-400">#ADM-0042</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Menu Navigasi (Scrollable) --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar px-3 py-4 space-y-1">
                
                {{-- Kelompok 1: Pengelolaan Platform --}}
                <div class="px-3 pb-1 text-[10px] font-semibold tracking-wider text-sky-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-[9px]"></i>
                    <span>Pengelolaan Platform</span>
                </div>

                {{-- 1. Manajemen Layanan --}}
                <a href="{{ route('admin.layanan') }}" data-nav="layanan" data-icon-default="text-sky-300" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.layanan') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.layanan*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-list-check w-5 text-center text-sm {{ request()->routeIs('admin.layanan*') ? 'text-sky-400' : 'text-sky-300' }}"></i>
                    <span class="text-xs flex-1">Manajemen Layanan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                        18 Layanan
                    </span>
                </a>

                {{-- 2. Konfirmasi Pekerjaan --}}
                <a href="{{ route('admin.konfirmasi') }}" data-nav="konfirmasi" data-icon-default="text-amber-400" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.konfirmasi') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.konfirmasi*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-clipboard-check w-5 text-center text-sm {{ request()->routeIs('admin.konfirmasi*') ? 'text-sky-400' : 'text-amber-400' }}"></i>
                    <span class="text-xs flex-1">Konfirmasi Pekerjaan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        6 Perlu Cek
                    </span>
                </a>

                {{-- 3. Pengaduan --}}
                <a href="{{ route('admin.pengaduan') }}" data-nav="pengaduan" data-icon-default="text-rose-400" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.pengaduan') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pengaduan*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-triangle-exclamation w-5 text-center text-sm {{ request()->routeIs('admin.pengaduan*') ? 'text-sky-400' : 'text-rose-400' }}"></i>
                    <span class="text-xs flex-1">Pengaduan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        2 Tiket
                    </span>
                </a>

                {{-- 4. Monitoring Chat --}}
                <a href="{{ route('admin.chat') }}" data-nav="chat" data-icon-default="text-emerald-400" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.chat') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.chat*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-comments w-5 text-center text-sm {{ request()->routeIs('admin.chat*') ? 'text-sky-400' : 'text-emerald-400' }}"></i>
                    <span class="text-xs flex-1">Monitoring Chat</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </a>

                {{-- 5. Moderasi & Kepatuhan --}}
                <a href="{{ route('admin.moderasi') }}" data-nav="moderasi" data-icon-default="text-purple-400" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.moderasi') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.moderasi*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-scale-balanced w-5 text-center text-sm {{ request()->routeIs('admin.moderasi*') ? 'text-sky-400' : 'text-purple-400' }}"></i>
                    <span class="text-xs flex-1">Moderasi & Kepatuhan</span>
                </a>

                {{-- Kelompok 2: Sistem --}}
                <div class="px-3 pt-4 pb-1 text-[10px] font-semibold tracking-wider text-gray-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-gear text-[9px]"></i>
                    <span>Sistem</span>
                </div>

                {{-- 6. Laporan & Analitik --}}
                <a href="{{ route('admin.laporan') }}" data-nav="laporan" data-icon-default="text-blue-400" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.laporan') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.laporan*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-sm {{ request()->routeIs('admin.laporan*') ? 'text-sky-400' : 'text-blue-400' }}"></i>
                    <span class="text-xs flex-1">Laporan & Analitik</span>
                </a>

                {{-- 7. Pengaturan --}}
                <a href="{{ route('admin.pengaturan') }}" data-nav="pengaturan" data-icon-default="text-gray-300" onclick="handleAdminDrawerNavClick(event, '{{ route('admin.pengaturan') }}')" class="admin-drawer-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.pengaturan*') ? 'bg-[#173B67] text-white font-bold shadow-md border-l-2 border-sky-400' : 'text-gray-200 hover:bg-white/10' }}">
                    <i class="fa-solid fa-sliders w-5 text-center text-sm {{ request()->routeIs('admin.pengaturan*') ? 'text-sky-400' : 'text-gray-300' }}"></i>
                    <span class="text-xs flex-1">Pengaturan</span>
                </a>

                <div class="pt-2"></div>

                {{-- Opsi Keluar (Paling Bawah) --}}
                <button type="button" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-rose-300 hover:bg-rose-500/20 transition-all text-left mt-2 border border-rose-500/20" onclick="closeBurgerModal(); setTimeout(openLogoutModal, 150);">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center text-sm text-rose-400"></i>
                    <span class="text-xs font-semibold flex-1">Keluar</span>
                </button>

            </div>

            {{-- Footer Drawer --}}
            <div class="p-4 border-t border-white/10 bg-black/20 text-[10px] text-gray-400 flex items-center justify-between">
                <span>SayaBantu Admin v1.2.0</span>
                <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Server Online
                </span>
            </div>

        </div>
    </div>
</div>


{{-- =====================================================
     2. PANEL NOTIFIKASI INTERAKTIF (DRAWER / SHEET)
====================================================== --}}
<div id="sbNotificationModal" class="sb-overlay-backdrop fixed inset-0 z-[120] pointer-events-none opacity-0 invisible" aria-hidden="true">
    {{-- Backdrop Hitam Semi-Transparan --}}
    <div class="sb-backdrop-click absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>

    {{-- Kontainer Panel Notifikasi Sesuai Frame Mobile --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="sbNotificationPanel" class="sb-sheet-panel relative w-full max-h-[85vh] bg-[#0E1D31] text-white pointer-events-auto rounded-t-[26px] shadow-2xl flex flex-col border-t border-white/15 translate-y-full">
            
            {{-- Header Notifikasi --}}
            <div class="px-5 pt-4 pb-3 border-b border-white/10">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#173B67] text-sky-400 flex items-center justify-center text-sm border border-sky-400/30">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-none">Notifikasi Admin</h3>
                            <p class="text-[10px] text-gray-400 mt-1">Pemberitahuan aktivitas platform</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" id="markAllReadBtn" onclick="markAllNotificationsAsRead()" class="text-[10px] font-semibold text-sky-300 hover:text-white bg-[#173B67] hover:bg-[#1E4E8C] px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1 border border-sky-400/30">
                            <i class="fa-solid fa-check-double text-[9px]"></i>
                            <span>Tandai Semua Dibaca</span>
                        </button>
                        <button type="button" class="sb-close-btn w-7 h-7 rounded-lg bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white flex items-center justify-center transition-colors" data-target="sbNotificationModal">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                {{-- Tabs Filter Notifikasi --}}
                <div class="flex items-center gap-1.5 mt-3 pt-1 overflow-x-auto custom-scrollbar text-[10px]">
                    <button type="button" class="sb-notif-tab active px-3 py-1 rounded-full font-semibold transition-colors bg-[#173B67] text-white border border-sky-400/40" data-filter="all">
                        Semua (<span id="notifCountTotal">5</span>)
                    </button>
                    <button type="button" class="sb-notif-tab px-3 py-1 rounded-full font-medium transition-colors bg-white/10 text-gray-300 hover:bg-white/20" data-filter="pesanan">
                        Pesanan
                    </button>
                    <button type="button" class="sb-notif-tab px-3 py-1 rounded-full font-medium transition-colors bg-white/10 text-gray-300 hover:bg-white/20" data-filter="pembayaran">
                        Pembayaran
                    </button>
                    <button type="button" class="sb-notif-tab px-3 py-1 rounded-full font-medium transition-colors bg-white/10 text-gray-300 hover:bg-white/20" data-filter="sistem">
                        Sistem
                    </button>
                </div>
            </div>

            {{-- List Notifikasi Item --}}
            <div id="notificationList" class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-2 max-h-[55vh]">

                {{-- Item 1: Pesanan Baru --}}
                <div class="sb-notif-item unread p-3 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] border border-white/10 transition-all cursor-pointer flex items-start gap-3" data-category="pesanan" onclick="clickNotifItem(this, '{{ route('admin.pesanan') }}', 'Pesanan Baru', 'Membuka pesanan #SB-2026-089')">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 shrink-0 flex items-center justify-center text-sm border border-blue-500/30 mt-0.5">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-white truncate">Pesanan Baru #SB-2026-089</h4>
                            <span class="text-[9px] text-sky-400 font-semibold shrink-0">2 mnt lalu</span>
                        </div>
                        <p class="text-[11px] text-gray-300 mt-1 leading-snug">
                            Layanan <strong class="text-white">Full Home Cleaning</strong> dari Budi S. menunggu konfirmasi mitra.
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 font-medium">Pesanan Masuk</span>
                            <span class="unread-badge w-2 h-2 rounded-full bg-sky-400"></span>
                        </div>
                    </div>
                </div>

                {{-- Item 2: Pembayaran Sukses --}}
                <div class="sb-notif-item unread p-3 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] border border-white/10 transition-all cursor-pointer flex items-start gap-3" data-category="pembayaran" onclick="clickNotifItem(this, '{{ route('admin.pembayaran') }}', 'Pembayaran Terverifikasi', 'Rp 450.000 via BCA VA telah diverifikasi')">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 shrink-0 flex items-center justify-center text-sm border border-emerald-500/30 mt-0.5">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-white truncate">Pembayaran Lunas #PAY-8821</h4>
                            <span class="text-[9px] text-sky-400 font-semibold shrink-0">15 mnt lalu</span>
                        </div>
                        <p class="text-[11px] text-gray-300 mt-1 leading-snug">
                            Dana <strong class="text-emerald-400">Rp 450.000</strong> pesanan #SB-2026-085 otomatis diverifikasi via BCA VA.
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-medium">Terverifikasi</span>
                            <span class="unread-badge w-2 h-2 rounded-full bg-sky-400"></span>
                        </div>
                    </div>
                </div>

                {{-- Item 3: Konflik Pelanggan --}}
                <div class="sb-notif-item unread p-3 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] border border-white/10 transition-all cursor-pointer flex items-start gap-3" data-category="pesanan" onclick="clickNotifItem(this, '{{ route('admin.konflik') }}', 'Konflik Baru', 'Keluhan keterlambatan mitra pada tiket KF-104')">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 shrink-0 flex items-center justify-center text-sm border border-rose-500/30 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-white truncate">Keluhan Tiket KF-104</h4>
                            <span class="text-[9px] text-sky-400 font-semibold shrink-0">1 jam lalu</span>
                        </div>
                        <p class="text-[11px] text-gray-300 mt-1 leading-snug">
                            Pelanggan Rina W. melaporkan mitra terlambat lebih dari 45 menit.
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-rose-500/20 text-rose-300 font-medium">Perlu Mediasi</span>
                            <span class="unread-badge w-2 h-2 rounded-full bg-sky-400"></span>
                        </div>
                    </div>
                </div>

                {{-- Item 4: Verifikasi Mitra --}}
                <div class="sb-notif-item unread p-3 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] border border-white/10 transition-all cursor-pointer flex items-start gap-3" data-category="sistem" onclick="clickNotifItem(this, null, 'Mitra Baru', 'Dokumen KTP & SKCK Siti Rahmawati siap diperiksa')">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 shrink-0 flex items-center justify-center text-sm border border-purple-500/30 mt-0.5">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-white truncate">Verifikasi Mitra Baru</h4>
                            <span class="text-[9px] text-gray-400 font-medium shrink-0">3 jam lalu</span>
                        </div>
                        <p class="text-[11px] text-gray-300 mt-1 leading-snug">
                            Mitra <strong class="text-white">Siti Rahmawati</strong> mengunggah berkas KTP & SKCK untuk kategori Cleaning Service.
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-medium">Verifikasi Berkas</span>
                            <span class="unread-badge w-2 h-2 rounded-full bg-sky-400"></span>
                        </div>
                    </div>
                </div>

                {{-- Item 5: Backup Sistem (Sudah Dibaca) --}}
                <div class="sb-notif-item p-3 rounded-2xl bg-white/[0.02] hover:bg-white/[0.06] border border-white/5 opacity-70 transition-all cursor-pointer flex items-start gap-3" data-category="sistem" onclick="clickNotifItem(this, null, 'Backup Sistem', 'Backup data harian sebesar 24.8 MB tersimpan aman di cloud storage')">
                    <div class="w-9 h-9 rounded-xl bg-gray-500/20 text-gray-400 shrink-0 flex items-center justify-center text-sm border border-gray-500/30 mt-0.5">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-semibold text-gray-300 truncate">Backup Basis Data Selesai</h4>
                            <span class="text-[9px] text-gray-400 shrink-0">6 jam lalu</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 leading-snug">
                            Snapshot harian basis data berhasil diunggah ke cloud (24.8 MB).
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-white/10 text-gray-400 font-medium">Sistem Rutin</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Notifikasi --}}
            <div class="p-3 border-t border-white/10 text-center bg-black/20">
                <button type="button" class="text-xs font-semibold text-sky-400 hover:underline inline-flex items-center gap-1.5" onclick="showToast('Notifikasi', 'Seluruh arsip notifikasi bulan ini telah dimuat.', 'info');">
                    <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                    Lihat Riwayat Notifikasi Lengkap
                </button>
            </div>

        </div>
    </div>
</div>


{{-- =====================================================
     3. MENU AVATAR CIRCLE "A" (DROPDOWN / QUICK POPUP)
====================================================== --}}
<div id="sbProfileQuickModal" class="sb-overlay-backdrop fixed inset-0 z-[120] pointer-events-none opacity-0 invisible" aria-hidden="true">
    {{-- Backdrop Hitam Semi-Transparan --}}
    <div class="sb-backdrop-click absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>

    {{-- Kontainer Dropdown Menu --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-start px-4 pt-16">
        <div id="sbProfileQuickPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-[24px] shadow-2xl border border-white/15 overflow-hidden scale-95 opacity-0 transition-all duration-200">
            
            {{-- Header Profil --}}
            <div class="p-5 bg-gradient-to-br from-[#173B67] to-[#0E1D31] border-b border-white/10 relative">
                <button type="button" class="sb-close-btn absolute top-4 right-4 w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors" data-target="sbProfileQuickModal">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>

                <div class="flex items-center gap-3.5">
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#173B67] to-[#1E4E8C] text-white font-bold text-2xl flex items-center justify-center shadow-lg border border-sky-400/40">
                            A
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-[#0E1D31] flex items-center justify-center text-[8px] text-white font-bold" title="Online">
                            <i class="fa-solid fa-check text-[7px]"></i>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-base font-bold text-white truncate">Admin SayaBantu</h3>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">ADMIN</span>
                        </div>
                        <p class="text-xs text-gray-300 truncate">admin@sayabantu.com</p>
                        <p class="text-[10px] text-sky-400 mt-0.5 flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-shield-halved text-[9px]"></i>
                            Akses Penuh
                        </p>
                    </div>
                </div>

                {{-- Status Bar Sesi --}}
                <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-between text-[10px] text-gray-300">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-clock text-sky-400"></i> Sesi Aktif: <strong class="text-white">2 jam 18 mnt</strong>
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">
                        Status Aman
                    </span>
                </div>
            </div>

            {{-- Pilihan Menu Cepat --}}
            <div class="p-3 space-y-1 bg-[#0B1727]">
                
                {{-- Ke Halaman Profile --}}
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-gray-200 hover:bg-white/10 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-[#173B67] text-sky-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform border border-sky-400/20">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-bold text-white">Profil & Pengaturan Admin</div>
                        <div class="text-[10px] text-gray-400">Kelola keamanan & preferensi sistem</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:translate-x-0.5 transition-transform"></i>
                </a>

                {{-- Ubah Kata Sandi --}}
                <button type="button" class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-gray-200 hover:bg-white/10 transition-all text-left group" onclick="closeProfileQuickModal(); setTimeout(openPasswordModal, 150);">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-bold text-white">Ubah Kata Sandi</div>
                        <div class="text-[10px] text-gray-400">Perbarui kata sandi akun admin</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:translate-x-0.5 transition-transform"></i>
                </button>

                {{-- Toggle Mode Tampilan --}}
                <div class="flex items-center justify-between px-3.5 py-3 rounded-xl text-gray-200 hover:bg-white/10 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-moon"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Mode Tampilan</div>
                            <div class="text-[10px] text-gray-400" id="themeStatusText">Tema Default SayaBantu (Gelap)</div>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="themeToggleCheckbox" class="sr-only peer" checked onchange="toggleAppTheme(this)">
                        <div class="w-9 h-5 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#173B67] border border-sky-400/40"></div>
                    </label>
                </div>

                {{-- Info Autentikasi 2FA --}}
                <div class="flex items-center justify-between px-3.5 py-3 rounded-xl text-gray-200 hover:bg-white/10 transition-all cursor-pointer" onclick="closeProfileQuickModalDirect(); adminSpaNavigateTo('{{ route('admin.profile') }}', true);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Autentikasi 2FA</div>
                            <div class="text-[10px] text-emerald-400 font-medium">Aktif • Perlindungan Maksimal</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                </div>

                {{-- Tombol Keluar --}}
                <button type="button" class="w-full flex items-center gap-3 px-3.5 py-3 rounded-xl text-rose-300 hover:bg-rose-500/20 transition-all text-left group mt-1" onclick="closeProfileQuickModal(); setTimeout(openLogoutModal, 150);">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-bold text-rose-400">Keluar dari Akun</div>
                        <div class="text-[10px] text-rose-300/70">Akhiri sesi aktif di perangkat ini</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-rose-400/50 group-hover:translate-x-0.5 transition-transform"></i>
                </button>

            </div>

        </div>
    </div>
</div>


{{-- =====================================================
     4. MODAL UBAH KATA SANDI INTERAKTIF
====================================================== --}}
<div id="sbPasswordModal" class="sb-overlay-backdrop fixed inset-0 z-[130] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/65 backdrop-blur-[2px]"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-center px-5">
        <div id="sbPasswordPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-[24px] shadow-2xl border border-white/15 p-5 scale-95 opacity-0 transition-all duration-200">
            
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#173B67] text-sky-400 flex items-center justify-center text-sm border border-sky-400/30">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white leading-none">Ubah Kata Sandi</h3>
                        <p class="text-[10px] text-gray-400 mt-1">Gunakan kombinasi minimal 8 karakter</p>
                    </div>
                </div>
                <button type="button" class="sb-close-btn w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors" data-target="sbPasswordModal">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="changePasswordForm" onsubmit="handlePasswordSubmit(event)" class="mt-4 space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" id="currentPassInput" required placeholder="Masukkan kata sandi lama" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400 pr-9 transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('currentPassInput', this)" class="absolute right-3 top-2.5 text-gray-400 hover:text-white text-xs">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" id="newPassInput" required minlength="8" placeholder="Minimal 8 karakter unik" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400 pr-9 transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('newPassInput', this)" class="absolute right-3 top-2.5 text-gray-400 hover:text-white text-xs">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" id="confirmPassInput" required minlength="8" placeholder="Ulangi kata sandi baru" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400 pr-9 transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('confirmPassInput', this)" class="absolute right-3 top-2.5 text-gray-400 hover:text-white text-xs">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="button" class="sb-close-btn flex-1 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-semibold text-gray-300 transition-colors" data-target="sbPasswordModal">
                        Batal
                    </button>
                    <button type="submit" id="savePasswordBtn" class="flex-1 py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] active:scale-95 text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-1.5 border border-sky-400/40">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Sandi
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- =====================================================
     5. MODAL KONFIRMASI KELUAR (LOGOUT)
====================================================== --}}
<div id="sbLogoutModal" class="sb-overlay-backdrop fixed inset-0 z-[130] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/70 backdrop-blur-[3px]"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-center px-6">
        <div id="sbLogoutPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-[24px] shadow-2xl border border-white/15 p-6 text-center scale-95 opacity-0 transition-all duration-200">
            
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 text-rose-400 mx-auto flex items-center justify-center text-2xl border border-rose-500/30 mb-4">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>

            <h3 class="text-base font-bold text-white">Yakin Ingin Keluar?</h3>
            <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                Sesi panel admin Anda akan diakhiri. Anda perlu memasukkan email & kata sandi lagi untuk login kembali.
            </p>

            <div class="mt-6 flex items-center gap-2.5">
                <button type="button" class="sb-close-btn flex-1 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-semibold text-gray-300 transition-colors" data-target="sbLogoutModal">
                    Tetap Masuk
                </button>
                <button type="button" onclick="confirmLogoutAction()" class="flex-1 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 active:scale-95 text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-power-off text-xs"></i>
                    Ya, Keluar
                </button>
            </div>

        </div>
    </div>
</div>


{{-- =====================================================
     6. TOAST NOTIFICATION CONTAINER GLOBAL
====================================================== --}}
<div id="sbToastContainer" class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] w-[90%] max-w-[400px] pointer-events-none flex flex-col gap-2 items-center"></div>


{{-- =====================================================
     7. JAVASCRIPT CONTROLLER LENGKAP & ANTI-MACET (BEBAS BUG TIMEOUT)
====================================================== --}}
<script data-layout-script="true">
    // State timer untuk mencegah auto-close race condition
    var burgerTimer = null;
    var notifTimer = null;
    var profileTimer = null;

    document.addEventListener('DOMContentLoaded', function() {
        bindHeaderButtons();
        bindBackdropAndEscape();
        preventPanelClickBubble();
    });

    // Cegah klik di dalam panel drawer memicu backdrop click
    function preventPanelClickBubble() {
        document.querySelectorAll('.sb-drawer-panel, .sb-sheet-panel').forEach(panel => {
            panel.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    }

    // Sambungkan tombol Burger, Notifikasi, dan Avatar Circle 'A'
    function bindHeaderButtons() {
        // Tombol Burger
        const burgerButtons = document.querySelectorAll('button[aria-label="Menu"], button[aria-label="Menu Navigasi"], [data-action="open-burger"]');
        burgerButtons.forEach(btn => {
            btn.setAttribute('type', 'button');
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                openBurgerModal();
            };
        });

        // Tombol Notifikasi
        const notifButtons = document.querySelectorAll('#sbNotifHeaderBtn, button[aria-label="Notifikasi"], button[aria-label="Notifikasi Realtime"], [data-action="open-notifications"]');
        notifButtons.forEach(btn => {
            btn.setAttribute('type', 'button');
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                openNotificationModal();
            };
        });

        // Tombol Avatar Circle 'A'
        const profileButtons = document.querySelectorAll('#sbAvatarHeaderBtn, .profile-admin, button[aria-label="Profil Admin"], [data-action="open-profile-menu"]');
        profileButtons.forEach(btn => {
            btn.setAttribute('type', 'button');
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                openProfileQuickModal();
            };
        });

        // Tombol Close umum
        document.querySelectorAll('.sb-close-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const targetId = this.getAttribute('data-target');
                if (targetId) {
                    closeModal(targetId);
                } else {
                    closeAllModals();
                }
            });
        });

        // Tab Filter Notifikasi
        document.querySelectorAll('.sb-notif-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.sb-notif-tab').forEach(t => {
                    t.classList.remove('active', 'bg-[#173B67]', 'text-white', 'border', 'border-sky-400/40', 'font-semibold');
                    t.classList.add('bg-white/10', 'text-gray-300');
                });
                this.classList.add('active', 'bg-[#173B67]', 'text-white', 'border', 'border-sky-400/40', 'font-semibold');
                this.classList.remove('bg-white/10', 'text-gray-300');

                const filter = this.getAttribute('data-filter');
                filterNotifications(filter);
            });
        });
    }

    // Kunci dan buka kunci scroll background admin saat burger menu dibuka
    function lockAdminBodyScroll() {
        document.documentElement.classList.add('overflow-hidden', 'sb-scroll-locked');
        document.body.classList.add('overflow-hidden', 'sb-scroll-locked');
    }

    function unlockAdminBodyScroll() {
        const anyModalOpen = document.querySelector('#sbBurgerModal.pointer-events-auto, #sbNotificationModal.pointer-events-auto, #sbProfileQuickModal.pointer-events-auto, #sbPasswordModal.pointer-events-auto, #sbLogoutModal.pointer-events-auto');
        if (!anyModalOpen) {
            document.documentElement.classList.remove('overflow-hidden', 'sb-scroll-locked');
            document.body.classList.remove('overflow-hidden', 'sb-scroll-locked');
        }
    }

    // Bind klik backdrop luar & tombol escape keyboard
    function bindBackdropAndEscape() {
        document.querySelectorAll('.sb-backdrop-click').forEach(bg => {
            bg.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllModals();
            });
            bg.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, { passive: false });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
            }
        });
    }

    /* =========================================================
       FUNGSI MODAL MANAGER (BUKA / TUTUP TANPA TIMEOUT BENTROK)
    ========================================================== */
    function openBurgerModal() {
        // Bersihkan timer pending agar tidak auto-close
        if (burgerTimer) { clearTimeout(burgerTimer); burgerTimer = null; }
        
        // Tutup modal lain secara instan tanpa mematikan modal ini
        closeNotificationModalDirect();
        closeProfileQuickModalDirect();

        const modal = document.getElementById('sbBurgerModal');
        const panel = document.getElementById('sbBurgerPanel');
        if (!modal || !panel) return;

        // .is-open adalah penanda tunggal status terbuka (lihat admin-components.css)
        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open', 'pointer-events-auto');
        panel.classList.remove('-translate-x-full');
        panel.classList.add('translate-x-0');
        modal.removeAttribute('aria-hidden');

        lockAdminBodyScroll();
        if (typeof syncAdminDrawerActiveNav === 'function') {
            syncAdminDrawerActiveNav();
        }
        if (typeof prefetchCoreAdminRoutes === 'function') {
            prefetchCoreAdminRoutes();
        }
    }

    function closeBurgerModal() {
        const modal = document.getElementById('sbBurgerModal');
        const panel = document.getElementById('sbBurgerPanel');
        if (!modal || !panel) return;

        panel.classList.add('-translate-x-full');
        panel.classList.remove('translate-x-0');
        modal.classList.remove('is-open', 'pointer-events-auto');
        modal.classList.add('pointer-events-none');

        if (burgerTimer) clearTimeout(burgerTimer);
        burgerTimer = setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100');
            modal.setAttribute('aria-hidden', 'true');
            burgerTimer = null;
            unlockAdminBodyScroll();
        }, 220);
    }

    function closeBurgerModalDirect() {
        if (burgerTimer) { clearTimeout(burgerTimer); burgerTimer = null; }
        const modal = document.getElementById('sbBurgerModal');
        const panel = document.getElementById('sbBurgerPanel');
        if (!modal || !panel) return;
        panel.classList.add('-translate-x-full');
        panel.classList.remove('translate-x-0');
        modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
        modal.setAttribute('aria-hidden', 'true');
        unlockAdminBodyScroll();
    }

    function syncAdminDrawerActiveNav(currentPath) {
        if (!currentPath) {
            try {
                currentPath = window.location.pathname;
            } catch(e) {
                currentPath = '';
            }
        }

        const allDrawerLinks = document.querySelectorAll('#sbBurgerPanel .admin-drawer-nav-item');
        allDrawerLinks.forEach(link => {
            const rawHref = link.getAttribute('href') || '';
            let linkPath = '';
            try {
                linkPath = new URL(rawHref, window.location.origin).pathname;
            } catch(e) {}

            const icon = link.querySelector('i');
            const defaultIconColor = link.getAttribute('data-icon-default') || 'text-sky-300';

            let isActive = false;
            if (linkPath && currentPath) {
                if (currentPath === linkPath || currentPath.startsWith(linkPath + '/') || (linkPath !== '/admin' && currentPath.includes(linkPath))) {
                    isActive = true;
                }
            }

            if (isActive) {
                link.classList.add('bg-[#173B67]', 'text-white', 'font-bold', 'shadow-md', 'border-l-2', 'border-sky-400');
                link.classList.remove('text-gray-200', 'hover:bg-white/10');
                if (icon) {
                    icon.classList.remove(defaultIconColor);
                    icon.classList.add('text-sky-400');
                }
            } else {
                link.classList.remove('bg-[#173B67]', 'text-white', 'font-bold', 'shadow-md', 'border-l-2', 'border-sky-400');
                link.classList.add('text-gray-200', 'hover:bg-white/10');
                if (icon) {
                    icon.classList.remove('text-sky-400');
                    icon.classList.add(defaultIconColor);
                }
            }
        });
    }
    window.syncAdminDrawerActiveNav = syncAdminDrawerActiveNav;

    function handleAdminDrawerNavClick(e, url) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        try {
            const local = (typeof window.toAdminLocalUrl === 'function') ? window.toAdminLocalUrl(url) : new URL(url, window.location.origin);
            if (local && typeof window.syncAdminDrawerActiveNav === 'function') {
                window.syncAdminDrawerActiveNav(local.pathname);
            }
        } catch(err) {}

        closeBurgerModalDirect();
        if (typeof window.adminSpaNavigateTo === 'function') {
            window.adminSpaNavigateTo(url, true);
        }
    }

    // Expose alias globally for inline onclick callers
    window.openBurgerModal = openBurgerModal;
    window.closeBurgerModal = closeBurgerModal;
    window.closeBurgerModalDirect = closeBurgerModalDirect;
    window.handleAdminDrawerNavClick = handleAdminDrawerNavClick;
    window.openSbBurger = openBurgerModal;
    window.closeSbBurger = closeBurgerModal;
    window.bindHeaderButtons = bindHeaderButtons;
    window.bindBackdropAndEscape = bindBackdropAndEscape;

    function openNotificationModal() {
        if (notifTimer) { clearTimeout(notifTimer); notifTimer = null; }

        closeBurgerModalDirect();
        closeProfileQuickModalDirect();

        const modal = document.getElementById('sbNotificationModal');
        const panel = document.getElementById('sbNotificationPanel');
        if (!modal || !panel) return;

        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open', 'pointer-events-auto');
        modal.removeAttribute('aria-hidden');
        panel.classList.remove('translate-y-full');
        panel.classList.add('translate-y-0');
    }

    function closeNotificationModal() {
        const modal = document.getElementById('sbNotificationModal');
        const panel = document.getElementById('sbNotificationPanel');
        if (!modal || !panel) return;

        panel.classList.add('translate-y-full');
        panel.classList.remove('translate-y-0');
        modal.classList.remove('is-open', 'pointer-events-auto');
        modal.classList.add('pointer-events-none');

        if (notifTimer) clearTimeout(notifTimer);
        notifTimer = setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.setAttribute('aria-hidden', 'true');
            modal.classList.remove('opacity-100');
            notifTimer = null;
        }, 220);
    }

    function closeNotificationModalDirect() {
        if (notifTimer) { clearTimeout(notifTimer); notifTimer = null; }
        const modal = document.getElementById('sbNotificationModal');
        const panel = document.getElementById('sbNotificationPanel');
        if (!modal || !panel) return;
        panel.classList.add('translate-y-full');
        panel.classList.remove('translate-y-0');
        modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
    }

    function openProfileQuickModal() {
        if (profileTimer) { clearTimeout(profileTimer); profileTimer = null; }

        closeBurgerModalDirect();
        closeNotificationModalDirect();

        const modal = document.getElementById('sbProfileQuickModal');
        const panel = document.getElementById('sbProfileQuickPanel');
        if (!modal || !panel) return;

        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open', 'pointer-events-auto');
        modal.removeAttribute('aria-hidden');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    }

    function closeProfileQuickModal() {
        const modal = document.getElementById('sbProfileQuickModal');
        const panel = document.getElementById('sbProfileQuickPanel');
        if (!modal || !panel) return;

        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        modal.classList.remove('is-open', 'pointer-events-auto');
        modal.classList.add('pointer-events-none');

        if (profileTimer) clearTimeout(profileTimer);
        profileTimer = setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.setAttribute('aria-hidden', 'true');
            modal.classList.remove('opacity-100');
            profileTimer = null;
        }, 200);
    }

    function closeProfileQuickModalDirect() {
        if (profileTimer) { clearTimeout(profileTimer); profileTimer = null; }
        const modal = document.getElementById('sbProfileQuickModal');
        const panel = document.getElementById('sbProfileQuickPanel');
        if (!modal || !panel) return;
        panel.classList.add('scale-95', 'opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
        modal.setAttribute('aria-hidden', 'true');
    }

    function openPasswordModal() {
        closeAllModals();
        const modal = document.getElementById('sbPasswordModal');
        const panel = document.getElementById('sbPasswordPanel');
        if (!modal || !panel) return;

        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open', 'pointer-events-auto');
        modal.removeAttribute('aria-hidden');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
        lockAdminBodyScroll();
    }

    function openLogoutModal() {
        closeAllModals();
        const modal = document.getElementById('sbLogoutModal');
        const panel = document.getElementById('sbLogoutPanel');
        if (!modal || !panel) return;

        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open', 'pointer-events-auto');
        modal.removeAttribute('aria-hidden');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
        lockAdminBodyScroll();
    }

    function closeModal(modalId) {
        if (modalId === 'sbBurgerModal') closeBurgerModal();
        else if (modalId === 'sbNotificationModal') closeNotificationModal();
        else if (modalId === 'sbProfileQuickModal') closeProfileQuickModal();
        else {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const panel = modal.querySelector('.sb-sheet-panel, .sb-drawer-panel');
            if (panel) {
                panel.classList.add('scale-95', 'opacity-0');
                panel.classList.remove('scale-100', 'opacity-100');
            }
            modal.classList.remove('is-open', 'pointer-events-auto');
            modal.classList.add('pointer-events-none');
            setTimeout(() => {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.setAttribute('aria-hidden', 'true');
                unlockAdminBodyScroll();
            }, 200);
        }
    }

    function closeAllModals() {
        closeBurgerModalDirect();
        closeNotificationModalDirect();
        closeProfileQuickModalDirect();
        ['sbPasswordModal', 'sbLogoutModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                const panel = modal.querySelector('.sb-sheet-panel');
                if (panel) {
                    panel.classList.add('scale-95', 'opacity-0');
                    panel.classList.remove('scale-100', 'opacity-100');
                }
                modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
        unlockAdminBodyScroll();
    }

    // Expose all functions to window for onclick caller access
    window.openNotificationModal = openNotificationModal;
    window.closeNotificationModal = closeNotificationModal;
    window.openSbNotifSheet = openNotificationModal;
    window.closeSbNotifSheet = closeNotificationModal;
    window.openProfileQuickModal = openProfileQuickModal;
    window.closeProfileQuickModal = closeProfileQuickModal;
    window.openSbAvatarMenu = openProfileQuickModal;
    window.closeSbAvatarMenu = closeProfileQuickModal;
    window.openPasswordModal = openPasswordModal;
    window.openLogoutModal = openLogoutModal;
    window.closeModal = closeModal;
    window.closeAllModals = closeAllModals;

    /* =========================================================
       FILTER & AKSI NOTIFIKASI
    ========================================================== */
    function filterNotifications(category) {
        const items = document.querySelectorAll('.sb-notif-item');
        let count = 0;
        items.forEach(item => {
            if (category === 'all' || item.getAttribute('data-category') === category) {
                item.style.display = 'flex';
                count++;
            } else {
                item.style.display = 'none';
            }
        });
        const countEl = document.getElementById('notifCountTotal');
        if (countEl) countEl.innerText = count;
    }

    function clickNotifItem(el, targetUrl, title, desc) {
        if (el.classList.contains('unread')) {
            el.classList.remove('unread');
            el.classList.add('opacity-75');
            const badge = el.querySelector('.unread-badge');
            if (badge) badge.remove();
        }

        updateNotificationDotBadge();

        if (targetUrl) {
            closeNotificationModalDirect();
            if (typeof window.adminSpaNavigateTo === 'function') {
                window.adminSpaNavigateTo(targetUrl, true);
            }
        } else {
            showToast(title, desc, 'success');
        }
    }

    function markAllNotificationsAsRead() {
        const unreadItems = document.querySelectorAll('.sb-notif-item.unread');
        unreadItems.forEach(item => {
            item.classList.remove('unread');
            item.classList.add('opacity-75');
            const badge = item.querySelector('.unread-badge');
            if (badge) badge.remove();
        });

        hideHeaderNotificationDot();
        showToast('Notifikasi', 'Semua notifikasi telah ditandai sebagai dibaca.', 'success');
    }

    function hideHeaderNotificationDot() {
        document.querySelectorAll('.notification-dot').forEach(dot => {
            dot.style.display = 'none';
        });
    }

    function updateNotificationDotBadge() {
        const remainingUnread = document.querySelectorAll('.sb-notif-item.unread').length;
        if (remainingUnread === 0) {
            hideHeaderNotificationDot();
        }
    }

    /* =========================================================
       FORM SUBMIT & PASSWORD MODAL
    ========================================================== */
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function handlePasswordSubmit(e) {
        e.preventDefault();
        const currentPass = document.getElementById('currentPassInput').value;
        const newPass = document.getElementById('newPassInput').value;
        const confirmPass = document.getElementById('confirmPassInput').value;
        const submitBtn = document.getElementById('savePasswordBtn');

        if (newPass !== confirmPass) {
            showToast('Validasi Gagal', 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.', 'error');
            return;
        }

        if (newPass.length < 8) {
            showToast('Validasi Gagal', 'Kata sandi minimal harus 8 karakter.', 'error');
            return;
        }

        const originalContent = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';

        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
            closeModal('sbPasswordModal');
            document.getElementById('changePasswordForm').reset();
            showToast('Berhasil!', 'Kata sandi akun Admin telah berhasil diperbarui.', 'success');
        }, 800);
    }

    /* =========================================================
       LOGOUT ACTION
    ========================================================== */
    function confirmLogoutAction() {
        closeModal('sbLogoutModal');
        showToast('Mengeluarkan Sesi', 'Sesi admin telah diakhiri. Mengalihkan ke login...', 'info');
        try {
            if (window.adminDomCache) window.adminDomCache.clear();
            localStorage.setItem('sb_admin_logged_out', 'true');
        } catch(e) {}
        setTimeout(() => {
            window.location.href = "{{ route('admin.logout') }}";
        }, 500);
    }

    /* =========================================================
       THEME TOGGLE
    ========================================================== */
    function toggleAppTheme(checkbox) {
        const text = document.getElementById('themeStatusText');
        if (checkbox.checked) {
            if (text) text.innerText = 'Tema Gelap Aktif (Dark Navy)';
            showToast('Tema Tampilan', 'Mode Gelap Navy SayaBantu aktif.', 'info');
        } else {
            if (text) text.innerText = 'Tema Terang Bersih (Light Clean)';
            showToast('Tema Tampilan', 'Mode Terang aktif.', 'info');
        }
    }

    /* =========================================================
       GLOBAL TOAST NOTIFICATION SYSTEM
    ========================================================== */
    function showToast(title, message, type = 'info') {
        const container = document.getElementById('sbToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'sb-toast w-full rounded-2xl p-3.5 shadow-2xl flex items-start gap-3 border pointer-events-auto transform -translate-y-4 opacity-0 text-white';

        let iconClass = 'fa-solid fa-circle-info text-sky-400';
        let bgStyle = 'background: rgba(14, 29, 49, 0.96); border-color: rgba(56, 189, 248, 0.3);';

        if (type === 'success') {
            iconClass = 'fa-solid fa-circle-check text-emerald-400';
            bgStyle = 'background: rgba(14, 29, 49, 0.96); border-color: rgba(52, 211, 153, 0.4);';
        } else if (type === 'error') {
            iconClass = 'fa-solid fa-circle-xmark text-rose-400';
            bgStyle = 'background: rgba(14, 29, 49, 0.96); border-color: rgba(244, 63, 94, 0.4);';
        } else if (type === 'warning') {
            iconClass = 'fa-solid fa-triangle-exclamation text-amber-400';
            bgStyle = 'background: rgba(14, 29, 49, 0.96); border-color: rgba(251, 191, 36, 0.4);';
        }

        toast.setAttribute('style', bgStyle + ' backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);');

        toast.innerHTML = `
            <div class="text-base shrink-0 mt-0.5"><i class="${iconClass}"></i></div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold text-white">${title}</div>
                <div class="text-[11px] text-gray-300 mt-0.5 leading-snug">${message}</div>
            </div>
            <button type="button" class="text-gray-400 hover:text-white text-xs p-1" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('-translate-y-4', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('-translate-y-4', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3500);
    }
    window.showToast = showToast;
</script>

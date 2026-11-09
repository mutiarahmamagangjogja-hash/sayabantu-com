{{-- =========================================================
     CUSTOMER HEADER - SAYABANTU.COM
========================================================= --}}

<header id="main_header" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all">
    {{-- BARIS ATAS: LOGO, SEARCH, ROLES, NOTIFIKASI --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between gap-4">
            
            {{-- LOGO BRAND --}}
            <a href="{{ route('customer.index') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0E1D31] to-[#173B67] flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform border border-sky-400/30">
                    <i class="fa-solid fa-handshake-angle text-sky-400 text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#0E1D31] flex items-center">
                        SayaBantu<span class="text-sky-500">.com</span>
                    </span>
                    <p class="text-[10px] text-slate-500 -mt-1 font-medium hidden sm:block">
                        Suruh apa aja siap melayani sepenuh hati
                    </p>
                </div>
            </a>

            {{-- SEARCH FORM (DESKTOP & TABLET) --}}
            <div class="hidden md:flex flex-1 max-w-lg mx-4">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input
                        type="text"
                        id="customerSearchInput"
                        placeholder="Mau cari bantuan apa? (Bersih-bersih, Pindahan, Antar Barang...)"
                        onkeyup="handleCustomerSearch(this.value)"
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-full border border-slate-200 bg-slate-50/80 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-100 transition-all shadow-inner"
                    >
                </div>
            </div>

            {{-- ACTION PILLS & USER BUTTONS --}}
            <div class="flex items-center gap-2 sm:gap-3">
                
                {{-- PILLS CUSTOMER & MITRA --}}
                <div class="flex items-center p-1 bg-slate-100 rounded-full border border-slate-200 text-xs font-bold">
                    <span class="px-3 py-1 rounded-full bg-[#173B67] text-white shadow-xs cursor-default">
                        Customer
                    </span>
                    <button 
                        type="button" 
                        onclick="openModalMitra()"
                        class="px-3 py-1 rounded-full text-slate-600 hover:text-slate-900 transition-colors"
                        title="Gabung Jadi Mitra Penyedia"
                    >
                        Mitra
                    </button>
                </div>

                {{-- TOMBOL NOTIFIKASI --}}
                <button
                    type="button"
                    onclick="toggleCustomerNotifModal()"
                    class="relative w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shrink-0"
                    title="Pemberitahuan"
                >
                    <i class="fa-regular fa-bell text-sm"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </button>

                {{-- SHORTCUT KE PANEL ADMIN --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900 hover:bg-[#173B67] text-white text-xs font-semibold shadow-sm transition-all"
                    title="Buka Panel Kontrol Administrator"
                >
                    <i class="fa-solid fa-shield-halved text-[11px] text-sky-400"></i>
                    <span>Panel Admin</span>
                </a>

                {{-- MOBILE HAMBURGER TOGGLE --}}
                <button
                    type="button"
                    id="mobileMenuToggleBtn"
                    onclick="toggleMobileMenu()"
                    class="md:hidden w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center hover:bg-slate-200 transition-colors"
                    aria-label="Menu"
                >
                    <i class="fa-solid fa-bars text-base"></i>
                </button>

            </div>

        </div>

        {{-- SEARCH FORM (MOBILE ONLY) --}}
        <div class="mt-2.5 md:hidden">
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input
                    type="text"
                    placeholder="Cari bantuan di SayaBantu..."
                    onkeyup="handleCustomerSearch(this.value)"
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-full border border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white transition-all"
                >
            </div>
        </div>

    </div>

    {{-- BARIS BAWAH: NAVIGASI UTAMA (DESKTOP) --}}
    <nav class="hidden md:block border-t border-slate-100 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <ul class="flex items-center gap-1 text-xs font-semibold text-slate-600 py-1.5">
                <li>
                    <a href="#banner_home" class="px-3.5 py-1.5 rounded-lg text-[#173B67] font-bold bg-sky-50/80 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-xs text-sky-600"></i> Beranda
                    </a>
                </li>
                <li>
                    <a href="#form-suruhan" class="px-3.5 py-1.5 rounded-lg hover:text-[#173B67] hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-pencil text-xs text-slate-400"></i> Buat Suruhan
                    </a>
                </li>
                <li>
                    <a href="#available-jobs" class="px-3.5 py-1.5 rounded-lg hover:text-[#173B67] hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-xs text-slate-400"></i> Available Services
                    </a>
                </li>
                <li>
                    <a href="#category_home" class="px-3.5 py-1.5 rounded-lg hover:text-[#173B67] hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-xs text-slate-400"></i> Kategori Jasa
                    </a>
                </li>
                <li>
                    <a href="#work_done" class="px-3.5 py-1.5 rounded-lg hover:text-[#173B67] hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-xs text-emerald-500"></i> Pekerjaan Selesai
                    </a>
                </li>
                <li>
                    <a href="#testimoni" class="px-3.5 py-1.5 rounded-lg hover:text-[#173B67] hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-xs text-yellow-400"></i> Testimoni
                    </a>
                </li>
                <li class="ml-auto">
                    <button type="button" onclick="openModalLacak()" class="px-3.5 py-1.5 rounded-lg text-sky-700 bg-sky-100/70 hover:bg-sky-100 font-bold transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-location-crosshairs text-xs"></i> Lacak Pesanan
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    {{-- MOBILE NAVIGATION DRAWER --}}
    <div id="mobileMenuDrawer" class="hidden md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-2 shadow-lg animate-fadeIn">
        <a href="#banner_home" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-bold text-[#173B67] bg-sky-50">
            <i class="fa-solid fa-house mr-2 text-sky-600"></i> Beranda
        </a>
        <a href="#form-suruhan" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-pencil mr-2 text-slate-400"></i> Buat Suruhan (Form Cepat)
        </a>
        <a href="#available-jobs" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-briefcase mr-2 text-slate-400"></i> Available Services
        </a>
        <a href="#category_home" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-layer-group mr-2 text-slate-400"></i> Pilihan Kategori
        </a>
        <a href="#work_done" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-circle-check mr-2 text-emerald-500"></i> Layanan Selesai
        </a>
        <a href="#testimoni" onclick="toggleMobileMenu()" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-star mr-2 text-yellow-400"></i> Testimoni Pelanggan
        </a>
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
            <button type="button" onclick="openModalLacak(); toggleMobileMenu();" class="flex-1 py-2 rounded-xl bg-sky-50 text-[#173B67] text-xs font-bold text-center">
                <i class="fa-solid fa-location-crosshairs mr-1"></i> Lacak Status
            </button>
            <a href="{{ route('admin.dashboard') }}" class="flex-1 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold text-center">
                <i class="fa-solid fa-shield-halved mr-1 text-sky-400"></i> Panel Admin
            </a>
        </div>
    </div>
</header>

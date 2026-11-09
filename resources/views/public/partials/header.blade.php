{{-- MOBILE APP HEADER --}}
<header class="bg-[#0E1D31] text-white px-4 pt-3 pb-4 shadow-md shrink-0 relative z-30">
    
    {{-- TOP BAR: BURGER + BRANDING + ACTIONS --}}
    <div class="flex items-center justify-between gap-2 mb-3">

        {{-- BURGER MENU + BRAND --}}
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                onclick="openCustomerDrawer()"
                class="header-icon shrink-0"
                aria-label="Menu Navigasi"
                title="Menu Navigasi"
            >
                <i class="fa-solid fa-bars text-[15px]"></i>
            </button>

            <h1 class="text-[17px] font-bold tracking-tight text-white leading-none">
                SayaBantu<span class="text-white">.com</span>
            </h1>
        </div>

        {{-- TOP RIGHT ACTION BUTTONS --}}
        <div class="flex items-center gap-1.5">

            {{-- ROLE SWITCHER PILL (CUSTOMER / MITRA) --}}
            <div class="bg-white/10 p-0.5 rounded-full border-white/15 flex items-center text-[10px] font-bold hidden">
                <button type="button" onclick="switchLoginRole('customer'); openModal('modalLoginCustomer');" class="px-2 py-0.5 rounded-full bg-sky-400 text-slate-950 shadow-xs">User</button>
                <button type="button" onclick="switchLoginRole('mitra'); openModal('modalLoginCustomer');" class="hidden">
                    Mitra
                </button>
            </div>

            {{-- IKON SEARCH --}}
            <button type="button" onclick="toggleHeaderSearch()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white flex items-center justify-center transition-all border border-white/10" title="Cari Layanan" aria-label="Pencarian">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </button>

            {{-- IKON NOTIFIKASI (HANYA UNTUK CUSTOMER YANG SUDAH LOGIN) --}}
            <button type="button" id="mobileHeaderNotifBtn" onclick="openModal('modalNotifCustomer')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white flex items-center justify-center transition-all border border-white/10 relative hidden" title="Pemberitahuan" aria-label="Notifikasi">
                <i class="fa-regular fa-bell text-xs"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-[#0E1D31]"></span>
            </button>

            {{-- TOMBOL MASUK (TAMU PUBLIK) --}}
            <button type="button" onclick="switchLoginRole('customer'); openModal('modalLoginCustomer');" id="mobileHeaderLoginBtn" class="px-3 py-1.5 rounded-full bg-white text-[#0E1D31] text-[10px] font-extrabold shadow-sm hover:bg-sky-100 transition-colors cursor-pointer">
                Masuk
            </button>

            {{-- PROFIL (SETELAH LOGIN) --}}
            <button type="button" onclick="handleAccountButtonClick()" id="mobileHeaderProfileBtn" class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-600 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-md border border-white/20 hidden" title="Akun Saya">
                <i class="fa-regular fa-user"></i>
            </button>
        </div>

    </div>

    {{-- SEARCH BAR (EXPANDABLE) --}}
    <div id="mobileHeaderSearchWrap" class="relative hidden">
        <div class="relative flex items-center">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-xs text-slate-400 pointer-events-none"></i>
            <input
                type="text"
                id="mobileSearchInput"
                onkeyup="filterMobileServices(this.value)"
                placeholder="Mau cari suruhan apa hari ini?"
                class="w-full bg-white text-slate-900 placeholder-slate-400 text-xs rounded-2xl pl-9 pr-9 py-2.5 shadow-xl border border-white/20 focus:outline-none focus:ring-2 focus:ring-sky-400"
            >
            <button type="button" onclick="toggleHeaderSearch()" class="absolute right-2.5 text-slate-400 hover:text-slate-700 p-1 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

</header>

<script>
    // Search bar di header: tersembunyi, muncul saat ikon search ditekan.
    // Mendukung dua varian: mobile header (mobileHeaderSearchWrap) dan
    // beranda pengunjung (headerSearchDropdown).
    function toggleHeaderSearch() {
        // Halaman layanan punya bar pencarian sendiri di area header.
        // Ikon search bawaan header diarahkan ke sana agar tidak dobel.
        const layananWrap = document.getElementById('layananSearchWrap');
        if (layananWrap && typeof toggleLayananSearch === 'function') {
            toggleLayananSearch();
            return;
        }

        const wrap = document.getElementById('mobileHeaderSearchWrap');
        const dropdown = document.getElementById('headerSearchDropdown');
        if (!wrap && !dropdown) return;

        [wrap, dropdown].forEach(el => {
            if (!el) return;
            el.classList.toggle('hidden');
            if (!el.classList.contains('hidden')) {
                const input = el.querySelector('input');
                if (input) input.focus();
            }
        });

        // Kosongkan hasil pencarian saat dropdown ditutup.
        const dropdownClosed = !dropdown || dropdown.classList.contains('hidden');
        if (dropdownClosed) {
            const results = document.getElementById('headerSearchResults');
            if (results) results.classList.add('hidden');
            const list = document.getElementById('headerSearchResultsList');
            if (list) list.innerHTML = '';
        }
    }
</script>

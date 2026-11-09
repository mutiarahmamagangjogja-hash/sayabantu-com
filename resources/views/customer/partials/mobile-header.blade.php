@php
    $isBurgerMenuPage = ($hideSearch ?? false) ||
        request()->routeIs('customer.favorit') || 
        request()->routeIs('customer.pengaduan') || 
        request()->routeIs('customer.tentang') || 
        request()->routeIs('customer.cara-kerja') || 
        request()->routeIs('customer.syarat-ketentuan') || 
        request()->routeIs('customer.kebijakan-privasi');
@endphp

{{-- TOP NAVBAR CUSTOMER (MENGIKUTI DESAIN HEADER DASHBOARD) --}}
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

            <a href="{{ route('customer.home') }}" onclick="handleDrawerHomeClick(); event.preventDefault();" class="flex items-center gap-1.5 group">
                <h1 class="text-[17px] font-bold tracking-tight text-white leading-none">
                    SayaBantu<span class="text-white">.com</span>
                </h1>
            </a>
        </div>

        {{-- SISI KANAN: SEARCH -> TOMBOL MASUK / PROFIL --}}
        <div class="flex items-center gap-1.5">

            {{-- 1. IKON SEARCH (Hanya tampil jika BUKAN halaman burger menu) --}}
            @if(!$isBurgerMenuPage)
            <button
                type="button"
                id="custHeaderSearchBtn"
                onclick="toggleHeaderSearch()"
                class="header-icon shrink-0"
                aria-label="Pencarian"
                title="Cari Layanan"
            >
                <i class="fa-solid fa-magnifying-glass text-[14px]"></i>
            </button>
            @endif

            {{-- 2. IKON NOTIFIKASI CUSTOMER (DOK DI SEBELAH PROFIL CIRCLE - HANYA KETIKA SUDAH LOGIN) --}}
            <button
                type="button"
                id="mobileHeaderNotifBtn"
                onclick="openModal('modalNotifCustomer')"
                class="header-icon shrink-0 relative hidden"
                aria-label="Notifikasi"
                title="Pemberitahuan"
            >
                <i class="fa-regular fa-bell text-[14px]"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-[#0E1D31]"></span>
            </button>

            {{-- 3. TOMBOL MASUK (TAMU PUBLIK) --}}
            <button
                type="button"
                id="mobileHeaderLoginBtn"
                onclick="switchLoginRole('customer'); openModal('modalLoginCustomer');"
                class="px-3 py-1.5 rounded-full bg-white text-[#0E1D31] text-[10.5px] font-extrabold shadow-sm hover:bg-sky-100 transition-all flex items-center gap-1 cursor-pointer {{ Auth::check() ? 'hidden' : '' }}"
            >
                Masuk
            </button>

            {{-- 4. TOMBOL PROFIL CUSTOMER (HANYA KETIKA SUDAH LOGIN) --}}
            <button
                type="button"
                id="mobileHeaderProfileBtn"
                onclick="handleAccountButtonClick()"
                class="header-icon shrink-0 !p-0.5 overflow-hidden ring-1 ring-white/30 {{ Auth::check() ? '' : 'hidden' }}"
                aria-label="Profil Akun"
                title="Profil Saya"
            >
                <div class="w-full h-full rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-bold text-xs flex items-center justify-center" id="mobileHeaderAvatarInitial">
                    {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : '' }}
                    @guest
                        <i class="fa-regular fa-user text-xs"></i>
                    @endguest
                </div>
            </button>
        </div>

    </div>

    @if(!$isBurgerMenuPage)
    {{-- SEARCH BAR DROPDOWN (EXPANDABLE SERAGAM DENGAN DASHBOARD) --}}
    <div id="mobileHeaderSearchWrap" class="relative hidden mt-3 z-30">
        <div class="bg-white rounded-2xl p-2.5 shadow-xl border border-slate-200 space-y-2">
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-xs text-slate-400 pointer-events-none"></i>
                <input
                    type="text"
                    id="mobileSearchInput"
                    oninput="filterLiveHeaderServices(this.value)"
                    placeholder="Cari layanan misal: Bersih Rumah, Pindahan, Antar Barang..."
                    class="w-full pl-8 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                    autocomplete="off"
                >
                <button type="button" onclick="toggleHeaderSearch()" class="absolute right-2 text-slate-400 hover:text-slate-700 p-1 text-xs" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div id="headerSearchResultsList" class="hidden max-h-52 overflow-y-auto space-y-1 text-xs divide-y divide-slate-100">
                {{-- Live Search Items --}}
            </div>
        </div>
    </div>
    @endif

</header>

<script>
    const MOBILE_HEADER_SERVICES = [
        { label: 'Full Home Cleaning', name: 'Full Home Cleaning', desc: 'Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.', budget: '85.000', icon: 'fa-broom' },
        { label: 'Basic Daily Cleaning', name: 'Basic Daily Cleaning', desc: 'Sapu, pel lantai rutin, dan lap meja/kaca.', budget: '50.000', icon: 'fa-spray-can-sparkles' },
        { label: 'Bersihkan Kamar Mandi', name: 'Bersihkan Kamar Mandi', desc: 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.', budget: '65.000', icon: 'fa-shower' },
        { label: 'Cuci Piring & Dapur', name: 'Cuci Piring & Dapur Bersih', desc: 'Bantu cuci piring tumpuk, bersihkan kompor & wastafel.', budget: '45.000', icon: 'fa-sink' },
        { label: 'Pindahan Kos & Rumah', name: 'Pindahan', desc: 'Bantu packing, angkat barang pindahan kos/rumah ke kendaraan.', budget: '150.000', icon: 'fa-boxes-packing' },
        { label: 'Angkut Barang Rumah Tangga', name: 'Angkut Barang', desc: 'Bantu angkat dan memindahkan barang rumah tangga.', budget: '80.000', icon: 'fa-dolly' },
        { label: 'Buang Sampah Rumah Tangga', name: 'Buang Sampah', desc: 'Bantu membuang sampah rumah tangga.', budget: '40.000', icon: 'fa-trash-can' },
        { label: 'Antar / Jemput Barang', name: 'Antar / Jemput', desc: 'Antar barang, dokumen penting, atau ambil laundry kilat.', budget: '50.000', icon: 'fa-motorcycle' },
        { label: 'Belanja Pasar Subuh', name: 'SanSu Food', desc: 'Belanja sayur ke pasar tradisional subuh atau supermarket.', budget: '45.000', icon: 'fa-basket-shopping' },
        { label: 'Titip Antre RS & Faskes', name: 'Titip Antre RS & Faskes', desc: 'Ambilkan nomor antrean dokter, faskes, atau antre tiket event.', budget: '60.000', icon: 'fa-hourglass-half' },
        { label: 'Nemenin Olahraga', name: 'Nemenin Olahraga', desc: 'Temani jogging pagi, gym, bersepeda atau jaga stand toko.', budget: '60.000', icon: 'fa-person-running' },
        { label: 'Bantu Kegiatan Harian', name: 'Bantu Kegiatan Harian', desc: 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.', budget: '60.000', icon: 'fa-handshake' }
    ];

    function toggleHeaderSearch() {
        const wrap = document.getElementById('mobileHeaderSearchWrap');
        if (!wrap) return;

        wrap.classList.toggle('hidden');
        if (!wrap.classList.contains('hidden')) {
            const input = document.getElementById('mobileSearchInput');
            if (input) {
                input.focus();
                if (input.value) filterLiveHeaderServices(input.value);
            }
        } else {
            const list = document.getElementById('headerSearchResultsList');
            if (list) {
                list.classList.add('hidden');
                list.innerHTML = '';
            }
        }
    }

    function filterLiveHeaderServices(query) {
        if (typeof filterMobileServices === 'function') {
            try { filterMobileServices(query); } catch(e) {}
        }

        const lower = (query || '').toLowerCase().trim();
        const list = document.getElementById('headerSearchResultsList');
        if (!list) return;

        if (!lower) {
            list.classList.add('hidden');
            list.innerHTML = '';
            return;
        }

        const matches = MOBILE_HEADER_SERVICES.filter(svc =>
            (svc.label + ' ' + svc.name + ' ' + svc.desc).toLowerCase().includes(lower)
        );

        if (matches.length === 0) {
            list.innerHTML = '<div class="p-3 text-center text-slate-400 text-[11px]">Tidak menemukan layanan yang cocok.</div>';
            list.classList.remove('hidden');
            return;
        }

        list.innerHTML = matches.map(svc => `
            <button type="button"
                onclick="handleHeaderServiceSelect('${svc.name}')"
                class="w-full text-left p-2.5 flex items-center gap-2.5 hover:bg-sky-50 active:bg-sky-100 transition-colors rounded-lg"
            >
                <span class="w-7 h-7 shrink-0 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs">
                    <i class="fa-solid ${svc.icon}"></i>
                </span>
                <span class="flex-1 min-w-0">
                    <span class="block text-[11px] font-bold text-slate-900 truncate">${svc.label}</span>
                    <span class="block text-[9.5px] text-slate-500 truncate">${svc.desc}</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300 group-hover:text-blue-500 transition-colors shrink-0"></i>
            </button>
        `).join('');
        list.classList.remove('hidden');
    }

    function handleHeaderServiceSelect(serviceName) {
        toggleHeaderSearch();
        if (typeof openModernServiceDetail === 'function') {
            openModernServiceDetail(serviceName);
        } else {
            const target = `/layanan?q=${encodeURIComponent(serviceName)}`;
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        }
    }
</script>

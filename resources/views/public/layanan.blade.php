@extends('layouts.public')

@section('title', 'Cari Layanan & Jasa - SayaBantu.com')

{{-- =========================================================
     TOP AREA (PERSIS SCREEN 3 REFERENSI USER)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- TOP BAR: BACK ARROW, "Search", 3-DOTS MENU --}}
    <header class="relative z-10 px-4 pt-3 pb-1">
        <div class="flex items-center justify-between w-full">
            {{-- BACK BUTTON --}}
            <a 
                href="{{ route('customer.index') }}" 
                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center border border-white/15 transition-all active:scale-95"
                aria-label="Kembali"
                title="Kembali ke Beranda"
            >
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>

            {{-- CENTER TITLE "Search" --}}
            <h1 class="text-base font-bold text-white tracking-wide">
                Search
            </h1>

            {{-- 3-DOTS MENU / DRAWER --}}
            <button 
                type="button" 
                onclick="openCustomerDrawer()" 
                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center border border-white/15 transition-all active:scale-95"
                aria-label="Menu"
                title="Pilihan Menu"
            >
                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
            </button>
        </div>

        {{-- SEARCH BAR (PERSIS SCREEN 3) --}}
        <div class="mt-3 relative">
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
                <input
                    type="text"
                    id="cariLayananInput"
                    onkeyup="filterLayananPage(this.value)"
                    value="{{ request('q') }}"
                    placeholder="Search"
                    class="w-full bg-white text-slate-900 placeholder-slate-400 text-xs rounded-2xl pl-9 pr-16 py-3 shadow-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-sky-400 transition-all"
                    autocomplete="off"
                >
                <div class="absolute right-3 flex items-center gap-1.5">
                    <button 
                        type="button" 
                        id="btnClearCariLayanan"
                        onclick="clearCariLayanan()" 
                        class="text-slate-400 hover:text-slate-700 p-1 text-xs hidden" 
                        title="Bersihkan"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <button 
                        type="button" 
                        onclick="toggleFilterSliders()" 
                        class="text-[#173B67] hover:text-[#0E1D31] p-1 text-xs transition-colors" 
                        title="Filter Layanan"
                    >
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- CATEGORY FILTER PILLS (HORIZONTAL SCROLLABLE, PERSIS SCREEN 3) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 pt-3 text-[11px] no-scrollbar">
            <button 
                type="button" 
                data-cat-btn="all"
                onclick="filterLayananPageByCat('all', this)" 
                class="btn-cat-filter px-3.5 py-1.5 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 shadow-xs transition-all"
            >
                All Service
            </button>
            <button 
                type="button" 
                data-cat-btn="bersih"
                onclick="filterLayananPageByCat('bersih', this)" 
                class="btn-cat-filter px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-slate-700 shrink-0 shadow-2xs transition-all"
            >
                Cleaning
            </button>
            <button 
                type="button" 
                data-cat-btn="angkut"
                onclick="filterLayananPageByCat('angkut', this)" 
                class="btn-cat-filter px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-slate-700 shrink-0 shadow-2xs transition-all"
            >
                Moving
            </button>
            <button 
                type="button" 
                data-cat-btn="antar"
                onclick="filterLayananPageByCat('antar', this)" 
                class="btn-cat-filter px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-slate-700 shrink-0 shadow-2xs transition-all"
            >
                Delivery
            </button>
            <button 
                type="button" 
                data-cat-btn="khusus"
                onclick="filterLayananPageByCat('khusus', this)" 
                class="btn-cat-filter px-3.5 py-1.5 rounded-full font-medium bg-white/80 hover:bg-white text-slate-700 shrink-0 shadow-2xs transition-all"
            >
                Others
            </button>
        </div>
    </header>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (PERSIS SCREEN 3 REFERENSI USER)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-4">

    @php
        $services = [
            [
                'cat' => 'bersih', 
                'name' => 'Full Home Cleaning', 
                'label' => 'Cleaning Service', 
                'img' => 'cleaner-card.jpg', 
                'budget' => '85.000',
                'desc' => 'Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.'
            ],
            [
                'cat' => 'angkut', 
                'name' => 'Pindahan', 
                'label' => 'Moving Service', 
                'img' => 'banner-moving.jpg', 
                'budget' => '150.000',
                'desc' => 'Bantu packing kardus rapi, angkat barang berat dan muat ke kendaraan pindahan.'
            ],
            [
                'cat' => 'antar', 
                'name' => 'SanSu Food', 
                'label' => 'Market Shopping', 
                'img' => 'banner-delivery.jpg', 
                'budget' => '45.000',
                'desc' => 'Bantu belanja sayur mayur ke pasar tradisional subuh atau supermarket terdekat.'
            ],
            [
                'cat' => 'bersih', 
                'name' => 'Basic Daily Cleaning', 
                'label' => 'Daily Home Cleaning', 
                'img' => 'cleaner-woman.jpg', 
                'budget' => '50.000',
                'desc' => 'Sapu, pel lantai rutin, lap meja/kaca, dan cuci piring harian.'
            ],
            [
                'cat' => 'bersih', 
                'name' => 'Bersihkan Kamar Mandi', 
                'label' => 'Bathroom Deep Clean', 
                'img' => 'banner-cleaning.jpg', 
                'budget' => '65.000',
                'desc' => 'Kuras bak, sikat kerak lantai membandel & kloset kinclong bebas kuman.'
            ],
            [
                'cat' => 'bersih', 
                'name' => 'Cuci Piring & Dapur Bersih', 
                'label' => 'Kitchen Cleaning', 
                'img' => 'cleaner-card.jpg', 
                'budget' => '45.000',
                'desc' => 'Bantu cuci piring tumpuk, lap kompor berminyak & wastafel kinclong.'
            ],
            [
                'cat' => 'angkut', 
                'name' => 'Angkut Barang', 
                'label' => 'Heavy Load Moving', 
                'img' => 'banner-moving.jpg', 
                'budget' => '80.000',
                'desc' => 'Angkat lemari, kulkas, kasur, atau tata ulang perabot antar lantai.'
            ],
            [
                'cat' => 'angkut', 
                'name' => 'Buang Sampah', 
                'label' => 'Debris & Trash Removal', 
                'img' => 'banner-moving.jpg', 
                'budget' => '40.000',
                'desc' => 'Buang puing renovasi rumah, ranting daun taman, atau barang rongsokan.'
            ],
            [
                'cat' => 'antar', 
                'name' => 'Antar / Jemput', 
                'label' => 'Express Courier', 
                'img' => 'banner-delivery.jpg', 
                'budget' => '50.000',
                'desc' => 'Antar jemput dokumen kantor penting, laundry kilat, atau barang tertinggal.'
            ],
            [
                'cat' => 'antar', 
                'name' => 'Titip Antre RS & Faskes', 
                'label' => 'Queue Assist Service', 
                'img' => 'banner-delivery.jpg', 
                'budget' => '60.000',
                'desc' => 'Ambilkan nomor antrean dokter faskes, BPJS, atau tiket event penting.'
            ],
            [
                'cat' => 'khusus', 
                'name' => 'Nemenin Olahraga', 
                'label' => 'Workout Companion', 
                'img' => 'mascot-helper.jpg', 
                'budget' => '60.000',
                'desc' => 'Teman jogging pagi, lari santai, nge-gym, atau gowes sepeda.'
            ],
            [
                'cat' => 'khusus', 
                'name' => 'Bantu Kegiatan Harian', 
                'label' => 'Daily Assistance', 
                'img' => 'mascot-helper.jpg', 
                'budget' => '60.000',
                'desc' => 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.'
            ],
        ];

        $catLabels = [
            'all' => 'All Service',
            'bersih' => 'Cleaning',
            'angkut' => 'Moving',
            'antar' => 'Delivery',
            'khusus' => 'Others',
        ];
    @endphp

    {{-- =====================================================
         1. SERVICE RECOMMENDATIONS (PERSIS SCREEN 3 REFERENSI)
    ====================================================== --}}
    <section id="serviceRecommendationsSection" class="space-y-2">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold text-[#0E1D31]">Service Recommendations</h3>
            <button 
                type="button" 
                onclick="filterLayananPageByCat('bersih', document.querySelector('[data-cat-btn=\'bersih\']'))" 
                class="text-[10px] text-blue-600 font-bold hover:underline"
            >
                See All
            </button>
        </div>

        {{-- WIDE FEATURED CARD (PERSIS SCREEN 3 REFERENSI USER) --}}
        <div 
            onclick="openModernServiceDetail('Full Home Cleaning')"
            class="group bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-2xs hover:shadow-md transition-all cursor-pointer"
        >
            {{-- HERO IMAGE DENGAN BADGE & HEART --}}
            <div class="relative h-44 bg-slate-100 overflow-hidden">
                <img 
                    src="{{ asset('images/cleaner-woman.jpg') }}" 
                    alt="House Cleaning Service" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/15 to-transparent"></div>

                {{-- TOP LEFT "RECOMMENDED" BADGE (PERSIS SCREEN 3) --}}
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 rounded-full bg-blue-600 text-white text-[9.5px] font-extrabold shadow-md flex items-center gap-1">
                        Recommended
                    </span>
                </div>

                {{-- TOP RIGHT FAVORITE HEART (PERSIS SCREEN 3) --}}
                <div class="absolute top-3 right-3">
                    <button 
                        type="button" 
                        onclick="event.stopPropagation(); toggleFavoriteService(this);" 
                        class="w-8 h-8 rounded-full bg-black/30 backdrop-blur-md text-white hover:text-rose-400 hover:bg-black/50 flex items-center justify-center transition-all shadow-sm"
                        title="Favorite"
                    >
                        <i class="fa-regular fa-heart text-xs"></i>
                    </button>
                </div>

                {{-- OVERLAY TITLE & INFO --}}
                <div class="absolute bottom-3 inset-x-3 text-white flex items-end justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-white drop-shadow-sm leading-tight">House Cleaning Service</h4>
                        <div class="flex items-center gap-2 text-[10px] text-white/90 mt-1">
                            <span>House Cleaning</span>
                            <span>•</span>
                            <span class="font-bold flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-400 text-[9px]"></i> 4.8 (432)
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold text-white bg-black/40 backdrop-blur-xs px-2 py-0.5 rounded-lg border border-white/20">
                            $100 - $200
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
         2. SERVICE FOUND GRID (PERSIS SCREEN 3 REFERENSI)
    ====================================================== --}}
    <section class="space-y-2.5">
        <div class="flex items-center justify-between px-1">
            <h3 id="serviceFoundTitle" class="text-xs font-bold text-[#0E1D31]">
                Service Found (<span id="jumlahLayananCount">16</span>)
            </h3>
            <span id="jumlahLayananPill" class="text-[9.5px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                16 Services
            </span>
        </div>

        {{-- 2-COLUMN GRID (PERSIS SCREEN 3 REFERENSI USER) --}}
        <div class="grid grid-cols-2 gap-2.5" id="layananGrid">
            @foreach($services as $svc)
                <div
                    class="layanan-item group bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-400 transition-all cursor-pointer overflow-hidden flex flex-col"
                    data-cat="{{ $svc['cat'] }}"
                    data-budget="{{ $svc['budget'] }}"
                    data-name="{{ $svc['name'] }}"
                    data-search="{{ strtolower($svc['label'] . ' ' . $svc['name'] . ' ' . $svc['desc']) }}"
                    onclick="openModernServiceDetail(@json($svc['name']))"
                >
                    {{-- IMAGE CONTAINER WITH HEART BUTTON --}}
                    <div class="relative h-28 bg-slate-100 overflow-hidden shrink-0">
                        <img
                            src="{{ asset('images/' . $svc['img']) }}"
                            alt="{{ $svc['label'] }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>

                        {{-- FAVORITE HEART BUTTON (PERSIS SCREEN 3) --}}
                        <button
                            type="button"
                            onclick="event.stopPropagation(); toggleFavoriteService(this);"
                            class="w-7 h-7 rounded-full bg-black/30 backdrop-blur-md text-white hover:text-rose-400 hover:bg-black/50 flex items-center justify-center transition-all shadow-xs absolute top-2 right-2"
                            title="Favorite"
                        >
                            <i class="fa-regular fa-heart text-xs"></i>
                        </button>

                        {{-- CATEGORY BADGE ON IMAGE BOTTOM LEFT --}}
                        <span class="absolute bottom-1.5 left-2 px-1.5 py-0.5 rounded-full bg-black/60 backdrop-blur-xs text-white text-[8px] font-bold">
                            {{ $catLabels[$svc['cat']] ?? 'Service' }}
                        </span>
                    </div>

                    {{-- CARD CONTENT --}}
                    <div class="p-2.5 flex-1 flex flex-col justify-between">
                        <div>
                            {{-- TITLE --}}
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 line-clamp-1 leading-snug">
                                {{ $svc['label'] }}
                            </h4>
                        </div>

                        {{-- PRICE TAG --}}
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[9px] text-slate-400 font-medium">Mulai</span>
                            <span class="text-[11px] font-extrabold text-blue-600">Rp {{ $svc['budget'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- EMPTY STATE --}}
        <div id="layananKosong" class="hidden text-center py-10 px-4 bg-white rounded-2xl border border-dashed border-slate-200">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg mx-auto mb-2">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <p class="text-xs font-bold text-slate-800">Layanan tidak ditemukan</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Coba kata kunci lain atau reset filter kategori.</p>
            <button 
                type="button" 
                onclick="clearCariLayanan(); filterLayananPageByCat('all', document.querySelector('[data-cat-btn=\'all\']'));"
                class="mt-3 px-3 py-1.5 rounded-xl bg-blue-600 text-white text-[10px] font-bold shadow-xs hover:bg-blue-700 transition-all"
            >
                Reset Filter
            </button>
        </div>
    </section>

    {{-- INFO PENUGASAN OTOMATIS --}}
    <section class="p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
        <div class="flex items-start gap-2.5 text-[10.5px]">
            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
            </div>
            <p class="text-slate-600 leading-snug">
                <strong class="text-slate-900">Penugasan Otomatis:</strong> Anda tidak perlu repot mencari mitra satu per satu. Sistem cerdas SayaBantu akan otomatis mencocokkan mitra terbaik dan terdekat setelah Anda memilih layanan.
            </p>
        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
    // Variabel filter halaman
    window.__layananCat = 'all';
    window.__layananQuery = '';

    // Filter berdasarkan kategori pill
    function filterLayananPageByCat(cat, btn) {
        document.querySelectorAll('.btn-cat-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-white/80', 'text-slate-700', 'font-medium');
        });
        if (btn) {
            btn.classList.remove('bg-white/80', 'text-slate-700', 'font-medium');
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
        }

        window.__layananCat = cat;

        // Tampilkan/sembunyikan rekomendasi jika sedang filter kategori tertentu
        const recSection = document.getElementById('serviceRecommendationsSection');
        if (recSection) {
            recSection.style.display = (cat === 'all' || cat === 'bersih') ? 'block' : 'none';
        }

        applyLayananFilter();
    }

    // Filter pencarian live
    function filterLayananPage(query) {
        window.__layananQuery = (query || '').toLowerCase().trim();
        const clearBtn = document.getElementById('btnClearCariLayanan');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !query);
        }
        applyLayananFilter();
    }

    // Bersihkan kata kunci pencarian
    function clearCariLayanan() {
        const input = document.getElementById('cariLayananInput');
        if (input) input.value = '';
        const clearBtn = document.getElementById('btnClearCariLayanan');
        if (clearBtn) clearBtn.classList.add('hidden');
        window.__layananQuery = '';
        applyLayananFilter();
    }

    // Filter slider icon trigger
    function toggleFilterSliders() {
        const input = document.getElementById('cariLayananInput');
        if (input) {
            input.focus();
            if (input.value) {
                clearCariLayanan();
            }
        }
    }

    // Jalankan filter gabungan (kategori + kata kunci pencarian)
    function applyLayananFilter() {
        const cat = window.__layananCat || 'all';
        const q = window.__layananQuery || '';
        let visible = 0;

        document.querySelectorAll('.layanan-item').forEach(item => {
            const itemCat = item.getAttribute('data-cat');
            const search = item.getAttribute('data-search') || '';
            const matchCat = (cat === 'all' || itemCat === cat);
            const matchQuery = (!q || search.includes(q));

            if (matchCat && matchQuery) {
                item.classList.remove('hidden');
                visible++;
            } else {
                item.classList.add('hidden');
            }
        });

        // Tampilkan empty state jika 0 hasil
        const emptyEl = document.getElementById('layananKosong');
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);

        // Update counter teks
        const countEl = document.getElementById('jumlahLayananCount');
        if (countEl) countEl.innerText = visible;

        const pillEl = document.getElementById('jumlahLayananPill');
        if (pillEl) pillEl.innerText = visible + ' Services';
    }

    // Auto-filter jika ada parameter ?q= atau ?cat= di URL
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const q = (params.get('q') || '').trim();
        const cat = (params.get('cat') || '').trim();

        if (q) {
            const input = document.getElementById('cariLayananInput');
            if (input) input.value = q;
            const clearBtn = document.getElementById('btnClearCariLayanan');
            if (clearBtn) clearBtn.classList.remove('hidden');
            window.__layananQuery = q.toLowerCase();
        }

        if (cat) {
            const btn = document.querySelector(`[data-cat-btn="${cat}"]`);
            if (btn) {
                filterLayananPageByCat(cat, btn);
                return;
            }
        }

        applyLayananFilter();
    });
</script>
@endpush

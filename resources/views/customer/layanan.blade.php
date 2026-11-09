@extends('layouts.customer')

@section('title', 'Katalog Layanan & Jasa - SayaBantu.com')

{{-- =========================================================
     TOP AREA (HEADER SERAGAM DENGAN BANNER IDENTIK SCREENSHOT)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER SERAGAM VIA MOBILE-HEADER --}}
    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => 'Semua Layanan & Jasa',
        'deskripsi' => 'Bebas dilihat tanpa login. Pilih layanan untuk langsung mengisi formulir pesanan.',
        'kembali' => false,
    ])
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (KATALOG 16 LAYANAN PERSIS SCREENSHOT)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-3.5">

    @php
        $services = [
            ['cat' => 'bersih', 'name' => 'Full Home Cleaning', 'label' => 'Full Home Cleaning', 'icon' => 'fa-broom', 'color' => 'emerald', 'budget' => '85.000',
             'desc' => 'Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.'],
            ['cat' => 'bersih', 'name' => 'Basic Daily Cleaning', 'label' => 'Basic Daily Cleaning', 'icon' => 'fa-spray-can-sparkles', 'color' => 'emerald', 'budget' => '50.000',
             'desc' => 'Sapu, pel lantai rutin, dan lap meja/kaca.'],
            ['cat' => 'bersih', 'name' => 'Bersihkan Kamar Mandi', 'label' => 'Bersihkan Kamar Mandi', 'icon' => 'fa-shower', 'color' => 'teal', 'budget' => '65.000',
             'desc' => 'Kuras bak, sikat kerak lantai kamar mandi & kloset kinclong.'],
            ['cat' => 'bersih', 'name' => 'Cuci Piring & Dapur Bersih', 'label' => 'Cuci Piring & Dapur', 'icon' => 'fa-sink', 'color' => 'cyan', 'budget' => '45.000',
             'desc' => 'Cuci piring tumpuk, bersihkan kompor & wastafel.'],
            ['cat' => 'angkut', 'name' => 'Pindahan', 'label' => 'Pindahan Kos & Rumah', 'icon' => 'fa-boxes-packing', 'color' => 'blue', 'budget' => '150.000',
             'desc' => 'Bantu packing, angkat barang pindahan kos/rumah ke kendaraan.'],
            ['cat' => 'angkut', 'name' => 'Angkut Barang', 'label' => 'Angkut Barang Rumah Tangga', 'icon' => 'fa-dolly', 'color' => 'blue', 'budget' => '80.000',
             'desc' => 'Bantu angkat dan memindahkan barang rumah tangga.'],
            ['cat' => 'angkut', 'name' => 'Buang Sampah', 'label' => 'Buang Sampah Rumah Tangga', 'icon' => 'fa-trash-can', 'color' => 'slate', 'budget' => '40.000',
             'desc' => 'Bantu membuang sampah rumah tangga ke tempat pembuangan.'],
            ['cat' => 'antar', 'name' => 'Antar / Jemput', 'label' => 'Antar / Jemput Barang', 'icon' => 'fa-motorcycle', 'color' => 'sky', 'budget' => '50.000',
             'desc' => 'Antar barang, dokumen penting, atau ambil laundry kilat.'],
            ['cat' => 'antar', 'name' => 'Bantu Food', 'label' => 'Belanja Pasar Subuh', 'icon' => 'fa-basket-shopping', 'color' => 'amber', 'budget' => '45.000',
             'desc' => 'Belanja sayur ke pasar tradisional subuh atau supermarket.'],
            ['cat' => 'antar', 'name' => 'Titip Antre RS & Faskes', 'label' => 'Titip Antre RS & Faskes', 'icon' => 'fa-hourglass-half', 'color' => 'purple', 'budget' => '60.000',
             'desc' => 'Ambilkan nomor antrean dokter, faskes, atau antre tiket event.'],
            ['cat' => 'khusus', 'name' => 'Nemenin Olahraga', 'label' => 'Nemenin Olahraga', 'icon' => 'fa-person-running', 'color' => 'orange', 'budget' => '60.000',
             'desc' => 'Temani jogging pagi, gym, bersepeda atau jaga stand toko.'],
            ['cat' => 'khusus', 'name' => 'Bantu Kegiatan Harian', 'label' => 'Bantu Kegiatan Harian', 'icon' => 'fa-handshake', 'color' => 'navy', 'budget' => '60.000',
             'desc' => 'Pendampingan sederhana untuk kebutuhan kegiatan sehari-hari.'],
        ];

        $catLabels = [
            'all' => 'Semua',
            'bersih' => 'Kebersihan',
            'angkut' => 'Pindah & Angkut',
            'antar' => 'Antar & Belanja',
            'khusus' => 'Kegiatan Harian',
        ];

        $colorMap = [
            'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'teal' => 'bg-teal-50 text-teal-600 border-teal-100',
            'cyan' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
            'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
            'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
            'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
            'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
            'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
            'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            'pink' => 'bg-pink-50 text-pink-600 border-pink-100',
            'stone' => 'bg-stone-100 text-stone-700 border-stone-200',
            'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
            'navy' => 'bg-slate-900 text-sky-400 border-slate-700',
        ];
    @endphp

    {{-- CARD KATALOG LAYANAN & JASA (PERSIS SCREENSHOT) --}}
    <section id="layananKatalogSection" class="card-sb p-4 space-y-3.5">
        <div class="flex items-center justify-between px-0.5">
            <div>
                <h3 class="text-xs font-bold text-[#0E1D31] flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-[#173B67]"></i> Katalog Layanan &amp; Jasa
                </h3>
                <p class="text-[9px] text-slate-400 mt-0.5">Seluruh layanan suruhan SayaBantu</p>
            </div>
            <button type="button" onclick="filterKatalogPageByCat('all')" id="jumlahLayananCust" class="text-[9px] text-[#173B67] font-bold bg-blue-50 hover:bg-blue-100 transition-colors px-2.5 py-0.5 rounded-full border border-blue-100 cursor-pointer focus:outline-none" title="Lihat Semua Kategori">
                {{ count($services) }} Layanan
            </button>
        </div>

        {{-- FILTER PILLS KATEGORI --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
            <button type="button" onclick="filterKatalogPageByCat('all', this)" class="btn-katalog-cat px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 transition-all">
                Semua
            </button>
            @foreach($catLabels as $key => $label)
                @if($key !== 'all')
                    <button type="button" onclick="filterKatalogPageByCat('{{ $key }}', this)" class="btn-katalog-cat px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                        {{ $label }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- DAFTAR KARTU 16 LAYANAN (PERSIS SCREENSHOT) --}}
        <div class="space-y-2.5" id="layananGridContainer">
            @foreach($services as $svc)
                <div
                    class="layanan-katalog-item rounded-2xl bg-white border border-slate-200/80 p-3 hover:border-sky-300 hover:shadow-xs transition-all cursor-pointer group active:scale-[0.99]"
                    data-cat="{{ $svc['cat'] }}"
                    data-budget="{{ $svc['budget'] }}"
                    data-name="{{ $svc['name'] }}"
                    data-search="{{ strtolower($svc['label'] . ' ' . $svc['name'] . ' ' . $svc['desc']) }}"
                    onclick='handleLayananItemClick(@json($svc["name"]), @json($svc["budget"]))'
                >
                    <div class="flex items-center gap-3">
                        {{-- IKON DI KOTAK BERWARNA --}}
                        <div class="w-11 h-11 shrink-0 rounded-xl border flex items-center justify-center text-base {{ $colorMap[$svc['color']] }}">
                            <i class="fa-solid {{ $svc['icon'] }}"></i>
                        </div>

                        {{-- INFO LAYANAN --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 truncate transition-colors">{{ $svc['label'] }}</h4>
                            <p class="text-[10px] text-slate-500 leading-snug mt-0.5 line-clamp-2">{{ $svc['desc'] }}</p>
                            <div class="mt-1.5">
                                <span class="inline-block text-[9.5px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                                    {{ $catLabels[$svc['cat']] ?? 'Layanan' }}
                                </span>
                            </div>
                        </div>

                        {{-- CHEVRON KANAN --}}
                        <i class="fa-solid fa-chevron-right text-[11px] text-slate-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all self-center shrink-0"></i>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- EMPTY STATE --}}
        <div id="layananKatalogKosong" class="hidden text-center py-8">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-lg mx-auto mb-2">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <p class="text-xs font-bold text-slate-700">Layanan tidak ditemukan</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Coba kata kunci lain atau pilih kategori Semua.</p>
        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
    // State Filter Kategori
    window.__currentKatalogCat = 'all';

    function filterKatalogPageByCat(cat, btn) {
        document.querySelectorAll('.btn-katalog-cat').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });
        if (btn) {
            btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
        }
        window.__currentKatalogCat = cat;
        applyKatalogFilter();
    }
    window.filterKatalogPageByCat = filterKatalogPageByCat;

    // Filter dari pencarian header
    function filterMobileServices(query) {
        const q = (query || '').toLowerCase().trim();
        let visible = 0;

        document.querySelectorAll('.layanan-katalog-item').forEach(item => {
            const itemCat = item.getAttribute('data-cat');
            const search = item.getAttribute('data-search') || '';
            const matchCat = (window.__currentKatalogCat === 'all' || itemCat === window.__currentKatalogCat);
            const matchQuery = (!q || search.includes(q));

            if (matchCat && matchQuery) {
                item.classList.remove('hidden');
                visible++;
            } else {
                item.classList.add('hidden');
            }
        });

        const emptyEl = document.getElementById('layananKatalogKosong');
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    }

    function applyKatalogFilter() {
        const input = document.getElementById('mobileSearchInput');
        const q = input ? input.value : '';
        filterMobileServices(q);
    }

    // Aksi Klik Layanan: Pengunjung wajib masuk akun terlebih dahulu (langsung diarahkan ke /customer/login)
    function handleLayananItemClick(name, budget) {
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            const redirectUrl = encodeURIComponent(window.location.pathname + window.location.search);
            const serviceParam = encodeURIComponent(name || '');
            window.location.href = "{{ route('customer.login') }}?redirect=" + redirectUrl + "&layanan=" + serviceParam;
            return;
        }

        if (typeof openModernServiceDetail === 'function') {
            openModernServiceDetail(name);
            return;
        }

        const target = `/layanan/detail?name=${encodeURIComponent(name)}&budget=${encodeURIComponent(budget || '')}`;
        if (typeof spaNavigateTo === 'function') {
            spaNavigateTo(target);
        } else {
            window.location.href = target;
        }
    }
    window.handleLayananItemClick = handleLayananItemClick;

    // Jika ada query param ?q= dari URL, langsung jalankan filter
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const q = params.get('q');
        if (q) {
            const input = document.getElementById('mobileSearchInput');
            if (input) {
                input.value = q;
                const wrap = document.getElementById('mobileHeaderSearchWrap');
                if (wrap) wrap.classList.remove('hidden');
            }
            filterMobileServices(q);
        }
    });
</script>
@endpush

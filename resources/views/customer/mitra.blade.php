@extends('layouts.customer')

@section('title', 'Mitra SayaBantu - Per Kota')

{{-- =========================================================
     TOP AREA (HEADER SERAGAM DENGAN HALAMAN CUSTOMER LAIN)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER SERAGAM VIA MOBILE-HEADER --}}
    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => 'Mitra Per Kota',
        'deskripsi' => 'Bebas dilihat tanpa login. Pilih kota untuk melihat personil terlatih & terverifikasi.',
        'kembali' => false,
    ])
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (DAFTAR MITRA PER KOTA - DINAMIS REAL-TIME)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-3.5">

    {{-- HERO MITRA DENGAN MASKOT BOCAH SAYABANTU --}}
    <section class="card-sb p-3.5 bg-gradient-to-br from-white via-sky-50/50 to-blue-50/40 border border-sky-150 overflow-hidden relative shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="shrink-0 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md relative bg-slate-900/10">
                <img src="{{ asset('images/mascot-helper.jpg') }}" alt="Maskot Bocah SayaBantu" class="w-full h-full object-cover">
                <span class="absolute bottom-0 inset-x-0 bg-emerald-600 text-white text-[7.5px] font-extrabold text-center py-0.5 tracking-wider">
                    VERIFIED
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[8.5px] font-extrabold mb-1">
                    <i class="fa-solid fa-shield-halved text-[8px]"></i> 100% Lolos Seleksi &amp; SKCK
                </div>
                <h2 class="text-xs font-bold text-[#0E1D31] leading-tight">
                    Bocah SayaBantu Siap Bertugas
                </h2>
                <p class="text-[9.5px] text-slate-600 mt-1 leading-relaxed">
                    Setiap personil telah melewati verifikasi identitas KTP resmi, tes keahlian, dan siap ditugaskan kapan saja dengan jaminan aman.
                </p>
                <div class="mt-2 pt-1 border-t border-sky-100 flex items-center justify-between">
                    <span class="text-[9px] text-slate-500 font-medium">Tertarik cari penghasilan?</span>
                    <a
                        href="{{ route('mitra.daftar') }}"
                        class="px-2.5 py-1 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white text-[9.5px] font-bold shadow-xs inline-flex items-center gap-1 transition-all"
                    >
                        <i class="fa-solid fa-user-plus text-[8.5px]"></i>
                        <span>Daftar Jadi Mitra</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- INFO PENUGASAN OTOMATIS --}}
    <section class="card-sb p-3.5">
        <div class="p-2.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-start gap-2 text-[10.5px]">
            <i class="fa-solid fa-wand-magic-sparkles text-sky-600 mt-0.5 shrink-0"></i>
            <p class="text-slate-700 leading-snug">
                <strong>Penugasan Otomatis:</strong> Anda tidak perlu repot memilih mitra. Saat Anda membuat suruhan, sistem cerdas kami akan <strong>otomatis memilihkan mitra terdekat dengan rating terbaik</strong>.
            </p>
        </div>
    </section>

    {{-- FILTER KOTA --}}
    <section class="card-sb p-3.5 space-y-3">
        <div class="flex items-center justify-between px-1">
            <div>
                <h2 class="text-xs font-bold text-[#0E1D31] flex items-center gap-1.5">
                    <i class="fa-solid fa-location-dot text-rose-500"></i> Mitra Per Kota
                </h2>
                <p class="text-[9px] text-slate-400">Hanya mitra yang terverifikasi untuk kota pilihan ditampilkan</p>
            </div>
            <a href="{{ route('customer.mitra') }}" id="jumlahMitraTotal" data-kota="all" class="text-[9px] font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 transition-colors px-2 py-0.5 rounded-full cursor-pointer focus:outline-none" title="Lihat Semua Personil">
                {{ $totalMitraAktif }} personil
            </a>
        </div>

        <div id="filterKotaBar" class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
            <a href="{{ route('customer.mitra') }}" data-kota="all" class="btn-kota-filter px-2.5 py-1 rounded-full {{ $selectedKota === '' ? 'font-bold bg-[#0E1D31] text-white' : 'font-medium bg-slate-100 text-slate-700' }} shrink-0 transition-all">
                Semua ({{ $totalMitraAktif }})
            </a>
            @foreach($mitraCountsByKota as $kota => $jumlah)
                <a href="{{ route('customer.mitra', ['kota' => $kota]) }}" data-kota="{{ $kota }}" class="btn-kota-filter px-2.5 py-1 rounded-full {{ mb_strtolower($selectedKota) === mb_strtolower($kota) ? 'font-bold bg-[#0E1D31] text-white' : 'font-medium bg-slate-100 text-slate-700' }} shrink-0 hover:bg-slate-200 transition-all">
                    {{ $kota }} ({{ $jumlah }})
                </a>
            @endforeach
        </div>
    </section>

    {{-- CONTAINER UNTUK DAFTAR MITRA DIKELOMPOKKAN PER KOTA --}}
    <div id="containerMitraPerKota" class="space-y-3.5">
        @foreach($dbMitrasByKota as $kota => $mitras)
            <section class="card-sb p-3.5 space-y-3 mitra-kota-group" data-kota="{{ $kota }}">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-[#0E1D31] text-white flex items-center justify-center text-[11px]">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-[#0E1D31] leading-none">{{ $kota }}</h3>
                            <p class="text-[9px] text-slate-400 mt-0.5">{{ $mitras->count() }} mitra siap tugas</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-0.5"></span>Tersedia
                    </span>
                </div>

                <div class="space-y-2.5">
                    @foreach($mitras as $mitra)
                        @php
                            $nama = $mitra->name;
                            $avatar = $mitra->avatar;
                            $service = $mitra->keahlian ?: $mitra->service ?: 'Layanan umum';
                            $rating = $mitra->reviewed_orders_avg_review_rating === null
                                ? null
                                : number_format((float) $mitra->reviewed_orders_avg_review_rating, 1);
                            $reviewCount = $mitra->reviewed_orders_count;
                            $tasks = $mitra->completed_orders_count;
                            $ratingBadge = $rating === null ? 'Belum ada ulasan' : '★ '.$rating.' ('.$reviewCount.' ulasan)';
                            $area = $mitra->area ?: $kota.' Pusat';
                        @endphp
                        <article
                            class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 hover:border-sky-300 transition-all cursor-pointer group active:scale-[0.99]"
                            data-profile-name="{{ $nama }}"
                            data-profile-avatar="{{ $avatar }}"
                            data-profile-service="{{ $service }}"
                            data-profile-rating="{{ $ratingBadge }}"
                            data-profile-area="{{ $area }}"
                            onclick="openBocahModal(this.dataset.profileName, this.dataset.profileAvatar, this.dataset.profileService, this.dataset.profileRating, this.dataset.profileArea)"
                        >
                            <div class="relative shrink-0">
                                @if($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $nama }}" class="w-12 h-12 rounded-xl object-cover border border-white shadow-xs">
                                @else
                                    <span class="w-12 h-12 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-bold border border-white shadow-xs">
                                        {{ mb_strtoupper(mb_substr($nama, 0, 1)) }}
                                    </span>
                                @endif
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white" title="Mitra Aktif"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">{{ $nama }}</h4>
                                    <span class="text-[10px] font-bold shrink-0">
                                        @if($rating !== null)
                                            <span class="text-yellow-400">★</span> {{ $rating }} <span class="text-slate-400 font-normal text-[9px]">({{ $reviewCount }} ulasan)</span>
                                        @else
                                            <span class="text-slate-500 font-medium">Belum ada ulasan</span>
                                        @endif
                                    </span>
                                </div>
                                <p class="text-[9px] text-sky-700 font-semibold mt-0.5 truncate">{{ $service }}</p>
                                <p class="text-[9px] text-slate-500 mt-1">
                                    <i class="fa-solid fa-location-dot text-rose-500"></i> {{ $area }}
                                    <span class="mx-0.5">•</span>
                                    <span>{{ $tasks }} pekerjaan selesai</span>
                                    <span class="mx-0.5">•</span>
                                    <span class="text-emerald-700 font-medium"><i class="fa-solid fa-shield-check"></i> Terverifikasi</span>
                                </p>
                            </div>
                            <button
                                type="button"
                                onclick="event.stopPropagation(); openBocahModal(this.closest('article').dataset.profileName, this.closest('article').dataset.profileAvatar, this.closest('article').dataset.profileService, this.closest('article').dataset.profileRating, this.closest('article').dataset.profileArea)"
                                class="shrink-0 px-2.5 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-[9.5px] font-bold self-center transition-colors cursor-pointer"
                            >
                                Profil
                            </button>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    {{-- EMPTY STATE MITRA PER KOTA (JIKA BELUM ADA MITRA DI-ACC ATAU KOTA KOSONG) --}}
    <div id="emptyMitraPerKota" class="card-sb p-8 text-center space-y-3 {{ $dbMitrasByKota->isNotEmpty() ? 'hidden' : '' }}">
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 mx-auto flex items-center justify-center text-2xl border border-sky-100 shadow-xs">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div class="space-y-1">
            <h3 class="text-xs font-bold text-slate-800" id="emptyMitraTitle">Belum Ada Mitra Terverifikasi</h3>
            <p class="text-[10px] text-slate-500 max-w-xs mx-auto leading-relaxed" id="emptyMitraDesc">
                {{ $selectedKota !== '' ? "Belum ada mitra terverifikasi yang terdaftar di {$selectedKota}." : 'Mitra yang disetujui admin akan otomatis tampil di sini.' }}
            </p>
        </div>
        <div class="pt-2">
            <a
                href="{{ route('mitra.daftar') }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white text-[10px] font-bold shadow-xs transition-all cursor-pointer"
            >
                <i class="fa-solid fa-user-plus text-[9px]"></i>
                <span>Daftar Menjadi Mitra</span>
            </a>
        </div>
    </div>

</main>
@endsection

@push('scripts')
<script>
    let currentSelectedKota = 'all';

    // Dapatkan mitra yang telah diverifikasi dan di-ACC oleh admin
    function getApprovedMitras() {
        return [];
    }

    // Render grup mitra per kota
    function renderDynamicMitraPerKota() {
        return;

        const approvedMitras = getApprovedMitras();
        const container = document.getElementById('containerMitraPerKota');
        const emptyState = document.getElementById('emptyMitraPerKota');
        const totalEl = document.getElementById('jumlahMitraTotal');

        if (totalEl) totalEl.innerText = approvedMitras.length + ' personil';

        // Daftar kota standar untuk filter
        const standardCities = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Bandung', 'Solo', 'Yogyakarta'];
        
        // Kumpulkan semua kota unik yang memiliki mitra terverifikasi
        const cityGroups = {};
        approvedMitras.forEach(m => {
            const kota = m.kota || 'Jakarta';
            if (!cityGroups[kota]) cityGroups[kota] = [];
            cityGroups[kota].push(m);
        });

        // Update bar filter kota beserta jumlah personil
        const filterBar = document.getElementById('filterKotaBar');
        if (filterBar) {
            let filterHtml = `
                <button type="button" onclick="filterKotaMitra('all', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-bold ${currentSelectedKota === 'all' ? 'bg-[#0E1D31] text-white' : 'bg-slate-100 text-slate-700 font-medium'} shrink-0 transition-all">
                    Semua (${approvedMitras.length})
                </button>
            `;

            // Gabungkan kota standar dan kota yang terdaftar
            const allCitiesSet = new Set([...standardCities, ...Object.keys(cityGroups)]);
            allCitiesSet.forEach(kota => {
                const count = cityGroups[kota] ? cityGroups[kota].length : 0;
                const isActive = currentSelectedKota === kota;
                filterHtml += `
                    <button type="button" onclick="filterKotaMitra('${kota}', this)" class="btn-kota-filter px-2.5 py-1 rounded-full ${isActive ? 'bg-[#0E1D31] text-white font-bold' : 'bg-slate-100 text-slate-700 font-medium'} shrink-0 hover:bg-slate-200 transition-all">
                        ${kota} (${count})
                    </button>
                `;
            });
            filterBar.innerHTML = filterHtml;
        }

        if (approvedMitras.length === 0) {
            if (emptyState) {
                emptyState.classList.remove('hidden');
                document.getElementById('emptyMitraTitle').innerText = 'Belum Ada Mitra Terverifikasi';
                document.getElementById('emptyMitraDesc').innerText = 'Pendaftaran calon mitra baru sedang dalam proses verifikasi berkas e-KTP & SKCK oleh admin. Mitra yang sudah di-ACC akan otomatis tampil di sini.';
            }
            if (container) {
                container.innerHTML = '';
                container.classList.add('hidden');
            }
            return;
        }

        if (container) {
            container.classList.remove('hidden');
            
            // Urutkan kota
            const sortedCities = Object.keys(cityGroups).sort();
            
            container.innerHTML = sortedCities.map(kota => {
                const list = cityGroups[kota];
                const listArticles = list.map(m => {
                    const nama = m.nama || m.name || 'Mitra';
                    const avatar = m.avatar || m.image || '';
                    const service = m.keahlian || m.service || 'Layanan belum diisi';
                    const ratingValue = Number(m.rating);
                    const reviewCount = Number(m.reviewCount || 0);
                    const rating = reviewCount > 0 && Number.isFinite(ratingValue) ? ratingValue.toFixed(1) : null;
                    const tasks = Number(m.tasks || 0);
                    const ratingBadge = rating
                        ? `★ ${rating} (${reviewCount} ulasan) · ${tasks} pekerjaan selesai`
                        : `Belum ada ulasan · ${tasks} pekerjaan selesai`;
                    const ratingMarkup = rating
                        ? `<span class="text-[10px] font-bold shrink-0"><span class="text-yellow-400">★</span> ${rating} <span class="text-slate-400 font-normal text-[9px]">(${reviewCount} ulasan)</span></span>`
                        : '<span class="text-[9px] text-slate-500 font-medium shrink-0">Belum ada ulasan</span>';
                    const area = m.area || m.kota || 'Wilayah belum diatur';
                    const avatarMarkup = avatar
                        ? `<img src="${avatar}" alt="${nama}" class="w-12 h-12 rounded-xl object-cover border border-white shadow-xs">`
                        : '<div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-user"></i></div>';

                    return `
                        <article
                            class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 hover:border-sky-300 transition-all cursor-pointer group active:scale-[0.99]"
                            onclick="openBocahModal('${nama.replace(/'/g, "\\'")}', '${avatar}', '${service.replace(/'/g, "\\'")}', '${ratingBadge}', '${area.replace(/'/g, "\\'")}')"
                        >
                            <div class="relative shrink-0">
                                ${avatarMarkup}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">${nama}</h4>
                                    ${ratingMarkup}
                                </div>
                                <p class="text-[9px] text-sky-700 font-semibold mt-0.5 truncate">${service}</p>
                                <p class="text-[9px] text-slate-500 mt-1">
                                    <i class="fa-solid fa-location-dot text-rose-500"></i> ${area}
                                    <span class="mx-0.5">•</span>
                                    <span class="text-emerald-700 font-medium"><i class="fa-solid fa-shield-check"></i> Terverifikasi</span>
                                </p>
                            </div>
                            <button
                                type="button"
                                onclick="event.stopPropagation(); openBocahModal('${nama.replace(/'/g, "\\'")}', '${avatar}', '${service.replace(/'/g, "\\'")}', '${ratingBadge}', '${area.replace(/'/g, "\\'")}')"
                                class="shrink-0 px-2.5 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-[9.5px] font-bold self-center transition-colors cursor-pointer"
                            >
                                Profil
                            </button>
                        </article>
                    `;
                }).join('');

                return `
                    <section class="card-sb p-3.5 space-y-3 mitra-kota-group" data-kota="${kota}">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-[#0E1D31] text-white flex items-center justify-center text-[11px]">
                                    <i class="fa-solid fa-city"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-[#0E1D31] leading-none">${kota}</h3>
                                    <p class="text-[9px] text-slate-400 mt-0.5">${list.length} mitra terverifikasi</p>
                                </div>
                            </div>
                            <span class="text-[9px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">Terverifikasi</span>
                        </div>

                        <div class="space-y-2.5">
                            ${listArticles}
                        </div>
                    </section>
                `;
            }).join('');
        }

        // Terapkan filter kota saat ini
        filterKotaMitra(currentSelectedKota);
    }
    // Filter grup mitra berdasarkan kota
    function filterKotaMitra(kota, btn) {
        currentSelectedKota = kota;

        document.querySelectorAll('.btn-kota-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });

        const activeButton = btn || Array.from(document.querySelectorAll('.btn-kota-filter')).find(button => button.dataset.kota === kota);
        if (activeButton) {
            activeButton.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
            activeButton.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
        }

        const groups = document.querySelectorAll('.mitra-kota-group');
        const emptyState = document.getElementById('emptyMitraPerKota');
        const emptyTitle = document.getElementById('emptyMitraTitle');
        const emptyDesc = document.getElementById('emptyMitraDesc');
        const totalEl = document.getElementById('jumlahMitraTotal');

        let visibleGroups = 0;
        let visiblePersonil = 0;

        groups.forEach(group => {
            const grupKota = group.getAttribute('data-kota');
            if (kota === 'all' || grupKota === kota) {
                group.classList.remove('hidden');
                visibleGroups++;
                visiblePersonil += group.querySelectorAll('article').length;
            } else {
                group.classList.add('hidden');
            }
        });

        if (totalEl) totalEl.innerText = visiblePersonil + ' personil';

        if (visibleGroups === 0) {
            if (emptyState) {
                emptyState.classList.remove('hidden');
                if (emptyTitle) emptyTitle.innerText = `Belum Ada Mitra Terverifikasi di ${kota}`;
                if (emptyDesc) emptyDesc.innerText = `Saat ini belum ada mitra terverifikasi untuk wilayah ${kota}. Calon mitra pendaftar masih dalam tahap peninjauan dan ACC admin.`;
            }
        } else {
            if (emptyState) emptyState.classList.add('hidden');
        }
    }
    window.filterKotaMitra = filterKotaMitra;

</script>
@endpush
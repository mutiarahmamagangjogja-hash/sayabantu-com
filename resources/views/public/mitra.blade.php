@extends('layouts.public')

@section('title', 'Mitra SayaBantu - Per Kota')

{{-- =========================================================
     TOP AREA (HEADER SERAGAM DENGAN HALAMAN CUSTOMER LAIN)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER SERAGAM VIA MOBILE-HEADER --}}
    @include('public.partials.header')

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
                <p class="text-[9px] text-slate-400">Pilih kota untuk memfilter personil terverifikasi</p>
            </div>
            <button type="button" onclick="filterKotaMitra('all')" id="jumlahMitraTotal" class="text-[9px] font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 transition-colors px-2 py-0.5 rounded-full cursor-pointer focus:outline-none" title="Lihat Semua Personil">
                0 personil
            </button>
        </div>

        <div id="filterKotaBar" class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
            <button type="button" onclick="filterKotaMitra('all', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 transition-all">
                Semua (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Jakarta', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Jakarta (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Bogor', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Bogor (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Depok', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Depok (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Tangerang', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Tangerang (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Bekasi', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Bekasi (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Bandung', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Bandung (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Solo', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Solo (0)
            </button>
            <button type="button" onclick="filterKotaMitra('Yogyakarta', this)" class="btn-kota-filter px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700 shrink-0 hover:bg-slate-200 transition-all">
                Yogyakarta (0)
            </button>
        </div>
    </section>

    {{-- CONTAINER UNTUK DAFTAR MITRA DIKELOMPOKKAN PER KOTA --}}
    <div id="containerMitraPerKota" class="space-y-3.5"></div>

    {{-- EMPTY STATE MITRA PER KOTA (JIKA BELUM ADA MITRA DI-ACC ATAU KOTA KOSONG) --}}
    <div id="emptyMitraPerKota" class="card-sb p-8 text-center space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 mx-auto flex items-center justify-center text-2xl border border-sky-100 shadow-xs">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div class="space-y-1">
            <h3 class="text-xs font-bold text-slate-800" id="emptyMitraTitle">Belum Ada Mitra Terverifikasi</h3>
            <p class="text-[10px] text-slate-500 max-w-xs mx-auto leading-relaxed" id="emptyMitraDesc">
                Pendaftaran calon mitra sedang dalam proses seleksi, tes keahlian, dan verifikasi berkas oleh admin. Mitra yang sudah disetujui (di-ACC) akan otomatis tampil di sini.
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
        let list = [];
        try {
            const raw = localStorage.getItem('sb_registered_mitras');
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) list = parsed;
            }
        } catch(e) {}

        try {
            const rawSingle = localStorage.getItem('sb_mitra_registration');
            if (rawSingle) {
                const s = JSON.parse(rawSingle);
                if (s && !list.some(m => (m.id && m.id === s.id) || (m.whatsapp && m.whatsapp === s.whatsapp))) {
                    list.push(s);
                }
            }
        } catch(e) {}

        // FILTER KETAT: HANYA MITRA YANG SUDAH DIVERIFIKASI & DI-ACC ADMIN (status: 'aktif')
        return list.filter(m => {
            const st = String(m.status || '').toLowerCase();
            return st === 'aktif' || st === 'aktif terverifikasi' || st === 'disetujui' || st === 'terverifikasi';
        });
    }

    // Render grup mitra per kota
    function renderDynamicMitraPerKota() {
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
    window.renderDynamicMitraPerKota = renderDynamicMitraPerKota;

    // Filter grup mitra berdasarkan kota
    function filterKotaMitra(kota, btn) {
        currentSelectedKota = kota;

        document.querySelectorAll('.btn-kota-filter').forEach(b => {
            b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
            b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
        });

        if (btn) {
            btn.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
            btn.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
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

    document.addEventListener('DOMContentLoaded', function() {
        renderDynamicMitraPerKota();
        // Tarik data mitra terverifikasi langsung dari database
        fetch('{{ route("api.mitra.per-kota") }}')
            .then(r => r.json())
            .then(data => {
                if (data.success && Array.isArray(data.mitras)) {
                    let localList = [];
                    try {
                        const raw = localStorage.getItem('sb_registered_mitras');
                        if (raw) localList = JSON.parse(raw);
                    } catch(e) {}
                    data.mitras.forEach(m => {
                        const idx = localList.findIndex(x => x.id === m.id || x.whatsapp === m.whatsapp);
                        if (idx >= 0) {
                            localList[idx] = { ...localList[idx], ...m };
                        } else {
                            localList.push(m);
                        }
                    });
                    localStorage.setItem('sb_registered_mitras', JSON.stringify(localList));
                    renderDynamicMitraPerKota();
                }
            })
            .catch(() => {});
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'sb_registered_mitras' || e.key === 'sb_mitra_registration') {
            renderDynamicMitraPerKota();
        }
    });
</script>
@endpush
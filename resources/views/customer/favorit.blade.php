@extends('layouts.customer')

@section('title', 'Layanan Favorit Saya - SayaBantu.com')

{{-- =========================================================
     TOP AREA (HEADER IDENTIK DASHBOARD, TANPA SEARCH)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER PERSIS DASHBOARD (TANPA SEARCH KARENA HALAMAN BURGER MENU) --}}
    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => 'Layanan Favorit Saya',
        'deskripsi' => 'Daftar layanan suruhan yang Anda simpan untuk akses kilat dan pemesanan 1-klik tanpa perlu mencari ulang.',
        'kembali' => false,
    ])

    @include('customer.partials.guard-login', ['pesan' => 'Silakan masuk akun untuk mengakses Layanan Favorit.'])

    {{-- SUMMARY KARTU KEUNTUNGAN --}}
    <section class="relative z-10 px-5 pb-5">
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="filterFavoritKategori('semua', document.querySelector('.fav-tab-btn'))" title="Tampilkan semua layanan favorit"
                class="w-full text-left p-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center gap-2.5 cursor-pointer hover:bg-white/20 transition-all focus:outline-none">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-400/30 flex items-center justify-center text-rose-300 text-sm">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div>
                    <span class="text-[9px] text-slate-300 block leading-none">Tersimpan</span>
                    <strong class="text-xs font-black text-white mt-0.5 block" id="favCountSummary">0 Layanan</strong>
                </div>
            </button>

            <button type="button" onclick="filterFavoritKategori('semua', document.querySelector('.fav-tab-btn'))" title="Pesan cepat 1-klik dari daftar favorit"
                class="w-full text-left p-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center gap-2.5 cursor-pointer hover:bg-white/20 transition-all focus:outline-none">
                <div class="w-8 h-8 rounded-xl bg-sky-400/20 border border-sky-300/30 flex items-center justify-center text-sky-300 text-sm">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <span class="text-[9px] text-slate-300 block leading-none">Pesan Cepat</span>
                    <strong class="text-xs font-black text-sky-300 mt-0.5 block">1-Klik Siap Jalan</strong>
                </div>
            </button>
        </div>
    </section>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (HALAMAN LAYANAN FAVORIT LENGKAP)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-6 space-y-3.5">

    {{-- FILTER TAB KATEGORI FAVORIT --}}
    <section class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs font-bold">
        <button type="button" onclick="filterFavoritKategori('semua', this)" class="fav-tab-btn px-3 py-1.5 rounded-full bg-[#173B67] text-white shadow-xs shrink-0">
            Semua (<span id="tabCountSemua">0</span>)
        </button>
        <button type="button" onclick="filterFavoritKategori('kebersihan', this)" class="fav-tab-btn px-3 py-1.5 rounded-full bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shrink-0">
            Kebersihan
        </button>
        <button type="button" onclick="filterFavoritKategori('pindahan', this)" class="fav-tab-btn px-3 py-1.5 rounded-full bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shrink-0">
            Pindahan &amp; Angkut
        </button>
    </section>

    {{-- DAFTAR KARTU FAVORIT --}}
    <div class="hidden space-y-2.5" id="favoritListContainer">

        {{-- FAVORIT 1: FULL HOME CLEANING --}}
        <div class="fav-item-card card-sb p-3.5 bg-white border border-slate-200/80 shadow-xs relative transition-all cursor-pointer hover:border-sky-300 active:scale-[0.99] group" data-id="full-clean" data-kategori="kebersihan" onclick="openModernServiceDetail('Full Home Cleaning')">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-lg shrink-0">
                    <i class="fa-solid fa-broom"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-1">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">Kebersihan</span>
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors mt-1 leading-snug">Full Home Cleaning</h3>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); toggleFavoritItem('full-clean', 'Full Home Cleaning')" class="fav-heart-btn w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center text-sm transition-transform active:scale-90 shrink-0" title="Hapus dari Favorit">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1 leading-relaxed line-clamp-2">
                        Pembersihan menyeluruh meliputi ruang tamu, kamar, perabotan, dan pel lantai kinclong.
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] text-slate-400 block leading-none">Mulai dari</span>
                            <strong class="text-xs font-black text-slate-900 mt-0.5 block">Rp 85.000</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="event.stopPropagation(); openModernServiceDetail('Full Home Cleaning')" class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                Detail
                            </button>
                            <button type="button" onclick="event.stopPropagation(); pesanLayananFavorit('Full Home Cleaning', '85.000')" class="px-3 py-1.5 rounded-xl text-[10.5px] font-extrabold text-white bg-[#173B67] hover:bg-[#0E1D31] transition-all shadow-xs flex items-center gap-1">
                                Pesan <i class="fa-solid fa-arrow-right text-[8.5px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAVORIT 3: PINDAHAN KOS & RUMAH --}}
        <div class="fav-item-card card-sb p-3.5 bg-white border border-slate-200/80 shadow-xs relative transition-all cursor-pointer hover:border-sky-300 active:scale-[0.99] group" data-id="pindahan" data-kategori="pindahan" onclick="openModernServiceDetail('Pindahan')">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-lg shrink-0">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-1">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">Pindahan</span>
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors mt-1 leading-snug">Pindahan Kos &amp; Rumah</h3>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); toggleFavoritItem('pindahan', 'Pindahan Kos & Rumah')" class="fav-heart-btn w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center text-sm transition-transform active:scale-90 shrink-0" title="Hapus dari Favorit">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1 leading-relaxed line-clamp-2">
                        Bantuan packing, angkut perabotan berat ke mobil pick-up, dan bongkar barang di tempat baru.
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] text-slate-400 block leading-none">Mulai dari</span>
                            <strong class="text-xs font-black text-slate-900 mt-0.5 block">Rp 150.000</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="event.stopPropagation(); openModernServiceDetail('Pindahan')" class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                Detail
                            </button>
                            <button type="button" onclick="event.stopPropagation(); pesanLayananFavorit('Pindahan Kos & Rumah', '150.000')" class="px-3 py-1.5 rounded-xl text-[10.5px] font-extrabold text-white bg-[#173B67] hover:bg-[#0E1D31] transition-all shadow-xs flex items-center gap-1">
                                Pesan <i class="fa-solid fa-arrow-right text-[8.5px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAVORIT 4: BASIC DAILY CLEANING --}}
        <div class="fav-item-card card-sb p-3.5 bg-white border border-slate-200/80 shadow-xs relative transition-all cursor-pointer hover:border-sky-300 active:scale-[0.99] group" data-id="daily-clean" data-kategori="kebersihan" onclick="openModernServiceDetail('Basic Daily Cleaning')">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 text-lg shrink-0">
                    <i class="fa-solid fa-spray-can-sparkles"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-1">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-teal-100 text-teal-800">Kebersihan</span>
                            <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors mt-1 leading-snug">Basic Daily Cleaning (Sapu &amp; Pel)</h3>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); toggleFavoritItem('daily-clean', 'Basic Daily Cleaning')" class="fav-heart-btn w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center text-sm transition-transform active:scale-90 shrink-0" title="Hapus dari Favorit">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1 leading-relaxed line-clamp-2">
                        Sapu bersih, pel lantai ruangan dengan cairan wangi, dan lap debu meja/jendela harian.
                    </p>
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[8.5px] text-slate-400 block leading-none">Mulai dari</span>
                            <strong class="text-xs font-black text-slate-900 mt-0.5 block">Rp 50.000</strong>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="event.stopPropagation(); openModernServiceDetail('Basic Daily Cleaning')" class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                                Detail
                            </button>
                            <button type="button" onclick="event.stopPropagation(); pesanLayananFavorit('Basic Daily Cleaning', '50.000')" class="px-3 py-1.5 rounded-xl text-[10.5px] font-extrabold text-white bg-[#173B67] hover:bg-[#0E1D31] transition-all shadow-xs flex items-center gap-1">
                                Pesan <i class="fa-solid fa-arrow-right text-[8.5px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- EMPTY STATE (TAMPIL JIKA SEMUA FAVORIT DIHAPUS) --}}
    <div id="favEmptyState" class="card-sb p-6 text-center bg-white border border-slate-200/80 space-y-2.5">
        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-400 flex items-center justify-center text-2xl mx-auto">
            <i class="fa-regular fa-heart"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold text-slate-800">Belum Ada Layanan Favorit</h4>
            <p class="text-[10px] text-slate-500 mt-1 max-w-[260px] mx-auto">
                Tandai ikon hati pada katalog layanan agar Anda dapat memesannya kembali secara cepat kapan saja.
            </p>
        </div>
        <button type="button" onclick="if(typeof spaNavigateTo === 'function') spaNavigateTo('{{ route('customer.layanan') }}'); else window.location.href='{{ route('customer.layanan') }}';" class="mt-2 px-4 py-2 rounded-xl bg-[#173B67] text-white font-bold text-[11px] shadow-xs cursor-pointer">
            Jelajahi Katalog Layanan
        </button>
    </div>

    {{-- REKOMENDASI LAYANAN LAINNYA UNTUK DITAMBAHKAN --}}
    <section class="card-sb p-3.5 bg-gradient-to-br from-slate-50 to-sky-50/40 border border-slate-200/80 space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-star text-yellow-400"></i> Rekomendasi Tambahan
            </h4>
            <a href="{{ route('customer.layanan') }}" class="text-[10px] font-bold text-sky-600 hover:underline">
                Lihat Semua
            </a>
        </div>
        <div class="grid grid-cols-2 gap-2 text-left">
            <div class="p-2.5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between cursor-pointer hover:border-sky-300 active:scale-[0.98] transition-all group" onclick="openModernServiceDetail('Bersihkan Kamar Mandi')">
                <div>
                    <span class="text-xs">🚿</span>
                    <strong class="text-[11px] font-bold text-slate-900 group-hover:text-blue-600 transition-colors block mt-1">Kamar Mandi</strong>
                    <span class="text-[9.5px] text-slate-500 block">Kuras bak &amp; kloset</span>
                </div>
                <button type="button" onclick="event.stopPropagation(); tambahFavoritRekomendasi('kamar-mandi', 'Bersihkan Kamar Mandi')" class="mt-2 w-full py-1 text-[9.5px] font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors border border-sky-200 cursor-pointer">
                    + Simpan Favorit
                </button>
            </div>

            <div class="p-2.5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between cursor-pointer hover:border-sky-300 active:scale-[0.98] transition-all group" onclick="openModernServiceDetail('Antar / Jemput Barang')">
                <div>
                    <span class="text-xs">🛵</span>
                    <strong class="text-[11px] font-bold text-slate-900 group-hover:text-blue-600 transition-colors block mt-1">Antar Jemput</strong>
                    <span class="text-[9.5px] text-slate-500 block">Ambil paket / kilat</span>
                </div>
                <button type="button" onclick="event.stopPropagation(); tambahFavoritRekomendasi('antar-jemput', 'Antar / Jemput Barang')" class="mt-2 w-full py-1 text-[9.5px] font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors border border-sky-200 cursor-pointer">
                    + Simpan Favorit
                </button>
            </div>
        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
    // Ambil daftar favorit dari localStorage
    function getStoredFavorites() {
        try {
            const raw = localStorage.getItem('sb_customer_favorites');
            if (raw) {
                const favorites = JSON.parse(raw);
                if (Array.isArray(favorites)) {
                    return favorites.filter(id => ['full-clean', 'pindahan', 'daily-clean'].includes(id));
                }
            }
        } catch (e) {}
        return [];
    }

    function saveStoredFavorites(favs) {
        localStorage.setItem('sb_customer_favorites', JSON.stringify(favs));
        updateFavoriteCounters();
    }

    function updateFavoriteCounters() {
        const favs = getStoredFavorites();
        const total = favs.length;

        const headerBadge = document.getElementById('favHeaderBadge');
        if (headerBadge) headerBadge.innerText = `${total} Tersimpan`;

        const summary = document.getElementById('favCountSummary');
        if (summary) summary.innerText = `${total} Layanan`;

        const tabSemua = document.getElementById('tabCountSemua');
        if (tabSemua) tabSemua.innerText = total;

        const emptyState = document.getElementById('favEmptyState');
        const container = document.getElementById('favoritListContainer');
        if (emptyState && container) {
            emptyState.classList.toggle('hidden', total > 0);
            container.classList.toggle('hidden', total === 0);
        }

        document.querySelectorAll('.fav-item-card').forEach(card => {
            card.classList.toggle('hidden', !favs.includes(card.dataset.id));
        });
    }

    // Toggle hapus / simpan favorit
    function toggleFavoritItem(id, nama) {
        let favs = getStoredFavorites();
        const idx = favs.indexOf(id);

        if (idx !== -1) {
            favs.splice(idx, 1);
            saveStoredFavorites(favs);
            // Sembunyikan elemen
            const card = document.querySelector(`.fav-item-card[data-id="${id}"]`);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 200);
            }
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Favorit Dihapus', `"${nama}" dihapus dari layanan favorit Anda.`, 'info');
            }
        } else {
            favs.push(id);
            saveStoredFavorites(favs);
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Favorit Disimpan ❤️', `"${nama}" ditambahkan ke layanan favorit Anda.`, 'success');
            }
        }
    }

    // Tambahkan dari rekomendasi
    function tambahFavoritRekomendasi(id, nama) {
        let favs = getStoredFavorites();
        if (!favs.includes(id)) {
            favs.push(id);
            saveStoredFavorites(favs);
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Ditambahkan ke Favorit! 🎉', `"${nama}" berhasil disimpan ke daftar favorit.`, 'success');
            }
            if (typeof renderFavoritesFromStorage === 'function') {
                renderFavoritesFromStorage();
            } else if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(window.location.href, false);
            }
        } else {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Sudah Ada', `"${nama}" sudah tersimpan di layanan favorit Anda.`, 'info');
            }
        }
    }

    // Pesan cepat layanan favorit langsung ke form dashboard
    function pesanLayananFavorit(namaLayanan, budget) {
        localStorage.setItem('selected_service_name', namaLayanan);
        localStorage.setItem('selected_service_budget', budget);

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Membuka Formulir', `Menyiapkan pesanan ${namaLayanan}...`, 'info');
        }

        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (user && user.isLoggedIn && user.role === 'customer') {
            const target = "{{ route('customer.dashboard') }}?tab=dashboard&layanan=" + encodeURIComponent(namaLayanan);
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        } else {
            const target = "{{ route('customer.home') }}#orderFormCard";
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        }
    }

    // Filter kategori favorit
    function filterFavoritKategori(kategori, btnEl) {
        document.querySelectorAll('.fav-tab-btn').forEach(btn => {
            btn.className = 'fav-tab-btn px-3 py-1.5 rounded-full bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shrink-0';
        });
        if (btnEl) {
            btnEl.className = 'fav-tab-btn px-3 py-1.5 rounded-full bg-[#173B67] text-white shadow-xs shrink-0';
        }

        const favorites = getStoredFavorites();
        const cards = document.querySelectorAll('.fav-item-card');
        let count = 0;
        cards.forEach(card => {
            const cardKategori = (card.getAttribute('data-kategori') || '').toLowerCase();
            if (favorites.includes(card.dataset.id) && (kategori === 'semua' || cardKategori === kategori)) {
                card.classList.remove('hidden');
                count++;
            } else {
                card.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('favEmptyState');
        if (emptyState) {
            emptyState.classList.toggle('hidden', count > 0);
        }
    }
    window.filterFavoritKategori = filterFavoritKategori;

    document.addEventListener('DOMContentLoaded', updateFavoriteCounters);
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Admin Dashboard - SayaBantu.com')
@section('header-gap-style', '0.6cm')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan menyeluruh aktivitas platform & operasional SayaBantu.com')

{{-- =========================================================
     HERO AREA: 4 KARTU RINGKASAN UTAMA
========================================================= --}}
@section('hero')
<section class="px-5" style="padding-top: 0.6cm; padding-bottom: 0; margin-bottom: 0;">

    {{-- 4 KARTU RINGKASAN UTAMA (LEBIH COMPACT & RAPI, GAP 0.6CM) --}}
    <div class="grid grid-cols-2" style="gap: 0.6cm;">

        {{-- 1. TOTAL PENGGUNA TERDAFTAR --}}
        <div
            class="stat-card cursor-pointer hover:scale-[1.01] active:scale-[0.99] transition-all bg-white border border-gray-200/80 rounded-2xl p-3 shadow-sm relative overflow-hidden"
            onclick="adminSpaNavigateTo('{{ route('admin.pengguna') }}')"
            title="Kelola Pengguna Terdaftar"
        >
            <div class="stat-decoration bg-blue-50/70 absolute -right-3 -bottom-3 w-16 h-16 rounded-full pointer-events-none"></div>

            <div class="relative flex items-start justify-between">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs border border-blue-100 shadow-xs">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[8px] font-semibold text-emerald-700 border border-emerald-200">
                    +8.4%
                </span>
            </div>

            <div class="relative mt-2">
                <p class="text-[9.5px] font-medium text-gray-500">Total Pengguna</p>
                <p class="mt-0.5 text-[18px] font-black leading-none text-gray-900" data-rt-stat="total_pengguna">
                    {{ number_format($totalPengguna ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[8px] text-gray-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-sky-500 text-[6.5px]"></i> Terdaftar di sistem
                </p>
            </div>
        </div>

        {{-- 2. TOTAL MITRA --}}
        <div
            class="stat-card cursor-pointer hover:scale-[1.01] active:scale-[0.99] transition-all bg-white border border-gray-200/80 rounded-2xl p-3 shadow-sm relative overflow-hidden"
            onclick="adminSpaNavigateTo('{{ route('admin.pengguna') }}')"
            title="Kelola Data Mitra"
        >
            <div class="stat-decoration bg-teal-50/70 absolute -right-3 -bottom-3 w-16 h-16 rounded-full pointer-events-none"></div>

            <div class="relative flex items-start justify-between">
                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs border border-teal-100 shadow-xs">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>
                <span class="rounded-full bg-teal-50 px-1.5 py-0.5 text-[8px] font-semibold text-teal-700 border border-teal-200">
                    Terverifikasi
                </span>
            </div>

            <div class="relative mt-2">
                <p class="text-[9.5px] font-medium text-gray-500">Total Mitra</p>
                <p class="mt-0.5 text-[18px] font-black leading-none text-gray-900" data-rt-stat="total_mitra">
                    {{ $totalMitra ?? 0 }}
                </p>
                <p class="mt-1 text-[8px] text-gray-400 flex items-center gap-1">
                    <i class="fa-solid fa-bolt text-teal-500 text-[6.5px]"></i> Penyedia aktif
                </p>
            </div>
        </div>

        {{-- 3. JUMLAH SURUHAN AKTIF --}}
        <div
            class="stat-card cursor-pointer hover:scale-[1.01] active:scale-[0.99] transition-all bg-white border border-gray-200/80 rounded-2xl p-3 shadow-sm relative overflow-hidden"
            onclick="adminSpaNavigateTo('{{ route('admin.pesanan') }}')"
            title="Kelola Suruhan Aktif"
        >
            <div class="stat-decoration bg-amber-50/70 absolute -right-3 -bottom-3 w-16 h-16 rounded-full pointer-events-none"></div>

            <div class="relative flex items-start justify-between">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs border border-amber-100 shadow-xs">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <span class="rounded-full bg-amber-50 px-1.5 py-0.5 text-[8px] font-semibold text-amber-700 border border-amber-200">
                    Sedang Jalan
                </span>
            </div>

            <div class="relative mt-2">
                <p class="text-[9.5px] font-medium text-gray-500">Suruhan Aktif</p>
                <p class="mt-0.5 text-[18px] font-black leading-none text-gray-900" data-rt-stat="suruhan_aktif">
                    {{ $suruhanAktif ?? 0 }}
                </p>
                <p class="mt-1 text-[8px] text-gray-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Dalam pengerjaan
                </p>
            </div>
        </div>

        {{-- 4. MENUNGGU KONFIRMASI ADMIN --}}
        <div
            class="stat-card cursor-pointer hover:scale-[1.01] active:scale-[0.99] transition-all bg-gradient-to-br from-white to-rose-50/30 border border-rose-200/80 rounded-2xl p-3 shadow-sm relative overflow-hidden"
            onclick="adminSpaNavigateTo('{{ route('admin.konfirmasi') }}')"
            title="Buka Konfirmasi Pekerjaan"
        >
            <div class="stat-decoration bg-rose-100/40 absolute -right-3 -bottom-3 w-16 h-16 rounded-full pointer-events-none"></div>

            <div class="relative flex items-start justify-between">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs border border-rose-200 shadow-xs">
                    <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                </div>
                <span class="rounded-full bg-rose-50 px-1.5 py-0.5 text-[8px] font-bold text-rose-600 border border-rose-200">
                    Butuh Aksi
                </span>
            </div>

            <div class="relative mt-2">
                <p class="text-[9.5px] font-medium text-rose-800">Menunggu Konfirmasi</p>
                <p class="mt-0.5 text-[18px] font-black leading-none text-rose-600" data-rt-stat="menunggu_konfirmasi">
                    {{ $menungguKonfirmasi ?? 0 }}
                </p>
                <p class="mt-1 text-[8px] text-rose-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-[6.5px]"></i> Kesepakatan baru
                </p>
            </div>
        </div>

    </div>

</section>
@endsection

{{-- =========================================================
     KONTEN DASHBOARD: PERLU DITANGANI, AKTIVITAS & TRANSAKSI
========================================================= --}}
@section('content')

{{-- =========================================================
     JUDUL SEKSI: OPERASIONAL PLATFORM
========================================================= --}}
<div style="margin-top: 0.6cm; margin-bottom: 0.6cm;" class="px-1">
    <h3 class="text-xs font-bold text-white tracking-wide" style="margin: 0; padding: 0; line-height: 1.2;">
        Operasional Platform
    </h3>
    <p class="text-[9px] text-white/80" style="margin-top: 2px; margin-bottom: 0; padding: 0; line-height: 1.3;">
        Pantauan tindakan administratif dan respon operasional sistem
    </p>
</div>

{{-- =========================================================
     BAGIAN 1: PERLU DITANGANI (ADMIN ACTION REQUIRED)
========================================================= --}}
<section class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm" style="margin-top: 0;">
    <div class="flex items-center justify-between pb-2 border-b border-gray-100" style="margin-bottom: 0.6cm;">
        <div>
            <h2 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                <i class="fa-solid fa-bell-concierge text-rose-500"></i>
                Perlu Ditangani
            </h2>
            <p class="mt-0.5 text-[9px] text-gray-400">
                Pekerjaan administratif yang membutuhkan tindak lanjut Admin
            </p>
        </div>
        <span id="dashPendingBadge" class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">0 Kategori</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.6cm;">

        {{-- EMPTY STATE KETIKA TIDAK ADA YANG PERLU DITANGANI --}}
        <div id="dashNoPendingWidget" class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-1">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg mb-1"></i>
            <p class="text-xs font-bold text-gray-900">Semua Operasional Lancar</p>
            <p class="text-[9px] text-gray-400">Tidak ada kesepakatan atau laporan yang memerlukan tindakan mendesak.</p>
        </div>

        @foreach(($pendingCompletionOrders ?? collect())->take(5) as $completionOrder)
            <a href="{{ route('admin.pesanan.detail', ltrim($completionOrder->code, '#')) }}" class="block p-3 rounded-xl bg-indigo-50/70 border border-indigo-200 shadow-2xs hover:border-indigo-400 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs border border-indigo-200 shrink-0">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[8.5px] font-bold font-mono px-1.5 py-0.5 rounded bg-white text-indigo-800 border border-indigo-200">{{ $completionOrder->code }}</span>
                                <span class="text-xs font-bold text-gray-900 truncate">{{ $completionOrder->layanan }}</span>
                            </div>
                            <p class="text-[9.5px] text-gray-700 mt-1">
                                {{ $completionOrder->customer_name }} telah mengonfirmasi pekerjaan selesai. Menunggu konfirmasi akhir Admin.
                            </p>
                        </div>
                    </div>
                    <span class="shrink-0 px-2.5 py-1.5 rounded-lg bg-[#173B67] text-white text-[9px] font-bold">Periksa</span>
                </div>
            </a>
        @endforeach

        {{-- ITEM 1: KESEPAKATAN BELUM DIKONFIRMASI (DINAMIS) --}}
        <div id="dashDealWidget" class="hidden p-3 rounded-xl bg-amber-50/40 border border-amber-200/80 shadow-2xs hover:border-amber-400 active:scale-[0.99] transition-all cursor-pointer group" onclick="if(typeof adminSpaNavigateTo === 'function') adminSpaNavigateTo('{{ route('admin.konfirmasi') }}'); else window.location.href='{{ route('admin.konfirmasi') }}';">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs border border-amber-200 shrink-0 mt-0.5">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span id="dashDealBadgeId" class="text-[8.5px] font-bold font-mono px-1.5 py-0.5 rounded bg-white text-gray-700 border border-amber-200 whitespace-nowrap shrink-0 inline-flex items-center justify-center">-</span>
                            <span class="text-xs font-bold text-gray-900 group-hover:text-amber-800 transition-colors truncate min-w-0 flex-1">Kesepakatan Belum Dikonfirmasi</span>
                        </div>
                        <p class="text-[9.5px] text-gray-600 mt-0.5" id="dashDealDesc">
                            Customer & Mitra telah sepakat tarif. Menunggu konfirmasi Admin.
                        </p>
                        <div class="mt-1 flex items-center gap-2 text-[8.5px] text-gray-400">
                            <span><i class="fa-regular fa-clock text-[7.5px]"></i> Baru</span>
                            <span>•</span>
                            <span class="text-amber-600 font-semibold">Status: Menunggu Konfirmasi</span>
                        </div>
                    </div>
                </div>
                <a
                    href="{{ route('admin.konfirmasi') }}"
                    onclick="event.preventDefault(); event.stopPropagation(); adminSpaNavigateTo('{{ route('admin.konfirmasi') }}');"
                    class="shrink-0 px-2.5 py-1.5 rounded-lg bg-[#173B67] hover:bg-[#1E4E8C] text-white text-[10px] font-bold flex items-center gap-1 shadow-xs transition-all"
                >
                    <span>Periksa</span>
                    <i class="fa-solid fa-chevron-right text-[7px]"></i>
                </a>
            </div>
        </div>

        {{-- ITEM 2: PENGADUAN BELUM SELESAI (DINAMIS DARI SB_COMPLAINTS) --}}
        <div id="dashboardComplaintWidget" class="hidden p-3 rounded-xl bg-rose-50/40 border border-rose-200/80 shadow-2xs hover:border-rose-400 active:scale-[0.99] transition-all cursor-pointer group" onclick="if(typeof adminSpaNavigateTo === 'function') adminSpaNavigateTo('{{ route('admin.pengaduan') }}'); else window.location.href='{{ route('admin.pengaduan') }}';">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs border border-rose-200 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span id="dashCompBadgeId" class="text-[8.5px] font-bold font-mono px-1.5 py-0.5 rounded bg-white text-rose-700 border border-rose-200 whitespace-nowrap shrink-0 inline-flex items-center justify-center">-</span>
                            <span class="text-xs font-bold text-gray-900 group-hover:text-rose-700 transition-colors truncate min-w-0 flex-1">Pengaduan Butuh Tindakan</span>
                        </div>
                        <p class="text-[9.5px] text-gray-600 mt-0.5" id="dashCompDesc">
                            Laporan penanganan pengaduan suruhan.
                        </p>
                        <div class="mt-1 flex items-center gap-2 text-[8.5px] text-gray-400">
                            <span><i class="fa-regular fa-clock text-[7.5px]"></i> Baru</span>
                            <span>•</span>
                            <span class="text-rose-600 font-semibold" id="dashCompStatusLabel">Status: Perlu Tindakan</span>
                        </div>
                    </div>
                </div>
                <a
                    href="{{ route('admin.pengaduan') }}"
                    onclick="event.preventDefault(); event.stopPropagation(); adminSpaNavigateTo('{{ route('admin.pengaduan') }}');"
                    class="shrink-0 px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-bold flex items-center gap-1 shadow-xs transition-all"
                >
                    <span>Tinjau</span>
                    <i class="fa-solid fa-chevron-right text-[7px]"></i>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- =========================================================
     BAGIAN 2: RINGKASAN TRANSAKSI & KOMISI PLATFORM
========================================================= --}}
<section class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm" style="margin-top: 0.6cm;">
    <div class="flex items-center justify-between pb-2 border-b border-gray-100" style="margin-bottom: 0.6cm;">
        <div>
            <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-line text-emerald-600"></i>
                Ringkasan Transaksi & Komisi
            </h3>
            <p class="text-[9px] text-gray-400 mt-0.5">Pantauan nilai transaksi & bagi hasil platform periode berjalan</p>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            Bulan Ini
        </span>
    </div>

    <div class="grid grid-cols-2" style="gap: 0.6cm;">
        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 cursor-pointer hover:border-emerald-300 active:scale-[0.99] transition-all group" onclick="if(typeof adminSpaNavigateTo === 'function') adminSpaNavigateTo('{{ route('admin.pembayaran') }}'); else window.location.href='{{ route('admin.pembayaran') }}';">
            <span class="text-[9px] text-gray-500 block font-medium">NILAI TRANSAKSI <i class="fa-solid fa-chevron-right text-[7px] ml-0.5"></i></span>
            <span class="text-sm font-black text-gray-900 group-hover:text-emerald-700 transition-colors block mt-0.5"><span id="dashNilaiTransaksi">Rp 0</span></span>
            <span class="text-[8.5px] text-emerald-600 mt-1 flex items-center gap-1 font-semibold">
                <i class="fa-solid fa-arrow-trend-up text-[7px]"></i> <span id="dashJumlahTransaksi">0 Transaksi Sukses</span>
            </span>
        </div>

        <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-100 cursor-pointer hover:border-sky-300 active:scale-[0.99] transition-all group" onclick="if(typeof adminSpaNavigateTo === 'function') adminSpaNavigateTo('{{ route('admin.pembayaran') }}'); else window.location.href='{{ route('admin.pembayaran') }}';">
            <span class="text-[9px] text-[#173B67] block font-medium">KOMISI PLATFORM (10%) <i class="fa-solid fa-chevron-right text-[7px] ml-0.5"></i></span>
            <span class="text-sm font-black text-[#173B67] group-hover:text-sky-800 transition-colors block mt-0.5"><span id="dashKomisiPlatform">Rp 0</span></span>
            <span class="text-[8.5px] text-sky-700 mt-1 flex items-center gap-1 font-semibold">
                <i class="fa-solid fa-shield-halved text-[7px]"></i> Pendapatan Bersih
            </span>
        </div>
    </div>

    <div class="pt-1 flex items-center justify-between text-[9px] text-gray-500" style="margin-top: 0.6cm;">
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Pembayaran QRIS: <strong>72%</strong>
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
            VA Bank: <strong>28%</strong>
        </span>
        <a href="{{ route('admin.pembayaran') }}" class="font-bold text-[#173B67] hover:underline flex items-center gap-0.5">
            Detail <i class="fa-solid fa-chevron-right text-[7px]"></i>
        </a>
    </div>
</section>

{{-- =========================================================
     BAGIAN 3: AKTIVITAS TERBARU (LIVE ACTIVITY TIMELINE)
========================================================= --}}
<section class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm" style="margin-top: 0.6cm;">
    <div class="flex items-center justify-between pb-2 border-b border-gray-100" style="margin-bottom: 0.6cm;">
        <div>
            <h2 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                Aktivitas Terbaru
            </h2>
            <p class="mt-0.5 text-[9px] text-gray-400">
                Riwayat pendaftaran, status suruhan, dan tindakan administratif
            </p>
        </div>
    </div>

    <div id="sbRecentActivitiesContainer" class="cursor-pointer" onclick="if (typeof adminSpaNavigateTo === 'function') adminSpaNavigateTo('{{ route('admin.pesanan') }}')" style="display: flex; flex-direction: column; gap: 0.6cm;">
        <div id="dashEmptyActivities" class="p-6 text-center space-y-1 bg-slate-50 rounded-xl border border-slate-200/80">
            <i class="fa-regular fa-clock text-slate-400 text-lg mb-1"></i>
            <p class="text-xs font-bold text-gray-800">Belum Ada Aktivitas</p>
            <p class="text-[9px] text-gray-400">Aktivitas pendaftaran dan perubahan status pesanan akan tercatat di sini.</p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activeCategoriesCount = {{ ($pendingCompletionOrders ?? collect())->isNotEmpty() ? 1 : 0 }};

        // 1. Sync Kesepakatan Belum Dikonfirmasi dari sb_customer_orders
        try {
            const CLEAN_KEY = 'sb_zero_reset_v6';
            if (!localStorage.getItem(CLEAN_KEY)) {
                localStorage.removeItem('sb_customer_orders');
                localStorage.removeItem('sb_active_order');
                localStorage.removeItem('sb_mitra_orders');
                localStorage.removeItem('sb_mitra_bids');
                localStorage.setItem(CLEAN_KEY, 'true');
            }
            const rawOrders = localStorage.getItem('sb_customer_orders');
            let orders = rawOrders ? JSON.parse(rawOrders) : [];

            // Discard any legacy demo codes
            const legacyCodes = ['9446', '1404', '5181', '1643', '1458', '9012', '8841', '8720', '8821', '8492'];
            if (Array.isArray(orders)) {
                orders = orders.filter(o => {
                    const codeStr = String(o.code || o.id || '');
                    return !legacyCodes.some(d => codeStr.includes(d));
                });
            }
            
            // Stats
            const suruhanAktif = orders.filter(o => !['selesai', 'dibatalkan'].includes(o.status));
            const elSuruhanAktif = document.querySelector('[data-rt-stat="suruhan_aktif"]');
            if (elSuruhanAktif) elSuruhanAktif.innerText = suruhanAktif.length;

            const waitingDeals = orders.filter(o => o.status === 'menunggu_konfirmasi_admin');
            const elMenungguKonf = document.querySelector('[data-rt-stat="menunggu_konfirmasi"]');
            if (elMenungguKonf) elMenungguKonf.innerText = waitingDeals.length;

            const dealWidget = document.getElementById('dashDealWidget');
            if (dealWidget) {
                if (waitingDeals.length > 0) {
                    const firstDeal = waitingDeals[0];
                    const badge = document.getElementById('dashDealBadgeId');
                    const desc = document.getElementById('dashDealDesc');
                    if (badge) badge.innerText = firstDeal.code || '#SB';
                    if (desc) {
                        const customerLabel = firstDeal.customerName || 'Customer';
                        const mitraLabel = firstDeal.mitraName || 'Mitra';
                        const tarifLabel = firstDeal.budget ? ('Rp ' + firstDeal.budget) : '-';
                        desc.innerHTML = 'Customer <strong>' + customerLabel + '</strong> & Mitra <strong>' + mitraLabel + '</strong> sepakat tarif ' + tarifLabel + '.';
                    }
                    dealWidget.classList.remove('hidden');
                    activeCategoriesCount++;
                } else {
                    dealWidget.classList.add('hidden');
                }
            }

            // Transaksi & Komisi
            const completedOrders = orders.filter(o => o.status === 'selesai');
            let totalNilai = 0;
            completedOrders.forEach(o => {
                const num = parseInt((o.budget || '0').replace(/[^0-9]/g, '')) || 0;
                totalNilai += num;
            });
            const komisi = Math.round(totalNilai * 0.1);

            const elNilai = document.getElementById('dashNilaiTransaksi');
            const elJml = document.getElementById('dashJumlahTransaksi');
            const elKomisi = document.getElementById('dashKomisiPlatform');

            if (elNilai) elNilai.innerText = 'Rp ' + totalNilai.toLocaleString('id-ID');
            if (elJml) elJml.innerText = completedOrders.length + ' Transaksi Sukses';
            if (elKomisi) elKomisi.innerText = 'Rp ' + komisi.toLocaleString('id-ID');

        } catch(e) {}

        // 2. Sync Pengaduan dari sb_complaints
        try {
            const raw = localStorage.getItem('sb_complaints');
            const list = raw ? JSON.parse(raw) : [];
            const activeOnly = list.filter(c => c.status !== 'selesai' && c.status !== 'ditolak');
            const widget = document.getElementById('dashboardComplaintWidget');
            if (widget) {
                if (activeOnly.length > 0) {
                    const latest = activeOnly[0];
                    const idEl = document.getElementById('dashCompBadgeId');
                    const descEl = document.getElementById('dashCompDesc');
                    const statusEl = document.getElementById('dashCompStatusLabel');
                    if (idEl) idEl.innerText = latest.id;
                    if (descEl) {
                        const kategoriLabel = latest.category || 'Keluhan';
                        const orderLabel = latest.orderId || '-';
                        const pelaporLabel = latest.reporterName || '-';
                        descEl.innerHTML = 'Laporan <strong>' + kategoriLabel + '</strong> pada suruhan <span class="font-mono font-semibold">' + orderLabel + '</span> (Pelapor: ' + pelaporLabel + ').';
                    }
                    if (statusEl) statusEl.innerText = `Status: ${latest.status === 'dalam_mediasi' ? 'Dalam Mediasi' : (latest.status === 'menunggu_bukti' ? 'Menunggu Bukti' : (latest.status === 'bukti_dikirim' ? 'Bukti Diterima' : 'Pemeriksaan'))}`;
                    widget.classList.remove('hidden');
                    activeCategoriesCount++;
                } else {
                    widget.classList.add('hidden');
                }
            }
        } catch(e) {}

        // 3. Toggle Empty State Perlu Ditangani & Badge
        const emptyPending = document.getElementById('dashNoPendingWidget');
        const pendingBadge = document.getElementById('dashPendingBadge');
        if (activeCategoriesCount === 0) {
            if (emptyPending) emptyPending.classList.remove('hidden');
            if (pendingBadge) {
                pendingBadge.className = 'px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap';
                pendingBadge.innerText = '0 Kategori';
            }
        } else {
            if (emptyPending) emptyPending.classList.add('hidden');
            if (pendingBadge) {
                pendingBadge.className = 'px-2 py-0.5 rounded-full text-[9px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 whitespace-nowrap';
                pendingBadge.innerText = activeCategoriesCount + ' Kategori';
            }
        }
    });
</script>
@endpush

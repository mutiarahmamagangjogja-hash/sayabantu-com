@extends('layouts.admin')

@section('title', 'Data Pengguna - SayaBantu.com')
@section('header-gap-style', '0.6cm')
@section('page-title', 'Data Pengguna')
@section('page-subtitle', 'Kelola Seluruh Pengguna & Mitra')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}
@section('hero')
<div class="px-5 pb-0" style="padding-top: 0.6cm;">
    {{-- ROLE SWITCHER: CUSTOMER & MITRA --}}
    <div class="grid grid-cols-2 text-xs" style="gap: 0.6cm; margin-bottom: 0.6cm;">
        <button
            type="button"
            id="tabBtnCustomer"
            onclick="switchPenggunaTab('customer')"
            class="py-2 px-3 rounded-xl {{ ($activeTab ?? 'customer') === 'customer' ? 'bg-[#173B67] text-white font-bold border border-sky-400/50 shadow-sm' : 'bg-white/10 hover:bg-white/20 text-gray-300 font-medium border border-white/10' }} text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none"
        >
            <i class="fa-solid fa-users text-sky-400"></i>
            <span>Customer (<span id="tabCustomerCount">{{ $totalCustomers ?? count($customers ?? []) }}</span>)</span>
        </button>
        <button
            type="button"
            id="tabBtnMitra"
            onclick="switchPenggunaTab('mitra')"
            class="py-2 px-3 rounded-xl {{ ($activeTab ?? 'customer') === 'mitra' ? 'bg-[#173B67] text-white font-bold border border-sky-400/50 shadow-sm' : 'bg-white/10 hover:bg-white/20 text-gray-300 font-medium border border-white/10' }} text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none"
        >
            <i class="fa-solid fa-handshake-angle text-teal-300"></i>
            <span>Mitra (<span id="tabHeroMitraCount">{{ $totalMitras ?? count($mitras ?? []) }}</span>)</span>
        </button>
    </div>

    {{-- =====================================================
         HERO SECTION: CUSTOMER (KONSUMEN)
    ===================================================== --}}
    <div id="customerHeroSection" class="{{ ($activeTab ?? 'customer') === 'customer' ? '' : 'hidden' }}">
        {{-- SUMMARY STATS PENGGUNA --}}
        <div class="p-4 rounded-[22px] bg-white/[0.14] backdrop-blur-md border border-white/25 shadow-xl">
            <div class="flex items-center justify-between pb-2.5 border-b border-white/15" style="margin-bottom: 0.6cm;">
                <span class="text-xs font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-sky-300"></i>
                    Statistik Akun Konsumen
                </span>
                <span id="heroCustomerBadge" class="text-[9.5px] font-bold px-2.5 py-0.5 rounded-full bg-sky-500/30 text-sky-200 border border-sky-400/40">
                    {{ $totalCustomers ?? count($customers ?? []) }} Terdaftar
                </span>
            </div>

            <div class="grid grid-cols-4 gap-2 text-center" id="penggunaStatFilters">
                <button type="button" class="pengguna-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-sky-400 shadow-md shadow-sky-500/25 cursor-pointer transition-all scale-[1.03] text-center focus:outline-none ring-2 ring-sky-400/30" onclick="filterPengguna('all')" data-filter="all" title="Semua Pengguna">
                    <div class="text-xs font-black text-white" id="statCustTotal">{{ $totalCustomers ?? count($customers ?? []) }}</div>
                    <div class="text-[8.5px] text-white font-bold mt-0.5">Total</div>
                </button>
                <button type="button" class="pengguna-stat-tab w-full p-2 rounded-xl bg-white/[0.10] border border-white/20 opacity-75 cursor-pointer hover:bg-white/[0.16] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterPengguna('aktif')" data-filter="aktif" title="Pengguna Aktif">
                    <div class="text-xs font-black text-emerald-300" id="statCustAktif">{{ $aktifCustomers ?? 0 }}</div>
                    <div class="text-[8.5px] text-gray-200 font-medium mt-0.5">Aktif</div>
                </button>
                <button type="button" class="pengguna-stat-tab w-full p-2 rounded-xl bg-white/[0.10] border border-white/20 opacity-75 cursor-pointer hover:bg-white/[0.16] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterPengguna('terverifikasi')" data-filter="terverifikasi" title="Pengguna Terverifikasi">
                    <div class="text-xs font-black text-sky-300" id="statCustTerverifikasi">{{ $verifCustomers ?? 0 }}</div>
                    <div class="text-[8.5px] text-gray-200 font-medium mt-0.5">Terverifikasi</div>
                </button>
                <button type="button" class="pengguna-stat-tab w-full p-2 rounded-xl bg-white/[0.10] border border-white/20 opacity-75 cursor-pointer hover:bg-white/[0.16] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterPengguna('baru')" data-filter="baru" title="Baru Mendaftar">
                    <div class="text-xs font-black text-amber-300" id="statCustBaru">{{ $baruCustomers ?? 0 }}</div>
                    <div class="text-[8.5px] text-gray-200 font-medium mt-0.5">Baru</div>
                </button>
            </div>
        </div>

        {{-- SEARCH BAR INTERAKTIF --}}
        <div style="margin-top: 0.6cm;">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                <input
                    type="text"
                    id="searchPenggunaInput"
                    placeholder="Cari nama, email, nomor HP..."
                    oninput="searchPengguna(this.value)"
                    class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-white/15 text-white placeholder-gray-300 text-xs border border-white/25 focus:outline-none focus:border-sky-400 focus:bg-white/20 transition-all font-medium"
                >
                <button
                    type="button"
                    id="clearSearchBtn"
                    onclick="clearSearchPengguna()"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-white text-xs hidden"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- FILTER TABS PENGGUNA --}}
        <div class="flex items-center justify-center gap-1.5 flex-wrap text-[10px]" style="margin-top: 0.6cm;">
            <button type="button" class="pengguna-filter-tab px-3.5 py-1.5 rounded-full font-bold transition-all bg-white text-[#173B67] shadow-sm border border-white" onclick="filterPengguna('all')" data-status="all">
                Semua (<span id="filterCountSemua">{{ $totalCustomers ?? count($customers ?? []) }}</span>)
            </button>
            <button type="button" class="pengguna-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterPengguna('aktif')" data-status="aktif">
                Aktif (<span id="filterCountAktif">{{ $aktifCustomers ?? 0 }}</span>)
            </button>
            <button type="button" class="pengguna-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterPengguna('terverifikasi')" data-status="terverifikasi">
                Terverifikasi (<span id="filterCountTerverifikasi">{{ $verifCustomers ?? 0 }}</span>)
            </button>
            <button type="button" class="pengguna-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterPengguna('baru')" data-status="baru">
                Baru (<span id="filterCountBaru">{{ $baruCustomers ?? 0 }}</span>)
            </button>
        </div>
    </div>

    {{-- =====================================================
         HERO SECTION: MITRA (PENYEDIA JASA)
    ===================================================== --}}
    <div id="mitraHeroSection" class="{{ ($activeTab ?? 'customer') === 'mitra' ? '' : 'hidden' }}">
        {{-- SUMMARY STATS MITRA --}}
        <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
            <div class="flex items-center justify-between pb-2.5 border-b border-white/10" style="margin-bottom: 0.6cm;">
                <span class="text-xs font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-handshake-angle text-teal-400"></i>
                    Total Penyedia Terdaftar
                </span>
                <span id="heroMitraBadge" class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30">
                    {{ $totalMitras ?? count($mitras ?? []) }} Mitra
                </span>
            </div>

            {{-- KARTU STATISTIK MITRA (DAPAT DIKLIK UNTUK FILTER) --}}
            <div class="grid grid-cols-4 gap-2 text-center" id="mitraStatFilters">
                <button type="button" class="mitra-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-teal-400 shadow-md shadow-teal-500/25 cursor-pointer transition-all scale-[1.03] text-center focus:outline-none ring-2 ring-teal-400/30" onclick="filterMitra('all')" data-filter="all" title="Semua Mitra">
                    <div class="text-xs font-black text-white" id="statMitraTotal">{{ $totalMitras ?? count($mitras ?? []) }}</div>
                    <div class="text-[8px] text-white font-bold mt-0.5">Total</div>
                </button>
                <button type="button" class="mitra-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterMitra('aktif')" data-filter="aktif" title="Mitra Aktif / Sudah Terdaftar">
                    <div class="text-xs font-black text-emerald-400" id="statMitraAktif">{{ $aktifMitras ?? 0 }}</div>
                    <div class="text-[8px] text-gray-300 mt-0.5">Aktif Kerja</div>
                </button>
                <button type="button" class="mitra-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterMitra('verifikasi')" data-filter="verifikasi" title="Menunggu Verifikasi & ACC Admin">
                    <div class="text-xs font-black text-amber-400" id="statMitraVerifikasi">{{ $verifMitras ?? 0 }}</div>
                    <div class="text-[8px] text-gray-300 mt-0.5">Verifikasi</div>
                </button>
                <button type="button" class="mitra-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterMitra('suspend')" data-filter="suspend" title="Mitra Ditangguhkan">
                    <div class="text-xs font-black text-rose-400" id="statMitraSuspend">{{ $suspendMitras ?? 0 }}</div>
                    <div class="text-[8px] text-gray-300 mt-0.5">Suspend</div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA
========================================================= --}}
@section('content')
<div style="margin-top: 0.6cm; display: flex; flex-direction: column; gap: 0.6cm;">

    {{-- =====================================================
         PANEL KONTEN 1: DAFTAR KONSUMEN / CUSTOMER
    ===================================================== --}}
    <div id="customerContentSection" class="space-y-3.5 {{ ($activeTab ?? 'customer') === 'customer' ? '' : 'hidden' }}">
        {{-- INFO BAR --}}
        <div class="flex items-center justify-between px-1 text-xs">
            <span class="text-xs font-bold text-white tracking-wide">Daftar Konsumen Terdaftar</span>
            <button type="button" onclick="showToast('Ekspor Data', 'Mengunduh laporan pengguna format CSV/Excel...', 'info')" class="text-[10.5px] font-semibold text-sky-200 hover:text-white transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-export text-[9.5px] text-sky-300"></i>
                <span>Ekspor Data</span>
            </button>
        </div>

        {{-- CONTAINER KARTU CUSTOMER (PRE-RENDERED SERVER SIDE) --}}
        <div id="adminCustomerListContainer" class="space-y-3.5">
            @forelse($customers ?? [] as $index => $c)
                @php
                    $normStatus = 'aktif';
                    $rawStatus = strtolower($c->status ?? '');
                    if (str_contains($rawStatus, 'verif')) {
                        $normStatus = 'terverifikasi';
                    } elseif (str_contains($rawStatus, 'baru') || $rawStatus === 'new') {
                        $normStatus = 'baru';
                    }

                    $nama = $c->name ?: 'Pelanggan SayaBantu';
                    $idCustomer = '#USR-' . str_pad((string) $c->id, 4, '0', STR_PAD_LEFT);
                    $wa = $c->phone ?: '-';
                    $waClean = preg_replace('/[^0-9]/', '', $wa);
                    if (str_starts_with($waClean, '0')) {
                        $waClean = '62' . substr($waClean, 1);
                    }
                    $email = $c->email ?: '-';
                    $initial = strtoupper(substr($nama, 0, 1));
                    $orders = $c->custom_order_count ?? 1;
                    $address = $c->custom_address ?? 'DKI Jakarta & Sekitarnya';
                    $regDate = $c->custom_joined_date ?? ($c->created_at ? $c->created_at->format('d M Y') : 'Baru saja');
                @endphp

                <div
                    class="p-4 rounded-2xl bg-white border border-gray-100 shadow-sm space-y-3 pengguna-card transition-all cursor-pointer hover:border-sky-300 hover:shadow-md active:scale-[0.995]"
                    data-status="{{ $normStatus }}"
                    data-name="{{ strtolower($nama) }}"
                    data-phone="{{ strtolower($wa) }}"
                    data-email="{{ strtolower($email) }}"
                    data-index="{{ $index }}"
                    onclick="openCustomerDetailByIndex({{ $index }})"
                    title="Lihat detail konsumen {{ $nama }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl bg-sky-50 text-[#173B67] border border-sky-100 flex items-center justify-center font-extrabold text-base shrink-0 shadow-2xs">
                                {{ $initial }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-bold text-gray-800 tracking-tight truncate">{{ $nama }}</h4>
                                    <span class="text-[9px] font-mono px-1.5 py-0.2 bg-sky-50 text-sky-700 rounded border border-sky-200 font-semibold">{{ $idCustomer }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 font-medium mt-0.5 truncate flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-[9px]"></i>
                                    <span>{{ $address }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($normStatus === 'aktif')
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @elseif($normStatus === 'terverifikasi')
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Terverifikasi
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Baru
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">No. Handphone</span>
                            <a href="tel:{{ $waClean }}" class="font-bold text-sky-700 font-mono hover:underline flex items-center gap-1 mt-0.5 truncate">
                                <i class="fa-solid fa-phone text-sky-600 text-[10px]"></i>
                                <span>{{ $wa }}</span>
                            </a>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Email Terdaftar</span>
                            <span class="font-medium text-slate-700 truncate block mt-0.5">{{ $email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Pesanan Dibuat</span>
                            <span class="font-bold text-sky-800 font-mono mt-0.5 block">{{ $orders }} Pesanan</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Bergabung Sejak</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block">{{ $regDate }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-2 flex-wrap">
                        <a href="tel:{{ $waClean }}" onclick="event.stopPropagation()" class="py-2 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 active:scale-95 text-sky-700 font-bold text-[10px] flex items-center gap-1.5 border border-sky-200 transition-all shadow-2xs">
                            <i class="fa-solid fa-phone text-sky-600"></i>
                            <span>Hubungi Telepon</span>
                        </a>
                        <button type="button" onclick="event.stopPropagation(); openCustomerDetailByIndex({{ $index }})" class="py-2 px-3.5 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-95 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                            <i class="fa-solid fa-user text-[9px]"></i>
                            <span>Detail Konsumen</span>
                        </button>
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        {{-- EMPTY STATE PENGGUNA --}}
        <div id="emptyPengguna" class="p-8 text-center bg-white rounded-[22px] border border-gray-100 shadow-sm space-y-2 {{ count($customers ?? []) > 0 ? 'hidden' : '' }}">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 mx-auto flex items-center justify-center text-xl border border-sky-100">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <h4 class="text-xs font-bold text-gray-800">Belum Ada Data Konsumen</h4>
            <p class="text-[10px] text-gray-400">Belum ada akun konsumen yang terdaftar di platform.</p>
            <button type="button" onclick="clearSearchPengguna(); filterPengguna('all')" class="mt-2 text-[10px] font-bold text-[#173B67] bg-sky-50 hover:bg-sky-100 px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                Reset Pencarian
            </button>
        </div>
    </div>

    {{-- =====================================================
         PANEL KONTEN 2: DAFTAR PENYEDIA JASA / MITRA
    ===================================================== --}}
    <div id="mitraContentSection" class="space-y-3.5 {{ ($activeTab ?? 'customer') === 'mitra' ? '' : 'hidden' }}">
        {{-- CONTAINER KARTU MITRA (PRE-RENDERED SERVER SIDE) --}}
        <div id="adminMitraListContainer" class="space-y-3.5">
            @forelse($mitras ?? [] as $mIndex => $m)
                @php
                    $mStatus = strtolower($m->status ?? 'verifikasi');
                    $isAktif = in_array($mStatus, ['aktif', 'approved', 'disetujui', 'active']);
                    $isSuspend = in_array($mStatus, ['suspend', 'ditinjau', 'tolak', 'rejected', 'ditolak']);
                    $normMitraStatus = $isAktif ? 'aktif' : ($isSuspend ? 'suspend' : 'verifikasi');
                    $mitraIdCode = $m->mitra_id ?: 'SB-' . str_pad((string) $m->id, 4, '0', STR_PAD_LEFT);
                    $mWa = $m->phone ?: '-';
                    $mWaClean = preg_replace('/[^0-9]/', '', $mWa);
                    if (str_starts_with($mWaClean, '0')) {
                        $mWaClean = '62' . substr($mWaClean, 1);
                    }
                    $mInitial = strtoupper(substr($m->name, 0, 2));
                    $mArea = $m->area ?: ($m->kota ?: 'Jakarta');
                    $mService = $m->service ?: ($m->keahlian ?: 'Full Home Cleaning, Service & Cuci AC');
                    $mNik = $m->nik ?: '-';
                    $mJaminan = $m->jaminan ?: 'BPKB Sepeda Motor Asli';
                    $mDate = $m->created_at ? $m->created_at->format('d M Y') : 'Baru saja';
                @endphp

                <div
                    class="p-4 rounded-2xl bg-white border border-gray-100 shadow-sm space-y-3 mitra-card transition-all cursor-pointer hover:border-teal-300 hover:shadow-md active:scale-[0.995]"
                    data-status="{{ $normMitraStatus }}"
                    data-id="{{ $mitraIdCode }}"
                    data-db-id="{{ $m->id }}"
                    data-name="{{ strtolower($m->name) }}"
                    data-mitra-index="{{ $mIndex }}"
                    onclick="openMitraDetail('{{ $mitraIdCode }}')"
                    title="Lihat detail berkas mitra {{ $m->name }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($m->avatar)
                                <img src="{{ $m->avatar }}" alt="{{ $m->name }}" class="w-11 h-11 rounded-2xl object-cover border border-sky-100 shrink-0 shadow-2xs">
                            @else
                                <div class="w-11 h-11 rounded-2xl bg-[#0E1D31] text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-2xs">
                                    {{ $mInitial }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-bold text-gray-800 tracking-tight truncate">{{ $m->name }}</h4>
                                    <span class="text-[9px] font-mono px-1.5 py-0.2 {{ $isAktif ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }} rounded font-bold">{{ $mitraIdCode }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 font-medium mt-0.5 truncate flex items-center gap-1">
                                    <i class="fa-solid fa-briefcase text-sky-600 text-[9px]"></i>
                                    <span>{{ $mService }}</span>
                                </p>
                                <p class="text-[9.5px] text-gray-400 mt-0.5 truncate flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-[8.5px]"></i>
                                    <span>{{ $mArea }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($isAktif)
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif Terverifikasi
                                </span>
                            @elseif($isSuspend)
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditangguhkan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Verifikasi & ACC
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">No. Handphone</span>
                            <a href="tel:{{ $mWaClean }}" onclick="event.stopPropagation()" class="font-bold text-sky-700 font-mono hover:underline flex items-center gap-1 mt-0.5 truncate">
                                <i class="fa-solid fa-phone text-sky-600 text-[10px]"></i>
                                <span>{{ $mWa }}</span>
                            </a>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Nomor e-KTP (NIK)</span>
                            <span class="font-mono font-bold text-slate-800 tracking-wide mt-0.5 block truncate">{{ $mNik }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Dokumen Jaminan</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block truncate">{{ $mJaminan }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Tanggal Daftar</span>
                            <span class="font-medium text-slate-700 mt-0.5 block">{{ $mDate }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-1.5 flex-wrap">
                        @if($isAktif)
                            <div class="flex items-center gap-1.5">
                                <span class="py-1.5 px-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px] inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui (Approved)
                                </span>
                                <button type="button" onclick="event.stopPropagation(); suspendMitraById('{{ $mitraIdCode }}', {{ $m->id }})" class="py-1.5 px-2.5 rounded-xl bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-600 font-bold text-[10px] transition-colors cursor-pointer border border-gray-200">
                                    Suspend
                                </button>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); openMitraDetail('{{ $mitraIdCode }}')" class="py-2 px-3 rounded-xl bg-[#0E1D31] hover:bg-slate-800 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer ml-auto">
                                <i class="fa-solid fa-folder-open text-[9.5px]"></i>
                                <span>Detail Berkas</span>
                            </button>
                        @elseif($isSuspend)
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="event.stopPropagation(); setujuiMitraById('{{ $mitraIdCode }}', {{ $m->id }})" class="py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] flex items-center gap-1 cursor-pointer shadow-xs">
                                    <i class="fa-solid fa-rotate-left"></i> Pulihkan Mitra
                                </button>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); openMitraDetail('{{ $mitraIdCode }}')" class="py-2 px-3 rounded-xl bg-[#0E1D31] hover:bg-slate-800 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer ml-auto">
                                <i class="fa-solid fa-folder-open text-[9.5px]"></i>
                                <span>Detail Berkas</span>
                            </button>
                        @else
                            <button type="button" onclick="event.stopPropagation(); openMitraDetail('{{ $mitraIdCode }}')" class="py-2 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-[10px] flex items-center gap-1.5 transition-all cursor-pointer">
                                <i class="fa-solid fa-folder-open text-[9.5px] text-gray-600"></i>
                                <span>Detail Berkas</span>
                            </button>
                            <div class="flex items-center gap-1.5 ml-auto">
                                <button type="button" onclick="event.stopPropagation(); setujuiMitraById('{{ $mitraIdCode }}', {{ $m->id }})" class="py-2 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                    <span>Setujui Mitra</span>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); openModalTolakMitra('{{ $mitraIdCode }}', {{ $m->id }})" class="py-2 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold text-[10px] flex items-center gap-1 transition-all cursor-pointer">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                    <span>Tolak Pendaftaran</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        {{-- EMPTY STATE MITRA --}}
        <div id="adminMitraEmptyState" class="p-8 text-center bg-white rounded-[22px] border border-gray-100 shadow-sm space-y-2 {{ count($mitras ?? []) > 0 ? 'hidden' : '' }}">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 mx-auto flex items-center justify-center text-xl border border-teal-100">
                <i class="fa-solid fa-handshake-slash"></i>
            </div>
            <h4 class="text-xs font-bold text-gray-800">Belum Ada Data Mitra</h4>
            <p class="text-[10px] text-gray-400">Belum ada penyedia jasa atau calon mitra yang mendaftar di platform.</p>
        </div>
    </div>

</div>
@endsection

{{-- =========================================================
     MODAL DETAIL PENGGUNA & MITRA & TOLAK PENDAFTARAN
========================================================= --}}
@section('modals')

{{-- MODAL 1: DETAIL KONSUMEN / CUSTOMER --}}
<div id="customerDetailModal" class="fixed inset-0 z-[120] hidden items-end sm:items-center justify-center transition-all duration-300" role="dialog" aria-modal="true" aria-labelledby="customerModalTitle">
    <div id="customerDetailBackdrop" class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeCustomerDetailModal()"></div>

    <div id="customerDetailPanel" class="relative w-full sm:max-w-md bg-[#0E1D31] text-white rounded-t-[30px] sm:rounded-[28px] border border-white/20 shadow-2xl flex flex-col max-h-[88vh] z-10 transform translate-y-full transition-transform duration-300 ease-out overflow-hidden">
        <div class="pt-3 pb-1 flex justify-center sm:hidden shrink-0">
            <div class="w-12 h-1 rounded-full bg-white/25"></div>
        </div>

        <div class="px-5 py-3.5 border-b border-white/10 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div id="customerModalAvatarBox" class="w-12 h-12 rounded-2xl bg-sky-50 text-[#173B67] font-extrabold text-lg flex items-center justify-center border border-sky-100 shrink-0 shadow-inner">
                    D
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 id="customerModalName" class="text-sm font-bold text-white truncate">Dimas Pratama</h3>
                        <span id="customerModalStatusBadge" class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">
                            Aktif
                        </span>
                    </div>
                    <p id="customerModalCategoryArea" class="text-[10px] text-gray-300 truncate mt-0.5">Personal • Kebayoran Baru, Jakarta Selatan</p>
                    <span id="customerModalId" class="inline-block text-[9px] font-mono font-semibold text-sky-400 mt-0.5">#USR-1082</span>
                </div>
            </div>
            <button type="button" onclick="closeCustomerDetailModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-gray-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 text-xs custom-scrollbar">
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                    <span class="text-[8px] text-gray-400 block font-bold uppercase">Pesanan</span>
                    <div class="text-xs font-black text-white mt-0.5" id="customerModalOrderCount">14</div>
                    <span class="text-[7.5px] text-gray-400">Total order</span>
                </div>
                <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                    <span class="text-[8px] text-gray-400 block font-bold uppercase">Total Belanja</span>
                    <div class="text-xs font-black text-emerald-400 mt-0.5" id="customerModalTotalSpent">Rp 2.85jt</div>
                    <span class="text-[7.5px] text-gray-400">Akumulasi</span>
                </div>
                <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                    <span class="text-[8px] text-gray-400 block font-bold uppercase">Skor Akun</span>
                    <div class="text-xs font-black text-sky-400 mt-0.5" id="customerModalAccountScore">99%</div>
                    <span class="text-[7.5px] text-gray-400">Sangat Baik</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-white/[0.05] border border-white/10 space-y-2.5">
                <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider flex items-center gap-1.5 pb-1.5 border-b border-white/10">
                    <i class="fa-solid fa-id-card text-sky-400"></i>
                    Informasi Profil &amp; Keanggotaan
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-[10px]">
                    <div>
                        <span class="text-gray-400 text-[8.5px] block font-medium">TANGGAL BERGABUNG</span>
                        <span class="font-semibold text-white flex items-center gap-1 mt-0.5">
                            <i class="fa-regular fa-calendar-check text-teal-400 text-[9px]"></i>
                            <span id="customerModalJoinedDate">15 Januari 2026</span>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[8.5px] block font-medium">MASA KEANGGOTAAN</span>
                        <span class="font-semibold text-emerald-300 flex items-center gap-1 mt-0.5">
                            <i class="fa-solid fa-award text-emerald-400 text-[9px]"></i>
                            <span id="customerModalTenure">9 Bulan Aktif</span>
                        </span>
                    </div>
                </div>

                <div class="space-y-1.5 pt-1 text-[10.5px]">
                    <div class="flex items-center justify-between text-gray-300">
                        <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-sky-400"></i> No. Handphone:
                        </span>
                        <a id="customerModalPhoneLink" href="#" class="font-mono text-sky-400 font-bold hover:underline flex items-center gap-1">
                            <span id="customerModalPhone">+62 812-3344-5566</span>
                            <i class="fa-solid fa-phone text-[8px]"></i>
                        </a>
                    </div>
                    <div class="flex items-center justify-between text-gray-300">
                        <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-sky-400"></i> Email:
                        </span>
                        <span id="customerModalEmail" class="font-mono text-white text-[10px]">dimas.pratama@gmail.com</span>
                    </div>
                    <div class="flex items-start justify-between text-gray-300 gap-2">
                        <span class="text-gray-400 text-[10px] flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-location-dot text-rose-400"></i> Alamat Rumah:
                        </span>
                        <span id="customerModalAddress" class="text-right text-white text-[10px] leading-tight">Jl. Senopati No. 18, Kebayoran Baru, Jakarta Selatan</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-300">
                        <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                            <i class="fa-solid fa-wallet text-amber-400"></i> Pembayaran Favorit:
                        </span>
                        <span id="customerModalPaymentMethod" class="px-2 py-0.2 rounded bg-sky-500/20 text-sky-300 text-[9px] font-bold border border-sky-500/30">
                            QRIS &amp; SayaBantu Pay
                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-sky-400"></i>
                        Riwayat Aktivitas &amp; Pesanan Terakhir
                    </h4>
                    <span class="text-[9px] text-gray-400">Log Real-Time</span>
                </div>
                <div id="customerModalActivityList" class="space-y-2"></div>
            </div>
        </div>

        <div class="p-3.5 border-t border-white/10 shrink-0 bg-[#0E1D31] flex items-center gap-2">
            <a id="customerModalChatBtn" href="#" class="flex-1 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 active:scale-98 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-md">
                <i class="fa-solid fa-phone text-sm"></i>
                Hubungi Telepon
            </a>
            <a href="{{ route('admin.pesanan') }}" class="px-3 py-2.5 rounded-xl bg-sky-500/20 hover:bg-sky-500/30 text-sky-200 border border-sky-400/30 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                <i class="fa-solid fa-list-check text-[11px]"></i>
                Daftar Order
            </a>
            <button type="button" onclick="closeCustomerDetailModal()" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 active:scale-98 text-gray-300 hover:text-white font-semibold text-xs transition-all cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- MODAL 2: DETAIL MITRA (BOTTOM SHEET) --}}
<div id="mitraDetailModal" class="sb-overlay-backdrop fixed inset-0 z-[200] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-mitra-click absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closeMitraDetailModal()"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="mitraDetailPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[92vh] border-t border-white/15 translate-y-full transition-transform duration-200 overflow-hidden">
            <div class="pt-3 pb-1 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto"></div>
            </div>

            <div class="px-5 pt-2 pb-3.5 border-b border-white/10 shrink-0 bg-[#0E1D31] flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="mitraModalAvatarBox" class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg border shrink-0 shadow-md bg-sky-50 text-[#173B67] border-sky-100">
                        B
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <h3 class="text-sm font-bold text-white tracking-tight truncate" id="mitraModalName">Budi Santoso</h3>
                            <span id="mitraModalStatusBadge" class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Menunggu Verifikasi
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-300 font-medium mt-0.5 truncate" id="mitraModalServiceArea">Full Home Cleaning • Jakarta Selatan</p>
                        <span class="text-[9px] text-teal-300 font-mono" id="mitraModalId">-</span>
                    </div>
                </div>
                <button type="button" onclick="closeMitraDetailModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-gray-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer" aria-label="Tutup">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 text-xs custom-scrollbar">
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                        <span class="text-[8px] text-gray-400 block font-bold uppercase">Rating</span>
                        <div class="text-xs font-black text-amber-300 mt-0.5 flex items-center justify-center gap-0.5">
                            <i class="fa-solid fa-star text-yellow-400 text-[9px]"></i>
                            <span id="mitraModalRating">4.9</span>
                        </div>
                        <span class="text-[7.5px] text-gray-400" id="mitraModalOrderCount">128 order</span>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                        <span class="text-[8px] text-gray-400 block font-bold uppercase">Penghasilan</span>
                        <div class="text-xs font-black text-emerald-400 mt-0.5" id="mitraModalTotalEarning">Rp 19.2jt</div>
                        <span class="text-[7.5px] text-gray-400">Total akumulasi</span>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                        <span class="text-[8px] text-gray-400 block font-bold uppercase">Penyelesaian</span>
                        <div class="text-xs font-black text-teal-300 mt-0.5" id="mitraModalCompletionRate">99%</div>
                        <span class="text-[7.5px] text-gray-400">Tepat Waktu</span>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/[0.06] border border-white/10">
                        <span class="text-[8px] text-gray-400 block font-bold uppercase">Bergabung</span>
                        <div class="text-xs font-black text-sky-300 mt-0.5" id="mitraModalTenure">Aktif</div>
                        <span class="text-[7.5px] text-gray-400" id="mitraModalJoinedDate">27 Okt 2026</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/[0.05] border border-white/10 space-y-2.5">
                    <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider flex items-center gap-1.5 pb-1.5 border-b border-white/10">
                        <i class="fa-solid fa-address-card text-teal-400"></i>
                        Biodata &amp; Dokumen Identitas
                    </div>
                    <div class="space-y-2 pt-1 text-[10.5px]">
                        <div class="flex items-center justify-between text-gray-300">
                            <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                                <i class="fa-solid fa-id-badge text-sky-400"></i> NIK e-KTP:
                            </span>
                            <span id="mitraModalNik" class="font-mono font-bold text-white text-[11px]">3174091507920003</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-300">
                            <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-sky-400"></i> No. Handphone:
                            </span>
                            <span id="mitraModalPhone" class="font-mono text-sky-400 font-bold">081234567890</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-300">
                            <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                                <i class="fa-regular fa-envelope text-sky-400"></i> Email:
                            </span>
                            <span id="mitraModalEmail" class="font-mono text-white text-[10px]">budi.santoso@gmail.com</span>
                        </div>
                        <div class="flex items-start justify-between text-gray-300 gap-2">
                            <span class="text-gray-400 text-[10px] flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid fa-location-dot text-rose-400"></i> Alamat:
                            </span>
                            <span id="mitraModalAddress" class="text-right text-white text-[10px] leading-tight">Jl. Kemang Raya No. 14, Jakarta Selatan</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-300">
                            <span class="text-gray-400 text-[10px] flex items-center gap-1.5">
                                <i class="fa-solid fa-certificate text-amber-400"></i> Dokumen Jaminan:
                            </span>
                            <span id="mitraModalDocumentBadge" class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[9px] font-bold border border-emerald-500/30">
                                BPKB Sepeda Motor Asli Valid
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-sky-400"></i>
                            Riwayat Aktivitas &amp; Pekerjaan Terakhir
                        </h4>
                        <span class="text-[9px] text-gray-400">Log Real-Time</span>
                    </div>
                    <div id="mitraModalActivityList" class="space-y-2"></div>
                </div>
            </div>

            <div class="p-3.5 border-t border-white/10 shrink-0 bg-[#0E1D31] flex items-center gap-2">
                <div id="mitraModalActionBtnContainer" class="flex-1"></div>
                <a id="mitraModalChatBtn" href="#" class="px-3.5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 active:scale-98 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-md shrink-0">
                    <i class="fa-solid fa-phone text-sm"></i>
                    Telepon
                </a>
                <button type="button" onclick="closeMitraDetailModal()" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 active:scale-98 text-gray-300 hover:text-white font-semibold text-xs transition-all cursor-pointer shrink-0">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: TOLAK PENDAFTARAN MITRA --}}
<div id="modalTolakMitraAdmin" class="sb-overlay-backdrop fixed inset-0 z-[220] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-xs" onclick="closeModalTolakMitra()"></div>
    <div class="relative mx-auto w-full max-w-[420px] h-full pointer-events-none flex items-center justify-center p-4">
        <div class="relative w-full bg-[#0E1D31] border border-rose-500/30 text-white pointer-events-auto rounded-[24px] shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm border border-rose-500/30">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-white">Tolak Pendaftaran Mitra</h3>
                        <p class="text-[9.5px] text-gray-400" id="tolakMitraSubtitle">Pilih alasan penolakan berkas calon mitra</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalTolakMitra()" class="w-7 h-7 rounded-lg bg-white/10 text-gray-400 hover:text-white flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form onsubmit="handleTolakMitraSubmit(event)" class="space-y-3.5 text-xs">
                <input type="hidden" id="tolakMitraTargetId" value="">
                <input type="hidden" id="tolakMitraTargetDbId" value="">

                <div>
                    <label class="block text-[10.5px] font-bold text-gray-300 mb-1">
                        Pilih Alasan Penolakan <span class="text-rose-400">*</span>
                    </label>
                    <select id="tolakMitraReasonSelect" onchange="handleSelectAlasanTolak(this.value)" class="w-full px-3 py-2.5 bg-white/10 border border-white/15 rounded-xl text-white text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none cursor-pointer">
                        <option value="Foto KTP buram / tidak terbaca jelas" class="bg-[#0E1D31] text-white">Foto KTP buram / tidak terbaca jelas</option>
                        <option value="Kualifikasi keahlian belum memenuhi syarat" class="bg-[#0E1D31] text-white">Kualifikasi keahlian belum memenuhi syarat</option>
                        <option value="Pelanggaran Kode Etik Kemitraan" class="bg-[#0E1D31] text-white">Pelanggaran Kode Etik Kemitraan</option>
                        <option value="Dokumen jaminan fisik tidak sesuai / tidak valid" class="bg-[#0E1D31] text-white">Dokumen jaminan fisik tidak sesuai / tidak valid</option>
                        <option value="custom" class="bg-[#0E1D31] text-white">Alasan Lainnya (Tulis alasan kustom)...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10.5px] font-bold text-gray-300 mb-1">
                        Detail Alasan (Ditampilkan pada Layar Mitra) <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="tolakMitraReasonText" rows="3" required class="w-full px-3 py-2 bg-white/10 border border-white/15 rounded-xl text-white text-xs placeholder:text-gray-500 focus:ring-2 focus:ring-rose-500 focus:outline-none resize-none leading-relaxed" placeholder="Tuliskan alasan penolakan pendaftaran...">Foto KTP buram / tidak terbaca jelas. Mohon unggah ulang foto e-KTP asli dengan pencahayaan terang dan seluruh teks terbaca.</textarea>
                </div>

                <div class="p-3 rounded-xl bg-white/[0.05] border border-white/10">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" id="tolakMitraCanReapply" checked class="mt-0.5 rounded text-rose-500 focus:ring-rose-400">
                        <div class="text-[10px] leading-tight">
                            <span class="font-bold text-white block">Izinkan kirim ulang data perbaikan (Re-upload)</span>
                            <span class="text-gray-400 mt-0.5 block">Mitra dapat mengunggah kembali foto identitas atau data perbaikan melalui portal tanpa mendaftar ulang dari awal.</span>
                        </div>
                    </label>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-white/10">
                    <button type="button" onclick="closeModalTolakMitra()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 font-bold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitTolakMitra" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:scale-95 text-white font-extrabold text-xs shadow-md flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-xs"></i>
                        <span>Konfirmasi Tolak Pendaftaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // State Pengguna & Mitra
    window.activePenggunaTab = '{{ $activeTab ?? "customer" }}';
    window.currentPenggunaFilter = 'all';
    window.currentAdminMitraFilter = 'all';
    window.searchKeyword = '';

    // =========================================================
    //  1. TAB SWITCHER CUSTOMER & MITRA (INSTAN TANPA RELOAD)
    // =========================================================
    window.switchPenggunaTab = function(tab) {
        window.activePenggunaTab = tab;

        const btnCust = document.getElementById('tabBtnCustomer');
        const btnMitra = document.getElementById('tabBtnMitra');
        const heroCust = document.getElementById('customerHeroSection');
        const heroMitra = document.getElementById('mitraHeroSection');
        const contentCust = document.getElementById('customerContentSection');
        const contentMitra = document.getElementById('mitraContentSection');

        if (tab === 'mitra') {
            if (btnCust) {
                btnCust.className = 'py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 font-medium border border-white/10 text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none';
            }
            if (btnMitra) {
                btnMitra.className = 'py-2 px-3 rounded-xl bg-[#173B67] text-white font-bold border border-sky-400/50 shadow-sm text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none';
            }
            if (heroCust) heroCust.classList.add('hidden');
            if (heroMitra) heroMitra.classList.remove('hidden');
            if (contentCust) contentCust.classList.add('hidden');
            if (contentMitra) contentMitra.classList.remove('hidden');

            try {
                history.replaceState(null, '', '{{ route("admin.mitra") }}');
            } catch(e) {}
        } else {
            if (btnCust) {
                btnCust.className = 'py-2 px-3 rounded-xl bg-[#173B67] text-white font-bold border border-sky-400/50 shadow-sm text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none';
            }
            if (btnMitra) {
                btnMitra.className = 'py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 font-medium border border-white/10 text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none';
            }
            if (heroCust) heroCust.classList.remove('hidden');
            if (heroMitra) heroMitra.classList.add('hidden');
            if (contentCust) contentCust.classList.remove('hidden');
            if (contentMitra) contentMitra.classList.add('hidden');

            try {
                history.replaceState(null, '', '{{ route("admin.pengguna") }}');
            } catch(e) {}
        }
    };

    // Sinkronkan tampilan hero/konten sesuai tab aktif saat halaman dimuat / di-refresh.
    document.addEventListener('DOMContentLoaded', function() {
        if (window.activePenggunaTab === 'mitra') {
            window.switchPenggunaTab('mitra');
        }
    });

    // =========================================================
    //  2. FILTER & PENCARIAN CUSTOMER
    // =========================================================
    window.filterPengguna = function(status) {
        window.currentPenggunaFilter = status;

        document.querySelectorAll('.pengguna-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'border-2', 'bg-white/[0.18]',
                'bg-emerald-500/20', 'bg-sky-500/20', 'bg-amber-500/20',
                'border-sky-400', 'border-emerald-400', 'border-amber-400',
                'shadow-sky-500/25', 'shadow-emerald-500/25', 'shadow-amber-500/25'
            );
            card.classList.add('bg-white/[0.10]', 'border', 'border-white/20', 'opacity-75');

            if (f === status) {
                card.classList.remove('bg-white/[0.10]', 'border-white/20', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-md', 'border-2');
                if (status === 'all') {
                    card.classList.add('bg-white/[0.18]', 'border-sky-400', 'shadow-sky-500/25');
                } else if (status === 'aktif') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25');
                } else if (status === 'terverifikasi') {
                    card.classList.add('bg-sky-500/20', 'border-sky-400', 'shadow-sky-500/25');
                } else if (status === 'baru') {
                    card.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25');
                }
            }
        });

        document.querySelectorAll('.pengguna-filter-tab').forEach(b => {
            const statAttr = b.getAttribute('data-status') || '';
            const isActive = (statAttr === status);
            if (isActive) {
                b.classList.add('font-bold', 'bg-white', 'text-[#173B67]', 'shadow-sm', 'border-white');
                b.classList.remove('font-medium', 'bg-white/10', 'text-gray-300', 'border-white/10');
            } else {
                b.classList.remove('font-bold', 'bg-white', 'text-[#173B67]', 'shadow-sm', 'border-white');
                b.classList.add('font-medium', 'bg-white/10', 'text-gray-300', 'border', 'border-white/10');
            }
        });

        applyCustomerFilterRules();
    };

    window.searchPengguna = function(val) {
        window.searchKeyword = (val || '').toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn) {
            if (window.searchKeyword.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        applyCustomerFilterRules();
    };

    window.clearSearchPengguna = function() {
        const input = document.getElementById('searchPenggunaInput');
        if (input) input.value = '';
        window.searchPengguna('');
    };

    function applyCustomerFilterRules() {
        const cards = document.querySelectorAll('.pengguna-card');
        const empty = document.getElementById('emptyPengguna');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status') || 'aktif';
            const cardName = card.getAttribute('data-name') || '';
            const cardPhone = card.getAttribute('data-phone') || '';
            const cardEmail = card.getAttribute('data-email') || '';

            const matchesStatus = (window.currentPenggunaFilter === 'all' || cardStatus === window.currentPenggunaFilter);
            const matchesSearch = !window.searchKeyword || 
                                  cardName.includes(window.searchKeyword) || 
                                  cardPhone.includes(window.searchKeyword) || 
                                  cardEmail.includes(window.searchKeyword);

            if (matchesStatus && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            if (empty) empty.classList.remove('hidden');
        } else {
            if (empty) empty.classList.add('hidden');
        }
    }

    // =========================================================
    //  3. FILTER MITRA
    // =========================================================
    window.filterMitra = function(status) {
        window.currentAdminMitraFilter = status;

        document.querySelectorAll('.mitra-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'border-2', 'bg-white/[0.18]',
                'bg-emerald-500/20', 'bg-amber-500/20', 'bg-rose-500/20',
                'border-teal-400', 'border-emerald-400', 'border-amber-400', 'border-rose-400',
                'shadow-teal-500/25', 'shadow-emerald-500/25', 'shadow-amber-500/25', 'shadow-rose-500/25'
            );
            card.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (f === status) {
                card.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-md', 'border-2');
                if (status === 'all') {
                    card.classList.add('bg-white/[0.18]', 'border-teal-400', 'shadow-teal-500/25');
                } else if (status === 'aktif') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25');
                } else if (status === 'verifikasi') {
                    card.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25');
                } else if (status === 'suspend') {
                    card.classList.add('bg-rose-500/20', 'border-rose-400', 'shadow-rose-500/25');
                }
            }
        });

        const cards = document.querySelectorAll('.mitra-card');
        const empty = document.getElementById('adminMitraEmptyState');
        let count = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status') || 'verifikasi';
            if (status === 'all' || cardStatus === status) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        if (count === 0) {
            if (empty) empty.classList.remove('hidden');
        } else {
            if (empty) empty.classList.add('hidden');
        }
    };

    // Sinkronisasi data mitra realtime agar pendaftar baru langsung muncul di tabel admin
    window.syncAdminMitraData = function() {
        fetch('/admin/api/mitras', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success || !Array.isArray(data.mitras)) return;
            const mitras = data.mitras;

            let totalCount = mitras.length;
            let aktifCount = 0;
            let verifCount = 0;
            let suspendCount = 0;

            mitras.forEach(m => {
                const st = (m.status || 'verifikasi').toLowerCase();
                const isAktif = ['aktif', 'approved', 'disetujui', 'active'].includes(st);
                const isSuspend = ['suspend', 'ditinjau', 'tolak', 'rejected', 'ditolak'].includes(st);
                if (isAktif) aktifCount++;
                else if (isSuspend) suspendCount++;
                else verifCount++;

                // Daftarkan ke mitraDataStore jika belum tercatat
                if (!mitraDataStore[m.id]) {
                    mitraDataStore[m.id] = {
                        id: m.id,
                        db_id: m.db_id,
                        nama: m.nama,
                        name: m.nama,
                        nik: m.nik || '-',
                        whatsapp: m.whatsapp || m.phone || '-',
                        phone: m.phone || '-',
                        email: m.email || '-',
                        alamat: m.alamat || m.area || m.kota || '-',
                        area: m.area || m.kota || 'Jakarta',
                        service: m.service || m.keahlian || 'Full Home Cleaning & Servis AC',
                        jaminan: m.jaminan || 'BPKB Sepeda Motor Asli',
                        avatar: m.avatar || m.image,
                        rating: m.rating ?? 'Belum ada ulasan',
                        tasks: m.tasks ?? 0,
                        totalEarning: 'Rp 0',
                        status: isAktif ? 'aktif' : (isSuspend ? 'suspend' : 'verifikasi'),
                        statusLabel: isAktif ? 'Aktif Terverifikasi' : 'Menunggu Verifikasi & ACC',
                        activities: [
                            { title: "Pendaftaran Akun Mitra", desc: "Berkas identitas e-KTP dan " + (m.jaminan || 'dokumen jaminan') + " telah diserahkan.", time: "Baru saja", badge: "Pendaftaran", badgeClass: "bg-amber-500/20 text-amber-300 border border-amber-500/30", icon: "fa-id-card", iconBox: "bg-amber-500/20 text-amber-400" }
                        ]
                    };

                    const container = document.getElementById('adminMitraListContainer');
                    if (container) {
                        const cardDiv = document.createElement('div');
                        const normStatus = isAktif ? 'aktif' : (isSuspend ? 'suspend' : 'verifikasi');
                        cardDiv.className = 'p-4 rounded-2xl bg-white border border-gray-100 shadow-sm space-y-3 mitra-card transition-all cursor-pointer hover:border-teal-300 hover:shadow-md active:scale-[0.995]';
                        cardDiv.setAttribute('onclick', `openMitraDetail('${m.id}')`);
                        cardDiv.setAttribute('title', 'Lihat detail berkas mitra ' + (m.nama || ''));
                        cardDiv.setAttribute('data-status', normStatus);
                        cardDiv.setAttribute('data-id', m.id);
                        cardDiv.setAttribute('data-db-id', m.db_id);
                        cardDiv.setAttribute('data-name', (m.nama || '').toLowerCase());
                        
                        let waClean = (m.whatsapp || m.phone || '').replace(/[^0-9]/g, '');
                        if (waClean.startsWith('0')) waClean = '62' + waClean.substring(1);
                        
                        const avatarHtml = m.avatar 
                            ? `<img src="${m.avatar}" alt="${m.nama}" class="w-11 h-11 rounded-2xl object-cover border border-sky-100 shrink-0 shadow-2xs">`
                            : `<div class="w-11 h-11 rounded-2xl bg-[#0E1D31] text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-2xs">${(m.nama || 'M').substring(0, 2).toUpperCase()}</div>`;

                        cardDiv.innerHTML = `
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    ${avatarHtml}
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <h4 class="text-xs font-bold text-gray-800 tracking-tight truncate">${m.nama}</h4>
                                            <span class="text-[9px] font-mono px-1.5 py-0.2 bg-sky-50 text-sky-700 border border-sky-200 rounded font-bold">${m.id}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-500 font-medium mt-0.5 truncate flex items-center gap-1">
                                            <i class="fa-solid fa-briefcase text-sky-600 text-[9px]"></i>
                                            <span>${m.service || 'Full Home Cleaning, Service & Cuci AC'}</span>
                                        </p>
                                        <p class="text-[9.5px] text-gray-400 mt-0.5 truncate flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-rose-400 text-[8.5px]"></i>
                                            <span>${m.area || m.kota || 'Jakarta'}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Verifikasi & ACC
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <div>
                                    <span class="text-gray-400 text-[8.5px] block font-bold uppercase">No. Handphone</span>
                                    <a href="tel:${waClean}" onclick="event.stopPropagation()" class="font-bold text-sky-700 font-mono hover:underline flex items-center gap-1 mt-0.5 truncate">
                                        <i class="fa-solid fa-phone text-sky-600 text-[10px]"></i>
                                        <span>${m.whatsapp || m.phone}</span>
                                    </a>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Nomor e-KTP (NIK)</span>
                                    <span class="font-mono font-bold text-slate-800 tracking-wide mt-0.5 block truncate">${m.nik || '-'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Dokumen Jaminan</span>
                                    <span class="font-semibold text-slate-700 mt-0.5 block truncate">${m.jaminan || 'BPKB Sepeda Motor Asli'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Tanggal Daftar</span>
                                    <span class="font-medium text-slate-700 mt-0.5 block">Baru saja</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-1.5 flex-wrap">
                                <button type="button" onclick="event.stopPropagation(); openMitraDetail('${m.id}')" class="py-2 px-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-[10px] flex items-center gap-1.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-folder-open text-[9.5px] text-gray-600"></i>
                                    <span>Detail Berkas</span>
                                </button>
                                <div class="flex items-center gap-1.5 ml-auto">
                                    <button type="button" onclick="event.stopPropagation(); setujuiMitraById('${m.id}', ${m.db_id})" class="py-2 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Setujui Mitra</span>
                                    </button>
                                    <button type="button" onclick="event.stopPropagation(); openModalTolakMitra('${m.id}', ${m.db_id})" class="py-2 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold text-[10px] flex items-center gap-1 transition-all cursor-pointer">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                        <span>Tolak Pendaftaran</span>
                                    </button>
                                </div>
                            </div>
                        `;
                        container.insertBefore(cardDiv, container.firstChild);
                    }
                }
            });

            // Update counter elements
            const statTotalEl = document.getElementById('statMitraTotal');
            if (statTotalEl) statTotalEl.textContent = totalCount;
            const heroBadgeEl = document.getElementById('heroMitraBadge');
            if (heroBadgeEl) heroBadgeEl.textContent = totalCount + ' Mitra';
            const navMitraCountEl = document.getElementById('navMitraCount');
            if (navMitraCountEl) navMitraCountEl.textContent = totalCount;
            const mitraCountEl = document.getElementById('mitraCount');
            if (mitraCountEl) mitraCountEl.textContent = totalCount;

            const statAktifEl = document.getElementById('statMitraAktif');
            if (statAktifEl) statAktifEl.textContent = aktifCount;
            const statVerifEl = document.getElementById('statMitraVerifikasi');
            if (statVerifEl) statVerifEl.textContent = verifCount;
            const statSuspendEl = document.getElementById('statMitraSuspend');
            if (statSuspendEl) statSuspendEl.textContent = suspendCount;
        })
        .catch(() => {});
    };

    // Sinkronisasi data customer realtime agar pendaftar baru langsung muncul di tabel admin
    window.syncAdminCustomerData = function() {
        fetch('/admin/api/customers', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success || !Array.isArray(data.customers)) return;
            const customers = data.customers;

            const tabCount = document.getElementById('tabCustomerCount');
            if (tabCount) tabCount.textContent = data.counts?.total ?? customers.length;
            const heroBadge = document.getElementById('heroCustomerBadge');
            if (heroBadge) heroBadge.textContent = (data.counts?.total ?? customers.length) + ' Terdaftar';
            const statTotal = document.getElementById('statCustTotal');
            if (statTotal) statTotal.textContent = data.counts?.total ?? customers.length;
            const filterSemua = document.getElementById('filterCountSemua');
            if (filterSemua) filterSemua.textContent = data.counts?.total ?? customers.length;

            const statAktif = document.getElementById('statCustAktif');
            if (statAktif && data.counts?.aktif !== undefined) statAktif.textContent = data.counts.aktif;
            const filterAktif = document.getElementById('filterCountAktif');
            if (filterAktif && data.counts?.aktif !== undefined) filterAktif.textContent = data.counts.aktif;

            const statVerif = document.getElementById('statCustTerverifikasi');
            if (statVerif && data.counts?.terverifikasi !== undefined) statVerif.textContent = data.counts.terverifikasi;

            const statBaru = document.getElementById('statCustBaru');
            if (statBaru && data.counts?.baru !== undefined) statBaru.textContent = data.counts.baru;

            const container = document.getElementById('adminCustomerListContainer');
            if (!container) return;

            customers.forEach((c, idx) => {
                const existing = container.querySelector(`[data-phone="${(c.phone || '').toLowerCase()}"]`);
                if (!existing) {
                    const normStatus = (c.status || '').toLowerCase().includes('verif') ? 'terverifikasi' : 
                                      ((c.status || '').toLowerCase().includes('baru') ? 'baru' : 'aktif');
                    const initial = (c.name || 'C').charAt(0).toUpperCase();
                    let waClean = (c.phone || '').replace(/[^0-9]/g, '');
                    if (waClean.startsWith('0')) waClean = '62' + waClean.substring(1);

                    customerDbStore[idx] = {
                        name: c.name,
                        id: c.id,
                        phone: c.phone,
                        email: c.email,
                        address: c.address || 'DKI Jakarta & Sekitarnya',
                        joinedDate: c.joinedDate || 'Baru saja',
                        orderCount: String(c.orderCount || 0),
                        totalSpent: c.totalSpent || 'Rp 0',
                        orders: []
                    };

                    const card = document.createElement('div');
                    card.className = 'p-4 rounded-2xl bg-white border border-gray-100 shadow-sm space-y-3 pengguna-card transition-all cursor-pointer hover:border-sky-300 hover:shadow-md active:scale-[0.995]';
                    card.setAttribute('data-status', normStatus);
                    card.setAttribute('data-name', (c.name || '').toLowerCase());
                    card.setAttribute('data-phone', (c.phone || '').toLowerCase());
                    card.setAttribute('data-email', (c.email || '').toLowerCase());
                    card.setAttribute('data-index', idx);
                    card.setAttribute('onclick', `openCustomerDetailByIndex(${idx})`);
                    card.setAttribute('title', 'Lihat detail konsumen ' + (c.name || ''));

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-[#173B67] border border-sky-100 flex items-center justify-center font-extrabold text-base shrink-0 shadow-2xs">
                                    ${initial}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="text-xs font-bold text-gray-800 tracking-tight truncate">${c.name}</h4>
                                        <span class="text-[9px] font-mono px-1.5 py-0.2 bg-sky-50 text-sky-700 rounded border border-sky-200 font-semibold">${c.id}</span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 font-medium mt-0.5 truncate flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-rose-400 text-[9px]"></i>
                                        <span>${c.address || 'DKI Jakarta & Sekitarnya'}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold ${normStatus === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'} inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full ${normStatus === 'aktif' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'}"></span> ${normStatus === 'aktif' ? 'Aktif' : 'Baru'}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-gray-400 text-[8.5px] block font-bold uppercase">No. Handphone</span>
                                <a href="tel:${waClean}" onclick="event.stopPropagation()" class="font-bold text-sky-700 font-mono hover:underline flex items-center gap-1 mt-0.5 truncate">
                                    <i class="fa-solid fa-phone text-sky-600 text-[10px]"></i>
                                    <span>${c.phone}</span>
                                </a>
                            </div>
                            <div>
                                <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Email Terdaftar</span>
                                <span class="font-medium text-slate-700 truncate block mt-0.5">${c.email}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Pesanan Dibuat</span>
                                <span class="font-bold text-sky-800 font-mono mt-0.5 block">${c.orderCount || 0} Pesanan</span>
                            </div>
                            <div>
                                <span class="text-gray-400 text-[8.5px] block font-bold uppercase">Bergabung Sejak</span>
                                <span class="font-semibold text-slate-700 mt-0.5 block">${c.joinedDate || 'Baru saja'}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-2 flex-wrap">
                            <a href="tel:${waClean}" onclick="event.stopPropagation()" class="py-2 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 active:scale-95 text-sky-700 font-bold text-[10px] flex items-center gap-1.5 border border-sky-200 transition-all shadow-2xs">
                                <i class="fa-solid fa-phone text-sky-600"></i>
                                <span>Hubungi Telepon</span>
                            </a>
                            <button type="button" onclick="event.stopPropagation(); openCustomerDetailByIndex(${idx})" class="py-2 px-3.5 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-[0.99] text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                                <i class="fa-solid fa-user text-[9px]"></i>
                                <span>Detail Konsumen</span>
                            </button>
                        </div>
                    `;
                    container.prepend(card);
                }
            });

            const emptyEl = document.getElementById('emptyPengguna');
            if (emptyEl && customers.length > 0) {
                emptyEl.classList.add('hidden');
            }
        })
        .catch(() => {});
    };

    // Auto-sync setiap 8 detik dan ketika tab browser aktif kembali
    setInterval(window.syncAdminMitraData, 8000);
    setInterval(window.syncAdminCustomerData, 8000);
    window.addEventListener('focus', function() {
        window.syncAdminMitraData();
        window.syncAdminCustomerData();
    });

    // =========================================================
    //  4. MODAL DETAIL CUSTOMER
    // =========================================================
    // Data customer asli dari database (dibangun server-side) agar detail selalu akurat
    const customerDbStore = {
        @foreach($customers ?? [] as $index => $c)
        {{ $index }}: {
            name: {!! json_encode($c->name ?: 'Pelanggan SayaBantu') !!},
            initial: {!! json_encode(strtoupper(substr($c->name ?: 'P', 0, 1))) !!},
            id: {!! json_encode('#USR-' . str_pad((string) $c->id, 4, '0', STR_PAD_LEFT)) !!},
            phone: {!! json_encode($c->phone ?: '-') !!},
            email: {!! json_encode($c->email ?: '-') !!},
            address: {!! json_encode($c->custom_address ?? 'DKI Jakarta & Sekitarnya') !!},
            categoryArea: {!! json_encode($c->custom_category_area ?? 'Personal') !!},
            paymentMethod: {!! json_encode($c->custom_payment_method ?? 'QRIS & Transfer Bank') !!},
            accountScore: {!! json_encode($c->custom_account_score ?? '100%') !!},
            status: {!! json_encode($c->status ?: 'aktif') !!},
            joinedDate: {!! json_encode(($c->created_at ? $c->created_at->format('d F Y') : 'Baru saja')) !!},
            tenure: {!! json_encode($c->custom_tenure ?? 'Aktif') !!},
            orderCount: {!! json_encode((string) ($c->custom_order_count ?? 0)) !!},
            totalSpent: {!! json_encode($c->custom_total_spent ?? 'Rp 0') !!},
            orders: {!! json_encode(($c->custom_orders ?? collect())->map(fn ($o) => ['code' => $o->code, 'layanan' => $o->layanan, 'status' => $o->status, 'biaya' => $o->biaya, 'mitra' => $o->mitra_name ?: 'Mencari Mitra', 'tanggal' => $o->created_at?->format('d M Y')])->values()) !!}
        },
        @endforeach
    };

    window.openCustomerDetailByIndex = function(idx) {
        const c = customerDbStore[idx];
        if (!c) return;

        const avatarBox = document.getElementById('customerModalAvatarBox');
        if (avatarBox) avatarBox.textContent = c.initial || c.name.charAt(0);

        const nameEl = document.getElementById('customerModalName');
        if (nameEl) nameEl.textContent = c.name;

        const badgeEl = document.getElementById('customerModalStatusBadge');
        if (badgeEl) {
            if (c.status === 'terverifikasi') {
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 bg-sky-500/20 text-sky-300 border border-sky-500/30';
                badgeEl.textContent = 'Terverifikasi';
            } else if (c.status === 'baru') {
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 bg-amber-500/20 text-amber-300 border border-amber-500/30';
                badgeEl.textContent = 'Pelanggan Baru';
            } else {
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                badgeEl.textContent = 'Aktif';
            }
        }

        const catEl = document.getElementById('customerModalCategoryArea');
        if (catEl) catEl.textContent = c.categoryArea;

        const idEl = document.getElementById('customerModalId');
        if (idEl) idEl.textContent = c.id;

        const ordEl = document.getElementById('customerModalOrderCount');
        if (ordEl) ordEl.textContent = c.orderCount;

        const spEl = document.getElementById('customerModalTotalSpent');
        if (spEl) spEl.textContent = c.totalSpent;

        const scEl = document.getElementById('customerModalAccountScore');
        if (scEl) scEl.textContent = c.accountScore;

        const jnEl = document.getElementById('customerModalJoinedDate');
        if (jnEl) jnEl.textContent = c.joinedDate;

        const tnEl = document.getElementById('customerModalTenure');
        if (tnEl) tnEl.textContent = c.tenure;

        const phEl = document.getElementById('customerModalPhone');
        if (phEl) phEl.textContent = c.phone;

        const phClean = String(c.phone).replace(/[^0-9]/g, '');
        const phLink = document.getElementById('customerModalPhoneLink');
        if (phLink) phLink.href = 'tel:' + phClean;

        const emEl = document.getElementById('customerModalEmail');
        if (emEl) emEl.textContent = c.email;

        const adEl = document.getElementById('customerModalAddress');
        if (adEl) adEl.textContent = c.address;

        const pmEl = document.getElementById('customerModalPaymentMethod');
        if (pmEl) pmEl.textContent = c.paymentMethod;

        const chatBtn = document.getElementById('customerModalChatBtn');
        if (chatBtn) chatBtn.href = 'tel:' + phClean;

        const listEl = document.getElementById('customerModalActivityList');
        if (listEl) {
            listEl.innerHTML = '';

            // Riwayat pesanan nyata lebih dulu, lalu aktivitas akun
            const orderActivities = (db.orders || []).slice(0, 5).map((o) => ({
                title: 'Pesanan ' + o.code + ': ' + (o.layanan || 'Layanan SayaBantu'),
                desc: 'Mitra: ' + (o.mitra || '-') + ' • Biaya: Rp ' + (o.biaya || '0') + ' • Status: ' + (o.status || '-'),
                time: o.tanggal || '-',
                badge: 'Pesanan',
                badgeClass: 'bg-sky-500/20 text-sky-300 border border-sky-500/30',
                icon: 'fa-clipboard-list',
                iconBox: 'bg-sky-500/20 text-sky-400'
            }));

            const activities = orderActivities.concat(c.activities || []);

            if (activities.length === 0) {
                listEl.innerHTML = '<div class="p-3 rounded-xl bg-white/[0.04] border border-white/10 text-[10px] text-gray-400 text-center">Belum ada riwayat aktivitas atau pesanan untuk pengguna ini.</div>';
            }

            activities.forEach(act => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl bg-white/[0.04] border border-white/10 flex items-start gap-2.5 hover:bg-white/[0.08] transition-colors';
                item.innerHTML = `
                    <div class="w-7 h-7 rounded-lg ${act.iconBox || 'bg-sky-500/20 text-sky-400'} flex items-center justify-center shrink-0 mt-0.5 text-xs">
                        <i class="fa-solid ${act.icon || 'fa-clipboard-check'}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1.5">
                            <span class="font-bold text-[11px] text-white truncate">${act.title}</span>
                            <span class="text-[8px] px-1.5 py-0.2 rounded font-bold shrink-0 ${act.badgeClass || 'bg-white/10 text-gray-300'}">${act.badge || 'Info'}</span>
                        </div>
                        <p class="text-[9.5px] text-gray-300 leading-snug mt-0.5">${act.desc}</p>
                        <span class="text-[8.5px] text-gray-400 block mt-1"><i class="fa-regular fa-clock text-[8px] mr-1"></i>${act.time}</span>
                    </div>
                `;
                listEl.appendChild(item);
            });
        }

        const modal = document.getElementById('customerDetailModal');
        const backdrop = document.getElementById('customerDetailBackdrop');
        const panel = document.getElementById('customerDetailPanel');

        if (modal && backdrop && panel) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            });
        }
    };

    window.closeCustomerDetailModal = function() {
        const modal = document.getElementById('customerDetailModal');
        const backdrop = document.getElementById('customerDetailBackdrop');
        const panel = document.getElementById('customerDetailPanel');

        if (!modal || !backdrop || !panel) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    };

    // =========================================================
    //  5. MODAL DETAIL & AKSI MITRA
    // =========================================================
    const mitraDataStore = {
        @foreach($mitras ?? [] as $m)
        @php
            $mCode = $m->mitra_id ?: 'SB-' . str_pad((string) $m->id, 4, '0', STR_PAD_LEFT);
            $mStatus = strtolower($m->status ?? 'verifikasi');
            $isAktif = in_array($mStatus, ['aktif', 'approved', 'disetujui', 'active']);
            $normStatus = $isAktif ? 'aktif' : (in_array($mStatus, ['suspend', 'ditinjau', 'tolak', 'rejected', 'ditolak']) ? 'suspend' : 'verifikasi');
        @endphp
        {!! json_encode($mCode) !!}: {
            id: {!! json_encode($mCode) !!},
            db_id: {{ $m->id }},
            nama: {!! json_encode($m->name) !!},
            name: {!! json_encode($m->name) !!},
            nik: {!! json_encode($m->nik ?: '-') !!},
            whatsapp: {!! json_encode($m->phone ?: '-') !!},
            phone: {!! json_encode($m->phone ?: '-') !!},
            email: {!! json_encode($m->email ?: '-') !!},
            alamat: {!! json_encode($m->alamat ?: ($m->area ?: ($m->kota ?: '-'))) !!},
            area: {!! json_encode($m->area ?: ($m->kota ?: 'Jakarta')) !!},
            service: {!! json_encode($m->service ?: ($m->keahlian ?: 'Full Home Cleaning & Servis AC')) !!},
            jaminan: {!! json_encode($m->jaminan ?: 'BPKB Sepeda Motor Asli') !!},
            avatar: {!! json_encode($m->avatar) !!},
            rating: '{{ $m->reviewed_orders_avg_review_rating === null ? 'Belum ada ulasan' : number_format((float) $m->reviewed_orders_avg_review_rating, 1) }}',
            tasks: {{ (int) $m->completed_orders_count }},
            totalEarning: 'Rp 0',
            status: '{{ $normStatus }}',
            statusLabel: '{{ $isAktif ? 'Aktif Terverifikasi' : 'Menunggu Verifikasi & ACC' }}',
            orders: {!! json_encode(($m->custom_orders ?? collect())->map(fn ($o) => ['code' => $o->code, 'layanan' => $o->layanan, 'status' => $o->status, 'biaya' => $o->biaya, 'customer' => $o->customer_name, 'tanggal' => $o->created_at?->format('d M Y')])->values()) !!},
            activities: [
                { title: "Pendaftaran Akun Mitra", desc: "Berkas identitas e-KTP dan {{ addslashes($m->jaminan ?: 'dokumen jaminan') }} telah diserahkan.", time: "{{ $m->created_at ? $m->created_at->diffForHumans() : 'Baru saja' }}", badge: "Pendaftaran", badgeClass: "bg-amber-500/20 text-amber-300 border border-amber-500/30", icon: "fa-id-card", iconBox: "bg-amber-500/20 text-amber-400" },
                @if($isAktif)
                { title: "Akun Mitra Disetujui & Terverifikasi", desc: "Pendaftaran disetujui penuh oleh Admin.", time: "{{ $m->updated_at ? $m->updated_at->diffForHumans() : 'Baru saja' }}", badge: "Aktif", badgeClass: "bg-teal-500/20 text-teal-300 border border-teal-500/30", icon: "fa-check-double", iconBox: "bg-teal-500/20 text-teal-400" }
                @else
                { title: "Verifikasi Berkas Fisik & Keahlian", desc: "Menunggu persetujuan admin untuk aktivasi pesanan.", time: "Menunggu", badge: "Verifikasi", badgeClass: "bg-sky-500/20 text-sky-300 border border-sky-500/30", icon: "fa-shield-halved", iconBox: "bg-sky-500/20 text-sky-400" }
                @endif
            ]
        },
        @endforeach
    };

    window.openMitraDetail = function(id) {
        const m = mitraDataStore[id];
        if (!m) return;

        const avBox = document.getElementById('mitraModalAvatarBox');
        if (avBox) {
            if (m.avatar) {
                avBox.innerHTML = `<img src="${m.avatar}" class="w-full h-full object-cover rounded-2xl">`;
            } else {
                avBox.className = 'w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg border shrink-0 shadow-md bg-[#0E1D31] text-white border-white/20';
                avBox.textContent = (m.name || m.nama || 'M').charAt(0).toUpperCase();
            }
        }

        const nameEl = document.getElementById('mitraModalName');
        if (nameEl) nameEl.textContent = m.name || m.nama;

        const badgeEl = document.getElementById('mitraModalStatusBadge');
        if (badgeEl) {
            const isAktif = (m.status === 'aktif' || m.status === 'approved');
            badgeEl.className = isAktif ? 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30';
            badgeEl.textContent = isAktif ? 'Aktif Terverifikasi' : 'Menunggu Verifikasi & ACC';
        }

        const srvEl = document.getElementById('mitraModalServiceArea');
        if (srvEl) srvEl.textContent = `${m.service || 'Cleaning Service'} • ${m.area || 'Jakarta'}`;

        const idEl = document.getElementById('mitraModalId');
        if (idEl) idEl.textContent = '#' + (m.id || m.regNo);

        const ratEl = document.getElementById('mitraModalRating');
        if (ratEl) ratEl.textContent = m.rating || 'Belum ada ulasan';

        const ordEl = document.getElementById('mitraModalOrderCount');
        if (ordEl) ordEl.textContent = (m.tasks || 0) + ' order';

        const earnEl = document.getElementById('mitraModalTotalEarning');
        if (earnEl) earnEl.textContent = m.totalEarning || 'Rp 0';

        const nikEl = document.getElementById('mitraModalNik');
        if (nikEl) nikEl.textContent = m.nik || '3174091507920003';

        const phEl = document.getElementById('mitraModalPhone');
        if (phEl) phEl.textContent = m.whatsapp || m.phone;

        const emEl = document.getElementById('mitraModalEmail');
        if (emEl) emEl.textContent = m.email || '-';

        const adEl = document.getElementById('mitraModalAddress');
        if (adEl) adEl.textContent = m.alamat || m.area || 'DKI Jakarta';

        const jmEl = document.getElementById('mitraModalDocumentBadge');
        if (jmEl) jmEl.textContent = (m.jaminan || 'BPKB Asli') + ' Valid';

        const chatBtn = document.getElementById('mitraModalChatBtn');
        const phClean = String(m.whatsapp || m.phone || '').replace(/[^0-9]/g, '');
        if (chatBtn) chatBtn.href = 'tel:' + phClean;

        const actContainer = document.getElementById('mitraModalActionBtnContainer');
        if (actContainer) {
            const isAktif = (m.status === 'aktif' || m.status === 'approved');
            if (isAktif) {
                actContainer.innerHTML = `
                    <button type="button" onclick="closeMitraDetailModal(); suspendMitraById('${m.id}')" class="w-full py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 font-bold text-xs transition-all cursor-pointer">
                        <i class="fa-solid fa-pause mr-1"></i> Suspend Mitra
                    </button>
                `;
            } else {
                actContainer.innerHTML = `
                    <button type="button" onclick="closeMitraDetailModal(); setujuiMitraById('${m.id}')" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-all shadow-md cursor-pointer">
                        <i class="fa-solid fa-circle-check mr-1"></i> Setujui &amp; ACC Mitra
                    </button>
                `;
            }
        }

        const listEl = document.getElementById('mitraModalActivityList');
        if (listEl) {
            listEl.innerHTML = '';

            const orderActivities = (m.orders || []).slice(0, 5).map((o) => ({
                title: 'Pesanan ' + o.code + ': ' + (o.layanan || 'Layanan SayaBantu'),
                desc: 'Customer: ' + (o.customer || '-') + ' • Biaya: Rp ' + (o.biaya || '0') + ' • Status: ' + (o.status || '-'),
                time: o.tanggal || '-',
                badge: 'Pesanan',
                badgeClass: 'bg-sky-500/20 text-sky-300 border border-sky-500/30',
                icon: 'fa-clipboard-list',
                iconBox: 'bg-sky-500/20 text-sky-400'
            }));

            const activities = orderActivities.concat(m.activities || []);

            activities.forEach(act => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl bg-white/[0.04] border border-white/10 flex items-start gap-2.5 hover:bg-white/[0.08] transition-colors';
                item.innerHTML = `
                    <div class="w-7 h-7 rounded-lg ${act.iconBox || 'bg-teal-500/20 text-teal-400'} flex items-center justify-center shrink-0 mt-0.5 text-xs">
                        <i class="fa-solid ${act.icon || 'fa-clipboard-check'}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1.5">
                            <span class="font-bold text-[11px] text-white truncate">${act.title}</span>
                            <span class="text-[8px] px-1.5 py-0.2 rounded font-bold shrink-0 ${act.badgeClass || 'bg-white/10 text-gray-300'}">${act.badge || 'Info'}</span>
                        </div>
                        <p class="text-[9.5px] text-gray-300 leading-snug mt-0.5">${act.desc}</p>
                        <span class="text-[8.5px] text-gray-400 block mt-1"><i class="fa-regular fa-clock text-[8px] mr-1"></i>${act.time}</span>
                    </div>
                `;
                listEl.appendChild(item);
            });
        }

        const modal = document.getElementById('mitraDetailModal');
        const panel = document.getElementById('mitraDetailPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
        }
    };

    window.closeMitraDetailModal = function() {
        const modal = document.getElementById('mitraDetailModal');
        const panel = document.getElementById('mitraDetailPanel');
        if (!modal || !panel) return;

        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
        }, 200);
    };

    // =========================================================
    //  6. AKSI SETUJUI, TOLAK, SUSPEND MITRA
    // =========================================================
    window.setujuiMitraById = function(id, explicitDbId) {
        const card = document.querySelector(`.mitra-card[data-id="${id}"]`);
        if (mitraDataStore[id]) {
            mitraDataStore[id].status = 'aktif';
            mitraDataStore[id].statusLabel = 'Aktif Terverifikasi';
        }

        const dbId = explicitDbId || (mitraDataStore[id] && mitraDataStore[id].db_id) || (card ? card.getAttribute('data-db-id') : null) || id;

        if (card) {
            card.setAttribute('data-status', 'aktif');
            const badgeBox = card.querySelector('.shrink-0');
            if (badgeBox) {
                badgeBox.innerHTML = `
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif Terverifikasi
                    </span>
                `;
            }
            const footer = card.querySelector('.border-t');
            if (footer) {
                footer.innerHTML = `
                    <div class="flex items-center gap-1.5">
                        <span class="py-1.5 px-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px] inline-flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui (Approved)
                        </span>
                        <button type="button" onclick="suspendMitraById('${id}', ${dbId})" class="py-1.5 px-2.5 rounded-xl bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-600 font-bold text-[10px] transition-colors cursor-pointer border border-gray-200">
                            Suspend
                        </button>
                    </div>
                    <button type="button" onclick="openMitraDetail('${id}')" class="py-2 px-3 rounded-xl bg-[#0E1D31] hover:bg-slate-800 text-white font-bold text-[10px] flex items-center gap-1.5 transition-all shadow-xs cursor-pointer ml-auto">
                        <i class="fa-solid fa-folder-open text-[9.5px]"></i>
                        <span>Detail Berkas</span>
                    </button>
                `;
            }
        }

        // Update counters di UI
        const vCountEl = document.getElementById('statMitraVerifikasi');
        if (vCountEl) {
            const curV = parseInt(vCountEl.textContent.trim()) || 0;
            if (curV > 0) vCountEl.textContent = curV - 1;
        }
        const vTabEl = document.getElementById('mitraFilterCountVerifikasi');
        if (vTabEl) {
            const curV = parseInt(vTabEl.textContent.trim()) || 0;
            if (curV > 0) vTabEl.textContent = curV - 1;
        }
        const aCountEl = document.getElementById('statMitraAktif');
        if (aCountEl) {
            const curA = parseInt(aCountEl.textContent.trim()) || 0;
            aCountEl.textContent = curA + 1;
        }
        const aTabEl = document.getElementById('mitraFilterCountAktif');
        if (aTabEl) {
            const curA = parseInt(aTabEl.textContent.trim()) || 0;
            aTabEl.textContent = curA + 1;
        }

        fetch(`/admin/api/mitras/${dbId}/acc`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }).catch(() => {});

        if (typeof showToast === 'function') {
            showToast('Mitra Disetujui', 'Pendaftaran mitra berhasil disetujui & akun telah aktif.', 'success');
        }
    };

    window.suspendMitraById = function(id, explicitDbId) {
        if (!confirm('Apakah Anda yakin ingin menangguhkan (suspend) akun mitra ini?')) return;

        const card = document.querySelector(`.mitra-card[data-id="${id}"]`);
        if (mitraDataStore[id]) {
            mitraDataStore[id].status = 'suspend';
        }

        if (card) {
            card.setAttribute('data-status', 'suspend');
            const badgeBox = card.querySelector('.shrink-0');
            if (badgeBox) {
                badgeBox.innerHTML = `
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditangguhkan
                    </span>
                `;
            }
        }

        const dbId = explicitDbId || (mitraDataStore[id] && mitraDataStore[id].db_id) || (card ? card.getAttribute('data-db-id') : null) || id;
        fetch(`/admin/api/mitras/${dbId}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }).catch(() => {});

        if (typeof showToast === 'function') {
            showToast('Mitra Ditangguhkan', 'Akun mitra telah dinonaktifkan sementara.', 'warning');
        }
    };

    window.openModalTolakMitra = function(id, explicitDbId) {
        const targetInput = document.getElementById('tolakMitraTargetId');
        if (targetInput) targetInput.value = id;
        const targetDbIdInput = document.getElementById('tolakMitraTargetDbId');
        if (targetDbIdInput) targetDbIdInput.value = explicitDbId || '';

        const modal = document.getElementById('modalTolakMitraAdmin');
        if (modal) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
        }
    };

    window.closeModalTolakMitra = function() {
        const modal = document.getElementById('modalTolakMitraAdmin');
        if (modal) {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
        }
    };

    window.handleSelectAlasanTolak = function(val) {
        const textarea = document.getElementById('tolakMitraReasonText');
        if (!textarea) return;
        if (val === 'custom') {
            textarea.value = '';
            textarea.placeholder = 'Tuliskan alasan penolakan secara spesifik di sini...';
            textarea.focus();
        } else {
            textarea.value = val + '. Silakan perbaiki dan lengkapi berkas pendaftaran Anda.';
        }
    };

    window.handleTolakMitraSubmit = function(e) {
        e.preventDefault();
        const id = document.getElementById('tolakMitraTargetId')?.value;
        if (!id) return;
        const explicitDbId = document.getElementById('tolakMitraTargetDbId')?.value;
        const card = document.querySelector(`.mitra-card[data-id="${id}"]`);

        if (card) {
            card.remove();
        }

        closeModalTolakMitra();

        // Update counters
        const totalEl = document.getElementById('statMitraTotal');
        if (totalEl) {
            const curT = parseInt(totalEl.textContent.trim()) || 0;
            if (curT > 0) totalEl.textContent = curT - 1;
        }
        const badgeEl = document.getElementById('heroMitraBadge');
        if (badgeEl && totalEl) {
            badgeEl.textContent = totalEl.textContent + ' Mitra';
        }
        const mitraCountEl = document.getElementById('mitraCount');
        if (mitraCountEl && totalEl) {
            mitraCountEl.textContent = totalEl.textContent;
        }
        const vCountEl = document.getElementById('statMitraVerifikasi');
        if (vCountEl) {
            const curV = parseInt(vCountEl.textContent.trim()) || 0;
            if (curV > 0) vCountEl.textContent = curV - 1;
        }
        const vTabEl = document.getElementById('mitraFilterCountVerifikasi');
        if (vTabEl) {
            const curV = parseInt(vTabEl.textContent.trim()) || 0;
            if (curV > 0) vTabEl.textContent = curV - 1;
        }

        const dbId = explicitDbId || (mitraDataStore[id] && mitraDataStore[id].db_id) || (card ? card.getAttribute('data-db-id') : null) || id;
        fetch(`/admin/api/mitras/${dbId}/tolak`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                reason: document.getElementById('tolakMitraReasonText')?.value || 'Berkas tidak sesuai syarat.',
                can_reapply: document.getElementById('tolakMitraCanReapply')?.checked ?? true
            })
        }).catch(() => {});

        if (typeof showToast === 'function') {
            showToast('Pendaftaran Ditolak', 'Notifikasi penolakan & instruksi perbaikan berhasil dikirim ke calon mitra.', 'error');
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCustomerDetailModal();
            closeMitraDetailModal();
            closeModalTolakMitra();
        }
    });

})();
</script>
@endpush

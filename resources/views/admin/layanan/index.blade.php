@extends('layouts.admin')

@section('title', 'Manajemen Layanan - SayaBantu.com')
@section('page-title', 'Manajemen Layanan')
@section('page-subtitle', 'Kelola daftar jenis layanan, kategori, ketentuan tarif, dan mode harga')

@section('hero')
<div class="px-5 pt-1 pb-0">
    {{-- STAT RINGKASAN LAYANAN --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-sky-400"></i>
                Katalog Layanan Platform
            </span>
            <button
                type="button"
                onclick="openServiceModal('create')"
                class="px-2.5 py-1 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-[10px] font-bold flex items-center gap-1 shadow-sm transition-all"
            >
                <i class="fa-solid fa-plus text-[9px]"></i>
                Tambah Layanan
            </button>
        </div>

        <div class="grid grid-cols-5 gap-1.5 text-center" id="layananStatFilters">
            <button type="button" class="layanan-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-sky-400 shadow-md shadow-sky-500/25 cursor-pointer transition-all scale-[1.03] text-center focus:outline-none" onclick="filterServices('all')" data-filter="all">
                <div class="text-xs font-black text-white" id="statTotalLayanan">5</div>
                <div class="text-[8px] text-white font-bold mt-0.5">Total</div>
            </button>
            <button type="button" class="layanan-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterServices('aktif')" data-filter="aktif">
                <div class="text-xs font-black text-emerald-400" id="statAktifLayanan">4</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Aktif</div>
            </button>
            <button type="button" class="layanan-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterServices('nonaktif')" data-filter="nonaktif">
                <div class="text-xs font-black text-rose-400" id="statNonaktifLayanan">1</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Nonaktif</div>
            </button>
            <button type="button" class="layanan-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterServices('nego')" data-filter="nego">
                <div class="text-xs font-black text-amber-400" id="statNegoLayanan">3</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Nego</div>
            </button>
            <button type="button" class="layanan-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterServices('pas')" data-filter="pas">
                <div class="text-xs font-black text-sky-400" id="statPasLayanan">2</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Harga Pas</div>
            </button>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="mt-3">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input
                type="text"
                id="searchLayananInput"
                placeholder="Cari nama layanan, kategori..."
                onkeyup="searchServices(this.value)"
                class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/15 focus:outline-none focus:border-sky-400 focus:bg-white/15 transition-all"
            >
            <button
                type="button"
                id="clearSearchLayananBtn"
                onclick="clearSearchServices()"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs hidden"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-3" style="margin-top: 0.6cm;">

    <div class="flex items-center justify-between px-1 text-xs" style="margin-bottom: 0.6cm;">
        <span class="text-xs font-bold text-white tracking-wide">Daftar Ketentuan Layanan</span>
        <span class="text-[10px] text-sky-200/90 font-medium">Total <span id="displayedLayananCount" class="font-bold text-white">5</span> Layanan Tampil</span>
    </div>

    <div id="serviceList" class="space-y-3">

        {{-- LAYANAN 1 --}}
        <div class="service-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 transition-all hover:border-sky-300 cursor-pointer active:scale-[0.99] group"
            onclick="editService(this)"
            data-category="pembersihan"
            data-status="aktif"
            data-mode="nego"
            data-name="full home cleaning"
            data-base-price="150000"
            data-min-price="100000"
            data-desc="Pembersihan rumah menyeluruh meliputi ruang tamu, kamar tidur, kamar mandi, dan dapur."
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#173B67] flex items-center justify-center text-lg border border-blue-100 shrink-0 service-icon-box">
                        <i class="fa-solid fa-broom"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-xs font-bold text-gray-900 truncate service-title">Full Home Cleaning</h3>
                            <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 service-mode-badge">
                                Nego Harga
                            </span>
                        </div>
                        <p class="text-[10px] text-sky-600 font-semibold mt-0.5 service-category-label">Kategori: Pembersihan & Rumah</p>
                        <p class="text-[9.5px] text-gray-500 line-clamp-2 mt-0.5 service-desc">
                            Pembersihan rumah menyeluruh meliputi ruang tamu, kamar tidur, kamar mandi, dan dapur.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge">
                    Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                    <strong class="text-gray-900 text-xs value-base-price">Rp 150.000</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                    <strong class="text-amber-700 text-xs value-min-price">Rp 100.000</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400 service-meta-info">ID: #SRV-01 • Diperbarui 3 hari lalu</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                    </button>
                    <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn">
                        Nonaktifkan
                    </button>
                </div>
            </div>
        </div>

        {{-- LAYANAN 2 --}}
        <div class="service-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 transition-all hover:border-sky-300 cursor-pointer active:scale-[0.99] group"
            onclick="editService(this)"
            data-category="teknisi"
            data-status="aktif"
            data-mode="nego"
            data-name="service ac split"
            data-base-price="85000"
            data-min-price="70000"
            data-desc="Cuci filter, cuci outdoor, dan cek tekanan freon untuk AC 0.5 - 2 PK."
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg border border-sky-100 shrink-0 service-icon-box">
                        <i class="fa-solid fa-snowflake"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-xs font-bold text-gray-900 truncate service-title">Service AC Split</h3>
                            <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 service-mode-badge">
                                Nego Harga
                            </span>
                        </div>
                        <p class="text-[10px] text-sky-600 font-semibold mt-0.5 service-category-label">Kategori: Teknisi & Pendingin</p>
                        <p class="text-[9.5px] text-gray-500 line-clamp-2 mt-0.5 service-desc">
                            Cuci filter, cuci outdoor, dan cek tekanan freon untuk AC 0.5 - 2 PK.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge">
                    Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                    <strong class="text-gray-900 text-xs value-base-price">Rp 85.000</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                    <strong class="text-amber-700 text-xs value-min-price">Rp 70.000</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400 service-meta-info">ID: #SRV-02 • Diperbarui 1 minggu lalu</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                    </button>
                    <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn">
                        Nonaktifkan
                    </button>
                </div>
            </div>
        </div>

        {{-- LAYANAN 3 --}}
        <div class="service-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 transition-all hover:border-sky-300 cursor-pointer active:scale-[0.99] group"
            onclick="editService(this)"
            data-category="logistik"
            data-status="aktif"
            data-mode="nego"
            data-name="angkut barang pindahan"
            data-base-price="220000"
            data-min-price="180000"
            data-desc="Jasa sewa mobil pick-up & 1 personil bongkar muat pindahan rumah/kost."
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg border border-teal-100 shrink-0 service-icon-box">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-xs font-bold text-gray-900 truncate service-title">Angkut Barang Pindahan</h3>
                            <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 service-mode-badge">
                                Nego Harga
                            </span>
                        </div>
                        <p class="text-[10px] text-teal-600 font-semibold mt-0.5 service-category-label">Kategori: Logistik & Transport</p>
                        <p class="text-[9.5px] text-gray-500 line-clamp-2 mt-0.5 service-desc">
                            Jasa sewa mobil pick-up & 1 personil bongkar muat pindahan rumah/kost.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge">
                    Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                    <strong class="text-gray-900 text-xs value-base-price">Rp 220.000</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                    <strong class="text-amber-700 text-xs value-min-price">Rp 180.000</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400 service-meta-info">ID: #SRV-03 • Diperbarui 4 hari lalu</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                    </button>
                    <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn">
                        Nonaktifkan
                    </button>
                </div>
            </div>
        </div>

        {{-- LAYANAN 4: HARGA PAS --}}
        <div class="service-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 transition-all hover:border-sky-300 cursor-pointer active:scale-[0.99] group"
            onclick="editService(this)"
            data-category="asisten"
            data-status="aktif"
            data-mode="pas"
            data-name="jasa belanja harian"
            data-base-price="45000"
            data-min-price="45000"
            data-desc="Bantuan belanja kebutuhan dapur, pasar tradisional, atau supermarket terdekat."
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg border border-indigo-100 shrink-0 service-icon-box">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-xs font-bold text-gray-900 truncate service-title">Jasa Belanja Harian</h3>
                            <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200 service-mode-badge">
                                Harga Pas
                            </span>
                        </div>
                        <p class="text-[10px] text-indigo-600 font-semibold mt-0.5 service-category-label">Kategori: Asisten Harian</p>
                        <p class="text-[9.5px] text-gray-500 line-clamp-2 mt-0.5 service-desc">
                            Bantuan belanja kebutuhan dapur, pasar tradisional, atau supermarket terdekat.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge">
                    Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN TETAP</span>
                    <strong class="text-gray-900 text-xs value-base-price">Rp 45.000</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">MODE HARGA</span>
                    <strong class="text-blue-700 text-xs value-min-price">Tarif Pas</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400 service-meta-info">ID: #SRV-04 • Diperbarui 2 minggu lalu</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                    </button>
                    <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn">
                        Nonaktifkan
                    </button>
                </div>
            </div>
        </div>

        {{-- LAYANAN 5: NONAKTIF --}}
        <div class="service-card p-4 rounded-[22px] bg-white border border-gray-200/80 opacity-75 shadow-sm space-y-3 transition-all hover:border-gray-400 cursor-pointer active:scale-[0.99] group"
            onclick="editService(this)"
            data-category="pembersihan"
            data-status="nonaktif"
            data-mode="nego"
            data-name="cuci sofa springbed"
            data-base-price="180000"
            data-min-price="140000"
            data-desc="Deep cleaning sedot debu tungau dan cuci busa basah dengan sabun antibakteri."
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center text-lg border border-gray-200 shrink-0 service-icon-box">
                        <i class="fa-solid fa-couch"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-xs font-bold text-gray-700 truncate service-title">Cuci Sofa & Springbed</h3>
                            <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 service-mode-badge">
                                Nego Harga
                            </span>
                        </div>
                        <p class="text-[10px] text-sky-600 font-semibold mt-0.5 service-category-label">Kategori: Pembersihan Furnitur</p>
                        <p class="text-[9.5px] text-gray-400 line-clamp-2 mt-0.5 service-desc">
                            Deep cleaning sedot debu tungau dan cuci busa basah dengan sabun antibakteri.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200 shrink-0 service-status-badge">
                    Nonaktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                    <strong class="text-gray-700 text-xs value-base-price">Rp 180.000</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                    <strong class="text-amber-700 text-xs value-min-price">Rp 140.000</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400 service-meta-info">ID: #SRV-05 • Dinonaktifkan sementara</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                    </button>
                    <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-semibold transition-colors service-toggle-btn">
                        Aktifkan
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('modals')
{{-- MODAL TAMBAH / UBAH LAYANAN --}}
<div id="serviceFormModal" class="sb-overlay-backdrop fixed inset-0 z-[200] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-service-click absolute inset-0 bg-black/65 backdrop-blur-[2px]" onclick="closeServiceModal()"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="serviceFormPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[26px] shadow-2xl p-5 border-t border-white/15 max-h-[90vh] overflow-y-auto translate-y-full transition-transform">
            
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#173B67] text-sky-400 flex items-center justify-center text-sm border border-sky-400/30">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white leading-none" id="serviceModalTitle">Tambah Layanan Baru</h3>
                        <p class="text-[10px] text-gray-400 mt-1">Konfigurasi tarif, mode, dan deskripsi</p>
                    </div>
                </div>
                <button type="button" onclick="closeServiceModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="serviceForm" onsubmit="saveServiceForm(event)" class="mt-4 space-y-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Nama Layanan</label>
                    <input type="text" id="formServiceName" required placeholder="Contoh: Bersih Kamar Kos" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Kategori</label>
                    <select id="formServiceCategory" required class="w-full bg-[#173B67] border border-white/15 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        <option value="pembersihan">Pembersihan & Rumah</option>
                        <option value="teknisi">Teknisi & Pendingin</option>
                        <option value="logistik">Logistik & Angkut</option>
                        <option value="asisten">Asisten & Belanja</option>
                        <option value="lainnya">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Deskripsi Singkat</label>
                    <textarea id="formServiceDesc" rows="2" required placeholder="Jelaskan ruang lingkup layanan..." class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-300 mb-1">Harga Acuan (Rp)</label>
                        <input type="number" id="formServiceBasePrice" required min="10000" step="5000" placeholder="100000" onchange="validatePrices()" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-300 mb-1">Batas Min. Nego (Rp)</label>
                        <input type="number" id="formServiceMinPrice" min="10000" step="5000" placeholder="80000" onchange="validatePrices()" class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-300 mb-1">Mekanisme / Mode Harga</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10">
                            <input type="radio" name="formPriceMode" value="nego" checked onchange="toggleMinPriceField(this.value)" class="text-sky-500">
                            <span>Nego Harga</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10">
                            <input type="radio" name="formPriceMode" value="pas" onchange="toggleMinPriceField(this.value)" class="text-sky-500">
                            <span>Harga Pas</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/10">
                    <div>
                        <div class="text-xs font-bold text-white">Status Layanan</div>
                        <div class="text-[10px] text-gray-400">Tampilkan di katalog pencarian customer</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="formServiceStatusToggle" checked class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="button" onclick="closeServiceModal()" class="flex-1 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-semibold text-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Layanan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentEditingCard = null;
    let currentModalMode = 'create';

    const categoryMap = {
        pembersihan: {
            label: 'Kategori: Pembersihan & Rumah',
            icon: 'fa-broom',
            bg: 'bg-blue-50 text-[#173B67] border-blue-100',
            colorText: 'text-sky-600'
        },
        teknisi: {
            label: 'Kategori: Teknisi & Pendingin',
            icon: 'fa-snowflake',
            bg: 'bg-sky-50 text-sky-600 border-sky-100',
            colorText: 'text-sky-600'
        },
        logistik: {
            label: 'Kategori: Logistik & Transport',
            icon: 'fa-truck',
            bg: 'bg-teal-50 text-teal-600 border-teal-100',
            colorText: 'text-teal-600'
        },
        asisten: {
            label: 'Kategori: Asisten Harian',
            icon: 'fa-bag-shopping',
            bg: 'bg-indigo-50 text-indigo-600 border-indigo-100',
            colorText: 'text-indigo-600'
        },
        lainnya: {
            label: 'Kategori: Layanan Lainnya',
            icon: 'fa-layer-group',
            bg: 'bg-purple-50 text-purple-600 border-purple-100',
            colorText: 'text-purple-600'
        }
    };

    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function updateSummaryStats() {
        const cards = document.querySelectorAll('.service-card');
        let total = cards.length;
        let aktif = 0;
        let nonaktif = 0;
        let nego = 0;
        let pas = 0;

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            const mode = card.getAttribute('data-mode');

            if (status === 'aktif') aktif++;
            else if (status === 'nonaktif') nonaktif++;

            if (mode === 'nego') nego++;
            else if (mode === 'pas') pas++;
        });

        const elTotal = document.getElementById('statTotalLayanan');
        const elAktif = document.getElementById('statAktifLayanan');
        const elNonaktif = document.getElementById('statNonaktifLayanan');
        const elNego = document.getElementById('statNegoLayanan');
        const elPas = document.getElementById('statPasLayanan');

        if (elTotal) elTotal.innerText = total;
        if (elAktif) elAktif.innerText = aktif;
        if (elNonaktif) elNonaktif.innerText = nonaktif;
        if (elNego) elNego.innerText = nego;
        if (elPas) elPas.innerText = pas;

        // Re-apply current active stat filter count
        const activeTab = document.querySelector('.layanan-stat-tab.scale-\\[1\\.03\\]');
        const currentFilter = activeTab ? activeTab.getAttribute('data-filter') : 'all';
        applyServicesFilter(currentFilter || 'all');
    }

    function applyServicesFilter(category) {
        const cards = document.querySelectorAll('.service-card');
        let count = 0;
        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            const mode = card.getAttribute('data-mode');

            let show = false;
            if (category === 'all') show = true;
            else if (category === 'aktif' && status === 'aktif') show = true;
            else if (category === 'nonaktif' && status === 'nonaktif') show = true;
            else if (category === 'nego' && mode === 'nego') show = true;
            else if (category === 'pas' && mode === 'pas') show = true;

            card.style.display = show ? 'block' : 'none';
            if (show) count++;
        });

        const countEl = document.getElementById('displayedLayananCount');
        if (countEl) countEl.innerText = count;
    }

    function filterServices(category) {
        // Berikan efek aktif berwarna pada kartu statistik yang memiliki angka di atas
        document.querySelectorAll('.layanan-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'border-2', 'bg-white/[0.18]',
                'bg-emerald-500/20', 'bg-rose-500/20', 'bg-amber-500/20', 'bg-sky-500/20',
                'border-sky-400', 'border-emerald-400', 'border-rose-400', 'border-amber-400',
                'shadow-sky-500/25', 'shadow-emerald-500/25', 'shadow-rose-500/25', 'shadow-amber-500/25'
            );
            card.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (f === category) {
                card.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-md', 'border-2');
                if (category === 'all') {
                    card.classList.add('bg-white/[0.18]', 'border-sky-400', 'shadow-sky-500/25');
                } else if (category === 'aktif') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25');
                } else if (category === 'nonaktif') {
                    card.classList.add('bg-rose-500/20', 'border-rose-400', 'shadow-rose-500/25');
                } else if (category === 'nego') {
                    card.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25');
                } else if (category === 'pas') {
                    card.classList.add('bg-sky-500/20', 'border-sky-400', 'shadow-sky-500/25');
                }
            }
        });

        applyServicesFilter(category);
    }
    window.filterServices = filterServices;
    window.searchServices = searchServices;

    function searchServices(keyword) {
        keyword = keyword.toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchLayananBtn');
        if (clearBtn) clearBtn.classList.toggle('hidden', keyword === '');

        const cards = document.querySelectorAll('.service-card');
        let count = 0;
        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const desc = (card.getAttribute('data-desc') || '').toLowerCase();
            const cat = (card.getAttribute('data-category') || '').toLowerCase();

            if (name.includes(keyword) || desc.includes(keyword) || cat.includes(keyword)) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        const countEl = document.getElementById('displayedLayananCount');
        if (countEl) countEl.innerText = count;
    }

    function clearSearchServices() {
        const input = document.getElementById('searchLayananInput');
        if (input) input.value = '';
        searchServices('');
    }

    function toggleMinPriceField(mode) {
        const minPriceInput = document.getElementById('formServiceMinPrice');
        if (mode === 'pas') {
            minPriceInput.disabled = true;
            minPriceInput.value = document.getElementById('formServiceBasePrice').value;
            minPriceInput.classList.add('opacity-50');
        } else {
            minPriceInput.disabled = false;
            minPriceInput.classList.remove('opacity-50');
        }
    }

    function validatePrices() {
        const base = parseInt(document.getElementById('formServiceBasePrice').value) || 0;
        const min = parseInt(document.getElementById('formServiceMinPrice').value) || 0;
        const mode = document.querySelector('input[name="formPriceMode"]:checked')?.value || 'nego';

        if (mode === 'nego' && min > base) {
            if (typeof showToast === 'function') {
                showToast('Validasi Harga', 'Batas minimum negosiasi tidak boleh lebih tinggi dari harga acuan!', 'warning');
            }
            document.getElementById('formServiceMinPrice').value = base;
        }
    }

    function openServiceModal(mode, el = null) {
        currentModalMode = mode;
        currentEditingCard = el ? (el.classList.contains('service-card') ? el : el.closest('.service-card')) : null;

        const modal = document.getElementById('serviceFormModal');
        const panel = document.getElementById('serviceFormPanel');
        const title = document.getElementById('serviceModalTitle');

        if (mode === 'create') {
            title.innerText = 'Tambah Layanan Baru';
            document.getElementById('serviceForm').reset();
            document.getElementById('formServiceMinPrice').disabled = false;
            document.getElementById('formServiceMinPrice').classList.remove('opacity-50');
            const defaultModeRadio = document.querySelector('input[name="formPriceMode"][value="nego"]');
            if (defaultModeRadio) defaultModeRadio.checked = true;
            document.getElementById('formServiceStatusToggle').checked = true;
        } else if (mode === 'edit' && currentEditingCard) {
            title.innerText = 'Ubah Ketentuan Layanan';
            const card = currentEditingCard;
            const nameEl = card.querySelector('.service-title');
            const descEl = card.querySelector('.service-desc');

            document.getElementById('formServiceName').value = nameEl ? nameEl.innerText.trim() : (card.getAttribute('data-name') || '');
            document.getElementById('formServiceCategory').value = card.getAttribute('data-category') || 'pembersihan';
            document.getElementById('formServiceDesc').value = card.getAttribute('data-desc') || (descEl ? descEl.innerText.trim() : '');
            document.getElementById('formServiceBasePrice').value = card.getAttribute('data-base-price') || 100000;
            document.getElementById('formServiceMinPrice').value = card.getAttribute('data-min-price') || 80000;

            const modeVal = card.getAttribute('data-mode') || 'nego';
            const radio = document.querySelector(`input[name="formPriceMode"][value="${modeVal}"]`);
            if (radio) radio.checked = true;
            toggleMinPriceField(modeVal);

            document.getElementById('formServiceStatusToggle').checked = (card.getAttribute('data-status') === 'aktif');
        }

        if (typeof lockAdminBodyScroll === 'function') {
            lockAdminBodyScroll();
        }
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
        panel.classList.remove('translate-y-full');
    }

    function closeServiceModal() {
        const modal = document.getElementById('serviceFormModal');
        const panel = document.getElementById('serviceFormPanel');
        if (!panel || !modal) return;

        panel.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            if (typeof unlockAdminBodyScroll === 'function') {
                unlockAdminBodyScroll();
            }
        }, 200);
    }

    function editService(btn) {
        openServiceModal('edit', btn);
    }

    function toggleServiceStatus(btn) {
        const card = btn.closest('.service-card');
        if (!card) return;

        const currentStatus = card.getAttribute('data-status');
        const badge = card.querySelector('.service-status-badge');
        const metaSpan = card.querySelector('.service-meta-info');
        const title = card.querySelector('.service-title')?.innerText || 'Layanan';

        const currentMeta = metaSpan ? metaSpan.innerText : '';
        const idPart = currentMeta.includes('•') ? currentMeta.split('•')[0].trim() : 'ID: #SRV';

        if (currentStatus === 'aktif') {
            card.setAttribute('data-status', 'nonaktif');
            card.classList.add('opacity-75');

            if (badge) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200 shrink-0 service-status-badge';
                badge.innerText = 'Nonaktif';
            }

            btn.className = 'px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-semibold transition-colors service-toggle-btn';
            btn.innerText = 'Aktifkan';

            if (metaSpan) {
                metaSpan.innerText = idPart + ' • Baru saja dinonaktifkan';
            }

            if (typeof showToast === 'function') {
                showToast('Layanan Dinonaktifkan', `"${title}" dinonaktifkan dan disembunyikan dari katalog customer.`, 'info');
            }
        } else {
            card.setAttribute('data-status', 'aktif');
            card.classList.remove('opacity-75');

            if (badge) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge';
                badge.innerText = 'Aktif';
            }

            btn.className = 'px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn';
            btn.innerText = 'Nonaktifkan';

            if (metaSpan) {
                metaSpan.innerText = idPart + ' • Baru saja diaktifkan';
            }

            if (typeof showToast === 'function') {
                showToast('Layanan Diaktifkan', `"${title}" berhasil diaktifkan dan dapat dipesan kembali.`, 'success');
            }
        }

        updateSummaryStats();
    }

    function saveServiceForm(event) {
        event.preventDefault();

        const name = document.getElementById('formServiceName').value.trim();
        const category = document.getElementById('formServiceCategory').value;
        const desc = document.getElementById('formServiceDesc').value.trim();
        const basePrice = parseInt(document.getElementById('formServiceBasePrice').value) || 0;
        const mode = document.querySelector('input[name="formPriceMode"]:checked')?.value || 'nego';
        let minPrice = parseInt(document.getElementById('formServiceMinPrice').value) || 0;
        const isAktif = document.getElementById('formServiceStatusToggle').checked;
        const status = isAktif ? 'aktif' : 'nonaktif';

        if (mode === 'pas') {
            minPrice = basePrice;
        } else if (mode === 'nego' && minPrice > basePrice) {
            if (typeof showToast === 'function') {
                showToast('Validasi Harga', 'Batas minimum negosiasi tidak boleh lebih tinggi dari harga acuan!', 'warning');
            }
            return;
        }

        const catInfo = categoryMap[category] || categoryMap.pembersihan;

        if (currentModalMode === 'edit' && currentEditingCard) {
            const card = currentEditingCard;

            // 1. Data attributes
            card.setAttribute('data-category', category);
            card.setAttribute('data-status', status);
            card.setAttribute('data-mode', mode);
            card.setAttribute('data-name', name.toLowerCase());
            card.setAttribute('data-base-price', basePrice);
            card.setAttribute('data-min-price', minPrice);
            card.setAttribute('data-desc', desc);

            // 2. Opacity
            if (status === 'aktif') {
                card.classList.remove('opacity-75');
            } else {
                card.classList.add('opacity-75');
            }

            // 3. Title
            const titleEl = card.querySelector('.service-title');
            if (titleEl) titleEl.innerText = name;

            // 4. Mode badge
            const modeBadge = card.querySelector('.service-mode-badge');
            if (modeBadge) {
                if (mode === 'nego') {
                    modeBadge.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 service-mode-badge';
                    modeBadge.innerText = 'Nego Harga';
                } else {
                    modeBadge.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200 service-mode-badge';
                    modeBadge.innerText = 'Harga Pas';
                }
            }

            // 5. Category label & icon
            const catLabel = card.querySelector('.service-category-label');
            if (catLabel) {
                catLabel.className = 'text-[10px] font-semibold mt-0.5 service-category-label ' + catInfo.colorText;
                catLabel.innerText = catInfo.label;
            }
            const iconBox = card.querySelector('.service-icon-box');
            if (iconBox) {
                iconBox.className = 'w-11 h-11 rounded-2xl flex items-center justify-center text-lg border shrink-0 service-icon-box ' + catInfo.bg;
                iconBox.innerHTML = `<i class="fa-solid ${catInfo.icon}"></i>`;
            }

            // 6. Description
            const descEl = card.querySelector('.service-desc');
            if (descEl) descEl.innerText = desc;

            // 7. Status badge & toggle button
            const statusBadge = card.querySelector('.service-status-badge');
            const toggleBtn = card.querySelector('.service-toggle-btn');
            if (statusBadge) {
                if (status === 'aktif') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 service-status-badge';
                    statusBadge.innerText = 'Aktif';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200 shrink-0 service-status-badge';
                    statusBadge.innerText = 'Nonaktif';
                }
            }
            if (toggleBtn) {
                if (status === 'aktif') {
                    toggleBtn.className = 'px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold transition-colors service-toggle-btn';
                    toggleBtn.innerText = 'Nonaktifkan';
                } else {
                    toggleBtn.className = 'px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-semibold transition-colors service-toggle-btn';
                    toggleBtn.innerText = 'Aktifkan';
                }
            }

            // 8. Pricing box
            const pricingBox = card.querySelector('.service-pricing-box');
            if (pricingBox) {
                if (mode === 'nego') {
                    pricingBox.innerHTML = `
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                            <strong class="text-gray-900 text-xs value-base-price">${formatRupiah(basePrice)}</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                            <strong class="text-amber-700 text-xs value-min-price">${formatRupiah(minPrice)}</strong>
                        </div>
                    `;
                } else {
                    pricingBox.innerHTML = `
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN TETAP</span>
                            <strong class="text-gray-900 text-xs value-base-price">${formatRupiah(basePrice)}</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">MODE HARGA</span>
                            <strong class="text-blue-700 text-xs value-min-price">Tarif Pas</strong>
                        </div>
                    `;
                }
            }

            // 9. Metadata timestamp
            const metaSpan = card.querySelector('.service-meta-info');
            if (metaSpan) {
                const currentMeta = metaSpan.innerText;
                const idPart = currentMeta.includes('•') ? currentMeta.split('•')[0].trim() : 'ID: #SRV';
                metaSpan.innerText = idPart + ' • Baru saja diperbarui';
            }

            if (typeof showToast === 'function') {
                showToast('Layanan Diperbarui', `Ketentuan layanan "${name}" berhasil diperbarui.`, 'success');
            }
        } else {
            // Mode Tambah Layanan Baru
            const totalCards = document.querySelectorAll('.service-card').length;
            const newId = `#SRV-${String(totalCards + 1).padStart(2, '0')}`;

            const newCard = document.createElement('div');
            newCard.className = `service-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 transition-all hover:border-sky-300 cursor-pointer active:scale-[0.99] group ${status === 'nonaktif' ? 'opacity-75' : ''}`;
            newCard.setAttribute('onclick', 'editService(this)');
            newCard.setAttribute('data-category', category);
            newCard.setAttribute('data-status', status);
            newCard.setAttribute('data-mode', mode);
            newCard.setAttribute('data-name', name.toLowerCase());
            newCard.setAttribute('data-base-price', basePrice);
            newCard.setAttribute('data-min-price', minPrice);
            newCard.setAttribute('data-desc', desc);

            const pricingHtml = (mode === 'nego') ? `
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN</span>
                    <strong class="text-gray-900 text-xs value-base-price">${formatRupiah(basePrice)}</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">BATAS MIN. NEGOSIASI</span>
                    <strong class="text-amber-700 text-xs value-min-price">${formatRupiah(minPrice)}</strong>
                </div>
            ` : `
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-base-price">HARGA ACUAN TETAP</span>
                    <strong class="text-gray-900 text-xs value-base-price">${formatRupiah(basePrice)}</strong>
                </div>
                <div>
                    <span class="text-gray-400 text-[8.5px] block font-medium label-min-price">MODE HARGA</span>
                    <strong class="text-blue-700 text-xs value-min-price">Tarif Pas</strong>
                </div>
            `;

            newCard.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg border shrink-0 service-icon-box ${catInfo.bg}">
                            <i class="fa-solid ${catInfo.icon}"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs font-bold text-gray-900 truncate service-title">${name}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold ${mode === 'nego' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200'} service-mode-badge">
                                    ${mode === 'nego' ? 'Nego Harga' : 'Harga Pas'}
                                </span>
                            </div>
                            <p class="text-[10px] font-semibold mt-0.5 service-category-label ${catInfo.colorText}">${catInfo.label}</p>
                            <p class="text-[9.5px] text-gray-500 line-clamp-2 mt-0.5 service-desc">${desc}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold ${status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200'} shrink-0 service-status-badge">
                        ${status === 'aktif' ? 'Aktif' : 'Nonaktif'}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px] service-pricing-box">
                    ${pricingHtml}
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                    <span class="text-[9.5px] text-gray-400 service-meta-info">ID: ${newId} • Baru saja dibuat</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="event.stopPropagation(); editService(this)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah
                        </button>
                        <button type="button" onclick="event.stopPropagation(); toggleServiceStatus(this)" class="px-2.5 py-1 rounded-lg ${status === 'aktif' ? 'bg-rose-50 hover:bg-rose-100 text-rose-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700'} text-[10px] font-semibold transition-colors service-toggle-btn">
                            ${status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}
                        </button>
                    </div>
                </div>
            `;

            const list = document.getElementById('serviceList');
            if (list) list.prepend(newCard);

            if (typeof showToast === 'function') {
                showToast('Layanan Ditambahkan', `Layanan "${name}" berhasil ditambahkan ke katalog.`, 'success');
            }
        }

        closeServiceModal();
        updateSummaryStats();
    }

    // Bind Escape key listener untuk menutup modal layanan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('serviceFormModal');
            if (modal && modal.classList.contains('opacity-100')) {
                closeServiceModal();
            }
        }
    });

    // Inisialisasi hitungan ringkasan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateSummaryStats();
    });
</script>
@endpush

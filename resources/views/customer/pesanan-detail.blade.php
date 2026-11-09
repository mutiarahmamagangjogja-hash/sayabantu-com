@extends('layouts.customer')

@section('title', 'Status Suruhan #' . $kodePesanan . ' - SayaBantu.com')

{{-- =========================================================
     TOP AREA (HEADER DETAIL SURUHAN & STATUS)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('customer.partials.mobile-header')

    @include('customer.partials.page-header', [
        'judul' => 'Status & Rincian Suruhan',
        'deskripsi' => 'Pantau alur suruhan Anda dari negosiasi mitra hingga pekerjaan selesai.',
        'badge' => $statusPesanan,
        'badgeColor' => $badgeStatus,
        'kembali' => true,
        'kembaliUrl' => route('customer.dashboard') . '?tab=pesanan',
        'kembaliLabel' => 'Kembali ke Pesanan Saya',
    ])
</div>
@endsection

{{-- =========================================================
     CONTENT AREA: STATUS, 11 ALUR, NEGOSIASI & PEMBAYARAN
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-3.5">

    {{-- 1. KARTU IDENTITAS TIKET & STATUS UTAMA --}}
    <section class="card-sb p-4 space-y-3 border-2 border-slate-200/90 shadow-sm bg-white rounded-2xl">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Nomor Tiket Suruhan</span>
                <strong class="text-sm font-black text-slate-900 font-mono block mt-0.5">#{{ ltrim($kodePesanan, '#') }}</strong>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[9.5px] font-extrabold px-3 py-1 rounded-full whitespace-nowrap shrink-0 shadow-xs {{ $kelasStatus }}">
                <i class="fa-solid {{ $ikonStatus }} text-[8.5px]"></i> {{ $statusPesanan }}
            </span>
        </div>

        <div class="pt-2 border-t border-slate-100 flex items-start justify-between gap-2">
            <div>
                <h2 class="text-xs font-extrabold text-[#0E1D31]">{{ $namaLayanan }}</h2>
                <span class="text-[9.5px] text-sky-700 font-bold mt-0.5 inline-block">
                    Metode: {{ $metodeHarga === 'Pas' ? 'Harga Pas (Tarif Katalog)' : 'Nego Harga (Tawar-Menawar Chat)' }}
                </span>
            </div>
            <div class="text-right shrink-0" id="detailOrderHeaderBiayaBox">
                <span class="text-[8.5px] text-slate-400 block" id="detailOrderHeaderBiayaLabel">Nominal Pengajuan</span>
                <strong class="text-sm font-black text-[#173B67] block" id="detailOrderHeaderBiayaValue">Rp {{ $biayaPesanan }}</strong>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center text-xs">
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                <span class="block text-[9px] text-slate-400 font-medium">Jadwal Pengerjaan</span>
                <strong class="block mt-0.5 text-[10.5px] text-slate-900 truncate">{{ $jadwalPesanan }}</strong>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                <span class="block text-[9px] text-slate-400 font-medium">Batas Waktu Penugasan</span>
                <strong class="block mt-0.5 text-[10.5px] text-emerald-700 font-bold">{{ $dbOrder?->durasi_cari ?? request('durasi_cari', '1 Jam') }} (Hari Ini)</strong>
            </div>
        </div>
    </section>

    {{-- 2. KARTU KHUSUS STATUS ALUR (MENCARI MITRA DISEKITAR ALA GOJEK DENGAN BATAS 1 JAM) --}}
    @if(empty($realBids) && ($statusKode === 'menunggu_penawaran' || $statusKode === 'menunggu_mitra'))
    <section class="card-sb p-4 bg-gradient-to-b from-[#0E1D31] via-[#10243E] to-[#0A1626] border-2 border-emerald-500/40 rounded-2xl shadow-lg space-y-3.5 text-white overflow-hidden relative">
        <style>
        @keyframes sbRadarSweep {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes sbRadarRipple {
            0% { transform: scale(0.6); opacity: 0.85; }
            50% { transform: scale(1.3); opacity: 0.35; }
            100% { transform: scale(1.85); opacity: 0; }
        }
        .animate-radar-sweep {
            animation: sbRadarSweep 3.5s linear infinite;
            transform-origin: center center;
        }
        .animate-radar-ripple-1 {
            animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }
        .animate-radar-ripple-2 {
            animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            animation-delay: 0.85s;
        }
        .animate-radar-ripple-3 {
            animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            animation-delay: 1.7s;
        }
        </style>

        {{-- Ambient blur --}}
        <div class="absolute -top-16 -left-16 w-36 h-36 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -right-16 w-36 h-36 bg-sky-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-start justify-between relative z-10">
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 text-[9px] font-black uppercase tracking-wider">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Mencari Mitra di Sekitar
                </span>
                <h3 class="text-sm font-black text-white mt-1.5">Sistem Menghubungkan Mitra Terdekat</h3>
                <p id="pesananDetailRadarTicker" class="text-[10px] text-slate-300 mt-0.5">
                    Menyiarkan suruhan ke mitra aktif dalam radius 5 km...
                </p>
            </div>
            <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 shrink-0">
                Langkah 4: Menunggu Mitra
            </span>
        </div>

        {{-- RADAR ANIMATION (ALA GOJEK) --}}
        <div class="relative w-44 h-44 mx-auto my-1 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border border-emerald-500/25"></div>
            <div class="absolute w-32 h-32 rounded-full border border-emerald-500/30"></div>
            <div class="absolute w-16 h-16 rounded-full border border-emerald-500/40"></div>
            <div class="absolute inset-x-0 top-1/2 h-px bg-emerald-500/20"></div>
            <div class="absolute inset-y-0 left-1/2 w-px bg-emerald-500/20"></div>

            {{-- Ripple waves --}}
            <div class="absolute w-16 h-16 rounded-full border-2 border-emerald-400 animate-radar-ripple-1"></div>
            <div class="absolute w-16 h-16 rounded-full border-2 border-emerald-400 animate-radar-ripple-2"></div>
            <div class="absolute w-16 h-16 rounded-full border-2 border-emerald-400 animate-radar-ripple-3"></div>

            {{-- Radar beam --}}
            <div class="absolute inset-0 rounded-full pointer-events-none animate-radar-sweep" style="background: conic-gradient(from 0deg, rgba(16, 185, 129, 0.45) 0deg, rgba(16, 185, 129, 0.08) 60deg, transparent 90deg, transparent 360deg);"></div>

            {{-- Pin --}}
            <div class="relative z-20 flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/30">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <span class="text-[8px] font-bold text-emerald-300 bg-slate-900/90 px-1.5 py-0.2 rounded-full border border-emerald-500/30 mt-0.5">Lokasi Anda</span>
            </div>

            {{-- Floating Mitra Pins --}}
            <div class="absolute top-4 right-5 z-10 animate-bounce" style="animation-duration: 2.2s;">
                <div class="w-5 h-5 rounded-full bg-[#173B67] border border-amber-400 text-amber-300 flex items-center justify-center text-[9px] shadow">
                    <i class="fa-solid fa-motorcycle"></i>
                </div>
            </div>
            <div class="absolute bottom-5 left-5 z-10 animate-bounce" style="animation-duration: 2.6s; animation-delay: 0.5s;">
                <div class="w-5 h-5 rounded-full bg-[#173B67] border border-sky-400 text-sky-300 flex items-center justify-center text-[9px] shadow">
                    <i class="fa-solid fa-broom"></i>
                </div>
            </div>
        </div>

        {{-- COUNTDOWN TIMER CARD (SESUAI INPUT CUSTOMER) --}}
        <div class="p-3 rounded-2xl bg-slate-900/80 border border-emerald-500/30 text-center space-y-1 relative z-10">
            <div class="flex items-center justify-center gap-1.5 text-[9.5px] font-black text-amber-400 uppercase tracking-wider">
                <i class="fa-solid fa-stopwatch animate-pulse"></i> Batas Waktu Penerimaan Mitra ({{ $dbOrder?->durasi_cari ?? request('durasi_cari', '1 Jam') }})
            </div>
            <div id="pesananDetailRadarCountdown" class="font-mono text-2xl font-black text-amber-300 tracking-wider">
                59:59
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                <div id="pesananDetailRadarBar" class="bg-gradient-to-r from-emerald-400 to-amber-400 h-1.5 rounded-full transition-all duration-1000" style="width: 100%;"></div>
            </div>
            <p class="text-[9.5px] text-slate-300 pt-0.5 leading-snug">
                Pesanan <strong>Hari Ini</strong> disiarkan ke mitra terdekat dengan batas waktu {{ $dbOrder?->durasi_cari ?? request('durasi_cari', '1 Jam') }}. Mitra yang merespons pertama akan langsung ditugaskan.
            </p>
        </div>

        {{-- STATUS PEMBAYARAN TERKUNCI --}}
        <div class="p-2.5 rounded-xl bg-white/10 border border-white/10 space-y-1 text-slate-300 relative z-10">
            <div class="flex items-center justify-between text-[10px]">
                <span class="text-slate-300 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-amber-400"></i> Pembayaran Terkunci Sementara
                </span>
                <span class="font-bold text-amber-300 bg-amber-400/20 px-2 py-0.5 rounded-full border border-amber-400/30 text-[9px]">
                    Menunggu Mitra
                </span>
            </div>
            <p class="text-[9px] text-slate-400">
                Pembayaran otomatis dibuka segera setelah seorang mitra menerima dan mengonfirmasi kesediaan tugas.
            </p>
        </div>
    </section>

    {{-- 2.B KEPUTUSAN CUSTOMER: MEMBANDINGKAN DAN MEMILIH DARI BEBERAPA MITRA --}}
    @elseif(in_array($statusKode, ['menunggu_keputusan', 'nego', 'menunggu_penawaran', 'menunggu_mitra'], true))
    <section class="card-sb p-4 bg-gradient-to-br from-blue-50/90 to-sky-50/60 border-2 border-sky-300 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#173B67] text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-[#0E1D31]">Langkah 5: Bandingkan Profil &amp; Tentukan 1 Mitra</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-sky-200 text-[#0E1D31]">
                        Menunggu Keputusan Anda
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    Satu suruhan hanya dapat memiliki <strong>satu Mitra yang ditetapkan</strong>. Bandingkan penawaran harga, penilaian/rating, pengalaman, dan jadwal di bawah ini. Anda dapat menolak penawaran yang tidak sesuai dan tetap mempertimbangkan tawaran Mitra lain.
                </p>
            </div>
        </div>

        {{-- DAFTAR KARTU PENAWARAN MITRA YANG BERSAING --}}
        <div class="space-y-3" id="wrapperDaftarPenawaranMitra">

            {{-- WADAH DINAMIS UNTUK PENAWARAN NEGO REAL DARI MITRA --}}
            <div id="dynamicMitraBidsArea" class="space-y-3">
                @if(!empty($realBids))
                    @foreach($realBids as $bid)
                        @php
                            $mName = $bid['mitraName'] ?? 'Mitra';
                            $mInitials = strtoupper(substr($mName, 0, 2));
                            $mId = $bid['mitraId'] ?? $bid['id'] ?? 'mitra';
                            $mPrice = $bid['hargaFormatted'] ?? '—';
                            $mCleanId = preg_replace('/[^a-zA-Z0-9]/', '', $mId);
                            $isCustomerNegoWaiting = (($dbOrder?->nego_by ?? '') === 'customer');
                        @endphp
                        <div class="card-penawaran p-3.5 rounded-2xl bg-gradient-to-br {{ $isCustomerNegoWaiting ? 'from-sky-50/80 to-white border-2 border-sky-400' : 'from-amber-50/80 to-white border-2 border-amber-400' }} shadow-md space-y-3 transition-all ring-2 ring-amber-200/50" id="cardMitra_{{ $mCleanId }}">
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-amber-600 to-[#173B67] text-white font-extrabold text-xs flex items-center justify-center shadow-sm shrink-0 border-2 border-white">
                                        {{ $mInitials }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <h4 class="text-xs font-black text-slate-900">{{ $mName }}</h4>
                                            @if($isCustomerNegoWaiting)
                                                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-900 border border-sky-300 text-[8px] font-black uppercase flex items-center gap-1">
                                                    <i class="fa-solid fa-clock text-sky-600"></i> Tawaran Balik Terkirim
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[8px] font-black uppercase flex items-center gap-1 animate-pulse">
                                                    <i class="fa-solid fa-bolt text-amber-600"></i> Tawaran Nego Baru Masuk
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[9.5px] text-slate-500 mt-0.5">
                                            @if(!empty($bid['rating']))
                                                <span class="text-amber-500 font-bold">★ {{ $bid['rating'] }}</span>
                                                <span>({{ $bid['reviewCount'] ?? 0 }} ulasan)</span>
                                            @else
                                                <span>Belum ada ulasan</span>
                                            @endif
                                            <span>({{ $bid['tugasSelesai'] ?? 0 }} tugas selesai)</span>
                                            @if(!empty($bid['spesialis']))
                                                <span>•</span>
                                                <span class="text-slate-600 font-medium">{{ $bid['spesialis'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($isCustomerNegoWaiting)
                                        <span class="text-[8px] text-sky-800 font-bold block uppercase tracking-wider">Tawaran Anda</span>
                                        <strong class="text-sm font-black text-sky-700 block">Rp {{ $mPrice }}</strong>
                                        <span class="text-[8px] font-extrabold text-sky-900 bg-sky-100 px-1.5 py-0.5 rounded block mt-0.5 border border-sky-300">Menunggu Respon Mitra</span>
                                    @else
                                        <span class="text-[8px] text-amber-800 font-bold block uppercase tracking-wider">Tawaran Balik (Nego)</span>
                                        <strong class="text-sm font-black text-amber-700 block">Rp {{ $mPrice }}</strong>
                                        <span class="text-[8px] font-extrabold text-amber-900 bg-amber-100 px-1.5 py-0.5 rounded block mt-0.5 border border-amber-300">Menunggu Respon Anda</span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-white border border-amber-200/80 text-[9.5px] text-slate-600 space-y-1 shadow-xs">
                                <p class="flex items-center gap-1.5 font-medium text-slate-800">
                                    <i class="fa-regular fa-clock text-amber-600"></i>
                                    <span>Estimasi Waktu &amp; Durasi: <strong class="text-[#173B67]">{{ $bid['durasi'] ?? 'Sesuai Permintaan' }}</strong></span>
                                </p>
                                <p class="flex items-start gap-1.5">
                                    <i class="fa-solid fa-quote-left text-amber-400 mt-0.5"></i>
                                    <span class="italic text-slate-700 font-medium">"{{ $bid['catatan'] ?? 'Siap hadir tepat waktu dan memberikan pengerjaan terbaik bergaransi.' }}"</span>
                                </p>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-1 border-t border-amber-200/60">
                                <a
                                    href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($mName) }}"
                                    onclick="event.stopPropagation();"
                                    class="py-1.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 font-bold text-[9.5px] flex items-center gap-1 transition-colors"
                                >
                                    <i class="fa-solid fa-comments text-sky-600"></i> Chat Nego
                                </a>
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); tolakTawaranMitra('{{ $mId }}', '{{ $mName }}')"
                                        class="py-1.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9.5px] transition-all cursor-pointer"
                                    >
                                        <i class="fa-solid fa-xmark mr-0.5"></i> Tolak
                                    </button>
                                    @if($isCustomerNegoWaiting)
                                        <button
                                            type="button"
                                            onclick="event.stopPropagation(); bukaModalNegoBalikCustomer('{{ $mId }}', '{{ $mName }}', '{{ $mPrice }}')"
                                            class="py-1.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-[9.5px] transition-all cursor-pointer shadow-xs"
                                        >
                                            <i class="fa-solid fa-pen mr-0.5"></i> Ubah Tawar
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            onclick="event.stopPropagation(); bukaModalNegoBalikCustomer('{{ $mId }}', '{{ $mName }}', '{{ $mPrice }}')"
                                            class="py-1.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-[9.5px] transition-all cursor-pointer shadow-xs"
                                        >
                                            <i class="fa-solid fa-hand-holding-dollar mr-0.5"></i> Tawar Balik
                                        </button>
                                        <button
                                            type="button"
                                            onclick="event.stopPropagation(); pilihMitraDanSepakati('{{ $mId }}', '{{ $mName }}', '{{ $mPrice }}')"
                                            class="py-1.5 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] shadow-sm transition-all flex items-center gap-1 cursor-pointer active:scale-95"
                                        >
                                            <i class="fa-solid fa-check"></i> Sepakati Rp {{ $mPrice }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- STATUS SAAT BELUM ADA PENAWARAN MASUK DARI MITRA --}}
            <div id="emptyBidsState" class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-2 {{ !empty($realBids) ? 'hidden' : '' }}">
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-sm shadow-xs">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <p class="text-xs font-bold text-slate-800">Menunggu Penawaran Masuk dari Mitra</p>
                <p class="text-[9.5px] text-slate-500 max-w-xs mx-auto leading-relaxed">
                    Suruhan Anda sedang disiarkan ke mitra terdekat. Penawaran resmi dari mitra yang bersedia akan langsung muncul di sini.
                </p>
            </div>

        </div>

        {{-- STATUS PEMBAYARAN TERKUNCI --}}
        <div class="p-3 rounded-xl bg-white/90 border border-sky-200 space-y-2">
            <div class="flex items-center justify-between text-[10px]">
                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-amber-500"></i> Status Pembayaran:
                </span>
                <span class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                    Terkunci Sementara
                </span>
            </div>
            <p class="text-[9.5px] text-slate-500 leading-snug">
                Tahap pembayaran akan otomatis dibuka setelah Anda memilih 1 Mitra dan kesepakatan dikonfirmasi oleh Admin.
            </p>
            <button
                type="button"
                disabled
                class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed border border-slate-200"
            >
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>Bayar Sekarang (Terkunci Menunggu Pemilihan Mitra)</span>
            </button>
        </div>

        {{-- OPSI JIKA TIDAK ADA KESEPAKATAN: TOLAK SELURUHNYA --}}
        <div class="p-3 rounded-xl bg-slate-100/80 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-[10px]">
                <span class="text-slate-600 font-medium">Tidak ada penawaran yang sesuai?</span>
                <button
                    type="button"
                    onclick="tolakSemuaPenawaran()"
                    class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-[9.5px] transition-all cursor-pointer"
                >
                    <i class="fa-solid fa-ban mr-1"></i> Tolak Seluruh Penawaran
                </button>
            </div>
            <p class="text-[9px] text-slate-500 leading-tight">
                Jika kedua pihak tidak mencapai kesepakatan, suruhan tidak akan dilanjutkan ke tahap pembayaran dan Anda dapat mengajukan ulang suruhan.
            </p>
        </div>
    </section>

    {{-- 2.C MENUNGGU KONFIRMASI ADMIN SETELAH MITRA DIPILIH --}}
    @elseif($statusKode === 'menunggu_konfirmasi_admin')
    <section class="card-sb p-4 bg-gradient-to-br from-indigo-50/90 to-blue-50/50 border-2 border-indigo-300 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#173B67] text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-[#0E1D31]">Langkah 6: Menunggu Konfirmasi Admin</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-indigo-200 text-[#0E1D31]">
                        Pemeriksaan SOP &amp; Tarif
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    @if(($dbOrder?->agreed_by ?? '') === 'mitra')
                        Mitra <strong class="text-slate-900" id="labelMitraTerpilihReview">{{ $namaMitra }}</strong> telah menyepakati tawaran harga Anda sebesar <strong>Rp {{ $biayaPesanan }}</strong>. Kesepakatan ini telah diteruskan ke Admin untuk peninjauan sebelum pembayaran dibuka.
                    @else
                        Anda telah memilih <strong class="text-slate-900" id="labelMitraTerpilihReview">{{ $namaMitra }}</strong> dengan tarif kesepakatan <strong>Rp {{ $biayaPesanan }}</strong>. Kesepakatan ini telah diteruskan ke Admin untuk peninjauan sebelum pembayaran dibuka.
                    @endif
                </p>
            </div>
        </div>

        {{-- INFORMASI PENETAPAN TUNGGAL (NO DOUBLE BOOKING) --}}
        <div class="p-3 rounded-xl bg-white border border-indigo-200 text-[10px] space-y-2">
            <div class="flex items-center gap-2 text-indigo-950 font-bold">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>Mitra Tunggal Ditetapkan &amp; Mitra Lain Diberitahu</span>
            </div>
            <p class="text-[9.5px] text-slate-600 leading-relaxed">
                Hanya 1 Mitra yang ditetapkan untuk pekerjaan ini. Mitra lain yang sebelumnya mengajukan tawaran telah diberi informasi bahwa Customer telah memilih Mitra lain sehingga tidak terjadi penerimaan ganda.
            </p>
        </div>

        {{-- STATUS PEMBAYARAN TERKUNCI --}}
        <div class="p-3 rounded-xl bg-white/90 border border-indigo-200 space-y-2">
            <div class="flex items-center justify-between text-[10px]">
                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-amber-500"></i> Status Pembayaran:
                </span>
                <span class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                    Terkunci Menunggu Admin
                </span>
            </div>
            <p class="text-[9.5px] text-slate-500 leading-snug">
                Setelah Admin menyetujui kesepakatan kerja, tautan pembayaran akan langsung dibuka secara otomatis.
            </p>
            <button
                type="button"
                disabled
                class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed border border-slate-200"
            >
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>Bayar Sekarang (Terkunci Menunggu Konfirmasi Admin)</span>
            </button>
        </div>

        {{-- STATUS VERIFIKASI ADMIN --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-1">
            <p class="text-[10px] text-slate-700 font-semibold flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                <span>Menunggu Konfirmasi Tim Admin SayaBantu</span>
            </p>
            <p class="text-[9px] text-slate-500 leading-relaxed">
                Admin sedang memeriksa kesepakatan suruhan ini di halaman Admin. Setelah diverifikasi, tombol pembayaran akan otomatis aktif.
            </p>
        </div>

    </section>

    {{-- 2.D STATUS SETELAH DIKONFIRMASI ADMIN: SIAP BAYAR --}}
    @elseif($statusKode === 'siap_bayar' || $statusKode === 'menunggu_pembayaran')
    <section class="card-sb p-4 bg-gradient-to-br from-emerald-50/90 to-teal-50/50 border-2 border-emerald-400 shadow-sm space-y-3">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-emerald-950">Langkah 7: Siap Melakukan Pembayaran</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900">
                        Dikonfirmasi Admin
                    </span>
                </div>
                <p class="text-[10px] text-emerald-800 mt-1 leading-relaxed">
                    Kesepakatan dengan <strong>{{ $namaMitra }}</strong> (<strong>Rp {{ $biayaPesanan }}</strong>) telah dikonfirmasi Admin. Silakan selesaikan pembayaran agar jadwal terkonfirmasi dan Mitra bersiap hadir.
                </p>
            </div>
        </div>

        <a
            href="/customer/pembayaran/{{ ltrim($kodePesanan, '#') }}?layanan={{ urlencode($namaLayanan) }}&total={{ urlencode($biayaPesanan) }}&biaya={{ urlencode($biayaPesanan) }}&alamat={{ urlencode($alamatPesanan) }}&jadwal={{ urlencode($jadwalPesanan) }}&metode={{ urlencode($metodeHarga) }}&mitra={{ urlencode($namaMitra) }}&kesepakatan=disetujui&status=menunggu_pembayaran"
            onclick="if (typeof window.clearPesananDetailTimers === 'function') window.clearPesananDetailTimers();"
            class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs text-center shadow-md flex items-center justify-center gap-2 transition-all cursor-pointer active:scale-98"
        >
            <i class="fa-solid fa-credit-card"></i>
            <span>Lanjut ke Halaman Pembayaran (Rp {{ $biayaPesanan }})</span>
        </a>
    </section>

    {{-- 2.E PENANGANAN PENOLAKAN: KEDUA PIHAK TIDAK MENCAPAI KESEPAKATAN / SELURUH PENAWARAN DITOLAK --}}
    @elseif($statusKode === 'semua_ditolak' || $statusKode === 'dibatalkan')
    <section class="card-sb p-4 bg-gradient-to-br from-rose-50/90 to-red-50/50 border-2 border-rose-300 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-rose-950">Kesepakatan Tidak Tercapai</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-rose-200 text-rose-900">
                        Pekerjaan Dibatalkan
                    </span>
                </div>
                <p class="text-[10px] text-rose-800 mt-1 leading-relaxed">
                    Karena penawaran ditolak atau kedua pihak tidak mencapai kesepakatan, <strong>suruhan tidak dilanjutkan ke tahap konfirmasi dan pembayaran</strong>.
                </p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-white border border-rose-200 text-[10px] space-y-2 text-slate-600">
            <p class="font-semibold text-slate-800">
                <i class="fa-solid fa-circle-info text-rose-600 mr-1"></i> Apa langkah Anda selanjutnya?
            </p>
            <p class="text-[9.5px] leading-relaxed">
                Anda dapat mengajukan kembali suruhan ini dengan memperbarui rincian atau budget, atau memilih layanan lain dari katalog kami.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <button
                type="button"
                onclick="ajukanKembaliSuruhan()"
                class="py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-[10px] text-center shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
            >
                <i class="fa-solid fa-rotate-right"></i>
                <span>Ajukan Kembali Suruhan</span>
            </button>
            <a
                href="{{ route('customer.layanan') }}"
                class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[10px] text-center transition-colors flex items-center justify-center gap-1.5"
            >
                <i class="fa-solid fa-layer-group text-slate-500"></i>
                <span>Pilih Layanan Lain</span>
            </a>
        </div>
    </section>

    {{-- 2.D STATUS SETELAH BAYAR: JADWAL DIKONFIRMASI (TERJADWAL) --}}
    @elseif($statusKode === 'terjadwal')
    <section class="card-sb p-4 bg-gradient-to-br from-blue-50/90 to-sky-50/60 border-2 border-sky-400 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#173B67] text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-calendar-check text-sky-300"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-[#0E1D31]">Langkah 8: Pembayaran Lunas • Jadwal Dikonfirmasi</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Lunas
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    Pembayaran telah diterima dan aman di platform. Jadwal pengerjaan Anda telah resmi tercatat. Mitra akan bersiap hadir ke lokasi sesuai waktu berikut:
                </p>
            </div>
        </div>

        {{-- BOX TAMPILAN JADWAL MENONJOL --}}
        <div class="p-3 rounded-xl bg-white border border-sky-200 shadow-xs space-y-2">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2 text-xs">
                    <i class="fa-solid fa-clock text-sky-600"></i>
                    <span class="text-slate-500 font-medium">Waktu Kedatangan:</span>
                </div>
                <strong class="text-xs font-black text-[#173B67]">{{ $jadwalPesanan }}</strong>
            </div>
            <div class="flex items-start justify-between gap-2 text-[10px]">
                <div class="flex items-center gap-2 text-slate-500">
                    <i class="fa-solid fa-location-dot text-rose-500"></i>
                    <span>Lokasi Pekerjaan:</span>
                </div>
                <span class="text-slate-800 font-bold text-right truncate max-w-[200px]">{{ $alamatPesanan }}</span>
            </div>
            <div class="p-2 rounded-lg bg-sky-50/60 text-[9.5px] text-sky-900 leading-snug">
                <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i>
                Ketika Mitra mulai berangkat dari titik penugasan, status pesanan akan otomatis berganti ke <strong>Mitra OTW</strong>.
            </div>
        </div>

        <div>
            <a href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($namaMitra) }}" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-extrabold text-xs text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                <i class="fa-solid fa-comments"></i> Chat Aplikasi Bersama Mitra
            </a>
        </div>

    </section>

    {{-- 2.E STATUS MITRA OTW --}}
    @elseif($statusKode === 'otw')
    <section class="card-sb p-4 bg-gradient-to-br from-amber-50/90 to-orange-50/60 border-2 border-amber-400 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs animate-bounce">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-amber-950">Langkah 9: Mitra Sedang OTW ke Lokasi 🛵</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 animate-pulse">
                        Dalam Perjalanan
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    Mitra <strong>{{ $namaMitra }}</strong> telah berangkat dan sedang dalam perjalanan menuju alamat Anda. Mohon pastikan nomor telepon aktif untuk koordinasi.
                </p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-white border border-amber-200 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Estimasi Kedatangan:</span>
                <strong class="text-emerald-700 font-black">10 - 15 Menit</strong>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-100">
                <span>Alamat Tujuan:</span>
                <span class="font-bold text-slate-800 truncate max-w-[200px]">{{ $alamatPesanan }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($namaMitra) }}" class="py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-extrabold text-xs text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                <i class="fa-solid fa-comments"></i> Chat Aplikasi
            </a>
            <div class="py-2.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200 font-bold text-[10px] text-center flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-location-dot"></i> Menunggu Mitra tiba
            </div>
        </div>
    </section>

    {{-- 2.F STATUS SEDANG DIKERJAKAN --}}
    @elseif($statusKode === 'berjalan')
    <section class="card-sb p-4 bg-gradient-to-br from-emerald-50/90 to-teal-50/50 border-2 border-emerald-400 shadow-sm space-y-3">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-emerald-950">Langkah 10: Pekerjaan Sedang Dilakukan</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 animate-pulse">
                        Sedang Berjalan
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    Mitra telah tiba dan sedang mengerjakan suruhan di lokasi Anda. Pastikan pekerjaan telah diperiksa sebelum Anda mengonfirmasi selesai.
                </p>
            </div>
        </div>

        <div>
            <a href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($namaMitra) }}" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-extrabold text-xs text-center flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                <i class="fa-solid fa-comments"></i> Chat Aplikasi Bersama Mitra
            </a>
        </div>

        <p class="text-[9.5px] text-emerald-900 bg-white border border-emerald-200 rounded-xl p-2.5">
            Mitra akan memperbarui status pekerjaan dari dashboard Mitra setelah pekerjaan selesai.
        </p>
    </section>

    {{-- 2.G STATUS MENUNGGU KONFIRMASI CUSTOMER (PEKERJAAN SELESAI OLEH MITRA) --}}
    @elseif($statusKode === 'menunggu_konfirmasi_customer')
    <section class="card-sb p-4 bg-gradient-to-br from-sky-50/90 to-blue-50/50 border-2 border-sky-400 shadow-sm space-y-3.5">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#173B67] text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-[#0E1D31]">Langkah 10: Pekerjaan Dinyatakan Selesai</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-sky-200 text-[#173B67] animate-pulse">
                        Menunggu Persetujuan Anda
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    Mitra <strong>{{ $namaMitra }}</strong> telah menyatakan pekerjaan suruhan selesai. Silakan periksa hasil pekerjaan di lokasi Anda sebelum mengonfirmasi.
                </p>
            </div>
        </div>

        <div class="p-3 rounded-xl bg-white border border-sky-200 space-y-2 text-[10px]">
            <p class="font-bold text-slate-800">
                <i class="fa-solid fa-clipboard-check text-sky-600 mr-1"></i> Periksa Hasil Pekerjaan:
            </p>
            <p class="text-[9.5px] text-slate-600 leading-relaxed">
                Jika pekerjaan sudah sesuai kesepakatan, silakan tekan <strong>Selesaikan Pesanan</strong>. Jika ada ketidaksesuaian, Anda dapat berkomunikasi dengan Mitra atau mengajukan pengaduan.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <a
                href="{{ route('customer.pengaduan') }}?kode={{ ltrim($kodePesanan, '#') }}"
                class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] text-center transition-colors flex items-center justify-center gap-1.5"
            >
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                <span>Ajukan Keluhan</span>
            </a>
            <button
                type="button"
                onclick="konfirmasiPekerjaanSelesaiCustomer()"
                class="py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] text-center shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95"
            >
                <i class="fa-solid fa-check-double"></i>
                <span>Selesaikan Pesanan</span>
            </button>
        </div>
    </section>

    {{-- 2.I STATUS SELESAI & KARTU REVIEW / PENILAIAN CUSTOMER --}}
    @elseif(in_array($statusKode, ['menunggu_konfirmasi_admin_selesai', 'selesai'], true))
    <section class="card-sb p-4 bg-gradient-to-br from-amber-50/95 via-yellow-50/60 to-emerald-50/50 border-2 border-amber-300 shadow-sm space-y-3.5" id="cardReviewPesananSelesai">
        <div class="flex items-start gap-2.5">
            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-white flex items-center justify-center text-base shrink-0 shadow-xs">
                <i class="fa-solid fa-award"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-1">
                    <h3 class="text-xs font-black text-[#0E1D31]">Langkah 11: Pekerjaan Selesai &amp; Beri Ulasan</h3>
                    <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full {{ $statusKode === 'selesai' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }} shrink-0">
                        {{ $statusKode === 'selesai' ? 'Selesai' : 'Menunggu Admin' }}
                    </span>
                </div>
                <p class="text-[10px] text-slate-600 mt-1 leading-relaxed">
                    @if($statusKode === 'selesai')
                        Pekerjaan telah dikonfirmasi Customer dan Admin. Dana tetap berada di escrow sampai Admin meneruskannya ke Mitra. Berikan ulasan serta penilaian untuk <strong class="text-slate-900">{{ $namaMitra }}</strong>.
                    @else
                        Pekerjaan telah Anda konfirmasi selesai. Sambil menunggu verifikasi Admin, Anda dapat memberi ulasan kepada Mitra <strong class="text-slate-900">{{ $namaMitra }}</strong>.
                    @endif
                </p>
            </div>
        </div>

        @if($statusKode === 'menunggu_konfirmasi_admin_selesai')
            <div class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-[9.5px] text-indigo-900">
                Admin sedang memvalidasi penutupan pesanan. Dana tetap ditahan di escrow sampai Admin mengonfirmasi dan meneruskan dana.
            </div>
        @endif

        {{-- AREA KOTAK REVIEW (BELUM DIULAS VS SUDAH DIULAS) --}}
        <div id="wrapperReviewCustomerState" class="p-3.5 rounded-2xl bg-white/95 border border-amber-200/90 shadow-2xs space-y-3">
            {{-- VIEW KETIKA BELUM ADA REVIEW --}}
            <div id="viewReviewNotSubmitted" class="{{ ($dbOrder?->review_rating) ? 'hidden' : '' }} space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10.5px] font-black text-slate-900 block">Nilai Pelayanan Mitra:</span>
                        <span class="text-[9px] text-slate-500">Pilih bintang sesuai kepuasan Anda:</span>
                    </div>
                    <span id="labelQuickRatingStars" class="text-[10px] font-extrabold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                        Pilih bintang
                    </span>
                </div>

                {{-- BINTANG INTERAKTIF 1 - 5 --}}
                <div class="flex items-center justify-center gap-2 py-1.5 bg-slate-50/80 rounded-xl border border-slate-100" id="quickStarRatingGroup">
                    <button type="button" onclick="setQuickStarRating(1)" class="quick-star-btn text-2xl text-slate-300 hover:scale-125 transition-transform cursor-pointer" data-star="1">☆</button>
                    <button type="button" onclick="setQuickStarRating(2)" class="quick-star-btn text-2xl text-slate-300 hover:scale-125 transition-transform cursor-pointer" data-star="2">☆</button>
                    <button type="button" onclick="setQuickStarRating(3)" class="quick-star-btn text-2xl text-slate-300 hover:scale-125 transition-transform cursor-pointer" data-star="3">☆</button>
                    <button type="button" onclick="setQuickStarRating(4)" class="quick-star-btn text-2xl text-slate-300 hover:scale-125 transition-transform cursor-pointer" data-star="4">☆</button>
                    <button type="button" onclick="setQuickStarRating(5)" class="quick-star-btn text-2xl text-slate-300 hover:scale-125 transition-transform cursor-pointer" data-star="5">☆</button>
                </div>
                <input type="hidden" id="quickSelectedRating" value="">

                {{-- INPUT ULASAN CEPAT --}}
                <div class="space-y-1">
                    <textarea id="quickReviewComment" rows="2" placeholder="Tulis ulasan pengalaman Anda (misal: Mitra sangat tepat waktu, ramah, dan pengerjaannya sangat bersih &amp; rapi)..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#173B67] leading-relaxed resize-none"></textarea>
                </div>

                {{-- TOMBOL KIRIM ULASAN & RATING --}}
                <div class="pt-2.5">
                    <button
                        type="button"
                        id="btnSubmitReviewDirect"
                        onclick="kirimReviewPesananDirect()"
                        style="background-color: #173B67 !important; color: #ffffff !important; display: flex !important; visibility: visible !important;"
                        class="w-full py-3 px-4 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95"
                    >
                        <i class="fa-solid fa-paper-plane text-xs text-amber-300"></i>
                        <span id="btnSubmitReviewDirectText" style="color: #ffffff !important;">Kirim Ulasan &amp; Rating</span>
                    </button>
                </div>
            </div>

            {{-- VIEW KETIKA SUDAH DIULAS --}}
            <div id="viewReviewSubmitted" class="{{ ($dbOrder?->review_rating) ? '' : 'hidden' }} space-y-2">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div>
                            <strong class="text-xs font-black text-slate-900 block leading-tight">Ulasan Anda Telah Terkirim</strong>
                            <span class="text-[9px] text-emerald-700 font-bold" id="displayReviewTime">
                                {{ $dbOrder?->reviewed_at ? $dbOrder->reviewed_at->format('d M Y • H:i') . ' WIB' : 'Baru saja' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-amber-500 text-xs font-black flex items-center gap-1 justify-end" id="displayReviewStars">
                            @php
                                $rStars = (int)($dbOrder?->review_rating ?? 0);
                            @endphp
                            <span>{{ str_repeat('★', $rStars) }}{{ str_repeat('☆', 5 - $rStars) }}</span>
                            <span class="text-slate-800 text-[10.5px]">({{ $rStars }}.0)</span>
                        </div>
                        <span class="text-[8px] text-slate-400 block font-bold uppercase">Terverifikasi Publik</span>
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-700 italic leading-relaxed" id="displayReviewComment">
                    "{{ $dbOrder?->review_comment ?: 'Pengerjaan sangat memuaskan, mitra sangat ramah dan profesional.' }}"
                </div>

                <div class="pt-1 flex items-center justify-end">
                    <button type="button" onclick="editReviewPesanan()" class="text-[10px] font-bold text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Ubah Ulasan
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- 3. PROGRES 11 ALUR LENGKAP PLATFORM --}}
    <section class="card-sb p-4 space-y-3 border border-slate-200/90">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-route text-[#173B67]"></i> Tracker 11 Tahapan Suruhan
            </h3>
            <span class="text-[9px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                Langkah {{ $stepAktif }} dari 11
            </span>
        </div>

        <ol class="space-y-2.5 text-xs">
            {{-- Tahap 1 - 3: FORM & PENGAJUAN (SELESAI) --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[9px] font-black shadow-xs mt-0.5">
                    ✓
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold text-slate-900">1-3. Form Diisi, Metode Dipilih &amp; Pengajuan Terkirim</strong>
                    <span class="block text-[9.5px] text-emerald-700 font-semibold mt-0.5">
                        Metode: {{ $metodeHarga === 'Pas' ? 'Harga Pas' : 'Nego Harga' }} • Penawaran Rp {{ $biayaPesanan }}
                    </span>
                </div>
            </li>

            {{-- Tahap 4: MITRA MELIHAT KEBUTUHAN --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 4 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 4 ? '✓' : '4' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 4 ? 'text-slate-900' : 'text-slate-400' }}">
                        4. Mitra Melihat Kebutuhan Customer
                    </strong>
                    <span class="block text-[9.5px] text-slate-500 mt-0.5">
                        Pengajuan aktif di halaman Kebutuhan Mitra untuk diambil atau ditawar.
                    </span>
                </div>
            </li>

            {{-- Tahap 5: NEGOSIASI / SETUJU HARGA --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 5 ? ($stepAktif > 5 ? 'bg-emerald-500 text-white' : 'bg-sky-600 text-white animate-pulse') : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 5 ? '✓' : '5' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 5 ? 'text-slate-900' : 'text-slate-400' }}">
                        5. Negosiasi Tawaran &amp; Persetujuan Mitra
                    </strong>
                    <span class="block text-[9.5px] {{ $stepAktif === 5 ? 'text-sky-700 font-bold' : 'text-slate-500' }} mt-0.5">
                        {{ $metodeHarga === 'Nego' ? 'Customer mengajukan tawaran, Mitra yang berhak menyetujui kesepakatan harga via chat.' : 'Mitra menyetujui ketersediaan jadwal sesuai tarif katalog.' }}
                    </span>
                </div>
            </li>

            {{-- Tahap 6: KONFIRMASI ADMIN --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 6 ? ($stepAktif > 6 ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white') : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 6 ? '✓' : '6' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 6 ? 'text-slate-900' : 'text-slate-400' }}">
                        6. Menunggu Konfirmasi Admin
                    </strong>
                    <span class="block text-[9.5px] text-slate-500 mt-0.5">
                        Admin memverifikasi kesesuaian detail instruksi &amp; harga kesepakatan.
                    </span>
                </div>
            </li>

            {{-- Tahap 7: PEMBAYARAN --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 7 ? ($stepAktif > 7 ? 'bg-emerald-500 text-white' : 'bg-emerald-600 text-white font-black') : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 7 ? '✓' : '7' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 7 ? 'text-slate-900' : 'text-slate-400' }}">
                        7. Melakukan Pembayaran
                    </strong>
                    <span class="block text-[9.5px] text-slate-500 mt-0.5">
                        Customer menyelesaikan pembayaran via QRIS, Transfer Bank, atau E-Wallet (Dana ditahan platform).
                    </span>
                </div>
            </li>

            {{-- Tahap 8: JADWAL RESMI TERBIT --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 8 ? ($stepAktif > 8 ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white font-black animate-pulse') : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 8 ? '✓' : '8' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 8 ? 'text-slate-900' : 'text-slate-400' }}">
                        8. Jadwal Terbit &amp; Konfirmasi Waktu
                    </strong>
                    <span class="block text-[9.5px] {{ $stepAktif === 8 ? 'text-blue-800 font-bold' : 'text-slate-500' }} mt-0.5">
                        Pembayaran lunas. Jadwal resmi pengerjaan tercatat dan Mitra bersiap sesuai jam temu.
                    </span>
                </div>
            </li>

            {{-- Tahap 9: MITRA OTW --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 9 ? ($stepAktif > 9 ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white font-black animate-pulse') : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 9 ? '✓' : '9' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 9 ? 'text-slate-900' : 'text-slate-400' }}">
                        9. Mitra OTW ke Lokasi
                    </strong>
                    <span class="block text-[9.5px] {{ $stepAktif === 9 ? 'text-amber-800 font-bold' : 'text-slate-500' }} mt-0.5">
                        Mitra memulai perjalanan dari titik penugasan menuju alamat rumah Anda.
                    </span>
                </div>
            </li>

            {{-- Tahap 10: PENGERJAAN DI LOKASI --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 10 ? ($stepAktif > 10 ? 'bg-emerald-500 text-white' : 'bg-emerald-600 text-white font-black animate-pulse') : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif > 10 ? '✓' : '10' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 10 ? 'text-slate-900' : 'text-slate-400' }}">
                        10. Pelaksanaan Pekerjaan
                    </strong>
                    <span class="block text-[9.5px] text-slate-500 mt-0.5">
                        Mitra tiba di lokasi dan melaksanakan instruksi pekerjaan sampai tuntas.
                    </span>
                </div>
            </li>

            {{-- Tahap 11: SELESAI, PENCAIRAN & ULASAN --}}
            <li class="flex items-start gap-2.5">
                <span class="shrink-0 w-5 h-5 rounded-full {{ $stepAktif >= 11 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center text-[9px] font-black mt-0.5">
                    {{ $stepAktif >= 11 ? '✓' : '11' }}
                </span>
                <div class="min-w-0">
                    <strong class="block text-[11px] font-bold {{ $stepAktif >= 11 ? 'text-slate-900' : 'text-slate-400' }}">
                        11. Konfirmasi Selesai &amp; Ulasan
                    </strong>
                    <span class="block text-[9.5px] text-slate-500 mt-0.5">
                        Customer menyetujui hasil kerja, dana diteruskan ke saldo Mitra, dan pemberian rating.
                    </span>
                </div>
            </li>
        </ol>
    </section>

    {{-- 4. INFORMASI MITRA PENGERJA (TETAPKAN TUNGGAL 1 MITRA) --}}
    @if($statusKode === 'menunggu_penawaran' || $statusKode === 'menunggu_mitra')
    <section class="card-sb p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-user-clock text-[#173B67]"></i> Mitra Calon Pengerja
        </h3>
        <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-[10px] space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <strong class="text-amber-950 font-bold">Belum Ada Mitra yang Ditetapkan</strong>
            </div>
            <p class="text-slate-600 text-[9.5px] leading-relaxed">
                Satu suruhan hanya memiliki satu Mitra resmi yang akan ditetapkan. Sistem saat ini sedang menyiarkan pengajuan Anda ke mitra standby terdekat.
            </p>
        </div>
    </section>

    @elseif($statusKode === 'menunggu_keputusan' || $statusKode === 'nego')
    <section class="card-sb p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-users text-[#173B67]"></i> Status Penetapan Mitra
        </h3>
        <div class="p-3 rounded-xl bg-sky-50 border border-sky-200 text-[10px] space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                <strong class="text-[#0E1D31] font-bold">Dalam Proses Negosiasi &amp; Pemilihan Mitra</strong>
            </div>
            <p class="text-slate-600 text-[9.5px] leading-relaxed">
                Silakan tinjau penawaran Mitra di Langkah 5 di atas, sepakati harga, atau ajukan tawar balik. Setelah kedua pihak sepakat, kesepakatan akan diteruskan ke verifikasi Admin.
            </p>
        </div>
    </section>

    @elseif($statusKode === 'semua_ditolak' || $statusKode === 'dibatalkan')
    <section class="card-sb p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-user-xmark text-rose-600"></i> Mitra Calon Pengerja
        </h3>
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-[10px] space-y-1.5">
            <strong class="text-rose-950 font-bold">Tidak Ada Mitra Ditetapkan</strong>
            <p class="text-rose-700 text-[9.5px] leading-relaxed">
                Penawaran ditolak atau kesepakatan tidak tercapai. Tidak ada mitra yang ditugaskan untuk suruhan ini.
            </p>
        </div>
    </section>

    @else
    {{-- STATUS KETIKA 1 MITRA TELAH DITETAPKAN SECARA TUNGGAL --}}
    <section class="card-sb p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-user-check text-[#173B67]"></i> Mitra Resmi Ditetapkan (Tunggal)
            </h3>
            <span class="text-[8px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                1 Mitra Terpilih
            </span>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-11 h-11 shrink-0 rounded-full bg-gradient-to-br from-[#173B67] to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-sm" id="avatarMitraTerpilihDisplay">
                {{ $inisialMitra }}
            </div>
            <div class="min-w-0 flex-1">
                <strong class="block text-xs font-bold text-slate-900 truncate" id="namaMitraTerpilihDisplay">{{ $namaMitra }}</strong>
                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5">
                    @if($ratingMitra !== null)
                        <span class="text-amber-500 font-bold">★ {{ $ratingMitra }}</span>
                    @else
                        <span>Belum ada ulasan</span>
                    @endif
                    <span>•</span>
                    <span>{{ $jumlahTugasMitra }} Tugas Selesai</span>
                    <span>•</span>
                    <span class="text-emerald-700 font-bold">Mitra Terpilih</span>
                </div>
            </div>
        </div>

        @if(in_array($statusKode, ['menunggu_konfirmasi_admin_selesai', 'selesai'], true))
        <div class="pt-1 space-y-2">
            <button type="button" onclick="bukaModalReviewPesanan('{{ $kodePesanan }}', '{{ $namaLayanan }}', '{{ $namaMitra }}')" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black text-xs shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                <i class="fa-solid fa-star text-[11px]"></i>
                <span id="btnLabelBeriUlasanMitra">{{ ($dbOrder?->review_rating) ? 'Lihat / Ubah Ulasan Mitra' : 'Beri Ulasan & Rating Mitra' }}</span>
            </button>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($namaMitra) }}" class="py-2 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[10.5px] text-center transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-comments"></i> Chat Aplikasi
                </a>
                <a href="{{ route('customer.pengaduan') }}" class="py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10.5px] text-center transition-colors flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-headset text-slate-500"></i> Bantuan CS
                </a>
            </div>
        </div>
        @else
        <div class="grid grid-cols-2 gap-2 pt-1">
            <a href="{{ route('customer.dashboard') }}?tab=pesan&room={{ ltrim($kodePesanan, '#') }}&mitra={{ rawurlencode($namaMitra) }}" class="py-2 rounded-xl bg-[#173B67] hover:bg-[#1d4b83] text-white font-bold text-[10.5px] text-center transition-colors flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-comments"></i> Chat Aplikasi
            </a>
            <a href="{{ route('customer.pengaduan') }}" class="py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10.5px] text-center transition-colors flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-headset text-slate-500"></i> Bantuan CS
            </a>
        </div>
        @endif
    </section>
    @endif

    {{-- 5. DETAIL LOKASI & CATATAN --}}
    <section class="card-sb p-4 space-y-3">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-location-dot text-[#173B67]"></i> Lokasi &amp; Catatan Pekerjaan
        </h3>

        <div class="space-y-2 text-[10.5px]">
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-16 shrink-0 font-medium">Alamat</span>
                <span class="text-slate-800 leading-relaxed font-semibold">{{ $alamatPesanan }}</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-slate-400 w-16 shrink-0 font-medium">Catatan</span>
                <span class="text-slate-700 leading-relaxed italic bg-slate-50 p-2 rounded-lg border border-slate-200/70 flex-1">{{ $catatanPesanan }}</span>
            </div>
        </div>
    </section>

    {{-- 6. RINCIAN BIAYA --}}
    <section class="card-sb p-4 space-y-2.5">
        <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-receipt text-[#173B67]"></i> Rincian Biaya
        </h3>

        @foreach($rincianBiaya as $baris)
        <div class="flex items-center justify-between text-[10.5px]">
            <span class="text-slate-500">{{ $baris['label'] }}</span>
            <span class="font-bold {{ $baris['tebal'] ? 'text-slate-900' : 'text-slate-700' }}">{{ $baris['nilai'] }}</span>
        </div>
        @endforeach

        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-900 block">Total Pembayaran</span>
                <span class="text-[8.5px] text-slate-400">Garansi uang kembali SayaBantu</span>
            </div>
            <strong class="text-sm font-black text-[#173B67]">Rp {{ $biayaPesanan }}</strong>
        </div>
    </section>

    {{-- 7. TINDAKAN PEMBATALAN PESANAN (SKENARIO D) --}}
    @if(!in_array($statusKode, ['selesai', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer', 'semua_ditolak']))
    <section class="card-sb p-3.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs space-y-2 text-center">
        <p class="text-[9.5px] text-slate-500">
            Ingin membatalkan pengajuan suruhan ini?
        </p>
        <button
            type="button"
            onclick="handleCustomerCancelOrder()"
            class="w-full py-2.5 rounded-xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100/90 text-rose-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
        >
            <i class="fa-solid fa-ban text-[11px]"></i> Batalkan Pesanan Ini
        </button>
    </section>
    @elseif(in_array($statusKode, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan']))
    <section class="card-sb p-3.5 bg-rose-50 border border-rose-200 rounded-2xl shadow-xs text-center space-y-1">
        <div class="inline-flex items-center gap-1.5 text-xs font-extrabold text-rose-800">
            <i class="fa-solid fa-circle-xmark"></i> Pesanan Telah Dibatalkan
        </div>
        <p class="text-[10px] text-rose-700 leading-snug">
            @if($statusKode === 'cancelled_by_admin')
                Dibatalkan oleh Admin. Seluruh dana telah dialihkan ke rekening/metode pembayaran Anda.
            @else
                Pesanan dibatalkan atas permintaan Anda. Pengembalian dana (refund) diproses sesuai regulasi platform.
            @endif
        </p>
    </section>
    @endif

</main>

{{-- MODAL PEMBATALAN OLEH CUSTOMER SETELAH KESEPAKATAN (SKENARIO D) --}}
<div id="modalBatalCustomer" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl transition-all">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="text-xs font-bold text-rose-600 flex items-center gap-1.5">
                <i class="fa-solid fa-ban"></i>
                Batalkan Pesanan &amp; Pengembalian Dana
            </h4>
            <button type="button" onclick="closeModalBatalCustomer()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="mt-3 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[9px] text-amber-900 leading-relaxed space-y-1">
            <strong><i class="fa-solid fa-shield-halved text-amber-600 mr-1"></i> Perlindungan Rekber Escrow:</strong>
            <p>Karena kesepakatan mitra telah terbentuk, pembatalan akan memproses pengembalian dana penuh (100% Refund) ke saldo/rekening Anda dan memberitahu Mitra serta Admin.</p>
        </div>

        <div class="mt-3 space-y-2 text-xs">
            <label class="block text-[10px] font-bold text-slate-700">
                Alasan Pembatalan <span class="text-rose-500">*</span>
            </label>
            <div class="space-y-1.5 text-[9px] text-slate-700">
                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="custCancelReasonPreset" value="Perubahan rencana mendadak dari pemesan" checked onchange="toggleCustCustomCancelReason(false)">
                    <span>Perubahan rencana mendadak dari pemesan</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="custCancelReasonPreset" value="Mitra tidak dapat dihubungi atau terlambat" onchange="toggleCustCustomCancelReason(false)">
                    <span>Mitra terlambat / tidak dapat dihubungi</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="custCancelReasonPreset" value="Lokasi / rumah sedang tidak dapat diakses" onchange="toggleCustCustomCancelReason(false)">
                    <span>Lokasi / rumah tidak dapat diakses</span>
                </label>
                <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="custCancelReasonPreset" value="custom" onchange="toggleCustCustomCancelReason(true)">
                    <span>Tuliskan alasan lainnya...</span>
                </label>
            </div>

            <div id="custCustomCancelReasonWrap" class="hidden mt-2">
                <textarea
                    id="custCustomCancelReasonInput"
                    rows="2"
                    placeholder="Tuliskan alasan pembatalan..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-[9px] text-slate-700 outline-none focus:border-rose-400 focus:bg-white resize-none"
                ></textarea>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button
                type="button"
                onclick="closeModalBatalCustomer()"
                class="flex-1 rounded-xl bg-slate-100 py-2.5 text-[9.5px] font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer"
            >
                Kembali
            </button>
            <button
                type="button"
                id="btnSubmitCustCancelOrder"
                onclick="submitCustCancelOrderAction()"
                class="flex-1 rounded-xl bg-rose-600 py-2.5 text-[9.5px] font-bold text-white shadow-xs hover:bg-rose-700 transition cursor-pointer"
            >
                Konfirmasi Batal
            </button>
        </div>
    </div>
</div>

{{-- MODAL TAWAR BALIK (NEGO HARGA) CUSTOMER KE MITRA --}}
<div id="modalNegoBalikCustomer" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl transition-all space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="text-xs font-bold text-[#173B67] flex items-center gap-1.5">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                Ajukan Tawar Balik (Nego Harga)
            </h4>
            <button type="button" onclick="closeModalNegoBalikCustomer()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-2.5 rounded-xl bg-sky-50 border border-sky-200 text-[10px] text-sky-900 leading-relaxed space-y-1">
            <p>Mitra: <strong id="labelNegoMitraNama">Mitra</strong></p>
            <p>Harga Penawaran Mitra Saat Ini: <strong id="labelNegoHargaMitra" class="text-amber-700">Rp 50.000</strong></p>
        </div>

        <div class="space-y-1.5 text-xs">
            <label class="block font-bold text-slate-700 text-[10.5px]">Nominal Harga yang Anda Tawarkan (Rp):</label>
            <input
                type="number"
                id="inputNegoBalikCustomer"
                placeholder="Contoh: 45000"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:border-[#173B67] outline-none"
            >
            <p class="text-[9px] text-slate-400">Tawaran ini akan langsung dikirim ke Mitra untuk disetujui atau ditawar kembali.</p>
        </div>

        <div class="flex gap-2 pt-2 border-t border-slate-100">
            <button
                type="button"
                onclick="closeModalNegoBalikCustomer()"
                class="flex-1 rounded-xl bg-slate-100 py-2.5 text-[9.5px] font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer"
            >
                Batal
            </button>
            <button
                type="button"
                id="btnSubmitNegoBalik"
                onclick="submitNegoBalikCustomer()"
                class="flex-1 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] py-2.5 text-[9.5px] font-bold text-white shadow-xs transition cursor-pointer"
            >
                Kirim Tawaran Balik
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Inisialisasi awal timer tracking agar terhindar dari TypeError saat SPA execution
    window._sbPesananDetailTimers = window._sbPesananDetailTimers || [];
    window.clearPesananDetailTimers = function() {
        if (window._sbPesananDetailTimers && window._sbPesananDetailTimers.length) {
            window._sbPesananDetailTimers.forEach(id => clearInterval(id));
            window._sbPesananDetailTimers = [];
        }
    };

    // =====================================================
    // REVIEW & RATING PESANAN SELESAI (TAHAP 11)
    // =====================================================
    function setQuickStarRating(val) {
        const hiddenInput = document.getElementById('quickSelectedRating');
        if (hiddenInput) hiddenInput.value = val;

        const starBtns = document.querySelectorAll('.quick-star-btn');
        starBtns.forEach(btn => {
            const sVal = parseInt(btn.getAttribute('data-star') || '1');
            if (sVal <= val) {
                btn.className = 'quick-star-btn text-2xl text-amber-400 hover:scale-125 transition-transform cursor-pointer';
                btn.textContent = '★';
            } else {
                btn.className = 'quick-star-btn text-2xl text-slate-200 hover:scale-125 transition-transform cursor-pointer';
                btn.textContent = '☆';
            }
        });

        const labelEl = document.getElementById('labelQuickRatingStars');
        if (labelEl) {
            const mapLabel = {
                5: '5.0 (Sempurna & Luar Biasa)',
                4: '4.0 (Sangat Baik & Rapi)',
                3: '3.0 (Cukup Baik)',
                2: '2.0 (Kurang Memuaskan)',
                1: '1.0 (Kecewa)'
            };
            labelEl.textContent = mapLabel[val] || `${val}.0 Bintang`;
        }
    }
    window.setQuickStarRating = setQuickStarRating;

    async function kirimReviewPesananDirect() {
        const rating = parseInt(document.getElementById('quickSelectedRating')?.value || '', 10);
        const commentEl = document.getElementById('quickReviewComment');
        const comment = (commentEl ? commentEl.value : '').trim();
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        if (!Number.isInteger(rating) || rating < 1 || rating > 5) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pilih Rating', 'Pilih jumlah bintang sebelum mengirim ulasan.', 'warning');
            }
            return;
        }

        const btnSubmit = document.getElementById('btnSubmitReviewDirect') || document.querySelector('#viewReviewNotSubmitted button');
        const btnText = document.getElementById('btnSubmitReviewDirectText');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
            if (btnText) {
                btnText.textContent = 'Menyimpan Ulasan...';
            } else {
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Menyimpan...';
            }
        }

        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(kodeBersih)}/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ rating, comment })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Ulasan belum berhasil disimpan.');
            }

            try {
                const reviews = JSON.parse(localStorage.getItem('sb_order_reviews') || '{}');
                reviews[kodeBersih] = {
                    rating,
                    comment,
                    time: new Date().toISOString(),
                    service: '{{ $namaLayanan }}',
                    mitra: '{{ $namaMitra }}'
                };
                localStorage.setItem('sb_order_reviews', JSON.stringify(reviews));

                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    const list = JSON.parse(rawList);
                    const item = list.find(o => (o.code || o.id || '').replace('#', '') === kodeBersih);
                    if (item) {
                        item.review_rating = rating;
                        item.review_comment = comment;
                        item.status = 'selesai';
                        localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                    }
                }
            } catch(e) {}

            renderReviewPesananDetailStatus(rating, comment, 'Baru saja');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Ulasan Terkirim!', `Terima kasih atas penilaian bintang ${rating} Anda untuk Mitra {{ $namaMitra }}!`, 'success');
            }
        } catch (error) {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) {
                    btnText.textContent = 'Kirim Ulasan & Rating';
                } else {
                    btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> Kirim Ulasan';
                }
            }
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Ulasan Gagal Disimpan', error.message || 'Silakan coba lagi.', 'error');
            }
        }
    }
    window.kirimReviewPesananDirect = kirimReviewPesananDirect;

    function renderReviewPesananDetailStatus(rating, comment, timeStr) {
        const notSubView = document.getElementById('viewReviewNotSubmitted');
        const subView = document.getElementById('viewReviewSubmitted');
        if (notSubView && subView) {
            notSubView.classList.add('hidden');
            subView.classList.remove('hidden');

            const starsBox = document.getElementById('displayReviewStars');
            if (starsBox) {
                const starsText = '★'.repeat(rating) + '☆'.repeat(5 - rating);
                starsBox.innerHTML = `<span>${starsText}</span> <span class="text-slate-800 text-[10.5px]">(${rating}.0)</span>`;
            }

            const commentBox = document.getElementById('displayReviewComment');
            if (commentBox) {
                commentBox.textContent = `"${comment}"`;
            }

            const timeBox = document.getElementById('displayReviewTime');
            if (timeBox && timeStr) {
                timeBox.textContent = timeStr;
            }

            const btnMitraLabel = document.getElementById('btnLabelBeriUlasanMitra');
            if (btnMitraLabel) {
                btnMitraLabel.textContent = 'Lihat / Ubah Ulasan Mitra';
            }
        }
    }
    window.renderReviewPesananDetailStatus = renderReviewPesananDetailStatus;

    function editReviewPesanan() {
        const notSubView = document.getElementById('viewReviewNotSubmitted');
        const subView = document.getElementById('viewReviewSubmitted');
        if (notSubView && subView) {
            subView.classList.add('hidden');
            notSubView.classList.remove('hidden');
            const commentEl = document.getElementById('quickReviewComment');
            if (commentEl) {
                const curText = document.getElementById('displayReviewComment')?.textContent?.replace(/^"|"$/g, '') || '';
                if (curText) commentEl.value = curText;
                commentEl.focus();
            }
            const btnSubmit = document.getElementById('btnSubmitReviewDirect') || document.querySelector('#viewReviewNotSubmitted button');
            const btnText = document.getElementById('btnSubmitReviewDirectText');
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) {
                    btnText.textContent = 'Perbarui Ulasan & Rating';
                } else {
                    btnSubmit.innerHTML = '<i class="fa-solid fa-star text-[11px]"></i> <span>Perbarui Ulasan &amp; Rating</span>';
                }
            }
        }
    }
    window.editReviewPesanan = editReviewPesanan;

    function bukaModalReviewPesanan(orderCode, serviceName, mitraName) {
        const reviewCard = document.getElementById('cardReviewPesananSelesai');
        if (reviewCard) reviewCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    window.bukaModalReviewPesanan = bukaModalReviewPesanan;

    // 1. Customer Memilih Satu Mitra & Menyepakati Detail
    async function pilihMitraDanSepakati(mitraId, mitraName, budgetStr) {
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(kodeBersih)}/pilih-mitra`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    mitra_id: mitraId,
                    mitra_name: mitraName,
                    biaya: budgetStr
                })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Pilihan Mitra belum berhasil dikirim ke Admin.');
            }
        } catch (error) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pemilihan Mitra Gagal', error.message || 'Silakan coba lagi.', 'error');
            }
            return;
        }

        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) {
                const ord = JSON.parse(raw);
                ord.status = 'menunggu_konfirmasi_admin';
                ord.step = 6;
                ord.selectedMitraId = mitraId;
                ord.mitraName = mitraName;
                ord.budget = budgetStr;
                ord.biaya = budgetStr;
                ord.agreed_price = budgetStr;
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }

            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(item => {
                    if ((item.code || '').replace('#', '') === kodeBersih) {
                        item.status = 'menunggu_konfirmasi_admin';
                        item.step = 6;
                        item.selectedMitraId = mitraId;
                        item.mitraName = mitraName;
                        item.budget = budgetStr;
                        item.biaya = budgetStr;
                        item.agreed_price = budgetStr;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch(e) {}

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Mitra Dipilih!', `Anda memilih ${mitraName} dengan kesepakatan harga Rp ${budgetStr}. Diteruskan ke verifikasi Admin.`, 'success');
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', 'menunggu_konfirmasi_admin');
        url.searchParams.set('mitra', mitraName);
        url.searchParams.set('biaya', budgetStr);

        setTimeout(() => {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(url.toString());
            } else {
                window.location.href = url.toString();
            }
        }, 150);
    }
    window.pilihMitraDanSepakati = pilihMitraDanSepakati;

    // Customer Mengonfirmasi Pekerjaan Selesai (Tahap 10)
    async function konfirmasiPekerjaanSelesaiCustomer() {
        if (!confirm('Apakah Anda yakin pekerjaan telah selesai dan sesuai dengan kesepakatan?')) return;
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch(`/api/orders/${encodeURIComponent(kodeBersih)}/customer-selesai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Konfirmasi pekerjaan belum berhasil disimpan.');
            }
        } catch (error) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Konfirmasi Gagal', error.message || 'Silakan coba lagi.', 'error');
            }
            return;
        }

        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) {
                const ord = JSON.parse(raw);
                ord.status = 'menunggu_konfirmasi_admin_selesai';
                ord.step = 11;
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(item => {
                    if ((item.code || '').replace('#', '') === kodeBersih) {
                        item.status = 'menunggu_konfirmasi_admin_selesai';
                        item.step = 11;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch(e) {}

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Pekerjaan Dikonfirmasi!', 'Terima kasih telah mengonfirmasi penyelesaian pekerjaan. Pesanan diteruskan ke Admin untuk penutupan resmi.', 'success');
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', 'menunggu_konfirmasi_admin_selesai');
        setTimeout(() => {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(url.toString());
            } else {
                window.location.href = url.toString();
            }
        }, 150);
    }
    window.konfirmasiPekerjaanSelesaiCustomer = konfirmasiPekerjaanSelesaiCustomer;

    // 2. Customer Menolak Tawaran Salah Satu Mitra (Mempertimbangkan Tawaran Mitra Lain)
    function tolakTawaranMitra(mitraId, mitraName) {
        const cardId = 'cardMitra_' + mitraId.replace(/[^a-zA-Z0-9]/g, '');
        const cardEl = document.getElementById(cardId);
        if (cardEl) {
            cardEl.style.transition = 'all 0.3s ease';
            cardEl.style.opacity = '0.4';
            cardEl.style.pointerEvents = 'none';
            cardEl.classList.add('grayscale');
            const btnArea = cardEl.querySelector('.flex.items-center.gap-1\\.5:last-child');
            if (btnArea) {
                btnArea.innerHTML = '<span class="text-[9px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-lg"><i class="fa-solid fa-xmark mr-1"></i> Tawaran Ditolak</span>';
            }
        }

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Tawaran Ditolak', `Tawaran dari ${mitraName} telah Anda tolak. Anda tetap dapat mempertimbangkan penawaran dari Mitra lain.`, 'info');
        } else {
            alert(`Tawaran dari ${mitraName} telah Anda tolak. Anda tetap dapat mempertimbangkan penawaran dari Mitra lain.`);
        }

        // Cek jika seluruh penawaran yang tampil sudah ditolak
        const allCards = document.querySelectorAll('.card-penawaran');
        const activeCards = Array.from(allCards).filter(c => !c.classList.contains('grayscale'));
        if (activeCards.length === 0) {
            setTimeout(() => {
                if (confirm('Seluruh penawaran dari mitra telah ditolak. Ingin membatalkan kesepakatan dan mengajukan ulang suruhan?')) {
                    tolakSemuaPenawaran();
                }
            }, 400);
        }
    }
    window.tolakTawaranMitra = tolakTawaranMitra;

    // 3. Customer Menolak Seluruh Penawaran / Tidak Mencapai Kesepakatan
    function tolakSemuaPenawaran() {
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        try {
            // Hapus kartu pesanan aktif dari beranda
            localStorage.removeItem('sb_active_order');

            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(item => {
                    if ((item.code || '').replace('#', '') === kodeBersih) {
                        item.status = 'semua_ditolak';
                        item.step = 0;
                        item.selectedMitraId = null;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch(e) {}

        // Sinkronkan pembatalan ke server
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch(`/api/orders/${kodeBersih}/customer-cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ reason: 'Seluruh penawaran ditolak oleh customer.' })
            }).catch(() => {});
        } catch(e) {}

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Kesepakatan Tidak Tercapai', 'Seluruh penawaran ditolak. Suruhan tidak dilanjutkan ke tahap pembayaran.', 'warning');
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', 'semua_ditolak');
        setTimeout(() => {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(url.toString());
            } else {
                window.location.href = url.toString();
            }
        }, 150);
    }
    window.tolakSemuaPenawaran = tolakSemuaPenawaran;

    // 4. Customer Mengajukan Kembali Suruhan (Mengulang Siklus)
    function ajukanKembaliSuruhan() {
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        try {
            const raw = localStorage.getItem('sb_active_order');
            if (raw) {
                const ord = JSON.parse(raw);
                ord.status = 'menunggu_penawaran';
                ord.step = 4;
                ord.selectedMitraId = null;
                ord.rejectedMitras = [];
                localStorage.setItem('sb_active_order', JSON.stringify(ord));
            }

            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                list.forEach(item => {
                    if ((item.code || '').replace('#', '') === kodeBersih) {
                        item.status = 'menunggu_penawaran';
                        item.step = 4;
                        item.selectedMitraId = null;
                        item.rejectedMitras = [];
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
        } catch(e) {}

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Suruhan Diajukan Kembali', 'Suruhan kembali disiarkan ke mitra terdekat.', 'info');
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', 'menunggu_penawaran');
        setTimeout(() => {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(url.toString());
            } else {
                window.location.href = url.toString();
            }
        }, 150);
    }
    window.ajukanKembaliSuruhan = ajukanKembaliSuruhan;

    // =========================================================
    // 4.B TAWAR BALIK (NEGO HARGA) CUSTOMER KE MITRA
    // =========================================================
    function bukaModalNegoBalikCustomer(mitraId, mitraName, currentPrice) {
        const modal = document.getElementById('modalNegoBalikCustomer');
        const labelMitra = document.getElementById('labelNegoMitraNama');
        const labelHarga = document.getElementById('labelNegoHargaMitra');
        const inputHarga = document.getElementById('inputNegoBalikCustomer');
        window.__activeNegoMitraId = mitraId;
        window.__activeNegoMitraName = mitraName;

        if (labelMitra) labelMitra.innerText = mitraName;
        if (labelHarga) labelHarga.innerText = 'Rp ' + currentPrice;
        if (inputHarga) {
            inputHarga.value = currentPrice.replace(/[^0-9]/g, '');
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.bukaModalNegoBalikCustomer = bukaModalNegoBalikCustomer;

    function closeModalNegoBalikCustomer() {
        const modal = document.getElementById('modalNegoBalikCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeModalNegoBalikCustomer = closeModalNegoBalikCustomer;

    function submitNegoBalikCustomer() {
        const inputHarga = document.getElementById('inputNegoBalikCustomer');
        const rawVal = inputHarga ? parseInt(inputHarga.value.replace(/[^0-9]/g, '')) : 0;
        if (!rawVal || rawVal <= 0) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Nominal Belum Valid', 'Harap masukkan nominal tawar balik yang valid.', 'warning');
            }
            return;
        }

        const formatted = rawVal.toLocaleString('id-ID');
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const btn = document.getElementById('btnSubmitNegoBalik');
        if (btn) btn.disabled = true;

        fetch(`/api/orders/${kodeBersih}/customer-nego`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ harga: rawVal, catatan: 'Tawaran tawar balik dari Customer' })
        })
        .then(r => r.json())
        .then(data => {
            closeModalNegoBalikCustomer();
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Tawaran Balik Terkirim', `Anda menawar Rp ${formatted} ke Mitra ${window.__activeNegoMitraName || 'Mitra'}. Menunggu respon Mitra.`, 'info');
            } else {
                alert(`Tawaran balik sebesar Rp ${formatted} berhasil dikirim ke Mitra!`);
            }
            // Update localStorage
            try {
                const raw = localStorage.getItem('sb_active_order');
                if (raw) {
                    const ord = JSON.parse(raw);
                    ord.status = 'nego';
                    ord.step = 5;
                    ord.nego_price = formatted;
                    ord.biaya = formatted;
                    ord.budget = formatted;
                    ord.nego_by = 'customer';
                    localStorage.setItem('sb_active_order', JSON.stringify(ord));
                }

                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    const list = JSON.parse(rawList);
                    list.forEach(item => {
                        if ((item.code || item.id || '').replace('#', '') === kodeBersih) {
                            item.status = 'nego';
                            item.step = 5;
                            item.nego_price = formatted;
                            item.biaya = formatted;
                            item.budget = formatted;
                            item.nego_by = 'customer';
                        }
                    });
                    localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                }
            } catch(e) {}
            closeModalCustomer('modalNegoBalik');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Tawaran Terkirim', `Tawaran balik Rp ${formatted} berhasil dikirim ke Mitra!`, 'success');
            }
            if (typeof syncAndRenderMitraBids === 'function') {
                syncAndRenderMitraBids();
            }
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Kirim Tawaran Nego';
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Kirim Tawaran Nego';
            }
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Gagal', 'Gagal mengirim tawaran balik. Silakan coba lagi.', 'error');
            }
        });
    }
    window.submitNegoBalikCustomer = submitNegoBalikCustomer;

    // =========================================================
    // 5. PEMBATALAN PESANAN OLEH CUSTOMER (SKENARIO D)
    // =========================================================
    const currentStepAktif = {{ (int) $stepAktif }};
    const currentKodePesanan = '{{ ltrim($kodePesanan, "#") }}';

    function openModalBatalCustomer() {
        const modal = document.getElementById('modalBatalCustomer');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.openModalBatalCustomer = openModalBatalCustomer;

    function closeModalBatalCustomer() {
        const modal = document.getElementById('modalBatalCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeModalBatalCustomer = closeModalBatalCustomer;

    function toggleCustCustomCancelReason(showCustom) {
        const wrap = document.getElementById('custCustomCancelReasonWrap');
        if (wrap) {
            wrap.classList.toggle('hidden', !showCustom);
            if (showCustom) {
                document.getElementById('custCustomCancelReasonInput')?.focus();
            }
        }
    }
    window.toggleCustCustomCancelReason = toggleCustCustomCancelReason;

    async function handleCustomerCancelOrder() {
        // Sebelum kesepakatan (step 4-5) -> batalkan langsung
        if (currentStepAktif <= 5) {
            if (!confirm(`Batalkan pengajuan suruhan #${currentKodePesanan}?\n\nPengajuan akan ditarik dari antrean mitra SayaBantu.`)) {
                return;
            }
            await processCustomerCancelApi('Dibatalkan langsung oleh pelanggan sebelum kesepakatan mitra.');
        } else {
            // Setelah kesepakatan -> buka modal konfirmasi dengan alasan & refund
            openModalBatalCustomer();
        }
    }
    window.handleCustomerCancelOrder = handleCustomerCancelOrder;

    async function submitCustCancelOrderAction() {
        const btn = document.getElementById('btnSubmitCustCancelOrder');
        const selectedRadio = document.querySelector('input[name="custCancelReasonPreset"]:checked');
        let reason = selectedRadio ? selectedRadio.value : '';
        if (reason === 'custom') {
            reason = document.getElementById('custCustomCancelReasonInput')?.value.trim();
        }
        if (!reason) {
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Alasan Diperlukan', 'Silakan pilih atau isi alasan pembatalan.', 'warning');
            }
            return;
        }

        if (btn) btn.disabled = true;
        try {
            await processCustomerCancelApi(reason);
        } finally {
            if (btn) btn.disabled = false;
        }
    }
    window.submitCustCancelOrderAction = submitCustCancelOrderAction;

    async function processCustomerCancelApi(reason) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const res = await fetch(`/api/orders/${currentKodePesanan}/customer-cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason })
            });
            const data = await res.json();

            // Update localStorage & hapus kartu aktif dari beranda
            try {
                localStorage.removeItem('sb_active_order');

                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    const list = JSON.parse(rawList);
                    list.forEach(item => {
                        if ((item.code || '').replace('#', '') === currentKodePesanan) {
                            item.status = 'cancelled_by_customer';
                            item.cancellation_reason = reason;
                        }
                    });
                    localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                }
            } catch(e) {}

            closeModalBatalCustomer();

            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pesanan Dibatalkan', data.message || 'Pesanan berhasil dibatalkan.', 'info');
            } else {
                alert(data.message || 'Pesanan berhasil dibatalkan.');
            }

            const url = new URL(window.location.href);
            url.searchParams.set('status', 'cancelled_by_customer');
            setTimeout(() => {
                if (typeof spaNavigateTo === 'function') {
                    spaNavigateTo(url.toString());
                } else {
                    window.location.href = url.toString();
                }
            }, 200);
        } catch(err) {
            console.error(err);
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Error', 'Terjadi kesalahan saat memproses pembatalan pesanan.', 'error');
            }
        }
    }

    // =========================================================
    // SINKRONISASI & RENDER PENAWARAN NEGO REAL-TIME DARI MITRA
    // =========================================================
    function syncAndRenderMitraBids() {
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
        let currentOrder = null;
        let bids = [];

        try {
            const rawActive = localStorage.getItem('sb_active_order');
            if (rawActive) {
                const act = JSON.parse(rawActive);
                if ((act.code || act.id || '').replace('#', '') === kodeBersih) {
                    currentOrder = act;
                }
            }

            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                const found = list.find(o => (o.code || o.id || '').replace('#', '') === kodeBersih);
                if (found) {
                    currentOrder = { ...(currentOrder || {}), ...found };
                }
            }

            const rawBids = localStorage.getItem('sb_order_bids_' + kodeBersih);
            if (rawBids) {
                bids = JSON.parse(rawBids);
            }
        } catch(e) {}

        // Bersihkan data mock jika pernah tersimpan di localStorage
        bids = (bids || []).filter(b => {
            const id = b.mitraId || b.id || '';
            const name = b.mitraName || '';
            return !['MTR-001', 'MTR-002', 'MTR-003'].includes(id) &&
                   !name.includes('Bambang') &&
                   !name.includes('Hendra') &&
                   !name.includes('Slamet');
        });

        if (currentOrder && currentOrder.bids && Array.isArray(currentOrder.bids)) {
            currentOrder.bids.forEach(b => {
                const id = b.mitraId || b.id || '';
                const name = b.mitraName || '';
                const isMock = ['MTR-001', 'MTR-002', 'MTR-003'].includes(id) || name.includes('Bambang') || name.includes('Hendra') || name.includes('Slamet');
                if (!isMock && !bids.some(x => (x.mitraId || x.id) === (b.mitraId || b.id))) {
                    bids.unshift(b);
                }
            });
        }
        if (currentOrder && currentOrder.latest_bid && (!bids || bids.length === 0)) {
            const lb = currentOrder.latest_bid;
            const lbName = lb.mitraName || '';
            if (!lbName.includes('Bambang') && !lbName.includes('Hendra') && !lbName.includes('Slamet')) {
                bids.unshift(lb);
            }
        }

        // Ambil data terbaru langsung dari Database API (menjangkau antar-browser / Incognito)
        fetch(`/api/orders/${kodeBersih}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.success && data.order) {
                    const dbOrd = data.order;
                    const currentStatus = '{{ $statusKode }}';

                    // Perbarui data order aktif di localStorage jika ada perubahan
                    try {
                        const rawAct = localStorage.getItem('sb_active_order');
                        if (rawAct) {
                            const act = JSON.parse(rawAct);
                            if (act && (act.code || act.id || '').replace('#', '') === kodeBersih) {
                                if (dbOrd.biaya) act.budget = dbOrd.biaya;
                                if (dbOrd.mitraName) act.mitraName = dbOrd.mitraName;
                                localStorage.setItem('sb_active_order', JSON.stringify(act));
                            }
                        }
                    } catch(e) {}

                    if (dbOrd.mitraName) {
                        const serverBid = {
                            id: 'BID-' + (dbOrd.id || 'SERVER'),
                            mitraId: dbOrd.mitraId || null,
                            mitraName: dbOrd.mitraName,
                            mitraPhone: dbOrd.mitraPhone,
                            rating: dbOrd.bids?.[0]?.rating || null,
                            reviewCount: dbOrd.bids?.[0]?.reviewCount || 0,
                            tugasSelesai: dbOrd.bids?.[0]?.tugasSelesai || 0,
                            spesialis: dbOrd.bids?.[0]?.spesialis || null,
                            hargaFormatted: dbOrd.biaya || '—',
                            durasi: 'Sesuai Permintaan',
                            nego_by: dbOrd.nego_by || null,
                            agreed_by: dbOrd.agreed_by || null,
                            agreed_price: dbOrd.agreed_price || null
                        };
                        const existIdx = bids.findIndex(x => (x.mitraId || x.id) === serverBid.mitraId);
                        if (existIdx >= 0) {
                            bids[existIdx] = { ...bids[existIdx], ...serverBid };
                        } else {
                            bids.unshift(serverBid);
                        }
                    }

                    if (dbOrd.bids && Array.isArray(dbOrd.bids)) {
                        dbOrd.bids.forEach(sb => {
                            if (!bids.some(x => (x.mitraId || x.id) === (sb.mitraId || sb.id))) {
                                bids.unshift(sb);
                            }
                        });
                    }

                    renderBidsToContainer(bids, currentOrder || dbOrd);
                }
            })
            .catch(() => {
                if (bids && bids.length > 0) {
                    renderBidsToContainer(bids, currentOrder);
                }
            });

        if (bids && bids.length > 0) {
            renderBidsToContainer(bids, currentOrder);
        }
    }

    function renderBidsToContainer(bids, currentOrder) {
        const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';

        // 1. Tampilkan harga penawaran nego di Header Pesanan bila tersedia
        const labelEl = document.getElementById('detailOrderHeaderBiayaLabel');
        const valEl = document.getElementById('detailOrderHeaderBiayaValue');
        if (bids.length > 0 && bids[0].hargaFormatted) {
            const displayNego = (currentOrder && currentOrder.nego_price) ? currentOrder.nego_price : bids[0].hargaFormatted;
            const originalBudget = (currentOrder && (currentOrder.original_budget || currentOrder.budget)) ? (currentOrder.original_budget || currentOrder.budget) : '{{ $biayaPesanan }}';
            if (labelEl) {
                labelEl.innerHTML = `<span class="text-slate-400">Budget Awal: <span class="line-through">Rp ${originalBudget}</span></span>`;
            }
            if (valEl) {
                valEl.innerHTML = `<span class="text-amber-600">Rp ${displayNego}</span> <span class="text-[7.5px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 px-1 py-0.2 rounded uppercase ml-1 inline-block align-middle animate-pulse">Tawaran Nego</span>`;
            }
        }

        // 2. Render kartu penawaran ke dalam wadah dinamis
        const dynamicContainer = document.getElementById('dynamicMitraBidsArea');
        const emptyState = document.getElementById('emptyBidsState');

        // Filter ketat: HANYA penawaran mitra asli (bukan mock/demo)
        const validBids = (bids || []).filter(b => {
            const id = b.mitraId || b.id || '';
            const name = b.mitraName || '';
            return !['MTR-001', 'MTR-002', 'MTR-003'].includes(id) &&
                   !name.includes('Bambang') &&
                   !name.includes('Hendra') &&
                   !name.includes('Slamet');
        });

        const hasServerCards = dynamicContainer && dynamicContainer.querySelectorAll('.card-penawaran').length > 0;
        if (validBids.length > 0 || hasServerCards) {
            if (emptyState) emptyState.classList.add('hidden');
        } else {
            if (emptyState) emptyState.classList.remove('hidden');
        }

        if (!dynamicContainer || validBids.length === 0) {
            return;
        }

        dynamicContainer.innerHTML = validBids.map((bid, idx) => {
            const mId = bid.mitraId || bid.id || `bid-${idx}`;
            const mName = bid.mitraName || 'Mitra';
            const mInitials = mName.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() || 'M';
            const mRating = bid.rating;
            const mTugas = bid.tugasSelesai ?? 0;
            const mSpesialis = bid.spesialis;
            const mPriceFormatted = bid.hargaFormatted || (typeof bid.harga === 'number' ? bid.harga.toLocaleString('id-ID') : '—');
            const mDurasi = bid.durasi || 'Sesuai Permintaan';
            const mCatatan = bid.catatan;
            const cardDomId = 'cardMitra_' + mId.replace(/[^a-zA-Z0-9]/g, '');
            const isCustomerNegoWaiting = (bid.nego_by === 'customer' || (currentOrder && currentOrder.nego_by === 'customer'));

            return `
                <div class="card-penawaran p-3.5 rounded-2xl bg-gradient-to-br ${isCustomerNegoWaiting ? 'from-sky-50/80 to-white border-2 border-sky-400' : 'from-amber-50/70 to-white border-2 border-amber-400'} shadow-md space-y-3 transition-all ring-2 ring-amber-200/50" id="${cardDomId}">
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-amber-600 to-[#173B67] text-white font-extrabold text-xs flex items-center justify-center shadow-sm shrink-0 border-2 border-white">
                                ${mInitials}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-black text-slate-900">${mName}</h4>
                                    ${isCustomerNegoWaiting ? `
                                        <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-900 border border-sky-300 text-[8px] font-black uppercase flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-sky-600"></i> Tawaran Balik Terkirim
                                        </span>
                                    ` : `
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[8px] font-black uppercase flex items-center gap-1 animate-pulse">
                                            <i class="fa-solid fa-bolt text-amber-600"></i> Tawaran Nego Mitra
                                        </span>
                                    `}
                                </div>
                                <div class="flex items-center gap-1.5 text-[9.5px] text-slate-500 mt-0.5">
                                    ${mRating ? `<span class="text-amber-500 font-bold">★ ${mRating} (${bid.reviewCount || 0} ulasan)</span>` : '<span>Belum ada ulasan</span>'}
                                    <span>(${mTugas} tugas selesai)</span>
                                    ${mSpesialis ? `<span>•</span><span class="text-slate-600 font-medium">${mSpesialis}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            ${isCustomerNegoWaiting ? `
                                <span class="text-[8px] text-sky-800 font-bold block uppercase tracking-wider">Tawaran Anda</span>
                                <strong class="text-sm font-black text-sky-700 block">Rp ${mPriceFormatted}</strong>
                                <span class="text-[8px] font-extrabold text-sky-900 bg-sky-100 px-1.5 py-0.5 rounded block mt-0.5 border border-sky-300">Menunggu Respon Mitra</span>
                            ` : `
                                <span class="text-[8px] text-amber-800 font-bold block uppercase tracking-wider">Tawaran Balik (Nego)</span>
                                <strong class="text-sm font-black text-amber-700 block">Rp ${mPriceFormatted}</strong>
                                <span class="text-[8px] font-extrabold text-amber-900 bg-amber-100 px-1.5 py-0.5 rounded block mt-0.5 border border-amber-300">Menunggu Respon Anda</span>
                            `}
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-white border border-amber-200/80 text-[9.5px] text-slate-600 space-y-1 shadow-xs">
                        <p class="flex items-center gap-1.5 font-medium text-slate-800">
                            <i class="fa-regular fa-clock text-amber-600"></i>
                            <span>Estimasi Waktu &amp; Durasi: <strong class="text-[#173B67]">${mDurasi}</strong></span>
                        </p>
                        ${mCatatan ? `<p class="flex items-start gap-1.5">
                            <i class="fa-solid fa-quote-left text-amber-400 mt-0.5"></i>
                            <span class="italic text-slate-700 font-medium">"${mCatatan}"</span>
                        </p>` : ''}
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-amber-200/60">
                        <a
                            href="/customer/dashboard?tab=pesan&room=${kodeBersih}&mitra=${encodeURIComponent(mName)}"
                            onclick="event.stopPropagation();"
                            class="py-1.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 font-bold text-[9.5px] flex items-center gap-1 transition-colors"
                        >
                            <i class="fa-solid fa-comments text-sky-600"></i> Chat Nego
                        </a>
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                onclick="event.stopPropagation(); tolakTawaranMitra('${mId}', '${mName}')"
                                class="py-1.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[9.5px] transition-all cursor-pointer"
                            >
                                <i class="fa-solid fa-xmark mr-0.5"></i> Tolak
                            </button>
                            ${isCustomerNegoWaiting ? `
                                <button
                                    type="button"
                                    onclick="event.stopPropagation(); bukaModalNegoBalikCustomer('${mId}', '${mName}', '${mPriceFormatted}')"
                                    class="py-1.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-[9.5px] transition-all cursor-pointer shadow-xs"
                                >
                                    <i class="fa-solid fa-pen mr-0.5"></i> Ubah Tawar
                                </button>
                            ` : `
                                <button
                                    type="button"
                                    onclick="event.stopPropagation(); bukaModalNegoBalikCustomer('${mId}', '${mName}', '${mPriceFormatted}')"
                                    class="py-1.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-[9.5px] transition-all cursor-pointer shadow-xs"
                                >
                                    <i class="fa-solid fa-hand-holding-dollar mr-0.5"></i> Tawar Balik
                                </button>
                                <button
                                    type="button"
                                    onclick="event.stopPropagation(); pilihMitraDanSepakati('${mId}', '${mName}', '${mPriceFormatted}')"
                                    class="py-1.5 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] shadow-sm transition-all flex items-center gap-1 cursor-pointer active:scale-95"
                                >
                                    <i class="fa-solid fa-check"></i> Sepakati Rp ${mPriceFormatted}
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    window.syncAndRenderMitraBids = syncAndRenderMitraBids;

    // Auto-sync status di localStorage agar selaras dengan data resmi server
    function initPesananDetailPage() {
        try {
            if (!window.location.pathname.includes('/customer/pesanan/')) return;
            const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
            const currentStatus = '{{ $statusKode }}';
            const rawList = localStorage.getItem('sb_customer_orders');
            if (rawList) {
                const list = JSON.parse(rawList);
                const found = list.find(item => (item.code || '').replace('#', '') === kodeBersih);
                if (found && found.status !== currentStatus) {
                    found.status = currentStatus;
                    localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                }
            }
            const rawAct = localStorage.getItem('sb_active_order');
            if (rawAct) {
                const act = JSON.parse(rawAct);
                if (act && (act.code || act.id || '').replace('#', '') === kodeBersih && act.status !== currentStatus) {
                    act.status = currentStatus;
                    localStorage.setItem('sb_active_order', JSON.stringify(act));
                }
            }
        } catch(e) {}

        // Sinkronisasi status ulasan jika sudah ada di localStorage
        try {
            const reviews = JSON.parse(localStorage.getItem('sb_order_reviews') || '{}');
            const savedReview = reviews[kodeBersih];
            if (savedReview && Number.isInteger(Number(savedReview.rating)) && typeof renderReviewPesananDetailStatus === 'function') {
                renderReviewPesananDetailStatus(Number(savedReview.rating), savedReview.comment || '', 'Tersimpan');
            }
        } catch(e) {}

        syncAndRenderMitraBids();

        // Timer management agar tidak menumpuk atau menarik user saat sudah di halaman pembayaran
        window._sbPesananDetailTimers = window._sbPesananDetailTimers || [];
        window.clearPesananDetailTimers();
        window._sbIsNavigatingStatus = false;

        // Polling status real-time dari server DB (jika Mitra atau Admin mengubah status order)
        const checkServerStatus = () => {
            // Jika user sudah berada di halaman lain (mis. pembayaran), hentikan timer segera
            if (!window.location.pathname.includes('/customer/pesanan/')) {
                window.clearPesananDetailTimers();
                return;
            }

            if (window._sbIsNavigatingStatus) {
                return;
            }

            const kodeBersih = '{{ ltrim($kodePesanan, "#") }}';
            const currentStatus = '{{ $statusKode }}';
            fetch(`/api/orders/${kodeBersih}`)
                .then(r => r.json())
                .then(data => {
                    // Validasi kembali lokasi pathname sebelum memproses
                    if (!window.location.pathname.includes('/customer/pesanan/')) {
                        window.clearPesananDetailTimers();
                        return;
                    }

                    if (data && data.success && data.order && data.order.status) {
                        const srvStatus = data.order.status;
                        if (srvStatus !== currentStatus && !window._sbIsNavigatingStatus) {
                            window._sbIsNavigatingStatus = true;
                            window.clearPesananDetailTimers();

                            try {
                                const cleanTarget = kodeBersih.replace(/[^0-9A-Z]/gi, '');
                                const numTarget = kodeBersih.replace(/[^0-9]/g, '');
                                const isMatchOrder = (o) => {
                                    if (!o) return false;
                                    const oc = String(o.code || o.id || '').replace(/[^0-9A-Z]/gi, '');
                                    const on = String(o.code || o.id || '').replace(/[^0-9]/g, '');
                                    return (cleanTarget && (oc.includes(cleanTarget) || cleanTarget.includes(oc))) || (numTarget && on === numTarget) || o.id == kodeBersih || o.code === kodeBersih;
                                };

                                const rawAct = localStorage.getItem('sb_active_order');
                                if (rawAct) {
                                    const act = JSON.parse(rawAct);
                                    if (isMatchOrder(act)) {
                                        if (['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin', 'semua_ditolak', 'selesai'].includes(srvStatus)) {
                                            localStorage.removeItem('sb_active_order');
                                        } else {
                                            act.status = srvStatus;
                                            if (data.order.step) act.step = data.order.step;
                                            if (data.order.mitra_name || data.order.mitraName) act.mitraName = data.order.mitra_name || data.order.mitraName;
                                            if (data.order.agreed_price) act.agreed_price = data.order.agreed_price;
                                            localStorage.setItem('sb_active_order', JSON.stringify(act));
                                        }
                                    }
                                }
                                if (['cancelled_by_customer', 'dibatalkan', 'cancelled_by_admin', 'semua_ditolak', 'selesai'].includes(srvStatus)) {
                                    try {
                                        const rawBayar = localStorage.getItem('sb_customer_pembayaran_selesai') || '{}';
                                        const daftarBayar = JSON.parse(rawBayar);
                                        const cb = kodeBersih.replace('#', '').trim();
                                        if (daftarBayar[cb] || daftarBayar['#' + cb]) {
                                            delete daftarBayar[cb];
                                            delete daftarBayar['#' + cb];
                                            localStorage.setItem('sb_customer_pembayaran_selesai', JSON.stringify(daftarBayar));
                                        }
                                    } catch(e) {}
                                }
                                const rawList = localStorage.getItem('sb_customer_orders');
                                if (rawList) {
                                    let list = JSON.parse(rawList);
                                    if (Array.isArray(list)) {
                                        const found = list.find(isMatchOrder);
                                        if (found) {
                                            found.status = srvStatus;
                                            if (data.order.step) found.step = data.order.step;
                                            if (data.order.mitra_name || data.order.mitraName) found.mitraName = data.order.mitra_name || data.order.mitraName;
                                            if (data.order.agreed_price) found.agreed_price = data.order.agreed_price;
                                            localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                                        }
                                    }
                                }
                            } catch(e) {}

                            const targetUrl = new URL(`/customer/pesanan/${encodeURIComponent(kodeBersih)}`, window.location.origin);
                            targetUrl.searchParams.set('status', srvStatus);
                            if (data.order.biaya) targetUrl.searchParams.set('biaya', data.order.biaya);
                            if (data.order.mitraName || data.order.mitra_name) {
                                targetUrl.searchParams.set('mitra', data.order.mitraName || data.order.mitra_name);
                            }
                            if (data.order.layanan) targetUrl.searchParams.set('layanan', data.order.layanan);
                            if (data.order.metode) targetUrl.searchParams.set('metode', data.order.metode);
                            if (data.order.jadwal) targetUrl.searchParams.set('jadwal', data.order.jadwal);
                            if (data.order.alamat) targetUrl.searchParams.set('alamat', data.order.alamat);
                            if (data.order.note) targetUrl.searchParams.set('note', data.order.note);
                            if (typeof window.invalidateCustomerCache === 'function') {
                                window.invalidateCustomerCache();
                            }

                            setTimeout(() => { window._sbIsNavigatingStatus = false; }, 6000);

                            if (typeof spaNavigateTo === 'function') {
                                spaNavigateTo(targetUrl.href, true, true);
                            } else {
                                window.location.replace(targetUrl.href);
                            }
                        }
                    }
                })
                .catch(() => {});
        };

        window._sbPesananDetailTimers.push(setInterval(checkServerStatus, 2500));
        window._sbPesananDetailTimers.push(setInterval(() => {
            if (!window.location.pathname.includes('/customer/pesanan/')) {
                window.clearPesananDetailTimers();
                return;
            }
            syncAndRenderMitraBids();
        }, 3000));

        const radarTimerEl = document.getElementById('pesananDetailRadarCountdown');
        const radarBarEl = document.getElementById('pesananDetailRadarBar');
        if (radarTimerEl) {
            @php
                $durasiMenitVal = (int) ($dbOrder?->durasi_menit ?? request('durasi_menit', 60));
                if ($durasiMenitVal <= 0) $durasiMenitVal = 60;
                $durasiLabelVal = $dbOrder?->durasi_cari ?? request('durasi_cari', '1 Jam');
            @endphp
            const orderCreatedMs = {{ $dbOrder?->created_at ? $dbOrder->created_at->timestamp * 1000 : 'Date.now()' }};
            const durationSec = {{ $durasiMenitVal * 60 }};
            const durationMs = durationSec * 1000;
            const updateRadarTimer = () => {
                const now = Date.now();
                const elapsed = now - orderCreatedMs;
                const remainingSec = Math.max(0, Math.floor((durationMs - elapsed) / 1000));
                const mins = Math.floor(remainingSec / 60);
                const secs = remainingSec % 60;
                const strMin = mins < 10 ? '0' + mins : mins;
                const strSec = secs < 10 ? '0' + secs : secs;
                radarTimerEl.innerText = `${strMin}:${strSec}`;
                if (radarBarEl) {
                    const pct = Math.max(0, (remainingSec / durationSec) * 100);
                    radarBarEl.style.width = pct + '%';
                }
            };
            updateRadarTimer();
            window._sbPesananDetailTimers.push(setInterval(updateRadarTimer, 1000));
        }

        const detailTickerEl = document.getElementById('pesananDetailRadarTicker');
        if (detailTickerEl) {
            const msgs = [
                'Menyiarkan suruhan ke mitra aktif dalam radius 5 km...',
                'Mitra terdekat sedang memeriksa detail penugasan...',
                'Menunggu konfirmasi kesediaan dari mitra...',
                'Batas waktu pencarian ' + @json($durasiLabelVal) + ' sesuai permintaan Anda...'
            ];
            let mIdx = 0;
            window._sbPesananDetailTimers.push(setInterval(() => {
                mIdx = (mIdx + 1) % msgs.length;
                detailTickerEl.style.opacity = '0';
                setTimeout(() => {
                    detailTickerEl.innerText = msgs[mIdx];
                    detailTickerEl.style.opacity = '1';
                }, 200);
            }, 3200));
        }
    }
    window.initPesananDetailPage = initPesananDetailPage;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPesananDetailPage);
    } else {
        initPesananDetailPage();
    }

    // Real-time listener dari Mitra di tab atau window lain (terpasang satu kali)
    if (!window._sbPesananDetailStorageBound) {
        window._sbPesananDetailStorageBound = true;
        window.addEventListener('storage', function(e) {
            if (!e.key || e.key === 'sb_customer_orders' || e.key === 'sb_active_order' || e.key.includes('sb_order_bids_')) {
                if (window.location.pathname.includes('/customer/pesanan/')) {
                    if (typeof syncAndRenderMitraBids === 'function') {
                        syncAndRenderMitraBids();
                    }
                }
            }
        });
        window.addEventListener('sb_order_bid_submitted', function() {
            if (window.location.pathname.includes('/customer/pesanan/')) {
                if (typeof syncAndRenderMitraBids === 'function') {
                    syncAndRenderMitraBids();
                }
            }
        });
    }
</script>
@endpush
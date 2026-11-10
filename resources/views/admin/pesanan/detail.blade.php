@extends('layouts.admin')

@php
    $rawId = (string) ($order['id'] ?? $id);
    $cleanOrderId = ltrim($rawId, '#');
    $displayOrderCode = str_starts_with($rawId, '#')
        ? $rawId
        : (str_starts_with($rawId, 'SB') ? '#'.$rawId : '#SB'.$rawId);
    $status = $order['status'] ?? 'Diproses';
    $workflowStatus = $order['workflow_status'] ?? null;
    $paymentStatus = $order['payment_status'] ?? 'menunggu';
    $isPaid = ($paymentStatus === 'terkonfirmasi');
    $isRefunded = ($paymentStatus === 'dikembalikan');
    $isPaymentPending = ! $isPaid && ! $isRefunded && ($order['payment_submitted'] ?? false);
    $isConfirmed = ($status === 'Dikonfirmasi');
    $isConflict = ($status === 'Konflik');
@endphp

@section('title', 'Detail Pesanan ' . $displayOrderCode . ' - SayaBantu.com')
@section('back-url', route('admin.pesanan'))
@section('back-text', 'Kembali ke Pesanan')
@section('page-title', 'Detail Pesanan')
@section('page-subtitle', 'Informasi dan status pemesanan layanan')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}
@section('hero')
<section class="relative z-10 px-5 pb-5 pt-1">
    <div class="flex items-start justify-between gap-3 p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div>
            <p class="text-[9px] font-medium text-white/60">
                Nomor Pesanan
            </p>
            <h2 class="mt-1 text-[20px] font-bold tracking-tight text-white flex items-center gap-2">
                {{ $displayOrderCode }}
                <span class="text-[10px] font-normal text-white/50">• {{ $order['category'] ?? 'Servis Rumah' }}</span>
            </h2>
        </div>

        <div id="orderHeroStatusBadgeContainer">
            @if($isConflict)
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-red-300/40 bg-red-500/20 px-3 py-1 text-[8.5px] font-semibold text-red-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5 animate-pulse"></span>
                    Konflik
                </span>
            @elseif($isConfirmed)
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-emerald-300/40 bg-emerald-500/20 px-3 py-1 text-[8.5px] font-semibold text-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                    Dikonfirmasi
                </span>
            @elseif($status === 'Selesai')
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-green-300/40 bg-green-500/20 px-3 py-1 text-[8.5px] font-semibold text-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5"></span>
                    Selesai
                </span>
            @elseif($status === 'Menunggu')
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-yellow-300/40 bg-yellow-400/20 px-3 py-1 text-[8.5px] font-semibold text-yellow-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 mr-1.5 animate-pulse"></span>
                    Menunggu
                </span>
            @elseif($isRefunded)
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-red-300/40 bg-red-500/20 px-3 py-1 text-[8.5px] font-semibold text-red-200">
                    Dana Dikembalikan
                </span>
            @else
                <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-amber-300/30 bg-amber-400/15 px-3 py-1 text-[8.5px] font-semibold text-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                    Diproses
                </span>
            @endif
        </div>
    </div>
</section>
@endsection

{{-- =========================================================
     CONTENT DETAIL
========================================================= --}}
@section('content')

<div class="detail-main space-y-4">

    {{-- BANNER PERINGATAN KONFLIK (Hanya muncul jika pesanan berstatus Konflik) --}}
    <div id="conflictAlertBanner" class="{{ $isConflict ? '' : 'hidden' }} rounded-[18px] border border-red-200 bg-red-50/90 p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-[11px] font-bold text-red-800">
                        Pesanan Memerlukan Mediasi Konflik
                    </h4>
                    <span class="text-[8px] font-medium text-red-500" id="conflictTimeLabel">
                        {{ $order['conflict_time'] ?? 'Dilaporkan' }}
                    </span>
                </div>
                <p class="mt-1 text-[9.5px] text-red-700 leading-relaxed" id="conflictReasonLabel">
                    {{ $order['conflict_reason'] ?? 'Pesanan ini sedang dalam pemantauan admin karena adanya perselisihan pekerjaan/pembayaran.' }}
                </p>
                <div class="mt-2.5 flex items-center gap-2">
                    <button
                        type="button"
                        onclick="openResolveConflictModal()"
                        class="rounded-lg bg-red-600 px-3 py-1.5 text-[8.5px] font-bold text-white shadow-xs hover:bg-red-700 transition active:scale-95 flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-check"></i>
                        Selesaikan Mediasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- BANNER PEMBERITAHUAN PEKERJAAN SELESAI (PEMBERITAHUAN DETAIL PESANAN ADMIN) --}}
    @php
        $isAwaitingAdminCompletion = ($workflowStatus === 'menunggu_konfirmasi_admin_selesai');
        $isJobFinished = ($status === 'Selesai' || $workflowStatus === 'selesai');
        $isCompletionNotified = $isAwaitingAdminCompletion || $isJobFinished;
    @endphp
    <div id="completionAlertBanner" class="{{ $isCompletionNotified ? '' : 'hidden' }} rounded-[18px] border {{ $isJobFinished ? 'border-emerald-300 bg-emerald-50/95' : 'border-indigo-300 bg-gradient-to-r from-indigo-50/95 via-sky-50/90 to-blue-50/90' }} p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div id="completionNoticeIcon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl {{ $isJobFinished ? 'bg-emerald-600 text-white' : 'bg-gradient-to-br from-indigo-600 to-sky-600 text-white animate-pulse' }} text-sm font-bold shadow-xs">
                <i class="fa-solid {{ $isJobFinished ? 'fa-check-double' : 'fa-clipboard-check' }}"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                    <h4 id="completionNoticeTitle" class="text-[11px] font-black {{ $isJobFinished ? 'text-emerald-900' : 'text-indigo-950' }} flex items-center gap-1.5">
                        <i class="fa-solid fa-bell text-amber-500"></i>
                        <span id="completionNoticeHeading">{{ $isJobFinished ? 'Pemberitahuan: Pekerjaan Telah Selesai & Ditutup' : 'Pemberitahuan: Pekerjaan Telah Dinyatakan Selesai' }}</span>
                        <span id="completionNoticeBadge" class="text-[8px] font-extrabold px-2 py-0.5 rounded-full {{ $isJobFinished ? 'bg-emerald-200 text-emerald-900' : 'bg-indigo-200 text-indigo-900' }}">
                            {{ $isJobFinished ? 'Tuntas' : 'Perlu Konfirmasi Admin' }}
                        </span>
                    </h4>
                    <span class="text-[8px] font-semibold {{ $isJobFinished ? 'text-emerald-700' : 'text-indigo-600' }}" id="completionNoticeTime">
                        {{ $order['time'] ?? 'Hari ini' }}
                    </span>
                </div>
                <p class="mt-1 text-[9.5px] leading-relaxed {{ $isJobFinished ? 'text-emerald-800' : 'text-indigo-900' }}" id="completionNoticeDesc">
                    @if($isJobFinished)
                        Customer <strong class="font-bold text-slate-900">{{ $order['customer'] ?? 'Customer' }}</strong> dan Mitra <strong class="font-bold text-slate-900">{{ $order['mitra'] ?? 'Mitra' }}</strong> telah menyelesaikan pesanan ini. Rekapitulasi pekerjaan telah tuntas dan dana telah diteruskan ke Mitra.
                    @else
                        Customer <strong class="font-bold text-slate-900">{{ $order['customer'] ?? 'Customer' }}</strong> dan Mitra <strong class="font-bold text-slate-900">{{ $order['mitra'] ?? 'Mitra' }}</strong> telah menyatakan pekerjaan suruhan ini telah selesai dikerjakan di lokasi. Silakan periksa hasil kerja dan lakukan konfirmasi penyelesaian pesanan agar dana escrow diteruskan ke Mitra.
                    @endif
                </p>
                <div id="completionNoticeActionContainer" class="mt-2.5 flex items-center gap-2 {{ $isAwaitingAdminCompletion ? '' : 'hidden' }}">
                    <button
                        type="button"
                        onclick="handleConfirmCompletion()"
                        class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-3.5 py-2 text-[9px] font-black text-white shadow-sm hover:from-emerald-700 hover:to-teal-700 transition active:scale-95 flex items-center gap-1.5 cursor-pointer"
                    >
                        <i class="fa-solid fa-flag-checkered text-[10px]"></i>
                        <span>Konfirmasi Penyelesaian &amp; Teruskan Dana</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
    INFORMASI LAYANAN
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <p class="text-[9px] text-slate-400">
                        Layanan
                    </p>
                    <h3 class="mt-1 text-[17px] font-bold text-navy" id="detailServiceName">
                        {{ $order['service'] ?? 'Bersihkan Rumah' }}
                    </h3>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[8px] font-medium text-slate-600">
                    {{ $displayOrderCode }}
                </span>
            </div>

            <div class="space-y-4">
                {{-- CUSTOMER --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] text-slate-400">
                            Customer
                        </p>
                        <p class="mt-1 text-[11px] font-semibold text-slate-700 flex items-center gap-1.5">
                            {{ $order['customer'] ?? 'Andi' }}
                            @if(!empty($order['customer_phone']))
                                <span class="text-[8.5px] font-normal text-slate-400">({{ $order['customer_phone'] }})</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-[11px] font-bold text-blue-600 shadow-2xs">
                        {{ $order['customer_initial'] ?? mb_substr($order['customer'] ?? 'A', 0, 1) }}
                    </div>
                </div>

                {{-- MITRA --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] text-slate-400">
                            Mitra Pelaksana
                        </p>
                        <p class="mt-1 text-[11px] font-semibold text-slate-700 flex items-center gap-1.5">
                            {{ $order['mitra'] ?? 'Budi' }}
                            @if(!empty($order['mitra_phone']))
                                <span class="text-[8.5px] font-normal text-slate-400">({{ $order['mitra_phone'] }})</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-full border border-green-100 bg-green-50 text-[11px] font-bold text-green-600 shadow-2xs">
                        {{ $order['mitra_initial'] ?? mb_substr($order['mitra'] ?? 'B', 0, 1) }}
                    </div>
                </div>

                {{-- TANGGAL --}}
                <div class="flex justify-between gap-4">
                    <span class="text-[9px] text-slate-400">
                        Tanggal Pelaksanaan
                    </span>
                    <span class="text-right text-[10px] font-semibold text-slate-700">
                        {{ $order['date'] ?? '31 Agustus 2026' }}
                    </span>
                </div>

                {{-- WAKTU --}}
                <div class="flex justify-between gap-4">
                    <span class="text-[9px] text-slate-400">
                        Waktu Layanan
                    </span>
                    <span class="text-right text-[10px] font-semibold text-slate-700">
                        {{ $order['time'] ?? '10.00 - 12.00' }}
                    </span>
                </div>

                {{-- ALAMAT --}}
                <div class="flex justify-between gap-5">
                    <span class="text-[9px] text-slate-400">
                        Alamat Pengerjaan
                    </span>
                    <span class="max-w-[220px] text-right text-[10px] font-semibold leading-relaxed text-slate-700">
                        {{ $order['address'] ?? 'Jl. Kaliurang No. 25, Yogyakarta' }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
    DETAIL PEKERJAAN
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <h3 class="text-[13px] font-bold text-navy">
                Detail Pekerjaan
            </h3>

            <p class="mt-3 text-[10px] leading-relaxed text-slate-500">
                {{ $order['description'] ?? 'Customer membutuhkan bantuan untuk pengerjaan layanan dengan spesifikasi sesuai kesepakatan aplikasi.' }}
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach(($order['tags'] ?? ['Ruang Tamu', 'Kamar', 'Dapur']) as $tag)
                    <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-[8px] font-medium text-[#294D65]">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
    PEMBAYARAN & PENGECEKAN DANA MASUK
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-[13px] font-bold text-navy">
                        Pembayaran
                    </h3>
                    <p class="mt-1 text-[8px] text-slate-400">
                        Status pembayaran Customer
                    </p>
                </div>

                <div id="paymentStatusBadgeContainer">
                    @if($isPaid)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-[8px] font-bold text-green-700">
                            <span class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-green-600 text-[7px] text-white">✓</span>
                            Terkonfirmasi
                        </span>
                    @elseif($isRefunded)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-[8px] font-bold text-red-700">
                            Dana Dikembalikan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-3 py-1.5 text-[8px] font-bold text-amber-700">
                            <span class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-amber-500 text-[7px] text-white">⏳</span>
                            {{ $isPaymentPending ? 'Menunggu Konfirmasi Admin' : 'Customer Belum Melaporkan Pembayaran' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- PANEL PENGECEKAN ALIRAN DANA MASUK (ADMIN PENGECEKAN REKBER) --}}
            <div class="mt-4 rounded-[16px] border border-slate-200/90 bg-gradient-to-br from-slate-50 to-blue-50/40 p-3.5 shadow-2xs">
                <div class="flex items-center justify-between gap-2 border-b border-slate-200/60 pb-2.5 mb-2.5">
                    <span class="flex items-center gap-1.5 text-[9px] font-bold text-slate-700">
                        <i class="fa-solid fa-building-columns text-blue-600"></i>
                        Status Rekening Bersama SayaBantu
                    </span>
                    <span id="bankSettlementIndicator" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-semibold {{ $isRefunded ? 'bg-red-100 text-red-700' : ($isPaid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800') }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isRefunded ? 'bg-red-500' : ($isPaid ? 'bg-emerald-500' : 'bg-amber-500') }}"></span>
                        {{ $isRefunded ? 'Dana Dikembalikan' : ($isPaid ? 'Dana Tercatat di Rekber' : ($isPaymentPending ? 'Laporan Customer Menunggu Admin' : 'Menunggu Pembayaran Customer')) }}
                    </span>
                </div>

                <div class="space-y-2 text-[8.5px]">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-700">{{ $isPaid ? 'Total Nominal Diterima' : ($isPaymentPending ? 'Nominal Dilaporkan Customer' : 'Total Nominal') }}</span>
                        <span class="font-bold {{ $isRefunded ? 'text-red-700' : ($isPaymentPending ? 'text-amber-800' : 'text-emerald-700') }} text-[10.5px]">{{ $isRefunded ? 'Dana dikembalikan' : ($isPaid ? ($order['total_price'] ?? 'Belum tercatat') : ($isPaymentPending ? ($order['total_price'] ?? 'Menunggu konfirmasi Admin') : 'Belum tercatat')) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-700">Metode Pembayaran</span>
                        <span class="font-semibold text-slate-900">{{ ($isPaid || $isRefunded || $isPaymentPending) ? ($order['payment_method'] ?? 'Belum tercatat') : 'Belum tercatat' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-700">Waktu Pembayaran Customer</span>
                        <span class="font-semibold text-slate-900">{{ ($isPaid || $isRefunded || $isPaymentPending) ? ($order['payment_time'] ?? 'Belum tercatat') : 'Belum tercatat' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-700">Status Pembayaran Customer</span>
                        <span id="adminVerificationLabel" class="font-bold {{ $isPaid ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $isRefunded ? 'Dana dikembalikan' : ($isPaid ? 'Dikonfirmasi Admin ✓' : ($isPaymentPending ? 'Menunggu Konfirmasi Admin' : 'Customer Belum Melapor')) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- STATUS NOTICE BOX --}}
            <div id="paymentStatusNoticeBox" class="mt-4 rounded-[15px] border {{ $isPaid ? 'border-green-100 bg-green-50/70' : 'border-amber-200/80 bg-amber-50/70' }} p-3 transition-colors">
                <div class="flex items-start gap-3">
                    <div id="paymentNoticeIcon" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $isPaid ? 'bg-green-500 text-white' : 'bg-amber-500 text-white' }} text-[13px] font-bold">
                        <i class="fa-solid {{ $isPaid ? 'fa-check' : 'fa-clock' }}"></i>
                    </div>

                    <div>
                        <p id="paymentNoticeTitle" class="text-[10px] font-bold {{ $isPaid ? 'text-green-700' : 'text-amber-800' }}">
                            {{ $isPaid ? 'Pembayaran telah dikonfirmasi Admin' : ($isPaymentPending ? 'Menunggu konfirmasi Admin' : 'Menunggu pembayaran Customer') }}
                        </p>
                        <p id="paymentNoticeDesc" class="mt-1 text-[8px] leading-relaxed {{ $isPaid ? 'text-green-600' : 'text-amber-700' }}">
                            {{ $isPaid ? 'Admin telah mengonfirmasi pembayaran via '.($order['payment_method'] ?? 'metode pembayaran tercatat').'. Dana telah masuk ke saldo Admin.' : ($isPaymentPending ? 'Customer sudah melaporkan pembayaran via '.($order['payment_method'] ?? 'metode pembayaran tercatat').'. Konfirmasi pembayaran setelah memeriksa transaksi.' : 'Customer belum melaporkan pembayaran.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- KARTU BUKTI PEMBAYARAN CUSTOMER --}}
            <div class="mt-4 rounded-[16px] border border-slate-200/90 bg-white p-3.5 shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-800">
                        <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                        Bukti Pembayaran Customer
                    </span>
                    <span class="text-[8.5px] font-extrabold px-2 py-0.5 rounded-full {{ $isPaid ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($isPaymentPending ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-100 text-slate-600') }}">
                        {{ $isPaid ? 'Terverifikasi Admin ✓' : ($isPaymentPending ? 'Wajib Diperiksa' : 'Belum Ada Bukti') }}
                    </span>
                </div>

                @if($isPaymentPending || $isPaid)
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        {{-- Thumbnail Preview --}}
                        <div onclick="openBuktiPembayaranModal()" class="w-14 h-14 rounded-lg overflow-hidden border border-slate-300 bg-slate-100 shrink-0 relative cursor-pointer group hover:ring-2 hover:ring-emerald-500 transition-all">
                            @if(!empty($order['payment_proof']))
                                <img src="{{ $order['payment_proof'] }}" alt="Bukti Pembayaran" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-600">
                                    <i class="fa-solid fa-receipt text-lg"></i>
                                    <span class="text-[7px] font-bold mt-0.5">Struk Bayar</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="flex-1 min-w-0 text-[9px] space-y-0.5">
                            <strong class="text-[10px] font-bold text-slate-800 block truncate">
                                {{ !empty($order['payment_proof']) ? 'Lampiran Bukti Transfer Customer' : 'Struk Resmi Pembayaran SayaBantu' }}
                            </strong>
                            <p class="text-slate-500">Metode: <strong class="text-slate-700">{{ $order['payment_method'] ?? 'QRIS' }}</strong> • {{ $order['total_price'] ?? ($order['service_price'] ?? 'Rp 20.000') }}</p>
                            <p class="text-slate-400 text-[8px]">{{ $order['payment_time'] ?? 'Waktu tidak tercatat' }}</p>
                        </div>

                        {{-- Action View Button --}}
                        <button type="button" onclick="openBuktiPembayaranModal()" class="shrink-0 px-2.5 py-1.5 rounded-lg bg-[#173B67] hover:bg-[#0E1D31] text-white text-[9px] font-bold flex items-center gap-1 cursor-pointer transition-colors shadow-2xs">
                            <i class="fa-solid fa-eye text-[8px]"></i>
                            <span>Lihat Bukti</span>
                        </button>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-slate-400 text-[9px]">
                        Customer belum mengunggah atau melaporkan bukti pembayaran.
                    </div>
                @endif
            </div>

            @if($isPaymentPending)
                <button
                    type="button"
                    id="btnAdminConfirmPayment"
                    onclick="openBuktiPembayaranModal(true)"
                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-[14px] bg-emerald-600 px-4 py-3 text-[9px] font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.99] cursor-pointer"
                >
                    <i class="fa-solid fa-receipt text-xs"></i>
                    <span>Periksa Bukti &amp; Konfirmasi Pembayaran Customer</span>
                </button>
            @endif

            {{-- RINCIAN BIAYA --}}
            <div class="mt-4 space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-[9px] text-slate-400">
                        Harga Layanan
                    </span>
                    <span class="text-[10px] font-semibold text-slate-700">
                        {{ $order['total_price'] ?? ($order['service_price'] ?? 'Rp 20.000') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-[9px] text-slate-400">
                        Biaya Platform / Tambahan
                    </span>
                    <span class="text-[10px] font-bold text-emerald-700">
                        Gratis (Rp 0)
                    </span>
                </div>

                <div class="border-t border-slate-100 pt-2.5">
                    <div class="flex justify-between">
                        <span class="text-[10px] font-semibold text-slate-700">
                            Total Pembayaran
                        </span>
                        <span class="text-[13px] font-bold text-navy" id="detailTotalPrice">
                            {{ $order['total_price'] ?? ($order['service_price'] ?? 'Rp 20.000') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- INFORMASI METODE TRANSAKSI --}}
            <div class="mt-4 rounded-[14px] border border-slate-100 bg-slate-50 p-3">
                <div class="flex justify-between gap-3">
                    <span class="text-[8px] text-slate-400">
                        Metode Pembayaran
                    </span>
                    <span class="text-[9px] font-semibold text-slate-600" id="detailPaymentMethod">
                        {{ $order['payment_method'] ?? 'QRIS' }}
                    </span>
                </div>

                <div class="mt-2 flex justify-between gap-3">
                    <span class="text-[8px] text-slate-400">
                        Waktu Pembayaran
                    </span>
                    <span class="text-right text-[9px] font-semibold text-slate-600" id="detailPaymentTime">
                        {{ $order['payment_time'] ?? '31 Agustus 2026 • 09.15 WIB' }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================
    STATUS PESANAN (TIMELINE)
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <h3 class="text-[13px] font-bold text-navy">
                Status Pesanan
            </h3>

            <div class="mt-5 space-y-5" id="orderTimelineContainer">
                @php
                    $timelineSteps = $order['timeline'] ?? [
                        ['key' => 'created', 'title' => 'Pesanan dibuat', 'time' => '31 Agustus 2026 • 08.30', 'completed' => true],
                        ['key' => 'accepted', 'title' => 'Mitra menerima pekerjaan', 'time' => '31 Agustus 2026 • 09.00', 'completed' => true],
                        ['key' => 'payment', 'title' => 'Pembayaran terkonfirmasi', 'time' => $isPaid ? 'Dikonfirmasi oleh Customer • '.$order['payment_time'] : 'Menunggu pembayaran Customer', 'completed' => $isPaid],
                        ['key' => 'started', 'title' => 'Layanan dimulai', 'time' => $isConfirmed ? 'Dikonfirmasi oleh Admin' : 'Belum dimulai', 'completed' => $isConfirmed],
                    ];
                @endphp

                @foreach($timelineSteps as $index => $step)
                    @php
                        $isCompleted = !empty($step['completed']);
                        $isCurrentStep = ($index === 3 && $isConflict);
                    @endphp
                    <div class="relative flex gap-3 timeline-row" data-step-index="{{ $index }}">
                        <div class="relative z-10 mt-1 h-3 w-3 shrink-0 rounded-full {{ $isCurrentStep ? 'bg-red-500 ring-4 ring-red-50' : ($isCompleted ? 'bg-green-500 ring-4 ring-green-50' : 'bg-slate-300 ring-4 ring-slate-50') }} transition-all">
                        </div>

                        <div>
                            <p class="text-[10px] font-semibold {{ $isCurrentStep ? 'text-red-700' : ($isCompleted ? ($index === 2 ? 'text-green-700' : 'text-slate-700') : 'text-slate-400') }}">
                                {{ $step['title'] }}
                            </p>
                            <p class="mt-1 text-[8px] {{ $isCurrentStep ? 'text-red-600' : ($isCompleted ? ($index === 2 ? 'text-green-600' : 'text-slate-400') : 'text-slate-400') }}">
                                {{ $step['time'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================
    TINDAKAN ADMIN (INTERAKTIF & LIVE)
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-[13px] font-bold text-navy">
                        Tindakan Admin
                    </h3>
                    <p class="text-[8px] text-slate-400 mt-0.5">
                        Alur persetujuan bertahap
                    </p>
                </div>
            </div>

            <div class="space-y-2.5" id="adminActionsWrapper">

                {{-- 1. STATUS PEMBAYARAN (dikonfirmasi Admin) --}}
                <div id="btnPaymentContainer">
                    @if($isPaid)
                        <div class="flex w-full items-center justify-center gap-2 rounded-[14px] border border-green-200 bg-green-50 px-4 py-3 text-[9px] font-semibold text-green-700 shadow-2xs">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-[8px] text-white">✓</span>
                            <span>Pembayaran Dikonfirmasi Admin • {{ $order['payment_method'] ?? 'Metode belum tercatat' }}</span>
                        </div>
                    @elseif($isPaymentPending)
                        <button
                            type="button"
                            onclick="openBuktiPembayaranModal(true)"
                            class="flex w-full items-center justify-center gap-2 rounded-[14px] bg-emerald-600 px-4 py-3 text-[9px] font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.99] cursor-pointer"
                        >
                            <i class="fa-solid fa-receipt"></i>
                            <span>Periksa Bukti &amp; Konfirmasi Pembayaran Customer</span>
                        </button>
                    @else
                        <div class="flex w-full items-center justify-center gap-2 rounded-[14px] border border-amber-300 bg-amber-50 px-4 py-3 text-[9px] font-bold text-amber-800">
                            Menunggu Customer Melakukan Pembayaran
                        </div>
                    @endif
                </div>

                {{-- 2. TOMBOL KONFIRMASI PESANAN --}}
                <div id="btnConfirmOrderContainer">
                    @if($isConfirmed)
                        <button
                            type="button"
                            onclick="handleUnconfirmOrder()"
                            class="flex w-full items-center justify-center gap-2 rounded-[14px] border border-blue-200 bg-blue-50 px-4 py-3 text-[9px] font-bold text-[#17314A] shadow-2xs transition hover:bg-blue-100/70 cursor-pointer"
                            title="Klik jika ingin membatalkan konfirmasi pesanan"
                        >
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#17314A] text-[8px] text-white">✓</span>
                            <span>Pesanan Telah Dikonfirmasi</span>
                        </button>
                    @else
                        <button
                            type="button"
                            id="btnSubmitConfirmOrder"
                            onclick="handleConfirmOrder()"
                            class="w-full rounded-[14px] bg-[#0E1D31] hover:bg-[#17314A] px-4 py-3 text-[9px] font-semibold text-white shadow-sm transition hover:opacity-95 active:scale-[0.99] cursor-pointer"
                        >
                            <span id="btnConfirmOrderText">Konfirmasi Pesanan</span>
                        </button>
                    @endif
                </div>

                {{-- 2.B TOMBOL KONFIRMASI PENYELESAIAN (PENUTUPAN PESANAN - TAHAP 11) --}}
                <div id="btnConfirmCompletionContainer">
                    @if(($order['status'] ?? '') === 'Selesai')
                        <div class="p-3 rounded-[14px] bg-emerald-50 border border-emerald-200 text-center text-[9px] font-bold text-emerald-800">
                            <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Pesanan Selesai &amp; Ditutup
                        </div>
                    @elseif($workflowStatus === 'menunggu_konfirmasi_admin_selesai')
                        <button
                            type="button"
                            id="btnSubmitConfirmCompletion"
                            onclick="handleConfirmCompletion()"
                            class="w-full rounded-[14px] bg-emerald-600 hover:bg-emerald-700 px-4 py-3 text-[9px] font-bold text-white shadow-sm transition active:scale-[0.99] cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <i class="fa-solid fa-flag-checkered text-[10px]"></i>
                            <span id="btnConfirmCompletionText">Konfirmasi Penyelesaian Pesanan</span>
                        </button>
                    @else
                        <div class="p-3 rounded-[14px] bg-slate-50 border border-slate-200 text-center text-[9px] font-medium text-slate-600">
                            Menunggu Customer mengonfirmasi pekerjaan selesai sebelum Admin dapat menutup pesanan.
                        </div>
                    @endif
                </div>

                {{-- 3. TOMBOL KONFLIK / SELESAIKAN MEDIASI --}}
                <div id="btnConflictContainer">
                    @if($isConflict)
                        <button
                            type="button"
                            onclick="openResolveConflictModal()"
                            class="w-full flex items-center justify-center gap-2 rounded-[14px] border border-emerald-300 bg-emerald-50 px-4 py-3 text-[9px] font-bold text-emerald-700 transition hover:bg-emerald-100 active:scale-[0.99] cursor-pointer shadow-2xs"
                        >
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                            <span>Selesaikan Mediasi Konflik</span>
                        </button>
                    @else
                        <button
                            type="button"
                            onclick="openMarkConflictModal()"
                            class="w-full rounded-[14px] border border-red-200 bg-red-50/70 px-4 py-3 text-[9px] font-semibold text-red-600 transition hover:bg-red-100 active:scale-[0.99] cursor-pointer"
                        >
                            Tandai sebagai Konflik
                        </button>
                    @endif
                </div>

                {{-- 4. TOMBOL TOLAK / BATALKAN PESANAN OLEH ADMIN (SKENARIO B) --}}
                <div id="btnCancelOrderContainer">
                    @if(in_array($order['status'] ?? '', ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan']))
                        <div class="p-3 rounded-[14px] bg-rose-50 border border-rose-200 text-center text-[9px] font-bold text-rose-700">
                            <i class="fa-solid fa-ban mr-1"></i> Pesanan Telah Dibatalkan ({{ ($order['status'] ?? '') === 'cancelled_by_admin' ? 'Oleh Admin' : 'Oleh Customer' }})
                        </div>
                    @else
                        <button
                            type="button"
                            onclick="openCancelOrderModal()"
                            class="w-full flex items-center justify-center gap-2 rounded-[14px] border border-rose-200 bg-rose-50/70 hover:bg-rose-100/90 text-rose-700 px-4 py-3 text-[9px] font-bold shadow-2xs transition active:scale-[0.99] cursor-pointer"
                        >
                            <i class="fa-solid fa-ban text-[10px] text-rose-600"></i>
                            <span>Tolak / Batalkan Pesanan</span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- =====================================================
    CATATAN ADMIN
    ====================================================== --}}
    <section>
        <div class="detail-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-[13px] font-bold text-navy">
                        Catatan Admin
                    </h3>
                    <p class="mt-1 text-[8px] text-slate-400">
                        Catatan internal mengenai pesanan
                    </p>
                </div>
                <span id="notesStatusSaved" class="text-[8px] font-medium text-emerald-600 hidden flex items-center gap-1">
                    <i class="fa-solid fa-check"></i>
                    Tersimpan
                </span>
            </div>

            <textarea
                id="adminOrderNotesInput"
                rows="4"
                placeholder="Tambahkan catatan khusus dari customer, bukti foto, atau instruksi mitra..."
                class="mt-4 w-full resize-none rounded-[14px] border border-slate-200 bg-slate-50 p-3 text-[9.5px] leading-relaxed text-slate-600 outline-none placeholder:text-slate-300 transition focus:border-[#4F7CAC] focus:bg-white focus:ring-2 focus:ring-blue-50"
            >{{ $order['notes'] ?? '' }}</textarea>

            <button
                type="button"
                id="btnSaveAdminNotes"
                onclick="handleSaveOrderNotes()"
                class="mt-3 w-full flex items-center justify-center gap-1.5 rounded-[13px] bg-[#EEF5FF] px-4 py-3 text-[9.5px] font-semibold text-[#294D65] transition hover:bg-[#E0EDFF] active:scale-[0.99] cursor-pointer"
            >
                <i class="fa-solid fa-floppy-disk text-[9px]"></i>
                <span id="btnSaveAdminNotesText">Simpan Catatan</span>
            </button>
        </div>
    </section>

</div>

{{-- =========================================================
     MODAL DIALOG TINDAKAN ADMIN
========================================================= --}}
@push('modals')

{{-- MODAL PEMERIKSAAN BUKTI PEMBAYARAN --}}
<div id="buktiPembayaranModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-xs pointer-events-none" style="display: none;">
    <div class="w-full max-w-md rounded-[24px] bg-white p-5 shadow-2xl transition-all pointer-events-auto max-h-[92vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 leading-tight">Bukti Pembayaran Customer</h4>
                    <p class="text-[9px] text-slate-400">Periksa bukti transfer sebelum melakukan konfirmasi</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('buktiPembayaranModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center cursor-pointer transition-colors">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="py-3.5 space-y-3 overflow-y-auto flex-1 pr-1">
            {{-- Image / Struk Proof Preview --}}
            <div class="rounded-2xl border-2 border-slate-200 bg-slate-900 overflow-hidden relative group text-center">
                @if(!empty($order['payment_proof']))
                    <div class="p-2 bg-slate-950">
                        <img id="modalBuktiPembayaranImg" src="{{ $order['payment_proof'] }}" alt="Bukti Pembayaran" class="w-full max-h-80 object-contain mx-auto rounded-lg">
                    </div>
                    <div class="p-2 bg-slate-800 flex items-center justify-between text-[9px] text-white">
                        <span class="flex items-center gap-1 font-semibold text-emerald-400">
                            <i class="fa-solid fa-check"></i> Bukti Lampiran Customer
                        </span>
                        <a href="{{ $order['payment_proof'] }}" target="_blank" class="text-sky-300 hover:text-sky-200 flex items-center gap-1 underline font-bold">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i> Lihat Ukuran Penuh
                        </a>
                    </div>
                @else
                    {{-- Digital Struk Transfer Rekber SayaBantu --}}
                    <div id="modalBuktiDigitalSlip" class="p-5 bg-gradient-to-b from-white via-slate-50 to-white text-slate-800 text-left space-y-3 font-sans">
                        <div class="flex items-center justify-between border-b border-dashed border-slate-300 pb-3">
                            <div>
                                <span class="text-xs font-black tracking-tight text-[#173B67] block">SayaBantu.com</span>
                                <span class="text-[8px] text-slate-400">Struk Transaksi Resmi Rekber</span>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full {{ $isPaid ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                {{ $isPaid ? 'Lunas / Terverifikasi' : 'Menunggu Verifikasi Admin' }}
                            </span>
                        </div>
                        <div class="text-center py-2.5 bg-slate-100/80 rounded-xl border border-slate-200/60">
                            <span class="text-[8.5px] text-slate-500 block uppercase font-bold tracking-wider">Total Pembayaran</span>
                            <strong class="text-lg font-black text-emerald-700 block">{{ $order['total_price'] ?? ($order['service_price'] ?? 'Rp 20.000') }}</strong>
                        </div>
                        <div class="space-y-1.5 text-[9.5px]">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nomor Transaksi:</span>
                                <strong class="font-mono text-slate-800">{{ $order['code'] ?? '#SB-SURUH' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama Customer:</span>
                                <strong class="text-slate-800">{{ $order['customer'] ?? 'Customer' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Layanan:</span>
                                <strong class="text-slate-800">{{ $order['service'] ?? 'Suruhan' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Mitra Ditugaskan:</span>
                                <strong class="text-slate-800">{{ $order['mitra'] ?? 'Mitra SayaBantu' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Metode Bayar:</span>
                                <strong class="text-slate-800">{{ $order['payment_method'] ?? 'QRIS' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Waktu Laporan:</span>
                                <span class="text-slate-700 font-medium">{{ $order['payment_time'] ?? 'Hari ini' }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Ringkasan Panduan Verifikasi --}}
            <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200/90 space-y-1">
                <span class="text-[9.5px] font-bold text-amber-900 flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Panduan Verifikasi Admin
                </span>
                <p class="text-[8.5px] text-amber-800 leading-relaxed">
                    Pastikan nominal <strong>{{ $order['total_price'] ?? ($order['service_price'] ?? 'Rp 20.000') }}</strong> telah sesuai dengan mutasi rekening/QRIS penerima SayaBantu sebelum menekan tombol konfirmasi.
                </p>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="pt-3 border-t border-slate-100 flex items-center gap-2 shrink-0">
            <button
                type="button"
                onclick="closeModal('buktiPembayaranModal')"
                class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold transition text-center cursor-pointer"
            >
                Tutup
            </button>
            @if($isPaymentPending)
                <button
                    type="button"
                    id="btnModalExecuteConfirmPayment"
                    onclick="executeAdminConfirmPaymentFromModal()"
                    class="flex-[1.5] py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white text-[10px] font-extrabold transition shadow-md flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    <span>Konfirmasi Pembayaran Sesuai</span>
                </button>
            @endif
        </div>
    </div>
</div>

{{-- MODAL TANDAI SEBAGAI KONFLIK --}}
<div id="markConflictModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-xs pointer-events-none" style="display: none;">
    <div class="w-full max-w-sm rounded-[20px] bg-white p-5 shadow-2xl transition-all pointer-events-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="text-xs font-bold text-red-600 flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Tandai Pesanan sebagai Konflik
            </h4>
            <button type="button" onclick="closeModal('markConflictModal')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <p class="mt-3 text-[9.5px] text-slate-500 leading-relaxed">
            Pilihlah atau rincikan alasan konflik antara customer dan mitra agar tim mediasi dapat segera menindaklanjuti penyelesaian masalah:
        </p>

        <div class="mt-3 space-y-2">
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="conflictPreset" value="Mitra tidak hadir di lokasi sesuai jadwal pengerjaan" checked onchange="toggleCustomConflictReason(false)">
                <span>Mitra tidak hadir di lokasi sesuai jadwal</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="conflictPreset" value="Customer mengeluhkan kualitas pekerjaan yang tidak sesuai kesepakatan" onchange="toggleCustomConflictReason(false)">
                <span>Komplain kualitas pengerjaan mitra</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="conflictPreset" value="Perselisihan mengenai nominal biaya tambahan pengerjaan" onchange="toggleCustomConflictReason(false)">
                <span>Perselisihan biaya tambahan / pembayaran</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="conflictPreset" value="custom" onchange="toggleCustomConflictReason(true)">
                <span>Tuliskan alasan lainnya...</span>
            </label>
        </div>

        <div id="customConflictReasonBox" class="mt-2.5 hidden">
            <textarea
                id="customConflictReasonInput"
                rows="2"
                placeholder="Rincikan masalah perselisihan..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-[9px] text-slate-700 outline-none focus:border-red-400 focus:bg-white"
            ></textarea>
        </div>

        <div class="mt-5 flex gap-2">
            <button
                type="button"
                onclick="closeModal('markConflictModal')"
                class="flex-1 rounded-xl bg-slate-100 py-2.5 text-[9.5px] font-bold text-slate-600 hover:bg-slate-200 transition"
            >
                Batal
            </button>
            <button
                type="button"
                id="btnSubmitMarkConflict"
                onclick="submitMarkConflict()"
                class="flex-1 rounded-xl bg-red-600 py-2.5 text-[9.5px] font-bold text-white shadow-xs hover:bg-red-700 transition"
            >
                Tandai Konflik
            </button>
        </div>
    </div>
</div>

{{-- MODAL SELESAIKAN MEDIASI KONFLIK --}}
<div id="resolveConflictModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-xs pointer-events-none" style="display: none;">
    <div class="w-full max-w-sm rounded-[20px] bg-white p-5 shadow-2xl transition-all pointer-events-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                Selesaikan Mediasi Konflik
            </h4>
            <button type="button" onclick="closeModal('resolveConflictModal')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <p class="mt-3 text-[10px] text-slate-600 leading-relaxed">
            Apakah mediasi antara Customer dan Mitra telah selesai dan pesanan {{ $displayOrderCode }} siap dilanjutkan ke status <strong>Diproses</strong>?
        </p>

        <div class="mt-5 flex gap-2">
            <button
                type="button"
                onclick="closeModal('resolveConflictModal')"
                class="flex-1 rounded-xl bg-slate-100 py-2.5 text-[9.5px] font-bold text-slate-600 hover:bg-slate-200 transition"
            >
                Batal
            </button>
            <button
                type="button"
                id="btnSubmitResolveConflict"
                onclick="submitResolveConflict()"
                class="flex-1 rounded-xl bg-emerald-600 py-2.5 text-[9.5px] font-bold text-white shadow-xs hover:bg-emerald-700 transition"
            >
                Selesaikan Mediasi
            </button>
        </div>
    </div>
</div>

{{-- MODAL TOLAK / BATALKAN PESANAN ADMIN (SKENARIO B) --}}
<div id="cancelOrderModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-xs pointer-events-none" style="display: none;">
    <div class="w-full max-w-sm rounded-[20px] bg-white p-5 shadow-2xl transition-all pointer-events-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="text-xs font-bold text-rose-600 flex items-center gap-1.5">
                <i class="fa-solid fa-ban"></i>
                Tolak / Batalkan Pesanan Admin
            </h4>
            <button type="button" onclick="closeModal('cancelOrderModal')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <p class="mt-3 text-[9.5px] text-slate-500 leading-relaxed">
            Pilih atau tuliskan alasan penolakan/pembatalan. Status akan diubah ke <span class="font-bold text-rose-600">cancelled_by_admin</span>, pengembalian dana escrow customer diproses, dan pesanan dihapus dari feed mitra:
        </p>

        <div class="mt-3 space-y-2">
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="adminCancelPreset" value="Alamat penugasan fiktif atau tidak terjangkau mitra" checked onchange="toggleCustomCancelReason(false)">
                <span>Alamat penugasan fiktif / tidak terjangkau</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="adminCancelPreset" value="Terindikasi pelanggaran Syarat & Ketentuan (ToS)" onchange="toggleCustomCancelReason(false)">
                <span>Pelanggaran Syarat &amp; Ketentuan (ToS)</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="adminCancelPreset" value="Permintaan pembatalan customer melalui CS SayaBantu" onchange="toggleCustomCancelReason(false)">
                <span>Permintaan customer via CS</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[9px] text-slate-700">
                <input type="radio" name="adminCancelPreset" value="custom" onchange="toggleCustomCancelReason(true)">
                <span>Tuliskan alasan spesifik lainnya...</span>
            </label>
        </div>

        <div id="customCancelReasonBox" class="mt-2.5 hidden">
            <textarea
                id="customCancelReasonInput"
                rows="2"
                placeholder="Tuliskan rincian alasan pembatalan..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-[9px] text-slate-700 outline-none focus:border-rose-400 focus:bg-white"
            ></textarea>
        </div>

        <div class="mt-3 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[8.5px] text-amber-800 flex items-start gap-2">
            <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 shrink-0"></i>
            <span>Dana di escrow (rekber) akan otomatis dialihkan ke status <strong>Refunded</strong> untuk dikembalikan ke customer.</span>
        </div>

        <div class="mt-4 flex gap-2">
            <button
                type="button"
                onclick="closeModal('cancelOrderModal')"
                class="flex-1 rounded-xl bg-slate-100 py-2.5 text-[9.5px] font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer"
            >
                Batal
            </button>
            <button
                type="button"
                id="btnSubmitAdminCancelOrder"
                onclick="submitAdminCancelOrder()"
                class="flex-1 rounded-xl bg-rose-600 py-2.5 text-[9.5px] font-bold text-white shadow-xs hover:bg-rose-700 transition cursor-pointer"
            >
                Tolak &amp; Batalkan
            </button>
        </div>
    </div>
</div>

@endpush

{{-- =========================================================
     JAVASCRIPT LOGIC (REALTIME, AJAX, & INTERAKSI RIIL)
========================================================= --}}
@push('scripts')
<script>
(function() {
    function getEffectiveOrderId() {
        let id = "{{ $cleanOrderId }}".replace(/^#+/, '');
        if (!id || id === '""' || id === 'null') {
            const segs = window.location.pathname.split('/').filter(Boolean);
            const last = segs[segs.length - 1];
            if (last && last !== 'pesanan') id = last;
        }
        if (!id) {
            const h2 = document.querySelector('h2');
            if (h2) {
                const m = h2.textContent.match(/SB[-0-9A-Z]+/i);
                if (m) id = m[0];
            }
        }
        return String(id || '').replace(/^#+/, '').trim();
    }
    const currentOrderId = getEffectiveOrderId();
    const currentOrderCode = "{{ $displayOrderCode }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Helper Modal Dialog
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'flex';
            el.classList.remove('hidden', 'pointer-events-none');
            el.classList.add('flex', 'pointer-events-auto');
        }
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            el.classList.remove('flex', 'pointer-events-auto');
            el.classList.add('hidden', 'pointer-events-none');
        }
    }
    window.openModal = openModal;
    window.closeModal = closeModal;

    window.openMarkConflictModal = function() { openModal('markConflictModal'); };
    window.openResolveConflictModal = function() { openModal('resolveConflictModal'); };
    window.openCancelOrderModal = function() { openModal('cancelOrderModal'); };
    window.openBuktiPembayaranModal = function() { openModal('buktiPembayaranModal'); };
    window.closeBuktiPembayaranModal = function() { closeModal('buktiPembayaranModal'); };

    window.executeAdminConfirmPaymentFromModal = async function() {
        const modalBtn = document.getElementById('btnModalExecuteConfirmPayment');
        if (modalBtn) {
            modalBtn.disabled = true;
            modalBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Memverifikasi...</span>';
        }
        await handleAdminConfirmPayment(true);
    };

    window.toggleCustomCancelReason = function(showCustom) {
        const box = document.getElementById('customCancelReasonBox');
        if (box) {
            box.classList.toggle('hidden', !showCustom);
            if (showCustom) {
                document.getElementById('customCancelReasonInput')?.focus();
            }
        }
    };

    window.toggleCustomConflictReason = function(showCustom) {
        const box = document.getElementById('customConflictReasonBox');
        if (box) {
            box.classList.toggle('hidden', !showCustom);
            if (showCustom) {
                document.getElementById('customConflictReasonInput')?.focus();
            }
        }
    };

    // Helper Feedback Toast
    function notify(title, message, type = 'info') {
        if (window.SayaBantuRealtime && typeof window.SayaBantuRealtime.showToast === 'function') {
            window.SayaBantuRealtime.showToast(title, message, type);
        } else if (typeof window.showToast === 'function') {
            window.showToast(title, message, type);
        } else {
            alert(`${title}: ${message}`);
        }
    }

    // Helper Invalidate Cache Navigasi agar saat kembali ke /admin/pesanan data selalu fresh
    function clearOrdersPageCache() {
        if (typeof window.invalidateAdminCache === 'function') {
            window.invalidateAdminCache('/admin/pesanan');
            window.invalidateAdminCache('/admin/dashboard');
        }
    }

    window.handleAdminConfirmPayment = async function(isFromModal = false) {
        if (!isFromModal) {
            openBuktiPembayaranModal();
            return;
        }

        const button = document.getElementById('btnAdminConfirmPayment');
        const modalBtn = document.getElementById('btnModalExecuteConfirmPayment');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Memverifikasi pembayaran...</span>';
        }
        if (modalBtn) {
            modalBtn.disabled = true;
            modalBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Memverifikasi pembayaran...</span>';
        }

        try {
            const orderId = getEffectiveOrderId();
            const endpoint = @json(route('admin.api.orders.confirm-payment', ['id' => '__ORDER__']))
                .replace('__ORDER__', encodeURIComponent(orderId));
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const result = await response.json();

            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Pembayaran gagal dikonfirmasi.');
            }

            clearOrdersPageCache();
            notify('Pembayaran Dikonfirmasi', result.message, 'success');
            window.location.reload();
        } catch (error) {
            notify('Konfirmasi Gagal', error.message || 'Terjadi kesalahan saat mengonfirmasi pembayaran.', 'error');
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-receipt text-xs"></i><span>Periksa Bukti &amp; Konfirmasi Pembayaran Customer</span>';
            }
            if (modalBtn) {
                modalBtn.disabled = false;
                modalBtn.innerHTML = '<i class="fa-solid fa-circle-check text-xs"></i><span>Konfirmasi Pembayaran Sesuai</span>';
            }
        }
    };

    // =========================================================
    // 2. AKSI: KONFIRMASI PESANAN
    // =========================================================
    window.handleConfirmOrder = async function() {
        const btn = document.getElementById('btnSubmitConfirmOrder');
        const btnText = document.getElementById('btnConfirmOrderText');
        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = 'Memproses...';

        try {
            const oid = getEffectiveOrderId();
            let res = await fetch(`/admin/api/orders/${encodeURIComponent(oid)}/confirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: oid, order_id: oid, code: oid })
            });
            let data = {};
            try { data = await res.json(); } catch(e) {}

            if (!res.ok || data.status !== 'success') {
                try {
                    const fbRes = await fetch(`/api/orders/${encodeURIComponent(oid)}/admin-confirm`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: oid, order_id: oid, code: oid })
                    });
                    const fbData = await fbRes.json();
                    if (fbData && fbData.success) {
                        data = { status: 'success', message: fbData.message };
                    }
                } catch(e2) {}
            }

            if (!data || data.status !== 'success') {
                try {
                    const fbRes2 = await fetch('/admin/api/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: oid, order_id: oid, code: oid, action: 'confirm' })
                    });
                    const fbData2 = await fbRes2.json();
                    if (fbData2 && fbData2.status === 'success') {
                        data = fbData2;
                    }
                } catch(e3) {}
            }

            if (data.status === 'success') {
                // Cross-role localStorage sync:
                try {
                    const cleanOid = String(oid || '').replace(/[^0-9A-Z]/gi, '');
                    const numOid = String(oid || '').replace(/[^0-9]/g, '');
                    const isTargetOrder = (item) => {
                        if (!item) return false;
                        const itemCode = String(item.code || item.id || '').replace(/[^0-9A-Z]/gi, '');
                        const itemNum = String(item.code || item.id || '').replace(/[^0-9]/g, '');
                        return (cleanOid && itemCode.includes(cleanOid)) || (cleanOid && cleanOid.includes(itemCode)) || (numOid && itemNum === numOid) || (item.id == oid) || (item.code === oid);
                    };

                    const isPaidNow = (data.order && (data.order.payment_status === 'terkonfirmasi' || data.order.status === 'terjadwal'))
                        || document.getElementById('btnActionConfirmPayment') === null;

                    const rawOrders = localStorage.getItem('sb_customer_orders');
                    if (rawOrders) {
                        const orders = JSON.parse(rawOrders);
                        const found = orders.find(isTargetOrder);
                        if (found) {
                            if (isPaidNow || found.status === 'terjadwal' || (found.step && found.step >= 8)) {
                                found.status = 'terjadwal';
                                found.step = 8;
                                found.escrow_status = 'held';
                            } else {
                                found.status = 'menunggu_pembayaran';
                                found.step = 7;
                            }
                            localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                        }
                    }
                    const rawActive = localStorage.getItem('sb_active_order');
                    if (rawActive) {
                        const act = JSON.parse(rawActive);
                        if (isTargetOrder(act)) {
                            if (isPaidNow || act.status === 'terjadwal' || (act.step && act.step >= 8)) {
                                act.status = 'terjadwal';
                                act.step = 8;
                                act.escrow_status = 'held';
                            } else {
                                act.status = 'menunggu_pembayaran';
                                act.step = 7;
                            }
                            localStorage.setItem('sb_active_order', JSON.stringify(act));
                        }
                    }
                    window.dispatchEvent(new Event('storage'));
                } catch(e) {}
                // Update Hero Badge
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-emerald-300/40 bg-emerald-500/20 px-3 py-1 text-[8.5px] font-semibold text-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                            Dikonfirmasi
                        </span>
                    `;
                }

                // Update Timeline Step 4
                const timelineContainer = document.getElementById('orderTimelineContainer');
                if (timelineContainer) {
                    const rows = timelineContainer.querySelectorAll('.timeline-row');
                    if (rows.length >= 4) {
                        const lastRow = rows[3];
                        const dot = lastRow.querySelector('div:first-child');
                        const pTitle = lastRow.querySelector('p:first-child');
                        const pTime = lastRow.querySelector('p:last-child');

                        if (dot) dot.className = 'relative z-10 mt-1 h-3 w-3 shrink-0 rounded-full bg-green-500 ring-4 ring-green-50 transition-all';
                        if (pTitle) {
                            pTitle.className = 'text-[10px] font-semibold text-slate-700';
                            pTitle.textContent = 'Pesanan Dikonfirmasi';
                        }
                        if (pTime) {
                            pTime.textContent = 'Dikonfirmasi oleh Admin • Baru saja';
                        }
                    }
                }

                // Update Tombol Tindakan Admin
                const confirmContainer = document.getElementById('btnConfirmOrderContainer');
                if (confirmContainer) {
                    confirmContainer.innerHTML = `
                        <button
                            type="button"
                            onclick="handleUnconfirmOrder()"
                            class="flex w-full items-center justify-center gap-2 rounded-[14px] border border-blue-200 bg-blue-50 px-4 py-3 text-[9px] font-bold text-[#17314A] shadow-2xs transition hover:bg-blue-100/70 cursor-pointer"
                            title="Klik jika ingin membatalkan konfirmasi pesanan"
                        >
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#17314A] text-[8px] text-white">✓</span>
                            <span>Pesanan Telah Dikonfirmasi</span>
                        </button>
                    `;
                }

                // Tutup banner konflik jika ada
                document.getElementById('conflictAlertBanner')?.classList.add('hidden');

                clearOrdersPageCache();
                notify('Pesanan Dikonfirmasi', `Pesanan ${currentOrderCode} telah disahkan dan siap dimulai mitra.`, 'success');

                if (window.SayaBantuRealtime && typeof window.SayaBantuRealtime.playNotificationChime === 'function') {
                    window.SayaBantuRealtime.playNotificationChime();
                }
            } else {
                notify('Gagal', data.message || 'Gagal mengonfirmasi pesanan.', 'error');
                if (btn) btn.disabled = false;
                if (btnText) btnText.textContent = 'Konfirmasi Pesanan';
            }
        } catch(err) {
            console.error(err);
            notify('Error', 'Koneksi gagal saat mengonfirmasi pesanan.', 'error');
            if (btn) btn.disabled = false;
            if (btnText) btnText.textContent = 'Konfirmasi Pesanan';
        }
    };

    // =========================================================
    // 2b. BATALKAN KONFIRMASI PESANAN
    // =========================================================
    window.handleUnconfirmOrder = async function() {
        try {
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(currentOrderId)}/unconfirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.status === 'success') {
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-amber-300/30 bg-amber-400/15 px-3 py-1 text-[8.5px] font-semibold text-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                            Diproses
                        </span>
                    `;
                }

                const confirmContainer = document.getElementById('btnConfirmOrderContainer');
                if (confirmContainer) {
                    confirmContainer.innerHTML = `
                        <button
                            type="button"
                            id="btnSubmitConfirmOrder"
                            onclick="handleConfirmOrder()"
                            class="w-full rounded-[14px] bg-[#0E1D31] hover:bg-[#17314A] px-4 py-3 text-[9px] font-semibold text-white shadow-sm transition hover:opacity-95 active:scale-[0.99] cursor-pointer"
                        >
                            <span id="btnConfirmOrderText">Konfirmasi Pesanan</span>
                        </button>
                    `;
                }

                // Step 4 timeline
                const timelineContainer = document.getElementById('orderTimelineContainer');
                if (timelineContainer) {
                    const rows = timelineContainer.querySelectorAll('.timeline-row');
                    if (rows.length >= 4) {
                        const lastRow = rows[3];
                        const dot = lastRow.querySelector('div:first-child');
                        const pTitle = lastRow.querySelector('p:first-child');
                        const pTime = lastRow.querySelector('p:last-child');

                        if (dot) dot.className = 'relative z-10 mt-1 h-3 w-3 shrink-0 rounded-full bg-slate-300 ring-4 ring-slate-50 transition-all';
                        if (pTitle) {
                            pTitle.className = 'text-[10px] font-semibold text-slate-400';
                            pTitle.textContent = 'Layanan dimulai';
                        }
                        if (pTime) {
                            pTime.textContent = 'Belum dimulai';
                        }
                    }
                }

                clearOrdersPageCache();
                notify('Konfirmasi Dibatalkan', 'Status pesanan kembali ke Diproses.', 'info');
            }
        } catch(e) {
            console.error(e);
        }
    };

    // =========================================================
    // 2.B AKSI: KONFIRMASI AKHIR PENYELESAIAN (PENUTUPAN PESANAN - TAHAP 11)
    // =========================================================
    window.handleConfirmCompletion = async function() {
        if (!confirm('Apakah Admin yakin ingin mengonfirmasi penyelesaian pekerjaan dan menutup pesanan ini secara tuntas?')) return;
        const btn = document.getElementById('btnSubmitConfirmCompletion');
        const btnText = document.getElementById('btnConfirmCompletionText');
        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = 'Menutup Pesanan...';

        try {
            const oid = getEffectiveOrderId();
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(oid)}/confirm-completion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: oid, order_id: oid, code: oid, action: 'confirm-completion' })
            });
            const data = await res.json();
            if (!res.ok || data.status !== 'success') {
                throw new Error(data.message || 'Penyelesaian pekerjaan belum berhasil dikonfirmasi.');
            }
            if (data.status === 'success') {
                // Cross-role localStorage sync:
                try {
                    const targetClean = oid;
                    const rawOrders = localStorage.getItem('sb_customer_orders');
                    if (rawOrders) {
                        const orders = JSON.parse(rawOrders);
                        const found = orders.find(o => (o.code || o.id || '').replace('#', '') === targetClean);
                        if (found) {
                            found.status = 'selesai';
                            found.step = 11;
                            localStorage.setItem('sb_customer_orders', JSON.stringify(orders));
                        }
                    }
                    const rawActive = localStorage.getItem('sb_active_order');
                    if (rawActive) {
                        const act = JSON.parse(rawActive);
                        if ((act.code || act.id || '').replace('#', '') === targetClean) {
                            act.status = 'selesai';
                            act.step = 11;
                            localStorage.setItem('sb_active_order', JSON.stringify(act));
                        }
                    }
                    window.dispatchEvent(new Event('storage'));
                } catch(e) {}
                if (typeof showToastNotification === 'function') {
                    showToastNotification('Pesanan Selesai!', 'Pesanan berhasil ditutup dan diselesaikan secara tuntas.', 'success');
                } else {
                    notify('Pesanan Selesai!', 'Pesanan berhasil ditutup dan diselesaikan secara tuntas.', 'success');
                }
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-emerald-300/30 bg-emerald-400/15 px-3 py-1 text-[8.5px] font-semibold text-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                            Selesai
                        </span>
                    `;
                }
                const banner = document.getElementById('completionAlertBanner');
                if (banner) {
                    banner.classList.remove('hidden', 'border-indigo-300', 'bg-gradient-to-r', 'from-indigo-50/95', 'via-sky-50/90', 'to-blue-50/90');
                    banner.classList.add('border-emerald-300', 'bg-emerald-50/95');
                    const heading = document.getElementById('completionNoticeHeading');
                    if (heading) heading.textContent = 'Pemberitahuan: Pekerjaan Telah Selesai & Ditutup';
                    const badge = document.getElementById('completionNoticeBadge');
                    if (badge) {
                        badge.className = 'text-[8px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900';
                        badge.textContent = 'Tuntas';
                    }
                    const iconBox = document.getElementById('completionNoticeIcon');
                    if (iconBox) {
                        iconBox.className = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white text-sm font-bold shadow-xs';
                        iconBox.innerHTML = '<i class="fa-solid fa-check-double"></i>';
                    }
                    const desc = document.getElementById('completionNoticeDesc');
                    if (desc) {
                        desc.className = 'mt-1 text-[9.5px] leading-relaxed text-emerald-800';
                        desc.innerHTML = 'Customer dan Mitra telah menyelesaikan pesanan ini. Rekapitulasi pekerjaan telah tuntas dan dana telah diteruskan ke Mitra.';
                    }
                    const actionContainer = document.getElementById('completionNoticeActionContainer');
                    if (actionContainer) actionContainer.classList.add('hidden');
                }
                const btnContainer = document.getElementById('btnConfirmCompletionContainer');
                if (btnContainer) {
                    btnContainer.innerHTML = `
                        <button type="button" disabled class="w-full rounded-[14px] bg-emerald-700/60 px-4 py-3 text-[9px] font-semibold text-white shadow-sm flex items-center justify-center gap-1.5 cursor-not-allowed">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Pesanan Telah Selesai & Ditutup</span>
                        </button>
                    `;
                }
                clearOrdersPageCache();
            }
        } catch (err) {
            console.error(err);
            notify('Konfirmasi Gagal', err.message || 'Gagal mengonfirmasi penyelesaian pekerjaan.', 'error');
        } finally {
            if (btn) btn.disabled = false;
            if (btnText) btnText.textContent = 'Konfirmasi Penyelesaian Pesanan';
        }
    };

    // =========================================================
    // 3. AKSI: TANDAI SEBAGAI KONFLIK
    // =========================================================
    window.submitMarkConflict = async function() {
        const btn = document.getElementById('btnSubmitMarkConflict');
        let selectedReason = '';
        const presetRadio = document.querySelector('input[name="conflictPreset"]:checked');
        if (presetRadio) {
            if (presetRadio.value === 'custom') {
                selectedReason = document.getElementById('customConflictReasonInput')?.value.trim() || 'Ditandai konflik oleh Admin';
            } else {
                selectedReason = presetRadio.value;
            }
        }

        if (btn) btn.disabled = true;

        try {
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(currentOrderId)}/conflict`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: selectedReason })
            });
            const data = await res.json();

            if (data.status === 'success') {
                closeModal('markConflictModal');

                // Tampilkan Banner Konflik
                const banner = document.getElementById('conflictAlertBanner');
                if (banner) {
                    banner.classList.remove('hidden');
                    const reasonEl = document.getElementById('conflictReasonLabel');
                    if (reasonEl) reasonEl.textContent = selectedReason;
                    const timeEl = document.getElementById('conflictTimeLabel');
                    if (timeEl) timeEl.textContent = 'Baru saja';
                }

                // Update Hero Badge
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-red-300/40 bg-red-500/20 px-3 py-1 text-[8.5px] font-semibold text-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5 animate-pulse"></span>
                            Konflik
                        </span>
                    `;
                }

                // Update Tombol Konflik menjadi "Selesaikan Mediasi Konflik"
                const conflictContainer = document.getElementById('btnConflictContainer');
                if (conflictContainer) {
                    conflictContainer.innerHTML = `
                        <button
                            type="button"
                            onclick="openResolveConflictModal()"
                            class="w-full flex items-center justify-center gap-2 rounded-[14px] border border-emerald-300 bg-emerald-50 px-4 py-3 text-[9px] font-bold text-emerald-700 transition hover:bg-emerald-100 active:scale-[0.99] cursor-pointer shadow-2xs"
                        >
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                            <span>Selesaikan Mediasi Konflik</span>
                        </button>
                    `;
                }

                clearOrdersPageCache();
                notify('Status Konflik', `Pesanan ${currentOrderCode} telah ditandai konflik mediasi.`, 'error');
            } else {
                notify('Gagal', data.message || 'Gagal menandai konflik.', 'error');
            }
        } catch(err) {
            console.error(err);
            notify('Error', 'Terjadi kesalahan jaringan.', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    };

    // =========================================================
    // 4. AKSI: SELESAIKAN MEDIASI KONFLIK
    // =========================================================
    window.submitResolveConflict = async function() {
        const btn = document.getElementById('btnSubmitResolveConflict');
        if (btn) btn.disabled = true;

        try {
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(currentOrderId)}/resolve-conflict`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.status === 'success') {
                closeModal('resolveConflictModal');

                // Sembunyikan Banner Konflik
                document.getElementById('conflictAlertBanner')?.classList.add('hidden');

                // Update Hero Badge
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-amber-300/30 bg-amber-400/15 px-3 py-1 text-[8.5px] font-semibold text-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                            Diproses
                        </span>
                    `;
                }

                // Kembalikan Tombol Konflik
                const conflictContainer = document.getElementById('btnConflictContainer');
                if (conflictContainer) {
                    conflictContainer.innerHTML = `
                        <button
                            type="button"
                            onclick="openMarkConflictModal()"
                            class="w-full rounded-[14px] border border-red-200 bg-red-50/70 px-4 py-3 text-[9px] font-semibold text-red-600 transition hover:bg-red-100 active:scale-[0.99] cursor-pointer"
                        >
                            Tandai sebagai Konflik
                        </button>
                    `;
                }

                clearOrdersPageCache();
                notify('Mediasi Selesai', `Status pesanan ${currentOrderCode} telah dipulihkan ke Diproses.`, 'success');
            } else {
                notify('Gagal', data.message || 'Gagal menyelesaikan konflik.', 'error');
            }
        } catch(err) {
            console.error(err);
            notify('Error', 'Gagal memproses penyelesaian konflik.', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    };

    // =========================================================
    // 6. AKSI: SIMPAN CATATAN ADMIN
    // =========================================================
    window.handleSaveOrderNotes = async function() {
        const input = document.getElementById('adminOrderNotesInput');
        const btn = document.getElementById('btnSaveAdminNotes');
        const btnText = document.getElementById('btnSaveAdminNotesText');
        const savedLabel = document.getElementById('notesStatusSaved');
        const notes = input ? input.value : '';

        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = 'Menyimpan...';

        try {
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(currentOrderId)}/notes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notes })
            });
            const data = await res.json();

            if (data.status === 'success') {
                if (savedLabel) {
                    savedLabel.classList.remove('hidden');
                    setTimeout(() => savedLabel.classList.add('hidden'), 3500);
                }
                notify('Catatan Disimpan', 'Catatan internal admin telah diperbarui.', 'success');
            } else {
                notify('Gagal', data.message || 'Gagal menyimpan catatan.', 'error');
            }
        } catch(err) {
            console.error(err);
            notify('Error', 'Gagal menghubungi server.', 'error');
        } finally {
            if (btn) btn.disabled = false;
            if (btnText) btnText.textContent = 'Simpan Catatan';
        }
    };

    // =========================================================
    // 7. AKSI: TOLAK / BATALKAN PESANAN OLEH ADMIN (SKENARIO B)
    // =========================================================
    window.submitAdminCancelOrder = async function() {
        const btn = document.getElementById('btnSubmitAdminCancelOrder');
        const selectedRadio = document.querySelector('input[name="adminCancelPreset"]:checked');
        let reason = selectedRadio ? selectedRadio.value : '';
        if (reason === 'custom') {
            reason = document.getElementById('customCancelReasonInput')?.value.trim();
        }
        if (!reason) {
            notify('Perhatian', 'Alasan pembatalan pesanan wajib dipilih atau diisi.', 'warning');
            return;
        }

        if (btn) btn.disabled = true;

        try {
            const res = await fetch(`/admin/api/orders/${encodeURIComponent(currentOrderId)}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason })
            });
            const data = await res.json();

            if (data.success) {
                closeModal('cancelOrderModal');
                clearOrdersPageCache();
                notify('Pesanan Dibatalkan', data.message || `Pesanan ${currentOrderCode} berhasil dibatalkan.`, 'info');

                // Update Hero Badge
                const heroBadge = document.getElementById('orderHeroStatusBadgeContainer');
                if (heroBadge) {
                    heroBadge.innerHTML = `
                        <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-rose-300/40 bg-rose-500/20 px-3 py-1 text-[8.5px] font-semibold text-rose-200">
                            <i class="fa-solid fa-ban mr-1.5 text-rose-300"></i> Dibatalkan Admin
                        </span>
                    `;
                }

                // Update Action Container
                const cancelBtnContainer = document.getElementById('btnCancelOrderContainer');
                if (cancelBtnContainer) {
                    cancelBtnContainer.innerHTML = `
                        <div class="p-3 rounded-[14px] bg-rose-50 border border-rose-200 text-center text-[9px] font-bold text-rose-700">
                            <i class="fa-solid fa-ban mr-1"></i> Pesanan Dibatalkan oleh Admin (Refund Diproses)
                        </div>
                    `;
                }

                clearOrdersPageCache();
            } else {
                notify('Gagal', data.message || 'Gagal membatalkan pesanan.', 'error');
            }
        } catch(err) {
            console.error(err);
            notify('Error', 'Terjadi kendala saat membatalkan pesanan.', 'error');
        } finally {
            if (btn) btn.disabled = false;
        }
    };
})();
</script>
@endpush
@endsection
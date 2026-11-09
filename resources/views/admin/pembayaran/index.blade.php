@extends('layouts.admin')

@section('title', 'Pembayaran - SayaBantu.com')
@section('header-gap-style', '0.6cm')
@section('page-title', 'Pembayaran')
@section('page-subtitle', 'Pantau transaksi serta status pembayaran yang berkaitan dengan suruhan')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}

@section('hero')


    {{-- =====================================================
         SALDO ADMIN
    ====================================================== --}}

    <section class="pembayaran-balance" style="padding-top: 0.6cm; padding-bottom: 0;">

        <div class="balance-card">

            <div class="balance-circle-one"></div>

            <div class="balance-circle-two"></div>


            <div class="balance-content">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="balance-label">
                            Saldo Admin
                        </p>

                        <p class="balance-subtitle">
                            Total dana terverifikasi
                        </p>

                    </div>

                    <div
                        class="
                            flex
                            h-8
                            w-8
                            items-center
                            justify-center
                            rounded-full
                            bg-white/15
                            text-xs
                            font-bold
                        "
                    >
                        Rp
                    </div>

                </div>


                <p class="balance-value" data-rt-stat="admin_balance">
                    Rp {{ number_format($saldoAdmin ?? 0, 0, ',', '.') }}
                </p>


                <div class="balance-footer">

                    <p class="balance-description">
                        Fee platform 10% dari pembayaran terverifikasi
                    </p>

                    <span class="balance-active">
                        Aktif
                    </span>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         RINGKASAN DANA (CENTERED)
    ====================================================== --}}

    <section class="pembayaran-summary" style="margin-top: 10px;">

        <div class="summary-grid-payment">


            {{-- DANA DITAHAN --}}
            <button
                type="button"
                onclick="openPaymentSummaryModal('ditahan')"
                class="summary-payment-card summary-payment-yellow text-center flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg focus:outline-hidden group"
                title="Klik untuk melihat rincian Dana Ditahan"
                aria-label="Buka detail Dana Ditahan"
            >
                <p class="summary-payment-label text-center flex items-center justify-center gap-1 group-hover:text-amber-200 transition-colors">
                    <span>Dana Ditahan</span>
                    <i class="fa-solid fa-circle-info text-[8px] opacity-75"></i>
                </p>
                <p class="summary-payment-number text-center" data-rt-stat="held_funds">
                    Rp {{ number_format($totalDanaDitahan ?? 0, 0, ',', '.') }}
                </p>
                <p class="summary-payment-description text-yellow-700 text-center">
                    Terverifikasi, belum dicairkan
                </p>
            </button>

            {{-- DANA MASUK --}}
            <button
                type="button"
                onclick="openPaymentSummaryModal('masuk')"
                class="summary-payment-card summary-payment-green text-center flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg focus:outline-hidden group"
                title="Klik untuk melihat rincian Dana Masuk"
                aria-label="Buka detail Dana Masuk"
            >
                <p class="summary-payment-label text-center flex items-center justify-center gap-1 group-hover:text-emerald-200 transition-colors">
                    <span>Dana Masuk</span>
                    <i class="fa-solid fa-circle-info text-[8px] opacity-75"></i>
                </p>
                <p class="summary-payment-number text-center" data-rt-stat="inflow_funds">
                    Rp {{ number_format($totalDanaMasuk ?? 0, 0, ',', '.') }}
                </p>
                <p class="summary-payment-description text-green-600 text-center">
                Pembayaran terverifikasi
                </p>
            </button>

            {{-- DANA KELUAR --}}
            <button
                type="button"
                onclick="openPaymentSummaryModal('keluar')"
                class="summary-payment-card summary-payment-blue text-center flex flex-col items-center justify-center cursor-pointer transition-all hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg focus:outline-hidden group"
                title="Klik untuk melihat rincian Dana Keluar"
                aria-label="Buka detail Dana Keluar"
            >
                <p class="summary-payment-label text-center flex items-center justify-center gap-1 group-hover:text-sky-200 transition-colors">
                    <span>Dana Keluar</span>
                    <i class="fa-solid fa-circle-info text-[8px] opacity-75"></i>
                </p>
                <p class="summary-payment-number text-center" data-rt-stat="outflow_funds">
                    Rp {{ number_format($totalDanaKeluar ?? 0, 0, ',', '.') }}
                </p>
                <p class="summary-payment-description text-blue-600 text-center">
                    Dibayarkan ke mitra
                </p>
            </button>

        </div>

    </section>


    {{-- =====================================================
         STATUS TRANSAKSI SEKALIGUS FILTER (DENGAN "SEMUA")
    ====================================================== --}}

    <section class="pembayaran-status pb-2" style="margin-top: 0.6cm;">

        <h3 class="pembayaran-status-title">
            Riwayat Transaksi
        </h3>

        <p class="pembayaran-status-subtitle">
            Daftar pembayaran terbaru
        </p>


        <div class="status-payment-grid" style="margin-top: 0.6cm; gap: 0.6cm;">

            {{-- SEMUA --}}
            <button
                type="button"
                onclick="filterPayment('semua', this)"
                class="status-payment-card active-status cursor-pointer transition-all w-full text-center"
                data-status="semua"
                title="Semua Transaksi"
            >
                <p class="status-payment-label">
                    Semua
                </p>
                <p class="status-payment-number" id="payCountSemua">
                    {{ $countSemua ?? 0 }}
                </p>
            </button>

            {{-- MENUNGGU --}}
            <button
                type="button"
                onclick="filterPayment('menunggu', this)"
                class="status-payment-card cursor-pointer transition-all w-full text-center"
                data-status="menunggu"
                title="Transaksi Menunggu"
            >
                <p class="status-payment-label">
                    Menunggu
                </p>
                <p class="status-payment-number" data-rt-stat="payment_pending" id="payCountMenunggu">
                    {{ $countMenunggu ?? 0 }}
                </p>
            </button>

            {{-- BERHASIL --}}
            <button
                type="button"
                onclick="filterPayment('berhasil', this)"
                class="status-payment-card cursor-pointer transition-all w-full text-center"
                data-status="berhasil"
                title="Transaksi Berhasil"
            >
                <p class="status-payment-label">
                    Berhasil
                </p>
                <p class="status-payment-number" data-rt-stat="payment_success" id="payCountBerhasil">
                    {{ $countBerhasil ?? 0 }}
                </p>
            </button>

            {{-- GAGAL --}}
            <button
                type="button"
                onclick="filterPayment('gagal', this)"
                class="status-payment-card cursor-pointer transition-all w-full text-center"
                data-status="gagal"
                title="Transaksi Gagal"
            >
                <p class="status-payment-label">
                    Gagal
                </p>
                <p class="status-payment-number" data-rt-stat="payment_failed" id="payCountGagal">
                    {{ $countGagal ?? 0 }}
                </p>
            </button>

        </div>

    </section>

@endsection



{{-- =========================================================
     MAIN CONTENT
========================================================= --}}

@section('content')

<div class="pembayaran-main" style="margin-top: 0.6cm;">

    {{-- Hidden counter for JS filter state compatibility --}}
    <span id="visiblePaymentCount" class="hidden"></span>


    {{-- =====================================================
         PAYMENT LIST
    ====================================================== --}}

    <div
        id="payment-list"
        style="display: flex; flex-direction: column; gap: 0.6cm;"
    >
        {{-- DYNAMIC PAYMENTS DARI DATABASE --}}
        @if(isset($orders) && count($orders) > 0)
            @foreach($orders as $o)
                @php
                    $cleanCode = str_replace('#', '', $o->code);
                    $nom = $o->nominal > 0 ? $o->nominal : (int) preg_replace('/[^0-9]/', '', (string)$o->biaya);
                    $adminFee = $o->admin_fee > 0 ? $o->admin_fee : (int) round($nom * 0.10);
                    $mitraFee = $o->mitra_fee > 0 ? $o->mitra_fee : (int) round($nom * 0.90);
                    $isRefund = in_array($o->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan']) || $o->escrow_status === 'refunded';
                    $isDiteruskan = in_array($o->escrow_status, ['released_to_mitra', 'released']);
                    $isPendingVerification = $o->payment_is_pending;
                    $canReleaseEscrow = $o->payment_verified_at !== null
                        && $o->escrow_status === 'held'
                        && $o->status === 'selesai'
                        && ! $isRefund;

                    if ($isRefund) {
                        $statusGroup = 'gagal';
                        $escrowLabel = 'Dikembalikan ke Customer (Refund)';
                        $badgeClass = 'payment-status-failed';
                    } elseif ($isDiteruskan) {
                        $statusGroup = 'berhasil';
                        $escrowLabel = 'Diteruskan ke Mitra (90%)';
                        $badgeClass = 'payment-status-success';
                    } elseif ($isPendingVerification) {
                        $statusGroup = 'menunggu';
                        $escrowLabel = 'Menunggu Konfirmasi Admin';
                        $badgeClass = 'payment-status-waiting';
                    } else {
                        $statusGroup = 'berhasil';
                        $escrowLabel = 'Tercatat di Escrow';
                        $badgeClass = 'payment-status-success';
                    }
                    $paymentType = $isRefund ? 'refund' : ($isPendingVerification ? 'pending' : ($isDiteruskan ? 'keluar' : 'ditahan'));
                    $holdReason = $o->escrow_hold_reason ?: 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai';
                @endphp
                <div class="payment-card payment-item cursor-pointer" data-status="{{ $statusGroup }}" data-payment-type="{{ $paymentType }}" onclick="if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.pembayaran.detail', $cleanCode) }}'); } else { window.location.href = '{{ route('admin.pembayaran.detail', $cleanCode) }}'; }">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h4 class="payment-number font-mono text-xs font-black">#PAY-{{ $cleanCode }}</h4>
                                <span class="text-[9px] px-1.5 py-0.2 rounded-md font-mono bg-slate-100 text-slate-900 font-bold border border-slate-200">#{{ $cleanCode }}</span>
                            </div>
                            <p class="payment-order mt-0.5 text-[11px]">{{ $o->layanan }} • <strong class="text-slate-950 font-semibold">{{ $o->customer_name }}</strong></p>
                        </div>
                        <span class="payment-status-badge {{ $badgeClass }}">
                            {{ $escrowLabel }}
                        </span>
                    </div>

                    {{-- RINGKASAN BAGI HASIL 10% ADMIN VS 90% MITRA --}}
                    <div class="mt-2.5 p-2 rounded-xl bg-white/[0.05] border border-white/10 grid grid-cols-3 gap-2 text-center text-[10px]">
                        <div>
                            <span class="text-slate-900 block text-[8.5px] uppercase font-bold">Total Pembayaran</span>
                            <strong class="font-mono text-slate-950 text-xs">Rp {{ number_format($nom, 0, ',', '.') }}</strong>
                        </div>
                        <div class="border-x border-white/10">
                            <span class="text-emerald-800 block text-[8.5px] uppercase font-bold">Fee Admin (10%)</span>
                            <strong class="font-mono text-emerald-800 text-xs">Rp {{ number_format($adminFee, 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <span class="text-sky-800 block text-[8.5px] uppercase font-bold">Hak Mitra (90%)</span>
                            <strong class="font-mono text-sky-800 text-xs">Rp {{ number_format($mitraFee, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    {{-- JIKA DITAHAN: ALASAN JELAS PENAHANAN DANA DITAMPILKAN SECARA EKSPLISIT --}}
                    @if($isPendingVerification)
                        <div class="mt-2.5 p-2.5 rounded-xl bg-amber-100 border border-amber-400 text-[10px] leading-relaxed">
                            <span class="font-extrabold text-amber-950">Customer melaporkan pembayaran.</span>
                            <span class="font-semibold text-amber-900"> Konfirmasi penerimaan dana hanya dari halaman detail suruhan.</span>
                        </div>
                    @endif

                    @if(!$isDiteruskan && !$isRefund && !$isPendingVerification)
                        <div class="mt-2.5 p-2.5 rounded-xl bg-amber-100 border border-amber-400 text-[10.5px]">
                            <div class="flex items-start gap-1.5">
                                <i class="fa-solid fa-shield-halved text-amber-800 mt-0.5 text-xs shrink-0"></i>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-amber-950 block text-[9px] uppercase tracking-wider">Dana Otomatis Ditahan di Escrow:</span>
                                    <p class="text-amber-900 font-semibold leading-snug mt-0.5">
                                        {{ $holdReason }}
                                    </p>
                                    @if($o->status !== 'selesai')
                                        <p class="text-amber-900 font-semibold leading-snug mt-1">Penerusan aktif setelah Customer dan Admin mengonfirmasi pekerjaan selesai.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-2.5 pt-2 border-t border-white/10 flex items-center justify-between text-xs">
                        <div>
                            <span class="payment-method-label text-[9px] text-slate-900 block">Metode Pembayaran</span>
                            <p class="payment-method text-[10.5px] text-slate-950 font-semibold">{{ $o->payment_method ?: 'Metode belum tercatat' }}</p>
                            @if($o->payment_submitted_at)
                                <span class="text-[9px] text-slate-900">Dilaporkan {{ $o->payment_submitted_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="payment-total-label text-[9px] text-slate-900 block">Nominal Escrow</span>
                            <p class="payment-total font-mono font-bold text-xs text-slate-950">Rp {{ number_format($nom, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Dana hanya dapat diteruskan setelah Customer dan Admin mengonfirmasi pekerjaan selesai. --}}
                    @if($isPendingVerification)
                        <div class="mt-3 pt-2.5 border-t border-white/10" onclick="event.stopPropagation()">
                            <button
                                type="button"
                                onclick="event.stopPropagation(); if (typeof window.adminSpaNavigateTo === 'function') { window.adminSpaNavigateTo('{{ route('admin.pembayaran.detail', $cleanCode) }}'); } else { window.location.href = '{{ route('admin.pembayaran.detail', $cleanCode) }}'; }"
                                class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-[10.5px] flex items-center justify-center gap-1.5 transition-all shadow-xs cursor-pointer"
                            >
                                <i class="fa-solid fa-magnifying-glass-dollar text-[10px]"></i>
                                <span>Lihat Detail Pembayaran</span>
                            </button>
                        </div>
                    @elseif(!$isDiteruskan && $canReleaseEscrow)
                        <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center gap-2" onclick="event.stopPropagation()">
                            <button
                                type="button"
                                onclick="event.stopPropagation(); openActionModalTeruskan('{{ $cleanCode }}', '{{ addslashes($o->mitra_name ?: 'Mitra') }}', {{ $nom }}, {{ $adminFee }}, {{ $mitraFee }})"
                                class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-extrabold text-[10.5px] flex items-center justify-center gap-1.5 transition-all shadow-xs cursor-pointer"
                            >
                                <i class="fa-solid fa-paper-plane text-[9px]"></i>
                                <span>Teruskan ke Mitra (Rp {{ number_format($mitraFee, 0, ',', '.') }})</span>
                            </button>
                        </div>
                    @elseif($isDiteruskan)
                        <div class="mt-2.5 pt-2 border-t border-white/10 flex items-center justify-between text-[10px]" onclick="event.stopPropagation()">
                            <span class="text-emerald-800 font-bold flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                Telah disalurkan ke {{ $o->mitra_name ?: 'Mitra' }} (Hak 90%: Rp {{ number_format($mitraFee, 0, ',', '.') }})
                            </span>
                            <a href="{{ route('admin.pembayaran.detail', $cleanCode) }}" class="text-sky-800 hover:underline font-bold text-[9.5px]">
                                Rincian <i class="fa-solid fa-chevron-right text-[7px]"></i>
                            </a>
                        </div>
                    @elseif($isRefund)
                        <div class="mt-2.5 pt-2 border-t border-white/10 text-[10px] font-bold text-rose-700">Dana dikembalikan kepada Customer.</div>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- CONTAINER KOSONG UNTUK PENAMBAHAN REALTIME --}}
        <div id="dynamicPaymentContainer" style="display: flex; flex-direction: column; gap: 0.6cm;"></div>
    </div>


</div>

@endsection

{{-- =========================================================
     MODAL DETAIL RINGKASAN DANA (DANA DITAHAN, MASUK, KELUAR)
========================================================= --}}
@section('modals')
<div id="paymentSummaryModal" class="sb-overlay-backdrop fixed inset-0 z-[200] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    {{-- Backdrop Hitam Semi-Transparan --}}
    <div class="sb-backdrop-click absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closePaymentSummaryModal()"></div>

    {{-- Frame Mobile Bottom Sheet --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="paymentSummaryPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[90vh] border-t border-white/15 translate-y-full transition-transform duration-200 overflow-hidden">

            {{-- 1. HEADER MODAL --}}
            <div class="px-5 pt-3.5 pb-3 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div id="summaryModalIconBox" class="w-10 h-10 rounded-xl flex items-center justify-center text-base border shadow-inner transition-colors">
                            <i id="summaryModalIcon" class="fa-solid fa-vault"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white tracking-tight" id="summaryModalTitle">Detail Ringkasan Dana</h3>
                                <span id="summaryModalBadge" class="px-2 py-0.5 rounded-full text-[9px] font-bold">
                                    Escrow
                                </span>
                            </div>
                            <p class="text-[10px] text-gray-300 font-medium mt-0.5" id="summaryModalSubtitle">Informasi &amp; rincian transaksi terkait</p>
                        </div>
                    </div>
                    <button type="button" onclick="closePaymentSummaryModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer" aria-label="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                {{-- PILL TABS SWITCHER DALAM MODAL --}}
                <div class="grid grid-cols-4 gap-1 p-1 bg-white/[0.06] rounded-xl mt-3 text-[9.5px] font-bold">
                    <button type="button" id="tabSummaryDitahan" onclick="switchSummaryModalTab('ditahan')" class="py-1.5 rounded-lg transition-all text-center">
                        Ditahan
                    </button>
                    <button type="button" id="tabSummaryMasuk" onclick="switchSummaryModalTab('masuk')" class="py-1.5 rounded-lg transition-all text-center">
                        Dana Masuk
                    </button>
                    <button type="button" id="tabSummaryKeluar" onclick="switchSummaryModalTab('keluar')" class="py-1.5 rounded-lg transition-all text-center">
                        Ke Mitra
                    </button>
                    <button type="button" id="tabSummaryRefund" onclick="switchSummaryModalTab('refund')" class="py-1.5 rounded-lg transition-all text-center">
                        Refund
                    </button>
                </div>
            </div>

            {{-- 2. KONTEN DETAIL (SCROLLABLE) --}}
            <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3.5 text-xs custom-scrollbar">

                {{-- HIGHLIGHT TOTAL KARTU BESAR --}}
                <div id="summaryHighlightBox" class="p-3.5 rounded-2xl border transition-all relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-semibold text-gray-300" id="summaryHighlightLabel">Total Saldo Ditahan</span>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-white/15 text-white" id="summaryHighlightPill">Escrow Terproteksi</span>
                    </div>
                    <div class="mt-1 flex items-baseline gap-2">
                        <h2 class="text-2xl font-black text-white tracking-tight" id="summaryHighlightValue">Rp2.150.000</h2>
                        <span class="text-[10.5px] text-gray-300" id="summaryHighlightNote">dari 8 pesanan</span>
                    </div>
                    <p class="text-[10px] text-gray-300/90 mt-1 leading-relaxed" id="summaryHighlightDesc">
                        Dana pembayaran customer yang masih ditahan di sistem rekber SayaBantu hingga pekerjaan disetujui.
                    </p>
                </div>

                {{-- KOTAK PENJELASAN SISTEM --}}
                <div class="p-3 rounded-xl bg-white/[0.04] border border-white/10 flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div class="text-[10.5px] leading-relaxed">
                        <span class="font-bold text-white block" id="summaryInfoTitle">Apa itu Dana Ditahan?</span>
                        <p class="text-gray-300 mt-0.5" id="summaryInfoText">
                            Uang pembayaran customer yang diamankan sistem. Mitra belum dapat mencairkan dana ini sampai suruhan tuntas dan customer tidak mengajukan komplain.
                        </p>
                    </div>
                </div>

                {{-- STATISTIK & BREAKDOWN METRIK --}}
                <div>
                    <h4 class="text-[11px] font-bold text-white mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-pie text-sky-400 text-xs"></i> Rincian &amp; Komponen
                    </h4>
                    <div class="grid grid-cols-2 gap-2" id="summaryMetricGrid">
                        {{-- Injected dynamically --}}
                    </div>
                </div>

                {{-- DAFTAR TRANSAKSI CONTOH TERKAIT --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-[11px] font-bold text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-sky-400 text-xs"></i>
                            <span id="summaryListTitle">Transaksi Terkait</span>
                        </h4>
                        <span class="text-[9.5px] text-gray-400" id="summaryListCounter">4 transaksi terbaru</span>
                    </div>

                    <div class="space-y-2" id="summaryListItems">
                        {{-- Injected dynamically --}}
                    </div>
                </div>

            </div>

            {{-- 3. FOOTER FIXED --}}
            <div class="p-4 border-t border-white/10 bg-[#0E1D31] shrink-0 flex items-center gap-2">
                <button
                    type="button"
                    id="btnApplySummaryFilter"
                    onclick="applySummaryCategoryFilter()"
                    class="flex-1 py-2.5 px-3 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-md active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span id="btnApplySummaryFilterText">Lihat Transaksi Ini di Daftar</span>
                </button>
                <button
                    type="button"
                    onclick="closePaymentSummaryModal()"
                    class="py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-bold text-xs transition-all active:scale-95 cursor-pointer"
                >
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

{{-- MODAL AKSI TERUSKAN DANA KE MITRA (90%) --}}
<div id="actionModalTeruskan" class="sb-overlay-backdrop fixed inset-0 z-[220] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closeActionModalTeruskan()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="actionPanelTeruskan" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[90vh] border-t border-white/15 translate-y-full transition-transform duration-200 overflow-hidden">
            <div class="px-5 pt-3 pb-3 border-b border-white/10 shrink-0">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-base">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight">Teruskan Dana ke Mitra</h3>
                            <p class="text-[10px] text-gray-300 font-medium" id="modalTeruskanCode">Suruhan #SB-XXXX</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeActionModalTeruskan()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="p-5 space-y-3.5 text-xs overflow-y-auto custom-scrollbar">
                {{-- KARTU HERO PENERUSAN --}}
                <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/20 via-teal-500/10 to-transparent border border-emerald-500/30 text-center">
                    <span class="text-[9.5px] font-bold text-emerald-300 uppercase tracking-wider block">HAK PENCAIRAN MITRA (90%)</span>
                    <h2 class="text-2xl font-black text-white mt-1 font-mono tracking-tight" id="modalTeruskanMitraFee">Rp 0</h2>
                    <span class="inline-block mt-1 text-[9.5px] text-gray-300">Penerima: <strong class="text-white" id="modalTeruskanMitraName">Mitra</strong></span>
                </div>

                {{-- RINCIAN PEMBAGIAN SISTEM --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Total Pembayaran Customer</span>
                        <strong class="font-mono text-white" id="modalTeruskanTotal">Rp 0</strong>
                    </div>
                    <div class="flex items-center justify-between text-emerald-300 font-semibold">
                        <span>Saldo Admin Platform SayaBantu (10%)</span>
                        <strong class="font-mono" id="modalTeruskanAdminFee">Rp 0</strong>
                    </div>
                    <div class="pt-2 border-t border-white/10 flex items-center justify-between text-sky-300 font-bold">
                        <span>Dana Bersih Ditransfer ke Rekening Mitra (90%)</span>
                        <strong class="font-mono" id="modalTeruskanFinalMitra">Rp 0</strong>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[10.5px] text-emerald-200 leading-relaxed flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 text-xs shrink-0"></i>
                    <span>Tindakan ini akan mengakhiri penahanan escrow dan menyalurkan saldo 90% ke rekening mitra. 10% saldo admin akan tersimpan permanen sebagai fee platform.</span>
                </div>
            </div>

            <div class="p-4 border-t border-white/10 bg-[#0E1D31] shrink-0 flex items-center gap-2">
                <button
                    type="button"
                    onclick="closeActionModalTeruskan()"
                    class="py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 font-bold text-xs transition-all active:scale-95 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    id="btnExecuteDirectPayout"
                    onclick="submitDirectPayout()"
                    class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Uang ke Mitra Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection



{{-- =========================================================
     SCRIPT FILTER
========================================================= --}}

@push('scripts')

<script>

var paymentSummaryRows = @json($paymentSummaryRows);
var formatPaymentSummaryCurrency = amount => `Rp${new Intl.NumberFormat('id-ID').format(amount)}`;
var paymentRealtimeKey = null;

document.addEventListener('sb:realtime:state', async event => {
    const stats = event.detail?.stats || {};
    const nextKey = [
        stats.payment_pending || 0,
        stats.total_pembayaran || 0,
        stats.payment_released || 0,
    ].join(':');

    if (paymentRealtimeKey === null) {
        paymentRealtimeKey = nextKey;
        return;
    }

    if (paymentRealtimeKey === nextKey) {
        return;
    }

    paymentRealtimeKey = nextKey;

    try {
        const response = await fetch(window.location.pathname, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) {
            throw new Error(`Payment list refresh failed with status ${response.status}.`);
        }

        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const updatedList = page.getElementById('payment-list');
        const paymentList = document.getElementById('payment-list');
        if (!updatedList || !paymentList) {
            throw new Error('Payment list container was not found in the refreshed page.');
        }

        paymentList.replaceChildren(...Array.from(updatedList.childNodes, node => document.importNode(node, true)));

        ['payCountSemua', 'payCountMenunggu', 'payCountBerhasil', 'payCountGagal'].forEach(id => {
            const currentCount = document.getElementById(id);
            const updatedCount = page.getElementById(id);
            if (currentCount && updatedCount) {
                currentCount.textContent = updatedCount.textContent;
            }
        });

        document.querySelectorAll('[data-rt-stat]').forEach(currentValue => {
            const key = currentValue.dataset.rtStat;
            const updatedValue = page.querySelector(`[data-rt-stat="${key}"]`);
            if (updatedValue) {
                currentValue.textContent = updatedValue.textContent;
            }
        });

        const selectedStatus = document.querySelector('.status-payment-card.active-status')?.dataset.status || 'semua';
        filterPayment(selectedStatus, document.querySelector(`.status-payment-card[data-status="${selectedStatus}"]`));
    } catch (error) {
        console.error('Unable to refresh admin payments after a realtime update.', error);
    }
});

function filterPayment(status, cardEl) {

    const items =
        document.querySelectorAll('.payment-item');

    const cards =
        document.querySelectorAll('.status-payment-card');

    const countElement =
        document.getElementById('visiblePaymentCount');

    let visibleCount = 0;


    /* =====================================================
       RESET SEMUA FILTER
    ===================================================== */

    cards.forEach(c => {
        c.classList.remove('active-status');
        c.classList.remove('active-filter');
    });


    /* =====================================================
       AKTIFKAN FILTER YANG DIPILIH
    ===================================================== */

    if (cardEl) {
        cardEl.classList.add('active-status');
    } else {
        const cardStatus = ['ditahan', 'masuk', 'keluar'].includes(status) ? 'berhasil' : status;
        const target = document.querySelector('.status-payment-card[data-status="' + cardStatus + '"]');
        if (target) {
            target.classList.add('active-status');
        }
    }


    /* =====================================================
       FILTER PAYMENT
    ===================================================== */

    items.forEach(item => {

        const itemStatus =
            item.getAttribute('data-status');
        const itemType = item.getAttribute('data-payment-type');

        if (
            status === 'semua' ||
            itemStatus === status ||
            itemType === status ||
            (status === 'masuk' && ['ditahan', 'keluar'].includes(itemType)) ||
            (status === 'berhasil' && itemType === 'ditahan')
        ) {

            item.style.display = 'block';

            visibleCount++;

        } else {

            item.style.display = 'none';

        }

    });


    /* =====================================================
       UPDATE JUMLAH
    ===================================================== */

    if (countElement) {
        countElement.textContent =
            visibleCount + ' Pembayaran';
    }


}
window.filterPayment = filterPayment;

/* =========================================================
   CONTROLLER MODAL DETAIL RINGKASAN DANA (3 CARD UTAMA)
========================================================= */
var paymentSummaryData = {
    ditahan: {
        title: 'Detail Dana Ditahan',
        subtitle: 'Escrow / Rekening Bersama Belum Dicairkan',
        badge: 'Escrow Aktif',
        badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30',
        icon: 'fa-solid fa-hand-holding-dollar',
        iconBoxClass: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
        highlightLabel: 'Total Dana Tertahan di Sistem',
        highlightPill: '8 Pesanan Berjalan',
        highlightValue: 'Rp2.150.000',
        highlightNote: '(Rp2,15 jt)',
        highlightDesc: 'Uang pembayaran customer yang masih ditahan di sistem rekber SayaBantu hingga pengerjaan selesai dan diverifikasi customer.',
        highlightBoxClass: 'bg-gradient-to-br from-amber-500/15 via-amber-600/10 to-transparent border-amber-500/30',
        infoTitle: 'Mekanisme Dana Ditahan (Escrow)',
        infoText: 'Setiap pembayaran customer langsung diamankan sistem SayaBantu. Mitra baru dapat mencairkan dana setelah pesanan diselesaikan dengan baik tanpa sengketa.',
        filterTarget: 'ditahan',
        filterBtnText: 'Filter Transaksi Menunggu (#PAY)',
        metrics: [
            { label: 'Dalam Pengerjaan', value: 'Rp 1.450.000', sub: '5 pesanan mitra aktif' },
            { label: 'Menunggu ACC Customer', value: 'Rp 700.000', sub: '3 pesanan selesai pengerjaan' },
            { label: 'Rata-rata Waktu Tahan', value: '1.2 Hari', sub: 'Hingga verifikasi selesai' },
            { label: 'Garansi Keamanan', value: '100% Proteksi', sub: 'Bebas penipuan kedua pihak' }
        ],
        items: [
            { id: '#PAY1024', order: '#SB1024 • Andi', amount: 'Rp140.000', method: 'Transfer Bank', status: 'Menunggu', badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30' },
            { id: '#PAY1019', order: '#SB1019 • Reza', amount: 'Rp250.000', method: 'QRIS Instant', status: 'Pengerjaan', badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30' },
            { id: '#PAY1016', order: '#SB1016 • Dewi', amount: 'Rp120.000', method: 'BCA Virtual Account', status: 'Pengerjaan', badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30' },
            { id: '#PAY1012', order: '#SB1012 • Riko', amount: 'Rp85.000', method: 'E-Wallet DANA', status: 'Verifikasi', badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30' }
        ]
    },
    masuk: {
        title: 'Detail Dana Masuk',
        subtitle: 'Akumulasi Uang Masuk Gross Bulan Ini',
        badge: 'Terverifikasi Lunas',
        badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30',
        icon: 'fa-solid fa-arrow-down-left',
        iconBoxClass: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        highlightLabel: 'Total Dana Masuk Berhasil',
        highlightPill: '32 Transaksi Lunas',
        highlightValue: 'Rp12.750.000',
        highlightNote: '(Rp12,75 jt)',
        highlightDesc: 'Akumulasi seluruh pembayaran customer yang berhasil tervalidasi via QRIS, Virtual Account, Transfer Bank, dan E-Wallet.',
        highlightBoxClass: 'bg-gradient-to-br from-emerald-500/15 via-emerald-600/10 to-transparent border-emerald-500/30',
        infoTitle: 'Arus Kas Masuk (Gross Cash-In)',
        infoText: 'Dana ini mencakup pembayaran yang sudah diverifikasi. Pembagiannya 10% untuk fee platform dan 90% untuk hak mitra.',
        filterTarget: 'berhasil',
        filterBtnText: 'Filter Transaksi Berhasil (#PAY)',
        metrics: [
            { label: 'Kanal QRIS Instant', value: 'Rp 6.850.000', sub: '18 transaksi (54%)' },
            { label: 'Virtual Account & Bank', value: 'Rp 4.900.000', sub: '11 transaksi (38%)' },
            { label: 'E-Wallet Direct', value: 'Rp 1.000.000', sub: '3 transaksi (8%)' },
            { label: 'Fee Platform (10%)', value: 'Rp 0', sub: 'Pendapatan SayaBantu' }
        ],
        items: [
            { id: '#PAY1023', order: '#SB1023 • Sinta', amount: 'Rp75.000', method: 'QRIS', status: 'Berhasil', badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' },
            { id: '#PAY1022', order: '#SB1022 • Dimas', amount: 'Rp250.000', method: 'E-Wallet', status: 'Berhasil', badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' },
            { id: '#PAY1020', order: '#SB1020 • Bayu', amount: 'Rp180.000', method: 'BCA VA', status: 'Berhasil', badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' },
            { id: '#PAY1018', order: '#SB1018 • Maya', amount: 'Rp95.000', method: 'QRIS', status: 'Berhasil', badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' }
        ]
    },
    keluar: {
        title: 'Detail Dana Keluar',
        subtitle: 'Pencairan Dana Ditransfer ke Mitra',
        badge: '100% Berhasil Dicairkan',
        badgeClass: 'bg-sky-500/20 text-sky-300 border border-sky-400/30',
        icon: 'fa-solid fa-arrow-up-right-from-square',
        iconBoxClass: 'bg-sky-500/20 text-sky-400 border-sky-500/30',
        highlightLabel: 'Total Pencairan Dana Mitra',
        highlightPill: '14 Kali Transfer',
        highlightValue: 'Rp4.300.000',
        highlightNote: '(Rp4,30 jt)',
        highlightDesc: 'Total akumulasi uang yang telah dicairkan dan ditransfer langsung ke rekening bank atau e-wallet mitra atas suruhan tuntas.',
        highlightBoxClass: 'bg-gradient-to-br from-sky-500/15 via-sky-600/10 to-transparent border-sky-500/30',
        infoTitle: 'Sistem Pencairan Mitra (Disbursement)',
        infoText: 'Mitra menerima pencairan secara otomatis atau manual request tanpa potongan admin bank. Seluruh biaya transfer ditanggung oleh platform.',
        filterTarget: 'keluar',
        filterBtnText: 'Lihat Semua Transaksi (#PAY)',
        metrics: [
            { label: 'Transfer Rekening Bank', value: 'Rp 3.100.000', sub: '9 pencairan bank mitra' },
            { label: 'E-Wallet Mitra', value: 'Rp 1.200.000', sub: '5 pencairan DANA / Gopay' },
            { label: 'Biaya Admin Bank', value: 'Rp 0', sub: 'Ditanggung platform' },
            { label: 'Tingkat Sukses Transfer', value: '100%', sub: '0 antrean gagal / retur' }
        ],
        items: []
    },
    refund: {
        title: 'Detail Pengembalian Dana (Refund)',
        subtitle: 'Escrow Dikembalikan ke Customer',
        badge: 'Refund Selesai',
        badgeClass: 'bg-rose-500/20 text-rose-300 border border-rose-400/30',
        icon: 'fa-solid fa-arrow-rotate-left',
        iconBoxClass: 'bg-rose-500/20 text-rose-400 border-rose-500/30',
        highlightLabel: 'Total Pengembalian Dana',
        highlightPill: '100% Terproteksi',
        highlightValue: 'Rp 430.000',
        highlightNote: '(Refund Penuh)',
        highlightDesc: 'Total akumulasi uang rekber escrow yang telah dikembalikan penuh ke rekening/e-wallet customer akibat pembatalan atau kendala penugasan.',
        highlightBoxClass: 'bg-gradient-to-br from-rose-500/15 via-rose-600/10 to-transparent border-rose-500/30',
        infoTitle: 'Mekanisme Refund Rekber (Escrow)',
        infoText: 'Setiap pesanan yang dibatalkan oleh Admin atau Customer diproses pengembalian dananya secara instan tanpa potongan biaya administrasi.',
        filterTarget: 'gagal',
        filterBtnText: 'Filter Transaksi Refund (#PAY)',
        metrics: [
            { label: 'Pembatalan Admin', value: 'Rp 180.000', sub: 'ToS / Alamat fiktif' },
            { label: 'Pembatalan Customer', value: 'Rp 250.000', sub: 'Perubahan rencana' },
            { label: 'Tingkat Sukses Refund', value: '100%', sub: 'Dana kembali utuh' },
            { label: 'Potongan Refund', value: 'Rp 0', sub: 'Gratis tanpa potongan' }
        ],
        items: [
            { id: '#PAY1022', order: 'Pindahan Rumah • Dimas Prasetyo', amount: 'Rp250.000', method: 'Refund QRIS', status: 'Dikembalikan ke Customer (Refund)', badgeClass: 'bg-rose-500/20 text-rose-300 border border-rose-400/30' },
            { id: '#PAY1019', order: 'Perbaikan Pipa • Reza Kurnia', amount: 'Rp180.000', method: 'Refund Bank Transfer', status: 'Dikembalikan ke Customer (Refund)', badgeClass: 'bg-rose-500/20 text-rose-300 border border-rose-400/30' }
        ]
    }
};

var currentSummaryType = 'ditahan';

function openPaymentSummaryModal(type = 'ditahan') {
    switchSummaryModalTab(type);

    const modal = document.getElementById('paymentSummaryModal');
    const panel = document.getElementById('paymentSummaryPanel');
    if (!modal || !panel) return;

    modal.classList.remove('pointer-events-none', 'opacity-0', 'invisible');
    modal.classList.add('opacity-100');
    panel.classList.remove('translate-y-full');
    panel.classList.add('translate-y-0');

    if (typeof lockAdminBodyScroll === 'function') {
        lockAdminBodyScroll();
    }
}
window.openPaymentSummaryModal = openPaymentSummaryModal;

function closePaymentSummaryModal() {
    const modal = document.getElementById('paymentSummaryModal');
    const panel = document.getElementById('paymentSummaryPanel');
    if (!modal || !panel) return;

    panel.classList.add('translate-y-full');
    panel.classList.remove('translate-y-0');
    modal.classList.remove('opacity-100');

    setTimeout(() => {
        modal.classList.add('pointer-events-none', 'opacity-0', 'invisible');
        if (typeof unlockAdminBodyScroll === 'function') {
            unlockAdminBodyScroll();
        }
    }, 200);
}
window.closePaymentSummaryModal = closePaymentSummaryModal;

function switchSummaryModalTab(type) {
    currentSummaryType = type;
    const data = paymentSummaryData[type];
    if (!data) return;

    const rows = paymentSummaryRows.filter(row => {
        if (type === 'ditahan') return row.group === 'ditahan';
        if (type === 'masuk') return ['ditahan', 'keluar'].includes(row.group);
        if (type === 'keluar') return row.group === 'keluar';
        return row.group === 'refund';
    });
    const total = rows.reduce((sum, row) => sum + (type === 'keluar' ? row.mitra_fee : row.amount), 0);
    const adminFees = type === 'refund' ? 0 : rows.reduce((sum, row) => sum + row.admin_fee, 0);
    const partnerFees = type === 'refund' ? 0 : rows.reduce((sum, row) => sum + row.mitra_fee, 0);

    data.highlightValue = formatPaymentSummaryCurrency(total);
    data.highlightNote = `${rows.length} transaksi`;
    data.highlightPill = `${rows.length} Transaksi`;
    data.items = rows.slice(0, 10).map(row => ({
        id: row.id,
        order: row.order,
        amount: formatPaymentSummaryCurrency(type === 'keluar' ? row.mitra_fee : row.amount),
        method: row.method,
        status: row.status,
        badgeClass: type === 'refund'
            ? 'bg-rose-500/20 text-rose-300 border border-rose-400/30'
            : (row.group === 'pending'
                ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30'
                : 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30')
    }));
    data.metrics = [
        { label: 'Jumlah Transaksi', value: String(rows.length), sub: 'Data pembayaran aktual' },
        { label: 'Total Nilai', value: formatPaymentSummaryCurrency(total), sub: type === 'keluar' ? 'Dana disalurkan ke mitra' : 'Berdasarkan status transaksi' },
        { label: 'Fee Admin (10%)', value: formatPaymentSummaryCurrency(adminFees), sub: 'Dari transaksi terverifikasi' },
        { label: 'Hak Mitra (90%)', value: formatPaymentSummaryCurrency(partnerFees), sub: 'Nilai hak mitra' }
    ];

    // 1. Update Tabs Styling
    const tabDitahan = document.getElementById('tabSummaryDitahan');
    const tabMasuk = document.getElementById('tabSummaryMasuk');
    const tabKeluar = document.getElementById('tabSummaryKeluar');
    const tabRefund = document.getElementById('tabSummaryRefund');

    [tabDitahan, tabMasuk, tabKeluar, tabRefund].forEach(t => {
        if (t) {
            t.className = 'py-1.5 rounded-lg transition-all text-center text-gray-300 hover:text-white cursor-pointer';
        }
    });

    if (type === 'ditahan' && tabDitahan) {
        tabDitahan.className = 'py-1.5 rounded-lg transition-all text-center bg-amber-500/20 text-amber-300 border border-amber-400/40 shadow-xs cursor-pointer';
    } else if (type === 'masuk' && tabMasuk) {
        tabMasuk.className = 'py-1.5 rounded-lg transition-all text-center bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 shadow-xs cursor-pointer';
    } else if (type === 'keluar' && tabKeluar) {
        tabKeluar.className = 'py-1.5 rounded-lg transition-all text-center bg-sky-500/20 text-sky-300 border border-sky-400/40 shadow-xs cursor-pointer';
    } else if (type === 'refund' && tabRefund) {
        tabRefund.className = 'py-1.5 rounded-lg transition-all text-center bg-rose-500/20 text-rose-300 border border-rose-400/40 shadow-xs cursor-pointer';
    }

    // 2. Header & Badges
    document.getElementById('summaryModalTitle').innerText = data.title;
    document.getElementById('summaryModalSubtitle').innerText = data.subtitle;

    const badge = document.getElementById('summaryModalBadge');
    badge.innerText = data.badge;
    badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold ' + data.badgeClass;

    const iconBox = document.getElementById('summaryModalIconBox');
    iconBox.className = 'w-10 h-10 rounded-xl flex items-center justify-center text-base border shadow-inner transition-colors ' + data.iconBoxClass;

    const icon = document.getElementById('summaryModalIcon');
    icon.className = data.icon;

    // 3. Highlight Box
    const highlightBox = document.getElementById('summaryHighlightBox');
    highlightBox.className = 'p-3.5 rounded-2xl border transition-all relative overflow-hidden ' + data.highlightBoxClass;
    document.getElementById('summaryHighlightLabel').innerText = data.highlightLabel;
    document.getElementById('summaryHighlightPill').innerText = data.highlightPill;
    document.getElementById('summaryHighlightValue').innerText = data.highlightValue;
    document.getElementById('summaryHighlightNote').innerText = data.highlightNote;
    document.getElementById('summaryHighlightDesc').innerText = data.highlightDesc;

    // 4. System Info
    document.getElementById('summaryInfoTitle').innerText = data.infoTitle;
    document.getElementById('summaryInfoText').innerText = data.infoText;

    // 5. Metrics Grid
    const metricGrid = document.getElementById('summaryMetricGrid');
    metricGrid.innerHTML = data.metrics.map(m => `
        <div class="p-2.5 rounded-xl bg-white/[0.04] border border-white/10 space-y-0.5">
            <span class="text-[9px] text-gray-400 block">${m.label}</span>
            <strong class="text-xs text-white block">${m.value}</strong>
            <span class="text-[8.5px] text-gray-400 block">${m.sub}</span>
        </div>
    `).join('');

    // 6. Transaction Items List
    const listItems = document.getElementById('summaryListItems');
    listItems.innerHTML = data.items.length ? data.items.map(it => `
        <div class="p-2.5 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-between gap-2.5 hover:bg-white/[0.07] transition-colors">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="font-mono font-bold text-sky-400 text-[11px]">${it.id}</span>
                    <span class="text-[8.5px] px-1.5 py-0.2 rounded-full font-bold ${it.badgeClass}">${it.status}</span>
                </div>
                <p class="text-[10px] text-gray-200 font-semibold truncate mt-0.5">${it.order}</p>
                <span class="text-[9px] text-gray-400 block">${it.method}</span>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[9px] text-gray-400 block">Nominal</span>
                <strong class="text-xs font-bold text-white">${it.amount}</strong>
            </div>
        </div>
    `).join('') : '<p class="text-[10px] text-gray-400 p-2">Belum ada transaksi pada kategori ini.</p>';
    document.getElementById('summaryListCounter').innerText = `${rows.length} transaksi`;

    // 7. Action Button
    document.getElementById('btnApplySummaryFilterText').innerText = data.filterBtnText;
}
window.switchSummaryModalTab = switchSummaryModalTab;

function applySummaryCategoryFilter() {
    const data = paymentSummaryData[currentSummaryType];
    const targetStatus = data ? data.filterTarget : 'semua';

    closePaymentSummaryModal();

    setTimeout(() => {
        filterPayment(targetStatus);

        const listSection = document.querySelector('.pembayaran-status') || document.getElementById('payment-list');
        if (listSection) {
            listSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 250);
}
window.applySummaryCategoryFilter = applySummaryCategoryFilter;

document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.payment-item');
    let semua = items.length;
    let menunggu = 0;
    let berhasil = 0;
    let gagal = 0;
    items.forEach(it => {
        const st = it.getAttribute('data-status');
        if (st === 'menunggu') menunggu++;
        else if (st === 'berhasil') berhasil++;
        else if (st === 'gagal') gagal++;
    });
    const cSemua = document.getElementById('payCountSemua');
    if (cSemua) cSemua.textContent = semua;
    const cMenunggu = document.getElementById('payCountMenunggu');
    if (cMenunggu) cMenunggu.textContent = menunggu;
    const cBerhasil = document.getElementById('payCountBerhasil');
    if (cBerhasil) cBerhasil.textContent = berhasil;
    const cGagal = document.getElementById('payCountGagal');
    if (cGagal) cGagal.textContent = gagal;
});

var activePayoutCode = null;
function formatRupiah(num) {
    return 'Rp ' + (num || 0).toLocaleString('id-ID');
}

function openActionModalTeruskan(code, mitraName, total, adminFee, mitraFee) {
    activePayoutCode = code;
    document.getElementById('modalTeruskanCode').innerText = 'Suruhan #' + code;
    document.getElementById('modalTeruskanMitraName').innerText = mitraName;
    document.getElementById('modalTeruskanMitraFee').innerText = formatRupiah(mitraFee);
    document.getElementById('modalTeruskanTotal').innerText = formatRupiah(total);
    document.getElementById('modalTeruskanAdminFee').innerText = formatRupiah(adminFee);
    document.getElementById('modalTeruskanFinalMitra').innerText = formatRupiah(mitraFee);

    const modal = document.getElementById('actionModalTeruskan');
    const panel = document.getElementById('actionPanelTeruskan');
    if (modal && panel) {
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('pointer-events-auto');
        panel.classList.remove('translate-y-full');
    }
}
window.openActionModalTeruskan = openActionModalTeruskan;

function closeActionModalTeruskan() {
    const modal = document.getElementById('actionModalTeruskan');
    const panel = document.getElementById('actionPanelTeruskan');
    if (panel) panel.classList.add('translate-y-full');
    if (modal) {
        modal.classList.remove('pointer-events-auto');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible', 'pointer-events-none');
            activePayoutCode = null;
        }, 200);
    }
}
window.closeActionModalTeruskan = closeActionModalTeruskan;

function submitDirectPayout() {
    if (!activePayoutCode) return;
    const btn = document.getElementById('btnExecuteDirectPayout');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Mengirim Dana...';
    }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    fetch('/admin/pembayaran/' + activePayoutCode + '/teruskan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            closeActionModalTeruskan();
            if (typeof showToast === 'function') {
                showToast('Dana Berhasil Diteruskan!', data.message, 'success');
            } else {
                alert(data.message);
            }
            setTimeout(() => {
                window.location.reload();
            }, 800);
        } else {
            alert((data && data.message) ? data.message : 'Gagal meneruskan dana.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> <span>Kirim Uang ke Mitra Sekarang</span>';
            }
        }
    })
    .catch(err => {
        alert(err.message || 'Terjadi kesalahan jaringan saat meneruskan dana.');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> <span>Kirim Uang ke Mitra Sekarang</span>';
        }
    });
}
window.submitDirectPayout = submitDirectPayout;

</script>

@endpush
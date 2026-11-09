@extends('layouts.admin')

@section('title', 'Detail Pembayaran #' . ($payment['clean_order_code'] ?? ($id ?? '1024')) . ' - SayaBantu.com')
@section('back-url', route('admin.pembayaran'))
@section('back-text', 'Kembali ke Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-subtitle', 'Rincian transaksi dana & penyaluran uang ke Mitra')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}
@section('hero')
@php
    $isReleased = in_array($payment['escrow_status'] ?? '', ['released_to_mitra', 'released']);
    $isPaymentPending = $payment['is_pending_verification'] ?? false;
    $isRefunded = $payment['is_refunded'] ?? false;
    $isWorkComplete = $payment['is_work_completed'] ?? false;
@endphp
<section class="relative z-10 px-5 pb-5 pt-1">
    <div class="flex items-start justify-between gap-3 p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div>
            <p class="text-[9px] font-medium text-white/60 uppercase tracking-wider">
                Nomor Pembayaran
            </p>
            <h2 class="mt-1 text-[20px] font-black tracking-tight text-white font-mono">
                #{{ $payment['kode'] ?? ('PAY-' . $payment['clean_order_code']) }}
            </h2>
            <p class="text-[10px] text-sky-200 mt-0.5">
                Suruhan #{{ $payment['clean_order_code'] }} • {{ $payment['layanan'] }}
            </p>
        </div>

        @if($isRefunded)
            <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-red-300/40 bg-red-500/20 px-3 py-1 text-[9px] font-bold text-red-200">
                Dana Dikembalikan
            </span>
        @elseif($isReleased)
            <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-emerald-300/40 bg-emerald-500/20 px-3 py-1 text-[9px] font-bold text-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                Diteruskan ke Mitra (90%)
            </span>
        @elseif($isPaymentPending)
            <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-amber-300/40 bg-amber-500/20 px-3 py-1 text-[9px] font-bold text-amber-200">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>
                Menunggu Konfirmasi Admin
            </span>
        @else
            <span class="mt-1 inline-flex shrink-0 items-center rounded-full border border-amber-300/40 bg-amber-500/20 px-3 py-1 text-[9px] font-bold text-amber-200">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                Ditahan (Escrow)
            </span>
        @endif
    </div>
</section>
@endsection

{{-- =========================================================
     CONTENT DETAIL
========================================================= --}}
@section('content')

<div class="space-y-3.5">

    {{-- 1. KARTU TOTAL DIBAYAR CUSTOMER --}}
    <section class="p-4 rounded-[22px] bg-white border border-slate-200/90 shadow-xs space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <span class="text-[9.5px] text-slate-400 font-semibold uppercase tracking-wider block">
                    Pembayaran dari Pelanggan
                </span>
                <h3 class="mt-1 text-[24px] font-black text-[#0E1D31] font-mono tracking-tight leading-none">
                    Rp {{ number_format($payment['total_bayar'], 0, ',', '.') }}
                </h3>
                @if($isRefunded)
                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                        <i class="fa-solid fa-rotate-left text-[9px]"></i> Dana telah dikembalikan
                    </span>
                @elseif($isPaymentPending)
                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-amber-950 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-400">
                        <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Konfirmasi Admin di Halaman Suruhan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> Pembayaran Dikonfirmasi Admin
                    </span>
                @endif
            </div>
            <div class="px-2.5 py-1.5 rounded-xl bg-sky-50 text-sky-900 border border-sky-100 text-[10px] font-bold text-right shrink-0">
                <i class="fa-solid fa-building-columns mr-1 text-sky-600"></i>
                {{ $payment['metode'] }}
            </div>
        </div>

        <div class="pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[10px]">
            <div>
                <span class="text-slate-400 block text-[9px]">REFERENSI PESANAN</span>
                <span class="font-mono font-bold text-slate-800">{{ $payment['kode_va'] }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[9px]">WAKTU DILAPORKAN CUSTOMER</span>
                <span class="font-semibold text-slate-700">{{ $payment['waktu_bayar'] }}</span>
            </div>
        </div>

        @if($isRefunded)
            <p class="text-[9.5px] text-red-700">
                Transaksi ini sudah dibatalkan dan dananya dikembalikan kepada Customer.
            </p>
        @elseif($isPaymentPending)
            <div class="p-3 rounded-xl bg-amber-100 border border-amber-400 text-[10px] text-amber-950 font-semibold leading-relaxed">
                Customer sudah melaporkan pembayaran. Admin perlu memverifikasi transaksi dari halaman detail suruhan; saldo admin baru diperbarui setelah konfirmasi.
            </div>
        @elseif(!empty($payment['payment_submitted_at']))
            <p class="text-[9.5px] text-emerald-700">
                Pembayaran dicatat oleh Customer pada {{ $payment['payment_submitted_at'] }}
            </p>
        @endif
    </section>

    {{-- 2. KARTU INFORMASI TRANSAKSI & PARA PIHAK --}}
    <section class="p-4 rounded-[22px] bg-white border border-slate-200/90 shadow-xs space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-users text-sky-600"></i> Informasi Pihak Terlibat
            </h4>
            <a href="{{ route('admin.pesanan.detail', $payment['clean_order_code']) }}" class="text-[10px] font-bold text-[#173B67] hover:underline flex items-center gap-1">
                Detail Pesanan #{{ $payment['clean_order_code'] }} <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 gap-2.5 text-xs">
            {{-- Customer --}}
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">CUSTOMER</span>
                <strong class="text-slate-900 text-xs block truncate">{{ $payment['customer_name'] }}</strong>
                <div class="flex items-center justify-between pt-1 text-[10px]">
                    <span class="text-slate-500 font-mono text-[9.5px]">{{ $payment['customer_phone'] }}</span>
                    @if($payment['customer_phone'] && $payment['customer_phone'] !== '-')
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $payment['customer_phone']) }}" class="text-slate-600 hover:text-slate-800 text-xs" title="Hubungi Customer">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Mitra --}}
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">MITRA PENYEDIA</span>
                    <span class="text-[8px] font-extrabold text-sky-700 bg-sky-100 px-1.5 py-0.2 rounded-full">{{ $payment['mitra_id'] }}</span>
                </div>
                <strong class="text-sky-900 text-xs block truncate">{{ $payment['mitra_name'] }}</strong>
                <div class="flex items-center justify-between pt-1 text-[10px]">
                    <span class="text-slate-500 font-mono text-[9.5px]">{{ $payment['mitra_phone'] }}</span>
                    @if($payment['mitra_phone'] && $payment['mitra_phone'] !== '-')
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $payment['mitra_phone']) }}" class="text-slate-600 hover:text-slate-800 text-xs" title="Hubungi Mitra">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- 3. KARTU RINCIAN BIAYA & BAGI HASIL --}}
    <section class="p-4 rounded-[22px] bg-white border border-slate-200/90 shadow-xs space-y-2.5">
        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
            <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-calculator text-slate-500"></i> Rincian Pembagian Hasil (10% Admin &amp; 90% Mitra)
            </h4>
            <span class="text-[9.5px] text-slate-400">Transparan</span>
        </div>

        <div class="space-y-1.5 text-xs">
            <div class="flex items-center justify-between text-[11px]">
                <span class="text-slate-600">Total Dibayar Customer (Gross)</span>
                <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($payment['total_bayar'], 0, ',', '.') }}</span>
            </div>

            <div class="flex items-center justify-between text-[11px]">
                <span class="text-emerald-700 font-semibold flex items-center gap-1">
                    Saldo Fee Admin Platform SayaBantu
                    <span class="text-[9px] text-emerald-600 font-bold">(10%)</span>
                </span>
                <span class="font-bold text-emerald-600 font-mono">+Rp {{ number_format($payment['biaya_platform'], 0, ',', '.') }}</span>
            </div>

            <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black text-sky-800 uppercase block">Bersih Diteruskan ke Mitra (90%)</span>
                    <span class="text-[9px] text-slate-500">Hak pencairan jasa pengerjaan mitra</span>
                </div>
                <span class="text-base font-black text-sky-600 font-mono">Rp {{ number_format($payment['nominal_mitra'], 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    {{-- 4. KARTU UTAMA: STATUS PENAHANAN DANA / PENYALURAN KE MITRA --}}
    <section class="p-4 rounded-[24px] bg-white border-2 {{ $isReleased ? 'border-emerald-500/30' : 'border-amber-500/40' }} shadow-md space-y-3 relative overflow-hidden" id="payoutSectionCard">

        {{-- Header Status Penyaluran --}}
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                    Penerusan Pembayaran
                </span>
                <h3 class="text-xs font-black text-slate-900 mt-0.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-transfer {{ $isReleased ? 'text-emerald-600' : ($isPaymentPending ? 'text-amber-500' : 'text-emerald-600') }}"></i>
                    Penyaluran Dana ke Mitra
                </h3>
            </div>

            <span id="payoutStatusBadge" class="px-2.5 py-1 rounded-full text-[9.5px] font-black {{ $isReleased ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }} shrink-0 flex items-center gap-1">
                <i class="fa-solid {{ $isReleased ? 'fa-circle-check text-emerald-600' : 'fa-clock text-amber-500' }} text-[8.5px]"></i>
                <span id="payoutStatusText">{{ $isReleased ? 'Sudah Diteruskan ke Mitra' : ($isPaymentPending ? 'Menunggu Verifikasi Pembayaran' : 'Ditahan di Escrow') }}</span>
            </span>
        </div>

        {{-- JIKA DITAHAN: ALASAN EKSPLISIT WAJIB DITAMPILKAN --}}
        @if($isPaymentPending)
            <div class="p-3 rounded-2xl bg-amber-100 border border-amber-400 text-xs">
                <p class="text-amber-950 font-semibold text-[11px] leading-relaxed">
                    Dana menunggu konfirmasi pembayaran dari halaman detail suruhan.
                </p>
            </div>
        @elseif(!$isReleased)
            <div class="p-3 rounded-2xl bg-amber-100 border border-amber-400 text-xs space-y-1">
                <div class="flex items-center gap-1.5 text-amber-950 font-bold text-[11px]">
                    <i class="fa-solid fa-shield-halved text-amber-800"></i>
                    <span>Dana Otomatis Ditahan di Escrow:</span>
                </div>
                <p class="text-amber-950 font-medium text-[11px] leading-relaxed pl-5">
                    "{{ $payment['escrow_hold_reason'] ?? 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai' }}"
                </p>
                <p class="text-[9.5px] text-amber-900 pl-5 pt-0.5">
                    {{ $isWorkComplete ? 'Customer dan Admin telah mengonfirmasi pekerjaan selesai. Admin dapat meneruskan dana ke Mitra.' : 'Dana baru dapat diteruskan setelah Customer dan Admin mengonfirmasi pekerjaan selesai.' }}
                </p>
            </div>
        @else
            <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1">
                <div class="flex items-center gap-1.5 text-emerald-900 font-bold text-[11px]">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Dana Berhasil Disalurkan ke Mitra</span>
                </div>
                <p class="text-emerald-800 text-[11px] leading-relaxed">
                    Dana sebesar <strong>Rp {{ number_format($payment['nominal_mitra'], 0, ',', '.') }}</strong> (90%) telah disalurkan kepada <strong>{{ $payment['mitra_name'] }}</strong>.
                </p>
                @if($payment['escrow_released_at'])
                <p class="text-[9.5px] text-emerald-600 font-mono">
                    Waktu Penyaluran: {{ $payment['escrow_released_at'] }}
                </p>
                @endif
            </div>
        @endif

        {{-- Detail Rekening Mitra Penerima --}}
        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-[9.5px] font-bold text-slate-400 uppercase">REKENING / DOMPET MITRA</span>
                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <i class="fa-solid fa-shield-check text-[8px]"></i> Rekening Terverifikasi
                </span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-[11px] flex items-center justify-center shadow-xs">
                        {{ $payment['mitra_bank'] ?? 'BCA' }}
                    </div>
                    <div>
                        <div class="font-mono font-bold text-slate-900 text-xs">{{ $payment['mitra_rekening'] ?? '8271 9920 1123' }}</div>
                        <div class="text-[9.5px] text-slate-500">a.n. {{ $payment['mitra_rekening_an'] ?? $payment['mitra_name'] }}</div>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[9px] text-slate-400 block">NOMINAL TRANSFER</span>
                    <strong class="text-sm font-black text-emerald-600 font-mono">Rp {{ number_format($payment['nominal_mitra'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        {{-- Dana hanya dapat diteruskan setelah Customer dan Admin mengonfirmasi pekerjaan selesai. --}}
        @if(!$isReleased && !$isPaymentPending && ($payment['can_release_escrow'] ?? false))
        <div id="payoutActionContainer" class="pt-1 grid grid-cols-1 gap-2">
            <button
                type="button"
                id="btnTriggerPayout"
                onclick="openPayoutConfirmationModal()"
                class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Teruskan Uang ke Mitra (Rp {{ number_format($payment['nominal_mitra'], 0, ',', '.') }})</span>
            </button>
        </div>
        @endif

    </section>

    {{-- 5. TOMBOL KEMBALI / LIHAT PESANAN --}}
    <section class="pt-1 pb-6">
        <a
            href="{{ route('admin.pesanan.detail', $payment['clean_order_code']) }}"
            class="w-full py-3 px-3 rounded-2xl bg-white border border-slate-200 text-slate-700 text-xs font-bold text-center shadow-xs hover:bg-slate-50 active:scale-95 transition-all inline-flex items-center justify-center gap-1.5"
        >
            <i class="fa-solid fa-clipboard-list text-xs text-slate-400"></i>
            Lihat Detail Pesanan #{{ $payment['clean_order_code'] }}
        </a>
    </section>

</div>
@endsection

{{-- =========================================================================
     MODALS
========================================================================== --}}
@section('modals')
{{-- Modal Konfirmasi Penyaluran Uang ke Mitra --}}
<div id="payoutConfirmModal" class="sb-overlay-backdrop fixed inset-0 z-[150] pointer-events-none opacity-0 invisible transition-all duration-200 flex items-end justify-center" aria-hidden="true">
    <div class="sb-backdrop-click fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" onclick="closePayoutConfirmationModal()"></div>

    <div class="relative w-full max-w-[430px] bg-white rounded-t-[32px] p-5 shadow-[0_-12px_40px_rgba(15,23,42,0.25)] z-[160] pointer-events-auto transform translate-y-full transition-transform duration-300 max-h-[92vh] flex flex-col pb-8">

        <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-3 shrink-0"></div>

        <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-base border border-emerald-500/20 shrink-0 shadow-xs">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-sm font-black text-slate-900 leading-tight">Teruskan Dana ke Mitra</h3>
                        <span class="px-1.5 py-0.5 rounded-full text-[8.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Escrow</span>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-0.5">Penyaluran dana suruhan #{{ $payment['clean_order_code'] }}</p>
                </div>
            </div>
            <button type="button" onclick="closePayoutConfirmationModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-500 flex items-center justify-center transition-all cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <div class="mt-3.5 space-y-3 text-xs overflow-y-auto custom-scrollbar pr-0.5">

            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 via-teal-50/40 to-white border border-emerald-200/90 shadow-xs text-center relative overflow-hidden">
                <span class="text-[9.5px] font-bold text-emerald-800 uppercase tracking-wider block">
                    TOTAL DITERUSKAN KE MITRA (90%)
                </span>
                <div class="mt-1 text-[26px] font-black text-slate-900 font-mono tracking-tight leading-none">
                    Rp {{ number_format($payment['nominal_mitra'], 0, ',', '.') }}
                </div>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/90 border border-emerald-200 text-[9.5px] text-emerald-800 font-semibold shadow-2xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-[9px]"></i>
                    Fee Platform Admin 10% (Rp {{ number_format($payment['biaya_platform'], 0, ',', '.') }}) telah dipotong
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between text-[10px]">
                    <span class="font-bold text-slate-400 uppercase tracking-wide">REKENING TUJUAN MITRA</span>
                    <span class="font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full flex items-center gap-1 text-[9px]">
                        <i class="fa-solid fa-shield-halved text-[8px]"></i> Terverifikasi
                    </span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200/70 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#005EAA] text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs tracking-tighter">
                            {{ $payment['mitra_bank'] ?? 'BCA' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-slate-900 text-xs" id="copyRekeningText">{{ $payment['mitra_rekening'] ?? '8271 9920 1123' }}</span>
                                <button type="button" onclick="salinRekening()" class="text-sky-600 hover:text-sky-700 text-[10px]" title="Salin Nomor Rekening">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            <div class="text-[10px] text-slate-500 font-medium">a.n. {{ $payment['mitra_rekening_an'] ?? $payment['mitra_name'] }} ({{ $payment['mitra_id'] }})</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="pt-3 border-t border-slate-100 flex items-center gap-2.5 shrink-0 mt-3">
            <button
                type="button"
                onclick="closePayoutConfirmationModal()"
                class="w-24 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold text-xs transition-all text-center cursor-pointer"
            >
                Batal
            </button>

            <button
                type="button"
                id="btnConfirmPayoutExecute"
                onclick="executeMitraPayout()"
                class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-98 text-white font-extrabold text-xs transition-all shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 cursor-pointer"
            >
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Kirim Uang Sekarang</span>
            </button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function openPayoutConfirmationModal() {
        const modal = document.getElementById('payoutConfirmModal');
        if (!modal) return;
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('pointer-events-auto');
        modal.querySelector('.transform').classList.remove('translate-y-full');
    }

    function closePayoutConfirmationModal() {
        const modal = document.getElementById('payoutConfirmModal');
        if (!modal) return;
        modal.querySelector('.transform').classList.add('translate-y-full');
        modal.classList.remove('pointer-events-auto');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible', 'pointer-events-none');
        }, 200);
    }

    function executeMitraPayout() {
        const btn = document.getElementById('btnConfirmPayoutExecute');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Memproses Transfer...';
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.pembayaran.teruskan", $payment["clean_order_code"]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok || !data.success) {
                throw new Error(data.message || 'Gagal meneruskan dana.');
            }
            return data;
        })
        .then(data => {
            closePayoutConfirmationModal();
            if (typeof showToast === 'function') {
                showToast('Dana Berhasil Diteruskan!', data.message, 'success');
            } else {
                alert(data.message);
            }
            setTimeout(() => {
                window.location.reload();
            }, 800);
        })
        .catch(err => {
            alert(err.message || 'Terjadi kesalahan jaringan saat meneruskan dana.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> <span>Kirim Uang Sekarang</span>';
            }
        });
    }

    function salinRekening() {
        const text = document.getElementById('copyRekeningText').innerText.replace(/\s+/g, '');
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showToast === 'function') {
                showToast('Disalin', 'Nomor rekening mitra berhasil disalin!', 'success');
            } else {
                alert('Nomor rekening disalin: ' + text);
            }
        });
    }

    window.openPayoutConfirmationModal = openPayoutConfirmationModal;
    window.closePayoutConfirmationModal = closePayoutConfirmationModal;
    window.executeMitraPayout = executeMitraPayout;
    window.salinRekening = salinRekening;
</script>
@endpush

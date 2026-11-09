@extends('layouts.admin')

@section('title', 'Konfirmasi Pekerjaan - SayaBantu.com')
@section('page-title', 'Konfirmasi Pekerjaan')
@section('page-subtitle', 'Pemeriksaan kesepakatan Customer & Mitra berstatus menunggu konfirmasi')

@push('styles')
<style>
.deal-card {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border: 1.5px solid #E2E8F0 !important;
    border-radius: 20px !important;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08) !important;
    animation: none !important;
}
.deal-card:hover {
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12) !important;
    border-color: #CBD5E1 !important;
}
</style>
@endpush

@section('hero')
<div class="px-5 pt-1 pb-0">
    {{-- STAT RINGKASAN KESEPAKATAN SEKALIGUS FILTER UTAMA --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-amber-400"></i>
                Status Kesepakatan Kerja
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                Pemeriksaan Admin
            </span>
        </div>

        <div class="grid grid-cols-4 gap-2 text-center" id="dealStatFilters">
            <button type="button" class="deal-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-amber-400 shadow-sm cursor-pointer transition-all scale-[1.03] text-center focus:outline-none" onclick="filterDeals('menunggu', this)" data-status="menunggu">
                <div class="text-xs font-black text-amber-400" id="statMenungguCount">0</div>
                <div class="text-[8px] text-white font-bold mt-0.5">Menunggu</div>
            </button>
            <button type="button" class="deal-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 cursor-pointer hover:bg-white/[0.08] transition-all text-center focus:outline-none" onclick="filterDeals('dikonfirmasi', this)" data-status="dikonfirmasi">
                <div class="text-xs font-black text-emerald-400" id="statDikonfirmasiCount">0</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Disetujui</div>
            </button>
            <button type="button" class="deal-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 cursor-pointer hover:bg-white/[0.08] transition-all text-center focus:outline-none" onclick="filterDeals('revisi', this)" data-status="revisi">
                <div class="text-xs font-black text-sky-400" id="statRevisiCount">0</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Revisi</div>
            </button>
            <button type="button" class="deal-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 cursor-pointer hover:bg-white/[0.08] transition-all text-center focus:outline-none" onclick="filterDeals('ditolak', this)" data-status="ditolak">
                <div class="text-xs font-black text-rose-400" id="statDitolakCount">0</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Ditolak</div>
            </button>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="mt-3">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input
                type="text"
                id="searchDealInput"
                placeholder="Cari no. suruhan, customer, mitra..."
                onkeyup="searchDeals(this.value)"
                class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/15 focus:outline-none focus:border-sky-400 focus:bg-white/15 transition-all"
            >
            <button
                type="button"
                id="clearSearchDealBtn"
                onclick="clearSearchDeals()"
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

    @if(($completionOrders ?? collect())->isNotEmpty())
        <section class="space-y-2" aria-labelledby="completionQueueTitle">
            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200">
                <h2 id="completionQueueTitle" class="text-xs font-bold text-indigo-950 flex items-center gap-2">
                    <i class="fa-solid fa-bell text-indigo-600"></i>
                    Pekerjaan Menunggu Konfirmasi Akhir ({{ $completionOrders->count() }})
                </h2>
                <p class="text-[9.5px] text-indigo-800 mt-1">Customer telah mengonfirmasi pekerjaan selesai. Dana tetap ditahan sampai Admin menutup pekerjaan dan meneruskan dana.</p>
            </div>
            @foreach($completionOrders as $completionOrder)
                <article class="p-3 rounded-xl bg-white border border-indigo-200 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[9px] font-bold font-mono text-indigo-800">{{ $completionOrder->code }}</p>
                            <h3 class="text-xs font-bold text-slate-900 mt-0.5">{{ $completionOrder->layanan }}</h3>
                            <p class="text-[9.5px] text-slate-700 mt-1">
                                Customer: {{ $completionOrder->customer_name }} · Mitra: {{ $completionOrder->mitra_name ?: 'Belum ditentukan' }}
                            </p>
                            <p class="text-[9px] text-slate-500 mt-0.5">Diperbarui {{ $completionOrder->updated_at?->format('d M Y, H:i') }}</p>
                        </div>
                        <a href="{{ route('admin.pesanan.detail', ltrim($completionOrder->code, '#')) }}" class="shrink-0 px-3 py-2 rounded-lg bg-[#173B67] text-white text-[9px] font-bold hover:bg-[#1E4E8C]">
                            Periksa Pekerjaan
                        </a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <div id="dealList" class="space-y-3">
        {{-- CONTAINER KESEPAKATAN REAL-TIME DARI SURUHAN CUSTOMER & MITRA --}}
        <div id="dynamicAdminDealsContainer" class="space-y-3"></div>

        {{-- EMPTY STATE KETIKA BELUM ADA KESEPAKATAN --}}
        <div id="adminDealsEmptyState" class="p-8 text-center bg-white rounded-[22px] border border-gray-200/80 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto border border-amber-100">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gray-900">Belum Ada Kesepakatan yang Perlu Dikonfirmasi</h4>
                <p class="text-[10px] text-gray-500 mt-0.5 leading-relaxed max-w-xs mx-auto">
                    Semua penawaran dan kesepakatan suruhan antara Customer dan Mitra akan otomatis muncul di sini.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- MODAL DETAIL KESEPAKATAN & TINDAKAN ADMIN (DESAIN MODERN RINGAN & BERSIH) --}}
@section('modals')
<div id="dealDetailModal" class="sb-overlay-backdrop fixed inset-0 z-[200] pointer-events-none opacity-0 invisible transition-all duration-200 flex items-end justify-center" aria-hidden="true">
    <div class="sb-backdrop-click fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" onclick="window.closeDealModal()"></div>

    <div id="dealDetailPanel" class="relative w-full max-w-[430px] bg-white rounded-t-[32px] p-5 shadow-[0_-12px_40px_rgba(15,23,42,0.25)] z-[210] pointer-events-auto transform translate-y-full transition-transform duration-300 max-h-[92vh] overflow-y-auto custom-scrollbar pb-10">
        
        {{-- Handle Bar Drawer --}}
        <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-3 shrink-0 cursor-pointer" onclick="window.closeDealModal()"></div>

        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-200 shrink-0">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 leading-none" id="modalOrderId">#SB-KSP01</h3>
                    <p class="text-[10px] text-slate-500 mt-1" id="modalServiceTitle">Full Home Cleaning</p>
                </div>
            </div>
            <button type="button" onclick="window.closeDealModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <div class="mt-4 space-y-3.5 text-xs">

            {{-- PARA PIHAK --}}
            <div class="grid grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase block">CUSTOMER</span>
                    <strong class="text-slate-900 text-xs block mt-0.5 truncate" id="modalCustomerName">Siti Rahma</strong>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase block">MITRA PENYEDIA</span>
                    <strong class="text-sky-700 text-xs block mt-0.5 truncate" id="modalMitraName">Budi Santoso</strong>
                </div>
            </div>

            {{-- RUANG LINGKUP & LOKASI --}}
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <div>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase block">RUANG LINGKUP PEKERJAAN</span>
                    <p class="text-slate-700 text-[11px] mt-0.5 leading-relaxed" id="modalWorkDesc">-</p>
                </div>
                <div class="pt-2 border-t border-slate-200/60 grid grid-cols-2 gap-2 text-[10px]">
                    <div>
                        <span class="text-slate-400 block text-[8.5px] uppercase">JADWAL PELAKSANAAN</span>
                        <span class="text-slate-800 font-semibold" id="modalSchedule">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[8.5px] uppercase">LOKASI</span>
                        <span class="text-slate-800 font-semibold truncate block" id="modalLocation">-</span>
                    </div>
                </div>
            </div>

            {{-- PERBANDINGAN HARGA --}}
            <div class="p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200/90 flex items-center justify-between">
                <div>
                    <span class="text-[9px] text-slate-500 block uppercase font-medium">HARGA ACUAN KATALOG</span>
                    <span class="text-xs text-slate-400 line-through font-mono" id="modalBasePrice">Rp 150.000</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] text-emerald-800 block font-bold uppercase">HARGA AKHIR DISEPAKATI</span>
                    <span class="text-base font-black text-emerald-700 font-mono" id="modalDealPrice">Rp 140.000</span>
                </div>
            </div>

            {{-- RIWAYAT NEGOSIASI --}}
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider block">Riwayat Negosiasi Antar Pihak</span>
                <div id="modalNegoLog" class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 max-h-36 overflow-y-auto custom-scrollbar">
                    {{-- Log items filled by JS --}}
                </div>
            </div>

            {{-- CATATAN ADMIN / ALASAN (OPTIONAL) --}}
            <div>
                <label for="adminDealNote" class="block text-[10px] font-bold text-slate-700 mb-1">Catatan Tindakan Admin (Wajib untuk Revisi/Tolak)</label>
                <input type="text" id="adminDealNote" placeholder="Tuliskan catatan verifikasi atau alasan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white transition-all">
            </div>

            {{-- TOMBOL TINDAKAN ADMIN (3 PILIHAN UTAMA) --}}
            <div class="pt-2 grid grid-cols-3 gap-2">
                {{-- 1. KONFIRMASI KESEPAKATAN --}}
                <button type="button" onclick="window.handleDealAction('confirm')" class="py-2.5 px-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[11px] shadow-sm transition-all flex flex-col items-center justify-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Konfirmasi</span>
                </button>

                {{-- 2. MINTA REVISI --}}
                <button type="button" onclick="window.handleDealAction('revision')" class="py-2.5 px-1 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-[11px] shadow-sm transition-all flex flex-col items-center justify-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    <span>Minta Revisi</span>
                </button>

                {{-- 3. TOLAK KESEPAKATAN --}}
                <button type="button" onclick="window.handleDealAction('reject')" class="py-2.5 px-1 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-[11px] shadow-sm transition-all flex flex-col items-center justify-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    <span>Tolak</span>
                </button>
            </div>

            {{-- TAUTAN KE HALAMAN DETAIL SURUHAN LENGKAP --}}
            <div class="pt-1">
                <a
                    id="modalFullOrderDetailLink"
                    href="{{ route('admin.pesanan.detail', 1024) }}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-3 py-2 text-[10px] font-bold text-[#173B67] transition-all active:scale-98 shadow-2xs"
                >
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    <span>Buka Rincian Pesanan Penuh & Rekber (#SB1024)</span>
                </a>
            </div>

            <p class="text-[9px] text-slate-400 text-center leading-tight pt-1">
                *Konfirmasi kesepakatan membuka gerbang pembayaran bagi Customer. Status dana & verifikasi penampungan rekber dapat dipantau di menu Pembayaran.
            </p>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    window.activeCardEl = window.activeCardEl || null;

    function filterDeals(status, btn) {
        document.querySelectorAll('.deal-stat-tab').forEach(t => {
            t.classList.remove('bg-white/[0.18]', 'border-2', 'border-amber-400', 'border-emerald-400', 'border-sky-400', 'border-rose-400', 'active-stat-tab', 'scale-[1.03]');
            t.classList.add('bg-white/[0.04]', 'border', 'border-white/10');
            const label = t.querySelector('div:last-child');
            if (label) {
                label.classList.remove('text-white', 'font-bold');
                label.classList.add('text-gray-300');
            }
        });
        const targetBtn = btn || document.querySelector(`.deal-stat-tab[data-status="${status}"]`);
        if (targetBtn) {
            targetBtn.classList.remove('bg-white/[0.04]', 'border-white/10');
            let activeColor = 'border-amber-400';
            if (status === 'dikonfirmasi') activeColor = 'border-emerald-400';
            else if (status === 'revisi') activeColor = 'border-sky-400';
            else if (status === 'ditolak') activeColor = 'border-rose-400';
            targetBtn.classList.add('bg-white/[0.18]', 'border-2', activeColor, 'active-stat-tab', 'scale-[1.03]');
            const label = targetBtn.querySelector('div:last-child');
            if (label) {
                label.classList.remove('text-gray-300');
                label.classList.add('text-white', 'font-bold');
            }
        }

        const cards = document.querySelectorAll('.deal-card');
        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'all' || cardStatus === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    window.filterDeals = filterDeals;

    function searchDeals(keyword) {
        keyword = (keyword || '').toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchDealBtn');
        if (clearBtn) clearBtn.classList.toggle('hidden', keyword === '');

        const cards = document.querySelectorAll('.deal-card');
        cards.forEach(card => {
            const orderId = (card.getAttribute('data-order-id') || '').toLowerCase();
            const customer = (card.getAttribute('data-customer') || '').toLowerCase();
            const mitra = (card.getAttribute('data-mitra') || '').toLowerCase();
            const service = (card.getAttribute('data-service') || '').toLowerCase();

            if (orderId.includes(keyword) || customer.includes(keyword) || mitra.includes(keyword) || service.includes(keyword)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    window.searchDeals = searchDeals;

    function clearSearchDeals() {
        const input = document.getElementById('searchDealInput');
        if (input) input.value = '';
        searchDeals('');
    }
    window.clearSearchDeals = clearSearchDeals;

    function openDealModal(target) {
        if (!target) return;
        const card = (target.classList && target.classList.contains('deal-card'))
            ? target
            : (target.closest ? target.closest('.deal-card') : null);

        if (!card) return;
        activeCardEl = card;

        const orderId = card.getAttribute('data-order-id') || '#SB-KSP01';
        const service = card.getAttribute('data-service') || 'Full Home Cleaning';
        const customer = card.getAttribute('data-customer') || 'Customer';
        const mitra = card.getAttribute('data-mitra') || 'Mitra';
        const desc = card.getAttribute('data-desc') || '-';
        const schedule = card.getAttribute('data-schedule') || '-';
        const location = card.getAttribute('data-location') || '-';
        const basePrice = card.getAttribute('data-base-price') || 'Rp 0';
        const dealPrice = card.getAttribute('data-deal-price') || 'Rp 0';

        const setTxt = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.innerText = val;
        };

        setTxt('modalOrderId', orderId);
        setTxt('modalServiceTitle', service);
        setTxt('modalCustomerName', customer);
        setTxt('modalMitraName', mitra);
        setTxt('modalWorkDesc', desc);
        setTxt('modalSchedule', schedule);
        setTxt('modalLocation', location);
        setTxt('modalBasePrice', basePrice);
        setTxt('modalDealPrice', dealPrice);

        const noteInput = document.getElementById('adminDealNote');
        if (noteInput) noteInput.value = '';

        // Tautan ke detail pesanan lengkap
        const fullLink = document.getElementById('modalFullOrderDetailLink');
        if (fullLink) {
            const cleanNum = orderId.replace(/[^0-9]/g, '') || '1024';
            fullLink.href = `/admin/pesanan/${cleanNum}`;
            const linkSpan = fullLink.querySelector('span');
            if (linkSpan) {
                linkSpan.innerText = `Buka Rincian Pesanan Penuh & Rekber (#SB${cleanNum})`;
            }
        }

        // Render Riwayat Nego
        const logBox = document.getElementById('modalNegoLog');
        if (logBox) {
            logBox.innerHTML = '';
            const rawHistory = card.getAttribute('data-nego-history') || '[]';
            try {
                const logs = JSON.parse(rawHistory);
                if (Array.isArray(logs) && logs.length > 0) {
                    logs.forEach(item => {
                        const isCust = (item.role === 'Customer');
                        const row = document.createElement('div');
                        row.className = `p-2 rounded-xl text-[10px] ${isCust ? 'bg-sky-50 border border-sky-200 text-sky-900' : 'bg-emerald-50 border border-emerald-200 text-emerald-900'}`;
                        row.innerHTML = `<strong class="block text-[9px] uppercase tracking-wide ${isCust ? 'text-sky-700' : 'text-emerald-700'}">${item.role}</strong> <span class="font-medium">${item.text}</span>`;
                        logBox.appendChild(row);
                    });
                } else {
                    logBox.innerHTML = '<span class="text-gray-400 text-[10px]">Data kesepakatan langsung tanpa riwayat tawar.</span>';
                }
            } catch (e) {
                logBox.innerHTML = '<span class="text-gray-400 text-[10px]">Data kesepakatan langsung tanpa riwayat tawar.</span>';
            }
        }

        const modal = document.getElementById('dealDetailModal');
        const panel = document.getElementById('dealDetailPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto', 'visible');
            modal.setAttribute('aria-hidden', 'false');
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
            document.body.classList.add('sb-scroll-locked');
        }
    }
    window.openDealModal = openDealModal;

    function closeDealModal() {
        const modal = document.getElementById('dealDetailModal');
        const panel = document.getElementById('dealDetailPanel');
        if (panel) {
            panel.classList.remove('translate-y-0');
            panel.classList.add('translate-y-full');
        }
        if (modal) {
            modal.classList.remove('pointer-events-auto');
            modal.classList.add('pointer-events-none');
        }
        setTimeout(() => {
            if (modal) {
                modal.classList.remove('opacity-100', 'visible');
                modal.classList.add('opacity-0', 'invisible');
                modal.setAttribute('aria-hidden', 'true');
            }
            document.body.classList.remove('sb-scroll-locked');
        }, 220);
    }
    window.closeDealModal = closeDealModal;

    async function handleDealAction(action) {
        const noteEl = document.getElementById('adminDealNote');
        const note = noteEl ? noteEl.value.trim() : '';
        const orderIdEl = document.getElementById('modalOrderId');
        const orderId = orderIdEl ? orderIdEl.innerText : '#SB-KSP01';

        const toastFn = (typeof window.showToast === 'function')
            ? window.showToast
            : (typeof showToast === 'function' ? showToast : function(t, m) { alert(`${t}: ${m}`); });

        if ((action === 'revision' || action === 'reject') && note === '') {
            toastFn('Catatan Diperlukan', 'Harap isi alasan / catatan untuk permintaan revisi atau penolakan.', 'warning');
            return;
        }

        const cleanCode = orderId.replace(/^#/, '');
        const endpointByAction = {
            confirm: `/api/orders/${encodeURIComponent(cleanCode)}/admin-confirm`,
            revision: `/api/orders/${encodeURIComponent(cleanCode)}/admin-revision`,
            reject: `/api/orders/${encodeURIComponent(cleanCode)}/admin-reject`
        };
        try {
            const response = await fetch(endpointByAction[action], {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ note })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Tindakan Admin belum berhasil disimpan.');
            }
        } catch (error) {
            toastFn('Tindakan Gagal', error.message || 'Silakan coba lagi.', 'error');
            return;
        }

        closeDealModal();

        if (action === 'confirm') {
            if (activeCardEl) {
                activeCardEl.setAttribute('data-status', 'dikonfirmasi');
                const badge = activeCardEl.querySelector('.deal-status-badge');
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 deal-status-badge';
                    badge.innerText = 'Dikonfirmasi Admin';
                }
            }
            updateStatCounters();
            syncOrderStatusToCustomer(orderId, 'siap_bayar', 7);

            toastFn('Kesepakatan Dikonfirmasi', `Kesepakatan ${orderId} telah diverifikasi. Customer kini dapat melakukan pembayaran.`, 'success');
        } else if (action === 'revision') {
            if (activeCardEl) {
                activeCardEl.setAttribute('data-status', 'revisi');
                const badge = activeCardEl.querySelector('.deal-status-badge');
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shrink-0 deal-status-badge';
                    badge.innerText = 'Diminta Revisi';
                }
            }
            updateStatCounters();
            toastFn('Permintaan Revisi Dikirim', `Instruksi perbaikan untuk ${orderId} telah dikirim ke Customer & Mitra.`, 'info');
        } else if (action === 'reject') {
            if (activeCardEl) {
                activeCardEl.setAttribute('data-status', 'ditolak');
                const badge = activeCardEl.querySelector('.deal-status-badge');
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0 deal-status-badge';
                    badge.innerText = 'Ditolak Admin';
                }
            }
            updateStatCounters();
            syncOrderStatusToCustomer(orderId, 'semua_ditolak', 0);
            toastFn('Kesepakatan Ditolak', `Kesepakatan ${orderId} dibatalkan dengan alasan: ${note}`, 'error');
        }
    }
    window.handleDealAction = handleDealAction;

    function updateStatCounters() {
        const cards = Array.from(document.querySelectorAll('.deal-card'));
        let waiting = 0, confirmed = 0, revision = 0, rejected = 0;
        cards.forEach(c => {
            const st = c.getAttribute('data-status');
            if (st === 'menunggu') waiting++;
            else if (st === 'dikonfirmasi') confirmed++;
            else if (st === 'revisi') revision++;
            else if (st === 'ditolak') rejected++;
        });

        const tabWaiting = document.querySelector('.deal-stat-tab[data-status="menunggu"] div:first-child');
        const tabConfirmed = document.querySelector('.deal-stat-tab[data-status="dikonfirmasi"] div:first-child');
        const tabRevision = document.querySelector('.deal-stat-tab[data-status="revisi"] div:first-child');
        const tabRejected = document.querySelector('.deal-stat-tab[data-status="ditolak"] div:first-child');

        if (tabWaiting) tabWaiting.innerText = waiting;
        if (tabConfirmed) tabConfirmed.innerText = confirmed;
        if (tabRevision) tabRevision.innerText = revision;
        if (tabRejected) tabRejected.innerText = rejected;
    }

    function syncOrderStatusToCustomer(orderId, newStatus, step) {
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) {
                const list = JSON.parse(raw);
                list.forEach(item => {
                    const cleanCode = (item.code || '').replace('#', '');
                    const cleanOrderId = (orderId || '').replace('#', '');
                    if (cleanCode === cleanOrderId || (!cleanOrderId.startsWith('SB-KSP') && item.status === 'menunggu_konfirmasi_admin')) {
                        item.status = newStatus;
                        item.step = step;
                    }
                });
                localStorage.setItem('sb_customer_orders', JSON.stringify(list));
            }
            const rawAct = localStorage.getItem('sb_active_order');
            if (rawAct) {
                const act = JSON.parse(rawAct);
                const cleanCode = (act.code || '').replace('#', '');
                const cleanOrderId = (orderId || '').replace('#', '');
                if (cleanCode === cleanOrderId || (!cleanOrderId.startsWith('SB-KSP') && act.status === 'menunggu_konfirmasi_admin')) {
                    act.status = newStatus;
                    act.step = step;
                    localStorage.setItem('sb_active_order', JSON.stringify(act));
                }
            }
        } catch(e) {}
    }
    window.syncOrderStatusToCustomer = syncOrderStatusToCustomer;

    function renderDynamicCustomerDeals() {
        const container = document.getElementById('dynamicAdminDealsContainer');
        const emptyState = document.getElementById('adminDealsEmptyState');
        const statMenunggu = document.getElementById('statMenungguCount');
        if (!container) return;
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            let list = raw ? JSON.parse(raw) : [];

            // Gabungkan juga dari sb_active_order jika statusnya menunggu_konfirmasi_admin
            const rawAct = localStorage.getItem('sb_active_order');
            if (rawAct) {
                const act = JSON.parse(rawAct);
                if (act && (act.code || act.id)) {
                    const c = (act.code || act.id).replace('#', '');
                    const idx = list.findIndex(x => (x.code || x.id || '').replace('#', '') === c);
                    if (idx >= 0) {
                        list[idx] = { ...list[idx], ...act };
                    } else {
                        list.unshift(act);
                    }
                }
            }

            // Gabungkan data dari database server agar selalu up-to-date
            @if(isset($waitingOrders) && $waitingOrders->isNotEmpty())
                const serverWaitingOrders = @json($waitingOrders);
                serverWaitingOrders.forEach(so => {
                    const c = (so.code || '').replace('#', '');
                    const exist = list.find(x => (x.code || x.id || '').replace('#', '') === c);
                    if (exist) {
                        exist.status = so.status;
                        exist.mitraName = so.mitra_name || exist.mitraName || 'Kala';
                        exist.budget = so.biaya || exist.budget;
                        exist.biaya = so.biaya || exist.biaya;
                    } else {
                        list.unshift({
                            code: so.code,
                            customerName: so.customer_name || 'Customer',
                            customerPhone: so.customer_phone,
                            mitraName: so.mitra_name || 'Kala',
                            layanan: so.layanan,
                            jadwal: so.jadwal,
                            alamat: so.alamat,
                            budget: so.biaya,
                            biaya: so.biaya,
                            status: so.status,
                            step: so.step
                        });
                    }
                });
            @endif

            const waitingDeals = list.filter(o => o.status === 'menunggu_konfirmasi_admin');
            if (waitingDeals.length === 0) {
                container.innerHTML = '';
                container.removeAttribute('data-rendered-key');
                if (emptyState) emptyState.classList.remove('hidden');
                if (statMenunggu) statMenunggu.innerText = '0';
                return;
            }

            if (emptyState) emptyState.classList.add('hidden');
            if (statMenunggu) statMenunggu.innerText = waitingDeals.length;

            const currentKey = waitingDeals.map(o => `${o.code || o.id}_${o.status}_${o.mitraName}_${o.budget || o.biaya}`).join('|');
            if (container.getAttribute('data-rendered-key') === currentKey) {
                return;
            }
            container.setAttribute('data-rendered-key', currentKey);

            container.innerHTML = waitingDeals.map(o => {
                const kode = o.code || o.id || '#SB-SURUH-8821';
                const cust = o.customerName || 'Customer SayaBantu';
                const mitra = o.mitraName || 'Kala';
                const layanan = o.layanan || 'Pekerjaan Serabutan';
                const jadwal = o.jadwal || o.waktu || 'Hari ini';
                const lokasi = o.alamat || 'Lokasi Pekerjaan';
                const biaya = o.budget ? ('Rp ' + o.budget) : (o.biaya ? (o.biaya.startsWith('Rp') ? o.biaya : 'Rp ' + o.biaya) : 'Rp 50.000');

                return `
                    <div class="deal-card p-4 rounded-[22px] bg-white border border-slate-200 shadow-sm space-y-3 transition-all hover:shadow-md cursor-pointer"
                        style="background-color: #ffffff !important; background: #ffffff !important; border: 1.5px solid #E2E8F0 !important; animation: none !important;"
                        data-status="menunggu"
                        data-order-id="${kode}"
                        data-customer="${cust}"
                        data-mitra="${mitra}"
                        data-service="${layanan}"
                        data-schedule="${jadwal}"
                        data-location="${lokasi}"
                        data-base-price="${biaya}"
                        data-deal-price="${biaya}"
                        data-created-at="Baru saja"
                        data-desc="Customer telah memilih ${mitra} dan menyepakati detail pekerjaan. Menunggu konfirmasi Admin sebelum pembayaran."
                        data-nego-history='[{"role":"Customer","text":"Memilih ${mitra} sebagai 1 mitra terpilih."},{"role":"Mitra","text":"Menyetujui rincian pekerjaan dan tarif ${biaya}."},{"role":"System","text":"Diteruskan ke tahap konfirmasi Admin."}]'
                        onclick="window.openDealModal(this)"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-[#173B67] bg-sky-50 px-2.5 py-0.5 rounded-lg border border-sky-100">
                                        ${kode}
                                    </span>
                                    <h3 class="text-xs font-bold text-slate-900 truncate">${layanan}</h3>
                                </div>
                                <p class="text-[10px] text-slate-600 mt-1">
                                    Customer: <strong class="text-slate-900">${cust}</strong> • Mitra Terpilih: <strong class="text-[#173B67]">${mitra}</strong>
                                </p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0 deal-status-badge">
                                Perlu Cek Admin
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-slate-100">
                            <span class="text-slate-500 text-[10px] flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400"></i> ${jadwal}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-900">${biaya}</span>
                                <button
                                    type="button"
                                    onclick="event.stopPropagation(); window.openDealModal(this.closest('.deal-card'));"
                                    class="tinjau-aksi-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#173B67] hover:bg-[#122e50] text-white text-[10px] font-bold active:scale-95 transition-all cursor-pointer shadow-xs"
                                    aria-label="Tinjau Aksi"
                                >
                                    <span>Tinjau Aksi</span>
                                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch(e) {}
    }
    window.renderDynamicCustomerDeals = renderDynamicCustomerDeals;

    // EVENT DELEGATION AGAR KLIK SELALU BERJALAN MULUS
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.tinjau-aksi-btn');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            const card = btn.closest('.deal-card');
            if (card) window.openDealModal(card);
            return;
        }

        const card = e.target.closest('.deal-card');
        if (card && !e.target.closest('#dealDetailModal') && !e.target.closest('button')) {
            window.openDealModal(card);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        renderDynamicCustomerDeals();
    });

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        renderDynamicCustomerDeals();
    }
</script>
@endpush

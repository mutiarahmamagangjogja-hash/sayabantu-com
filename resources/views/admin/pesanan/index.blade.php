@extends('layouts.admin')

@section('title', 'Suruhan - SayaBantu.com')
@section('header-gap-style', '0.6cm')
@section('page-title', 'Suruhan')
@section('page-subtitle', 'Pantau permintaan layanan, proses kesepakatan, dan perkembangan pekerjaan')

{{-- =========================================================
HERO AREA (DI DALAM TOP GRADIENT UNIFORM)
========================================================= --}}
@section('hero')

@php
    $ordersMap = $ordersDetail ?? [];

    $countSemua = $countSemua ?? 0;
    $countMenunggu = $countMenunggu ?? 0;
    $countDiproses = $countDiproses ?? 0;
    $countSelesai = $countSelesai ?? 0;
    $countKonflik = $countKonflik ?? 0;
@endphp

    {{-- SUMMARY (5 CARD SEJAJAR SEBAGAI FILTER LANGSUNG) --}}
    <section class="pesanan-summary pb-0" style="padding-top: 0.6cm;">
        <div class="summary-grid">
            {{-- SEMUA --}}
            <button type="button" class="summary-card summary-blue active cursor-pointer hover:scale-105 active:scale-95 transition-all text-center w-full" onclick="filterOrders('semua', this)" title="Lihat Semua Pesanan" data-summary="semua">
                <span class="summary-label">Semua</span>
                <span class="summary-number">{{ $countSemua }}</span>
            </button>

            {{-- MENUNGGU --}}
            <button type="button" class="summary-card summary-yellow cursor-pointer hover:scale-105 active:scale-95 transition-all text-center w-full" onclick="filterOrders('menunggu', this)" title="Filter Pesanan Menunggu" data-summary="menunggu">
                <span class="summary-label">Menunggu</span>
                <span class="summary-number">{{ $countMenunggu }}</span>
            </button>

            {{-- DIPROSES --}}
            <button type="button" class="summary-card summary-orange cursor-pointer hover:scale-105 active:scale-95 transition-all text-center w-full" onclick="filterOrders('diproses', this)" title="Filter Pesanan Diproses" data-summary="diproses">
                <span class="summary-label">Diproses</span>
                <span class="summary-number">{{ $countDiproses }}</span>
            </button>

            {{-- SELESAI --}}
            <button type="button" class="summary-card summary-green cursor-pointer hover:scale-105 active:scale-95 transition-all text-center w-full" onclick="filterOrders('selesai', this)" title="Filter Pesanan Selesai" data-summary="selesai">
                <span class="summary-label">Selesai</span>
                <span class="summary-number">{{ $countSelesai }}</span>
            </button>

            {{-- KONFLIK --}}
            <button type="button" class="summary-card summary-red cursor-pointer hover:scale-105 active:scale-95 transition-all text-center w-full" onclick="filterOrders('konflik', this)" title="Filter Pesanan Konflik" data-summary="konflik">
                <span class="summary-label">Konflik</span>
                <span class="summary-number">{{ $countKonflik }}</span>
            </button>
        </div>
    </section>

@endsection

@section('content')

<div class="pesanan-main" style="margin-top: 0.6cm;">

    {{-- =====================================================
    DAFTAR PESANAN
    ====================================================== --}}

    <section>

        <div class="section-header" style="margin-bottom: 0.6cm;">

            <div>

                <h3 class="section-title text-white font-bold" style="color: #ffffff !important;">
                    Daftar Pesanan
                </h3>

                <p class="mt-1 text-[9px] leading-relaxed text-white/70">
                    Klik pesanan untuk melihat detail.
                </p>

            </div>

            <div class="flex items-center gap-2">
                <span
                    id="visibleOrderCount"
                    class="section-count"
                    style="background: rgba(255, 255, 255, 0.20); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.30);"
                >
                    {{ $countSemua ?? 0 }} Pesanan
                </span>
                <button
                    type="button"
                    onclick="loadAndRenderAdminOrders(false)"
                    class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-white/10 hover:bg-white/20 text-white/80 border border-white/20 transition-all cursor-pointer flex items-center gap-1 active:scale-95"
                    title="Segarkan Data Real-Time"
                >
                    <i class="fa-solid fa-arrows-rotate text-[8px]"></i>
                    <span>Segarkan</span>
                </button>
            </div>

        </div>


        {{-- =================================================
        PENCARIAN PESANAN (KODE / CUSTOMER / MITRA / LAYANAN)
        ================================================== --}}
        <div class="relative" style="margin-bottom: 0.6cm;">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-white/60 text-xs"></i>
            <input
                type="text"
                id="searchOrderInput"
                placeholder="Cari kode pesanan, customer, mitra, layanan..."
                oninput="filterOrders(currentOrderFilter, null, this.value)"
                class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-white/15 text-white placeholder-white/60 text-xs border border-white/25 focus:outline-none focus:border-sky-300 focus:bg-white/20 transition-all font-medium"
            >
        </div>


        {{-- =================================================
        ORDER LIST
        ================================================== --}}

        <div
            id="orderList"
            style="display: flex; flex-direction: column; gap: 0.6cm;"
        >
            {{-- DYNAMIC REAL-TIME ORDERS DARI DATABASE --}}
            <div id="dynamicAdminOrdersContainer" style="display: flex; flex-direction: column; gap: 0.6cm;">
                @if(isset($dbOrders) && $dbOrders->count() > 0)
                    @foreach($dbOrders as $o)
                        @php
                            $cleanCode = ltrim($o->code, '#');
                            $st = strtolower($o->status ?? '');
                            if ($st === 'selesai') {
                                $filter = 'selesai';
                                $statusLabel = 'Selesai';
                                $statusClass = 'status-success';
                            } elseif (in_array($st, ['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer'])) {
                                $filter = 'konflik';
                                $statusLabel = 'Konflik';
                                $statusClass = 'status-conflict';
                            } elseif (in_array($st, ['siap_bayar', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi', 'diproses', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran'])) {
                                $filter = 'diproses';
                                $statusLabel = ($st === 'dikonfirmasi' || $st === 'terjadwal') ? 'Dikonfirmasi' : 'Diproses';
                                $statusClass = ($st === 'dikonfirmasi' || $st === 'terjadwal') ? 'status-success' : 'status-process';
                            } else {
                                $filter = 'menunggu';
                                $statusLabel = 'Menunggu';
                                $statusClass = 'status-waiting';
                            }
                            $custName = $o->customer_name ?: 'Customer';
                            $mitraName = $o->mitra_name ?: ($o->mitra_id ? 'Mitra Terpilih' : 'Belum ada');
                            $srvName = $o->layanan ?: 'Suruhan Jasa';
                            $dateStr = $o->jadwal ?: ($o->created_at ? $o->created_at->format('d/m/Y • H:i') : 'Hari ini');
                            $priceStr = 'Rp ' . $o->biaya;
                        @endphp
                        <a
                            href="/admin/pesanan/{{ $cleanCode }}"
                            class="order-item order-card block"
                            data-status="{{ $filter }}"
                        >
                            <div class="order-card-top">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="order-number">#{{ $cleanCode }}</p>
                                        <p class="order-date">{{ $dateStr }}</p>
                                    </div>
                                    <span class="order-status {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>
                            <div class="order-card-body">
                                <p class="order-service">{{ $srvName }}</p>
                                <div class="order-detail">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Customer: {{ $custName }}</span>
                                </div>
                                <div class="order-detail">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Mitra: {{ $mitraName }}</span>
                                </div>
                            </div>
                            <div class="order-card-footer">
                                <div>
                                    <p class="order-price-label">Total</p>
                                    <p class="order-price">{{ $priceStr }}</p>
                                </div>
                                <span class="detail-button">
                                    Detail
                                    <i class="fa-solid fa-chevron-right text-[7px]"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>


        {{-- =================================================
        EMPTY STATE
        ================================================== --}}

        <div
            id="emptyState"
            class="empty-state {{ ($countSemua ?? 0) > 0 ? 'hidden' : '' }}"
        >

            <div class="empty-icon">

                <i class="fa-regular fa-folder-open text-sm"></i>

            </div>

            <h3>
                Tidak ada pesanan
            </h3>

            <p>
                Belum ada pesanan pada kategori ini.
            </p>

        </div>

</div>


@push('scripts')

<script>
function renderAdminOrdersList(orders, counts) {
    const container = document.getElementById('dynamicAdminOrdersContainer');
    const emptyState = document.getElementById('emptyState');
    const countElement = document.getElementById('visibleOrderCount');

    let countSemua = orders.length;
    let countMenunggu = 0;
    let countDiproses = 0;
    let countSelesai = 0;
    let countKonflik = 0;

    if (counts) {
        countSemua = counts.semua ?? countSemua;
        countMenunggu = counts.menunggu ?? 0;
        countDiproses = counts.diproses ?? 0;
        countSelesai = counts.selesai ?? 0;
        countKonflik = counts.konflik ?? 0;
    } else {
        orders.forEach(o => {
            const st = (o.status || '').toLowerCase();
            if (st === 'selesai') {
                countSelesai++;
            } else if (['konflik', 'komplain', 'dibatalkan', 'cancelled_by_admin', 'cancelled_by_customer'].includes(st)) {
                countKonflik++;
            } else if (['siap_bayar', 'terjadwal', 'otw', 'berjalan', 'dikonfirmasi', 'diproses', 'menunggu_pembayaran', 'menunggu_konfirmasi_pembayaran'].includes(st)) {
                countDiproses++;
            } else {
                countMenunggu++;
            }
        });
    }

    const elSemua = document.querySelector('button[data-summary="semua"] .summary-number');
    const elMenunggu = document.querySelector('button[data-summary="menunggu"] .summary-number');
    const elDiproses = document.querySelector('button[data-summary="diproses"] .summary-number');
    const elSelesai = document.querySelector('button[data-summary="selesai"] .summary-number');
    const elKonflik = document.querySelector('button[data-summary="konflik"] .summary-number');

    if (elSemua) elSemua.innerText = countSemua;
    if (elMenunggu) elMenunggu.innerText = countMenunggu;
    if (elDiproses) elDiproses.innerText = countDiproses;
    if (elSelesai) elSelesai.innerText = countSelesai;
    if (elKonflik) elKonflik.innerText = countKonflik;

    if (orders.length === 0) {
        if (container) container.innerHTML = '';
        if (emptyState) emptyState.classList.remove('hidden');
        if (countElement) countElement.textContent = '0 Pesanan';
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (countElement) countElement.textContent = orders.length + ' Pesanan';

    if (container) {
        container.innerHTML = orders.map(o => {
            const cleanCode = (o.code || o.id || '#SB').replace('#', '');
            const customerName = o.customerName || o.customer_name || 'Customer';
            const mitraName = o.mitraName || o.mitra_name || (o.selectedMitraId ? 'Mitra Terpilih' : 'Belum ada');
            const serviceName = o.layanan || 'Suruhan Jasa';
            const dateStr = o.jadwal || o.tanggal || o.createdAt || 'Hari ini';
            const priceStr = o.biaya ? ('Rp ' + o.biaya) : (o.budget ? ('Rp ' + o.budget) : 'Rp 0');

            const filterStatus = o.adminFilter || 'diproses';
            const statusLabel = o.statusLabel || 'Diproses';
            const statusClass = o.statusClass || 'status-process';

            return `
            <a
                href="/admin/pesanan/${cleanCode}"
                class="order-item order-card block"
                data-status="${filterStatus}"
            >
                <div class="order-card-top">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="order-number">#${cleanCode}</p>
                            <p class="order-date">${dateStr}</p>
                        </div>
                        <span class="order-status ${statusClass}">
                            ${statusLabel}
                        </span>
                    </div>
                </div>
                <div class="order-card-body">
                    <p class="order-service">${serviceName}</p>
                    <div class="order-detail">
                        <i class="fa-solid fa-user"></i>
                        <span>Customer: ${customerName}</span>
                    </div>
                    <div class="order-detail">
                        <i class="fa-solid fa-handshake"></i>
                        <span>Mitra: ${mitraName}</span>
                    </div>
                </div>
                <div class="order-card-footer">
                    <div>
                        <p class="order-price-label">Total</p>
                        <p class="order-price">${priceStr}</p>
                    </div>
                    <span class="detail-button">
                        Detail
                        <i class="fa-solid fa-chevron-right text-[7px]"></i>
                    </span>
                </div>
            </a>
            `;
        }).join('');
    }

    // Terapkan filter status & kata kunci aktif
    filterOrders(currentOrderFilter, null, currentOrderKeyword);
}

function loadAndRenderAdminOrders() {
    fetch('/admin/api/orders')
        .then(r => r.json())
        .then(data => {
            if (data && data.success && Array.isArray(data.orders)) {
                renderAdminOrdersList(data.orders, data.counts);
            }
        })
        .catch(() => {});
}
window.loadAndRenderAdminOrders = loadAndRenderAdminOrders;

var currentOrderFilter = 'semua';
var currentOrderKeyword = '';

function filterOrders(status, btnEl, keyword) {
    if (status) currentOrderFilter = status;
    status = currentOrderFilter || 'semua';

    if (typeof keyword === 'string') {
        currentOrderKeyword = keyword.toLowerCase().trim();
    }

    // 1. Highlight matching summary card
    document.querySelectorAll('.summary-card').forEach(function(card) {
        card.classList.remove('active');
        card.style.outline = 'none';
        card.style.boxShadow = '';
        card.style.transform = '';
    });

    const targetSummary = btnEl || document.querySelector('.summary-card[data-summary="' + status + '"]');
    if (targetSummary) {
        targetSummary.classList.add('active');
        targetSummary.style.outline = '2.5px solid #ffffff';
        targetSummary.style.boxShadow = '0 0 14px rgba(255, 255, 255, 0.45)';
        targetSummary.style.transform = 'translateY(-2px) scale(1.04)';
    }

    // 2. Filter order cards
    const items = document.querySelectorAll('.order-item');
    const emptyState = document.getElementById('emptyState');
    const countElement = document.getElementById('visibleOrderCount');
    let visibleCount = 0;

    items.forEach(function(item) {
        const itemStatus = item.getAttribute('data-status');
        const matchesStatus = (status === 'semua' || status === 'all' || itemStatus === status);
        const matchesKeyword = !currentOrderKeyword || (item.textContent || '').toLowerCase().indexOf(currentOrderKeyword) !== -1;

        if (matchesStatus && matchesKeyword) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // 3. Update count & empty state
    if (visibleCount === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
    } else {
        if (emptyState) emptyState.classList.add('hidden');
    }

    if (countElement) {
        countElement.textContent = visibleCount + ' Pesanan';
    }
}
window.filterOrders = filterOrders;

// Auto filter on page load if query parameter exists
document.addEventListener('DOMContentLoaded', function() {
    loadAndRenderAdminOrders();
    setInterval(() => loadAndRenderAdminOrders(false), 5000);
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam) {
        filterOrders(statusParam);
    }
});
</script>

@endpush

@endsection
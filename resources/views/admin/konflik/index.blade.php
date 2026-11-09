@extends('layouts.admin')

@section('title', 'Pusat Konflik - SayaBantu.com')
@section('page-title', 'Pusat Konflik')
@section('page-subtitle', 'Mediasi & Penyelesaian Masalah')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}
@section('hero')
<div class="px-5 pt-1 pb-5">
    {{-- SUMMARY STATS KONFLIK --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                Status Penanganan Masalah
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">
                {{ $countButuhAksi ?? 0 }} Kasus Aktif
            </span>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center">
            <button type="button" class="konflik-stat-tab w-full p-2.5 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterKonflik('butuh_aksi')" data-filter="butuh_aksi">
                <div class="text-sm font-black text-rose-400">{{ $countButuhAksi ?? 0 }}</div>
                <div class="text-[9px] text-gray-300 mt-0.5">Butuh Aksi</div>
            </button>
            <button type="button" class="konflik-stat-tab w-full p-2.5 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterKonflik('investigasi')" data-filter="investigasi">
                <div class="text-sm font-black text-amber-400">{{ $countInvestigasi ?? 0 }}</div>
                <div class="text-[9px] text-gray-300 mt-0.5">Investigasi</div>
            </button>
            <button type="button" class="konflik-stat-tab w-full p-2.5 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterKonflik('selesai')" data-filter="selesai">
                <div class="text-sm font-black text-emerald-400">{{ $countSelesai ?? 0 }}</div>
                <div class="text-[9px] text-gray-300 mt-0.5">Selesai Damai</div>
            </button>
        </div>
    </div>

    {{-- FILTER TABS (DITENGAHKAN SAMA RATA KIRI KANAN) --}}
    <div class="flex items-center justify-center gap-1.5 mt-3 flex-wrap text-[10px]">
        <button type="button" class="konflik-filter-tab active px-3.5 py-1.5 rounded-full font-bold transition-all bg-white text-[#173B67] shadow-sm border border-white" onclick="filterKonflik('all')" data-status="all">
            Semua (<span id="konflikCount">{{ $countSemua ?? 0 }}</span>)
        </button>
        <button type="button" class="konflik-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterKonflik('butuh_aksi')" data-status="butuh_aksi">
            Butuh Aksi ({{ $countButuhAksi ?? 0 }})
        </button>
        <button type="button" class="konflik-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterKonflik('investigasi')" data-status="investigasi">
            Investigasi ({{ $countInvestigasi ?? 0 }})
        </button>
        <button type="button" class="konflik-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterKonflik('selesai')" data-status="selesai">
            Selesai ({{ $countSelesai ?? 0 }})
        </button>
    </div>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA
========================================================= --}}
@section('content')
<div class="space-y-3">
    @forelse($dbKonflik ?? [] as $k)
        @php
            $isButuhAksi = in_array($k->status, ['konflik', 'komplain']);
            $kStatus = $isButuhAksi ? 'butuh_aksi' : 'selesai';
            $badgeBg = $isButuhAksi ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $badgeText = $isButuhAksi ? 'Butuh Mediasi' : 'Selesai';
        @endphp
        <div class="konflik-card p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3 cursor-pointer hover:border-sky-300 active:scale-[0.99] transition-all group" data-status="{{ $kStatus }}">
            <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-sky-800">#KF-{{ str_pad((string)$k->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-[9px] px-2 py-0.5 rounded-full font-bold {{ $badgeBg }} border">
                            {{ $badgeText }}
                        </span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Pesanan {{ $k->code }} • {{ $k->layanan }}</p>
                </div>
                <span class="text-[9px] text-gray-400 shrink-0">{{ $k->updated_at ? $k->updated_at->diffForHumans() : 'Baru saja' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                <div>
                    <span class="text-[9px] text-gray-400 block font-semibold">CUSTOMER</span>
                    <span class="font-bold text-gray-800">{{ $k->customer_name }}</span>
                    <span class="text-[9px] text-gray-500 block">{{ $k->customer_phone ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-[9px] text-gray-400 block font-semibold">MITRA PENYEDIA</span>
                    <span class="font-bold text-gray-800">{{ $k->mitra_name ?: 'Mitra' }}</span>
                    <span class="text-[9px] text-gray-500 block">{{ $k->mitra_phone ?: '-' }}</span>
                </div>
            </div>

            <div>
                <p class="text-[11px] text-gray-700 leading-snug">
                    <strong class="text-gray-900">Alasan/Catatan:</strong> {{ $k->cancellation_reason ?: ($k->note ?: 'Kasus dalam penanganan mediasi admin.') }}
                </p>
            </div>
        </div>
    @empty
        {{-- EMPTY STATE KETIKA BELUM ADA KONFLIK --}}
        <div id="adminKonflikEmptyState" class="p-8 text-center bg-white rounded-[22px] border border-gray-200/80 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto border border-emerald-100">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gray-900">Belum Ada Konflik yang Perlu Dimediasi</h4>
                <p class="text-[10px] text-gray-500 mt-0.5 leading-relaxed max-w-xs mx-auto">
                    Semua operasional dan interaksi kerja antara Customer dan Mitra berjalan dengan tertib.
                </p>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    function filterKonflik(status) {
        // Efek aktif berwarna pada kartu statistik di atas (yang ada angkanya)
        document.querySelectorAll('.konflik-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'border-2', 'bg-white/[0.18]',
                'bg-rose-500/20', 'bg-amber-500/20', 'bg-emerald-500/20',
                'border-rose-400', 'border-amber-400', 'border-emerald-400',
                'shadow-rose-500/25', 'shadow-amber-500/25', 'shadow-emerald-500/25'
            );
            card.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (f === status) {
                card.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-md', 'border-2');
                if (status === 'butuh_aksi') {
                    card.classList.add('bg-rose-500/20', 'border-rose-400', 'shadow-rose-500/25');
                } else if (status === 'investigasi') {
                    card.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25');
                } else if (status === 'selesai') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25');
                }
            }
        });

        // Sinkronisasi filter tabs di bawah
        document.querySelectorAll('.konflik-filter-tab').forEach(b => {
            const bStatus = b.getAttribute('data-status');
            b.classList.remove('active', 'bg-white', 'text-[#173B67]', 'font-bold', 'border-white', 'shadow-sm');
            b.classList.add('bg-white/10', 'text-gray-300', 'hover:bg-white/20', 'border', 'border-white/10', 'font-medium');

            if (bStatus === status) {
                b.classList.remove('bg-white/10', 'text-gray-300', 'hover:bg-white/20', 'font-medium');
                b.classList.add('active', 'bg-white', 'text-[#173B67]', 'font-bold', 'border-white', 'shadow-sm');
            }
        });

        const cards = document.querySelectorAll('.konflik-card');
        let count = 0;
        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'all' || cardStatus === status) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
        const countEl = document.getElementById('konflikCount');
        if (countEl) countEl.innerText = count;
    }
    window.filterKonflik = filterKonflik;

    function resolveKonflik(btn, id) {
        const card = btn.closest('.konflik-card');
        if (!card) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
        setTimeout(() => {
            card.setAttribute('data-status', 'selesai');
            card.style.opacity = '0.8';
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Selesai';
            showToast('Mediasi Berhasil', 'Tiket #' + id + ' telah berhasil ditandai selesai damai.', 'success');
        }, 600);
    }
    window.resolveKonflik = resolveKonflik;
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Review dan Rating - SayaBantu.com')
@section('page-title', 'Review dan Rating')
@section('page-subtitle', 'Ulasan dan Kepuasan Pelanggan')

{{-- =========================================================
     TOP HERO AREA
========================================================= --}}
@section('hero')
<div class="px-5 pt-1 pb-5">
    {{-- SUMMARY STATS REVIEW --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-star text-yellow-400"></i>
                Skor Kepuasan Layanan
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                {{ ($totalReviews ?? 0) > 0 ? '98% Puas' : '0 Ulasan' }}
            </span>
        </div>

        <div class="grid grid-cols-4 gap-2 text-center">
            <button type="button" class="review-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterReview('all')" data-filter="all" title="Rata-rata Rating">
                <div class="text-xs font-black flex items-center justify-center gap-1 text-yellow-300">
                    {{ $avgRating ?? '0.0' }} <i class="fa-solid fa-star text-yellow-400 text-[9px]"></i>
                </div>
                <div class="text-[8px] text-gray-300 mt-0.5">Rata-rata</div>
            </button>
            <button type="button" class="review-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterReview('all')" data-filter="all" title="Total Ulasan">
                <div class="text-xs font-black text-white">{{ $totalReviews ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Ulasan</div>
            </button>
            <button type="button" class="review-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterReview('star5')" data-filter="star5" title="Bintang 5">
                <div class="text-xs font-black text-emerald-400">{{ $star5Count ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">★ 5 Bintang</div>
            </button>
            <button type="button" class="review-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterReview('problem')" data-filter="problem" title="Keluhan">
                <div class="text-xs font-black text-rose-400">{{ $keluhanCount ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Keluhan</div>
            </button>
        </div>
    </div>

    {{-- FILTER TABS (DITENGAHKAN SAMA RATA KIRI KANAN) --}}
    <div class="flex items-center justify-center gap-1.5 mt-3 flex-wrap text-[10px]">
        <button type="button" class="review-filter-tab active px-3.5 py-1.5 rounded-full font-bold transition-all bg-white text-[#173B67] shadow-sm border border-white" onclick="filterReview('all')" data-cat="all">
            Semua (<span id="reviewCount">{{ $totalReviews ?? 0 }}</span>)
        </button>
        <div class="relative">
            <button
                type="button"
                id="ratingFilterToggle"
                class="review-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10"
                onclick="toggleRatingFilters(this)"
                aria-expanded="false"
            >
                Bintang <i class="fa-solid fa-chevron-down ml-1 text-[8px]"></i>
            </button>
            <div id="ratingFilterOptions" class="hidden absolute z-20 top-full left-1/2 -translate-x-1/2 mt-1.5 p-1 rounded-xl bg-[#173B67] border border-sky-400/30 shadow-lg whitespace-nowrap">
                <button type="button" class="review-filter-tab px-2.5 py-1 rounded-lg font-medium text-[9px] text-gray-200 hover:bg-white/15" onclick="filterReview('star1')" data-cat="star1">1</button>
                <button type="button" class="review-filter-tab px-2.5 py-1 rounded-lg font-medium text-[9px] text-gray-200 hover:bg-white/15" onclick="filterReview('star2')" data-cat="star2">2</button>
                <button type="button" class="review-filter-tab px-2.5 py-1 rounded-lg font-medium text-[9px] text-gray-200 hover:bg-white/15" onclick="filterReview('star3')" data-cat="star3">3</button>
                <button type="button" class="review-filter-tab px-2.5 py-1 rounded-lg font-medium text-[9px] text-gray-200 hover:bg-white/15" onclick="filterReview('star4')" data-cat="star4">4</button>
                <button type="button" class="review-filter-tab px-2.5 py-1 rounded-lg font-medium text-[9px] text-gray-200 hover:bg-white/15" onclick="filterReview('star5')" data-cat="star5">5</button>
            </div>
        </div>
        <button type="button" class="review-filter-tab px-3.5 py-1.5 rounded-full font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 border border-white/10" onclick="filterReview('problem')" data-cat="problem">
            Perlu Ditindak ({{ $keluhanCount ?? 0 }})
        </button>
    </div>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA
========================================================= --}}
@section('content')
<div class="space-y-3">
    @forelse($reviews ?? [] as $r)
        @php
            $ratingVal = (int) ($r->review_rating ?: 5);
            $cat = $ratingVal === 5 ? 'star5' : ($ratingVal <= 2 ? 'problem' : 'star' . $ratingVal);
        @endphp
        <div class="review-card p-4 rounded-[22px] bg-white border border-gray-100 shadow-sm space-y-3" data-category="{{ $cat }}">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-[#173B67] border border-sky-100 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr($r->customer_name ?: 'C', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">{{ $r->customer_name ?: 'Customer' }}</h4>
                        <p class="text-[9.5px] text-gray-400">{{ $r->layanan }} • Mitra: {{ $r->mitra_name ?: 'Mitra' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 text-yellow-400 text-xs">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= $ratingVal ? 'solid' : 'regular' }} fa-star text-[10px]"></i>
                    @endfor
                </div>
            </div>
            @if($r->review_comment)
                <p class="text-[10.5px] text-gray-700 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    "{{ $r->review_comment }}"
                </p>
            @endif
            <div class="flex items-center justify-between text-[9px] text-gray-400 pt-1 border-t border-gray-50">
                <span>Pesanan {{ $r->code }}</span>
                <span>{{ $r->reviewed_at ? \Carbon\Carbon::parse($r->reviewed_at)->diffForHumans() : 'Baru saja' }}</span>
            </div>
        </div>
    @empty
        {{-- EMPTY STATE REVIEW --}}
        <div id="emptyReview" class="p-8 text-center bg-white rounded-[22px] border border-gray-100 shadow-sm space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 mx-auto flex items-center justify-center text-xl">
                <i class="fa-regular fa-star-half-stroke"></i>
            </div>
            <h4 class="text-xs font-bold text-gray-800">Belum Ada Ulasan Masuk</h4>
            <p class="text-[10px] text-gray-400">Belum ada review atau penilaian layanan dari konsumen.</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
    function toggleRatingFilters(button) {
        const options = document.getElementById('ratingFilterOptions');
        if (!options) return;

        const isHidden = options.classList.toggle('hidden');
        button.setAttribute('aria-expanded', String(!isHidden));
        button.querySelector('i')?.classList.toggle('fa-chevron-up', !isHidden);
        button.querySelector('i')?.classList.toggle('fa-chevron-down', isHidden);
    }

    function filterReview(cat) {
        // Efek aktif pada stat tab di atas
        document.querySelectorAll('.review-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'border-2', 'bg-white/[0.18]',
                'bg-emerald-500/20', 'bg-rose-500/20',
                'border-yellow-400', 'border-teal-400', 'border-emerald-400', 'border-rose-400',
                'shadow-yellow-500/25', 'shadow-teal-500/25', 'shadow-emerald-500/25', 'shadow-rose-500/25'
            );
            card.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (f === cat) {
                card.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-md', 'border-2');
                if (cat === 'all') {
                    card.classList.add('bg-white/[0.18]', 'border-teal-400', 'shadow-teal-500/25');
                } else if (cat === 'star5') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25');
                } else if (cat === 'problem') {
                    card.classList.add('bg-rose-500/20', 'border-rose-400', 'shadow-rose-500/25');
                }
            }
        });

        // Sinkronisasi filter tabs di bawah
        document.querySelectorAll('.review-filter-tab').forEach(b => {
            const bCat = b.getAttribute('data-cat');
            b.classList.remove('active', 'bg-white', 'text-[#173B67]', 'font-bold', 'border-white', 'shadow-sm');
            b.classList.add('bg-white/10', 'text-gray-300', 'hover:bg-white/20', 'border', 'border-white/10', 'font-medium');

            if (bCat === cat) {
                b.classList.remove('bg-white/10', 'text-gray-300', 'hover:bg-white/20', 'font-medium');
                b.classList.add('active', 'bg-white', 'text-[#173B67]', 'font-bold', 'border-white', 'shadow-sm');
            }
        });

        const ratingOptions = document.getElementById('ratingFilterOptions');
        const ratingToggle = document.getElementById('ratingFilterToggle');
        if (ratingOptions && !ratingOptions.classList.contains('hidden')) {
            ratingOptions.classList.add('hidden');
            ratingToggle?.setAttribute('aria-expanded', 'false');
            ratingToggle?.querySelector('i')?.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }

        const cards = document.querySelectorAll('.review-card');
        const empty = document.getElementById('emptyReview');
        let count = 0;

        cards.forEach(card => {
            const cardCategories = (card.getAttribute('data-category') || '').split(' ');
            if (cat === 'all' || cardCategories.includes(cat)) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        const reviewCountEl = document.getElementById('reviewCount');
        if (reviewCountEl && cat === 'all') {
            reviewCountEl.innerText = count;
        }

        if (count === 0) {
            if (empty) empty.classList.remove('hidden');
        } else {
            if (empty) empty.classList.add('hidden');
        }
    }
    window.filterReview = filterReview;
    window.toggleRatingFilters = toggleRatingFilters;

    function openReplyPrompt(customerName) {
        const replyText = prompt(`Balas ulasan dari ${customerName}:`, 'Terima kasih banyak atas ulasan positif dan kepercayaan Anda menggunakan layanan SayaBantu.com! 🙏');
        if (replyText !== null && replyText.trim() !== '') {
            showToast('Balasan Terkirim', `Balasan admin untuk ${customerName} telah dipublikasikan.`, 'success');
        }
    }
    window.openReplyPrompt = openReplyPrompt;
</script>
@endpush

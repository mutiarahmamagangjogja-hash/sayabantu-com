{{-- =====================================================
     PAGE HEADER STANDAR HALAMAN CUSTOMER
     -----------------------------------------------------
     Bagian paling atas setiap halaman fitur customer (selain
     Beranda) supaya user selalu tahu sedang berada di halaman
     mana. Berisi:
       1. Tombol "Kembali ke Beranda" (opsional)
       2. Judul halaman
       3. Satu kalimat deskripsi (subtitle)

     Wajib dipanggil DI DALAM <div class="customer-top-area">
     dan SETELAH @include('customer.partials.mobile-header').

     Data header diberikan melalui parameter judul dan deskripsi.
     Jika 'kembaliUrl' diisi, tombol memakai tautan biasa (tanpa handleDrawerHomeClick)
     supaya tidak dipaksa kembali ke Beranda.
====================================================== --}}
@php
    $judulHeader     = $judul ?? '';
    $deskripsiHeader = $deskripsi ?? '';
    $badgeHeader     = $badge ?? null;
    $badgeIconHeader = $badgeIcon ?? null;
    $tampilKembali   = $kembali ?? false;
    $kembaliUrl      = $kembaliUrl ?? null;
    $kembaliLabel    = $kembaliLabel ?? 'Kembali ke Beranda';

    $badgePalette = [
        'sky'     => 'text-sky-200',
        'amber'   => 'text-amber-300',
        'emerald' => 'text-emerald-300',
        'rose'    => 'text-rose-200',
        'slate'   => 'text-slate-200',
    ];
    $badgeColorHeader = $badgePalette[$badgeColor ?? 'sky'] ?? $badgePalette['sky'];
@endphp

<section class="relative z-10 px-5 pt-6 pb-5">
    @if($tampilKembali || $badgeHeader)
    <div class="flex items-center gap-2 mb-2">
        @if($tampilKembali)
        @if($kembaliUrl)
        <a href="{{ $kembaliUrl }}" onclick="if (typeof window.clearPesananDetailTimers === 'function') window.clearPesananDetailTimers();" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-white/75 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left text-[9px]"></i>
            {{ $kembaliLabel }}
        </a>
        @else
        <a href="{{ route('customer.index') }}" onclick="handleDrawerHomeClick(); event.preventDefault();" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-white/75 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left text-[9px]"></i>
            {{ $kembaliLabel }}
        </a>
        @endif
        @endif

        @if($badgeHeader)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 border border-white/15 text-[9px] font-bold {{ $badgeColorHeader }}">
            @if($badgeIconHeader)
            <i class="fa-solid {{ $badgeIconHeader }} text-[8px]"></i>
            @endif
            {{ $badgeHeader }}
        </span>
        @endif
    </div>
    @endif

    <h1 class="text-[20px] font-bold tracking-tight text-white leading-tight">{{ $judulHeader }}</h1>

    @if($deskripsiHeader)
    <p class="mt-1 text-[10px] text-white/70 leading-relaxed">{{ $deskripsiHeader }}</p>
    @endif
</section>

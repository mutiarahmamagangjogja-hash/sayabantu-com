{{-- =====================================================
     PAGE HEADER STANDAR HALAMAN FITUR MITRA
     -----------------------------------------------------
     Bagian paling atas setiap halaman fitur mitra (selain
     Beranda) supaya mitra selalu tahu sedang berada di
     halaman mana. Berisi:
       1. Judul halaman (sejajar dengan badge konteks)
       2. Satu kalimat keterangan singkat fungsi halaman
       3. Badge konteks singkat (opsional, rata kanan)

     Dipakai di dalam <main class="content-under-gradient">
     sebagai anak pertama SETIAP view tab/halaman mitra.

     Pemakaian:
       @include('mitra.partials.page-header', [
           'judul'      => 'Saldo & Pendapatan',
           'deskripsi'  => 'Pantau penghasilan dan ajukan penarikan dana.',
           'badge'      => 'Keuangan Mitra',
           'badgeColor' => 'sky',        // sky | amber | emerald | rose | slate
       ])
====================================================== --}}
@php
    $judulHeader     = $judul ?? '';
    $deskripsiHeader = $deskripsi ?? '';
    $badgeHeader     = $badge ?? null;

    $badgePalette = [
        'sky'     => 'bg-sky-500/20 text-white border-sky-400/40',
        'amber'   => 'bg-amber-500/20 text-white border-amber-400/40',
        'emerald' => 'bg-emerald-500/20 text-white border-emerald-400/40',
        'rose'    => 'bg-rose-500/20 text-white border-rose-400/40',
        'slate'   => 'bg-slate-500/20 text-white border-slate-400/40',
    ];
    $badgeColorHeader = $badgePalette[$badgeColor ?? 'sky'] ?? $badgePalette['sky'];
@endphp

<section class="hidden">
    {{-- Judul & badge disejajarkan dalam satu baris --}}
    <div class="flex items-center justify-between gap-2">
        <h1 class="text-[17px] font-extrabold tracking-tight text-white leading-tight min-w-0">{{ $judulHeader }}</h1>

        @if($badgeHeader)
        <span class="inline-flex items-center text-[9px] font-bold px-2.5 py-1 rounded-full border {{ $badgeColorHeader }} whitespace-nowrap shrink-0">
            {{ $badgeHeader }}
        </span>
        @endif
    </div>

    @if($deskripsiHeader)
    <p class="text-[10px] text-white/75 leading-relaxed">{{ $deskripsiHeader }}</p>
    @endif
</section>

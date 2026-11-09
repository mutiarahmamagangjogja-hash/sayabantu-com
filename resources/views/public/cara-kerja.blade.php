@extends('layouts.public')

@section('title', 'Cara Kerja - SayaBantu.com')

@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('public.partials.header')

    <section class="relative z-10 px-5 pt-1 pb-5">
        <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-white/75 mb-2">
            <i class="fa-solid fa-arrow-left text-[9px]"></i>
            Kembali ke Beranda
        </a>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 border border-white/15 text-[9px] font-bold text-sky-200">
            <i class="fa-solid fa-diagram-project text-[8px]"></i> Panduan Pengguna
        </span>
        <h1 class="mt-2 text-[20px] font-bold tracking-tight text-white">Cara Kerja</h1>
        <p class="mt-1 text-[10px] text-white/70">Empat langkah sederhana dari memilih layanan sampai tugas selesai.</p>
    </section>
</div>
@endsection

@section('content')
<main class="content-under-gradient px-4 pt-3 pb-4 space-y-3.5">

    @php
        $langkah = [
            [
                'no' => '1',
                'icon' => 'fa-magnifying-glass',
                'color' => 'sky',
                'title' => 'Pilih Layanan',
                'desc' => 'Buka daftar layanan dan cari yang sesuai kebutuhan Anda. Setiap layanan dilengkapi estimasi biaya agar Anda tahu gambaran harga sejak awal.',
            ],
            [
                'no' => '2',
                'icon' => 'fa-pen-to-square',
                'color' => 'amber',
                'title' => 'Isi Formulir Pesanan',
                'desc' => 'Lengkapi detail tugas: apa yang dikerjakan, lokasi, waktu, dan catatan tambahan. Semakin lengkap, semakin mudah mitra memahaminya.',
            ],
            [
                'no' => '3',
                'icon' => 'fa-user-check',
                'color' => 'emerald',
                'title' => 'Mitra Ditugaskan Otomatis',
                'desc' => 'Sistem mencocokkan mitra terdekat dengan rating terbaik di area Anda. Anda tidak perlu memilih mitra secara manual.',
            ],
            [
                'no' => '4',
                'icon' => 'fa-circle-check',
                'color' => 'indigo',
                'title' => 'Tugas Selesai',
                'desc' => 'Mitra mengerjakan tugas sesuai kesepakatan. Setelah selesai, Anda bisa memberi penilaian untuk menjaga kualitas layanan.',
            ],
        ];

        $colorMap = [
            'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
            'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
            'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        ];
    @endphp

    {{-- MASKOT HERO PANDUAN CARA KERJA --}}
    <section class="card-sb p-4 bg-gradient-to-br from-white via-sky-50/50 to-blue-50/40 border border-sky-150 overflow-hidden relative shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="shrink-0 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md relative bg-slate-900/10">
                <img src="{{ asset('images/mascot-helper.jpg') }}" alt="Maskot Bocah SayaBantu" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[8.5px] font-extrabold mb-1">
                    <i class="fa-solid fa-bolt text-emerald-600 text-[8px]"></i> Praktis &amp; Tanpa Ribet
                </span>
                <h2 class="text-xs font-bold text-[#0E1D31] leading-tight">
                    Mudah dalam 4 Langkah
                </h2>
                <p class="text-[9.5px] text-slate-600 mt-1 leading-relaxed">
                    Mulai dari pesan layanan hingga pekerjaan selesai dan rumah Anda rapi kembali bersama Bocah SayaBantu.
                </p>
            </div>
        </div>
    </section>

    @foreach($langkah as $l)
        <section class="card-sb p-3.5 flex items-start gap-3">
            <div class="w-9 h-9 shrink-0 rounded-xl border flex items-center justify-center text-sm font-bold {{ $colorMap[$l['color']] }}">
                <i class="fa-solid {{ $l['icon'] }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-bold text-slate-400">LANGKAH {{ $l['no'] }}</span>
                </div>
                <h2 class="text-xs font-bold text-slate-900 mt-0.5">{{ $l['title'] }}</h2>
                <p class="text-[10.5px] leading-snug text-slate-500 mt-1">{{ $l['desc'] }}</p>
            </div>
        </section>
    @endforeach

    {{-- CATATAN PENTING --}}
    <section class="card-sb p-4 space-y-2">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Hal yang Perlu Diketahui</h2>
        </div>
        <ul class="space-y-1.5 text-[10.5px] text-slate-600">
            <li class="flex items-start gap-2">
                <i class="fa-solid fa-check text-emerald-500 text-[9px] mt-1"></i>
                <span>Layanan bisa dilihat tanpa perlu masuk, tetapi pemesanan memerlukan akun.</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="fa-solid fa-check text-emerald-500 text-[9px] mt-1"></i>
                <span>Estimasi biaya bersifat perkiraan awal dan dapat menyesuaikan kondisi lapangan.</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="fa-solid fa-check text-emerald-500 text-[9px] mt-1"></i>
                <span>Status pesanan dapat dipantau dari halaman beranda setelah Anda masuk.</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="fa-solid fa-check text-emerald-500 text-[9px] mt-1"></i>
                <span>Untuk tugas khusus di luar daftar, gunakan kategori Jasa Lainnya.</span>
            </li>
        </ul>
    </section>

</main>
@endsection

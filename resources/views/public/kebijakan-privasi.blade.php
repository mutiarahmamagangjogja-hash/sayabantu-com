@extends('layouts.public')

@section('title', 'Kebijakan Privasi - SayaBantu.com')

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
            <i class="fa-solid fa-shield-halved text-[8px]"></i> Perlindungan Data
        </span>
        <h1 class="mt-2 text-[20px] font-bold tracking-tight text-white">Kebijakan Privasi</h1>
        <p class="mt-1 text-[10px] text-white/70">Bagaimana kami mengumpulkan dan menjaga data Anda.</p>
    </section>
</div>
@endsection

@section('content')
<main class="content-under-gradient px-4 pt-3 pb-4 space-y-3.5">

    <section class="card-sb p-3.5">
        <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 flex items-start gap-2 text-[10.5px]">
            <i class="fa-solid fa-lock text-emerald-600 mt-0.5 shrink-0"></i>
            <p class="text-slate-700 leading-snug">
                Kami menghargai kepercayaan Anda. Dokumen ini menjelaskan data apa yang kami kelola, untuk apa, dan bagaimana kami menjaganya.
            </p>
        </div>
    </section>

    @php
        $bagian = [
            [
                'icon' => 'fa-database',
                'color' => 'sky',
                'title' => '1. Data yang Kami Kumpulkan',
                'poin' => [
                    'Data akun seperti nama dan nomor telepon saat Anda mendaftar.',
                    'Detail pesanan seperti jenis layanan, lokasi, dan catatan tugas.',
                    'Data teknis sederhana seperti jenis perangkat dan halaman yang dibuka.',
                ],
            ],
            [
                'icon' => 'fa-bullseye',
                'color' => 'amber',
                'title' => '2. Tujuan Penggunaan Data',
                'poin' => [
                    'Memproses pesanan dan mencocokkan Anda dengan mitra yang sesuai.',
                    'Menghubungi Anda terkait status atau perubahan pesanan.',
                    'Meningkatkan kualitas layanan dan pengalaman penggunaan platform.',
                ],
            ],
            [
                'icon' => 'fa-share-nodes',
                'color' => 'indigo',
                'title' => '3. Pembagian Data',
                'poin' => [
                    'Data lokasi dan detail tugas dibagikan kepada mitra yang ditugaskan.',
                    'Kami tidak menjual data pribadi Anda kepada pihak ketiga.',
                    'Data hanya dibagikan jika diwajibkan oleh peraturan yang berlaku.',
                ],
            ],
            [
                'icon' => 'fa-shield-heart',
                'color' => 'emerald',
                'title' => '4. Penyimpanan & Keamanan',
                'poin' => [
                    'Data disimpan selama akun Anda masih aktif digunakan.',
                    'Kami menerapkan pembatasan akses untuk melindungi data pengguna.',
                    'Anda dapat meminta penghapusan data akun kapan saja.',
                ],
            ],
            [
                'icon' => 'fa-user-gear',
                'color' => 'purple',
                'title' => '5. Hak Anda',
                'poin' => [
                    'Melihat dan memperbarui data akun Anda melalui halaman profil.',
                    'Meminta salinan data pribadi yang kami simpan.',
                    'Menarik persetujuan penggunaan data dengan menutup akun.',
                ],
            ],
            [
                'icon' => 'fa-cookie-bite',
                'color' => 'orange',
                'title' => '6. Cookie & Penyimpanan Lokal',
                'poin' => [
                    'Kami menggunakan penyimpanan lokal untuk menjaga status masuk akun.',
                    'Cookie membantu mengingat preferensi agar penggunaan lebih nyaman.',
                    'Anda dapat menghapus data ini melalui pengaturan peramban Anda.',
                ],
            ],
        ];

        $colorMap = [
            'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
            'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
            'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
            'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
        ];
    @endphp

    @foreach($bagian as $b)
        <section class="card-sb p-4 space-y-2.5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl border flex items-center justify-center text-sm {{ $colorMap[$b['color']] }}">
                    <i class="fa-solid {{ $b['icon'] }}"></i>
                </div>
                <h2 class="text-xs font-bold text-[#0E1D31]">{{ $b['title'] }}</h2>
            </div>
            <ul class="space-y-1.5 text-[10.5px] text-slate-600">
                @foreach($b['poin'] as $poin)
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[4px] text-slate-300 mt-1.5"></i>
                        <span class="leading-snug">{{ $poin }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach

</main>
@endsection

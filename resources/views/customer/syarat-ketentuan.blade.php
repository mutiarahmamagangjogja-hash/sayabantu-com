@extends('layouts.customer')

@section('title', 'Syarat & Ketentuan - SayaBantu.com')

@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => 'Syarat & Ketentuan',
        'deskripsi' => 'Aturan penggunaan platform SayaBantu.com.',
        'kembali' => false,
    ])
</div>
@endsection

@section('content')
<main class="content-under-gradient px-4 pt-3 pb-4 space-y-3.5">

    <section class="card-sb p-3.5">
        <div class="p-2.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-start gap-2 text-[10.5px]">
            <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 shrink-0"></i>
            <p class="text-slate-700 leading-snug">
                Dengan menggunakan layanan SayaBantu.com, Anda dianggap telah membaca dan menyetujui ketentuan di bawah ini. Mohon dibaca dengan saksama.
            </p>
        </div>
    </section>

    @php
        $pasal = [
            [
                'icon' => 'fa-user-check',
                'color' => 'sky',
                'title' => '1. Ketentuan Akun Pengguna',
                'poin' => [
                    'Pengguna wajib memberikan data yang benar dan terkini saat mendaftar.',
                    'Akun bersifat pribadi dan tidak boleh dipindahtangankan tanpa persetujuan.',
                    'Pengguna bertanggung jawab menjaga kerahasiaan data masuk akunnya.',
                ],
            ],
            [
                'icon' => 'fa-cart-shopping',
                'color' => 'amber',
                'title' => '2. Pemesanan Layanan',
                'poin' => [
                    'Pesanan dibuat melalui formulir yang tersedia di platform.',
                    'Detail tugas, lokasi, dan waktu harus diisi dengan jelas dan benar.',
                    'Estimasi biaya yang ditampilkan bersifat perkiraan awal, bukan harga final.',
                ],
            ],
            [
                'icon' => 'fa-users-gear',
                'color' => 'emerald',
                'title' => '3. Penyedia Layanan (Mitra)',
                'poin' => [
                    'Mitra wajib mengerjakan tugas sesuai kesepakatan yang telah disetujui.',
                    'Mitra wajib menjaga sikap sopan dan profesional selama bertugas.',
                    'Mitra dilarang meminta pembayaran di luar kesepakatan yang tercatat.',
                ],
            ],
            [
                'icon' => 'fa-ban',
                'color' => 'rose',
                'title' => '4. Hal yang Dilarang',
                'poin' => [
                    'Memesan layanan untuk tujuan melanggar hukum atau merugikan pihak lain.',
                    'Menggunakan platform untuk menyebarkan konten palsu atau menyesatkan.',
                    'Melecehkan, mengancam, atau berbuat kasar kepada pengguna maupun mitra.',
                ],
            ],
            [
                'icon' => 'fa-triangle-exclamation',
                'color' => 'orange',
                'title' => '5. Batasan Tanggung Jawab',
                'poin' => [
                    'SayaBantu memfasilitasi pertemuan pengguna dengan penyedia layanan.',
                    'Kesepakatan akhir atas pekerjaan berada di antara pengguna dan mitra.',
                    'SayaBantu berupaya menengahi jika terjadi perselisihan antar pihak.',
                ],
            ],
            [
                'icon' => 'fa-rotate',
                'color' => 'indigo',
                'title' => '6. Perubahan Ketentuan',
                'poin' => [
                    'Ketentuan ini dapat diperbarui sewaktu-waktu sesuai kebutuhan.',
                    'Perubahan akan diinformasikan melalui halaman ini.',
                    'Penggunaan berkelanjutan berarti Anda menyetujui ketentuan terbaru.',
                ],
            ],
        ];

        $colorMap = [
            'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
            'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
            'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'rose' => 'bg-rose-50 text-rose-600 border-rose-100',
            'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
            'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        ];
    @endphp

    @foreach($pasal as $p)
        <section class="card-sb p-4 space-y-2.5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl border flex items-center justify-center text-sm {{ $colorMap[$p['color']] }}">
                    <i class="fa-solid {{ $p['icon'] }}"></i>
                </div>
                <h2 class="text-xs font-bold text-[#0E1D31]">{{ $p['title'] }}</h2>
            </div>
            <ul class="space-y-1.5 text-[10.5px] text-slate-600">
                @foreach($p['poin'] as $poin)
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

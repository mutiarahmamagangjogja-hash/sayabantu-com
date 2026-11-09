@extends('layouts.customer')

@section('title', 'Tentang SayaBantu - SayaBantu.com')

@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => 'Tentang SayaBantu',
        'deskripsi' => 'Kenali lebih dekat siapa kami dan apa yang kami tawarkan.',
        'kembali' => false,
    ])
</div>
@endsection

@section('content')
<main class="content-under-gradient px-4 pt-3 pb-4 space-y-3.5">

    {{-- MASKOT HEADER TENTANG KAMI --}}
    <section class="card-sb p-4 bg-gradient-to-br from-white via-sky-50/50 to-blue-50/40 border border-sky-150 overflow-hidden relative shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="shrink-0 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md relative bg-slate-900/10">
                <img src="{{ asset('images/mascot-helper.jpg') }}" alt="Maskot Bocah SayaBantu" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[8.5px] font-extrabold mb-1">
                    <i class="fa-solid fa-heart text-rose-500 text-[8px]"></i> Sahabat Keseharian Anda
                </span>
                <h2 class="text-xs font-bold text-[#0E1D31] leading-tight">
                    Solusi Cerdas Segala Kebutuhan
                </h2>
                <p class="text-[9.5px] text-slate-600 mt-1 leading-relaxed">
                    Platform on-demand terdepan yang menghubungkan Anda dengan personil terlatih untuk kebersihan, pindahan, belanja, dan servis teknisi.
                </p>
            </div>
        </div>
    </section>

    {{-- APA ITU SAYABANTU --}}
    <section class="card-sb p-4 space-y-2">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-question"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Apa itu SayaBantu?</h2>
        </div>
        <p class="text-[11px] leading-relaxed text-slate-600">
            <strong>SayaBantu.com</strong> merupakan platform yang dirancang untuk menghubungkan masyarakat dengan berbagai penyedia layanan sesuai kebutuhan. Melalui SayaBantu, pengguna dapat menemukan informasi layanan dan melakukan pemesanan dalam satu platform yang mudah digunakan.
        </p>
    </section>

    {{-- TUJUAN KAMI --}}
    <section class="card-sb p-4 space-y-2">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Tujuan Kami</h2>
        </div>
        <p class="text-[11px] leading-relaxed text-slate-600">
            SayaBantu hadir untuk mempermudah masyarakat dalam menemukan layanan yang sesuai dengan kebutuhan serta menciptakan ruang yang menghubungkan pengguna dengan penyedia layanan. Platform ini dirancang agar proses pencarian dan pemesanan layanan menjadi lebih praktis dan terstruktur.
        </p>
    </section>

    {{-- APA YANG KAMI TAWARKAN --}}
    <section class="card-sb p-4 space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-gift"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Apa yang Kami Tawarkan?</h2>
        </div>

        {{-- KEMUDAHAN --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-2.5">
            <div class="w-7 h-7 shrink-0 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs">
                <i class="fa-solid fa-hand-pointer"></i>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Kemudahan</h3>
                <p class="text-[10.5px] leading-snug text-slate-500 mt-0.5">
                    Membantu pengguna menemukan layanan yang sesuai dengan kebutuhan melalui platform yang sederhana dan mudah digunakan.
                </p>
            </div>
        </div>

        {{-- KETERHUBUNGAN --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-2.5">
            <div class="w-7 h-7 shrink-0 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs">
                <i class="fa-solid fa-link"></i>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Keterhubungan</h3>
                <p class="text-[10.5px] leading-snug text-slate-500 mt-0.5">
                    Menghubungkan pengguna dengan penyedia layanan dalam satu platform untuk mempermudah proses pemenuhan kebutuhan.
                </p>
            </div>
        </div>

        {{-- INFORMASI YANG JELAS --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-2.5">
            <div class="w-7 h-7 shrink-0 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Informasi yang Jelas</h3>
                <p class="text-[10.5px] leading-snug text-slate-500 mt-0.5">
                    Menyediakan informasi layanan secara terstruktur agar pengguna dapat memahami layanan sebelum melakukan pemesanan.
                </p>
            </div>
        </div>
    </section>

    {{-- TAMBAHAN: NILAI KAMI --}}
    <section class="card-sb p-4 space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-heart"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Nilai yang Kami Pegang</h2>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <i class="fa-solid fa-shield-heart text-emerald-600 text-sm"></i>
                <h3 class="text-[11px] font-bold text-slate-900 mt-1">Aman &amp; Terpercaya</h3>
                <p class="text-[9.5px] leading-snug text-slate-500 mt-0.5">Setiap penyedia layanan mengikuti proses verifikasi.</p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <i class="fa-solid fa-handshake-angle text-sky-600 text-sm"></i>
                <h3 class="text-[11px] font-bold text-slate-900 mt-1">Sepenuh Hati</h3>
                <p class="text-[9.5px] leading-snug text-slate-500 mt-0.5">Melayani dengan ramah sesuai kebutuhan pengguna.</p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <i class="fa-solid fa-money-bill-wave text-amber-600 text-sm"></i>
                <h3 class="text-[11px] font-bold text-slate-900 mt-1">Harga Transparan</h3>
                <p class="text-[9.5px] leading-snug text-slate-500 mt-0.5">Estimasi biaya ditampilkan sebelum pemesanan.</p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <i class="fa-solid fa-clock text-indigo-600 text-sm"></i>
                <h3 class="text-[11px] font-bold text-slate-900 mt-1">Siap 24 Jam</h3>
                <p class="text-[9.5px] leading-snug text-slate-500 mt-0.5">Layanan suruhan siap membantu kapan pun dibutuhkan.</p>
            </div>
        </div>
    </section>

    {{-- TAMBAHAN: KOMITMEN KAMI --}}
    <section class="card-sb p-4 space-y-2">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <h2 class="text-sm font-bold text-[#0E1D31]">Komitmen Kami</h2>
        </div>
        <p class="text-[11px] leading-relaxed text-slate-600">
            Kami terus mengembangkan SayaBantu agar semakin banyak masyarakat terbantu dan semakin banyak penyedia layanan lokal yang dapat tumbuh bersama. Setiap masukan dari pengguna menjadi bahan perbaikan kami.
        </p>
    </section>

</main>
@endsection

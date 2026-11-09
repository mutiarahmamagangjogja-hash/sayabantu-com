@extends('layouts.customer')

@section('title', $serviceName . ' - SayaBantu.com')

@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR (JUDUL + DESKRIPSI HALAMAN) --}}
    @include('customer.partials.page-header', [
        'judul' => $serviceName,
        'deskripsi' => 'Bantuan profesional yang disesuaikan dengan kebutuhan Anda.',
        'kembali' => false,
    ])
</div>
@endsection

@section('content')
<main class="content-under-gradient px-4 pt-3 pb-4 space-y-3.5">
    <section class="card-sb p-4 space-y-4">
        <div class="flex items-center justify-center h-24 rounded-2xl bg-sky-50 text-[#173B67]">
            <i class="fa-solid fa-hand-holding-heart text-4xl"></i>
        </div>

        <div>
            <h2 class="text-sm font-bold text-[#0E1D31]">{{ $serviceName }}</h2>
            <p class="mt-1 text-[11px] leading-relaxed text-slate-600">{{ $serviceDescription }}</p>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center">
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                <span class="block text-[9px] text-slate-400">Status Layanan</span>
                <strong class="block mt-0.5 text-xs text-emerald-700 font-bold">Mitra Standby</strong>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                <span class="block text-[9px] text-slate-400">Penugasan</span>
                <strong class="block mt-0.5 text-xs text-[#173B67] font-bold">Otomatis</strong>
            </div>
        </div>

        <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 text-[10px] leading-relaxed text-slate-700">
            Sistem akan mencocokkan mitra terdekat dengan rating terbaik setelah Anda mengajukan layanan.
        </div>

        <a href="{{ route('customer.index') }}#orderFormCard" class="block w-full py-3 rounded-xl bg-[#0E1D31] text-white text-center text-xs font-bold hover:bg-[#173B67] transition-colors">
            Ajukan Layanan Ini
        </a>
        <p class="text-[10px] text-center text-slate-400">
            <i class="fa-solid fa-lock text-[9px] mr-1"></i> Perlu masuk sebagai Customer untuk mengajukan pesanan.
        </p>
    </section>
</main>
@endsection

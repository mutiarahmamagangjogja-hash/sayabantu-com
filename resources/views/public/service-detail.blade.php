@extends('layouts.public')

@section('title', $serviceName . ' - SayaBantu.com')

@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    @include('public.partials.header')

    <section class="relative z-10 px-5 pt-4 pb-6">
        <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-white/75 mb-3">
            <i class="fa-solid fa-arrow-left text-[9px]"></i>
            Kembali ke Layanan
        </a>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 border border-white/15 text-[9px] font-bold text-sky-200">
            <i class="fa-solid fa-circle-info text-[8px]"></i> Detail Layanan Publik
        </span>
        <h1 class="mt-2 text-[20px] font-bold tracking-tight text-white">{{ $serviceName }}</h1>
        <p class="mt-1 text-[10px] text-white/70">Bantuan profesional yang disesuaikan dengan kebutuhan Anda.</p>
    </section>
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
                <span class="block text-[9px] text-slate-400">Mulai dari</span>
                <strong class="block mt-0.5 text-xs text-[#173B67]">Rp {{ $serviceBudget }}</strong>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                <span class="block text-[9px] text-slate-400">Penugasan</span>
                <strong class="block mt-0.5 text-xs text-emerald-700">Otomatis</strong>
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

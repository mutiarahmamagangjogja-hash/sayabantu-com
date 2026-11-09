{{-- NATIVE MOBILE BOTTOM NAVIGATION BAR --}}
@php
    $isBeranda = request()->routeIs('customer.index') || request()->routeIs('customer.home');
    $isLayanan = request()->routeIs('customer.layanan') || request()->routeIs('customer.service');
    $isMitra   = request()->routeIs('customer.mitra');
@endphp
<nav class="w-full bg-white/95 backdrop-blur-lg border-t border-slate-200/80 px-4 py-2 flex items-center justify-between shadow-bottom-nav shrink-0 z-40 relative">

    {{-- GUEST NAVIGATION --}}
    <div id="mNavGuest" class="w-full flex items-center justify-between">
        {{-- BERANDA --}}
        <a href="{{ route('customer.index') }}" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all {{ $isBeranda ? 'text-sky-600 font-extrabold' : 'text-slate-500 hover:text-slate-900 font-semibold' }}">
            <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $isBeranda ? 'bg-sky-50 text-sky-600' : '' }}">
                <i class="fa-solid fa-house text-xs"></i>
            </div>
            <span>Beranda</span>
        </a>

        {{-- SEMUA LAYANAN (KATALOG) --}}
        <a href="{{ route('customer.layanan') }}" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all {{ $isLayanan ? 'text-sky-600 font-extrabold' : 'text-slate-500 hover:text-slate-900 font-semibold' }}">
            <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $isLayanan ? 'bg-sky-50 text-sky-600' : '' }}">
                <i class="fa-solid fa-list-check text-xs"></i>
            </div>
            <span>Layanan</span>
        </a>

        {{-- MITRA --}}
        <a href="{{ route('customer.mitra') }}" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all {{ $isMitra ? 'text-sky-600 font-extrabold' : 'text-slate-500 hover:text-slate-900 font-semibold' }}">
            <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $isMitra ? 'bg-sky-50 text-sky-600' : '' }}">
                <i class="fa-solid fa-users text-xs"></i>
            </div>
            <span>Mitra</span>
        </a>

        {{-- AKUN / MASUK --}}
        <button type="button" onclick="switchLoginRole('customer'); openModal('modalLoginCustomer');" class="flex flex-col items-center justify-center gap-0.5 text-slate-500 hover:text-[#0E1D31] font-semibold text-[10px] w-14 transition-all">
            <div class="w-6 h-6 flex items-center justify-center">
                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
            </div>
            <span>Masuk</span>
        </button>
    </div>

    {{-- CUSTOMER LOGGED IN NAVIGATION: DASHBOARD, LAYANAN, PESANAN, PROFILE --}}
    <div id="mNavCustomer" class="w-full flex items-center justify-around hidden">
        {{-- DASHBOARD --}}
        <button type="button" onclick="switchCustomerNavTab('dashboard')" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all text-sky-600 font-extrabold" id="mCustNavDashboard">
            <div class="w-6 h-6 rounded-full flex items-center justify-center bg-sky-50 text-sky-600">
                <i class="fa-solid fa-house text-xs"></i>
            </div>
            <span>Dashboard</span>
        </button>

        {{-- LAYANAN --}}
        <button type="button" onclick="switchCustomerNavTab('layanan')" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all text-slate-500 hover:text-slate-900 font-semibold" id="mCustNavLayanan">
            <div class="w-6 h-6 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-list-check text-xs"></i>
            </div>
            <span>Layanan</span>
        </button>

        {{-- PESAN (CHAT) — POSISI TENGAH --}}
        <button type="button" onclick="switchCustomerNavTab('pesan')" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all text-slate-500 hover:text-slate-900 font-semibold relative" id="mCustNavPesan">
            <div class="w-6 h-6 rounded-full flex items-center justify-center relative">
                <i class="fa-regular fa-comment-dots text-xs"></i>
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-rose-500"></span>
            </div>
            <span>Pesan</span>
        </button>

        {{-- PESANAN --}}
        <button type="button" onclick="switchCustomerNavTab('pesanan')" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all text-slate-500 hover:text-slate-900 font-semibold relative" id="mCustNavPesanan">
            <div class="w-6 h-6 rounded-full flex items-center justify-center relative">
                <i class="fa-solid fa-receipt text-xs"></i>
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-rose-500"></span>
            </div>
            <span>Pesanan</span>
        </button>

        {{-- PROFILE --}}
        <button type="button" onclick="switchCustomerNavTab('profile')" class="flex flex-col items-center justify-center gap-0.5 text-[10px] w-14 transition-all text-slate-500 hover:text-slate-900 font-semibold" id="mCustNavProfile">
            <div class="w-6 h-6 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-user text-xs"></i>
            </div>
            <span>Profile</span>
        </button>
    </div>

</nav>

{{-- =========================================================
     KOMPONEN MODAL: MENCARI MITRA DISEKITAR (ALA GOJEK)
     Batas Waktu 1 Jam Khusus Pengerjaan Hari Ini
========================================================= --}}
<style>
@keyframes sbRadarSweep {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes sbRadarRipple {
    0% { transform: scale(0.6); opacity: 0.85; }
    50% { transform: scale(1.3); opacity: 0.35; }
    100% { transform: scale(1.85); opacity: 0; }
}
.animate-radar-sweep {
    animation: sbRadarSweep 3.5s linear infinite;
    transform-origin: center center;
}
.animate-radar-ripple-1 {
    animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
.animate-radar-ripple-2 {
    animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    animation-delay: 0.85s;
}
.animate-radar-ripple-3 {
    animation: sbRadarRipple 2.6s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    animation-delay: 1.7s;
}
</style>

<div id="sbGojekSearchModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 transition-all">
    <div class="relative w-full max-w-sm rounded-3xl bg-gradient-to-b from-[#0E1D31] via-[#10243E] to-[#0A1626] border-2 border-emerald-500/40 p-5 text-white shadow-2xl overflow-hidden space-y-3.5">
        
        {{-- AMBIENT GLOW EFFECTS --}}
        <div class="absolute -top-20 -left-20 w-44 h-44 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-44 h-44 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>

        {{-- TOP HEADER & BADGE --}}
        <div class="text-center space-y-1 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 text-[10px] font-black uppercase tracking-wider shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Mencari Mitra di Sekitar
            </span>
            <h3 class="text-base font-extrabold text-white tracking-tight">Menghubungkan ke Mitra Terdekat</h3>
            <p id="sbRadarTicker" class="text-[11px] text-slate-300 transition-opacity duration-300 min-h-[16px]">
                Menyiarkan suruhan ke mitra aktif dalam radius 5 km...
            </p>
        </div>

        {{-- RADAR SCANNER CONTAINER --}}
        <div class="relative w-52 h-52 mx-auto my-1 flex items-center justify-center">
            {{-- Outer static ring --}}
            <div class="absolute inset-0 rounded-full border border-emerald-500/25"></div>
            {{-- Mid static ring --}}
            <div class="absolute w-36 h-36 rounded-full border border-emerald-500/30"></div>
            {{-- Inner static ring --}}
            <div class="absolute w-20 h-20 rounded-full border border-emerald-500/40"></div>
            
            {{-- Radar crosshairs --}}
            <div class="absolute inset-x-0 top-1/2 h-px bg-emerald-500/20"></div>
            <div class="absolute inset-y-0 left-1/2 w-px bg-emerald-500/20"></div>

            {{-- Animated expanding pulse rings (Ripple Waves) --}}
            <div class="absolute w-20 h-20 rounded-full border-2 border-emerald-400 animate-radar-ripple-1"></div>
            <div class="absolute w-20 h-20 rounded-full border-2 border-emerald-400 animate-radar-ripple-2"></div>
            <div class="absolute w-20 h-20 rounded-full border-2 border-emerald-400 animate-radar-ripple-3"></div>

            {{-- Rotating Radar Sweep Beam --}}
            <div class="absolute inset-0 rounded-full pointer-events-none animate-radar-sweep" style="background: conic-gradient(from 0deg, rgba(16, 185, 129, 0.45) 0deg, rgba(16, 185, 129, 0.08) 60deg, transparent 90deg, transparent 360deg);"></div>

            {{-- Center Pin (Lokasi Penugasan) --}}
            <div class="relative z-20 flex flex-col items-center">
                <div class="w-9 h-9 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm shadow-lg shadow-emerald-500/50 ring-4 ring-emerald-400/30">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <span class="text-[8.5px] font-black text-emerald-300 bg-slate-900/90 px-2 py-0.5 rounded-full border border-emerald-500/30 mt-1 shadow">Lokasi Anda</span>
            </div>

            {{-- Floating Mitra Pins on Radar --}}
            <div class="absolute top-5 right-7 z-10 animate-bounce" style="animation-duration: 2.3s;">
                <div class="w-6 h-6 rounded-full bg-[#173B67] border-2 border-amber-400 text-amber-300 flex items-center justify-center text-[10px] shadow-md shadow-amber-500/30">
                    <i class="fa-solid fa-motorcycle"></i>
                </div>
            </div>
            <div class="absolute bottom-6 left-6 z-10 animate-bounce" style="animation-duration: 2.8s; animation-delay: 0.6s;">
                <div class="w-6 h-6 rounded-full bg-[#173B67] border-2 border-sky-400 text-sky-300 flex items-center justify-center text-[10px] shadow-md shadow-sky-500/30">
                    <i class="fa-solid fa-broom"></i>
                </div>
            </div>
            <div class="absolute bottom-9 right-8 z-10 animate-pulse" style="animation-duration: 1.8s;">
                <div class="w-5 h-5 rounded-full bg-[#173B67] border-2 border-emerald-400 text-emerald-300 flex items-center justify-center text-[9px] shadow-md">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
        </div>

        {{-- COUNTDOWN TIMER CARD (SESUAI INPUT CUSTOMER) --}}
        <div class="p-3 rounded-2xl bg-slate-900/80 border border-emerald-500/30 text-center space-y-1 relative z-10">
            <div class="flex items-center justify-center gap-1.5 text-[9.5px] font-black text-amber-400 uppercase tracking-wider">
                <i class="fa-solid fa-stopwatch animate-pulse"></i> Batas Waktu Pencarian (<span id="radarCountdownLabel">1 Jam</span>)
            </div>
            <div id="radarCountdownTimer" class="font-mono text-3xl font-black text-amber-300 tracking-wider">
                59:59
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden mt-1">
                <div id="radarCountdownBar" class="bg-gradient-to-r from-emerald-400 to-amber-400 h-1.5 rounded-full transition-all duration-1000" style="width: 100%;"></div>
            </div>
            <p class="text-[9.5px] text-slate-300 pt-0.5 leading-snug">
                Sistem mencari mitra terdekat sesuai batas waktu <span id="radarCountdownDescDuration" class="font-bold text-amber-300">1 Jam</span> yang Anda tentukan. Mitra yang merespons pertama akan langsung ditetapkan.
            </p>
        </div>

        {{-- ORDER MINI SUMMARY --}}
        <div class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-xs flex items-center justify-between relative z-10">
            <div class="min-w-0 pr-2">
                <span class="text-[9px] text-slate-400 block font-mono" id="radarOrderCode">#SB-SURUH-....</span>
                <strong class="text-[11px] font-bold text-white block truncate" id="radarOrderLayanan">Layanan</strong>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[8.5px] text-slate-400 block">Biaya Layanan</span>
                <strong class="text-xs font-black text-emerald-400" id="radarOrderBiaya">Rp 0</strong>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="space-y-1.5 pt-0.5 relative z-10">
            <button
                type="button"
                id="btnRadarGoToOrder"
                class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Pantau Status &amp; Detail Pesanan</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>
            <button
                type="button"
                onclick="closeGojekSearchModal()"
                class="w-full py-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 font-semibold text-[11px] transition-colors text-center cursor-pointer"
            >
                Tutup (Tetap Berjalan di Latar Belakang)
            </button>
        </div>
    </div>
</div>


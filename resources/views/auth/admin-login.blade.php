<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Admin - SayaBantu.com</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; background: #CBD5E1; color: #111827; margin: 0; padding: 0; }
        
        .mobile-app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            position: relative;
            overflow-x: hidden;
            background: #F7F9FC;
            box-shadow: 0 0 50px rgba(15, 23, 42, 0.20);
            display: flex;
            flex-direction: column;
        }

        /* =====================================================
           STANDARISASI GRADIENT 15 CM PERSIS SELURUH PLATFORM
        ====================================================== */
        .admin-login-top-area {
            position: relative;
            overflow: hidden;
            color: white;
            min-height: 15cm;
            height: 15cm;
            padding: 40px 20px 0;
            background:
                radial-gradient(circle at 92% 4%, rgba(255, 255, 255, 0.10), transparent 27%),
                radial-gradient(circle at 0% 42%, rgba(75, 110, 140, 0.20), transparent 32%),
                radial-gradient(circle at 100% 58%, rgba(112, 143, 164, 0.15), transparent 30%),
                linear-gradient(
                    180deg,
                    #0E1D31 0%,
                    #0E1D31 24%,
                    #11263D 38%,
                    #17334C 52%,
                    #294D65 65%,
                    #5F7C8E 77%,
                    #A7BAC6 87%,
                    #D9E3E9 94%,
                    #EDF2F6 97%,
                    #F7F9FC 100%
                );
        }

        /* DEKORASI LINGKARAN HEADER UNIFORM SAYABANTU */
        .header-circle-one {
            position: absolute;
            width: 190px;
            height: 190px;
            right: -95px;
            top: -95px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.05);
            pointer-events: none;
        }

        .header-circle-two {
            position: absolute;
            width: 155px;
            height: 155px;
            left: -70px;
            top: 75px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.035);
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="mobile-app">
    
    {{-- HEADER GRADIENT TEPAT 15 CM --}}
    <div class="admin-login-top-area">
        <div class="header-circle-one"></div>
        <div class="header-circle-two"></div>

        <div class="header-content text-center relative z-10">
            {{-- LOGO GEMBOK DENGAN SHADOW & BORDER GLOW --}}
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#173B67] to-[#0E1D31] flex items-center justify-center text-sky-400 text-xl shadow-xl mx-auto border border-sky-400/30">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <h1 class="text-2xl font-black text-white mt-3.5 tracking-tight leading-tight">
                Portal Admin SayaBantu
            </h1>
            <p class="text-xs text-white/75 mt-1 font-medium">
                Akses khusus manajemen pesanan, mitra &amp; pembayaran
            </p>

            {{-- CHIP IDENTITAS RESMI --}}
            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-semibold text-slate-200 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem Keamanan Terpadu • Sesi Terproteksi</span>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA DITARIK KE ATAS GRADIENT AGAR POSISI PAS DI TENGAH --}}
    <div class="content-under-gradient px-5 relative z-10 space-y-4 pb-8 flex-1 flex flex-col justify-between">
        
        <div class="space-y-4">
            {{-- NOTIFIKASI FLASH / ERROR / PERINGATAN --}}
            @if(session('warning'))
                <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-400/30 text-amber-900 text-xs flex items-start gap-2 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-3 rounded-2xl bg-sky-500/10 border border-sky-400/30 text-sky-900 text-xs flex items-start gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-info text-sky-600 mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-400/30 text-emerald-900 text-xs flex items-start gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-400/30 text-rose-900 text-xs flex items-start gap-2 shadow-sm">
                    <i class="fa-solid fa-circle-xmark text-rose-600 mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- KARTU FORM LOGIN ADMINISTRATOR --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-lg space-y-4 text-xs">
                
                {{-- HEADER KARTU LOGIN --}}
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold text-slate-800 leading-tight">Autentikasi Administrator</h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">Gunakan kredensial akun terverifikasi</p>
                    </div>
                    <span class="w-8 h-8 rounded-xl bg-sky-50 text-[#173B67] flex items-center justify-center text-xs shadow-xs">
                        <i class="fa-solid fa-key"></i>
                    </span>
                </div>

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Email Administrator</label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input 
                                type="email" 
                                name="email"
                                value="{{ old('email') }}" 
                                placeholder="admin@sayabantu.com"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none transition-all"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-slate-700">Kata Sandi Master</label>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input 
                                type="password" 
                                name="password"
                                id="adminPasswordInput"
                                value="" 
                                placeholder="Masukkan kata sandi admin"
                                class="w-full pl-9 pr-9 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none transition-all"
                                required
                            >
                            <button type="button" onclick="toggleAdminPassVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer" tabindex="-1" aria-label="Lihat Sandi">
                                <i class="fa-regular fa-eye" id="passToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-[#173B67] to-[#0E1D31] hover:brightness-110 active:scale-[0.99] text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                    >
                        <i class="fa-solid fa-arrow-right-to-bracket text-sky-400"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </form>

            </div>

            {{-- KARTU INFORMASI KEAMANAN (AGAR HALAMAN PROPORSIONAL & TIDAK KOSONG) --}}
            <div class="p-3.5 rounded-2xl bg-white/90 border border-slate-200/80 shadow-xs space-y-2">
                <div class="flex items-center gap-2.5 text-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-600 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-[11px] font-bold text-slate-800 leading-tight">Zona Terproteksi Administrator</h3>
                        <p class="text-[9.5px] text-slate-500 leading-tight mt-0.5">Hanya staf berwenang yang dapat mengakses halaman ini</p>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[9.5px] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-lock text-emerald-500"></i> Enkripsi 256-bit TLS
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-sky-500"></i> Sesi Auto-Logout 2 Jam
                    </span>
                </div>
            </div>
        </div>

        {{-- FOOTER COPYRIGHT & ENGINE VERSION --}}
        <div class="text-center pt-4 pb-2 space-y-1">
            <p class="text-[10px] text-slate-400 font-medium">© 2026 SayaBantu.com • Hak Cipta Dilindungi</p>
            <p class="text-[9px] text-slate-400/80 font-mono">SayaBantu Admin v1.2.0</p>
        </div>

    </div>

</div>

<script>
    function toggleAdminPassVisibility() {
        const input = document.getElementById('adminPasswordInput');
        const icon = document.getElementById('passToggleIcon');
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // =========================================================
    //  STANDARISASI JARAK GRADIENT 15 CM (PERSIS SEPERTI CUSTOMER & MITRA)
    //  - Gradient selalu tepat 15 cm (~567px)
    //  - Kartu login otomatis tertarik ke posisi proporsional dan presisi di tengah
    // =========================================================
    function syncGradientEkor() {
        const top = document.querySelector('.admin-login-top-area');
        const content = document.querySelector('.content-under-gradient');
        if (!top || !content) return;

        // Standarisasi panjang gradient 15cm (1cm = 37.795px di standar CSS)
        const targetHeightPx = Math.round(15 * 37.795); // ~567px

        top.style.height = targetHeightPx + 'px';
        top.style.minHeight = targetHeightPx + 'px';

        const headerContent = top.querySelector('.header-content');
        if (!headerContent) return;

        const headerRect = headerContent.getBoundingClientRect();
        const topRect = top.getBoundingClientRect();
        const headerContentHeight = Math.max(0, headerRect.bottom - topRect.top);
        
        // Jarak dari teks header ke kartu login
        const desiredContentTop = headerContentHeight + 18;
        const pullUp = targetHeightPx - desiredContentTop;

        content.style.marginTop = '-' + Math.max(0, pullUp) + 'px';
    }

    window.addEventListener('load', syncGradientEkor);
    window.addEventListener('resize', syncGradientEkor);
    document.addEventListener('DOMContentLoaded', syncGradientEkor);
</script>
</body>
</html>

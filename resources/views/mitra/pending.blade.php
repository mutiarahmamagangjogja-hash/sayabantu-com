<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ in_array($status ?? '', ['rejected', 'ditolak'], true) ? 'Pendaftaran Belum Disetujui' : 'Pendaftaran Sedang Ditinjau' }} - SayaBantu.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; background: #E5E7EB; color: #111827; margin: 0; padding: 0; }
        .mobile-app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            position: relative;
            overflow-x: hidden;
            background: #F7F9FC;
            box-shadow: 0 0 40px rgba(15,23,42,0.14);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .pending-top-area {
            position: relative;
            overflow: hidden;
            color: white;
            background:
                radial-gradient(circle at 92% 4%, rgba(255,255,255,0.10), transparent 27%),
                radial-gradient(circle at 0% 42%, rgba(75,110,140,0.20), transparent 32%),
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
            padding: 24px 20px 48px;
        }
    </style>
</head>
<body>

@php
    $isRejected = in_array($status ?? '', ['rejected', 'ditolak'], true);
    $mitraName = $mitra?->name ?? (session('mitra_auth.name') ?? 'Calon Mitra');
    $mitraId = $mitra?->mitra_id ?? (session('mitra_auth.mitra_id') ?? 'MTR-BARU');
    $mitraPhone = $mitra?->phone ?? (session('mitra_auth.phone') ?? '-');
    $mitraKota = $mitra?->kota ?? 'Indonesia';
    $mitraKeahlian = $mitra?->keahlian ?? (session('mitra_auth.specialty') ?? 'Full Home Cleaning');
    $rejectionReason = $reason ?? ($mitra?->rejection_reason ?? 'Berkas pendaftaran atau data persyaratan belum memenuhi ketentuan standar platform.');
    $reapplyAllowed = $canReapply ?? ($mitra?->can_reapply ?? false);
    $csHelpUrl = route('customer.pengaduan');
@endphp

<div class="mobile-app">

    <div>
        {{-- HEADER GRADIENT --}}
        <div class="pending-top-area">
            <div class="flex items-center justify-between">
                <a href="{{ route('customer.home') }}" class="text-xs text-white/80 hover:text-white flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Beranda
                </a>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isRejected ? 'bg-rose-500/30 text-rose-200 border border-rose-400/40' : 'bg-amber-500/30 text-amber-200 border border-amber-400/40' }}">
                    {{ $isRejected ? 'Pendaftaran Ditolak' : 'Antrean Moderasi Admin' }}
                </span>
            </div>

            <div class="mt-6 text-center">
                <div class="w-16 h-16 rounded-2xl {{ $isRejected ? 'bg-gradient-to-tr from-rose-500 to-red-700' : 'bg-gradient-to-tr from-amber-400 to-amber-600' }} flex items-center justify-center text-white text-2xl shadow-lg mx-auto border-2 border-white/20">
                    <i class="fa-solid {{ $isRejected ? 'fa-user-xmark' : 'fa-hourglass-half animate-spin' }}" style="{{ $isRejected ? '' : 'animation-duration: 4s;' }}"></i>
                </div>
                <h1 class="text-lg font-black text-white mt-3">Portal Calon Mitra</h1>
                <p class="text-xs text-white/75 mt-0.5">{{ $mitraName }} • <span class="font-mono text-white/90">{{ $mitraId }}</span></p>
            </div>
        </div>

        {{-- CONTENT CARD AREA --}}
        <div class="px-5 -mt-6 relative z-10 space-y-4">

            @if($isRejected)
                {{-- =========================================================================
                     SKENARIO A: REJECTION CARD (WARNA AKSEN MERAH / SLATE)
                     Heading: "Pendaftaran Belum Dapat Disetujui"
                     Box Alasan: Menampilkan alasan yang diketikkan Admin
                     Tombol Aksi: [Ajukan Perbaikan / Unggah Ulang Berkas] (jika diizinkan) atau [Hubungi Admin]
                ========================================================================== --}}
                <div class="bg-white rounded-[24px] p-5 border-2 border-rose-200 shadow-xl space-y-4">
                    
                    {{-- Heading & Icon --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-lg border border-rose-200">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <span class="text-[9.5px] font-extrabold text-rose-700 uppercase tracking-wider block">Status Verifikasi Berkas</span>
                            <h2 class="text-sm font-black text-slate-900 leading-tight mt-0.5">
                                Pendaftaran Belum Dapat Disetujui
                            </h2>
                        </div>
                    </div>

                    {{-- Box Alasan Dari Admin --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-1.5 text-xs">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block flex items-center gap-1.5">
                            <i class="fa-solid fa-comment-dots text-rose-500"></i> Catatan &amp; Alasan Penolakan Admin:
                        </span>
                        <div class="p-3 rounded-xl bg-white border border-rose-100 text-slate-800 text-[11px] leading-relaxed font-medium">
                            {{ $rejectionReason }}
                        </div>
                        <p class="text-[9.5px] text-slate-400 mt-1">
                            Tim Admin SayaBantu telah meninjau berkas pendaftaran Anda berdasarkan standar kepatuhan dan integritas mitra.
                        </p>
                    </div>

                    {{-- Informasi Hak Perbaikan --}}
                    @if($reapplyAllowed)
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 shrink-0"></i>
                            <div class="leading-tight">
                                <strong class="block font-bold">Kirim Ulang Diizinkan</strong>
                                <span class="text-[10px] text-emerald-800 mt-0.5 block">Admin memperbolehkan Anda melengkapi atau memperbarui berkas yang kurang jelas.</span>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs flex items-start gap-2">
                            <i class="fa-solid fa-circle-info text-slate-500 mt-0.5 shrink-0"></i>
                            <div class="leading-tight">
                                <strong class="block font-bold">Pendaftaran Final</strong>
                                <span class="text-[10px] text-slate-500 mt-0.5 block">Untuk informasi banding atau klarifikasi lebih lanjut, silakan hubungi tim Admin CS kami.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Tombol Aksi --}}
                    <div class="space-y-2 pt-1">
                        @if($reapplyAllowed)
                            <a
                                href="{{ route('mitra.daftar') }}"
                                class="w-full py-3 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-98 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <i class="fa-solid fa-file-arrow-up"></i>
                                <span>Ajukan Perbaikan / Unggah Ulang Berkas</span>
                            </a>
                        @endif

                        <a
                            href="{{ $csHelpUrl }}"
                            class="w-full py-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] active:scale-98 text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-headset text-sky-400 text-sm"></i>
                            <span>Hubungi Admin / Bantuan CS</span>
                        </a>

                        <form action="{{ route('mitra.logout') }}" method="POST" class="pt-1">
                            @csrf
                            <button
                                type="submit"
                                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                <span>Keluar dari Akun</span>
                            </button>
                        </form>
                    </div>

                </div>

            @else
                {{-- =========================================================================
                     SKENARIO 2: STATUS CARD: "Pendaftaran Sedang Ditinjau Admin"
                     Keterangan: "Proses verifikasi membutuhkan waktu maksimal 1x24 jam kerja."
                     Tombol Aksi: "Hubungi Bantuan / CS" dan "Keluar"
                ========================================================================== --}}
                <div class="bg-white rounded-[24px] p-5 border-2 border-amber-200 shadow-xl space-y-4">
                    
                    {{-- Status Card Heading & Icon --}}
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-lg border border-amber-200">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <div>
                            <span class="text-[9.5px] font-extrabold text-amber-700 uppercase tracking-wider block">Status Pengajuan Kemitraan</span>
                            <h2 class="text-sm font-black text-slate-900 leading-tight mt-0.5">
                                Pendaftaran Sedang Ditinjau Admin
                            </h2>
                        </div>
                    </div>

                    {{-- Keterangan Wajib --}}
                    <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-950 text-xs space-y-1.5">
                        <strong class="block font-bold flex items-center gap-1.5 text-amber-900">
                            <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Estimasi Waktu Peninjauan:
                        </strong>
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                            Proses verifikasi membutuhkan waktu maksimal 1x24 jam kerja.
                        </p>
                    </div>

                    {{-- Ringkasan Berkas Yang Dikirim --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-2 text-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rincian Data Pendaftaran</span>
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60">
                                <span class="text-slate-500">Nama Calon Mitra</span>
                                <strong class="text-slate-900">{{ $mitraName }}</strong>
                            </div>
                            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60">
                                <span class="text-slate-500">ID Registrasi</span>
                                <span class="font-mono font-bold text-[#173B67]">{{ $mitraId }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60">
                                <span class="text-slate-500">Kota Penugasan</span>
                                <span class="font-semibold text-slate-800">{{ $mitraKota }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Spesialisasi</span>
                                <span class="font-semibold text-slate-800 truncate max-w-[200px]">{{ $mitraKeahlian }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tahap Berikutnya --}}
                    <div class="p-3 rounded-xl bg-sky-50 border border-sky-200 text-slate-700 text-xs space-y-1">
                        <span class="font-bold text-[#173B67] flex items-center gap-1 text-[10.5px]">
                            <i class="fa-solid fa-shield-halved text-sky-600"></i> Apa langkah selanjutnya?
                        </span>
                        <p class="text-[10px] text-slate-600 leading-relaxed">
                            Setelah Admin menyetujui akun Anda, status otomatis berubah menjadi <strong>Disetujui (Approved)</strong> dan Anda langsung dapat menerima pesanan di Dashboard Mitra.
                        </p>
                    </div>

                    {{-- Tepat 2 Tombol Aksi Wajib: "Hubungi Bantuan / CS" dan "Keluar" --}}
                    <div class="space-y-2 pt-1">
                        <a
                            href="{{ $csHelpUrl }}"
                            class="w-full py-3 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-98 text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-headset text-white text-sm"></i>
                            <span>Hubungi Bantuan / CS</span>
                        </a>

                        <form action="{{ route('mitra.logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>

                </div>
            @endif

            {{-- FOOTER INFO --}}
            <div class="text-center text-[10px] text-slate-400 py-2">
                SayaBantu • Keamanan dan Kualitas Mitra Terpercaya
            </div>

        </div>
    </div>

    <div></div>

</div>

<script>
(function() {
    const isRejected = @json($isRejected);
    if (isRejected) {
        return;
    }

    const mitraId = @json($mitraId);
    const mitraPhone = @json($mitraPhone);
    const mitraName = @json($mitraName);
    const identifier = mitraId || mitraPhone;

    let isRedirecting = false;
    let pollTimer = null;

    function showApprovalNotice() {
        if (document.getElementById('accToastBanner')) return;
        const banner = document.createElement('div');
        banner.id = 'accToastBanner';
        banner.className = 'fixed top-4 left-1/2 -translate-x-1/2 w-[90%] max-w-[390px] bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-4 rounded-2xl shadow-2xl z-50 flex items-center gap-3 border border-emerald-300 transition-all duration-300';
        banner.innerHTML = `
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check text-white"></i>
            </div>
            <div class="text-left flex-1">
                <strong class="text-xs font-black block text-white">Pendaftaran Disetujui Admin! 🎉</strong>
                <span class="text-[10.5px] text-emerald-100 block mt-0.5 leading-tight">Admin telah menyetujui akun Anda. Langsung dialihkan ke Dashboard Mitra...</span>
            </div>
        `;
        document.body.appendChild(banner);
    }

    async function checkApprovalStatus() {
        if (isRedirecting) return;
        try {
            const queryParam = identifier ? ('?identifier=' + encodeURIComponent(identifier)) : '';
            const res = await fetch('{{ route("mitra.status") }}' + queryParam, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) return;
            const data = await res.json();

            // Skenario 1: Disetujui (Approved)
            if (data && (data.status === 'approved' || data.status === 'aktif' || data.isLoggedIn === true)) {
                isRedirecting = true;
                if (pollTimer) clearInterval(pollTimer);

                // Sinkronisasi status autentikasi di browser client
                try {
                    localStorage.removeItem('sb_mitra_logged_out');
                    localStorage.setItem('sb_mitra_auth', JSON.stringify({
                        isLoggedIn: true,
                        name: data.name || mitraName,
                        phone: data.phone || mitraPhone,
                        id: data.mitra_id || mitraId,
                        mitra_id: data.mitra_id || mitraId,
                        status: 'approved',
                        role: 'mitra'
                    }));
                    localStorage.setItem('sb_mitra_registered', 'true');
                } catch(e) {}

                showApprovalNotice();

                // Langsung diarahkan ke dashboard tanpa harus login lagi
                setTimeout(function() {
                    window.location.replace(data.redirect || "{{ route('mitra.dashboard') }}");
                }, 700);
                return;
            }

            // Skenario 2: Ditolak Admin
            if (data && (data.status === 'rejected' || data.status === 'ditolak')) {
                isRedirecting = true;
                if (pollTimer) clearInterval(pollTimer);
                window.location.reload();
            }
        } catch(err) {
            // Abaikan kesalahan koneksi sementara dan coba lagi di interval berikutnya
        }
    }

    // Polling setiap 2 detik ke server
    pollTimer = setInterval(checkApprovalStatus, 2000);
    // Cek segera 500ms setelah halaman dimuat
    setTimeout(checkApprovalStatus, 500);
})();
</script>

</body>
</html>

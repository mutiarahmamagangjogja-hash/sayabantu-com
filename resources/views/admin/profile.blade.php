@extends('layouts.admin')

@section('title', 'Profil Admin - SayaBantu.com')
@section('page-title', 'Profil Admin')
@section('page-subtitle', 'Pengaturan akun, keamanan sistem, dan log audit SayaBantu.com')

@section('hero')
<div class="px-5 pb-5 pt-1">
    {{-- HERO CARD PROFIL KHUSUS ADMIN (PERSIS SCREENSHOT) --}}
    <div class="p-5 rounded-[22px] bg-white/[0.10] backdrop-blur-md border border-white/20 shadow-xl text-center">
        
        {{-- Avatar Lingkaran Besar --}}
        <div class="relative inline-block mx-auto mb-3">
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-[#173B67] to-[#1E4E8C] text-white font-black text-3xl flex items-center justify-center shadow-2xl border-2 border-sky-400/40 mx-auto">
                A
            </div>
            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-[#0E1D31] flex items-center justify-center text-[8px] text-white" title="Online">
                <i class="fa-solid fa-check text-[7px]"></i>
            </span>
        </div>

        <div class="flex items-center justify-center gap-1.5">
            <h2 class="text-lg font-bold text-white">Admin SayaBantu</h2>
            <i class="fa-solid fa-circle-check text-sky-400 text-sm" title="Terverifikasi"></i>
        </div>

        <p class="text-xs text-gray-300 mt-0.5">admin@sayabantu.com</p>

        <div class="mt-2.5 flex items-center justify-center gap-2 flex-wrap">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Status Online
            </span>
            <span class="text-[10px] text-gray-400">ID: #ADM-0042</span>
        </div>

        {{-- Ringkasan Operasional Admin --}}
        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-white/10 text-center">
            <div class="p-2 rounded-xl bg-white/[0.06]">
                <div class="text-sm font-black text-sky-400">1.280+</div>
                <div class="text-[9px] text-gray-200 mt-0.5">Tiket Selesai</div>
            </div>
            <div class="p-2 rounded-xl bg-white/[0.06]">
                <div class="text-sm font-black text-sky-400">99.8%</div>
                <div class="text-[9px] text-gray-200 mt-0.5">Respon Cepat</div>
            </div>
            <div class="p-2 rounded-xl bg-white/[0.06]">
                <div class="text-sm font-black text-emerald-400">Aktif</div>
                <div class="text-[9px] text-gray-200 mt-0.5">Hak Akses Penuh</div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">

    {{-- =========================================================================
         1. INFORMASI AKUN ADMIN
    ========================================================================== --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-50 text-[#173B67] font-bold flex items-center justify-center text-xs">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xs font-bold text-gray-900">Informasi Akun Administrator</h3>
            </div>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-sky-50 text-sky-700">Akses Penuh</span>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500 text-[11px]">Tingkat Akses</span>
                <span class="font-bold text-gray-900">Administrator (Level 1)</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500 text-[11px]">Alamat Email</span>
                <span class="font-semibold text-gray-800">admin@sayabantu.com</span>
            </div>
            <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500 text-[11px]">ID Administrator</span>
                <span class="font-mono font-bold text-sky-700">#ADM-0042</span>
            </div>
            <div class="flex items-center justify-between py-1.5">
                <span class="text-gray-500 text-[11px]">Sesi Aktif Saat Ini</span>
                <span class="text-emerald-600 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[6px]"></i> 2 jam 18 menit
                </span>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         2. KEAMANAN & SANDI
    ========================================================================== --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-xs">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3 class="text-xs font-bold text-gray-900">Keamanan & Kredensial</h3>
            </div>
        </div>

        {{-- Card Ganti Password --}}
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white text-[#173B67] shadow-sm flex items-center justify-center text-sm border border-gray-100">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Kata Sandi Akun</h4>
                    <p class="text-[10px] text-gray-500">Terakhir diubah 18 hari lalu</p>
                </div>
            </div>
            <button type="button" onclick="openPasswordModal()" class="px-3 py-1.5 rounded-lg bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold text-xs transition-colors flex items-center gap-1.5 shadow-sm">
                <span>Ubah</span>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
            </button>
        </div>

        {{-- Card 2FA Switch --}}
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white text-emerald-600 shadow-sm flex items-center justify-center text-sm border border-gray-100">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Autentikasi 2FA</h4>
                    <p class="text-[10px] text-gray-500">Verifikasi kode masuk via SMS</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="twoFactorToggle" checked class="sr-only peer" onchange="toggleTwoFactor(this)">
                <div class="w-10 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173B67]"></div>
            </label>
        </div>

        {{-- Card Biometrik / PIN --}}
        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white text-indigo-600 shadow-sm flex items-center justify-center text-sm border border-gray-100">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Login Cepat Biometrik</h4>
                    <p class="text-[10px] text-gray-500">Gunakan Touch ID atau Face Unlock</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="biometricToggle" checked class="sr-only peer" onchange="toggleBiometric(this)">
                <div class="w-10 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173B67]"></div>
            </label>
        </div>

        {{-- Sesi Perangkat --}}
        <div class="pt-2">
            <div class="flex items-center justify-between pb-2 mb-2">
                <h4 class="text-[11px] font-bold text-gray-700">Perangkat Login Terdaftar</h4>
                <button type="button" onclick="terminateOtherSessions()" class="text-[10px] font-bold text-rose-600 hover:underline">
                    Keluarkan Sesi Lain
                </button>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-laptop text-gray-600"></i>
                        <div>
                            <div class="font-bold text-gray-800 text-[11px]">Chrome on Windows 11</div>
                            <div class="text-[9px] text-gray-400">Jakarta, ID • IP 182.253.11.9</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">
                        Aktif Sekarang
                    </span>
                </div>

                <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-mobile-screen-button text-gray-600"></i>
                        <div>
                            <div class="font-bold text-gray-800 text-[11px]">Safari on iPhone 15 Pro</div>
                            <div class="text-[9px] text-gray-400">Jakarta, ID • 2 hari lalu</div>
                        </div>
                    </div>
                    <button type="button" onclick="removeSingleSession(this)" class="text-[10px] text-gray-400 hover:text-rose-500 p-1">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================================
         3. PREFERENSI NOTIFIKASI SISTEM
    ========================================================================== --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-50 text-[#173B67] font-bold flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <h3 class="text-xs font-bold text-gray-900">Peringatan & Notifikasi Sistem</h3>
            </div>
        </div>

        <div class="space-y-3">
            {{-- Pesanan Baru --}}
            <div class="flex items-center justify-between py-1">
                <div>
                    <div class="text-xs font-bold text-gray-800">Pesanan Baru Masuk</div>
                    <div class="text-[10px] text-gray-400">Pemberitahuan saat customer membuat order</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="toggleNotifPref('Pesanan Baru', this)">
                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#173B67]"></div>
                </label>
            </div>

            {{-- Pembayaran Berhasil --}}
            <div class="flex items-center justify-between py-1">
                <div>
                    <div class="text-xs font-bold text-gray-800">Konfirmasi Pembayaran</div>
                    <div class="text-[10px] text-gray-400">Verifikasi otomatis transaksi lunas</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="toggleNotifPref('Pembayaran', this)">
                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#173B67]"></div>
                </label>
            </div>

            {{-- Konflik & Tiket Komplain --}}
            <div class="flex items-center justify-between py-1">
                <div>
                    <div class="text-xs font-bold text-gray-800">Peringatan Konflik Mendesak</div>
                    <div class="text-[10px] text-gray-400">Tiket masalah butuh tindakan mediasi</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer" onchange="toggleNotifPref('Konflik Mendesak', this)">
                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#173B67]"></div>
                </label>
            </div>

            {{-- Rekap Laporan Harian --}}
            <div class="flex items-center justify-between py-1">
                <div>
                    <div class="text-xs font-bold text-gray-800">Rekap Harian Sistem</div>
                    <div class="text-[10px] text-gray-400">Kirim ringkasan otomatis setiap jam 23:00</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" onchange="toggleNotifPref('Rekap Harian', this)">
                    <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#173B67]"></div>
                </label>
            </div>
        </div>

        <div class="pt-2">
            <button type="button" onclick="showToast('Pengaturan Tersimpan', 'Preferensi notifikasi sistem admin telah diperbarui.', 'success')" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-check"></i>
                Simpan Preferensi Notifikasi
            </button>
        </div>
    </div>

    {{-- =========================================================================
         4. LOG AKTIVITAS ADMIN TERKINI
    ========================================================================== --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-50 text-[#173B67] font-bold flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-xs font-bold text-gray-900">Riwayat Aksi Admin</h3>
            </div>
            <span class="text-[9px] text-gray-400">Terbaru</span>
        </div>

        <div class="space-y-4 relative before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200">
            {{-- Log 1 --}}
            <div class="relative flex items-start gap-3 pl-7">
                <div class="absolute left-1.5 top-0.5 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[7px] text-white">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-800">Verifikasi Pembayaran #PAY-8821</div>
                    <p class="text-[10px] text-gray-500 mt-0.5">Otomatisasi sistem verifikasi dana masuk Rp 450.000 via BCA VA.</p>
                    <span class="text-[9px] text-sky-600 font-semibold">15 menit lalu</span>
                </div>
            </div>

            {{-- Log 2 --}}
            <div class="relative flex items-start gap-3 pl-7">
                <div class="absolute left-1.5 top-0.5 w-4 h-4 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-[7px] text-white">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-800">Penugasan Mitra Pesanan #SB-2026-089</div>
                    <p class="text-[10px] text-gray-500 mt-0.5">Menghubungkan mitra terdekat untuk layanan Full Home Cleaning.</p>
                    <span class="text-[9px] text-gray-400">2 jam lalu</span>
                </div>
            </div>

            {{-- Log 3 --}}
            <div class="relative flex items-start gap-3 pl-7">
                <div class="absolute left-1.5 top-0.5 w-4 h-4 rounded-full bg-[#173B67] border-2 border-white flex items-center justify-center text-[7px] text-white">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-800">Mediasi Selesai Tiket #KF-102</div>
                    <p class="text-[10px] text-gray-500 mt-0.5">Solusi kompensasi disepakati oleh customer dan mitra.</p>
                    <span class="text-[9px] text-gray-400">5 jam lalu</span>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         5. KARTU KELUAR / LOGOUT
    ========================================================================== --}}
    <div class="p-4 rounded-[22px] bg-rose-50/80 border border-rose-200/80 flex items-center justify-between">
        <div>
            <h4 class="text-xs font-bold text-rose-800">Keluar dari Sesi Admin</h4>
            <p class="text-[10px] text-rose-600 mt-0.5">Akhiri akses administrator di perangkat ini</p>
        </div>
        <button type="button" onclick="openLogoutModal()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-power-off text-xs"></i>
            Keluar
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Two-factor toggle
    function toggleTwoFactor(checkbox) {
        if (checkbox.checked) {
            showToast('2FA Diaktifkan', 'Autentikasi dua faktor aktif untuk akun admin.', 'success');
        } else {
            showToast('2FA Dinonaktifkan', 'Perhatian: Keamanan akun kini tanpa 2FA.', 'warning');
        }
    }

    // Biometric toggle
    function toggleBiometric(checkbox) {
        if (checkbox.checked) {
            showToast('Biometrik Aktif', 'Login biometrik perangkat diaktifkan.', 'success');
        } else {
            showToast('Biometrik Mati', 'Sensor biometrik dinonaktifkan.', 'info');
        }
    }

    // Notification preference toggle
    function toggleNotifPref(name, checkbox) {
        const status = checkbox.checked ? 'diaktifkan' : 'dinonaktifkan';
        showToast('Preferensi Diperbarui', `Notifikasi "${name}" telah ${status}.`, 'info');
    }

    // Terminate sessions
    function terminateOtherSessions() {
        showToast('Sesi Berhasil Ditutup', 'Semua sesi aktif selain perangkat ini telah dikeluarkan.', 'success');
    }

    function removeSingleSession(btn) {
        const item = btn.closest('.flex');
        if (item) {
            item.style.transition = 'all 0.2s ease';
            item.style.opacity = '0';
            item.style.transform = 'scale(0.95)';
            setTimeout(() => {
                item.remove();
                showToast('Sesi Dikeluarkan', 'Sesi pada perangkat berhasil diakhiri.', 'info');
            }, 200);
        }
    }
</script>
@endpush

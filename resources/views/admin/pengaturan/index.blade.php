@extends('layouts.admin')

@section('title', 'Pengaturan Platform - SayaBantu.com')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Konfigurasi terpusat identitas, komisi transaksi, hak akses peran, dan kebijakan')

@section('hero')
<div class="px-5 pt-1 pb-4">
    {{-- STAT RINGKASAN KONFIGURASI --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-sky-400"></i>
                Pusat Kendali Konfigurasi
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                Sistem Stabil
            </span>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center">
            <button type="button" onclick="showSettingDetail('komisi')" title="Detail pengaturan komisi"
                class="p-2 rounded-xl bg-white/[0.04] border border-white/5 cursor-pointer hover:bg-white/[0.12] hover:border-sky-400/40 active:scale-95 transition-all">
                <div class="text-xs font-black text-sky-400">10%</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Komisi Platform</div>
            </button>
            <button type="button" onclick="showSettingDetail('peran')" title="Detail hak akses peran"
                class="p-2 rounded-xl bg-white/[0.04] border border-white/5 cursor-pointer hover:bg-white/[0.12] hover:border-sky-400/40 active:scale-95 transition-all">
                <div class="text-xs font-black text-white">3 Peran</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Hak Akses</div>
            </button>
            <button type="button" onclick="showSettingDetail('keamanan')" title="Detail keamanan akun"
                class="p-2 rounded-xl bg-white/[0.04] border border-white/5 cursor-pointer hover:bg-white/[0.12] hover:border-sky-400/40 active:scale-95 transition-all">
                <div class="text-xs font-black text-emerald-400">2FA Aktif</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Keamanan</div>
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4">

    {{-- =========================================================
         1. PENGATURAN UMUM PLATFORM
    ========================================================= --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#173B67] flex items-center justify-center text-sm border border-blue-100">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Identitas & Kontak Platform</h3>
                    <p class="text-[10px] text-gray-400">Informasi resmi yang ditampilkan ke pengguna</p>
                </div>
            </div>
        </div>

        <div class="space-y-3 text-xs">
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nama Platform</label>
                <input type="text" value="SayaBantu.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 font-semibold focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Kontak Resmi</label>
                <input type="email" value="cs@sayabantu.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nomor Telepon CS Bantuan</label>
                <input type="text" value="+62 812-9988-7766" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 font-mono focus:outline-none focus:border-sky-500">
            </div>
        </div>
    </div>

    {{-- =========================================================
         2. PENGATURAN TRANSAKSI & KEUANGAN
    ========================================================= --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Ketentuan Transaksi & Komisi</h3>
                    <p class="text-[10px] text-gray-400">Bagi hasil platform dan aturan pencairan dana</p>
                </div>
            </div>
            <span class="text-[9px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full">Level 1 Otorisasi</span>
        </div>

        <div class="space-y-3 text-xs">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Komisi Platform (%)</label>
                    <div class="relative">
                        <input type="number" min="0" max="30" value="10" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 font-bold focus:outline-none focus:border-sky-500 pr-8">
                        <span class="absolute right-3 top-2 text-gray-400 font-bold text-xs">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Min. Penarikan Mitra</label>
                    <input type="text" value="Rp 50.000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 font-bold focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="pt-1">
                <label class="block text-[11px] font-semibold text-gray-700 mb-1.5">Metode Pembayaran Aktif</label>
                <div class="space-y-2">
                    <div onclick="showSettingDetail('qris')" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100 cursor-pointer hover:border-emerald-300 hover:bg-emerald-50/40 active:scale-[0.99] transition-all" title="Detail metode pembayaran QRIS">
                        <span class="font-semibold text-gray-800 text-[11px]">QRIS Dinamis (Semua Bank & E-Wallet)</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                    </div>
                    <div onclick="showSettingDetail('va')" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100 cursor-pointer hover:border-emerald-300 hover:bg-emerald-50/40 active:scale-[0.99] transition-all" title="Detail metode pembayaran Virtual Account">
                        <span class="font-semibold text-gray-800 text-[11px]">Virtual Account (BCA, Mandiri, BRI)</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         3. PENGATURAN HAK AKSES & PERAN
    ========================================================= --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm border border-purple-100">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Hak Akses & Peran Administratif</h3>
                    <p class="text-[10px] text-gray-400">Pembatasan wewenang tervalidasi di backend</p>
                </div>
            </div>
        </div>

        <div class="space-y-2 text-xs">
            <div onclick="showSettingDetail('peran-admin')" class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between cursor-pointer hover:border-purple-300 hover:bg-purple-50/40 active:scale-[0.99] transition-all" title="Detail peran administrator">
                <div>
                    <strong class="text-gray-900 text-[11px] block">Administrator Utama</strong>
                    <span class="text-[9.5px] text-gray-500">Akses seluruh modul operasional, keuangan, dan sistem</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-50 text-purple-700">1 Akun</span>
            </div>

            <div onclick="showSettingDetail('peran-operator')" class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between cursor-pointer hover:border-blue-300 hover:bg-blue-50/40 active:scale-[0.99] transition-all" title="Detail peran operator mediasi">
                <div>
                    <strong class="text-gray-900 text-[11px] block">Operator Mediasi & Pengaduan</strong>
                    <span class="text-[9.5px] text-gray-500">Akses modul pengaduan, investigasi chat, dan moderasi</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700">2 Akun</span>
            </div>

            <div onclick="showSettingDetail('peran-cs')" class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between cursor-pointer hover:border-sky-300 hover:bg-sky-50/40 active:scale-[0.99] transition-all" title="Detail peran customer support">
                <div>
                    <strong class="text-gray-900 text-[11px] block">Customer Support</strong>
                    <span class="text-[9.5px] text-gray-500">Akses verifikasi data pengguna dan pemantauan suruhan</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-700">3 Akun</span>
            </div>
        </div>
    </div>

    {{-- =========================================================
         4. DOKUMEN KEBIJAKAN PLATFORM
    ========================================================= --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#173B67] flex items-center justify-center text-sm border border-sky-100">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Versi Dokumen Kebijakan</h3>
                    <p class="text-[10px] text-gray-400">Versi aktif dan tanggal pembaharuan regulasi</p>
                </div>
            </div>
            <a href="{{ route('admin.moderasi') }}" class="text-[10px] font-bold text-[#173B67] hover:underline">Kelola &rarr;</a>
        </div>

        <div class="space-y-2 text-xs">
            <div onclick="showSettingDetail('kode-etik')" class="flex items-center justify-between py-1 border-b border-gray-50 text-[11px] cursor-pointer hover:text-[#173B67] active:scale-[0.995] transition-all" title="Detail Kode Etik Mitra">
                <span class="text-gray-600">Kode Etik Mitra</span>
                <span class="font-bold text-gray-900">v2.1 (Berlaku 01 Jan 2026)</span>
            </div>
            <div onclick="showSettingDetail('snk')" class="flex items-center justify-between py-1 border-b border-gray-50 text-[11px] cursor-pointer hover:text-[#173B67] active:scale-[0.995] transition-all" title="Detail Syarat & Ketentuan">
                <span class="text-gray-600">Syarat & Ketentuan</span>
                <span class="font-bold text-gray-900">v3.0 (Berlaku 15 Feb 2026)</span>
            </div>
            <div onclick="showSettingDetail('privasi')" class="flex items-center justify-between py-1 text-[11px] cursor-pointer hover:text-[#173B67] active:scale-[0.995] transition-all" title="Detail Kebijakan Privasi">
                <span class="text-gray-600">Kebijakan Privasi</span>
                <span class="font-bold text-gray-900">v2.0 (Berlaku 01 Jan 2026)</span>
            </div>
        </div>
    </div>

    {{-- =========================================================
         5. NOTIFIKASI & KEAMANAN
    ========================================================= --}}
    <div class="p-5 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm border border-rose-100">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Keamanan Sesi & Notifikasi</h3>
                    <p class="text-[10px] text-gray-400">Proteksi akun dan batasan waktu aktif</p>
                </div>
            </div>
        </div>

        <div class="space-y-3 text-xs">
            <div class="flex items-center justify-between py-1">
                <div onclick="showSettingDetail('timeout')" class="cursor-pointer hover:text-[#173B67] transition-colors" title="Detail batas waktu sesi">
                    <div class="font-bold text-gray-800 text-[11px]">Batas Waktu Sesi (Timeout)</div>
                    <div class="text-[9.5px] text-gray-400">Keluarkan sesi otomatis setelah tidak aktif</div>
                </div>
                <select class="bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-xs font-semibold text-gray-800 focus:outline-none">
                    <option value="120">120 Menit (Default)</option>
                    <option value="60">60 Menit</option>
                    <option value="30">30 Menit</option>
                </select>
            </div>

            <div class="flex items-center justify-between py-1">
                <div onclick="showSettingDetail('2fa')" class="cursor-pointer hover:text-[#173B67] transition-colors" title="Detail autentikasi dua langkah">
                    <div class="font-bold text-gray-800 text-[11px]">Autentikasi Dua Langkah (2FA)</div>
                    <div class="text-[9.5px] text-gray-400">Wajib verifikasi OTP saat masuk perangkat baru</div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
            </div>
        </div>

        <div class="pt-3">
            <button type="button" onclick="saveSettings()" class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-check"></i>
                Simpan Seluruh Pengaturan
            </button>
        </div>
    </div>

</div>
@endsection

@section('modals')
{{-- MODAL DETAIL PENGATURAN --}}
<div id="settingDetailModal" class="sb-overlay-backdrop fixed inset-0 z-[220] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closeSettingDetail()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div class="relative w-full bg-white pointer-events-auto rounded-t-[26px] shadow-2xl flex flex-col max-h-[88vh] overflow-hidden">
            <div class="pt-3 pb-1 shrink-0">
                <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto"></div>
            </div>

            <div class="px-5 pb-3 border-b border-gray-100 flex items-start justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div id="settingDetailIcon" class="w-9 h-9 rounded-xl bg-sky-50 text-[#173B67] flex items-center justify-center text-sm border border-sky-100 shrink-0">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 id="settingDetailTitle" class="text-sm font-bold text-gray-900 truncate">Detail Pengaturan</h3>
                        <p id="settingDetailSubtitle" class="text-[10px] text-gray-400 truncate">Konfigurasi platform</p>
                    </div>
                </div>
                <button type="button" onclick="closeSettingDetail()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center shrink-0 cursor-pointer" aria-label="Tutup">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div id="settingDetailBody" class="flex-1 overflow-y-auto px-5 py-4 space-y-2.5 text-xs"></div>

            <div class="p-4 border-t border-gray-100 shrink-0 flex items-center gap-2">
                <button type="button" onclick="closeSettingDetail(); saveSettings();" class="flex-1 py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold text-xs transition-all cursor-pointer">
                    <i class="fa-solid fa-check mr-1"></i> Simpan Pengaturan
                </button>
                <button type="button" onclick="closeSettingDetail()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold text-xs transition-all cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // {{-- Detail pengaturan & tab konfigurasi --}}
    function saveSettings() {
        showToast('Pengaturan Disimpan', 'Konfigurasi platform SayaBantu.com telah diperbarui dan tercatat pada audit log.', 'success');
    }

    var settingDetails = {
        'komisi': {
            title: 'Komisi Platform',
            subtitle: 'Bagi hasil per transaksi selesai',
            icon: 'fa-percent',
            rows: [['Nilai komisi', '10% dari nilai transaksi'], ['Berlaku untuk', 'Semua layanan Harga Pas & Nego'], ['Dipotong saat', 'Pesanan ditandai selesai'], ['Penerima', 'Kas platform SayaBantu'], ['Terakhir diubah', '01 Jan 2026 oleh Administrator Utama']]
        },
        'peran': {
            title: 'Hak Akses & Peran',
            subtitle: '3 peran administratif aktif',
            icon: 'fa-user-lock',
            rows: [['Administrator Utama', '1 akun — seluruh modul'], ['Operator Mediasi', '2 akun — pengaduan & moderasi'], ['Customer Support', '3 akun — verifikasi & pemantauan'], ['Validasi', 'Diterapkan di backend (middleware)']]
        },
        'keamanan': {
            title: 'Keamanan Akun',
            subtitle: 'Proteksi sesi & autentikasi',
            icon: 'fa-shield-halved',
            rows: [['2FA', 'Aktif — OTP saat perangkat baru'], ['Timeout sesi', '120 menit tanpa aktivitas'], ['Percobaan login', 'Maksimal 5 kali lalu terkunci'], ['Log audit', 'Semua tindakan admin tercatat']]
        },
        'qris': {
            title: 'QRIS Dinamis',
            subtitle: 'Metode pembayaran aktif',
            icon: 'fa-qrcode',
            rows: [['Status', 'Aktif'], ['Cakupan', 'Semua bank & e-wallet'], ['Verifikasi', 'Otomatis 1–3 menit'], ['Biaya layanan', 'Rp 2.000 per transaksi']]
        },
        'va': {
            title: 'Virtual Account',
            subtitle: 'BCA, Mandiri, BRI',
            icon: 'fa-building-columns',
            rows: [['Status', 'Aktif'], ['Bank', 'BCA, Mandiri, BRI'], ['Verifikasi', 'Otomatis via callback bank'], ['Batas waktu bayar', '24 jam setelah VA dibuat']]
        },
        'peran-admin': {
            title: 'Administrator Utama',
            subtitle: '1 akun terdaftar',
            icon: 'fa-user-shield',
            rows: [['Modul', 'Operasional, keuangan, sistem'], ['Bisa menghapus data', 'Ya'], ['Bisa mengubah komisi', 'Ya'], ['Wajib 2FA', 'Ya']]
        },
        'peran-operator': {
            title: 'Operator Mediasi & Pengaduan',
            subtitle: '2 akun terdaftar',
            icon: 'fa-scale-balanced',
            rows: [['Modul', 'Pengaduan, investigasi chat, moderasi'], ['Bisa menghapus data', 'Tidak'], ['Bisa mengubah komisi', 'Tidak'], ['Wajib 2FA', 'Ya']]
        },
        'peran-cs': {
            title: 'Customer Support',
            subtitle: '3 akun terdaftar',
            icon: 'fa-headset',
            rows: [['Modul', 'Verifikasi pengguna, pemantauan suruhan'], ['Bisa menghapus data', 'Tidak'], ['Bisa menyetujui mitra', 'Ya (dengan catatan)'], ['Wajib 2FA', 'Ya']]
        },
        'kode-etik': {
            title: 'Kode Etik Mitra',
            subtitle: 'v2.1 — berlaku 01 Jan 2026',
            icon: 'fa-handshake-angle',
            rows: [['Versi aktif', 'v2.1'], ['Berlaku sejak', '01 Jan 2026'], ['Dikelola di', 'Admin › Moderasi & Kepatuhan'], ['Pelanggaran', 'Teguran hingga penonaktifan akun']]
        },
        'snk': {
            title: 'Syarat & Ketentuan',
            subtitle: 'v3.0 — berlaku 15 Feb 2026',
            icon: 'fa-file-contract',
            rows: [['Versi aktif', 'v3.0'], ['Berlaku sejak', '15 Feb 2026'], ['Halaman publik', 'Lihat halaman Syarat & Ketentuan'], ['Perubahan', 'Persetujuan ulang pengguna']]
        },
        'privasi': {
            title: 'Kebijakan Privasi',
            subtitle: 'v2.0 — berlaku 01 Jan 2026',
            icon: 'fa-file-shield',
            rows: [['Versi aktif', 'v2.0'], ['Berlaku sejak', '01 Jan 2026'], ['Data yang disimpan', 'Identitas, kontak, riwayat pesanan'], ['Retensi', '24 bulan setelah akun tidak aktif']]
        },
        'timeout': {
            title: 'Batas Waktu Sesi',
            subtitle: 'Auto logout saat tidak aktif',
            icon: 'fa-clock',
            rows: [['Default', '120 menit'], ['Opsi', '60 menit / 30 menit'], ['Berlaku untuk', 'Semua peran admin'], ['Aktivitas dihitung', 'Klik, ketik, dan pindah halaman']]
        },
        '2fa': {
            title: 'Autentikasi Dua Langkah (2FA)',
            subtitle: 'OTP saat masuk perangkat baru',
            icon: 'fa-mobile-screen-button',
            rows: [['Status', 'Aktif'], ['Metode', 'OTP melalui SMS & email'], ['Berlaku untuk', 'Semua akun admin'], ['Bisa dilewati', 'Tidak']]
        }
    };

    function showSettingDetail(key) {
        var item = settingDetails[key] || settingDetails['komisi'];
        var modal = document.getElementById('settingDetailModal');
        var title = document.getElementById('settingDetailTitle');
        var sub = document.getElementById('settingDetailSubtitle');
        var body = document.getElementById('settingDetailBody');
        var icon = document.getElementById('settingDetailIcon');
        if (!modal || !body) return;

        if (title) title.textContent = item.title;
        if (sub) sub.textContent = item.subtitle;
        if (icon) icon.innerHTML = '<i class="fa-solid ' + item.icon + '"></i>';

        body.innerHTML = item.rows.map(function (row) {
            return '<div class="flex items-start justify-between gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100">' +
                '<span class="text-gray-500 shrink-0">' + row[0] + '</span>' +
                '<span class="font-semibold text-gray-900 text-right">' + row[1] + '</span>' +
                '</div>';
        }).join('');

        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modal.classList.add('is-open');
    }

    function closeSettingDetail() {
        var modal = document.getElementById('settingDetailModal');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.classList.add('invisible', 'opacity-0', 'pointer-events-none');
    }

    window.showSettingDetail = showSettingDetail;
    window.closeSettingDetail = closeSettingDetail;
</script>
@endpush

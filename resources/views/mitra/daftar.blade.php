@extends('layouts.mitra')

@section('title', 'Pendaftaran Mitra Baru - SayaBantu.com')
@section('hide-bottom-nav', 'true')

{{-- =========================================================
     TOP HERO AREA DENGAN GRADIENT IDENTIK SAYABANTU
========================================================= --}}
@section('page-top')
<div class="mitra-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER ATAS --}}
    <header class="relative z-20 px-5 pt-4">
        <div class="flex items-center justify-between w-full">
            {{-- Tombol Kembali --}}
            <a
                href="{{ route('mitra.login') }}"
                class="header-icon shrink-0"
                title="Kembali ke Halaman Masuk"
            >
                <i class="fa-solid fa-arrow-left text-[14px]"></i>
            </a>

            {{-- Identitas Brand --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[17px] font-bold tracking-tight text-white leading-none">
                    SayaBantu<span class="text-white">.com</span>
                </span>
                <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-emerald-500/25 text-emerald-200 border border-emerald-400/30">
                    Gabung Mitra
                </span>
            </div>

            {{-- Bantuan CS --}}
            <button
                type="button"
                onclick="showDaftarToast('Bantuan', 'Silakan lengkapi formulir pendaftaran 4 langkah ini.', 'info')"
                class="header-icon shrink-0"
                title="Pusat Bantuan Pendaftaran"
            >
                <i class="fa-solid fa-circle-question text-[15px] text-sky-200"></i>
            </button>
        </div>

        {{-- JUDUL DAN SUB-JUDUL --}}
        <div class="pt-4 pb-2 text-center">
            <h1 class="text-[19px] font-black tracking-tight text-white leading-tight">
                Pendaftaran Mitra Baru
            </h1>
            <p class="text-[11px] text-sky-100/90 mt-1 max-w-[320px] mx-auto leading-relaxed">
                Lengkapi identitas diri, spesialisasi keahlian, berkas legalitas, dan dokumen jaminan kemitraan.
            </p>
        </div>

        {{-- PROGRESS STEPPER 4 TAHAP --}}
        <div class="pt-2 pb-1">
            <div class="flex items-center justify-between relative px-2">
                {{-- Garis Latar Stepper --}}
                <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-0.5 bg-white/20 -z-0"></div>
                <div id="stepperProgressBar" class="absolute left-6 top-1/2 -translate-y-1/2 h-0.5 bg-emerald-400 transition-all duration-300 -z-0" style="width: 0%;"></div>

                {{-- Step 1 --}}
                <div class="step-indicator active flex flex-col items-center relative z-10" data-step="1">
                    <div class="w-7 h-7 rounded-full bg-emerald-500 text-white font-bold text-[10px] flex items-center justify-center shadow-md border-2 border-white/80 transition-all step-circle">
                        1
                    </div>
                    <span class="text-[9px] font-bold text-white mt-1 step-label">Data Diri</span>
                </div>

                {{-- Step 2 --}}
                <div class="step-indicator flex flex-col items-center relative z-10" data-step="2">
                    <div class="w-7 h-7 rounded-full bg-white/20 text-white/70 font-bold text-[10px] flex items-center justify-center border-2 border-transparent transition-all step-circle">
                        2
                    </div>
                    <span class="text-[9px] font-medium text-white/60 mt-1 step-label">Keahlian</span>
                </div>

                {{-- Step 3 --}}
                <div class="step-indicator flex flex-col items-center relative z-10" data-step="3">
                    <div class="w-7 h-7 rounded-full bg-white/20 text-white/70 font-bold text-[10px] flex items-center justify-center border-2 border-transparent transition-all step-circle">
                        3
                    </div>
                    <span class="text-[9px] font-medium text-white/60 mt-1 step-label">Berkas & Jaminan</span>
                </div>

                {{-- Step 4 --}}
                <div class="step-indicator flex flex-col items-center relative z-10" data-step="4">
                    <div class="w-7 h-7 rounded-full bg-white/20 text-white/70 font-bold text-[10px] flex items-center justify-center border-2 border-transparent transition-all step-circle">
                        4
                    </div>
                    <span class="text-[9px] font-medium text-white/60 mt-1 step-label">Konfirmasi</span>
                </div>
            </div>
        </div>
    </header>
</div>
@endsection

{{-- =========================================================
     CONTENT AREA (MULTI-STEP REGISTRATION FORM)
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-2 pb-12 space-y-4">

    {{-- FORMULIR UTAMA --}}
    <form id="formRegistrasiMitra" enctype="multipart/form-data" novalidate onsubmit="handleRegistrasiSubmit(event)">
        @csrf

        {{-- =========================================================================
             TAHAP 1: DATA DIRI & KONTAK PRIBADI
        ========================================================================== --}}
        <div id="stepSection1" class="step-section space-y-3.5">
            <div class="card-sb p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h2 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-address-card text-sky-600"></i>
                        Data Identitas Sesuai e-KTP
                    </h2>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase">Langkah 1 dari 4</span>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label for="regNama" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="regNama"
                        name="nama"
                        required
                        placeholder="Contoh: Kala Pratama"
                        class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                    >
                </div>

                {{-- NIK --}}
                <div>
                    <label for="regNik" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Nomor Induk Kependudukan (NIK 16 Digit) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="regNik"
                        name="nik"
                        required
                        maxlength="16"
                        pattern="[0-9]{16}"
                        placeholder="Contoh: 3171022408900001"
                        class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-sky-500 focus:bg-white"
                    >
                    <span class="text-[9px] text-slate-400 mt-0.5 block">Wajib 16 digit angka asli untuk validasi kependudukan.</span>
                </div>

                {{-- Kontak: Telepon & Email --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label for="regWa" class="block text-[10px] font-bold text-slate-700 mb-1">
                            No. Handphone Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="regWa"
                            name="whatsapp"
                            required
                            placeholder="0812-xxxx-xxxx"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                    <div>
                        <label for="regEmail" class="block text-[10px] font-bold text-slate-700 mb-1">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="regEmail"
                            name="email"
                            required
                            placeholder="nama@gmail.com"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                </div>

                {{-- PIN / Kata Sandi Masuk Akun Portal Mitra --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label for="regPin" class="block text-[10px] font-bold text-slate-700 mb-1">
                            PIN / Sandi Masuk Mitra <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="regPin"
                            name="pin"
                            required
                            minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                    <div>
                        <label for="regPinConfirm" class="block text-[10px] font-bold text-slate-700 mb-1">
                            Ulangi PIN / Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="regPinConfirm"
                            name="pin_confirm"
                            required
                            minlength="6"
                            placeholder="Ulangi PIN / sandi"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                </div>

                {{-- Gender & Tanggal Lahir --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label for="regGender" class="block text-[10px] font-bold text-slate-700 mb-1">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="regGender"
                            name="gender"
                            required
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-sky-500 focus:bg-white cursor-pointer"
                        >
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="regTglLahir" class="block text-[10px] font-bold text-slate-700 mb-1">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="regTglLahir"
                            name="tanggal_lahir"
                            required
                            value="1995-05-12"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                </div>

                {{-- Kota Domisili --}}
                <div>
                    <label for="regKota" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Kota Wilayah Operasional Tugas <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="regKota"
                        name="kota"
                        required
                        class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 font-semibold focus:outline-none focus:border-sky-500 focus:bg-white cursor-pointer"
                    >
                        <option value="Jakarta">Jakarta (Pusat, Selatan, Timur, Barat, Utara)</option>
                        <option value="Bogor">Bogor (Kota & Kabupaten)</option>
                        <option value="Depok">Depok (Margonda, Cimanggis, Sawangan)</option>
                        <option value="Tangerang">Tangerang & Tangerang Selatan (BSD, Bintaro)</option>
                        <option value="Bekasi">Bekasi (Kota & Cikarang)</option>
                        <option value="Bandung">Bandung (Kota & Cimahi)</option>
                        <option value="Solo">Solo (Surakarta & Sukoharjo)</option>
                        <option value="Yogyakarta">Yogyakarta (Sleman, Bantul, Kota)</option>
                    </select>
                </div>

                {{-- Alamat Domisili --}}
                <div>
                    <label for="regAlamat" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Alamat Tempat Tinggal Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="regAlamat"
                        name="alamat"
                        rows="2"
                        required
                        placeholder="Tuliskan nama jalan, RT/RW, kelurahan, dan patokan rumah..."
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                    ></textarea>
                </div>
            </div>

            {{-- Tombol Lanjut ke Tahap 2 --}}
            <button
                type="button"
                onclick="goToStep(2)"
                class="w-full py-3 rounded-2xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-98 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Lanjut: Keahlian & Rekening</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

        {{-- =========================================================================
             TAHAP 2: KATEGORI KEAHLIAN & REKENING PENCAIRAN SALDO
        ========================================================================== --}}
        <div id="stepSection2" class="step-section hidden space-y-3.5">
            <div class="card-sb p-4 space-y-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h2 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-sky-600"></i>
                        Spesialisasi Keahlian Layanan
                    </h2>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase">Langkah 2 dari 4</span>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-700 mb-1.5">
                        Pilih Kategori Layanan yang Anda Kuasai (Bisa lebih dari 1) <span class="text-rose-500">*</span>
                    </label>
                    
                    {{-- Grid Pilihan Kategori --}}
                    <div class="grid grid-cols-2 gap-2 text-[10.5px]" id="serviceSkillGrid">
                        {{-- 1. Bersihkan Rumah --}}
                        <label class="service-pill flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 cursor-pointer transition-all">
                            <input type="checkbox" name="skills[]" value="Full Home Cleaning" checked class="accent-[#173B67] w-4 h-4 rounded">
                            <div class="min-w-0">
                                <strong class="block text-slate-900 text-xs">Bersihkan Rumah</strong>
                                <span class="text-[9px] text-slate-500">Cleaning, cuci piring</span>
                            </div>
                        </label>

                        {{-- 2. Angkut Barang --}}
                        <label class="service-pill flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 cursor-pointer transition-all">
                            <input type="checkbox" name="skills[]" value="Angkut Barang & Pindahan" class="accent-[#173B67] w-4 h-4 rounded">
                            <div class="min-w-0">
                                <strong class="block text-slate-900 text-xs">Angkut Barang</strong>
                                <span class="text-[9px] text-slate-500">Pindahan kos & pick up</span>
                            </div>
                        </label>

                        {{-- 3. Belanja --}}
                        <label class="service-pill flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 cursor-pointer transition-all">
                            <input type="checkbox" name="skills[]" value="Belanja & Titip Antre" class="accent-[#173B67] w-4 h-4 rounded">
                            <div class="min-w-0">
                                <strong class="block text-slate-900 text-xs">Belanja & Titip Antre</strong>
                                <span class="text-[9px] text-slate-500">Pasar subuh, antre RS</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pengalaman Kerja --}}
                <div>
                    <label for="regPengalaman" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Pengalaman Kerja Singkat <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        id="regPengalaman"
                        name="pengalaman"
                        rows="2"
                        required
                        placeholder="Contoh: Sudah berpengalaman membersihkan rumah dan apartemen selama 3 tahun, membawa peralatan sendiri."
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                    ></textarea>
                </div>
            </div>

            {{-- REKENING BANK / E-WALLET PENCAIRAN --}}
            <div class="card-sb p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h2 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i>
                        Rekening Pencairan Dana Tugas
                    </h2>
                    <span class="text-[8.5px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        Bebas Biaya Admin
                    </span>
                </div>

                <div>
                    <label for="regBank" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Nama Bank / Dompet Digital <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="regBank"
                        name="bank"
                        required
                        class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 font-semibold focus:outline-none focus:border-sky-500 focus:bg-white cursor-pointer"
                    >
                        <option value="BCA">BCA (Bank Central Asia)</option>
                        <option value="Mandiri">Bank Mandiri</option>
                        <option value="BRI">Bank BRI</option>
                        <option value="BNI">Bank BNI</option>
                        <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                        <option value="GoPay">GoPay (Gojek)</option>
                        <option value="OVO">OVO Cash</option>
                        <option value="DANA">DANA Dompet Digital</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label for="regNoRekening" class="block text-[10px] font-bold text-slate-700 mb-1">
                            No. Rekening / No. HP <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="regNoRekening"
                            name="nomor_rekening"
                            required
                            placeholder="Contoh: 827199201123"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                    <div>
                        <label for="regAtasNama" class="block text-[10px] font-bold text-slate-700 mb-1">
                            Atas Nama Rekening <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="regAtasNama"
                            name="atas_nama"
                            required
                            placeholder="Sesuai buku tabungan"
                            class="w-full px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:bg-white"
                        >
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2 text-[10px] text-amber-900">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 shrink-0"></i>
                    <p class="leading-tight">
                        <strong>Penting:</strong> Nama pemilik rekening <strong>harus sama</strong> dengan nama di KTP pendaftar demi keamanan pencegahan pencucian uang.
                    </p>
                </div>
            </div>

            {{-- Navigasi Tombol --}}
            <div class="flex items-center gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToStep(1)"
                    class="w-28 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all text-center cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </button>
                <button
                    type="button"
                    onclick="goToStep(3)"
                    class="flex-1 py-3 rounded-2xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-98 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Lanjut: Berkas & Jaminan</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        {{-- =========================================================================
             TAHAP 3: UPLOAD BERKAS LEGALITAS & FOTO JAMINAN KEMITRAAN (FITUR UTAMA)
        ========================================================================== --}}
        <div id="stepSection3" class="step-section hidden space-y-3.5">
            
            {{-- KOTAK PENJELASAN INTEGRITAS & KEAMANAN JAMINAN --}}
            <div class="card-sb p-3.5 bg-gradient-to-br from-indigo-900 via-[#17314A] to-[#0E1D31] text-white border-0 shadow-md relative overflow-hidden">
                <div class="relative z-10 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/20 text-amber-300 text-base flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-white leading-tight flex items-center gap-1.5">
                            Standar Keamanan & Dokumen Jaminan
                        </h2>
                        <p class="text-[10px] text-slate-200 mt-1 leading-relaxed">
                            Pelanggan SayaBantu mempercayakan rumah, kantor, dan barang berharga mereka kepada Mitra. Oleh karena itu, <strong>setiap mitra wajib melampirkan berkas resmi dan 1 dokumen jaminan fisik</strong> demi menjamin rasa aman kedua belah pihak.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 1. UPLOAD FOTO E-KTP ASLI --}}
            <div class="card-sb p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-900">
                            1. Foto e-KTP Asli <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400">Pastikan tulisan NIK, nama, dan foto wajah terlihat jelas</span>
                    </div>
                    <span id="badgeKtpStatus" class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                        Belum Diunggah
                    </span>
                </div>

                {{-- Dropzone / Upload Box --}}
                <div
                    onclick="document.getElementById('fileInputKtp').click()"
                    class="upload-dropzone p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-sky-400 bg-slate-50 hover:bg-sky-50/40 text-center cursor-pointer transition-all group relative overflow-hidden"
                >
                    <input type="file" id="fileInputKtp" accept="image/*" class="hidden" onchange="previewUploadImage(this, 'previewKtp', 'badgeKtpStatus')">
                    
                    {{-- Preview Container --}}
                    <div id="previewKtp" class="hidden">
                        <img src="" alt="Preview KTP" class="max-h-36 mx-auto rounded-xl object-contain shadow-xs">
                        <p class="text-[9.5px] text-emerald-600 font-bold mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Foto e-KTP Berhasil Dipilih (Klik untuk mengganti)
                        </p>
                    </div>

                    {{-- Placeholder --}}
                    <div id="placeholderKtp" class="space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mx-auto text-base group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <strong class="text-xs font-bold text-slate-700 block">Klik untuk Ambil / Upload Foto KTP</strong>
                        <span class="text-[9px] text-slate-400 block">Format JPG, PNG, atau HEIC (Maksimal 5MB)</span>
                    </div>
                </div>
            </div>

            {{-- 2. UPLOAD SELFIE DENGAN E-KTP --}}
            <div class="card-sb p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-900">
                            2. Foto Selfie Memegang e-KTP <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400">Verifikasi wajah Anda memegang KTP di samping wajah</span>
                    </div>
                    <span id="badgeSelfieStatus" class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                        Belum Diunggah
                    </span>
                </div>

                <div
                    onclick="document.getElementById('fileInputSelfie').click()"
                    class="upload-dropzone p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-sky-400 bg-slate-50 hover:bg-sky-50/40 text-center cursor-pointer transition-all group relative overflow-hidden"
                >
                    <input type="file" id="fileInputSelfie" name="selfie" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewUploadImage(this, 'previewSelfie', 'badgeSelfieStatus')">
                    
                    <div id="previewSelfie" class="hidden">
                        <img src="" alt="Preview Selfie KTP" class="max-h-36 mx-auto rounded-xl object-contain shadow-xs">
                        <p class="text-[9.5px] text-emerald-600 font-bold mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Foto Selfie Berhasil Dipilih (Klik untuk mengganti)
                        </p>
                    </div>

                    <div id="placeholderSelfie" class="space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mx-auto text-base group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-camera-rotate"></i>
                        </div>
                        <strong class="text-xs font-bold text-slate-700 block">Klik untuk Selfie bersama e-KTP</strong>
                        <span class="text-[9px] text-slate-400 block">Pastikan pencahayaan cukup dan wajah tidak tertutup masker</span>
                    </div>
                </div>
            </div>

            {{-- 3. FOTO WAJAH / FOTO DIRI UNTUK PROFIL MITRA --}}
            <div class="card-sb p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="fileInputFotoWajah" class="block text-xs font-bold text-slate-900">
                            3. Foto Wajah / Foto Diri <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400">Foto wajah yang jelas untuk ditampilkan pada profil mitra</span>
                    </div>
                    <span id="badgeFotoWajahStatus" class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                        Belum Diunggah
                    </span>
                </div>

                <div
                    onclick="document.getElementById('fileInputFotoWajah').click()"
                    class="upload-dropzone p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-sky-400 bg-slate-50 hover:bg-sky-50/40 text-center cursor-pointer transition-all group relative overflow-hidden"
                >
                    <input type="file" id="fileInputFotoWajah" name="foto_wajah" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewUploadImage(this, 'previewFotoWajah', 'badgeFotoWajahStatus')">

                    <div id="previewFotoWajah" class="hidden">
                        <img src="" alt="Preview foto wajah mitra" class="max-h-36 mx-auto rounded-xl object-contain shadow-xs">
                        <p class="text-[9.5px] text-emerald-600 font-bold mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Foto Wajah Berhasil Dipilih (Klik untuk mengganti)
                        </p>
                    </div>

                    <div id="placeholderFotoWajah" class="space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mx-auto text-base group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <strong class="text-xs font-bold text-slate-700 block">Klik untuk Ambil / Upload Foto Wajah</strong>
                        <span class="text-[9px] text-slate-400 block">Format JPG, PNG, atau WebP (Maksimal 5MB)</span>
                    </div>
                </div>
            </div>

            {{-- 3. UPLOAD FOTO SKCK (SURAT KETERANGAN CATATAN KEPOLISIAN) --}}
            <div class="card-sb p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-900">
                            4. Foto SKCK Aktif / Surat Domisili <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-400">Bukti bebas catatan kriminal dari Kepolisian RI</span>
                    </div>
                    <span id="badgeSkckStatus" class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                        Belum Diunggah
                    </span>
                </div>

                <div
                    onclick="document.getElementById('fileInputSkck').click()"
                    class="upload-dropzone p-4 rounded-2xl border-2 border-dashed border-slate-200 hover:border-sky-400 bg-slate-50 hover:bg-sky-50/40 text-center cursor-pointer transition-all group relative overflow-hidden"
                >
                    <input type="file" id="fileInputSkck" accept="image/*" class="hidden" onchange="previewUploadImage(this, 'previewSkck', 'badgeSkckStatus')">
                    
                    <div id="previewSkck" class="hidden">
                        <img src="" alt="Preview SKCK" class="max-h-36 mx-auto rounded-xl object-contain shadow-xs">
                        <p class="text-[9.5px] text-emerald-600 font-bold mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Foto SKCK Berhasil Dipilih (Klik untuk mengganti)
                        </p>
                    </div>

                    <div id="placeholderSkck" class="space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-base group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <strong class="text-xs font-bold text-slate-700 block">Klik untuk Ambil / Upload Foto SKCK</strong>
                        <span class="text-[9px] text-slate-400 block">Dapat digantikan sementara dengan Surat Keterangan Domisili RT/RW</span>
                    </div>
                </div>
            </div>

            {{-- 4. FOTO DOKUMEN JAMINAN KEMITRAAN (AGUNAN INTEGRITAS) --}}
            <div class="card-sb p-4 border-2 border-amber-400/50 bg-amber-50/20 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-amber-200/80">
                    <div>
                        <label class="block text-xs font-black text-amber-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-amber-600"></i>
                            5. Dokumen Jaminan Kemitraan Fisik <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-500">Pilih 1 dokumen asli yang akan dijaminkan selama masa kemitraan</span>
                    </div>
                    <span id="badgeJaminanStatus" class="text-[9px] font-bold text-slate-400 bg-white px-2 py-0.5 rounded-full border border-amber-300">
                        Belum Diunggah
                    </span>
                </div>

                {{-- Pilihan Jenis Dokumen Jaminan --}}
                <div>
                    <label for="selectJenisJaminan" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Pilih Jenis Dokumen Jaminan yang Digunakan:
                    </label>
                    <select
                        id="selectJenisJaminan"
                        name="jenis_jaminan"
                        required
                        class="w-full px-3 py-2.5 rounded-xl bg-white border border-amber-300 text-xs text-slate-900 font-bold focus:outline-none focus:border-amber-500 cursor-pointer"
                    >
                        <option value="BPKB Sepeda Motor Asli">🛵 BPKB Sepeda Motor Asli (Paling Disarankan)</option>
                        <option value="Ijazah Asli Terakhir (SMA/SMK/D3/S1)">🎓 Ijazah Pendidikan Asli Terakhir (SMA/SMK/S1)</option>
                        <option value="Buku Nikah / Akta Kelahiran Asli">💍 Buku Nikah / Akta Kelahiran Asli</option>
                        <option value="Kartu Keluarga (KK) Asli">📑 Kartu Keluarga (KK) Asli</option>
                        <option value="Sertifikat Kepemilikan Rumah / Tanah">🏠 Sertifikat Hak Milik (SHM) / Petok Asli</option>
                    </select>
                </div>

                {{-- Nomor Seri / Identitas Dokumen Jaminan --}}
                <div>
                    <label for="inputNoJaminan" class="block text-[10px] font-bold text-slate-700 mb-1">
                        Nomor Seri / No. Registrasi Dokumen Jaminan <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="inputNoJaminan"
                        name="nomor_jaminan"
                        required
                        placeholder="Contoh: No. BPKB M-0918239 atau No. Ijazah DN-01/08/..."
                        class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder-slate-400 font-mono focus:outline-none focus:border-amber-500"
                    >
                </div>

                {{-- Area Upload Foto Fisik Jaminan --}}
                <div
                    onclick="document.getElementById('fileInputJaminan').click()"
                    class="upload-dropzone p-4 rounded-2xl border-2 border-dashed border-amber-300 hover:border-amber-500 bg-white hover:bg-amber-50/50 text-center cursor-pointer transition-all group relative overflow-hidden"
                >
                    <input type="file" id="fileInputJaminan" accept="image/*" class="hidden" onchange="previewUploadImage(this, 'previewJaminan', 'badgeJaminanStatus')">
                    
                    <div id="previewJaminan" class="hidden">
                        <img src="" alt="Preview Dokumen Jaminan" class="max-h-36 mx-auto rounded-xl object-contain shadow-xs">
                        <p class="text-[9.5px] text-emerald-600 font-bold mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> Foto Dokumen Jaminan Berhasil Dipilih (Klik untuk mengganti)
                        </p>
                    </div>

                    <div id="placeholderJaminan" class="space-y-1.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto text-base group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <strong class="text-xs font-bold text-slate-800 block">Klik untuk Ambil / Upload Foto Fisik Jaminan</strong>
                        <span class="text-[9px] text-slate-500 block">Foto halaman depan & data kepemilikan dokumen jaminan asli</span>
                    </div>
                </div>

                {{-- Jaminan Brankas Aman --}}
                <div class="p-2.5 rounded-xl bg-white border border-amber-200 flex items-start gap-2 text-[9.5px] text-slate-600">
                    <i class="fa-solid fa-lock text-emerald-600 mt-0.5 shrink-0"></i>
                    <p class="leading-tight">
                        <strong>Tanda Terima Resmi:</strong> Saat berkas diverifikasi dan jaminan fisik diserahkan ke kantor perwakilan, Anda akan menerima <strong>Surat Perjanjian Penitipan Bermaterai Resmi</strong> dan dokumen disimpan dalam brankas terproteksi.
                    </p>
                </div>
            </div>

            {{-- Navigasi Tombol --}}
            <div class="flex items-center gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToStep(2)"
                    class="w-28 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all text-center cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </button>
                <button
                    type="button"
                    onclick="goToStep(4)"
                    class="flex-1 py-3 rounded-2xl bg-[#173B67] hover:bg-[#0E1D31] active:scale-98 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Lanjut: Konfirmasi & Kirim</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        {{-- =========================================================================
             TAHAP 4: PAKTA INTEGRITAS, REVIEW DATA & KIRIM PENDAFTARAN
        ========================================================================== --}}
        <div id="stepSection4" class="step-section hidden space-y-3.5">
            
            {{-- RANGKUMAN PENDAFTARAN --}}
            <div class="card-sb p-4 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h2 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
                        Ringkasan Data Calon Mitra
                    </h2>
                    <span class="text-[9px] text-slate-400 font-semibold uppercase">Tahap Terakhir</span>
                </div>

                <div class="space-y-2 text-[11px]">
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500 text-[10px]">Nama Lengkap</span>
                        <strong class="text-slate-900 font-bold" id="reviewNama">-</strong>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500 text-[10px]">NIK KTP</span>
                        <span class="font-mono font-bold text-slate-800" id="reviewNik">-</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500 text-[10px]">No. Handphone</span>
                        <span class="font-mono font-bold text-slate-800" id="reviewWa">-</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500 text-[10px]">Wilayah Kota</span>
                        <strong class="text-sky-700 font-bold" id="reviewKota">-</strong>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500 text-[10px]">Rekening Pencairan</span>
                        <span class="font-semibold text-slate-800" id="reviewRekening">-</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500 text-[10px]">Jenis Jaminan</span>
                        <span class="font-extrabold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200" id="reviewJaminan">-</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500 text-[10px]">Foto Wajah</span>
                        <strong class="font-bold text-emerald-700" id="reviewFotoWajah">Belum diunggah</strong>
                    </div>
                </div>
            </div>

            {{-- PERSETUJUAN KODE ETIK & PAKTA INTEGRITAS --}}
            <div class="card-sb p-4 space-y-3 bg-slate-50/80 border border-slate-200">
                <h3 class="text-xs font-bold text-slate-900">
                    Pakta Integritas & Pernyataan Kemitraan
                </h3>

                <div class="space-y-2.5 text-[10.5px]">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" id="agree1" required checked class="mt-0.5 accent-[#173B67] w-4 h-4 rounded shrink-0">
                        <span class="text-slate-700 leading-snug">
                            Saya menyatakan seluruh informasi, identitas KTP, dan foto dokumen jaminan yang saya lampirkan adalah <strong>asli, sah menurut hukum, dan milik saya pribadi</strong>.
                        </span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" id="agree2" required checked class="mt-0.5 accent-[#173B67] w-4 h-4 rounded shrink-0">
                        <span class="text-slate-700 leading-snug">
                            Saya bersumpah menjunjung tinggi <strong>kejujuran, etika kesopanan, keamanan barang pelanggan</strong>, serta tidak melakukan tindakan melawan hukum selama menjalankan tugas.
                        </span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" id="agree3" required checked class="mt-0.5 accent-[#173B67] w-4 h-4 rounded shrink-0">
                        <span class="text-slate-700 leading-snug">
                            Saya bersedia menyerahkan <strong>fisik dokumen jaminan asli</strong> yang dipilih ke kantor perwakilan SayaBantu terdekat setelah dinyatakan lolos tahap verifikasi berkas online.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Navigasi Tombol Kirim --}}
            <div class="flex items-center gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToStep(3)"
                    class="w-28 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all text-center cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </button>
                <button
                    type="submit"
                    id="btnKirimPendaftaran"
                    class="flex-1 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-98 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Pendaftaran Mitra Sekarang</span>
                </button>
            </div>

            <p class="text-[9.5px] text-slate-400 text-center leading-tight">
                *Proses verifikasi berkas membutuhkan waktu maksimal 1x24 jam kerja oleh Tim Administrasi SayaBantu.
            </p>
        </div>

    </form>

</main>
@endsection

{{-- =========================================================================
     MODAL SUKSES PENDAFTARAN MITRA (DITEMPATKAN DI ROOT BODY)
========================================================================== --}}
@section('modals')
<div id="modalSuksesDaftar" class="sb-overlay-backdrop fixed inset-0 z-[150] pointer-events-none opacity-0 invisible transition-all duration-300 flex items-center justify-center p-4" aria-hidden="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm cursor-pointer pointer-events-auto" onclick="closeModalSuksesDaftar()"></div>

    {{-- Content Modal --}}
    <div class="relative w-full max-w-[390px] bg-white rounded-[28px] p-6 shadow-2xl z-10 pointer-events-auto transform scale-95 transition-transform duration-300 text-center space-y-4">
        
        {{-- Tombol Tutup X di Pojok Kanan Atas --}}
        <button
            type="button"
            onclick="closeModalSuksesDaftar()"
            class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center text-sm transition-all cursor-pointer z-20"
            title="Tutup Modal"
            aria-label="Tutup"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

        {{-- Ikon Sukses --}}
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 border-4 border-emerald-100 flex items-center justify-center mx-auto text-2xl shadow-inner animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>

        <div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[9.5px] font-black uppercase mb-1">
                Pendaftaran Berhasil Dikirim
            </span>
            <h3 class="text-base font-black text-slate-900 leading-tight">
                Selamat Datang di Keluarga Besar SayaBantu!
            </h3>
            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                Berkas identitas dan dokumen jaminan Anda telah berhasil masuk ke antrean verifikasi Admin.
            </p>
        </div>

        {{-- Kartu Info Tiket Registrasi --}}
        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/90 text-left space-y-2 text-xs">
            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/80">
                <span class="text-slate-400 text-[10px]">ID Mitra Resmi</span>
                <strong class="font-mono text-xs font-black text-emerald-700" id="idMitraOutput">#MTR-8829</strong>
            </div>
            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/80">
                <span class="text-slate-400 text-[10px]">Nomor Registrasi</span>
                <strong class="font-mono text-xs font-black text-[#173B67]" id="nomorRegistrasiOutput">#REG-MTR-882901</strong>
            </div>
            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/80">
                <span class="text-slate-400 text-[10px]">Status Akun</span>
                <span class="text-[9.5px] font-extrabold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 flex items-center gap-1">
                    <i class="fa-solid fa-clock-rotate-left text-[8.5px]"></i> Menunggu Verifikasi &amp; ACC Admin
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-[10px]">Jaminan Terdaftar</span>
                <strong class="text-slate-800 font-semibold text-[10.5px]" id="jaminanTerdaftarOutput">BPKB Motor Asli</strong>
            </div>
        </div>

        {{-- Langkah Selanjutnya & Pemberitahuan Email --}}
        <div class="p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200/90 text-left space-y-2 text-[11px] text-amber-950">
            <strong class="block font-bold text-amber-900 flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-envelope-circle-check text-amber-600"></i> Pemberitahuan Verifikasi via Email:
            </strong>
            <p class="leading-relaxed text-slate-700">
                Pendaftaran Anda berhasil dicatat dan saat ini berstatus <strong>Menunggu Verifikasi &amp; ACC Admin</strong>.
            </p>
            <div class="p-2.5 rounded-xl bg-white/90 border border-amber-200/60 flex items-start gap-2 text-[10.5px] text-slate-600">
                <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-xs shrink-0"></i>
                <span>Pemberitahuan resmi terkait apakah pendaftaran Anda <strong>diterima</strong> atau <strong>tidak</strong> akan dikirimkan langsung melalui <strong>notifikasi email</strong> ke alamat email yang Anda daftarkan.</span>
            </div>
        </div>

        {{-- Tombol Aksi: Pantau Status Pendaftaran & Kembali ke Beranda --}}
        <div class="space-y-2 pt-1.5">
            <a
                href="{{ route('mitra.pending') }}"
                onclick="closeModalSuksesDaftar()"
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <i class="fa-solid fa-hourglass-half text-xs"></i>
                <span>Pantau Status Pendaftaran</span>
            </a>
            <a
                href="{{ route('customer.index') }}"
                onclick="closeModalSuksesDaftar()"
                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-98 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer"
            >
                <i class="fa-solid fa-house text-xs"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;

    // Navigasi Antar Tahap Form
    function goToStep(step) {
        // Validasi sebelum maju
        if (step > currentStep) {
            if (!validateStep(currentStep)) return;
        }

        // Sembunyikan semua step
        for (let i = 1; i <= 4; i++) {
            const sec = document.getElementById('stepSection' + i);
            if (sec) sec.classList.add('hidden');
        }

        // Tampilkan step yang dituju
        const targetSec = document.getElementById('stepSection' + step);
        if (targetSec) targetSec.classList.remove('hidden');

        // Update Stepper Visual
        currentStep = step;
        updateStepperVisual(step);

        // Jika sampai ke step 4, isi ringkasan data review
        if (step === 4) {
            populateReviewSummary();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateStepperVisual(step) {
        // Progress bar percentage
        const progressPercent = ((step - 1) / 3) * 100;
        const bar = document.getElementById('stepperProgressBar');
        if (bar) bar.style.width = progressPercent + '%';

        // Bulatan Stepper
        document.querySelectorAll('.step-indicator').forEach(ind => {
            const indStep = parseInt(ind.getAttribute('data-step'), 10);
            const circle = ind.querySelector('.step-circle');
            const label = ind.querySelector('.step-label');

            if (indStep < step) {
                // Sudah selesai
                circle.className = "w-7 h-7 rounded-full bg-emerald-500 text-white font-bold text-[10px] flex items-center justify-center shadow-md border-2 border-white/80 transition-all step-circle";
                circle.innerHTML = '<i class="fa-solid fa-check text-[9px]"></i>';
                label.className = "text-[9px] font-bold text-white mt-1 step-label";
            } else if (indStep === step) {
                // Sedang aktif
                circle.className = "w-7 h-7 rounded-full bg-sky-500 text-white font-bold text-[10px] flex items-center justify-center shadow-lg border-2 border-white transition-all ring-2 ring-sky-300 step-circle";
                circle.innerText = indStep;
                label.className = "text-[9px] font-black text-white mt-1 step-label";
            } else {
                // Belum aktif
                circle.className = "w-7 h-7 rounded-full bg-white/20 text-white/70 font-bold text-[10px] flex items-center justify-center border-2 border-transparent transition-all step-circle";
                circle.innerText = indStep;
                label.className = "text-[9px] font-medium text-white/60 mt-1 step-label";
            }
        });
    }

    function validateStep(step, showFeedback = true) {
        if (step === 1) {
            const nama = document.getElementById('regNama').value.trim();
            const nik = document.getElementById('regNik').value.trim();
            const wa = document.getElementById('regWa').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const alamat = document.getElementById('regAlamat').value.trim();
            const pin = document.getElementById('regPin') ? document.getElementById('regPin').value : '';
            const pinConfirm = document.getElementById('regPinConfirm') ? document.getElementById('regPinConfirm').value : '';

            if (!nama || !nik || !wa || !email || !alamat) {
                if (showFeedback) alert('Harap lengkapi semua data diri wajib bertanda bintang (*) pada Tahap 1.');
                return false;
            }
            if (nik.length < 16) {
                if (showFeedback) alert('Nomor Induk Kependudukan (NIK) harus berjumlah 16 digit angka.');
                return false;
            }
            if (!pin || pin.length < 6) {
                if (showFeedback) alert('PIN / Kata sandi masuk mitra minimal 6 karakter.');
                return false;
            }
            if (pin !== pinConfirm) {
                if (showFeedback) alert('Konfirmasi PIN / Kata sandi tidak cocok.');
                return false;
            }
        } else if (step === 2) {
            const checkedSkills = document.querySelectorAll('input[name="skills[]"]:checked');
            if (checkedSkills.length === 0) {
                if (showFeedback) alert('Pilihlah minimal 1 kategori keahlian layanan yang Anda kuasai.');
                return false;
            }
            const norek = document.getElementById('regNoRekening').value.trim();
            const an = document.getElementById('regAtasNama').value.trim();
            if (!norek || !an) {
                if (showFeedback) alert('Harap lengkapi nomor rekening dan nama pemilik rekening pencairan dana.');
                return false;
            }
        } else if (step === 3) {
            const noJaminan = document.getElementById('inputNoJaminan').value.trim();
            if (!noJaminan) {
                if (showFeedback) alert('Harap isi nomor seri / nomor registrasi dokumen jaminan kemitraan Anda.');
                return false;
            }
            if (!document.getElementById('fileInputSelfie').files.length) {
                if (showFeedback) alert('Foto selfie sambil memegang e-KTP wajib diunggah.');
                return false;
            }
            if (!document.getElementById('fileInputFotoWajah').files.length) {
                if (showFeedback) alert('Foto wajah / foto diri wajib diunggah.');
                return false;
            }
        }

        const section = document.getElementById('stepSection' + step);
        const invalidField = section ? section.querySelector(':invalid') : null;
        if (invalidField) {
            if (showFeedback) invalidField.reportValidity();
            return false;
        }

        return true;
    }

    // Live Preview Image Saat Calon Mitra Memilih Berkas
    function previewUploadImage(input, previewId, badgeId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById(previewId);
                const placeholder = previewContainer.parentElement.querySelector('[id^="placeholder"]');
                const img = previewContainer.querySelector('img');
                const badge = document.getElementById(badgeId);

                img.src = e.target.result;
                previewContainer.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');

                if (badge) {
                    badge.className = "text-[9px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1";
                    badge.innerHTML = '<i class="fa-solid fa-check text-[8px]"></i> Terunggah';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Tampilkan Ringkasan pada Tahap 4
    function populateReviewSummary() {
        document.getElementById('reviewNama').innerText = document.getElementById('regNama').value || '-';
        document.getElementById('reviewNik').innerText = document.getElementById('regNik').value || '-';
        document.getElementById('reviewWa').innerText = document.getElementById('regWa').value || '-';
        document.getElementById('reviewKota').innerText = document.getElementById('regKota').value || '-';

        const bank = document.getElementById('regBank').value;
        const norek = document.getElementById('regNoRekening').value;
        const an = document.getElementById('regAtasNama').value;
        document.getElementById('reviewRekening').innerText = `${bank} - ${norek} (${an})`;

        const jaminan = document.getElementById('selectJenisJaminan').value;
        const noJaminan = document.getElementById('inputNoJaminan').value;
        document.getElementById('reviewJaminan').innerText = `${jaminan} (#${noJaminan})`;
        document.getElementById('reviewFotoWajah').innerText = document.getElementById('fileInputFotoWajah').files.length
            ? 'Sudah diunggah'
            : 'Belum diunggah';
    }

    window.goToStep = goToStep;

    // Handle Form Submit
    function handleRegistrasiSubmit(e) {
        e.preventDefault();

        for (let step = 1; step <= 4; step++) {
            if (!validateStep(step, false)) {
                goToStep(step);
                validateStep(step);
                return;
            }
        }

        const btn = document.getElementById('btnKirimPendaftaran');
        const selfie = document.getElementById('fileInputSelfie').files[0];
        const fotoWajah = document.getElementById('fileInputFotoWajah').files[0];

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Menyimpan & Mendaftarkan Mitra...';

        const idOut = document.getElementById('idMitraOutput');

            const nama = document.getElementById('regNama').value.trim();
            const nik = document.getElementById('regNik').value.trim();
            const wa = document.getElementById('regWa').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const kota = document.getElementById('regKota').value || 'Jakarta';
            const alamat = document.getElementById('regAlamat') ? document.getElementById('regAlamat').value.trim() : '';
            const pin = document.getElementById('regPin') ? document.getElementById('regPin').value : '123456';
            const jaminan = document.getElementById('selectJenisJaminan') ? document.getElementById('selectJenisJaminan').value : 'BPKB Sepeda Motor Asli';
            const noJaminan = document.getElementById('inputNoJaminan') ? document.getElementById('inputNoJaminan').value.trim() : '';

            // Ambil skills yang dicentang
            const checkedSkills = Array.from(document.querySelectorAll('input[name="skills[]"]:checked')).map(cb => cb.value);
            const keahlianStr = checkedSkills.length > 0 ? checkedSkills.join(', ') : 'Full Home Cleaning';

            const areaStr = alamat ? (kota + ' (' + alamat.split(',')[0].trim() + ')') : kota;
            const fullJaminan = jaminan + (noJaminan ? ` (#${noJaminan})` : '');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const formData = new FormData();
            Object.entries({
                nama,
                nik,
                whatsapp: wa,
                email,
                kota,
                area: areaStr,
                alamat,
                pin,
                keahlian: keahlianStr,
                service: keahlianStr,
                jaminan: fullJaminan
            }).forEach(([key, value]) => formData.append(key, value));
            formData.append('selfie', selfie);
            formData.append('foto_wajah', fotoWajah);

            fetch('{{ route("mitra.daftar.submit") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    let errMsg = data.message || 'Pendaftaran gagal diproses oleh server.';
                    if (data.errors) {
                        const errList = Object.values(data.errors).flat().join('\n• ');
                        errMsg += '\n\n• ' + errList;
                    }
                    throw new Error(errMsg);
                }
                return data;
            })
            .then((data) => {
                const assignedId = data.mitra.mitra_id;
                const assignedReg = data.mitra.regNo;
                const avatarUrl = data.mitra.avatar;

                document.getElementById('nomorRegistrasiOutput').innerText = assignedReg;
                if (idOut) idOut.innerText = assignedId;
                document.getElementById('jaminanTerdaftarOutput').innerText = fullJaminan;

                const newMitra = {
                    id: assignedId,
                    mitra_id: assignedId,
                    db_id: (data.mitra && data.mitra.id) ? data.mitra.id : null,
                    regNo: assignedReg,
                    nama: nama,
                    name: nama,
                    nik: nik,
                    whatsapp: wa,
                    phone: wa,
                    email: email,
                    kota: kota,
                    alamat: alamat,
                    area: areaStr,
                    pin: pin,
                    status: 'verifikasi',
                    statusLabel: 'Menunggu Verifikasi & ACC Admin',
                    keahlian: keahlianStr,
                    service: keahlianStr,
                    jaminan: fullJaminan,
                    avatar: avatarUrl,
                    image: avatarUrl,
                    rating: null,
                    tasks: 0,
                    registeredAt: new Date().toISOString()
                };

                // Update cache lokal sb_registered_mitras
                try {
                    let list = [];
                    const rawList = localStorage.getItem('sb_registered_mitras');
                    if (rawList) {
                        try { list = JSON.parse(rawList); } catch(err) {}
                    }
                    list = list.filter(m => m.whatsapp !== wa && m.id !== assignedId);
                    list.unshift(newMitra);
                    localStorage.setItem('sb_registered_mitras', JSON.stringify(list));
                    localStorage.setItem('sb_mitra_registration', JSON.stringify(newMitra));

                    // Set auth mitra lokal (status masih verifikasi, belum di-ACC admin)
                    localStorage.setItem('sb_mitra_auth', JSON.stringify({
                        isLoggedIn: false,
                        name: nama,
                        phone: wa,
                        specialty: keahlianStr,
                        role: 'mitra',
                        status: 'verifikasi'
                    }));
                } catch(e) {}

                // Reset tombol
                btn.disabled = false;
                btn.innerHTML = '<span>Kirim Pendaftaran &amp; Selesaikan</span><i class="fa-solid fa-arrow-right text-xs"></i>';

                // Tampilkan Modal Sukses (Notifikasi Pendaftaran Mitra)
                const modal = document.getElementById('modalSuksesDaftar');
                if (modal) {
                    modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
                    modal.classList.add('opacity-100', 'pointer-events-auto', 'is-open');
                    modal.removeAttribute('aria-hidden');
                    modal.style.display = 'flex';
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                    modal.style.pointerEvents = 'auto';
                    const card = modal.querySelector('.transform');
                    if (card) {
                        card.classList.remove('scale-95');
                        card.classList.add('scale-100');
                    }
                    document.documentElement.classList.add('sb-scroll-locked');
                    document.body.classList.add('sb-scroll-locked');

                    // Polling deteksi ACC admin otomatis saat modal sukses terbuka
                    const targetId = assignedId || wa;
                    if (targetId) {
                        const regPollTimer = setInterval(async () => {
                            try {
                                const res = await fetch(`/api/mitra/status?identifier=${encodeURIComponent(targetId)}`, {
                                    headers: { 'Accept': 'application/json' }
                                });
                                if (!res.ok) return;
                                const d = await res.json();
                                if (d && (d.status === 'approved' || d.status === 'aktif' || d.isLoggedIn === true)) {
                                    clearInterval(regPollTimer);
                                    localStorage.removeItem('sb_mitra_logged_out');
                                    localStorage.setItem('sb_mitra_auth', JSON.stringify({
                                        isLoggedIn: true,
                                        name: d.name || nama,
                                        phone: d.phone || wa,
                                        id: d.mitra_id || assignedId,
                                        mitra_id: d.mitra_id || assignedId,
                                        status: 'approved',
                                        role: 'mitra'
                                    }));
                                    localStorage.setItem('sb_mitra_registered', 'true');
                                    window.location.replace(d.redirect || "{{ route('mitra.dashboard') }}");
                                }
                            } catch(e) {}
                        }, 2000);
                    }
                }
            })
            .catch((err) => {
                btn.disabled = false;
                btn.innerHTML = '<span>Kirim Pendaftaran &amp; Selesaikan</span><i class="fa-solid fa-arrow-right text-xs"></i>';
                alert('⚠️ Pendaftaran Tidak Dapat Dikirim:\n\n' + (err.message || 'Terjadi kesalahan sistem saat menyimpan pendaftaran.'));
            });
    }

    function closeModalSuksesDaftar() {
        const modal = document.getElementById('modalSuksesDaftar');
        if (!modal) return;
        modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        modal.style.opacity = '';
        modal.style.visibility = '';
        modal.style.pointerEvents = '';
        const card = modal.querySelector('.transform');
        if (card) {
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
        }
        document.documentElement.classList.remove('sb-scroll-locked');
        document.body.classList.remove('sb-scroll-locked');
    }
    window.closeModalSuksesDaftar = closeModalSuksesDaftar;
</script>
@endpush

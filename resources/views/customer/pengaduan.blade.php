@extends('layouts.customer')

@section('title', 'Pusat Pengaduan & Mediasi - SayaBantu.com')

{{-- =========================================================
     TOP AREA (HEADER SERAGAM DENGAN BANNER CUSTOMER)
========================================================= --}}
@section('page-top')
<div class="customer-top-area">
    <div class="header-circle-one"></div>
    <div class="header-circle-two"></div>

    {{-- HEADER NAVBAR --}}
    @include('customer.partials.mobile-header')

    {{-- PAGE HEADER STANDAR --}}
    @include('customer.partials.page-header', [
        'judul' => 'Pusat Pengaduan',
        'deskripsi' => 'Sampaikan keluhan dan pantau penanganan masalah oleh Tim Mediasi SayaBantu.',
        'badge' => 'Mediasi Terpercaya',
        'badgeColor' => 'rose',
        'kembali' => false,
    ])
</div>
@endsection

{{-- =========================================================
     CONTENT AREA: STATISTIK, AJUKAN KELUHAN, & RIWAYAT PENGADUAN
========================================================= --}}
@section('content')
<main class="content-under-gradient px-4 pt-3 pb-8 space-y-3.5">

    {{-- 1. CARD HERO AJUKAN KELUHAN --}}
    <section class="card-sb p-4 bg-gradient-to-br from-[#0E1D31] via-[#17314A] to-[#173B67] text-white border-0 shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-rose-500/15 blur-xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[9px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-shield-halved text-[8px]"></i> Perlindungan Pengguna
                    </span>
                    <h2 class="text-sm font-extrabold text-white mt-1.5 leading-snug">
                        Mengalami Masalah pada Suruhan?
                    </h2>
                    <p class="text-[10px] text-white/80 leading-relaxed mt-0.5">
                        Kami menjamin setiap laporan ditinjau secara objektif, akuntabel, dan ditindaklanjuti maksimal 1x24 jam kerja.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-rose-400 text-lg shrink-0 shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            {{-- TOMBOL UTAMA AJUKAN KELUHAN --}}
            <button
                type="button"
                onclick="openModalAjukanKeluhanCustomer()"
                class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border border-rose-400/30"
            >
                <i class="fa-solid fa-bullhorn text-[11px]"></i>
                <span>Ajukan Keluhan Baru</span>
            </button>
        </div>
    </section>

    {{-- 2. RINGKASAN STATUS PENGADUAN SAYA --}}
    <section class="grid grid-cols-4 gap-2 text-center text-xs" id="custComplaintStatFilters">
        <button type="button" class="cust-stat-tab p-2.5 rounded-2xl bg-white border border-slate-900 shadow-xs cursor-pointer transition-all text-center focus:outline-none ring-2 ring-slate-900/20 scale-[1.02]" onclick="filterCustomerComplaints('all', this)" data-status="all">
            <span class="block text-sm font-black text-slate-900" id="statCustTotal">0</span>
            <span class="block text-[8.5px] text-slate-600 mt-0.5 font-bold">Total</span>
        </button>
        <button type="button" class="cust-stat-tab p-2.5 rounded-2xl bg-white border border-rose-200/80 shadow-xs cursor-pointer hover:border-rose-400 transition-all text-center focus:outline-none" onclick="filterCustomerComplaints('baru', this)" data-status="baru">
            <span class="block text-sm font-black text-rose-600" id="statCustBaru">0</span>
            <span class="block text-[8.5px] text-rose-700 mt-0.5 font-semibold">Menunggu</span>
        </button>
        <button type="button" class="cust-stat-tab p-2.5 rounded-2xl bg-white border border-amber-200/80 shadow-xs cursor-pointer hover:border-amber-400 transition-all text-center focus:outline-none" onclick="filterCustomerComplaints('proses', this)" data-status="proses">
            <span class="block text-sm font-black text-amber-600" id="statCustProses">0</span>
            <span class="block text-[8.5px] text-amber-700 mt-0.5 font-semibold">Pemeriksaan</span>
        </button>
        <button type="button" class="cust-stat-tab p-2.5 rounded-2xl bg-white border border-emerald-200/80 shadow-xs cursor-pointer hover:border-emerald-400 transition-all text-center focus:outline-none" onclick="filterCustomerComplaints('selesai', this)" data-status="selesai">
            <span class="block text-sm font-black text-emerald-600" id="statCustSelesai">0</span>
            <span class="block text-[8.5px] text-emerald-700 mt-0.5 font-semibold">Selesai</span>
        </button>
    </section>

    {{-- 3. FILTER TABS & SEARCH --}}
    <section class="space-y-2">
        <div class="flex items-center justify-between px-0.5">
            <h3 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-[#173B67]"></i> Riwayat Pengaduan Saya
            </h3>
            <span class="text-[9.5px] text-slate-400 font-medium" id="lblCustComplaintCount">
                Memuat riwayat...
            </span>
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px]">
            <button type="button" onclick="filterCustomerComplaints('all', this)" class="btn-tab-cust-comp px-3 py-1 rounded-full font-bold bg-[#0E1D31] text-white shrink-0 shadow-2xs">
                Semua
            </button>
            <button type="button" onclick="filterCustomerComplaints('baru', this)" class="btn-tab-cust-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                Menunggu Pemeriksaan
            </button>
            <button type="button" onclick="filterCustomerComplaints('proses', this)" class="btn-tab-cust-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                Dalam Penyelidikan
            </button>
            <button type="button" onclick="filterCustomerComplaints('selesai', this)" class="btn-tab-cust-comp px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 shrink-0">
                Selesai
            </button>
        </div>
    </section>

    {{-- 4. DAFTAR KARTU PENGADUAN SAYA --}}
    <section class="space-y-2.5" id="customerComplaintList">
        {{-- Diisi secara dinamis oleh JavaScript --}}
    </section>

    {{-- EMPTY STATE KETIKA BELUM ADA PENGADUAN --}}
    <div id="emptyCustComplaints" class="hidden card-sb p-6 text-center space-y-2.5 bg-white border border-slate-200/80">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto border border-emerald-100">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold text-slate-900">Belum Ada Pengaduan Aktif</h4>
            <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed max-w-xs mx-auto">
                Pengalaman suruhan Anda berjalan dengan lancar. Jika menemui masalah, silakan tekan tombol <strong>Ajukan Keluhan Baru</strong>.
            </p>
        </div>
        <button
            type="button"
            onclick="openModalAjukanKeluhanCustomer()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0E1D31] text-white font-bold text-[10px] hover:bg-[#173B67] transition-colors"
        >
            <i class="fa-solid fa-plus text-[9px]"></i> Buat Pengaduan
        </button>
    </div>

    {{-- INFORMASI PANDUAN MEDIASI --}}
    <section class="card-sb p-3.5 bg-slate-50 border border-slate-200/70 text-[10px] text-slate-600 space-y-2">
        <strong class="text-slate-800 font-bold block flex items-center gap-1.5">
            <i class="fa-solid fa-scale-balanced text-sky-600"></i> Standar Operasional Mediasi SayaBantu
        </strong>
        <ul class="space-y-1.5 text-[9.5px] leading-relaxed">
            <li class="flex items-start gap-1.5">
                <span class="text-sky-600 font-bold">1.</span>
                <span><strong>Objektivitas &amp; Fakta:</strong> Tim investigasi memeriksa bukti kronologi, rekaman chat di platform, dan histori pesanan dari kedua belah pihak.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-sky-600 font-bold">2.</span>
                <span><strong>Garansi Hak Pengguna:</strong> Apabila kelalaian berasal dari Mitra, dana pembayaran suruhan dapat dikembalikan penuh atau dikompensasikan.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-sky-600 font-bold">3.</span>
                <span><strong>Sanksi Ketat:</strong> Pelanggaran etika, tindakan tidak menyenangkan, atau pembatalan sepihak berakibat penangguhan akun mitra hingga pemutusan kemitraan.</span>
            </li>
        </ul>
    </section>

</main>

{{-- =========================================================
     MODAL 1: FORMULIR AJUKAN KELUHAN CUSTOMER
========================================================= --}}
<div id="modalAjukanKeluhanCustomer" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white">Formulir Pengaduan Layanan</h3>
                    <p class="text-[9.5px] text-sky-200 mt-0.5">Sampaikan laporan masalah dengan jelas &amp; akurat</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAjukanKeluhanCustomer()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- FORM BODY --}}
        <form id="formPengaduanCustomer" onsubmit="submitPengaduanCustomer(event)" class="p-4 space-y-3 overflow-y-auto text-xs flex-1">

            {{-- 1. PEMILIHAN SURUHAN / PEKERJAAN BERMASALAH --}}
            <div class="space-y-1">
                <label for="pilihSuruhanPengaduan" class="block text-[11px] font-bold text-slate-800">
                    Pilih Suruhan / Pekerjaan yang Bermasalah <span class="text-rose-500">*</span>
                </label>
                <select
                    id="pilihSuruhanPengaduan"
                    onchange="onSelectSuruhanPengaduan(this)"
                    required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                    <option value="">-- Pilih dari riwayat pesanan Anda --</option>
                    {{-- Diisi secara dinamis oleh JS dari sb_customer_orders --}}
                    <option value="manual">Tulis nomor tiket / pesanan lain secara manual...</option>
                </select>
                <div id="wrapManualOrderId" class="hidden mt-1.5">
                    <input
                        type="text"
                        id="inputManualOrderId"
                        placeholder="Contoh: #SB-SURUH-8821 atau sebutkan judul layanan..."
                        class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#173B67]"
                    >
                </div>
            </div>

            {{-- 2. PIHAK TERLAPOR (MITRA) --}}
            <div class="space-y-1">
                <label for="inputNamaTerlapor" class="block text-[11px] font-bold text-slate-800">
                    Pihak Terlapor (Nama Mitra / Terlapor)
                </label>
                <input
                    type="text"
                    id="inputNamaTerlapor"
                    placeholder="Contoh: Kala (Mitra)"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
            </div>

            {{-- 3. KATEGORI KELUHAN --}}
            <div class="space-y-1">
                <label for="pilihKategoriKeluhan" class="block text-[11px] font-bold text-slate-800">
                    Kategori Keluhan <span class="text-rose-500">*</span>
                </label>
                <select
                    id="pilihKategoriKeluhan"
                    required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                    <option value="">-- Pilih Kategori Masalah --</option>
                    <option value="Mitra Tidak Hadir / Terlambat">Mitra Tidak Hadir / Terlambat Tanpa Kabar</option>
                    <option value="Kualitas Pekerjaan Buruk">Kualitas Pekerjaan Buruk / Tidak Sesuai SOP</option>
                    <option value="Masalah Pembayaran / Tarif">Masalah Pembayaran / Permintaan Uang Ilegal</option>
                    <option value="Perilaku Tidak Sopan / Pelanggaran Etika">Perilaku Tidak Sopan / Pelanggaran Etika</option>
                    <option value="Kerusakan / Kehilangan Barang">Kerusakan / Kehilangan Barang Pelanggan</option>
                    <option value="Kendala Teknis Aplikasi">Kendala Teknis Platform / Aplikasi</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            {{-- 4. DESKRIPSI KRONOLOGI KEJADIAN --}}
            <div class="space-y-1">
                <label for="inputKronologi" class="block text-[11px] font-bold text-slate-800">
                    Kronologi Kejadian <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="inputKronologi"
                    rows="3"
                    required
                    minlength="10"
                    placeholder="Jelaskan secara runtut apa yang terjadi: waktu kejadian, kesepakatan awal, dan bentuk kerugian/kendala yang Anda alami..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67] leading-relaxed resize-none"
                ></textarea>
                <span class="text-[9.5px] text-slate-400 block">Jelaskan kronologi secara objektif dan jelas.</span>
            </div>

            {{-- 5. BUKTI PENDUKUNG (FOTO / TANGKAPAN LAYAR) --}}
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-800">
                    Bukti Pendukung (Foto, Tangkapan Layar Chat, Bukti Transfer)
                </label>
                <div class="p-3 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-sky-400 transition-colors text-center cursor-pointer relative" onclick="document.getElementById('inputBuktiFile').click()">
                    <input
                        type="file"
                        id="inputBuktiFile"
                        accept="image/*"
                        onchange="previewBuktiPengaduan(this)"
                        class="hidden"
                    >
                    <div id="buktiPlaceholder">
                        <i class="fa-solid fa-cloud-arrow-up text-lg text-slate-400"></i>
                        <p class="text-[10px] font-bold text-slate-700 mt-1">Pilih Foto atau Tangkapan Layar</p>
                        <p class="text-[8.5px] text-slate-400">Format JPG, PNG (Maks 5 MB)</p>
                    </div>
                    <div id="buktiPreviewBox" class="hidden items-center justify-center gap-2">
                        <img id="buktiPreviewImg" src="" alt="Bukti" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                        <div class="text-left">
                            <span id="buktiFileName" class="block text-[10px] font-bold text-slate-800 truncate max-w-[180px]">bukti.jpg</span>
                            <span class="text-[9px] text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Siap dilampirkan</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. PERNYATAAN KEBENARAN --}}
            <div class="pt-1 flex items-start gap-2">
                <input type="checkbox" id="checkIntegritas" required class="w-4 h-4 mt-0.5 rounded text-[#173B67] focus:ring-[#173B67] border-slate-300 cursor-pointer">
                <label for="checkIntegritas" class="text-[10px] text-slate-600 leading-tight cursor-pointer">
                    Saya menyatakan bahwa seluruh kronologi dan bukti yang dilaporkan adalah benar adanya untuk diproses tim mediasi SayaBantu.
                </label>
            </div>

            {{-- TOMBOL AKSI SUBMIT --}}
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeModalAjukanKeluhanCustomer()"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btnSubmitPengaduan"
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:scale-95 text-white font-extrabold text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim Pengaduan</span>
                </button>
            </div>

        </form>
    </div>
</div>

{{-- =========================================================
     MODAL 2: DETAIL TIKET & PERKEMBANGAN PENANGANAN
========================================================= --}}
<div id="modalDetailPengaduanCustomer" class="fixed inset-0 z-[150] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white flex items-center justify-between shrink-0">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-mono px-2 py-0.5 rounded-full bg-white/15 text-sky-200 font-bold" id="detTiketId">#ADU-000</span>
                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-rose-500 text-white" id="detTiketBadge">Menunggu</span>
                </div>
                <h3 class="text-xs font-extrabold text-white mt-1" id="detTiketCategory">Kategori Masalah</h3>
            </div>
            <button type="button" onclick="closeModalDetailPengaduanCustomer()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- DETAIL BODY --}}
        <div class="p-4 space-y-3 overflow-y-auto text-xs flex-1">

            {{-- INFO SURUHAN & TERLAPOR --}}
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-[10px]">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Suruhan Bermasalah:</span>
                    <strong class="text-slate-900 font-mono" id="detTiketOrderId">-</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Pihak Terlapor:</span>
                    <strong class="text-rose-700" id="detTiketReported">-</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Tanggal Dilaporkan:</span>
                    <span class="text-slate-700" id="detTiketDate">-</span>
                </div>
            </div>

            {{-- KRONOLOGI KEJADIAN --}}
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-900 block flex items-center gap-1">
                    <i class="fa-solid fa-file-lines text-[#173B67]"></i> Kronologi yang Dilaporkan
                </span>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-[10px] leading-relaxed whitespace-pre-wrap" id="detTiketChronology">
                    -
                </div>
            </div>

            {{-- BUKTI PENDUKUNG --}}
            <div class="space-y-1" id="detTiketEvidenceWrap">
                <span class="text-[11px] font-bold text-slate-900 block flex items-center gap-1">
                    <i class="fa-solid fa-paperclip text-sky-600"></i> Bukti Pendukung
                </span>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[10px] text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-image text-slate-400 text-base"></i>
                    <span id="detTiketEvidenceText" class="truncate flex-1">-</span>
                </div>
            </div>

            {{-- PERKEMBANGAN PENANGANAN OLEH ADMIN (TIMELINE MEDIASI) --}}
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <span class="text-[11px] font-extrabold text-slate-900 block flex items-center gap-1.5">
                    <i class="fa-solid fa-timeline text-emerald-600"></i> Perkembangan Penanganan Kasus
                </span>

                <div class="relative pl-5 space-y-3 before:absolute before:left-2 before:top-1.5 before:bottom-1.5 before:w-0.5 before:bg-slate-200" id="detTiketTimeline">
                    {{-- Diisi secara dinamis --}}
                </div>
            </div>

            {{-- KARTU STATUS TINDAK LANJUT MEDIASI (DINAMIS SESUAI STATUS) --}}
            <div id="detTiketActionBanner" class="p-3.5 rounded-2xl border space-y-2 text-xs hidden">
                {{-- Diisi dinamis oleh JavaScript --}}
            </div>

            {{-- CATATAN HASIL MEDIASI DARI ADMIN --}}
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 space-y-1 text-[10px] hidden" id="detTiketAdminDecisionBox">
                <strong class="text-emerald-900 font-bold block flex items-center gap-1">
                    <i class="fa-solid fa-clipboard-check text-emerald-600"></i> Catatan Hasil Mediasi
                </strong>
                <p class="text-emerald-800 leading-relaxed" id="detTiketAdminDecisionText">-</p>
            </div>

            {{-- KONTAK ESKALASI CS --}}
            <div class="pt-2">
                <div class="w-full py-2 px-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-[10px] text-center flex items-center justify-center gap-1.5 border border-slate-200">
                    <i class="fa-solid fa-shield-halved text-sky-600 text-xs"></i> Tim Mediasi Admin Memproses Tiket Otomatis
                </div>
            </div>

        </div>

    </div>
</div>

{{-- =========================================================
     MODAL 3: RUANG MEDIASI 3-PIHAK (CUSTOMER, ADMIN, MITRA)
========================================================= --}}
<div id="modalRuangMediasiCustomer" class="fixed inset-0 z-[160] hidden items-center justify-center p-4 bg-black/65 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[92vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold leading-tight text-white" id="custRoomTitle">Ruang Mediasi</h3>
                        <span class="text-[8.5px] font-black px-2 py-0.5 rounded-full bg-teal-500/30 text-teal-200 border border-teal-400/30 animate-pulse">
                            Aktif
                        </span>
                    </div>
                    <p class="text-[9.5px] text-sky-200 mt-0.5" id="custRoomSub">Fasilitasi penyelesaian damai dengan pendampingan Admin</p>
                </div>
            </div>
            <button type="button" onclick="closeRuangMediasiCustomerModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- DAFTAR PIHAK TERLIBAT (3 PIHAK) --}}
        <div class="px-4 py-2 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-[9.5px] shrink-0">
            <div class="flex items-center gap-1.5 text-teal-700 font-bold">
                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                <span>Admin (Fasilitator)</span>
            </div>
            <div class="flex items-center gap-1.5 text-sky-800 font-bold">
                <i class="fa-solid fa-user text-[10px]"></i>
                <span id="custRoomCustomerLabel">Anda (Customer)</span>
            </div>
            <div class="flex items-center gap-1.5 text-rose-700 font-bold">
                <i class="fa-solid fa-helmet-safety text-[10px]"></i>
                <span id="custRoomMitraLabel">Mitra Terkait</span>
            </div>
        </div>

        {{-- AREA CHAT & KARTU USULAN (SCROLLABLE) --}}
        <div class="p-4 space-y-3.5 overflow-y-auto text-xs flex-1 bg-slate-50/50" id="custMediationScrollArea">

            {{-- KARTU USULAN PENYELESAIAN DAMAI DARI ADMIN --}}
            <div id="custProposalCard" class="hidden p-3.5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-300 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between pb-1.5 border-b border-emerald-200">
                    <span class="text-[10.5px] font-black text-emerald-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-scale-balanced text-emerald-600"></i>
                        USULAN KESEPAKATAN DAMAI (MUFAKAT)
                    </span>
                    <span id="custProposalBadge" class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        Menunggu Persetujuan
                    </span>
                </div>

                <div class="text-[10px] text-emerald-950 bg-white/80 p-2.5 rounded-xl border border-emerald-200 leading-relaxed space-y-1">
                    <strong class="font-bold text-slate-800 block text-[9.5px]">Poin Kesepakatan yang Ditawarkan:</strong>
                    <p id="custProposalTermsText" class="leading-relaxed">
                        Pekerjaan diselesaikan sesuai rincian awal tanpa pemungutan biaya di luar kesepakatan aplikasi.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[9px] pt-1">
                    <div class="p-2 rounded-xl bg-white/80 border border-emerald-100 flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Customer:</span>
                        <span id="custProposalCustStatus" class="font-bold text-amber-600">Menunggu</span>
                    </div>
                    <div class="p-2 rounded-xl bg-white/80 border border-emerald-100 flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Mitra:</span>
                        <span id="custProposalMitraStatus" class="font-bold text-slate-600">Menunggu</span>
                    </div>
                </div>

                {{-- TOMBOL AKSI CUSTOMER TERHADAP USULAN --}}
                <div class="pt-1 flex items-center gap-2" id="custProposalActionButtons">
                    <button
                        type="button"
                        onclick="customerApproveProposal()"
                        class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-extrabold text-[10.5px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Setujui Usulan Ini</span>
                    </button>
                    <button
                        type="button"
                        onclick="customerRequestProposalChange()"
                        class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-300 cursor-pointer transition-all"
                    >
                        Minta Perubahan
                    </button>
                </div>

                <div id="custProposalAgreedNotice" class="hidden p-2 rounded-xl bg-emerald-600 text-white text-[10px] text-center font-bold">
                    <i class="fa-solid fa-circle-check mr-1"></i> Anda telah menyetujui poin usulan penyelesaian ini.
                </div>
            </div>

            {{-- DAFTAR PESAN MEDIASI 3-PIHAK --}}
            <div class="space-y-2.5" id="custMediationMessagesList">
                {{-- Diisi secara dinamis oleh JS --}}
            </div>

        </div>

        {{-- CHAT INPUT BAR --}}
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="custMediationInputText"
                    placeholder="Tulis tanggapan atau penjelasan Anda..."
                    onkeydown="if(event.key==='Enter') sendCustomerMediationMessage()"
                    class="flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#173B67]"
                >
                <button
                    type="button"
                    onclick="sendCustomerMediationMessage()"
                    class="py-2 px-4 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] active:scale-95 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all cursor-pointer shrink-0"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim</span>
                </button>
            </div>
            <span class="text-[8.5px] text-slate-400 mt-1 block">Semua pesan dipantau oleh Admin Fasilitator untuk menjaga objektivitas.</span>
        </div>

    </div>
</div>

{{-- =========================================================
     MODAL 4: UNGGAH BUKTI TAMBAHAN OLEH CUSTOMER
========================================================= --}}
<div id="modalUploadBuktiCustomer" class="fixed inset-0 z-[160] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh] animate-fadeIn">

        {{-- MODAL HEADER --}}
        <div class="p-4 bg-gradient-to-r from-amber-600 to-yellow-600 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-circle-question"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold leading-tight text-white">Unggah Bukti Tambahan</h3>
                    <p class="text-[9.5px] text-amber-100 mt-0.5">Permintaan bukti dari Tim Investigasi Admin</p>
                </div>
            </div>
            <button type="button" onclick="closeUploadBuktiCustomerModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formUploadBuktiCustomer" onsubmit="submitUploadBuktiCustomer(event)" class="p-4 space-y-3.5 text-xs overflow-y-auto">
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 space-y-1 text-[10px] text-amber-900">
                <strong class="font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-info text-amber-600"></i> Petunjuk Admin:
                </strong>
                <p id="custReqEvidenceInstructionsText" class="leading-relaxed">-</p>
                <div class="pt-1 text-[9px] text-amber-700 font-semibold" id="custReqEvidenceDeadlineText">
                    Batas Waktu: -
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-800">
                    Pilih File Foto / Dokumen / Screenshot <span class="text-rose-500">*</span>
                </label>
                <div class="p-3.5 rounded-xl bg-slate-50 border-2 border-dashed border-slate-300 hover:border-amber-400 transition-colors text-center cursor-pointer" onclick="document.getElementById('inputBuktiTambahanFile').click()">
                    <input
                        type="file"
                        id="inputBuktiTambahanFile"
                        accept="image/*"
                        required
                        onchange="previewBuktiTambahan(this)"
                        class="hidden"
                    >
                    <div id="phBuktiTambahan">
                        <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>
                        <p class="text-[10.5px] font-bold text-slate-700 mt-1">Pilih Dokumen Bukti</p>
                        <p class="text-[9px] text-slate-400">Format JPG, PNG (Maks 5 MB)</p>
                    </div>
                    <div id="actBuktiTambahan" class="hidden items-center justify-center gap-2">
                        <i class="fa-solid fa-file-image text-emerald-600 text-lg"></i>
                        <div class="text-left">
                            <span id="nameBuktiTambahan" class="block text-[10px] font-bold text-slate-800 truncate max-w-[200px]">file.jpg</span>
                            <span class="text-[9px] text-emerald-600 font-semibold"><i class="fa-solid fa-check"></i> Siap diunggah</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label for="inputCatatanBuktiTambahan" class="block text-[11px] font-bold text-slate-800">
                    Keterangan Penjelasan Bukti <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="inputCatatanBuktiTambahan"
                    rows="3"
                    required
                    placeholder="Jelaskan apa yang terlihat pada bukti ini untuk mempermudah verifikasi Admin..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 leading-relaxed resize-none"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeUploadBuktiCustomerModal()"
                    class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 active:scale-95 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                    <span>Kirim Bukti ke Admin</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // State Filter Pengaduan Customer
    window.__currentCustCompFilter = 'all';
    let currentCustomerActiveComplaint = null;

    // Bersihkan data dummy lama (hanya sekali jika tersisa)
    (function cleanupCustomerDummyComplaints() {
        try {
            const key = 'sb_cleared_dummy_complaints_v4';
            if (!localStorage.getItem(key)) {
                const raw = localStorage.getItem('sb_complaints');
                if (raw) {
                    let list = JSON.parse(raw);
                    list = list.filter(c => !['#ADU-102', '#ADU-101', '#ADU-100', '#ADU-098'].includes(c.id));
                    localStorage.setItem('sb_complaints', JSON.stringify(list));
                }
                localStorage.setItem(key, 'true');
            }
        } catch(e) {}
    })();

    // Ambil daftar pengaduan dari LocalStorage (data murni tanpa dummy)
    function getStoredComplaints() {
        try {
            const raw = localStorage.getItem('sb_complaints');
            return raw ? JSON.parse(raw) : [];
        } catch(e) {
            return [];
        }
    }

    // Simpan daftar pengaduan
    function saveStoredComplaints(complaints) {
        try {
            localStorage.setItem('sb_complaints', JSON.stringify(complaints));
        } catch(e) {}
    }

    // Helper resolusi nama customer aktif
    function getCustomerDisplayName() {
        try {
            if (typeof getCustomerAuth === 'function') {
                const u = getCustomerAuth();
                if (u && u.name) return u.name;
            }
            const raw = localStorage.getItem('sb_customer_user');
            if (raw) {
                const u = JSON.parse(raw);
                if (u && u.name) return u.name;
            }
        } catch(e) {}
        return 'Customer SayaBantu';
    }

    // Render Riwayat Pengaduan Customer
    function renderCustomerComplaints() {
        const list = getStoredComplaints();
        const container = document.getElementById('customerComplaintList');
        const emptyEl = document.getElementById('emptyCustComplaints');
        const countLbl = document.getElementById('lblCustComplaintCount');

        // Filter pengaduan yang relevan dengan customer (pelapor customer atau terlapor customer)
        const custName = getCustomerDisplayName().toLowerCase();
        const custComplaints = list.filter(item => {
            const role = item.reporterRole || 'customer';
            const rep = (item.reporterName || '').toLowerCase();
            const repd = (item.reportedName || '').toLowerCase();
            return role === 'customer' || rep.includes(custName) || repd.includes(custName);
        });

        // Update Statistik
        const total = custComplaints.length;
        const baru = custComplaints.filter(c => c.status === 'baru').length;
        const proses = custComplaints.filter(c => ['proses', 'menunggu_bukti', 'bukti_dikirim', 'dalam_mediasi'].includes(c.status)).length;
        const selesai = custComplaints.filter(c => ['selesai', 'ditolak', 'mediasi_tidak_tercapai', 'menunggu_keputusan_admin'].includes(c.status)).length;

        document.getElementById('statCustTotal').innerText = total;
        document.getElementById('statCustBaru').innerText = baru;
        document.getElementById('statCustProses').innerText = proses;
        document.getElementById('statCustSelesai').innerText = selesai;

        // Terapkan filter tab aktif
        let filtered = custComplaints;
        if (window.__currentCustCompFilter === 'baru') {
            filtered = custComplaints.filter(c => c.status === 'baru');
        } else if (window.__currentCustCompFilter === 'proses') {
            filtered = custComplaints.filter(c => ['proses', 'menunggu_bukti', 'bukti_dikirim', 'dalam_mediasi'].includes(c.status));
        } else if (window.__currentCustCompFilter === 'selesai') {
            filtered = custComplaints.filter(c => ['selesai', 'ditolak', 'mediasi_tidak_tercapai', 'menunggu_keputusan_admin'].includes(c.status));
        }

        if (countLbl) {
            countLbl.innerText = `${filtered.length} dari ${total} pengaduan`;
        }

        if (filtered.length === 0) {
            container.innerHTML = '';
            if (emptyEl) emptyEl.classList.remove('hidden');
            return;
        }

        if (emptyEl) emptyEl.classList.add('hidden');

        container.innerHTML = filtered.map(item => {
            const statusConfig = {
                'baru': {
                    label: 'Menunggu Pemeriksaan',
                    badgeClass: 'bg-rose-50 text-rose-700 border-rose-200',
                    dotColor: 'bg-rose-500',
                },
                'proses': {
                    label: 'Dalam Penyelidikan',
                    badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
                    dotColor: 'bg-amber-500 animate-pulse',
                },
                'menunggu_bukti': {
                    label: 'Perlu Bukti Tambahan',
                    badgeClass: 'bg-orange-50 text-orange-800 border-orange-200',
                    dotColor: 'bg-orange-500 animate-pulse',
                },
                'bukti_dikirim': {
                    label: 'Bukti Terkirim',
                    badgeClass: 'bg-sky-50 text-sky-800 border-sky-200',
                    dotColor: 'bg-sky-500',
                },
                'dalam_mediasi': {
                    label: 'Dalam Mediasi 3-Pihak',
                    badgeClass: 'bg-teal-50 text-teal-800 border-teal-300',
                    dotColor: 'bg-teal-500 animate-pulse',
                },
                'mediasi_tidak_tercapai': {
                    label: 'Mediasi Tidak Tercapai',
                    badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
                    dotColor: 'bg-amber-500',
                },
                'menunggu_keputusan_admin': {
                    label: 'Mediasi Tidak Tercapai',
                    badgeClass: 'bg-amber-50 text-amber-800 border-amber-200',
                    dotColor: 'bg-amber-500',
                },
                'selesai': {
                    label: 'Selesai Dimediasi',
                    badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    dotColor: 'bg-emerald-500',
                },
                'ditolak': {
                    label: 'Laporan Ditolak',
                    badgeClass: 'bg-slate-100 text-slate-600 border-slate-200',
                    dotColor: 'bg-slate-400',
                }
            }[item.status] || {
                label: 'Diproses',
                badgeClass: 'bg-sky-50 text-sky-700 border-sky-200',
                dotColor: 'bg-sky-500',
            };

            const isMediationActive = item.status === 'dalam_mediasi';

            return `
                <article class="card-sb p-3.5 bg-white border ${isMediationActive ? 'border-teal-400 ring-2 ring-teal-400/20 shadow-md' : 'border-slate-200/90 shadow-xs'} space-y-2.5 hover:border-sky-300 transition-all cursor-pointer active:scale-[0.99] group" onclick='openDetailPengaduanCustomerById("${item.id}")'>
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                <span class="font-mono text-[9px] font-black px-2 py-0.5 rounded-md bg-slate-100 text-[#173B67] border border-slate-200">
                                    ${item.id}
                                </span>
                                <span class="text-[9.5px] font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                    ${item.category}
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-500">
                                Suruhan: <strong class="text-slate-800 font-mono">${item.orderId || '-'}</strong> • Terlapor: <strong class="text-rose-700">${item.reportedName || 'Mitra'}</strong>
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[8.5px] font-extrabold border shrink-0 ${statusConfig.badgeClass}">
                            <span class="w-1.5 h-1.5 rounded-full ${statusConfig.dotColor}"></span>
                            ${statusConfig.label}
                        </span>
                    </div>

                    <p class="text-[10px] text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed line-clamp-2">
                        "${item.chronology || item.summary}"
                    </p>

                    ${isMediationActive ? `
                    <div class="p-2.5 rounded-xl bg-teal-50 border border-teal-200 text-[9.5px] text-teal-900 flex items-center justify-between gap-2">
                        <span class="flex items-center gap-1.5 font-bold">
                            <i class="fa-solid fa-comments text-teal-600"></i> Ruang Mediasi 3-Pihak Terbuka
                        </span>
                        <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-teal-600 text-white shadow-2xs">
                            Buka Dialog
                        </span>
                    </div>
                    ` : ''}

                    ${item.adminNote ? `
                    <div class="p-2 rounded-xl bg-emerald-50/70 border border-emerald-200 text-[9.5px] text-emerald-800 flex items-start gap-1.5">
                        <i class="fa-solid fa-clipboard-check text-emerald-600 mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="font-bold">Respon Admin:</strong> ${item.adminNote}
                        </div>
                    </div>
                    ` : ''}

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[9.5px]">
                        <span class="text-slate-400">
                            <i class="fa-regular fa-clock mr-1"></i> ${item.date || 'Hari ini'}
                        </span>
                        <button
                            type="button"
                            onclick='event.stopPropagation(); openDetailPengaduanCustomerById("${item.id}")'
                            class="font-bold text-[#173B67] group-hover:text-blue-600 flex items-center gap-1 transition-colors cursor-pointer"
                        >
                            <span>${isMediationActive ? 'Masuk Mediasi' : 'Lihat Progres Mediasi'}</span>
                            <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </button>
                    </div>
                </article>
            `;
        }).join('');
    }

    // Filter Tab & Stat Boxes Pengaduan Customer
    function filterCustomerComplaints(status, clickedEl) {
        window.__currentCustCompFilter = status;

        document.querySelectorAll('.cust-stat-tab').forEach(card => {
            const st = card.getAttribute('data-status');
            card.classList.remove('ring-2', 'scale-[1.02]', 'border-slate-900', 'border-rose-500', 'border-amber-500', 'border-emerald-500', 'bg-rose-50/60', 'bg-amber-50/60', 'bg-emerald-50/60');

            if (st === status) {
                card.classList.add('scale-[1.02]', 'ring-2');
                if (status === 'all') {
                    card.classList.add('border-slate-900', 'ring-slate-900/20');
                } else if (status === 'baru') {
                    card.classList.add('border-rose-500', 'ring-rose-400/30', 'bg-rose-50/60');
                } else if (status === 'proses') {
                    card.classList.add('border-amber-500', 'ring-amber-400/30', 'bg-amber-50/60');
                } else if (status === 'selesai') {
                    card.classList.add('border-emerald-500', 'ring-emerald-400/30', 'bg-emerald-50/60');
                }
            } else {
                if (st === 'all') card.classList.add('border-slate-200/80');
                else if (st === 'baru') card.classList.add('border-rose-200/80');
                else if (st === 'proses') card.classList.add('border-amber-200/80');
                else if (st === 'selesai') card.classList.add('border-emerald-200/80');
            }
        });

        const filterMap = { 'all': 0, 'baru': 1, 'proses': 2, 'selesai': 3 };
        const targetIdx = filterMap[status] ?? 0;
        const pillButtons = document.querySelectorAll('.btn-tab-cust-comp');
        pillButtons.forEach((b, idx) => {
            if (idx === targetIdx) {
                b.classList.remove('bg-slate-100', 'text-slate-700', 'font-medium');
                b.classList.add('bg-[#0E1D31]', 'text-white', 'font-bold');
            } else {
                b.classList.remove('bg-[#0E1D31]', 'text-white', 'font-bold');
                b.classList.add('bg-slate-100', 'text-slate-700', 'font-medium');
            }
        });

        renderCustomerComplaints();
    }
    window.filterCustomerComplaints = filterCustomerComplaints;

    // Populate Dropdown Pesanan Customer di Modal
    function populateCustomerOrdersDropdown() {
        const sel = document.getElementById('pilihSuruhanPengaduan');
        if (!sel) return;

        let orders = [];
        try {
            const raw = localStorage.getItem('sb_customer_orders');
            if (raw) orders = JSON.parse(raw);
        } catch(e) {}

        sel.innerHTML = `
            <option value="">-- Pilih dari riwayat pesanan Anda --</option>
            <option value="manual">Tulis nomor tiket / pesanan lain secara manual...</option>
        `;

        if (orders.length > 0) {
            orders.forEach(ord => {
                const opt = document.createElement('option');
                opt.value = ord.code || '#SB-SURUH';
                opt.text = `${ord.code} - ${ord.layanan} (${ord.jadwal || ord.waktu || 'Tercatat'})`;
                opt.setAttribute('data-mitra', ord.mitraName || 'Kala (Mitra)');
                sel.insertBefore(opt, sel.lastElementChild);
            });
        }
    }

    function onSelectSuruhanPengaduan(el) {
        const val = el.value;
        const manualWrap = document.getElementById('wrapManualOrderId');
        const terlaporInput = document.getElementById('inputNamaTerlapor');

        if (val === 'manual') {
            if (manualWrap) manualWrap.classList.remove('hidden');
        } else {
            if (manualWrap) manualWrap.classList.add('hidden');
            const selectedOpt = el.options[el.selectedIndex];
            const mitra = selectedOpt.getAttribute('data-mitra');
            if (mitra && terlaporInput) {
                terlaporInput.value = mitra;
            }
        }
    }

    // Buka Modal Form Pengaduan
    function openModalAjukanKeluhanCustomer() {
        populateCustomerOrdersDropdown();
        document.getElementById('formPengaduanCustomer').reset();
        document.getElementById('buktiPlaceholder').classList.remove('hidden');
        document.getElementById('buktiPreviewBox').classList.add('hidden');
        document.getElementById('wrapManualOrderId').classList.add('hidden');

        const modal = document.getElementById('modalAjukanKeluhanCustomer');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalAjukanKeluhanCustomer() {
        const modal = document.getElementById('modalAjukanKeluhanCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Preview File Bukti
    function previewBuktiPengaduan(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('buktiPreviewImg').src = e.target.result;
                document.getElementById('buktiFileName').innerText = file.name;
                document.getElementById('buktiPlaceholder').classList.add('hidden');
                document.getElementById('buktiPreviewBox').classList.remove('hidden');
                document.getElementById('buktiPreviewBox').classList.add('flex');
            };
            reader.readAsDataURL(file);
        }
    }

    // Submit Pengaduan Customer
    function submitPengaduanCustomer(e) {
        e.preventDefault();

        const btn = document.getElementById('btnSubmitPengaduan');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Mendaftarkan Laporan...';

        const selSuruhan = document.getElementById('pilihSuruhanPengaduan');
        let orderCode = selSuruhan.value;
        if (orderCode === 'manual') {
            orderCode = document.getElementById('inputManualOrderId').value.trim() || '#SB-SURUH-MANUAL';
        }

        const terlapor = document.getElementById('inputNamaTerlapor').value.trim() || 'Kala (Mitra)';
        const kategori = document.getElementById('pilihKategoriKeluhan').value;
        const kronologi = document.getElementById('inputKronologi').value.trim();
        const buktiFile = document.getElementById('inputBuktiFile').files[0];
        const buktiText = buktiFile ? buktiFile.name : 'Bukti kronologi & tangkapan layar chat dilampirkan.';

        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' • ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const randId = '#ADU-' + Math.floor(100 + Math.random() * 900);

        const newComplaint = {
            id: randId,
            orderId: orderCode,
            reporterName: getCustomerDisplayName(),
            reporterRole: 'customer',
            reportedName: terlapor,
            category: kategori,
            summary: kronologi.length > 90 ? (kronologi.substring(0, 90) + '...') : kronologi,
            chronology: kronologi,
            evidence: buktiText,
            status: 'baru',
            adminNote: '',
            date: dateStr,
            timeline: [
                { stage: 'Pengaduan Diterima', time: dateStr, desc: 'Laporan keluhan berhasil didaftarkan ke sistem mediasi terpusat SayaBantu.' },
                { stage: 'Menunggu Pemeriksaan Admin', time: dateStr, desc: 'Tiket diteruskan ke antrean investigasi dashboard Admin.' }
            ]
        };

        setTimeout(() => {
            const list = getStoredComplaints();
            list.unshift(newComplaint);
            saveStoredComplaints(list);

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane text-[10px]"></i> <span>Kirim Pengaduan</span>';
            closeModalAjukanKeluhanCustomer();

            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Pengaduan Terkirim!', `Nomor Tiket Anda: ${randId}. Admin akan menindaklanjuti dalam waktu maksimal 1x24 jam.`, 'success');
            } else {
                alert(`Pengaduan berhasil didaftarkan!\n\nNomor Tiket: ${randId}\nLaporan telah masuk ke antrean Tim Mediasi Admin SayaBantu.`);
            }

            renderCustomerComplaints();
        }, 600);
    }

    function openDetailPengaduanCustomerById(id) {
        const list = getStoredComplaints();
        const item = list.find(c => c.id === id);
        if (item) {
            openDetailPengaduanCustomer(item);
        }
    }
    window.openDetailPengaduanCustomerById = openDetailPengaduanCustomerById;

    // Buka Modal Detail Tiket Pengaduan
    function openDetailPengaduanCustomer(item) {
        currentCustomerActiveComplaint = item;

        document.getElementById('detTiketId').innerText = item.id;
        document.getElementById('detTiketCategory').innerText = item.category;
        document.getElementById('detTiketOrderId').innerText = item.orderId || '-';
        document.getElementById('detTiketReported').innerText = item.reportedName || '-';
        document.getElementById('detTiketDate').innerText = item.date || '-';
        document.getElementById('detTiketChronology').innerText = item.chronology || item.summary || '-';
        document.getElementById('detTiketEvidenceText').innerText = item.evidence || 'Tidak ada berkas pendukung terlampir.';

        const badgeEl = document.getElementById('detTiketBadge');
        if (badgeEl) {
            const labels = {
                'baru': 'Menunggu Pemeriksaan',
                'proses': 'Dalam Penyelidikan',
                'menunggu_bukti': 'Perlu Bukti Tambahan',
                'bukti_dikirim': 'Bukti Dikirim',
                'dalam_mediasi': 'Dalam Mediasi 3-Pihak',
                'mediasi_tidak_tercapai': 'Mediasi Tidak Tercapai',
                'menunggu_keputusan_admin': 'Mediasi Tidak Tercapai',
                'selesai': 'Selesai Dimediasi',
                'ditolak': 'Ditolak'
            };
            const classes = {
                'baru': 'bg-rose-500 text-white',
                'proses': 'bg-amber-500 text-white',
                'menunggu_bukti': 'bg-orange-500 text-white',
                'bukti_dikirim': 'bg-sky-500 text-white',
                'dalam_mediasi': 'bg-teal-600 text-white',
                'mediasi_tidak_tercapai': 'bg-amber-600 text-white',
                'menunggu_keputusan_admin': 'bg-amber-600 text-white',
                'selesai': 'bg-emerald-600 text-white',
                'ditolak': 'bg-slate-500 text-white'
            };
            badgeEl.innerText = labels[item.status] || 'Diproses';
            badgeEl.className = `text-[9px] font-extrabold px-2 py-0.5 rounded-full ${classes[item.status] || 'bg-blue-600 text-white'}`;
        }

        // Render Action Banner Dinamis
        const actBanner = document.getElementById('detTiketActionBanner');
        if (actBanner) {
            if (item.status === 'menunggu_bukti') {
                const instructions = item.evidenceRequest?.instructions || 'Admin meminta bukti tambahan untuk memperjelas laporan.';
                const deadline = item.evidenceRequest?.deadline || 'Segera';
                actBanner.className = 'p-3.5 rounded-2xl bg-amber-50 border border-amber-300 space-y-2 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            <strong class="text-amber-900 font-bold flex items-center gap-1.5 text-xs">
                                <i class="fa-solid fa-file-circle-question text-amber-600"></i> Permintaan Bukti dari Admin
                            </strong>
                            <p class="text-[10px] text-amber-800 leading-relaxed">${instructions}</p>
                            <span class="text-[9px] text-amber-700 font-semibold block mt-1">Batas Waktu: ${deadline}</span>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="openUploadBuktiCustomerModal()"
                        class="w-full py-2 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-[10.5px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Unggah Bukti Tambahan Sekarang</span>
                    </button>
                `;
            } else if (item.status === 'bukti_dikirim') {
                actBanner.className = 'p-3 rounded-2xl bg-sky-50 border border-sky-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-sky-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-circle-check text-sky-600"></i> Bukti Tambahan Telah Dikirim
                    </strong>
                    <p class="text-[10px] text-sky-800 leading-relaxed">
                        Dokumen bukti telah diterima dan sedang dipelajari oleh Admin untuk menentukan langkah mediasi selanjutnya.
                    </p>
                `;
            } else if (item.status === 'dalam_mediasi') {
                actBanner.className = 'p-3.5 rounded-2xl bg-teal-50 border border-teal-300 space-y-2 text-xs shadow-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <div class="space-y-0.5">
                        <strong class="text-teal-900 font-extrabold flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-handshake text-teal-600"></i> Sesi Mediasi 3-Pihak Sedang Berlangsung
                        </strong>
                        <p class="text-[10px] text-teal-800 leading-relaxed">
                            Admin SayaBantu telah membuka ruang mediasi untuk Anda dan Mitra. Anda dapat saling berdiskusi dan meninjau usulan mufakat.
                        </p>
                    </div>
                    <button
                        type="button"
                        onclick="openRuangMediasiCustomerModal()"
                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-700 to-emerald-600 hover:from-teal-600 hover:to-emerald-500 text-white font-extrabold text-[11px] shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all"
                    >
                        <i class="fa-solid fa-comments text-xs"></i>
                        <span>Masuk ke Ruang Mediasi Khusus</span>
                    </button>
                `;
            } else if (item.status === 'mediasi_tidak_tercapai' || item.status === 'menunggu_keputusan_admin') {
                actBanner.className = 'p-3 rounded-2xl bg-amber-50 border border-amber-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-amber-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-handshake text-amber-600"></i> Mediasi Tidak Mencapai Kesepakatan
                    </strong>
                    <p class="text-[10px] text-amber-800 leading-relaxed">
                        Admin telah mencatat hasil mediasi. Keputusan atas usulan tetap berada pada Customer dan Mitra.
                    </p>
                `;
            } else if (item.status === 'selesai') {
                actBanner.className = 'p-3 rounded-2xl bg-emerald-50 border border-emerald-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-emerald-900 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Mediasi Selesai & Kasus Ditutup
                    </strong>
                    <p class="text-[10px] text-emerald-800 leading-relaxed">
                        Kesepakatan damai telah tercapai dan seluruh komitmen telah tuntas dilaksanakan.
                    </p>
                    <button
                        type="button"
                        onclick="openRuangMediasiCustomerModal()"
                        class="mt-1 text-[10px] font-bold text-teal-700 hover:text-teal-900 underline flex items-center gap-1"
                    >
                        <i class="fa-solid fa-clock-rotate-left"></i> Lihat Catatan & Rekaman Mediasi
                    </button>
                `;
            } else if (item.status === 'ditolak') {
                actBanner.className = 'p-3 rounded-2xl bg-slate-100 border border-slate-300 space-y-1 text-xs';
                actBanner.classList.remove('hidden');
                actBanner.innerHTML = `
                    <strong class="text-slate-800 font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-ban text-slate-500"></i> Pengaduan Ditolak Berdasarkan Pemeriksaan
                    </strong>
                    <p class="text-[10px] text-slate-600 leading-relaxed">
                        ${item.rejectionReason || 'Laporan tidak dapat dilanjutkan setelah verifikasi bukti.'}
                    </p>
                `;
            } else {
                actBanner.classList.add('hidden');
            }
        }

        // Timeline Mediasi
        const timelineEl = document.getElementById('detTiketTimeline');
        if (timelineEl) {
            const timelineData = item.timeline && item.timeline.length > 0 ? item.timeline : [
                { stage: 'Pengaduan Diterima', time: item.date || 'Hari ini', desc: 'Laporan tercatat di sistem mediasi terpusat SayaBantu.' },
                { stage: 'Pemeriksaan Berkas', time: item.date || 'Hari ini', desc: 'Dalam antrean verifikasi bukti tim investigasi.' }
            ];

            timelineEl.innerHTML = timelineData.map((step, idx) => `
                <div class="relative">
                    <span class="absolute -left-[25px] top-0.5 w-3.5 h-3.5 rounded-full ${idx === 0 ? 'bg-[#173B67] text-white' : 'bg-sky-400 text-white'} flex items-center justify-center text-[7.5px] font-black shadow-xs">
                        ${idx + 1}
                    </span>
                    <strong class="text-[10px] font-bold text-slate-900 block leading-tight">${step.stage}</strong>
                    <span class="text-[8.5px] text-slate-400 block">${step.time}</span>
                    <p class="text-[9.5px] text-slate-600 mt-0.5 leading-relaxed">${step.desc}</p>
                </div>
            `).join('');
        }

        // Box Catatan Admin
        const decisionBox = document.getElementById('detTiketAdminDecisionBox');
        const decisionText = document.getElementById('detTiketAdminDecisionText');
        if (item.adminNote) {
            decisionBox.classList.remove('hidden');
            decisionText.innerText = item.adminNote;
        } else {
            decisionBox.classList.add('hidden');
        }

        const modal = document.getElementById('modalDetailPengaduanCustomer');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModalDetailPengaduanCustomer() {
        const modal = document.getElementById('modalDetailPengaduanCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // =========================================================
    // RUANG MEDIASI CUSTOMER
    // =========================================================
    function openRuangMediasiCustomerModal() {
        if (!currentCustomerActiveComplaint) return;
        const item = currentCustomerActiveComplaint;

        document.getElementById('custRoomTitle').innerText = `Ruang Mediasi ${item.id}`;
        document.getElementById('custRoomSub').innerText = `Suruhan: ${item.orderId || '-'} • Kategori: ${item.category}`;
        document.getElementById('custRoomCustomerLabel').innerText = `Anda (${item.reporterRole === 'customer' ? item.reporterName : (item.reportedName || 'Customer')})`;
        document.getElementById('custRoomMitraLabel').innerText = item.reporterRole === 'customer' ? (item.reportedName || 'Mitra') : item.reporterName;

        renderCustomerMediationMessages();
        renderCustomerProposalCard();

        const modal = document.getElementById('modalRuangMediasiCustomer');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        setTimeout(() => {
            const sc = document.getElementById('custMediationScrollArea');
            if (sc) sc.scrollTop = sc.scrollHeight;
        }, 100);
    }
    window.openRuangMediasiCustomerModal = openRuangMediasiCustomerModal;

    function closeRuangMediasiCustomerModal() {
        const modal = document.getElementById('modalRuangMediasiCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeRuangMediasiCustomerModal = closeRuangMediasiCustomerModal;

    function renderCustomerMediationMessages() {
        const item = currentCustomerActiveComplaint;
        const listEl = document.getElementById('custMediationMessagesList');
        if (!listEl || !item) return;

        let messages = (item.mediation && item.mediation.messages) ? item.mediation.messages : [];
        if (messages.length === 0) {
            const defaultDirections = item.mediationDirections || 'Kedua pihak diminta menjelaskan kronologi secara objektif.';
            messages = [
                {
                    sender: 'Admin SayaBantu (Fasilitator)',
                    role: 'admin',
                    text: `Mediasi resmi dibuka untuk tiket ${item.id}. Arahan: "${defaultDirections}"`,
                    time: item.date || 'Hari ini'
                }
            ];
            if (!item.mediation) item.mediation = {};
            item.mediation.messages = messages;
        }

        listEl.innerHTML = messages.map(m => {
            if (m.role === 'admin') {
                return `
                    <div class="p-2.5 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-slate-800 space-y-1 max-w-[94%] shadow-2xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-teal-800">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> ${m.sender}
                            </span>
                            <span class="text-slate-400 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-slate-700 leading-relaxed">${m.text}</p>
                    </div>
                `;
            } else if (m.role === 'customer') {
                return `
                    <div class="ml-auto p-2.5 rounded-2xl bg-[#0E1D31] text-white space-y-1 max-w-[88%] shadow-xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-sky-200">
                            <span>Anda (Customer)</span>
                            <span class="opacity-70 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-white leading-relaxed">${m.text}</p>
                    </div>
                `;
            } else {
                return `
                    <div class="mr-auto p-2.5 rounded-2xl bg-white border border-slate-200 text-slate-800 space-y-1 max-w-[88%] shadow-xs">
                        <div class="flex items-center justify-between text-[9px] font-bold text-rose-700">
                            <span>${m.sender || 'Mitra Penugasan'}</span>
                            <span class="text-slate-400 font-mono">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-slate-700 leading-relaxed">${m.text}</p>
                    </div>
                `;
            }
        }).join('');
    }

    function renderCustomerProposalCard() {
        const item = currentCustomerActiveComplaint;
        const card = document.getElementById('custProposalCard');
        if (!card || !item) return;

        // Tampilkan jika dalam mediasi atau ada usulan
        if (item.status === 'dalam_mediasi' || (item.mediation && item.mediation.proposal)) {
            card.classList.remove('hidden');

            const proposal = (item.mediation && item.mediation.proposal) ? item.mediation.proposal : {
                title: 'Usulan Penyelesaian Damai',
                terms: 'Mitra bersedia menyelesaikan pekerjaan dan mematuhi kesepakatan harga aplikasi tanpa penambahan biaya sepihak.',
                customerApproved: false,
                mitraApproved: false
            };

            document.getElementById('custProposalTermsText').innerText = proposal.terms;

            const custStatusEl = document.getElementById('custProposalCustStatus');
            const mitraStatusEl = document.getElementById('custProposalMitraStatus');
            const badgeEl = document.getElementById('custProposalBadge');
            const btnWrap = document.getElementById('custProposalActionButtons');
            const notice = document.getElementById('custProposalAgreedNotice');

            if (proposal.customerApproved) {
                custStatusEl.innerText = 'Disetujui';
                custStatusEl.className = 'font-bold text-emerald-600';
                if (btnWrap) btnWrap.classList.add('hidden');
                if (notice) notice.classList.remove('hidden');
            } else {
                custStatusEl.innerText = 'Menunggu';
                custStatusEl.className = 'font-bold text-amber-600';
                if (btnWrap) btnWrap.classList.remove('hidden');
                if (notice) notice.classList.add('hidden');
            }

            if (proposal.mitraApproved) {
                mitraStatusEl.innerText = 'Disetujui';
                mitraStatusEl.className = 'font-bold text-emerald-600';
            } else {
                mitraStatusEl.innerText = 'Menunggu';
                mitraStatusEl.className = 'font-bold text-slate-500';
            }

            if (proposal.customerApproved && proposal.mitraApproved) {
                badgeEl.innerText = 'Kesepakatan Tercapai';
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
            } else {
                badgeEl.innerText = 'Menunggu Persetujuan';
                badgeEl.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-100 text-amber-800 border border-amber-300';
            }
        } else {
            card.classList.add('hidden');
        }
    }

    function sendCustomerMediationMessage() {
        const input = document.getElementById('custMediationInputText');
        const text = (input ? input.value : '').trim();
        if (!text || !currentCustomerActiveComplaint) return;

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getStoredComplaints();
        const found = list.find(c => c.id === currentCustomerActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            if (!found.mediation.messages) found.mediation.messages = [];
            found.mediation.messages.push({
                sender: getCustomerDisplayName(),
                role: 'customer',
                text: text,
                time: timeStr
            });
            saveStoredComplaints(list);
            currentCustomerActiveComplaint = found;
        }

        input.value = '';
        renderCustomerMediationMessages();

        const sc = document.getElementById('custMediationScrollArea');
        if (sc) sc.scrollTop = sc.scrollHeight;
    }
    window.sendCustomerMediationMessage = sendCustomerMediationMessage;

    function customerApproveProposal() {
        if (!currentCustomerActiveComplaint) return;
        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getStoredComplaints();
        const found = list.find(c => c.id === currentCustomerActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            if (!found.mediation.proposal) {
                found.mediation.proposal = {
                    title: 'Usulan Kesepakatan Damai',
                    terms: 'Pekerjaan diselesaikan sesuai rincian awal tanpa pemungutan biaya tambahan sepihak.',
                    customerApproved: false,
                    mitraApproved: false
                };
            }
            found.mediation.proposal.customerApproved = true;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Customer Menyetujui Usulan Mufakat',
                time: timeStr,
                desc: 'Customer telah menyetujui poin usulan penyelesaian damai yang difasilitasi Admin.'
            });

            // Tambahkan pesan otomatis ke chat mediasi
            found.mediation.messages = found.mediation.messages || [];
            found.mediation.messages.push({
                sender: getCustomerDisplayName(),
                role: 'customer',
                text: 'Saya telah membaca dan menyetujui poin usulan kesepakatan damai ini.',
                time: now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            });

            saveStoredComplaints(list);
            currentCustomerActiveComplaint = found;
        }

        renderCustomerProposalCard();
        renderCustomerMediationMessages();
        renderCustomerComplaints();

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Usulan Disetujui!', 'Persetujuan Anda telah dicatat dalam mediasi.', 'success');
        } else {
            alert('Terima kasih, persetujuan Anda telah dicatat dalam ruang mediasi.');
        }
    }
    window.customerApproveProposal = customerApproveProposal;

    function customerRequestProposalChange() {
        const note = prompt('Tuliskan poin usulan yang ingin disesuaikan atau diajukan:');
        if (!note || !currentCustomerActiveComplaint) return;

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getStoredComplaints();
        const found = list.find(c => c.id === currentCustomerActiveComplaint.id);
        if (found) {
            if (!found.mediation) found.mediation = {};
            found.mediation.messages = found.mediation.messages || [];
            found.mediation.messages.push({
                sender: getCustomerDisplayName(),
                role: 'customer',
                text: `[Permintaan Penyesuaian Usulan]: "${note}"`,
                time: timeStr
            });
            saveStoredComplaints(list);
            currentCustomerActiveComplaint = found;
        }

        renderCustomerMediationMessages();
        const sc = document.getElementById('custMediationScrollArea');
        if (sc) sc.scrollTop = sc.scrollHeight;
    }
    window.customerRequestProposalChange = customerRequestProposalChange;

    // =========================================================
    // UPLOAD BUKTI TAMBAHAN CUSTOMER
    // =========================================================
    function openUploadBuktiCustomerModal() {
        if (!currentCustomerActiveComplaint) return;
        const item = currentCustomerActiveComplaint;

        const instText = item.evidenceRequest?.instructions || 'Harap sertakan foto atau tangkapan layar pendukung laporan.';
        const deadText = item.evidenceRequest?.deadline || '24 jam kerja';

        document.getElementById('custReqEvidenceInstructionsText').innerText = instText;
        document.getElementById('custReqEvidenceDeadlineText').innerText = `Batas Waktu: ${deadText}`;

        document.getElementById('formUploadBuktiCustomer').reset();
        document.getElementById('phBuktiTambahan').classList.remove('hidden');
        document.getElementById('actBuktiTambahan').classList.add('hidden');

        const modal = document.getElementById('modalUploadBuktiCustomer');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    window.openUploadBuktiCustomerModal = openUploadBuktiCustomerModal;

    function closeUploadBuktiCustomerModal() {
        const modal = document.getElementById('modalUploadBuktiCustomer');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    window.closeUploadBuktiCustomerModal = closeUploadBuktiCustomerModal;

    function previewBuktiTambahan(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('nameBuktiTambahan').innerText = file.name;
            document.getElementById('phBuktiTambahan').classList.add('hidden');
            document.getElementById('actBuktiTambahan').classList.remove('hidden');
            document.getElementById('actBuktiTambahan').classList.add('flex');
        }
    }
    window.previewBuktiTambahan = previewBuktiTambahan;

    function submitUploadBuktiCustomer(e) {
        e.preventDefault();
        if (!currentCustomerActiveComplaint) return;

        const fileInput = document.getElementById('inputBuktiTambahanFile');
        const fileName = (fileInput.files && fileInput.files[0]) ? fileInput.files[0].name : 'bukti_tambahan_customer.jpg';
        const notes = document.getElementById('inputCatatanBuktiTambahan').value.trim();

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const list = getStoredComplaints();
        const found = list.find(c => c.id === currentCustomerActiveComplaint.id);
        if (found) {
            found.status = 'bukti_dikirim';
            found.evidence = `${found.evidence || ''} | Tambahan: ${fileName} ("${notes}")`;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Bukti Tambahan Diunggah oleh Customer',
                time: timeStr,
                desc: `Customer mengunggah berkas [${fileName}]. Catatan: "${notes}"`
            });
            saveStoredComplaints(list);
            currentCustomerActiveComplaint = found;
        }

        closeUploadBuktiCustomerModal();
        openDetailPengaduanCustomer(currentCustomerActiveComplaint);
        renderCustomerComplaints();

        if (typeof showCustomerToast === 'function') {
            showCustomerToast('Bukti Berhasil Dikirim', 'Admin akan memverifikasi bukti yang Anda sertakan.', 'success');
        } else {
            alert('Bukti berhasil dikirimkan ke Admin SayaBantu!');
        }
    }
    window.submitUploadBuktiCustomer = submitUploadBuktiCustomer;

    // Inisialisasi Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderCustomerComplaints();
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Pusat Pengaduan - SayaBantu.com')
@section('page-title', 'Pengaduan')
@section('page-subtitle', 'Pusat penanganan laporan masalah Customer & Mitra secara objektif dan akuntabel')

@section('hero')
<div class="px-5 pt-1 pb-0">
    {{-- STAT RINGKASAN PENGADUAN --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                Status Tiket Laporan Masalah
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">
                Mediasi Terpusat
            </span>
        </div>

        <div class="grid grid-cols-5 gap-1.5 text-center" id="complaintStatFilters">
            <button type="button" class="complaint-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-sky-400 shadow-md shadow-sky-500/25 cursor-pointer transition-all scale-[1.03] text-center focus:outline-none ring-2 ring-sky-400/30" onclick="filterComplaints('all')" data-filter="all">
                <div class="text-xs font-black text-white" id="statAdminTotal">0</div>
                <div class="text-[7.5px] text-white font-bold mt-0.5">Total</div>
            </button>
            <button type="button" class="complaint-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterComplaints('baru')" data-filter="baru">
                <div class="text-xs font-black text-rose-400" id="statAdminBaru">0</div>
                <div class="text-[7.5px] text-gray-300 mt-0.5">Baru</div>
            </button>
            <button type="button" class="complaint-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterComplaints('proses')" data-filter="proses">
                <div class="text-xs font-black text-amber-400" id="statAdminProses">0</div>
                <div class="text-[7.5px] text-gray-300 mt-0.5">Bukti/Info</div>
            </button>
            <button type="button" class="complaint-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterComplaints('dalam_mediasi')" data-filter="dalam_mediasi">
                <div class="text-xs font-black text-teal-400" id="statAdminMediasi">0</div>
                <div class="text-[7.5px] text-gray-300 mt-0.5">Mediasi</div>
            </button>
            <button type="button" class="complaint-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterComplaints('selesai')" data-filter="selesai">
                <div class="text-xs font-black text-emerald-400" id="statAdminSelesai">0</div>
                <div class="text-[7.5px] text-gray-300 mt-0.5">Selesai</div>
            </button>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="mt-3">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input
                type="text"
                id="searchComplaintInput"
                placeholder="Cari ID pengaduan, nama pelapor, isu..."
                onkeyup="searchComplaints(this.value)"
                class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/15 focus:outline-none focus:border-sky-400 focus:bg-white/15 transition-all"
            >
            <button
                type="button"
                id="clearSearchComplaintBtn"
                onclick="clearSearchComplaints()"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs hidden"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-3" style="margin-top: 0.6cm;">

    <span id="displayedComplaintsCount" class="hidden">0</span>

    <div id="complaintList" class="space-y-3">
        <div id="emptyAdminComplaints" class="text-center py-12 px-4 rounded-[22px] bg-white border border-gray-100 shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-3 text-2xl border border-gray-100">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800">Belum Ada Pengaduan Masuk</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                Semua pengaduan atau sengketa dari Customer dan Mitra akan otomatis tercatat di sini secara real-time.
            </p>
        </div>
    </div>

</div>
@endsection

@section('modals')
{{-- =========================================================================
     1. MODAL DETAIL INVESTIGASI PENGADUAN (BOTTOM SHEET)
========================================================================= --}}
<div id="complaintDetailModal" class="sb-overlay-backdrop fixed inset-0 z-[200] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    {{-- Backdrop Hitam Semi-Transparan --}}
    <div class="sb-backdrop-click absolute inset-0 bg-black/75 backdrop-blur-[3px]" onclick="closeComplaintModal()"></div>

    {{-- Frame Mobile Bottom Sheet --}}
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="complaintDetailPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[90vh] border-t border-white/15 translate-y-full transition-transform duration-200 overflow-hidden">
            
            {{-- HEADER MODAL (STICKY / FIXED) --}}
            <div class="px-5 pt-3.5 pb-3 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600/30 to-rose-500/10 text-rose-400 flex items-center justify-center text-base border border-rose-500/30 shadow-inner">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white tracking-tight" id="modalComplaintId">#ADU-100</h3>
                                <span id="modalStatusBadge" class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Pemeriksaan
                                </span>
                            </div>
                            <p class="text-[10.5px] text-gray-300 font-medium mt-0.5 truncate max-w-[220px]" id="modalComplaintCategory">Kategori Laporan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeComplaintModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer" aria-label="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- KONTEN INVESTIGASI (SCROLLABLE DENGAN CUSTOM SCROLLBAR) --}}
            <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3.5 text-xs custom-scrollbar">

                {{-- STATUS BANNER PROAKTIF --}}
                <div id="modalStatusBanner" class="p-3 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-sky-400 text-sm mt-0.5 shrink-0" id="modalStatusBannerIcon"></i>
                    <div>
                        <h4 class="text-[11px] font-bold text-white" id="modalStatusBannerTitle">Tahap Pemeriksaan Awal</h4>
                        <p class="text-[10px] text-gray-300 mt-0.5 leading-relaxed" id="modalStatusBannerDesc">
                            Admin sedang memeriksa kronologi, identitas pelapor, terlapor, dan bukti yang tersedia. Gunakan tombol aksi di bawah untuk memproses laporan.
                        </p>
                    </div>
                </div>

                {{-- KARTU PERMINTAAN BUKTI DARI ADMIN (MUNCUL JIKA STATUS MENUNGGU BUKTI) --}}
                <div id="modalActiveEvidenceCard" class="hidden p-3.5 rounded-2xl bg-amber-500/10 border border-amber-400/30 space-y-2 shadow-xs">
                    <div class="flex items-center justify-between pb-1.5 border-b border-amber-400/20">
                        <span class="text-[10px] font-bold text-amber-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-circle-question"></i>
                            PERMINTAAN BUKTI DARI ADMIN
                        </span>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-200 border border-amber-400/30">
                            Menunggu Bukti
                        </span>
                    </div>
                    <div class="space-y-1 text-[10.5px]">
                        <div>
                            <span class="text-gray-400 text-[9px] block">BUKTI YANG DIMINTA:</span>
                            <strong class="text-white" id="modalReqEvidenceTypeLabel">Foto kondisi pekerjaan & dokumen pendukung</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 text-[9px] block">KETERANGAN ADMIN:</span>
                            <p class="text-amber-100 bg-black/25 p-2 rounded-xl text-[10px] leading-relaxed" id="modalReqEvidenceDescLabel">
                                Harap unggah foto kondisi partisi meja setelah pekerjaan dilakukan dan sertakan foto kondisi awal jika tersedia.
                            </p>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-[9.5px]">
                            <span class="text-gray-400">Batas Waktu Pengumpulan:</span>
                            <strong class="text-rose-300 font-bold" id="modalReqEvidenceDeadlineLabel">26 Oktober 2026</strong>
                        </div>
                    </div>
                </div>

                {{-- KARTU RUANG MEDIASI AKTIF (MUNCUL JIKA STATUS DALAM MEDIASI) --}}
                <div id="modalActiveMediationCard" class="hidden p-3.5 rounded-2xl bg-teal-500/10 border border-teal-400/30 space-y-2.5 shadow-xs">
                    <div class="flex items-center justify-between pb-1.5 border-b border-teal-400/20">
                        <span class="text-[10px] font-bold text-teal-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-comments"></i>
                            RUANG MEDIASI BERLANGSUNG
                        </span>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-teal-400/20 text-teal-200 border border-teal-400/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span> Sesi Aktif
                        </span>
                    </div>
                    <p class="text-[10.5px] text-teal-100 leading-relaxed">
                        Mediasi 3-pihak (Customer, Mitra, & Admin Fasilitator) sedang berlangsung. Anda dapat mengarahkan diskusi dan memantau usulan kesepakatan secara langsung.
                    </p>
                    <button type="button" onclick="window.openMediationRoomModal()" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-teal-950/40 transition-all cursor-pointer">
                        <i class="fa-solid fa-door-open"></i>
                        <span>Buka Ruang Mediasi Khusus</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>

                {{-- INFORMASI PIHAK TERLIBAT & SURUHAN --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-2.5 shadow-xs">
                    <div class="grid grid-cols-2 gap-2 pb-2.5 border-b border-white/5">
                        <div>
                            <span class="text-[9px] font-semibold text-gray-400 tracking-wider block">PELAPOR</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <i class="fa-solid fa-user text-[10px] text-gray-400"></i>
                                <strong class="text-white text-xs truncate" id="modalReporter">-</strong>
                            </div>
                        </div>
                        <div>
                            <span class="text-[9px] font-semibold text-gray-400 tracking-wider block">PIHAK TERLAPOR</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <i class="fa-solid fa-helmet-safety text-[10px] text-sky-400"></i>
                                <strong class="text-sky-300 text-xs truncate" id="modalReported">-</strong>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[10px]">
                        <div>
                            <span class="text-gray-400 block text-[8.5px] font-medium">SURUHAN TERKAIT</span>
                            <span class="text-white font-mono font-bold text-[11px]" id="modalSuruhanId">-</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-400 block text-[8.5px] font-medium">WAKTU LAPORAN</span>
                            <span class="text-gray-300 text-[10px]" id="modalReportDate">-</span>
                        </div>
                    </div>
                </div>

                {{-- KRONOLOGI PELAPOR --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-1.5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[9.5px] font-bold text-rose-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-bullhorn text-[10px]"></i>
                            KRONOLOGI PERMASALAHAN
                        </span>
                    </div>
                    <p class="text-gray-200 text-[11px] leading-relaxed bg-black/20 p-2.5 rounded-xl border border-white/5" id="modalChronology">-</p>
                </div>

                {{-- BUKTI PENDUKUNG --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-2 shadow-xs">
                    <span class="text-[9.5px] font-bold text-sky-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-paperclip text-[10px]"></i>
                        BUKTI PENDUKUNG
                    </span>
                    <p class="text-gray-200 text-[11px] leading-relaxed" id="modalEvidence">-</p>
                    <div class="pt-1 flex items-center gap-2">
                        <a href="{{ route('admin.chat') }}" class="px-3 py-1.5 rounded-xl bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 text-[10.5px] font-bold border border-sky-400/30 flex items-center gap-1.5 transition-colors shadow-xs">
                            <i class="fa-solid fa-comments text-xs"></i>
                            <span>Buka Monitoring Chat Pesanan</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[8.5px] opacity-70"></i>
                        </a>
                    </div>
                </div>

                {{-- TANGGAPAN PIHAK TERLAPOR --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-1.5 shadow-xs">
                    <span class="text-[9.5px] font-bold text-amber-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-reply-all text-[10px]"></i>
                        TANGGAPAN PIHAK TERKAIT
                    </span>
                    <p class="text-gray-200 text-[11px] leading-relaxed bg-black/20 p-2.5 rounded-xl border border-white/5" id="modalResponse">-</p>
                </div>

                {{-- CATATAN INVESTIGASI INTERNAL ADMIN --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-bold text-gray-200">
                            Catatan Investigasi Internal Admin
                        </label>
                        <span class="text-[8.5px] text-gray-400">(Tersimpan privat di sistem admin)</span>
                    </div>

                    {{-- CHIP REKOMENDASI CEPAT --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[9.5px] custom-scrollbar">
                        <button type="button" onclick="setQuickComplaintNote('Terjadi perbedaan pemahaman cakupan kerja awal. Disarankan klarifikasi rincian SPK melalui mediasi.', this)" class="chip-quick-note px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 whitespace-nowrap active:scale-95 transition-all cursor-pointer">
                            ⚖️ Klarifikasi SPK
                        </button>
                        <button type="button" onclick="setQuickComplaintNote('Bukti awal belum memadai. Diperlukan foto kondisi fisik sebelum dan sesudah pekerjaan.', this)" class="chip-quick-note px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 whitespace-nowrap active:scale-95 transition-all cursor-pointer">
                            📷 Butuh Bukti Tambahan
                        </button>
                        <button type="button" onclick="setQuickComplaintNote('Kedua belah pihak telah menyepakati penyelesaian tanpa denda sanksi tambahan.', this)" class="chip-quick-note px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 whitespace-nowrap active:scale-95 transition-all cursor-pointer">
                            🤝 Mufakat Damai
                        </button>
                        <button type="button" onclick="setQuickComplaintNote('Laporan tidak memenuhi ketentuan SayaBantu.com dan tidak disertai dasar bukti yang sah.', this)" class="chip-quick-note px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 whitespace-nowrap active:scale-95 transition-all cursor-pointer">
                            ❌ Tidak Memenuhi Syarat
                        </button>
                    </div>

                    <textarea id="adminComplaintNote" rows="2" placeholder="Catatan internal pemeriksaan admin..." class="w-full bg-white/5 border border-white/15 focus:border-sky-400 focus:bg-white/[0.08] rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-sky-400/40 transition-all"></textarea>
                </div>

                {{-- RIWAYAT TINDAKAN & TIMELINE --}}
                <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                    <span class="text-[9.5px] font-bold text-gray-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-timeline text-[10px]"></i>
                        RIWAYAT PENANGANAN PENGADUAN
                    </span>
                    <div id="modalTimelineList" class="space-y-2 text-[10px] text-gray-300">
                        {{-- Diisi secara dinamis --}}
                    </div>
                </div>

            </div>

            {{-- FOOTER AKSI PENYELESAIAN (3 TOMBOL UTAMA MENURUT SOP RESMI) --}}
            <div class="shrink-0 px-5 pt-3 pb-6 sm:pb-4 border-t border-white/10 bg-[#0B1728]/95 backdrop-blur-md">
                <div id="modalActionFooter">
                    {{-- Di-render secara dinamis oleh renderModalActionFooter() berdasarkan status tiket --}}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     2. FORM MULAI MEDIASI (#startMediationModal)
========================================================================= --}}
<div id="startMediationModal" class="sb-overlay-backdrop fixed inset-0 z-[210] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/80 backdrop-blur-[4px]" onclick="closeStartMediationModal()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="startMediationPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[88vh] border-t border-teal-500/30 translate-y-full transition-transform duration-200 overflow-hidden">
            
            {{-- HEADER FORM MULAI MEDIASI --}}
            <div class="px-5 pt-3.5 pb-3 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center text-sm border border-teal-500/30">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight">Form Mulai Mediasi</h3>
                            <p class="text-[10px] text-teal-300 font-medium">Fasilitasi Penyelesaian Konflik 3-Pihak</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeStartMediationModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- BODY FORM MULAI MEDIASI --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 text-xs custom-scrollbar">

                {{-- INFORMASI PENGADUAN --}}
                <div class="p-3 rounded-2xl bg-white/[0.04] border border-white/10 space-y-2">
                    <div class="text-[9px] font-bold text-teal-300 tracking-wider uppercase">Informasi Pengaduan</div>
                    <div class="grid grid-cols-2 gap-2 text-[10.5px]">
                        <div>
                            <span class="text-gray-400 block text-[8.5px]">NOMOR PENGADUAN</span>
                            <strong class="text-white font-mono" id="startMediationTicketId">#ADU-100</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[8.5px]">PERMASALAHAN</span>
                            <span class="text-white truncate block font-medium" id="startMediationProblem">Perbedaan Pendapat Biaya</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[8.5px]">PELAPOR</span>
                            <strong class="text-gray-200" id="startMediationReporter">Budi Santoso (Mitra)</strong>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[8.5px]">PIHAK TERLAPOR</span>
                            <strong class="text-sky-300" id="startMediationReported">Riko Sanjaya (Customer)</strong>
                        </div>
                    </div>
                </div>

                {{-- RINGKASAN MASALAH (NETRAL) --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Ringkasan Masalah <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="startMediationSummary" rows="3" class="w-full bg-white/5 border border-white/15 focus:border-teal-400 focus:bg-white/[0.08] rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-teal-400/40 transition-all leading-relaxed" placeholder="Tuliskan ringkasan permasalahan secara netral berdasarkan kronologi dan bukti yang telah diperiksa..."></textarea>
                    <p class="text-[9.5px] text-gray-400 leading-normal">
                        Admin mengisi ringkasan permasalahan secara objektif. Teks ini akan dibaca oleh Customer dan Mitra di Ruang Mediasi.
                    </p>
                </div>

                {{-- CATATAN ATAU ARAHAN AWAL ADMIN --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Catatan atau Arahan Awal Admin <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="startMediationDirections" rows="3" class="w-full bg-white/5 border border-white/15 focus:border-teal-400 focus:bg-white/[0.08] rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-teal-400/40 transition-all leading-relaxed" placeholder="Arahan kepada kedua pihak mengenai hal-hal yang perlu diklarifikasi selama mediasi..."></textarea>
                    <p class="text-[9.5px] text-gray-400 leading-normal">
                        Memberikan arahan netral agar kedua belah pihak menyampaikan argumen secara jelas dan mempertimbangkan jalan tengah yang adil.
                    </p>
                </div>

                {{-- PERINGATAN NETRALITAS --}}
                <div class="p-3 rounded-xl bg-teal-500/10 border border-teal-500/20 text-[10px] text-teal-200 flex items-start gap-2">
                    <i class="fa-solid fa-shield-halved text-teal-400 mt-0.5 shrink-0"></i>
                    <span>Ringkasan masalah dan arahan awal disusun secara netral. Catatan investigasi internal Admin tidak otomatis ditampilkan kepada Customer dan Mitra.</span>
                </div>

            </div>

            {{-- FOOTER FORM MULAI MEDIASI --}}
            <div class="shrink-0 px-5 pt-3 pb-6 sm:pb-4 border-t border-white/10 bg-[#0B1728]/95 backdrop-blur-md">
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" onclick="closeStartMediationModal()" class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/15 text-gray-300 font-bold text-xs transition-all cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" onclick="submitStartMediation()" class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-teal-950/50 transition-all cursor-pointer">
                        <i class="fa-solid fa-handshake"></i>
                        <span>Mulai Mediasi</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     3. RUANG MEDIASI KHUSUS (#mediationRoomModal)
========================================================================= --}}
<div id="mediationRoomModal" class="sb-overlay-backdrop fixed inset-0 z-[220] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/85 backdrop-blur-[4px]" onclick="closeMediationRoomModal()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="mediationRoomPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col h-[92vh] border-t border-teal-500/40 translate-y-full transition-transform duration-200 overflow-hidden">
            
            {{-- HEADER RUANG MEDIASI --}}
            <div class="px-5 pt-3 pb-2.5 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-2.5"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-700 text-white flex items-center justify-center text-sm shadow-md">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white tracking-tight" id="roomHeaderTitle">Ruang Mediasi #ADU-100</h3>
                                <span class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span> Dalam Mediasi
                                </span>
                            </div>
                            <p class="text-[9.5px] text-gray-300 truncate" id="roomHeaderSub">Customer: Riko Sanjaya · Mitra: Budi Santoso</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeMediationRoomModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                {{-- PARTICIPANTS STATUS BAR --}}
                <div class="mt-2.5 py-1.5 px-2.5 rounded-xl bg-white/[0.04] border border-white/5 flex items-center justify-between text-[9px] text-gray-300">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Customer: <strong class="text-white" id="roomCustNameBadge">Riko Sanjaya</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        <span>Mitra: <strong class="text-white" id="roomMitraNameBadge">Budi Santoso</strong></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-teal-300 text-[10px]"></i>
                        <span class="text-teal-300 font-bold">Admin</span>
                    </div>
                </div>
            </div>

            {{-- CHAT & MEDIATION FEED (SCROLLABLE) --}}
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-xs custom-scrollbar" id="mediationChatScrollArea">

                {{-- RINGKASAN MASALAH PINNED --}}
                <div class="p-3 rounded-2xl bg-teal-500/10 border border-teal-500/25 space-y-1">
                    <span class="text-[9px] font-bold text-teal-300 uppercase tracking-wider block">
                        <i class="fa-solid fa-bullhorn mr-1"></i> Ringkasan Masalah Mediasi
                    </span>
                    <p class="text-[10.5px] text-teal-100 leading-relaxed" id="roomPinnedSummary">
                        Perbedaan pemahaman antara Mitra dan Customer mengenai cakupan pekerjaan pembongkaran partisi dinding kaca dan biaya tambahan.
                    </p>
                </div>

                {{-- DAFTAR PESAN MEDIASI 3 PIHAK --}}
                <div class="space-y-2.5" id="mediationMessagesList">
                    {{-- Pesan 1: Admin --}}
                    <div class="p-2.5 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-white space-y-1 max-w-[92%]">
                        <div class="flex items-center justify-between text-[9px] font-bold text-teal-300">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> Admin SayaBantu (Fasilitator)
                            </span>
                            <span class="opacity-70">10:00 WIB</span>
                        </div>
                        <p class="text-[10.5px] text-gray-100 leading-relaxed">
                            Selamat pagi. Kami meminta kedua pihak menjelaskan kembali kesepakatan pekerjaan awal agar permasalahan ini dapat diselesaikan berdasarkan informasi dan bukti yang tersedia.
                        </p>
                    </div>

                    {{-- Pesan 2: Mitra --}}
                    <div class="p-2.5 rounded-2xl bg-sky-500/15 border border-sky-500/30 text-white space-y-1 max-w-[92%] ml-auto">
                        <div class="flex items-center justify-between text-[9px] font-bold text-sky-300">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-helmet-safety"></i> Budi Santoso (Mitra)
                            </span>
                            <span class="opacity-70">10:05 WIB</span>
                        </div>
                        <p class="text-[10.5px] text-gray-100 leading-relaxed">
                            Saya mengajukan biaya tambahan karena pekerjaan pembongkaran partisi tersebut menurut saya berada di luar kesepakatan awal angkut meja kantor.
                        </p>
                    </div>

                    {{-- Pesan 3: Customer --}}
                    <div class="p-2.5 rounded-2xl bg-purple-500/15 border border-purple-500/30 text-white space-y-1 max-w-[92%]">
                        <div class="flex items-center justify-between text-[9px] font-bold text-purple-300">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-user"></i> Riko Sanjaya (Customer)
                            </span>
                            <span class="opacity-70">10:08 WIB</span>
                        </div>
                        <p class="text-[10.5px] text-gray-100 leading-relaxed">
                            Menurut saya, pekerjaan tersebut masih termasuk dalam pekerjaan yang sebelumnya kami sepakati karena partisi meja tersebut menempel pada unit meja kerja.
                        </p>
                    </div>

                    {{-- Pesan 4: Admin --}}
                    <div class="p-2.5 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-white space-y-1 max-w-[92%]">
                        <div class="flex items-center justify-between text-[9px] font-bold text-teal-300">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> Admin SayaBantu (Fasilitator)
                            </span>
                            <span class="opacity-70">10:12 WIB</span>
                        </div>
                        <p class="text-[10.5px] text-gray-100 leading-relaxed">
                            Baik, kami akan memeriksa kembali kesepakatan awal dan meminta kedua pihak mempertimbangkan usulan penyelesaian yang dapat diterima bersama di bawah ini.
                        </p>
                    </div>
                </div>

                {{-- KARTU KHUSUS: AJUKAN USULAN KESEPAKATAN --}}
                <div id="proposalCardContainer" class="p-3.5 rounded-2xl bg-gradient-to-br from-amber-500/10 to-teal-500/10 border-2 border-teal-400/40 space-y-3 shadow-md">
                    <div class="flex items-center justify-between pb-2 border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-teal-400/20 text-teal-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Usulan Penyelesaian</h4>
                                <span class="text-[9px] text-gray-300">Diajukan oleh: <strong class="text-sky-300" id="proposalAuthorName">Budi Santoso (Mitra)</strong></span>
                            </div>
                        </div>
                        <span id="proposalGlobalBadge" class="px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Menunggu Persetujuan
                        </span>
                    </div>

                    {{-- ISI USULAN --}}
                    <div class="p-2.5 rounded-xl bg-black/25 border border-white/5 space-y-1.5 text-[10.5px]">
                        <div class="flex items-start gap-2 text-gray-200">
                            <i class="fa-solid fa-circle-check text-teal-400 text-[10px] mt-1 shrink-0"></i>
                            <span>Pekerjaan diselesaikan sesuai kesepakatan awal.</span>
                        </div>
                        <div class="flex items-start gap-2 text-gray-200">
                            <i class="fa-solid fa-circle-check text-teal-400 text-[10px] mt-1 shrink-0"></i>
                            <span>Tidak ada biaya tambahan untuk pekerjaan yang dipermasalahkan.</span>
                        </div>
                        <div class="flex items-start gap-2 text-gray-200">
                            <i class="fa-solid fa-circle-check text-teal-400 text-[10px] mt-1 shrink-0"></i>
                            <span>Pekerjaan tambahan di luar kesepakatan awal tidak termasuk dalam penyelesaian ini.</span>
                        </div>
                    </div>

                    {{-- STATUS PERSETUJUAN KEDUA BELAH PIHAK --}}
                    <div class="grid grid-cols-2 gap-2 text-[10px]">
                        <div class="p-2 rounded-xl bg-white/5 border border-white/5">
                            <span class="text-gray-400 text-[8.5px] block">PERSETUJUAN CUSTOMER:</span>
                            <div class="flex items-center gap-1.5 mt-0.5" id="customerApprovalStatusWrap">
                                <span class="w-2 h-2 rounded-full bg-amber-400" id="custStatusDot"></span>
                                <strong class="text-amber-300" id="custStatusText">Menunggu</strong>
                            </div>
                        </div>
                        <div class="p-2 rounded-xl bg-white/5 border border-white/5">
                            <span class="text-gray-400 text-[8.5px] block">PERSETUJUAN MITRA:</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <strong class="text-emerald-300" id="mitraStatusText">Diajukan</strong>
                            </div>
                        </div>
                    </div>

                    {{-- NOTIFIKASI KESEPAKATAN TERCAPAI --}}
                    <div id="agreementSuccessNotice" class="hidden p-2.5 rounded-xl bg-emerald-500/20 border border-emerald-400/40 text-[10px] text-emerald-200 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-sm shrink-0"></i>
                        <span><strong>Kesepakatan Tercapai!</strong> Kedua pihak telah menyetujui usulan penyelesaian. Admin dapat menyelesaikan mediasi.</span>
                    </div>

                    {{-- TOMBOL TINDAKAN TERHADAP USULAN --}}
                    <div class="flex items-center gap-2 pt-1" id="proposalActionButtons">
                    </div>
                </div>

            </div>

            {{-- CHAT INPUT UNTUK ADMIN --}}
            <div class="px-4 py-2.5 border-t border-white/10 bg-[#0E1D31]/95 shrink-0">
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        id="adminMediationInput"
                        placeholder="Ketik arahan atau klarifikasi Admin..."
                        onkeydown="if(event.key==='Enter') sendMediationChatMessage()"
                        class="flex-1 bg-white/10 border border-white/15 focus:border-teal-400 focus:bg-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-400 focus:outline-none transition-all"
                    >
                    <button type="button" onclick="sendMediationChatMessage()" class="w-9 h-9 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white flex items-center justify-center text-xs transition-all shadow-md cursor-pointer shrink-0">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            {{-- FOOTER AKHIR MEDIASI (SELESAIKAN MEDIASI VS TIDAK TERCAPAI KESEPAKATAN) --}}
            <div class="shrink-0 px-4 pt-2.5 pb-5 border-t border-white/10 bg-[#0B1728]">
                <div class="grid grid-cols-2 gap-2">
                    {{-- TIDAK TERCAPAI KESEPAKATAN --}}
                    <button type="button" onclick="submitFailMediation()" class="py-2.5 px-2 rounded-xl bg-gradient-to-r from-amber-700/80 to-rose-700/80 hover:from-amber-600 hover:to-rose-600 active:scale-95 text-white font-bold text-[10.5px] flex items-center justify-center gap-1.5 border border-amber-400/30 transition-all cursor-pointer shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-amber-200"></i>
                        <span>Tidak Tercapai Kesepakatan</span>
                    </button>

                    {{-- SELESAIKAN MEDIASI --}}
                    <button type="button" id="btnResolveMediation" onclick="submitResolveMediation()" class="py-2.5 px-2 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 active:scale-95 text-white font-bold text-[10.5px] flex items-center justify-center gap-1.5 border border-emerald-400/40 shadow-md shadow-emerald-950/40 transition-all cursor-pointer">
                        <i class="fa-solid fa-circle-check text-emerald-200"></i>
                        <span>Selesaikan Mediasi</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     4. FORM PERMINTAAN BUKTI (#requestEvidenceModal)
========================================================================= --}}
<div id="requestEvidenceModal" class="sb-overlay-backdrop fixed inset-0 z-[210] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/80 backdrop-blur-[4px]" onclick="closeRequestEvidenceModal()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="requestEvidencePanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[88vh] border-t border-amber-500/40 translate-y-full transition-transform duration-200 overflow-hidden">
            
            {{-- HEADER FORM PERMINTAAN BUKTI --}}
            <div class="px-5 pt-3.5 pb-3 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-sm border border-amber-500/30">
                            <i class="fa-solid fa-file-circle-question"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight">Form Permintaan Bukti</h3>
                            <p class="text-[10px] text-amber-300 font-medium" id="reqEvidenceModalSub">Pengaduan #ADU-100</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeRequestEvidenceModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- BODY FORM PERMINTAAN BUKTI --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5 text-xs custom-scrollbar">

                {{-- PIHAK YANG DIMINTA MEMBERIKAN BUKTI --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Pihak yang Diminta Memberikan Bukti <span class="text-rose-400">*</span>
                    </label>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="reqEvidenceTarget" value="customer" class="text-amber-500 focus:ring-0">
                            <div>
                                <span class="text-white font-semibold text-xs block">Customer</span>
                                <span class="text-[9.5px] text-gray-400" id="reqEvidenceCustLabel">Riko Sanjaya</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="reqEvidenceTarget" value="mitra" class="text-amber-500 focus:ring-0">
                            <div>
                                <span class="text-white font-semibold text-xs block">Mitra</span>
                                <span class="text-[9.5px] text-gray-400" id="reqEvidenceMitraLabel">Budi Santoso</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="reqEvidenceTarget" value="keduanya" checked class="text-amber-500 focus:ring-0">
                            <div>
                                <span class="text-white font-semibold text-xs block">Kedua Pihak (Customer & Mitra)</span>
                                <span class="text-[9.5px] text-gray-400">Meminta bukti silang dari kedua belah pihak</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- JENIS BUKTI YANG DIMINTA --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Jenis Bukti yang Diminta <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2 text-[10.5px]">
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                            <input type="checkbox" name="reqEvidenceTypes" value="Foto kondisi pekerjaan" checked class="rounded text-amber-500 focus:ring-0">
                            <span class="text-gray-200">Foto kondisi pekerjaan</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                            <input type="checkbox" name="reqEvidenceTypes" value="Video pekerjaan" class="rounded text-amber-500 focus:ring-0">
                            <span class="text-gray-200">Video pekerjaan</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                            <input type="checkbox" name="reqEvidenceTypes" value="Dokumen atau bukti transaksi" class="rounded text-amber-500 focus:ring-0">
                            <span class="text-gray-200">Dokumen / transaksi</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                            <input type="checkbox" name="reqEvidenceTypes" value="Bukti percakapan" class="rounded text-amber-500 focus:ring-0">
                            <span class="text-gray-200">Bukti percakapan</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-white/5 border border-white/10 cursor-pointer col-span-2">
                            <input type="checkbox" name="reqEvidenceTypes" value="Bukti lainnya" class="rounded text-amber-500 focus:ring-0">
                            <span class="text-gray-200">Bukti lainnya (kuitansi / nota eksternal)</span>
                        </label>
                    </div>
                </div>

                {{-- KETERANGAN PERMINTAAN BUKTI --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Keterangan Permintaan Bukti <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="reqEvidenceInstructions" rows="3" class="w-full bg-white/5 border border-white/15 focus:border-amber-400 focus:bg-white/[0.08] rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-amber-400/40 transition-all leading-relaxed" placeholder="Jelaskan secara spesifik bukti yang harus diserahkan..."></textarea>
                    <p class="text-[9.5px] text-gray-400">Contoh: Harap unggah foto kondisi partisi meja setelah pekerjaan dilakukan dan sertakan foto kondisi awal jika tersedia.</p>
                </div>

                {{-- BATAS WAKTU PENGUMPULAN --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Batas Waktu Pengumpulan (Deadline) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="reqEvidenceDeadline" value="26 Oktober 2026" class="w-full pl-9 pr-3 py-2 rounded-xl bg-white/5 border border-white/15 focus:border-amber-400 text-xs text-white placeholder-gray-400 focus:outline-none transition-all">
                    </div>
                </div>

                {{-- LAMPIRAN CONTOH DARI ADMIN (OPSIONAL) --}}
                <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/10 text-[10px] space-y-1">
                    <span class="text-gray-400 block font-semibold text-[9px]">LAMPIRAN CONTOH DARI ADMIN (OPSIONAL):</span>
                    <div class="flex items-center gap-2 text-sky-300">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Contoh_Format_Foto_Kondisi_Pekerjaan.pdf (320 KB)</span>
                    </div>
                </div>

            </div>

            {{-- FOOTER FORM PERMINTAAN BUKTI --}}
            <div class="shrink-0 px-5 pt-3 pb-6 sm:pb-4 border-t border-white/10 bg-[#0B1728]/95 backdrop-blur-md">
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" onclick="closeRequestEvidenceModal()" class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/15 text-gray-300 font-bold text-xs transition-all cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" onclick="submitRequestEvidence()" class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-500 hover:to-yellow-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-amber-950/50 transition-all cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Permintaan Bukti</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     6. FORM TOLAK PENGADUAN (#rejectComplaintModal)
========================================================================= --}}
<div id="rejectComplaintModal" class="sb-overlay-backdrop fixed inset-0 z-[210] pointer-events-none opacity-0 invisible transition-all duration-200" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/80 backdrop-blur-[4px]" onclick="closeRejectComplaintModal()"></div>
    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="rejectComplaintPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[28px] shadow-2xl flex flex-col max-h-[88vh] border-t border-rose-500/40 translate-y-full transition-transform duration-200 overflow-hidden">
            
            {{-- HEADER TOLAK PENGADUAN --}}
            <div class="px-5 pt-3.5 pb-3 border-b border-white/10 shrink-0 bg-[#0E1D31]">
                <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center text-sm border border-rose-500/30">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight">Form Tolak Pengaduan</h3>
                            <p class="text-[10px] text-rose-300 font-medium" id="rejectComplaintModalSub">Pengaduan #ADU-100</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeRejectComplaintModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- BODY TOLAK PENGADUAN --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5 text-xs custom-scrollbar">

                {{-- PERTANYAAN KONFIRMASI --}}
                <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-[11px] text-rose-200 leading-relaxed font-semibold">
                    <i class="fa-solid fa-circle-question mr-1.5 text-rose-400"></i>
                    Apakah kamu yakin ingin menolak pengaduan ini?
                </div>

                {{-- ALASAN PENOLAKAN (RADIO OPTIONS) --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Alasan Penolakan <span class="text-rose-400">*</span>
                    </label>
                    <div class="space-y-1.5 text-[11px]">
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="rejectionReason" value="Tidak ditemukan pelanggaran berdasarkan pemeriksaan" checked onchange="updateRejectNotePlaceholder(this.value)" class="text-rose-500 focus:ring-0">
                            <span class="text-gray-200">Tidak ditemukan pelanggaran berdasarkan pemeriksaan</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="rejectionReason" value="Bukti tidak cukup untuk mendukung laporan setelah pemeriksaan" onchange="updateRejectNotePlaceholder(this.value)" class="text-rose-500 focus:ring-0">
                            <span class="text-gray-200">Bukti tidak cukup untuk mendukung laporan setelah pemeriksaan</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="rejectionReason" value="Laporan tidak sesuai dengan ketentuan SayaBantu.com" onchange="updateRejectNotePlaceholder(this.value)" class="text-rose-500 focus:ring-0">
                            <span class="text-gray-200">Laporan tidak sesuai dengan ketentuan SayaBantu.com</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="rejectionReason" value="Laporan duplikat" onchange="updateRejectNotePlaceholder(this.value)" class="text-rose-500 focus:ring-0">
                            <span class="text-gray-200">Laporan duplikat</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all">
                            <input type="radio" name="rejectionReason" value="Alasan lainnya" onchange="updateRejectNotePlaceholder(this.value)" class="text-rose-500 focus:ring-0">
                            <span class="text-gray-200">Alasan lainnya</span>
                        </label>
                    </div>
                </div>

                {{-- CATATAN ADMIN (WAJIB DIISI) --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-white">
                        Catatan Admin <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="rejectAdminNote" rows="3" class="w-full bg-white/5 border border-white/15 focus:border-rose-400 focus:bg-white/[0.08] rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-rose-400/40 transition-all leading-relaxed" placeholder="Catatan harus menjelaskan alasan keputusan dan dasar pemeriksaannya..."></textarea>
                    <p class="text-[9.5px] text-gray-400 leading-normal">
                        Catatan ini wajib diisi agar pelapor mendapatkan penjelasan yang memadai serta keputusan dapat dipertanggungjawabkan secara objektif.
                    </p>
                </div>

            </div>

            {{-- FOOTER FORM TOLAK PENGADUAN --}}
            <div class="shrink-0 px-5 pt-3 pb-6 sm:pb-4 border-t border-white/10 bg-[#0B1728]/95 backdrop-blur-md">
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" onclick="closeRejectComplaintModal()" class="py-2.5 px-3 rounded-xl bg-white/10 hover:bg-white/15 text-gray-300 font-bold text-xs transition-all cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" onclick="submitRejectComplaint()" class="py-2.5 px-3 rounded-xl bg-gradient-to-r from-rose-700 to-red-600 hover:from-rose-600 hover:to-red-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-rose-950/50 transition-all cursor-pointer">
                        <i class="fa-solid fa-ban"></i>
                        <span>Konfirmasi Penolakan</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeComplaintCard = null;
    let activeComplaintData = null;
    let isProposalCustomerApproved = false;

    // Ambil data pengaduan yang tersimpan di LocalStorage
    function getAdminStoredComplaints() {
        if (!localStorage.getItem('sb_cleared_dummy_complaints_v4')) {
            try {
                const current = JSON.parse(localStorage.getItem('sb_complaints') || '[]');
                const realOnly = current.filter(c => !['#ADU-102', '#ADU-101', '#ADU-100', '#ADU-098'].includes(c.id));
                localStorage.setItem('sb_complaints', JSON.stringify(realOnly));
                localStorage.setItem('sb_cleared_dummy_complaints_v4', 'true');
            } catch(e) {}
        }
        try {
            const raw = localStorage.getItem('sb_complaints');
            return raw ? JSON.parse(raw) : [];
        } catch(e) {
            return [];
        }
    }

    // Simpan data pengaduan ke LocalStorage
    function saveAdminStoredComplaints(complaints) {
        try {
            localStorage.setItem('sb_complaints', JSON.stringify(complaints));
        } catch(e) {}
    }

    // Helper Toast Universal
    function getAdminToast() {
        return (typeof window.showToast === 'function')
            ? window.showToast
            : (typeof showToast === 'function' ? showToast : function(t, m) { alert(`${t}: ${m}`); });
    }

    // Sinkronisasi data di DOM dengan LocalStorage
    function syncAdminComplaints() {
        const stored = getAdminStoredComplaints();
        const listContainer = document.getElementById('complaintList');
        if (!listContainer) return;

        listContainer.innerHTML = '';

        if (!stored || stored.length === 0) {
            listContainer.innerHTML = `
                <div id="emptyAdminComplaints" class="text-center py-12 px-4 rounded-[22px] bg-white border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-3 text-2xl border border-gray-100">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Belum Ada Pengaduan Masuk</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                        Semua pengaduan atau sengketa dari Customer dan Mitra akan otomatis tercatat di sini secara real-time.
                    </p>
                </div>
            `;
            updateAdminStatsAndTabs();
            return;
        }

        stored.forEach(item => {
            const newCard = document.createElement('div');
            newCard.className = `complaint-card p-4 rounded-[22px] bg-white border border-rose-200 shadow-sm space-y-3 transition-all hover:border-rose-400 cursor-pointer`;
            newCard.setAttribute('data-status', item.status || 'baru');
            newCard.setAttribute('data-id', item.id);
            newCard.setAttribute('data-order-id', item.orderId || '-');
            newCard.setAttribute('data-reporter', `${item.reporterName} (${item.reporterRole === 'mitra' ? 'Mitra' : 'Customer'})`);
            newCard.setAttribute('data-reported', item.reportedName || '-');
            newCard.setAttribute('data-category', item.category || 'Keluhan Layanan');
            newCard.setAttribute('data-date', item.date || 'Hari ini');
            newCard.setAttribute('data-summary', item.summary || item.chronology || '-');
            newCard.setAttribute('data-chronology', item.chronology || item.summary || '-');
            newCard.setAttribute('data-evidence', item.evidence || 'Lampiran foto / tangkapan layar');
            newCard.setAttribute('data-reported-response', item.response || 'Belum ada klarifikasi tanggapan');
            if (item.adminNote) newCard.setAttribute('data-admin-note', item.adminNote);
            newCard.onclick = function() { openComplaintModal(this); };

            newCard.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100">
                                ${item.id}
                            </span>
                            <h3 class="text-xs font-bold text-gray-900 truncate">${item.category || 'Keluhan Layanan'}</h3>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-1">
                            Suruhan: <strong class="text-gray-900">${item.orderId || '-'}</strong> • Pelapor: <strong class="text-gray-900">${item.reporterName}</strong>
                        </p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0 complaint-status-badge">
                        Baru Masuk
                    </span>
                </div>

                <p class="text-[10px] text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100 leading-relaxed">
                    ${item.summary || item.chronology || '-'}
                </p>

                <div class="grid grid-cols-2 gap-2 text-[10px]">
                    <div class="text-gray-500">
                        <span class="text-gray-400 block text-[8.5px]">TERLAPOR</span>
                        <strong class="text-gray-800 truncate block">${item.reportedName || '-'}</strong>
                    </div>
                    <div class="text-gray-500">
                        <span class="text-gray-400 block text-[8.5px]">KATEGORI</span>
                        <strong class="text-rose-600 truncate block">${item.category || 'Keluhan'}</strong>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                    <span class="text-[9px] text-gray-400">${item.date || 'Hari ini'}</span>
                    <span class="text-[10px] text-[#173B67] font-bold flex items-center gap-1">
                        <span>Periksa Kronologi</span>
                        <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    </span>
                </div>
            `;

            updateCardBadge(newCard, item.status || 'baru');
            listContainer.appendChild(newCard);
        });

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();
    }

    // Update Badge & Border Kartu di List
    function updateCardBadge(card, status) {
        const badge = card.querySelector('.complaint-status-badge');
        if (!badge) return;

        if (status === 'baru') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Baru Masuk';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-rose-200 shadow-sm space-y-3 transition-all hover:border-rose-400 cursor-pointer';
        } else if (status === 'proses') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Pemeriksaan';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-amber-200 shadow-sm space-y-3 transition-all hover:border-amber-400 cursor-pointer';
        } else if (status === 'menunggu_bukti') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-orange-50 text-orange-700 border border-orange-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Menunggu Bukti';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-orange-200 shadow-sm space-y-3 transition-all hover:border-orange-400 cursor-pointer';
        } else if (status === 'bukti_dikirim') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Bukti Diterima';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-sky-200 shadow-sm space-y-3 transition-all hover:border-sky-400 cursor-pointer';
        } else if (status === 'dalam_mediasi') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Dalam Mediasi';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border-2 border-teal-400 shadow-md shadow-teal-500/10 space-y-3 transition-all hover:border-teal-500 cursor-pointer';
        } else if (status === 'mediasi_tidak_tercapai' || status === 'menunggu_keputusan_admin') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Mediasi Tidak Tercapai';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-amber-300 shadow-sm space-y-3 transition-all hover:border-amber-400 cursor-pointer';
        } else if (status === 'selesai') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Selesai Dimediasi';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-emerald-200 shadow-sm space-y-3 transition-all hover:border-emerald-400 cursor-pointer';
        } else if (status === 'ditolak') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-700 border border-gray-200 shrink-0 complaint-status-badge';
            badge.innerText = 'Ditolak';
            card.className = 'complaint-card p-4 rounded-[22px] bg-white border border-gray-200 shadow-sm space-y-3 transition-all hover:border-gray-400 cursor-pointer opacity-80';
        }
    }

    let currentComplaintFilter = 'all';

    // Perhitungan Counter Tab Statistik
    function updateAdminStatsAndTabs() {
        const cards = document.querySelectorAll('.complaint-card');
        let total = cards.length;
        let baru = 0;
        let proses = 0;
        let mediasi = 0;
        let selesai = 0;

        cards.forEach(c => {
            const st = c.getAttribute('data-status');
            if (st === 'baru') baru++;
            else if (st === 'proses' || st === 'menunggu_bukti' || st === 'bukti_dikirim') proses++;
            else if (st === 'dalam_mediasi') mediasi++;
            else if (st === 'selesai' || st === 'ditolak' || st === 'mediasi_tidak_tercapai' || st === 'menunggu_keputusan_admin') selesai++;
        });

        const elTotal = document.getElementById('statAdminTotal');
        const elBaru = document.getElementById('statAdminBaru');
        const elProses = document.getElementById('statAdminProses');
        const elMediasi = document.getElementById('statAdminMediasi');
        const elSelesai = document.getElementById('statAdminSelesai');

        if (elTotal) elTotal.innerText = total;
        if (elBaru) elBaru.innerText = baru;
        if (elProses) elProses.innerText = proses;
        if (elMediasi) elMediasi.innerText = mediasi;
        if (elSelesai) elSelesai.innerText = selesai;

        const countEl = document.getElementById('displayedComplaintsCount');
        if (countEl) countEl.innerText = total;
    }

    // Filter Tab Status
    function filterComplaints(status) {
        currentComplaintFilter = status;

        document.querySelectorAll('.complaint-stat-tab').forEach(card => {
            const f = card.getAttribute('data-filter');
            card.classList.remove(
                'scale-[1.03]', 'shadow-md', 'shadow-lg', 'border-2', 'bg-white/[0.18]',
                'bg-rose-500/20', 'bg-amber-500/20', 'bg-teal-500/20', 'bg-emerald-500/20',
                'border-sky-400', 'border-rose-400', 'border-amber-400', 'border-teal-400', 'border-emerald-400',
                'shadow-sky-500/25', 'shadow-rose-500/25', 'shadow-amber-500/25', 'shadow-teal-500/25', 'shadow-emerald-500/25',
                'ring-2', 'ring-sky-400/40', 'ring-rose-400/40', 'ring-amber-400/40', 'ring-teal-400/40', 'ring-emerald-400/40',
                'opacity-100'
            );
            card.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (f === status) {
                card.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                card.classList.add('scale-[1.03]', 'shadow-lg', 'border-2', 'opacity-100');
                if (status === 'all') {
                    card.classList.add('bg-white/[0.18]', 'border-sky-400', 'shadow-sky-500/25', 'ring-2', 'ring-sky-400/40');
                } else if (status === 'baru') {
                    card.classList.add('bg-rose-500/20', 'border-rose-400', 'shadow-rose-500/25', 'ring-2', 'ring-rose-400/40');
                } else if (status === 'proses') {
                    card.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25', 'ring-2', 'ring-amber-400/40');
                } else if (status === 'dalam_mediasi') {
                    card.classList.add('bg-teal-500/20', 'border-teal-400', 'shadow-teal-500/25', 'ring-2', 'ring-teal-400/40');
                } else if (status === 'selesai') {
                    card.classList.add('bg-emerald-500/20', 'border-emerald-400', 'shadow-emerald-500/25', 'ring-2', 'ring-emerald-400/40');
                }
            }
        });

        applyComplaintsVisibility();
    }
    window.filterComplaints = filterComplaints;

    // Filter Visibility Berdasarkan Tab & Keyword
    function applyComplaintsVisibility() {
        const input = document.getElementById('searchComplaintInput');
        const keyword = input ? input.value.toLowerCase().trim() : '';

        const cards = document.querySelectorAll('.complaint-card');
        let count = 0;
        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            let matchesStatus = false;

            if (currentComplaintFilter === 'all') {
                matchesStatus = true;
            } else if (currentComplaintFilter === 'baru') {
                matchesStatus = (cardStatus === 'baru');
            } else if (currentComplaintFilter === 'proses') {
                matchesStatus = (cardStatus === 'proses' || cardStatus === 'menunggu_bukti' || cardStatus === 'bukti_dikirim');
            } else if (currentComplaintFilter === 'dalam_mediasi') {
                matchesStatus = cardStatus === 'dalam_mediasi';
            } else if (currentComplaintFilter === 'selesai') {
                matchesStatus = ['selesai', 'ditolak', 'mediasi_tidak_tercapai', 'menunggu_keputusan_admin'].includes(cardStatus);
            }

            const id = (card.getAttribute('data-id') || '').toLowerCase();
            const rep = (card.getAttribute('data-reporter') || '').toLowerCase();
            const repd = (card.getAttribute('data-reported') || '').toLowerCase();
            const sum = (card.getAttribute('data-summary') || '').toLowerCase();
            const cat = (card.getAttribute('data-category') || '').toLowerCase();

            const matchesSearch = (keyword === '' || id.includes(keyword) || rep.includes(keyword) || repd.includes(keyword) || sum.includes(keyword) || cat.includes(keyword));

            if (matchesStatus && matchesSearch) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        const countEl = document.getElementById('displayedComplaintsCount');
        if (countEl) countEl.innerText = count;
    }

    function searchComplaints(keyword) {
        const clearBtn = document.getElementById('clearSearchComplaintBtn');
        if (clearBtn) clearBtn.classList.toggle('hidden', (keyword || '').trim() === '');
        applyComplaintsVisibility();
    }
    window.searchComplaints = searchComplaints;

    function clearSearchComplaints() {
        const input = document.getElementById('searchComplaintInput');
        if (input) input.value = '';
        const clearBtn = document.getElementById('clearSearchComplaintBtn');
        if (clearBtn) clearBtn.classList.add('hidden');
        applyComplaintsVisibility();
    }
    window.clearSearchComplaints = clearSearchComplaints;

    // Buka Modal Detail Pengaduan
    function openComplaintModal(card) {
        activeComplaintCard = card;
        const id = card.getAttribute('data-id');
        const st = card.getAttribute('data-status') || 'baru';

        // Load record dari storage jika ada
        const stored = getAdminStoredComplaints();
        activeComplaintData = stored.find(s => s.id === id) || {
            id: id,
            orderId: card.getAttribute('data-order-id'),
            reporterName: card.getAttribute('data-reporter'),
            reportedName: card.getAttribute('data-reported'),
            category: card.getAttribute('data-category'),
            date: card.getAttribute('data-date'),
            summary: card.getAttribute('data-summary'),
            chronology: card.getAttribute('data-chronology'),
            evidence: card.getAttribute('data-evidence'),
            response: card.getAttribute('data-reported-response'),
            status: st,
            timeline: []
        };

        document.getElementById('modalComplaintId').innerText = activeComplaintData.id;
        document.getElementById('modalComplaintCategory').innerText = activeComplaintData.category;
        document.getElementById('modalReporter').innerText = activeComplaintData.reporterName;
        document.getElementById('modalReported').innerText = activeComplaintData.reportedName;
        document.getElementById('modalSuruhanId').innerText = activeComplaintData.orderId || '-';
        document.getElementById('modalReportDate').innerText = activeComplaintData.date || '-';
        document.getElementById('modalChronology').innerText = activeComplaintData.chronology || '-';
        document.getElementById('modalEvidence').innerText = activeComplaintData.evidence || '-';
        document.getElementById('modalResponse').innerText = activeComplaintData.response || '-';
        document.getElementById('adminComplaintNote').value = activeComplaintData.adminNote || '';

        // Tampilkan Banner dan Status Sesuai Tahap
        renderComplaintStatusUI(st);

        // Render Riwayat Timeline
        renderComplaintTimeline(activeComplaintData);

        // Render Tombol Aksi di Footer secara Dinamis
        renderModalActionFooter(st);

        // Tampilkan Modal
        const modal = document.getElementById('complaintDetailModal');
        const panel = document.getElementById('complaintDetailPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
            document.body.classList.add('sb-scroll-locked');
        }
    }
    window.openComplaintModal = openComplaintModal;

    // Render Banner Informasi Status Pengaduan
    function renderComplaintStatusUI(st) {
        const badge = document.getElementById('modalStatusBadge');
        const banner = document.getElementById('modalStatusBanner');
        const bannerIcon = document.getElementById('modalStatusBannerIcon');
        const bannerTitle = document.getElementById('modalStatusBannerTitle');
        const bannerDesc = document.getElementById('modalStatusBannerDesc');

        const activeEvidenceCard = document.getElementById('modalActiveEvidenceCard');
        const activeMediationCard = document.getElementById('modalActiveMediationCard');

        if (activeEvidenceCard) activeEvidenceCard.classList.add('hidden');
        if (activeMediationCard) activeMediationCard.classList.add('hidden');

        if (st === 'baru') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30';
            badge.innerText = 'Baru Masuk';
            bannerTitle.innerText = 'Laporan Baru Diterima';
            bannerDesc.innerText = 'Silakan periksa identitas pelapor, bukti lampiran, dan kronologi. Lakukan mediasi jika konflik membutuhkan fasilitasi, atau minta bukti tambahan.';
            bannerIcon.className = 'fa-solid fa-triangle-exclamation text-rose-400 text-sm mt-0.5 shrink-0';
        } else if (st === 'proses') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30';
            badge.innerText = 'Dalam Pemeriksaan';
            bannerTitle.innerText = 'Dalam Investigasi Admin';
            bannerDesc.innerText = 'Admin sedang meneliti kesepakatan awal dan bukti pekerjaan. Anda dapat memulai mediasi resmi atau meminta bukti tambahan kepada pihak terkait.';
            bannerIcon.className = 'fa-solid fa-magnifying-glass text-amber-400 text-sm mt-0.5 shrink-0';
        } else if (st === 'menunggu_bukti') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30';
            badge.innerText = 'Menunggu Bukti';
            bannerTitle.innerText = 'Menunggu Penyerahan Bukti Pihak Terkait';
            bannerDesc.innerText = 'Permintaan bukti tambahan telah dikirimkan ke pihak terkait. Sistem sedang menunggu unggahan bukti sebelum batas waktu.';
            bannerIcon.className = 'fa-solid fa-clock text-orange-400 text-sm mt-0.5 shrink-0';
            if (activeEvidenceCard) activeEvidenceCard.classList.remove('hidden');
        } else if (st === 'bukti_dikirim') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30';
            badge.innerText = 'Bukti Diterima';
            bannerTitle.innerText = 'Bukti Baru Telah Diterima!';
            bannerDesc.innerText = 'Pihak terkait telah mengunggah bukti tambahan yang diminta. Bukti siap diperiksa untuk memulai mediasi atau menentukan keputusan.';
            bannerIcon.className = 'fa-solid fa-cloud-arrow-up text-sky-400 text-sm mt-0.5 shrink-0';
        } else if (st === 'dalam_mediasi') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30';
            badge.innerText = 'Dalam Mediasi';
            bannerTitle.innerText = 'Sesi Mediasi Sedang Berlangsung';
            bannerDesc.innerText = 'Fasilitasi mediasi 3-pihak aktif di Ruang Mediasi Khusus. Pantau usulan kesepakatan dan tanggapan Customer serta Mitra.';
            bannerIcon.className = 'fa-solid fa-handshake text-teal-400 text-sm mt-0.5 shrink-0';
            if (activeMediationCard) activeMediationCard.classList.remove('hidden');
        } else if (st === 'mediasi_tidak_tercapai' || st === 'menunggu_keputusan_admin') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/20 text-amber-200 border border-amber-500/30';
            badge.innerText = 'Mediasi Tidak Tercapai';
            bannerTitle.innerText = 'Mediasi Tidak Mencapai Kesepakatan';
            bannerDesc.innerText = 'Admin telah mencatat bahwa Customer dan Mitra belum mencapai kesepakatan. Admin tidak menetapkan keputusan bagi kedua pihak.';
            bannerIcon.className = 'fa-solid fa-handshake text-amber-300 text-sm mt-0.5 shrink-0';
        } else if (st === 'selesai') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
            badge.innerText = 'Selesai Dimediasi';
            bannerTitle.innerText = 'Pengaduan Telah Selesai Ditutup';
            bannerDesc.innerText = 'Kesepakatan damai telah tercapai atau tindak lanjut telah dipenuhi. Berkas pengaduan telah ditutup secara resmi.';
            bannerIcon.className = 'fa-solid fa-circle-check text-emerald-400 text-sm mt-0.5 shrink-0';
        } else if (st === 'ditolak') {
            badge.className = 'px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-500/20 text-gray-300 border border-gray-500/30';
            badge.innerText = 'Ditolak';
            bannerTitle.innerText = 'Pengaduan Ditolak';
            bannerDesc.innerText = 'Laporan tidak dapat dilanjutkan berdasarkan hasil pemeriksaan objektif dan telah diarsipkan.';
            bannerIcon.className = 'fa-solid fa-ban text-gray-400 text-sm mt-0.5 shrink-0';
        }
    }

    // Render Timeline Riwayat Tindakan
    function renderComplaintTimeline(data) {
        const container = document.getElementById('modalTimelineList');
        if (!container) return;

        let timeline = data.timeline || [];
        if (timeline.length === 0) {
            timeline = [
                { stage: 'Pengaduan Diterima', time: data.date || '08 Okt 2026', desc: 'Laporan keluhan tercatat di sistem mediasi SayaBantu.' },
                { stage: 'Pemeriksaan Berkas', time: 'Tahap Awal', desc: 'Admin memeriksa kronologi dan bukti pekerjaan.' }
            ];
        }

        container.innerHTML = timeline.map((item, idx) => `
            <div class="flex items-start gap-2.5 pb-2 ${idx !== timeline.length - 1 ? 'border-b border-white/5' : ''}">
                <div class="w-2 h-2 rounded-full bg-teal-400 mt-1 shrink-0"></div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between text-[9px]">
                        <strong class="text-white">${item.stage}</strong>
                        <span class="text-gray-400">${item.time}</span>
                    </div>
                    <p class="text-[9.5px] text-gray-300 mt-0.5 leading-relaxed">${item.desc}</p>
                </div>
            </div>
        `).join('');
    }

    // Render Dinamis 3 Tombol Aksi di Footer Detail Modal
    function renderModalActionFooter(st) {
        const footer = document.getElementById('modalActionFooter');
        if (!footer) return;

        if (st === 'dalam_mediasi') {
            footer.innerHTML = `
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="window.openMediationRoomModal()" class="col-span-2 py-2.5 px-3 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 active:scale-95 text-white font-bold text-xs flex items-center justify-center gap-2 border border-teal-400/40 shadow-lg shadow-teal-950/60 cursor-pointer">
                        <i class="fa-solid fa-door-open text-teal-200"></i>
                        <span>Buka Ruang Mediasi Khusus</span>
                    </button>
                    <button type="button" onclick="window.openRejectComplaintModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-rose-800 to-red-700 hover:from-rose-700 hover:to-red-600 active:scale-95 text-white font-bold text-[11px] flex flex-col items-center justify-center gap-1 border border-rose-500/30 cursor-pointer">
                        <i class="fa-solid fa-ban text-xs text-rose-200"></i>
                        <span class="leading-none">Tolak Pengaduan</span>
                    </button>
                </div>
            `;
        } else if (st === 'menunggu_bukti') {
            footer.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="window.openRequestEvidenceModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-amber-700 to-yellow-600 hover:from-amber-600 hover:to-yellow-500 active:scale-95 text-white font-bold text-[11px] shadow-md transition-all flex flex-col items-center justify-center gap-1 border border-amber-400/40 cursor-pointer">
                        <i class="fa-solid fa-pen-to-square text-xs text-amber-200"></i>
                        <span class="leading-none">Ubah Permintaan</span>
                    </button>
                    <button type="button" onclick="window.openRejectComplaintModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-rose-700 to-red-600 hover:from-rose-600 hover:to-red-500 active:scale-95 text-white font-bold text-[11px] shadow-md transition-all flex flex-col items-center justify-center gap-1 border border-rose-400/40 cursor-pointer">
                        <i class="fa-solid fa-ban text-xs text-rose-200"></i>
                        <span class="leading-none">Tolak Pengaduan</span>
                    </button>
                </div>
            `;
        } else if (st === 'mediasi_tidak_tercapai' || st === 'menunggu_keputusan_admin') {
            footer.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="flex-1 py-2 px-3 rounded-xl bg-amber-500/15 border border-amber-400/30 text-amber-200 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Mediasi ditutup tanpa kesepakatan</span>
                    </div>
                    <button type="button" onclick="window.openMediationRoomModal()" class="py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-eye text-teal-300"></i>
                        <span>Lihat Ruang Mediasi</span>
                    </button>
                </div>
            `;
        } else if (st === 'selesai') {
            footer.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="flex-1 py-2 px-3 rounded-xl bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        <span>Kasus Telah Ditutup & Selesai Dimediasi</span>
                    </div>
                    <button type="button" onclick="window.openMediationRoomModal()" class="py-2 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-eye text-teal-300"></i>
                        <span>Lihat Ruang Mediasi</span>
                    </button>
                </div>
            `;
        } else if (st === 'ditolak') {
            footer.innerHTML = `
                <div class="w-full py-2.5 px-3 rounded-xl bg-rose-500/15 border border-rose-400/30 text-rose-300 font-bold text-xs flex items-center justify-center gap-2">
                    <i class="fa-solid fa-ban text-sm"></i>
                    <span>Pengaduan Ditolak Berdasarkan Pemeriksaan</span>
                </div>
            `;
        } else {
            // Default: Baru / Proses / Bukti Dikirim (3 Tombol Utama Sesuai SOP)
            footer.innerHTML = `
                <div class="grid grid-cols-3 gap-2">
                    {{-- 1. TOMBOL MULAI MEDIASI --}}
                    <button type="button" onclick="window.openStartMediationModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-teal-700 to-emerald-600 hover:from-teal-600 hover:to-emerald-500 active:scale-95 text-white font-bold text-[11px] shadow-md shadow-emerald-950/50 transition-all flex flex-col items-center justify-center gap-1 border border-teal-400/40 cursor-pointer">
                        <i class="fa-solid fa-handshake text-xs text-teal-200"></i>
                        <span class="leading-none">Mulai Mediasi</span>
                    </button>

                    {{-- 2. TOMBOL PERLU BUKTI --}}
                    <button type="button" onclick="window.openRequestEvidenceModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-amber-700 to-yellow-600 hover:from-amber-600 hover:to-yellow-500 active:scale-95 text-white font-bold text-[11px] shadow-md shadow-amber-950/50 transition-all flex flex-col items-center justify-center gap-1 border border-amber-400/40 cursor-pointer">
                        <i class="fa-solid fa-file-circle-question text-xs text-amber-200"></i>
                        <span class="leading-none">Perlu Bukti</span>
                    </button>

                    {{-- 3. TOMBOL TOLAK PENGADUAN --}}
                    <button type="button" onclick="window.openRejectComplaintModal()" class="py-2.5 px-1.5 rounded-xl bg-gradient-to-tr from-rose-700 to-red-600 hover:from-rose-600 hover:to-red-500 active:scale-95 text-white font-bold text-[11px] shadow-md shadow-rose-950/50 transition-all flex flex-col items-center justify-center gap-1 border border-rose-400/40 cursor-pointer">
                        <i class="fa-solid fa-ban text-xs text-rose-200"></i>
                        <span class="leading-none">Tolak Pengaduan</span>
                    </button>
                </div>
            `;
        }
    }

    function setQuickComplaintNote(text, btn) {
        const textarea = document.getElementById('adminComplaintNote');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
        }
        if (btn) {
            document.querySelectorAll('.chip-quick-note').forEach(c => {
                c.classList.remove('bg-sky-500/30', 'border-sky-400', 'text-white');
                c.classList.add('bg-white/5', 'border-white/10', 'text-gray-300');
            });
            btn.classList.remove('bg-white/5', 'border-white/10', 'text-gray-300');
            btn.classList.add('bg-sky-500/30', 'border-sky-400', 'text-white');
        }
    }
    window.setQuickComplaintNote = setQuickComplaintNote;

    function closeComplaintModal() {
        const modal = document.getElementById('complaintDetailModal');
        const panel = document.getElementById('complaintDetailPanel');
        if (panel) panel.classList.add('translate-y-full');
        setTimeout(() => {
            if (modal) {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
            }
            document.body.classList.remove('sb-scroll-locked');
        }, 220);
    }
    window.closeComplaintModal = closeComplaintModal;

    // =========================================================================
    // IMPLEMENTASI 1: MULAI MEDIASI & RUANG MEDIASI
    // =========================================================================

    function openStartMediationModal() {
        const id = document.getElementById('modalComplaintId')?.innerText || '#ADU-100';
        const reporter = document.getElementById('modalReporter')?.innerText || 'Budi Santoso (Mitra)';
        const reported = document.getElementById('modalReported')?.innerText || 'Riko Sanjaya (Customer)';
        const category = document.getElementById('modalComplaintCategory')?.innerText || 'Perbedaan Pendapat Biaya';
        const chronology = document.getElementById('modalChronology')?.innerText || '';

        document.getElementById('startMediationTicketId').innerText = id;
        document.getElementById('startMediationReporter').innerText = reporter;
        document.getElementById('startMediationReported').innerText = reported;
        document.getElementById('startMediationProblem').innerText = category;

        // Default Ringkasan Netral
        const summaryEl = document.getElementById('startMediationSummary');
        if (summaryEl) {
            if (id === '#ADU-100') {
                summaryEl.value = 'Terjadi perbedaan pemahaman antara Mitra dan Customer mengenai cakupan pekerjaan yang telah disepakati. Mitra menyatakan bahwa pekerjaan tambahan memerlukan biaya di luar kesepakatan awal, sedangkan Customer berpendapat bahwa pekerjaan tersebut masih termasuk dalam kesepakatan.';
            } else {
                summaryEl.value = chronology ? `Terdapat perbedaan pemahaman antara kedua pihak terkait: ${chronology.substring(0, 160)}...` : 'Kedua belah pihak membutuhkan klarifikasi dan solusi bersama terkait permasalahan pekerjaan.';
            }
        }

        // Default Arahan Awal Netral
        const dirEl = document.getElementById('startMediationDirections');
        if (dirEl) {
            dirEl.value = 'Kedua pihak diminta menjelaskan dasar pemahaman masing-masing berdasarkan kesepakatan pekerjaan awal dan bukti yang tersedia. Kedua pihak diharapkan mempertimbangkan alternatif penyelesaian yang dapat diterima bersama.';
        }

        const modal = document.getElementById('startMediationModal');
        const panel = document.getElementById('startMediationPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
        }
    }
    window.openStartMediationModal = openStartMediationModal;

    function closeStartMediationModal() {
        const modal = document.getElementById('startMediationModal');
        const panel = document.getElementById('startMediationPanel');
        if (panel) panel.classList.add('translate-y-full');
        setTimeout(() => {
            if (modal) {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }, 220);
    }
    window.closeStartMediationModal = closeStartMediationModal;

    function submitStartMediation() {
        const id = document.getElementById('startMediationTicketId')?.innerText || '#ADU-100';
        const summary = (document.getElementById('startMediationSummary')?.value || '').trim();
        const directions = (document.getElementById('startMediationDirections')?.value || '').trim();

        if (!summary) {
            getAdminToast()('Peringatan', 'Harap isi ringkasan masalah secara netral sebelum memulai mediasi.', 'error');
            return;
        }

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        // Update di LocalStorage
        const stored = getAdminStoredComplaints();
        let found = stored.find(s => s.id === id);
        if (found) {
            found.status = 'dalam_mediasi';
            found.mediationSummary = summary;
            found.mediationDirections = directions;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Mediasi Dimulai oleh Admin',
                time: timeStr,
                desc: `Admin memulai proses mediasi 3-pihak. Arahan awal: "${directions}"`
            });
            found.mediation = found.mediation || {};
            found.mediation.summary = summary;
            found.mediation.directions = directions;
            if (!found.mediation.messages || found.mediation.messages.length === 0) {
                found.mediation.messages = [
                    {
                        sender: 'Admin SayaBantu (Fasilitator)',
                        role: 'admin',
                        text: `Selamat datang di Ruang Mediasi 3-Pihak untuk tiket ${found.id}. Arahan Admin: ${directions || 'Harap sampaikan klarifikasi dan masukan secara objektif dan saling menghargai.'}`,
                        time: timeStr
                    }
                ];
            }
            if (!found.mediation.proposal) {
                found.mediation.proposal = {
                    title: 'Usulan Penyelesaian Bersama',
                    author: 'Admin SayaBantu',
                    terms: [
                        'Pekerjaan diselesaikan sesuai kesepakatan awal pada aplikasi.',
                        'Tidak ada denda/biaya tambahan di luar invoice resmi platform.',
                        'Kedua pihak bersepakat menyelesaikan pesanan secara damai.'
                    ],
                    customerApproved: false,
                    mitraApproved: false
                };
            }
        } else {
            const newRecord = {
                id: id,
                orderId: activeComplaintCard ? activeComplaintCard.getAttribute('data-order-id') : '#SB-00115',
                reporterName: activeComplaintCard ? activeComplaintCard.getAttribute('data-reporter') : 'Budi Santoso (Mitra)',
                reporterRole: 'mitra',
                reportedName: activeComplaintCard ? activeComplaintCard.getAttribute('data-reported') : 'Riko Sanjaya (Customer)',
                category: activeComplaintCard ? activeComplaintCard.getAttribute('data-category') : 'Perbedaan Pendapat Biaya',
                summary: summary,
                chronology: activeComplaintCard ? activeComplaintCard.getAttribute('data-chronology') : summary,
                evidence: activeComplaintCard ? activeComplaintCard.getAttribute('data-evidence') : '-',
                status: 'dalam_mediasi',
                mediationSummary: summary,
                mediationDirections: directions,
                mediation: {
                    summary: summary,
                    directions: directions,
                    messages: [
                        {
                            sender: 'Admin SayaBantu (Fasilitator)',
                            role: 'admin',
                            text: `Selamat datang di Ruang Mediasi 3-Pihak untuk tiket ${id}. Arahan Admin: ${directions || 'Harap sampaikan klarifikasi dan masukan secara objektif.'}`,
                            time: timeStr
                        }
                    ],
                    proposal: {
                        title: 'Usulan Penyelesaian Bersama',
                        author: 'Admin SayaBantu',
                        terms: [
                            'Pekerjaan diselesaikan sesuai kesepakatan awal pada aplikasi.',
                            'Tidak ada denda/biaya tambahan di luar invoice resmi platform.',
                            'Kedua pihak bersepakat menyelesaikan pesanan secara damai.'
                        ],
                        customerApproved: false,
                        mitraApproved: false
                    }
                },
                date: activeComplaintCard ? activeComplaintCard.getAttribute('data-date') : timeStr,
                timeline: [
                    { stage: 'Pengaduan Tercatat', time: timeStr, desc: 'Laporan keluhan tercatat.' },
                    { stage: 'Mediasi Dimulai oleh Admin', time: timeStr, desc: `Admin memulai proses mediasi 3-pihak. Arahan: "${directions}"` }
                ]
            };
            stored.unshift(newRecord);
        }
        saveAdminStoredComplaints(stored);

        // Update DOM
        if (activeComplaintCard) {
            activeComplaintCard.setAttribute('data-status', 'dalam_mediasi');
            updateCardBadge(activeComplaintCard, 'dalam_mediasi');
        }

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();

        closeStartMediationModal();
        renderComplaintStatusUI('dalam_mediasi');
        renderModalActionFooter('dalam_mediasi');

        getAdminToast()('Mediasi Dimulai', `Status tiket ${id} diubah menjadi Dalam Mediasi. Notifikasi telah dikirim ke Customer & Mitra.`, 'success');

        // Buka langsung Ruang Mediasi Khusus
        setTimeout(() => {
            openMediationRoomModal();
        }, 300);
    }
    window.submitStartMediation = submitStartMediation;

    // Buka Ruang Mediasi Khusus
    function openMediationRoomModal() {
        const id = document.getElementById('modalComplaintId')?.innerText || (activeComplaintData ? activeComplaintData.id : '#ADU-100');
        const stored = getAdminStoredComplaints();
        const comp = stored.find(s => s.id === id) || activeComplaintData || {};

        const reporter = comp.reporterName || document.getElementById('modalReporter')?.innerText || 'Pelapor';
        const reported = comp.reportedName || document.getElementById('modalReported')?.innerText || 'Terlapor';
        const isCustReporter = (comp.reporterRole === 'customer');
        const custName = isCustReporter ? reporter : reported;
        const mitraName = isCustReporter ? reported : reporter;

        const titleEl = document.getElementById('roomHeaderTitle');
        if (titleEl) titleEl.innerText = `Ruang Mediasi ${comp.id || id}`;

        const custBadge = document.getElementById('roomCustNameBadge');
        if (custBadge) custBadge.innerText = custName;

        const mitraBadge = document.getElementById('roomMitraNameBadge');
        if (mitraBadge) mitraBadge.innerText = mitraName;

        const subEl = document.getElementById('roomHeaderSub');
        if (subEl) subEl.innerText = `Customer: ${custName} · Mitra: ${mitraName}`;

        const summaryEl = document.getElementById('roomPinnedSummary');
        if (summaryEl) {
            summaryEl.innerText = (comp.mediation && comp.mediation.summary)
                ? comp.mediation.summary
                : (comp.summary || comp.chronology || 'Proses mediasi 3-pihak difasilitasi oleh Admin platform SayaBantu.');
        }

        // Render live chat messages
        comp.mediation = comp.mediation || {};
        comp.mediation.messages = comp.mediation.messages || [];
        if (comp.mediation.messages.length === 0) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            comp.mediation.messages.push({
                sender: 'Admin SayaBantu (Fasilitator)',
                role: 'admin',
                text: 'Selamat datang di Ruang Mediasi 3-Pihak. Silakan sampaikan klarifikasi dan masukan secara objektif dan saling menghargai.',
                time: timeStr
            });
            saveAdminStoredComplaints(stored);
        }

        const msgList = document.getElementById('mediationMessagesList');
        if (msgList) {
            msgList.innerHTML = comp.mediation.messages.map(m => {
                const isAdmin = m.role === 'admin';
                const isMitra = m.role === 'mitra';
                const isCust = m.role === 'customer';

                let bgClass = 'bg-teal-500/15 border-teal-500/30 text-teal-300';
                let iconClass = 'fa-shield-halved';
                let alignClass = 'mr-auto';

                if (isMitra) {
                    bgClass = 'bg-sky-500/15 border-sky-500/30 text-sky-300';
                    iconClass = 'fa-helmet-safety';
                    alignClass = 'ml-auto';
                } else if (isCust) {
                    bgClass = 'bg-purple-500/15 border-purple-500/30 text-purple-300';
                    iconClass = 'fa-user';
                    alignClass = 'mr-auto';
                }

                return `
                    <div class="p-2.5 rounded-2xl ${bgClass} border text-white space-y-1 max-w-[92%] ${alignClass}">
                        <div class="flex items-center justify-between text-[9px] font-bold">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid ${iconClass}"></i> ${m.sender}
                            </span>
                            <span class="opacity-70">${m.time}</span>
                        </div>
                        <p class="text-[10.5px] text-gray-100 leading-relaxed">${m.text}</p>
                    </div>
                `;
            }).join('');

            const scrollArea = document.getElementById('mediationChatScrollArea');
            if (scrollArea) scrollArea.scrollTop = scrollArea.scrollHeight;
        }

        // Render Usulan & Persetujuan
        const proposal = comp.mediation.proposal || {
            title: 'Usulan Penyelesaian Bersama',
            author: 'Admin SayaBantu',
            terms: [
                'Pekerjaan diselesaikan sesuai kesepakatan awal.',
                'Tidak ada biaya/denda tambahan di luar invoice resmi.',
                'Kedua pihak bersepakat menyelesaikan pesanan secara damai.'
            ],
            customerApproved: false,
            mitraApproved: false
        };
        comp.mediation.proposal = proposal;

        const authorEl = document.getElementById('proposalAuthorName');
        if (authorEl) authorEl.innerText = proposal.author || 'Admin SayaBantu';

        const custStatusText = document.getElementById('custStatusText');
        const custStatusDot = document.getElementById('custStatusDot');
        const mitraStatusText = document.getElementById('mitraStatusText');
        const globalBadge = document.getElementById('proposalGlobalBadge');
        const notice = document.getElementById('agreementSuccessNotice');

        const custApproved = !!proposal.customerApproved;
        const mitraApproved = !!proposal.mitraApproved;

        if (custStatusText) {
            custStatusText.innerText = custApproved ? 'Disetujui' : 'Menunggu';
            custStatusText.className = custApproved ? 'text-emerald-300 font-bold' : 'text-amber-300';
        }
        if (custStatusDot) {
            custStatusDot.className = custApproved ? 'w-2 h-2 rounded-full bg-emerald-400' : 'w-2 h-2 rounded-full bg-amber-400';
        }

        if (mitraStatusText) {
            mitraStatusText.innerText = mitraApproved ? 'Disetujui' : 'Menunggu';
            mitraStatusText.className = mitraApproved ? 'text-emerald-300 font-bold' : 'text-amber-300';
        }

        if (custApproved && mitraApproved) {
            if (globalBadge) {
                globalBadge.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                globalBadge.innerText = 'Kesepakatan Tercapai';
            }
            if (notice) notice.classList.remove('hidden');
        } else {
            if (globalBadge) {
                globalBadge.className = 'px-2 py-0.5 rounded-full text-[8.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30';
                globalBadge.innerText = 'Menunggu Persetujuan';
            }
            if (notice) notice.classList.add('hidden');
        }

        const modal = document.getElementById('mediationRoomModal');
        const panel = document.getElementById('mediationRoomPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
        }
    }
    window.openMediationRoomModal = openMediationRoomModal;

    function closeMediationRoomModal() {
        const modal = document.getElementById('mediationRoomModal');
        const panel = document.getElementById('mediationRoomPanel');
        if (panel) panel.classList.add('translate-y-full');
        setTimeout(() => {
            if (modal) {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }, 220);
    }
    window.closeMediationRoomModal = closeMediationRoomModal;

    // Kirim pesan chat mediasi oleh Admin
    function sendMediationChatMessage() {
        const input = document.getElementById('adminMediationInput');
        const msg = (input ? input.value : '').trim();
        if (!msg) return;

        const id = document.getElementById('modalComplaintId')?.innerText || (activeComplaintData ? activeComplaintData.id : null);
        const stored = getAdminStoredComplaints();
        const comp = stored.find(s => s.id === id);

        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const newMsgObj = {
            sender: 'Admin SayaBantu (Fasilitator)',
            role: 'admin',
            text: msg,
            time: timeStr
        };

        if (comp) {
            comp.mediation = comp.mediation || {};
            comp.mediation.messages = comp.mediation.messages || [];
            comp.mediation.messages.push(newMsgObj);
            saveAdminStoredComplaints(stored);
        }

        const list = document.getElementById('mediationMessagesList');
        if (list) {
            const newMsg = document.createElement('div');
            newMsg.className = 'p-2.5 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-white space-y-1 max-w-[92%] animate-fade-in mr-auto';
            newMsg.innerHTML = `
                <div class="flex items-center justify-between text-[9px] font-bold text-teal-300">
                    <span class="flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved"></i> Admin SayaBantu (Fasilitator)
                    </span>
                    <span class="opacity-70">${timeStr}</span>
                </div>
                <p class="text-[10.5px] text-gray-100 leading-relaxed">${msg}</p>
            `;
            list.appendChild(newMsg);

            const scrollArea = document.getElementById('mediationChatScrollArea');
            if (scrollArea) scrollArea.scrollTop = scrollArea.scrollHeight;
        }

        input.value = '';
    }
    window.sendMediationChatMessage = sendMediationChatMessage;

    // Selesaikan Mediasi (Tombol Hijau di Ruang Mediasi)
    function submitResolveMediation() {
        const id = document.getElementById('modalComplaintId')?.innerText;
        if (!id) {
            getAdminToast()('Pengaduan Tidak Ditemukan', 'Pilih pengaduan yang valid sebelum menyelesaikan mediasi.', 'warning');
            return;
        }

        const stored = getAdminStoredComplaints();
        const found = stored.find(s => s.id === id);
        if (!found) {
            getAdminToast()('Pengaduan Tidak Ditemukan', 'Data pengaduan tidak tersedia untuk diperbarui.', 'warning');
            return;
        }

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const proposal = found.mediation?.proposal;
        if (!proposal?.customerApproved || !proposal?.mitraApproved) {
            getAdminToast()('Persetujuan Belum Lengkap', 'Hanya Customer dan Mitra yang dapat menyetujui usulan. Mediasi dapat ditutup berhasil setelah keduanya menyetujui.', 'warning');
            return;
        }

        const resolutionNote = 'Customer dan Mitra telah menyetujui usulan penyelesaian. Admin mengonfirmasi hasil mediasi.';

        found.status = 'selesai';
        found.adminNote = resolutionNote;
        found.timeline = found.timeline || [];
        found.timeline.push({
            stage: 'Mediasi Selesai & Kasus Ditutup',
            time: timeStr,
            desc: resolutionNote
        });
        saveAdminStoredComplaints(stored);

        if (activeComplaintCard) {
            activeComplaintCard.setAttribute('data-status', 'selesai');
            activeComplaintCard.setAttribute('data-admin-note', resolutionNote);
            updateCardBadge(activeComplaintCard, 'selesai');
        }

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();

        closeMediationRoomModal();
        renderComplaintStatusUI('selesai');
        renderModalActionFooter('selesai');

        getAdminToast()('Mediasi Selesai', `Tiket ${id} berhasil dimediasi dan ditutup resmi dengan kesepakatan damai.`, 'success');
    }
    window.submitResolveMediation = submitResolveMediation;

    // Admin hanya mencatat hasil mediasi, bukan menetapkan keputusan pengganti para pihak.
    function submitFailMediation() {
        const id = document.getElementById('modalComplaintId')?.innerText;
        if (!id) {
            getAdminToast()('Pengaduan Tidak Ditemukan', 'Pilih pengaduan yang valid sebelum mencatat hasil mediasi.', 'warning');
            return;
        }

        const stored = getAdminStoredComplaints();
        const found = stored.find(s => s.id === id);
        if (!found) {
            getAdminToast()('Pengaduan Tidak Ditemukan', 'Data pengaduan tidak tersedia untuk diperbarui.', 'warning');
            return;
        }

        const reason = prompt('Tuliskan alasan/kendala tidak tercapainya kesepakatan mediasi:', 'Kedua pihak belum mencapai kesepahaman mengenai kompensasi biaya tambahan.');
        if (!reason) return;

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        found.status = 'mediasi_tidak_tercapai';
        found.failureReason = reason;
        found.timeline = found.timeline || [];
        found.timeline.push({
            stage: 'Mediasi Selesai: Tidak Tercapai Kesepakatan',
            time: timeStr,
            desc: `Mediasi berakhir tanpa kesepakatan: ${reason}.`
        });
        saveAdminStoredComplaints(stored);

        if (activeComplaintCard) {
            activeComplaintCard.setAttribute('data-status', 'mediasi_tidak_tercapai');
            updateCardBadge(activeComplaintCard, 'mediasi_tidak_tercapai');
        }

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();

        closeMediationRoomModal();
        renderComplaintStatusUI('mediasi_tidak_tercapai');
        renderModalActionFooter('mediasi_tidak_tercapai');

        getAdminToast()('Hasil Mediasi Dicatat', `Tiket ${id} dicatat tidak mencapai kesepakatan. Admin tidak menetapkan keputusan bagi Customer atau Mitra.`, 'info');
    }
    window.submitFailMediation = submitFailMediation;

    // =========================================================================
    // IMPLEMENTASI 2: TOMBOL PERLU BUKTI
    // =========================================================================

    function openRequestEvidenceModal() {
        const id = document.getElementById('modalComplaintId')?.innerText || '#ADU-100';
        const reporter = document.getElementById('modalReporter')?.innerText || 'Budi Santoso (Mitra)';
        const reported = document.getElementById('modalReported')?.innerText || 'Riko Sanjaya (Customer)';

        document.getElementById('reqEvidenceModalSub').innerText = `Pengaduan ${id}`;
        document.getElementById('reqEvidenceCustLabel').innerText = reported;
        document.getElementById('reqEvidenceMitraLabel').innerText = reporter;

        const instEl = document.getElementById('reqEvidenceInstructions');
        if (instEl && !instEl.value) {
            instEl.value = 'Harap unggah foto kondisi partisi meja setelah pekerjaan dilakukan dan sertakan foto kondisi awal jika tersedia.';
        }

        const modal = document.getElementById('requestEvidenceModal');
        const panel = document.getElementById('requestEvidencePanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
        }
    }
    window.openRequestEvidenceModal = openRequestEvidenceModal;

    function closeRequestEvidenceModal() {
        const modal = document.getElementById('requestEvidenceModal');
        const panel = document.getElementById('requestEvidencePanel');
        if (panel) panel.classList.add('translate-y-full');
        setTimeout(() => {
            if (modal) {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }, 220);
    }
    window.closeRequestEvidenceModal = closeRequestEvidenceModal;

    function submitRequestEvidence() {
        const id = document.getElementById('modalComplaintId')?.innerText || '#ADU-100';
        const instructions = (document.getElementById('reqEvidenceInstructions')?.value || '').trim();
        const deadline = (document.getElementById('reqEvidenceDeadline')?.value || '26 Oktober 2026').trim();

        if (!instructions) {
            getAdminToast()('Peringatan', 'Harap isi rincian keterangan bukti yang diminta secara jelas.', 'error');
            return;
        }

        const checkedTypes = [];
        document.querySelectorAll('input[name="reqEvidenceTypes"]:checked').forEach(c => checkedTypes.push(c.value));
        const targetRadio = document.querySelector('input[name="reqEvidenceTarget"]:checked')?.value || 'keduanya';

        let targetLabel = 'Kedua Pihak (Customer & Mitra)';
        if (targetRadio === 'customer') targetLabel = 'Customer';
        else if (targetRadio === 'mitra') targetLabel = 'Mitra';

        const typesStr = checkedTypes.length ? checkedTypes.join(', ') : 'Foto kondisi pekerjaan';

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const stored = getAdminStoredComplaints();
        let found = stored.find(s => s.id === id);
        if (found) {
            found.status = 'menunggu_bukti';
            found.evidenceRequest = {
                target: targetLabel,
                types: typesStr,
                instructions: instructions,
                deadline: deadline
            };
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Permintaan Bukti Terkirim',
                time: timeStr,
                desc: `Admin meminta bukti [${typesStr}] kepada ${targetLabel}. Batas pengumpulan: ${deadline}. Ket: "${instructions}"`
            });
        }
        saveAdminStoredComplaints(stored);

        // Update Label di Kartu Menunggu Bukti
        const reqTypeLbl = document.getElementById('modalReqEvidenceTypeLabel');
        const reqDescLbl = document.getElementById('modalReqEvidenceDescLabel');
        const reqDeadLbl = document.getElementById('modalReqEvidenceDeadlineLabel');
        if (reqTypeLbl) reqTypeLbl.innerText = typesStr;
        if (reqDescLbl) reqDescLbl.innerText = instructions;
        if (reqDeadLbl) reqDeadLbl.innerText = deadline;

        if (activeComplaintCard) {
            activeComplaintCard.setAttribute('data-status', 'menunggu_bukti');
            updateCardBadge(activeComplaintCard, 'menunggu_bukti');
        }

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();

        closeRequestEvidenceModal();
        renderComplaintStatusUI('menunggu_bukti');
        renderModalActionFooter('menunggu_bukti');

        getAdminToast()('Permintaan Bukti Terkirim', `Notifikasi permintaan bukti dan tenggat waktu ${deadline} telah dikirimkan ke ${targetLabel}.`, 'info');
    }
    window.submitRequestEvidence = submitRequestEvidence;

    // =========================================================================
    // IMPLEMENTASI 3: TOMBOL TOLAK PENGADUAN
    // =========================================================================

    function openRejectComplaintModal() {
        const id = document.getElementById('modalComplaintId')?.innerText || '#ADU-100';
        document.getElementById('rejectComplaintModalSub').innerText = `Pengaduan ${id}`;

        const radio = document.querySelector('input[name="rejectionReason"]:checked');
        updateRejectNotePlaceholder(radio ? radio.value : 'Tidak ditemukan pelanggaran berdasarkan pemeriksaan');

        const modal = document.getElementById('rejectComplaintModal');
        const panel = document.getElementById('rejectComplaintPanel');
        if (modal && panel) {
            modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            panel.classList.remove('translate-y-full');
        }
    }
    window.openRejectComplaintModal = openRejectComplaintModal;

    function updateRejectNotePlaceholder(reason) {
        const noteEl = document.getElementById('rejectAdminNote');
        if (!noteEl) return;

        if (reason === 'Tidak ditemukan pelanggaran berdasarkan pemeriksaan') {
            noteEl.value = 'Berdasarkan pemeriksaan bukti SPK dan klarifikasi, tidak ditemukan indikasi pelanggaran SOP atau kelalaian yang disengaja. Pengaduan diselesaikan secara administratif.';
        } else if (reason === 'Bukti tidak cukup untuk mendukung laporan setelah pemeriksaan') {
            noteEl.value = 'Bukti yang dilampirkan tidak mencukupi untuk membuktikan klaim kerugian setelah batas waktu pengumpulan klarifikasi terlewati.';
        } else if (reason === 'Laporan tidak sesuai dengan ketentuan SayaBantu.com') {
            noteEl.value = 'Materi pengaduan berada di luar lingkup perlindungan transaksi aplikasi SayaBantu.com.';
        } else if (reason === 'Laporan duplikat') {
            noteEl.value = 'Laporan merupakan duplikasi dari tiket aktif yang saat ini telah dalam proses penanganan.';
        } else {
            noteEl.value = '';
            noteEl.placeholder = 'Jelaskan secara spesifik dan objektif alasan penolakan laporan ini...';
        }
    }
    window.updateRejectNotePlaceholder = updateRejectNotePlaceholder;

    function closeRejectComplaintModal() {
        const modal = document.getElementById('rejectComplaintModal');
        const panel = document.getElementById('rejectComplaintPanel');
        if (panel) panel.classList.add('translate-y-full');
        setTimeout(() => {
            if (modal) {
                modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }, 220);
    }
    window.closeRejectComplaintModal = closeRejectComplaintModal;

    function submitRejectComplaint() {
        const id = document.getElementById('modalComplaintId')?.innerText || '#ADU-100';
        const reason = document.querySelector('input[name="rejectionReason"]:checked')?.value || 'Tidak ditemukan pelanggaran berdasarkan pemeriksaan';
        const note = (document.getElementById('rejectAdminNote')?.value || '').trim();

        if (!note) {
            getAdminToast()('Peringatan', 'Catatan admin wajib diisi untuk mempertanggungjawabkan keputusan penolakan secara akuntabel.', 'error');
            return;
        }

        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                        now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

        const stored = getAdminStoredComplaints();
        let found = stored.find(s => s.id === id);
        if (found) {
            found.status = 'ditolak';
            found.rejectionReason = reason;
            found.adminNote = note;
            found.timeline = found.timeline || [];
            found.timeline.push({
                stage: 'Pengaduan Ditolak oleh Admin',
                time: timeStr,
                desc: `Alasan: ${reason}. Catatan Admin: "${note}"`
            });
        } else {
            const newRecord = {
                id: id,
                orderId: activeComplaintCard ? activeComplaintCard.getAttribute('data-order-id') : '#SB-00115',
                reporterName: activeComplaintCard ? activeComplaintCard.getAttribute('data-reporter') : 'Dimas Pratama',
                reporterRole: 'customer',
                reportedName: activeComplaintCard ? activeComplaintCard.getAttribute('data-reported') : 'Joko Susanto',
                category: activeComplaintCard ? activeComplaintCard.getAttribute('data-category') : 'Keluhan Layanan',
                summary: activeComplaintCard ? activeComplaintCard.getAttribute('data-summary') : note,
                chronology: activeComplaintCard ? activeComplaintCard.getAttribute('data-chronology') : note,
                evidence: activeComplaintCard ? activeComplaintCard.getAttribute('data-evidence') : '-',
                status: 'ditolak',
                rejectionReason: reason,
                adminNote: note,
                date: activeComplaintCard ? activeComplaintCard.getAttribute('data-date') : timeStr,
                timeline: [
                    { stage: 'Pengaduan Tercatat', time: timeStr, desc: 'Laporan tercatat.' },
                    { stage: 'Pengaduan Ditolak oleh Admin', time: timeStr, desc: `Alasan: ${reason}. Catatan: "${note}"` }
                ]
            };
            stored.unshift(newRecord);
        }
        saveAdminStoredComplaints(stored);

        if (activeComplaintCard) {
            activeComplaintCard.setAttribute('data-status', 'ditolak');
            activeComplaintCard.setAttribute('data-admin-note', note);
            updateCardBadge(activeComplaintCard, 'ditolak');
        }

        updateAdminStatsAndTabs();
        applyComplaintsVisibility();

        closeRejectComplaintModal();
        renderComplaintStatusUI('ditolak');
        renderModalActionFooter('ditolak');

        getAdminToast()('Pengaduan Ditolak', `Keputusan penolakan tiket ${id} telah disimpan dalam riwayat dan diarsipkan.`, 'error');
    }
    window.submitRejectComplaint = submitRejectComplaint;

    // Inisialisasi saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function() {
        syncAdminComplaints();
    });

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        syncAdminComplaints();
    }
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Moderasi & Kepatuhan - SayaBantu.com')
@section('page-title', 'Moderasi & Kepatuhan')
@section('page-subtitle', 'Penegakan Kode Etik Mitra, Syarat Ketentuan, serta tata kelola sanksi akun')

@section('hero')
<div class="px-5 pt-1 pb-0">
    {{-- STAT RINGKASAN MODERASI --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-purple-400"></i>
                Integritas & Kepatuhan Sistem
            </span>
            <button type="button" onclick="openKodeEtikModal()" class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 hover:bg-purple-500/30 transition-colors cursor-pointer focus:outline-none">
                Kode Etik v2.1
            </button>
        </div>

        <div class="grid grid-cols-4 gap-2 text-center" id="modStatFilters">
            <button type="button" class="mod-stat-tab w-full p-2 rounded-xl bg-white/[0.18] border-2 border-rose-400 shadow-md shadow-rose-500/25 cursor-pointer transition-all scale-[1.03] text-center focus:outline-none ring-2 ring-rose-400/30" onclick="filterModerasi('all')" data-filter="all">
                <div class="text-xs font-black text-white" id="statModKasusAktif">{{ $countAktif ?? 0 }}</div>
                <div class="text-[8px] text-white font-bold mt-0.5">Kasus Aktif</div>
            </button>
            <button type="button" class="mod-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterModerasi('peringatan')" data-filter="peringatan">
                <div class="text-xs font-black text-amber-400" id="statModPeringatan">{{ $countPeringatan ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Peringatan</div>
            </button>
            <button type="button" class="mod-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterModerasi('dibatasi')" data-filter="dibatasi">
                <div class="text-xs font-black text-sky-400" id="statModDibatasi">{{ $countDibatasi ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Dibatasi</div>
            </button>
            <button type="button" class="mod-stat-tab w-full p-2 rounded-xl bg-white/[0.04] border border-white/10 opacity-75 cursor-pointer hover:bg-white/[0.08] hover:opacity-100 transition-all text-center focus:outline-none" onclick="filterModerasi('suspend')" data-filter="suspend">
                <div class="text-xs font-black text-rose-500" id="statModSuspend">{{ $countSuspend ?? 0 }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Suspend</div>
            </button>
        </div>
    </div>

    {{-- SWITCH TAB ANTARA KASUS DAN DOKUMEN --}}
    <div class="flex items-center justify-center gap-2 mt-3 text-xs">
        <button type="button" id="tabKasusBtn" onclick="switchModTab('kasus')" class="flex-1 py-2 rounded-xl font-bold transition-all bg-[#173B67] text-white border border-sky-400/40 shadow-sm flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-gavel text-xs"></i>
            <span>Kasus Pelanggaran ({{ $countAktif ?? 0 }})</span>
        </button>
        <button type="button" id="tabDokumenBtn" onclick="switchModTab('dokumen')" class="flex-1 py-2 rounded-xl font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-file-contract text-xs"></i>
            <span>Dokumen Kebijakan (3)</span>
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-4" style="margin-top: 0.6cm;">

    {{-- VIEW 1: KASUS PELANGGARAN --}}
    <div id="viewKasusPelanggaran" class="space-y-3">

        @forelse($kasusMitra ?? [] as $km)
            @php
                $kmId = $km->mitra_id ?: ('SB-' . str_pad((string) $km->id, 4, '0', STR_PAD_LEFT));
                $kmStatus = strtolower($km->status ?? 'suspend');
                $isSuspend = in_array($kmStatus, ['suspend', 'tolak', 'rejected', 'ditolak']);
                $filterType = $isSuspend ? 'suspend' : 'peringatan';
                $caseCode = '#MOD-' . str_pad((string) $km->id, 3, '0', STR_PAD_LEFT);
            @endphp
            <div class="mod-case-card p-4 rounded-[22px] bg-white border border-rose-200 shadow-sm space-y-3 hover:border-rose-400 transition-all cursor-pointer"
                data-case-id="{{ $caseCode }}"
                data-account="{{ $km->name }} ({{ $kmId }})"
                data-type="{{ $km->rejection_reason ?: 'Penangguhan Akun Mitra' }}"
                data-date="{{ $km->updated_at ? $km->updated_at->format('d M Y • H:i') . ' WIB' : 'Baru saja' }}"
                data-source="Moderasi Akun Sistem"
                data-clause="Pasal Kepatuhan Kode Etik Mitra SayaBantu.com"
                data-sanction="{{ $isSuspend ? 'Penangguhan Akun Mitra (Suspend)' : 'Evaluasi Kepatuhan' }}"
                data-status="{{ $filterType }}"
                data-mod-filter="{{ $filterType }}"
                data-chronology="{{ $km->rejection_reason ?: 'Status akun saat ini: ' . ucfirst($kmStatus) }}"
                onclick="openModerationModal(this)"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs text-rose-700 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100">
                                {{ $caseCode }}
                            </span>
                            <h3 class="text-xs font-bold text-gray-900 truncate">{{ $km->name }} ({{ $kmId }})</h3>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-1">
                            Status: <strong class="text-rose-700">{{ ucfirst($kmStatus) }}</strong>
                        </p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-800 border border-rose-300 shrink-0">
                        {{ $isSuspend ? 'Akun Ditangguhkan' : 'Peringatan' }}
                    </span>
                </div>

                <p class="text-[10px] text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                    {{ $km->rejection_reason ?: 'Akun mitra memerlukan tindakan evaluasi kepatuhan dari administrator.' }}
                </p>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                    <span class="text-[9px] text-gray-400">{{ $km->updated_at ? $km->updated_at->diffForHumans() : 'Baru saja' }}</span>
                    <span class="text-[10px] text-rose-600 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-lock"></i> Detail Evaluasi
                    </span>
                </div>
            </div>
        @empty
            {{-- EMPTY STATE DEFAULT JIKA TIDAK ADA KASUS --}}
            <div id="modEmptyStateDefault" class="p-8 rounded-[22px] bg-white border border-gray-200 text-center space-y-3 shadow-xs">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Belum Ada Pelanggaran atau Kasus Moderasi</h4>
                    <p class="text-[10px] text-gray-500 mt-1 leading-relaxed max-w-xs mx-auto">
                        Seluruh mitra mematuhi Kode Etik dan Syarat Ketentuan SayaBantu.com dengan baik.
                    </p>
                </div>
            </div>
        @endforelse

        {{-- EMPTY STATE JIKA FILTER KOSONG --}}
        <div id="modEmptyState" class="hidden p-6 rounded-[22px] bg-white border border-gray-200 text-center space-y-2">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <p class="text-xs font-bold text-gray-800">Tidak Ada Kasus Terpilih</p>
            <p class="text-[10px] text-gray-500">Tidak ada kasus aktif dengan status filter ini saat ini.</p>
            <button type="button" onclick="filterModerasi('all')" class="mt-2 px-3 py-1.5 rounded-xl bg-[#173B67] text-white text-[10px] font-bold shadow-xs">
                Tampilkan Semua Kasus
            </button>
        </div>

    </div>

    {{-- VIEW 2: DOKUMEN KEBIJAKAN & KEPATUHAN --}}
    <div id="viewDokumenKepatuhan" class="space-y-3 hidden">

        <div class="flex items-center justify-between px-1 text-xs">
            <span class="text-gray-500 font-medium">Dokumen Regulasi & Kebijakan Platform</span>
            <button type="button" onclick="showToast('Dokumen Kebijakan', 'Membuka formulir versi revisi dokumen...', 'info')" class="text-[10px] font-bold text-[#173B67] hover:underline flex items-center gap-1">
                <i class="fa-solid fa-plus text-[9px]"></i> Rilis Versi Baru
            </button>
        </div>

        {{-- DOKUMEN 1: KODE ETIK MITRA --}}
        <div class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base border border-purple-100 shrink-0">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Kode Etik Mitra SayaBantu</h4>
                        <p class="text-[10px] text-gray-500 mt-0.5">Standar pelayanan, kejujuran, dan keselamatan kerja</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    v2.1 Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px]">
                <div>
                    <span class="text-gray-400 block text-[8.5px]">TANGGAL BERLAKU</span>
                    <strong class="text-gray-800">01 Januari 2026</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[8.5px]">PERSETUJUAN MITRA</span>
                    <strong class="text-emerald-700">48 / 48 Mitra (100%)</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400">Persetujuan digital tercatat di sistem</span>
                <button type="button" onclick="openKodeEtikModal()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors cursor-pointer">
                    Lihat Dokumen
                </button>
            </div>
        </div>

        {{-- DOKUMEN 2: SYARAT & KETENTUAN --}}
        <div class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#173B67] flex items-center justify-center text-base border border-blue-100 shrink-0">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Syarat & Ketentuan Penggunaan</h4>
                        <p class="text-[10px] text-gray-500 mt-0.5">Ketentuan layanan, pembayaran, dan mekanisme suruhan</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    v3.0 Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px]">
                <div>
                    <span class="text-gray-400 block text-[8.5px]">TANGGAL BERLAKU</span>
                    <strong class="text-gray-800">15 Februari 2026</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[8.5px]">PERSETUJUAN KONSUMEN</span>
                    <strong class="text-emerald-700">1.240 Pengguna Terdaftar</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400">Diverifikasi saat registrasi akun</span>
                <a href="{{ route('customer.syarat-ketentuan') }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors">
                    Lihat Dokumen
                </a>
            </div>
        </div>

        {{-- DOKUMEN 3: KEBIJAKAN PRIVASI --}}
        <div class="p-4 rounded-[22px] bg-white border border-gray-200/80 shadow-sm space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base border border-teal-100 shrink-0">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Kebijakan Privasi & Mediasi</h4>
                        <p class="text-[10px] text-gray-500 mt-0.5">Perlindungan data pribadi & hak audit chat admin</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    v2.0 Aktif
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-[10px]">
                <div>
                    <span class="text-gray-400 block text-[8.5px]">TANGGAL BERLAKU</span>
                    <strong class="text-gray-800">01 Januari 2026</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[8.5px]">STATUS ENKRIPSI</span>
                    <strong class="text-teal-700">Tersertifikasi SSL 256-bit</strong>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                <span class="text-[9.5px] text-gray-400">Sesuai UU Perlindungan Data Pribadi</span>
                <a href="{{ route('customer.kebijakan-privasi') }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-semibold transition-colors">
                    Lihat Dokumen
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@section('modals')
{{-- MODAL TINDAKAN MODERASI & SANKSI --}}
<div id="moderationActionModal" class="sb-overlay-backdrop fixed inset-0 z-[130] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/65 backdrop-blur-[2px]" onclick="closeModerationModal()"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="moderationActionPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[26px] shadow-2xl p-5 border-t border-white/15 max-h-[90vh] overflow-y-auto translate-y-full transition-transform pb-8">
            
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm border border-purple-500/30">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white leading-none" id="modCaseId">#MOD-041</h3>
                        <p class="text-[10px] text-gray-400 mt-1" id="modAccountName">Joko Susanto</p>
                    </div>
                </div>
                <button type="button" onclick="closeModerationModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="mt-4 space-y-3.5 text-xs">

                {{-- KLAUSUL PASAL YANG DIDUGA DILANGGAR --}}
                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-[9px] text-purple-300 font-bold block mb-1">KETENTUAN / PASAL YANG DILANGGAR</span>
                    <p class="text-white text-[11px] font-semibold" id="modClause">-</p>
                    <p class="text-gray-300 text-[10px] mt-1 leading-relaxed" id="modChronology">-</p>
                </div>

                {{-- PILIHAN TINDAKAN SANKSI (3 TINGKATAN SESUAI SPESIFIKASI) --}}
                <div>
                    <label class="block text-[10px] font-semibold text-gray-300 mb-1.5">Tingkat Tindakan Moderasi</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10">
                            <input type="radio" name="sanctionLevel" value="peringatan" checked class="text-amber-500">
                            <div>
                                <strong class="text-white text-xs block">Peringatan Tertulis (SP)</strong>
                                <span class="text-[9.5px] text-gray-400">Kirim teguran resmi tercatat dalam rekam jejak akun</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10">
                            <input type="radio" name="sanctionLevel" value="pembatasan" class="text-sky-500">
                            <div>
                                <strong class="text-sky-300 text-xs block">Pembatasan Fitur (7 - 30 Hari)</strong>
                                <span class="text-[9.5px] text-gray-400">Blokir sementara penerimaan pesanan atau penawaran baru</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10">
                            <input type="radio" name="sanctionLevel" value="suspend" class="text-rose-500">
                            <div>
                                <strong class="text-rose-400 text-xs block">Penangguhan Akun (Suspend)</strong>
                                <span class="text-[9.5px] text-gray-400">Nonaktifkan akun sepenuhnya dari akses platform SayaBantu</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-300 mb-1">Dasar Pertimbangan & Riwayat Putusan</label>
                    <textarea id="adminModReason" rows="2" placeholder="Catat dasar pertimbangan sanksi untuk akuntabilitas..." class="w-full bg-white/5 border border-white/15 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400"></textarea>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="button" onclick="closeModerationModal()" class="flex-1 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-semibold text-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="applyModerationAction()" class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 active:scale-95 text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-1.5 border border-purple-400/40">
                        <i class="fa-solid fa-gavel"></i>
                        Terapkan Tindakan
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- MODAL PRATINJAU KODE ETIK MITRA --}}
<div id="kodeEtikModal" class="sb-overlay-backdrop fixed inset-0 z-[130] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/65 backdrop-blur-[2px]" onclick="closeKodeEtikModal()"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="kodeEtikPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[26px] shadow-2xl p-5 border-t border-white/15 max-h-[85vh] overflow-y-auto translate-y-full transition-transform pb-8">
            
            <div class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-4"></div>

            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm border border-purple-500/30">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Kode Etik Mitra SayaBantu</h3>
                        <p class="text-[10px] text-purple-300 font-semibold mt-0.5">Versi 2.1 • Berlaku Efektif Seluruh Mitra</p>
                    </div>
                </div>
                <button type="button" onclick="closeKodeEtikModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="mt-4 space-y-3 text-xs">
                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="font-bold text-sky-400 text-[11px] mb-1">Pasal 1: Standar Pelayanan & Integritas Kerja</div>
                    <p class="text-[10px] text-gray-300 leading-relaxed">Setiap mitra wajib memberikan pelayanan secara profesional, jujur, santun, menjaga standar keselamatan kerja, dan wajib mengenakan seragam/identitas resmi SayaBantu.</p>
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="font-bold text-amber-400 text-[11px] mb-1">Pasal 4: Ketepatan Waktu & Kehadiran</div>
                    <p class="text-[10px] text-gray-300 leading-relaxed">Mitra wajib mengonfirmasi pesanan dan hadir di lokasi sesuai jadwal. Keterlambatan lebih dari 30 menit tanpa pemberitahuan sah berakibat sanksi Peringatan Tertulis (SP-1).</p>
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="font-bold text-rose-400 text-[11px] mb-1">Pasal 9: Larangan Transaksi Finansial di Luar Sistem</div>
                    <p class="text-[10px] text-gray-300 leading-relaxed">Dilarang keras meminta transfer langsung ke rekening pribadi, memotong biaya di luar aplikasi, atau menerima pesanan offline dari pelanggan platform. Pelanggaran berakibat Penangguhan Akun Permanen (Suspend).</p>
                </div>
            </div>

            <div class="pt-4 mt-2">
                <button type="button" onclick="closeKodeEtikModal()" class="w-full py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchModTab(tab) {
        const kasusBtn = document.getElementById('tabKasusBtn');
        const dokBtn = document.getElementById('tabDokumenBtn');
        const viewKasus = document.getElementById('viewKasusPelanggaran');
        const viewDok = document.getElementById('viewDokumenKepatuhan');

        if (tab === 'kasus') {
            kasusBtn.className = 'flex-1 py-2 rounded-xl font-bold transition-all bg-[#173B67] text-white border border-sky-400/40 shadow-sm flex items-center justify-center gap-1.5';
            dokBtn.className = 'flex-1 py-2 rounded-xl font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 flex items-center justify-center gap-1.5';
            viewKasus.classList.remove('hidden');
            viewDok.classList.add('hidden');
        } else {
            dokBtn.className = 'flex-1 py-2 rounded-xl font-bold transition-all bg-[#173B67] text-white border border-sky-400/40 shadow-sm flex items-center justify-center gap-1.5';
            kasusBtn.className = 'flex-1 py-2 rounded-xl font-medium transition-all bg-white/10 text-gray-300 hover:bg-white/20 flex items-center justify-center gap-1.5';
            viewDok.classList.remove('hidden');
            viewKasus.classList.add('hidden');
        }
    }

    let currentModFilter = 'all';

    function filterModerasi(type) {
        currentModFilter = type;

        // Pastikan berada di tab Kasus Pelanggaran
        const viewKasus = document.getElementById('viewKasusPelanggaran');
        if (viewKasus && viewKasus.classList.contains('hidden')) {
            switchModTab('kasus');
        }

        // Update styling stat tabs
        document.querySelectorAll('.mod-stat-tab').forEach(tab => {
            const filter = tab.getAttribute('data-filter');
            const labelText = tab.querySelector('div:last-child');

            tab.classList.remove(
                'scale-[1.03]', 'shadow-md', 'shadow-lg', 'border-2', 'bg-white/[0.18]',
                'bg-amber-500/20', 'bg-sky-500/20', 'bg-rose-500/20',
                'border-rose-400', 'border-amber-400', 'border-sky-400', 'border-rose-500',
                'shadow-rose-500/25', 'shadow-amber-500/25', 'shadow-sky-500/25',
                'ring-2', 'ring-rose-400/30', 'ring-amber-400/30', 'ring-sky-400/30', 'ring-rose-500/30',
                'opacity-100'
            );
            tab.classList.add('bg-white/[0.04]', 'border', 'border-white/10', 'opacity-75');

            if (labelText) {
                labelText.classList.remove('text-white', 'font-bold');
                labelText.classList.add('text-gray-300');
            }

            if (filter === type) {
                tab.classList.remove('bg-white/[0.04]', 'border-white/10', 'opacity-75');
                tab.classList.add('scale-[1.03]', 'shadow-lg', 'border-2', 'opacity-100');

                if (labelText) {
                    labelText.classList.remove('text-gray-300');
                    labelText.classList.add('text-white', 'font-bold');
                }

                if (type === 'all') {
                    tab.classList.add('bg-white/[0.18]', 'border-rose-400', 'shadow-rose-500/25', 'ring-2', 'ring-rose-400/30');
                } else if (type === 'peringatan') {
                    tab.classList.add('bg-amber-500/20', 'border-amber-400', 'shadow-amber-500/25', 'ring-2', 'ring-amber-400/30');
                } else if (type === 'dibatasi') {
                    tab.classList.add('bg-sky-500/20', 'border-sky-400', 'shadow-sky-500/25', 'ring-2', 'ring-sky-400/30');
                } else if (type === 'suspend') {
                    tab.classList.add('bg-rose-500/20', 'border-rose-500', 'shadow-rose-500/25', 'ring-2', 'ring-rose-500/30');
                }
            }
        });

        // Filter cards
        const cards = document.querySelectorAll('.mod-case-card');
        let visibleCount = 0;
        cards.forEach(card => {
            const f = card.getAttribute('data-mod-filter');
            if (type === 'all' || f === type) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // Update tab text
        const tabSpan = document.querySelector('#tabKasusBtn span');
        if (tabSpan) {
            tabSpan.innerText = `Kasus Pelanggaran (${visibleCount})`;
        }

        // Empty state
        const emptyState = document.getElementById('modEmptyState');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    function openModerationModal(card) {
        document.getElementById('modCaseId').innerText = card.getAttribute('data-case-id');
        document.getElementById('modAccountName').innerText = card.getAttribute('data-account');
        document.getElementById('modClause').innerText = card.getAttribute('data-clause');
        document.getElementById('modChronology').innerText = card.getAttribute('data-chronology');
        document.getElementById('adminModReason').value = '';

        const modal = document.getElementById('moderationActionModal');
        const panel = document.getElementById('moderationActionPanel');
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
        panel.classList.remove('translate-y-full');
    }

    function closeModerationModal() {
        const modal = document.getElementById('moderationActionModal');
        const panel = document.getElementById('moderationActionPanel');
        panel.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
        }, 200);
    }

    function applyModerationAction() {
        const caseId = document.getElementById('modCaseId').innerText;
        const level = document.querySelector('input[name="sanctionLevel"]:checked').value;
        const reason = document.getElementById('adminModReason').value.trim();

        if (reason === '') {
            showToast('Alasan Wajib', 'Harap masukkan dasar pertimbangan dan catatan moderasi.', 'warning');
            return;
        }

        closeModerationModal();
        showToast('Tindakan Diterapkan', `Kasus ${caseId} telah diputuskan (${level.toUpperCase()}) dan diberlakukan pada sistem.`, 'success');
    }

    function openKodeEtikModal() {
        const modal = document.getElementById('kodeEtikModal');
        const panel = document.getElementById('kodeEtikPanel');
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
        panel.classList.remove('translate-y-full');
    }

    function closeKodeEtikModal() {
        const modal = document.getElementById('kodeEtikModal');
        const panel = document.getElementById('kodeEtikPanel');
        panel.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
        }, 200);
    }

    window.switchModTab = switchModTab;
    window.filterModerasi = filterModerasi;
    window.openModerationModal = openModerationModal;
    window.closeModerationModal = closeModerationModal;
    window.applyModerationAction = applyModerationAction;
    window.openKodeEtikModal = openKodeEtikModal;
    window.closeKodeEtikModal = closeKodeEtikModal;
</script>
@endpush

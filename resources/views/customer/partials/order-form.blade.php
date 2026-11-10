{{-- =========================================================
     PARTIAL: FORM AJUKAN SURUHAN CUSTOMER
     Alur 3 Langkah Form (sesuai Alur Pengajuan Suruhan):
     1. Mengisi Form Ajukan Suruhan (Katalog, Detail, Waktu, Lokasi, Info Pendukung)
     2. Menentukan Metode Harga (Nego Harga vs Harga Pas)
     3. Memeriksa dan Mengirim Pengajuan (Review Ringkasan & Kirim ke Mitra)
     
     Mempertahankan ID elemen custForm* untuk kompatibilitas penuh.
========================================================= --}}
<section id="custOrderFormCard" class="card-sb p-4 sm:p-5 border-2 border-slate-200/90 shadow-sm bg-white rounded-2xl">

    {{-- HEADER FORM & STATUS --}}
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5">
        <div>
            <div class="flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-[#173B67] flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
                <h3 class="text-sm font-extrabold text-[#0E1D31]">Form Ajukan Suruhan</h3>
            </div>
            <p class="text-[10px] text-slate-500 mt-0.5">Alur pengajuan resmi SayaBantu langsung ke Mitra</p>
        </div>
        <button
            type="button"
            onclick="toggleCustAlurOverview()"
            class="text-[9.5px] font-bold px-2.5 py-1 rounded-full bg-sky-50 hover:bg-sky-100 text-[#173B67] border border-sky-200 transition-all flex items-center gap-1 cursor-pointer"
        >
            <i class="fa-solid fa-circle-info text-sky-600"></i>
            <span>11 Alur Layanan</span>
            <i id="custAlurChevron" class="fa-solid fa-chevron-down text-[8px] transition-transform"></i>
        </button>
    </div>

    {{-- EXPANDABLE 11 ALUR LENGKAP --}}
    <div id="custAlurOverviewBox" class="hidden mb-4 p-3.5 rounded-xl bg-gradient-to-br from-slate-50 to-sky-50/60 border border-sky-200/90 text-xs transition-all">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-sky-200/60">
            <span class="text-[11px] font-black text-[#0E1D31] flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-sky-600"></i> Alur Pengajuan Suruhan hingga Selesai
            </span>
            <span class="text-[9px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-full border border-sky-200">
                Langkah 1 - 11
            </span>
        </div>
        <ol class="space-y-1.5 text-[10px] text-slate-700">
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">1</span>
                <span><strong>Mengisi Form:</strong> Pilih layanan katalog, detail kebutuhan, waktu, lokasi &amp; info pendukung.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">2</span>
                <span><strong>Menentukan Biaya Jasa:</strong> Masukkan nominal biaya yang Anda sediakan (minimal Rp 10.000).</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-[#0E1D31] text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">3</span>
                <span><strong>Memeriksa &amp; Mengirim:</strong> Review seluruh data lalu kirim ke platform.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-sky-700 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">4</span>
                <span><strong>Mitra Melihat Kebutuhan:</strong> Tampil pada halaman Kebutuhan Mitra untuk diambil/ditawar.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">5</span>
                <span><strong>Negosiasi / Setuju Harga:</strong> Negosiasi via chat (jika Nego) atau persetujuan Mitra (jika Pas).</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">6</span>
                <span><strong>Konfirmasi Admin:</strong> Admin memverifikasi kesesuaian detail &amp; harga kesepakatan.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">7</span>
                <span><strong>Pembayaran:</strong> Customer diarahkan ke pembayaran bergaransi aman platform.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">8</span>
                <span><strong>Melaksanakan Pekerjaan:</strong> Mitra bekerja sesuai kesepakatan (status Berlangsung).</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">9</span>
                <span><strong>Konfirmasi Selesai:</strong> Mitra tandai selesai, Customer memverifikasi hasil.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">10</span>
                <span><strong>Penerusan Dana Mitra:</strong> Dana yang ditahan diteruskan ke saldo Mitra.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0 mt-0.5">11</span>
                <span><strong>Memberikan Ulasan:</strong> Penilaian rating &amp; ulasan untuk apresiasi Mitra.</span>
            </li>
        </ol>
    </div>

    {{-- STEPPER WIZARD 3 TAHAP PENGISIAN --}}
    <div class="mb-4">
        <div class="grid grid-cols-3 gap-1 relative text-[9px] font-bold text-center">
            {{-- Step 1 Tab --}}
            <button
                type="button"
                id="btnStepIndicator1"
                onclick="goToCustStep(1)"
                class="step-indicator-btn py-1.5 px-1 rounded-xl bg-[#0E1D31] text-white flex flex-col items-center justify-center gap-0.5 transition-all shadow-xs"
            >
                <span class="flex items-center gap-1 font-extrabold">
                    <span id="badgeStepNumber1" class="w-3.5 h-3.5 rounded-full bg-white text-[#0E1D31] text-[8px] flex items-center justify-center font-bold">1</span>
                    <span class="truncate">Detail Form</span>
                </span>
                <span class="text-[8px] opacity-80 font-normal truncate">Kebutuhan &amp; Lokasi</span>
            </button>

            {{-- Step 2 Tab --}}
            <button
                type="button"
                id="btnStepIndicator2"
                onclick="goToCustStep(2)"
                class="step-indicator-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex flex-col items-center justify-center gap-0.5 transition-all"
            >
                <span class="flex items-center gap-1 font-bold">
                    <span id="badgeStepNumber2" class="w-3.5 h-3.5 rounded-full bg-slate-300 text-slate-700 text-[8px] flex items-center justify-center font-bold">2</span>
                    <span class="truncate">Biaya Layanan</span>
                </span>
                <span class="text-[8px] opacity-70 font-normal truncate">Min. Rp 10.000</span>
            </button>

            {{-- Step 3 Tab --}}
            <button
                type="button"
                id="btnStepIndicator3"
                onclick="goToCustStep(3)"
                class="step-indicator-btn py-1.5 px-1 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex flex-col items-center justify-center gap-0.5 transition-all"
            >
                <span class="flex items-center gap-1 font-bold">
                    <span id="badgeStepNumber3" class="w-3.5 h-3.5 rounded-full bg-slate-300 text-slate-700 text-[8px] flex items-center justify-center font-bold">3</span>
                    <span class="truncate">Periksa &amp; Kirim</span>
                </span>
                <span class="text-[8px] opacity-70 font-normal truncate">Kirim ke Mitra</span>
            </button>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form id="custSuruhanForm" novalidate onsubmit="handleCustSuruhanSubmit(event)" class="text-xs">

        {{-- ====================================================
             LANGKAH 1: MENGISI DETAIL FORM SURUHAN
        ==================================================== --}}
        <div id="custStepSection1" class="space-y-3.5">

            <div class="p-2.5 rounded-xl bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 flex items-start gap-2">
                <i class="fa-solid fa-pen-fancy text-sky-600 mt-0.5 shrink-0 text-sm"></i>
                <div class="flex-1">
                    <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 1: Mengisi Form Ajukan Suruhan</strong>
                    <p class="text-[10px] text-slate-600 leading-snug">
                        Pilih layanan katalog, lengkapi instruksi kebutuhan, waktu pengerjaan, dan lokasi alamat penugasan.
                    </p>
                </div>
            </div>

            {{-- PILIH LAYANAN KATALOG --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[10.5px] font-bold text-slate-700">
                        1. Pilih Layanan Katalog <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[9px] text-slate-400 font-medium">16 Layanan Tersedia</span>
                </div>
                <select
                    id="custFormLayanan"
                    onchange="onCustServiceSelected(this.value)"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all cursor-pointer"
                    required
                >
                    <option value="" disabled selected>-- Pilih salah satu layanan katalog --</option>
                    <optgroup label="🧹 Kebersihan">
                        <option value="Full Home Cleaning">Full Home Cleaning (Pembersihan Menyeluruh)</option>
                        <option value="Basic Daily Cleaning">Basic Daily Cleaning (Sapu &amp; Pel Rutin)</option>
                        <option value="Bersihkan Kamar Mandi">Bersihkan Kamar Mandi &amp; Kloset</option>
                        <option value="Cuci Piring & Dapur Bersih">Cuci Piring &amp; Dapur Bersih</option>
                    </optgroup>
                    <optgroup label="📦 Pindahan &amp; Angkut">
                        <option value="Pindahan">Pindahan Kos &amp; Rumah</option>
                        <option value="Angkut Barang">Angkut Barang Rumah Tangga</option>
                        <option value="Buang Sampah">Buang Sampah Rumah Tangga</option>
                    </optgroup>
                    <optgroup label="🛵 Antar, Belanja &amp; Antre">
                        <option value="Antar / Jemput">Antar / Jemput Barang atau Orang</option>
                        <option value="SanSu Food">SanSu Food (Belanja Pasar / Makanan)</option>
                        <option value="Titip Antre RS & Faskes">Titip Antre RS, Faskes &amp; Tiket</option>
                    </optgroup>
                    <optgroup label="🤝 Kegiatan Harian">
                        <option value="Nemenin Olahraga">Nemenin Olahraga (Jogging / Gym)</option>
                        <option value="Bantu Kegiatan Harian">Bantu Kegiatan Harian</option>
                    </optgroup>
                </select>

                {{-- INFO PREVIEW LAYANAN TERPILIH (TANPA MENAMPILKAN HARGA) --}}
                <div id="custServicePreviewCard" class="hidden mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 text-xs transition-all">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span id="custPreviewIcon" class="w-8 h-8 rounded-lg bg-sky-100 text-[#173B67] flex items-center justify-center text-sm shrink-0">
                                <i class="fa-solid fa-broom"></i>
                            </span>
                            <div>
                                <h4 id="custPreviewTitle" class="text-xs font-bold text-slate-900 leading-tight">Full Home Cleaning</h4>
                                <span id="custPreviewCat" class="text-[9px] font-semibold text-sky-700">Kategori: Kebersihan</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[9.5px]">Mitra Standby</span>
                        </div>
                    </div>
                    <p id="custPreviewDesc" class="text-[9.5px] text-slate-600 mt-1.5 leading-snug">
                        Pembersihan rumah menyeluruh meliputi kamar, ruang tamu, lantai dan debu.
                    </p>
                </div>
            </div>

            {{-- DETAIL KEBUTUHAN / INSTRUKSI PEKERJAAN --}}
            <div>
                <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                    2. Detail Kebutuhan atau Pekerjaan <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="custFormNote"
                    rows="2"
                    placeholder="Contoh: Bersihkan 2 kamar tidur dan 1 kamar mandi. Sapu, pel lantai, dan buang sampah ke tempat pembuangan..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all"
                    required
                ></textarea>
                {{-- Quick helper chip prompts --}}
                <div class="flex items-center gap-1.5 mt-1 overflow-x-auto pb-0.5 text-[9px]">
                    <span class="text-slate-400 shrink-0">Saran cepat:</span>
                    <button type="button" onclick="appendCustNote('Perlu penanganan teliti & bersih.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                        + Teliti &amp; bersih
                    </button>
                    <button type="button" onclick="appendCustNote('Peralatan sapu & pel sudah siap.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                        + Alat sudah siap
                    </button>
                    <button type="button" onclick="appendCustNote('Bawa tangga jika perlu.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 shrink-0 transition-colors">
                        + Bawa alat bantu
                    </button>
                </div>
            </div>

            {{-- WAKTU PENGERJAAN (TANGGAL, JAM & MENIT) --}}
            <div class="space-y-2">
                <label class="block text-[10.5px] font-bold text-slate-700">
                    3. Waktu Pengerjaan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" onclick="setCustDatePreset('today', this)" class="btn-cust-date-chip py-1.5 rounded-lg bg-[#0E1D31] text-white font-bold text-[10px] text-center transition-all cursor-pointer">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setCustDatePreset('tomorrow', this)" class="btn-cust-date-chip py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] text-center transition-all cursor-pointer">
                        Besok
                    </button>
                    <button type="button" onclick="setCustDatePreset('custom', this)" class="btn-cust-date-chip py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[10px] text-center transition-all cursor-pointer">
                        Pilih Tanggal
                    </button>
                </div>

                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-6">
                        <input
                            type="date"
                            id="custFormTanggal"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none"
                            value="{{ date('Y-m-d') }}"
                            required
                        >
                    </div>
                    <div class="col-span-3">
                        <select id="custFormJam" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:outline-none" required>
                            @for($j = 7; $j <= 23; $j++)
                                @php $jamStr = sprintf('%02d', $j); @endphp
                                <option value="{{ $jamStr }}" {{ $j == 14 ? 'selected' : '' }}>{{ $jamStr }}:00</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-span-3">
                        <select id="custFormMenit" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:outline-none" required>
                            <option value="00">00 Mnt</option>
                            <option value="15">15 Mnt</option>
                            <option value="30" selected>30 Mnt</option>
                            <option value="45">45 Mnt</option>
                        </select>
                    </div>
                </div>

                {{-- BATAS WAKTU MENDAPATKAN MITRA (DIINPUT CUSTOMER SEPERTI GOJEK) --}}
                <div id="custTodayNoticeBox" class="p-3 rounded-2xl bg-gradient-to-br from-amber-50/90 via-orange-50/70 to-amber-100/40 border border-amber-200/90 shadow-2xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-[11px] shrink-0 shadow-sm">
                                <i class="fa-solid fa-stopwatch animate-pulse"></i>
                            </span>
                            <div>
                                <span class="text-[10.5px] font-extrabold text-amber-950 block leading-tight">Batas Waktu Menemukan Mitra</span>
                                <span class="text-[8.5px] text-amber-800/80">Tentukan berapa lama Anda ingin menunggu mitra</span>
                            </div>
                        </div>
                        <span id="custDurasiPillBadge" class="text-[9.5px] font-black px-2 py-0.5 rounded-full bg-amber-200/90 text-amber-950 border border-amber-300 shadow-2xs">
                            1 Jam (60 Mnt)
                        </span>
                    </div>

                    {{-- Input Angka & Pilihan Satuan --}}
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input
                                type="number"
                                id="custFormDurasiAngka"
                                min="1"
                                max="720"
                                value="1"
                                oninput="syncCustDurasiInput()"
                                class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:border-[#173B67] focus:outline-none transition-all"
                                placeholder="Cth: 1"
                            >
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-semibold pointer-events-none">Durasi</span>
                        </div>
                        <div class="w-28 shrink-0">
                            <select
                                id="custFormDurasiSatuan"
                                onchange="syncCustDurasiInput()"
                                class="w-full px-2.5 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:border-[#173B67] focus:outline-none transition-all cursor-pointer"
                            >
                                <option value="jam" selected>Jam</option>
                                <option value="menit">Menit</option>
                            </select>
                        </div>
                    </div>

                    {{-- Quick Chip Presets --}}
                    <div>
                        <span class="text-[8.5px] text-amber-900/80 font-bold block mb-1">Pilihan Cepat Waktu:</span>
                        <div class="grid grid-cols-4 gap-1.5 text-[9.5px]">
                            <button type="button" onclick="setCustDurasiPreset(15, 'menit', this)" class="btn-cust-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                15 Mnt
                            </button>
                            <button type="button" onclick="setCustDurasiPreset(30, 'menit', this)" class="btn-cust-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                30 Mnt
                            </button>
                            <button type="button" onclick="setCustDurasiPreset(1, 'jam', this)" class="btn-cust-durasi-chip py-1.5 rounded-lg bg-[#0E1D31] text-white font-bold border border-slate-900 transition-colors text-center cursor-pointer">
                                1 Jam
                            </button>
                            <button type="button" onclick="setCustDurasiPreset(2, 'jam', this)" class="btn-cust-durasi-chip py-1.5 rounded-lg bg-white/90 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 transition-colors text-center cursor-pointer">
                                2 Jam
                            </button>
                        </div>
                    </div>

                    <p class="text-[9px] text-amber-900/90 leading-snug">
                        <i class="fa-solid fa-radar text-amber-700 mr-1"></i> Sistem akan memancarkan radar pencarian mitra di sekitar lokasi Anda sesuai batas waktu yang Anda tentukan.
                    </p>

                    {{-- Hidden Form Storage --}}
                    <input type="hidden" id="custFormDurasiCari" value="1 Jam">
                    <input type="hidden" id="custFormDurasiMenit" value="60">
                </div>
            </div>

            {{-- ALAMAT LOKASI & PATOKAN --}}
            <div>
                <label class="block text-[10.5px] font-bold text-slate-700 mb-1">
                    4. Alamat Lokasi &amp; Patokan <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="custFormAlamat"
                    rows="2"
                    placeholder="Contoh: Jl. Sudirman No. 12, RT 03/RW 02, Patokan samping minimarket seberang halte..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#173B67] focus:bg-white focus:outline-none transition-all"
                    required
                ></textarea>
            </div>

            {{-- CATATAN TAMBAHAN (OPSIONAL) - GENDER BEBAS OTOMATIS TANPA BAGIAN GENDER DI UI --}}
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-700 flex items-center gap-1">
                        <i class="fa-solid fa-note-sticky text-[#173B67]"></i> 5. Catatan Tambahan (Opsional)
                    </span>
                    <span class="text-[8.5px] text-slate-400">Instruksi Khusus</span>
                </div>

                {{-- Gender mitra otomatis bebas tanpa form gender --}}
                <input type="hidden" name="custFormGender" id="custFormGenderVal" value="Bebas">

                <div>
                    <input
                        type="text"
                        id="custFormPendukung"
                        placeholder="Contoh: Parkir mudah, patokan dekat gapura putih..."
                        class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#173B67]"
                    >
                </div>
            </div>

            {{-- TOMBOL LANJUT KE STEP 2 --}}
            <button
                type="button"
                onclick="nextCustStep(2)"
                class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Lanjut: Tentukan Biaya Layanan</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>

        </div>{{-- /#custStepSection1 --}}


        {{-- ====================================================
             LANGKAH 2: MENENTUKAN BIAYA LAYANAN
             (Langsung Input Nominal, Minimal Rp 10.000)
        ==================================================== --}}
        <div id="custStepSection2" class="space-y-3.5 hidden">

            <div class="p-2.5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex items-start gap-2">
                <i class="fa-solid fa-coins text-[#173B67] mt-0.5 shrink-0 text-sm"></i>
                <div class="flex-1">
                    <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 2: Tentukan Biaya Layanan</strong>
                    <p class="text-[10px] text-slate-600 leading-snug">
                        Masukkan nominal biaya yang Anda sediakan untuk pekerjaan ini. Tarif minimal pemesanan adalah <strong>Rp 10.000</strong>.
                    </p>
                </div>
            </div>

            {{-- BOX INPUT NOMINAL BIAYA --}}
            <div class="p-3.5 rounded-xl bg-white border-2 border-slate-200 space-y-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <label for="custFormBudget" class="text-xs font-black text-slate-900 block">
                            Nominal Biaya Jasa <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[9.5px] text-slate-500">Ketik nominal biaya yang Anda sediakan</span>
                    </div>
                    <span class="text-[9px] font-black text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        Minimal Rp 10.000
                    </span>
                </div>

                <div class="relative flex items-center">
                    <span class="absolute left-3.5 text-sm font-black text-slate-500">Rp</span>
                    <input
                        type="text"
                        id="custFormBudget"
                        onkeyup="formatRupiahSimple(this); syncCustBudgetReview();"
                        placeholder="50.000"
                        class="w-full pl-11 pr-3.5 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm font-black text-slate-900 focus:bg-white focus:border-[#173B67] focus:ring-2 focus:ring-[#173B67]/20 focus:outline-none transition-all"
                        required
                    >
                </div>

                {{-- Alert jika < 10.000 --}}
                <div id="custBudgetMinAlert" class="hidden p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-[10px] flex items-center gap-1.5 font-bold">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>Biaya minimal pemesanan adalah Rp 10.000. Mohon masukkan nominal Rp 10.000 atau lebih.</span>
                </div>

                {{-- Preset chips nominal --}}
                <div class="space-y-1.5">
                    <span class="text-[9px] text-slate-400 font-semibold block">Pilihan Cepat Nominal:</span>
                    <div class="grid grid-cols-4 gap-1.5 text-[10px]">
                        <button type="button" onclick="setCustQuickNominal(20000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 20.000
                        </button>
                        <button type="button" onclick="setCustQuickNominal(50000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 50.000
                        </button>
                        <button type="button" onclick="setCustQuickNominal(75000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 75.000
                        </button>
                        <button type="button" onclick="setCustQuickNominal(100000)" class="py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold border border-slate-200 transition-colors text-center cursor-pointer">
                            Rp 100.000
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                    <span><i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i>Garansi Transaksi Aman</span>
                    <span class="font-bold text-slate-700">Langsung diteruskan ke mitra</span>
                </div>
            </div>

            {{-- Hidden input sync kompatibilitas --}}
            <input type="hidden" id="custFormNego" value="Langsung">

            {{-- Dummy hidden cards untuk memelihara compat fungsi lama jika ada --}}
            <div class="hidden" id="cardMetodeNego"></div>
            <div class="hidden" id="cardMetodePas"></div>
            <div class="hidden" id="boxDetailNego"></div>
            <div class="hidden" id="boxDetailPas"></div>

            {{-- TOMBOL NAVIGASI STEP 2 --}}
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToCustStep(1)"
                    class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali</span>
                </button>
                <button
                    type="button"
                    onclick="nextCustStep(3)"
                    class="py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <span>Lanjut: Periksa Data</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </div>

        </div>{{-- /#custStepSection2 --}}


        {{-- ====================================================
             LANGKAH 3: MEMERIKSA DAN MENGIRIM PENGAJUAN
        ==================================================== --}}
        <div id="custStepSection3" class="space-y-3.5 hidden">

            <div class="p-2.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex items-start gap-2">
                <i class="fa-solid fa-clipboard-check text-emerald-600 mt-0.5 shrink-0 text-sm"></i>
                <div class="flex-1">
                    <strong class="text-[11px] font-bold text-[#0E1D31] block">Langkah 3: Memeriksa dan Mengirim Pengajuan</strong>
                    <p class="text-[10px] text-slate-600 leading-snug">
                        Periksa kembali seluruh detail kebutuhan, alamat, jadwal, dan harga sebelum dikirimkan ke Mitra.
                    </p>
                </div>
            </div>

            {{-- KARTU REVIEW RINGKASAN --}}
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2.5 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Ringkasan Pengajuan</span>
                    <button type="button" onclick="goToCustStep(1)" class="text-[9px] font-bold text-[#173B67] hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-pen text-[8px]"></i> Ubah
                    </button>
                </div>

                {{-- Layanan & Status --}}
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 block">Layanan Terpilih:</span>
                        <strong id="reviewCustLayanan" class="text-xs font-bold text-slate-900">-</strong>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[9px] text-slate-400 block">Status Penugasan:</span>
                        <span id="reviewCustMetodeBadge" class="text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                            Siap Disiarkan
                        </span>
                    </div>
                </div>

                {{-- Waktu & Jadwal --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 block">Jadwal Pengerjaan:</span>
                        <span id="reviewCustWaktu" class="text-[11px] font-semibold text-slate-800">-</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 block">Batas Cari Mitra:</span>
                        <span id="reviewCustDurasi" class="text-[11px] font-bold text-amber-700">1 Jam</span>
                    </div>
                </div>

                {{-- Alamat --}}
                <div>
                    <span class="text-[9px] text-slate-400 block">Alamat &amp; Patokan Lokasi:</span>
                    <p id="reviewCustAlamat" class="text-[11px] text-slate-800 leading-snug">-</p>
                </div>

                {{-- Detail Kebutuhan --}}
                <div>
                    <span class="text-[9px] text-slate-400 block">Detail Kebutuhan:</span>
                    <p id="reviewCustNote" class="text-[11px] text-slate-700 italic bg-white p-2 rounded-lg border border-slate-200/80 leading-snug">-</p>
                </div>

                {{-- Info Pendukung (Catatan) - Gender tidak ditampilkan di UI --}}
                <div class="pt-1 text-[10px] text-slate-600 flex items-center justify-between">
                    <span id="reviewCustGender" class="hidden">Bebas</span>
                    <span id="reviewCustPendukungWrapper" class="text-slate-500 truncate max-w-full"></span>
                </div>

                {{-- Rincian Budget --}}
                <div class="pt-2 border-t border-slate-200 space-y-1.5">
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <div>
                            <span class="text-[9.5px] font-bold text-slate-700 block">Total Biaya Suruhan:</span>
                            <span id="reviewCustPriceNote" class="text-[8.5px] text-slate-400">Minimal pemesanan Rp 10.000</span>
                        </div>
                        <strong id="reviewCustBudget" class="text-sm font-black text-emerald-700">Rp -</strong>
                    </div>
                    <span id="reviewCustRawBudget" class="hidden"></span>
                </div>
            </div>

            {{-- TRANSPARANSI ALUR TAHAP BERIKUTNYA --}}
            <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 space-y-1.5 text-[10px] text-slate-700">
                <span class="text-[10.5px] font-bold text-[#173B67] flex items-center gap-1.5">
                    <i class="fa-solid fa-bullhorn text-sky-600"></i> Apa yang terjadi setelah dikirim?
                </span>
                <p class="text-[9.5px] text-slate-600 leading-relaxed">
                    Pengajuan Anda langsung masuk ke halaman <strong>Kebutuhan Mitra</strong> (Tahap 4). Mitra terdekat akan melihat detail pekerjaan dan mengajukan kesediaan atau bernegosiasi chat (Tahap 5) sebelum diteruskan ke konfirmasi Admin &amp; Pembayaran.
                </p>
            </div>

            {{-- CHECKBOX PERSETUJUAN --}}
            <div class="pt-1 flex items-start gap-2">
                <input
                    type="checkbox"
                    id="custCheckTerms"
                    onchange="toggleCustFormSubmit(this.checked)"
                    class="w-4 h-4 mt-0.5 rounded text-[#173B67] focus:ring-[#173B67] border-slate-300 cursor-pointer"
                >
                <label for="custCheckTerms" class="text-[10px] text-slate-600 leading-tight cursor-pointer">
                    Saya telah memeriksa seluruh data di atas dan menyetujui pengajuan suruhan untuk ditampilkan kepada Mitra SayaBantu.
                </label>
            </div>

            {{-- TOMBOL AKSI STEP 3 --}}
            <div class="grid grid-cols-12 gap-2 pt-1">
                <button
                    type="button"
                    onclick="goToCustStep(2)"
                    class="col-span-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                >
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali</span>
                </button>
                <button
                    type="button"
                    id="custBtnSubmitSuruhan"
                    onclick="handleCustSuruhanSubmit(event)"
                    disabled
                    class="col-span-8 py-3 rounded-xl bg-[#0E1D31] text-white font-extrabold text-xs shadow-md opacity-50 cursor-not-allowed hover:bg-[#173B67] transition-all flex items-center justify-center gap-2"
                >
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pengajuan Suruhan</span>
                </button>
            </div>

            <p class="text-[9.5px] text-center text-slate-400">
                <i class="fa-solid fa-shield-halved text-[9px] mr-1"></i> Data Anda terlindungi &amp; transaksi bergaransi aman SayaBantu.
            </p>

        </div>{{-- /#custStepSection3 --}}

    </form>

</section>
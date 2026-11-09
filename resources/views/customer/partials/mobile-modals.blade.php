{{-- MOBILE BOTTOM SHEETS & MODALS --}}

{{-- BACKDROP OVERLAY --}}
<div id="mobileModalBackdrop" onclick="closeAllMobileSheets()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 hidden transition-opacity duration-300"></div>

{{-- 1. BOTTOM SHEET: KONFIRMASI PESANAN SURUHAN --}}
<div id="sheetKonfirmasiPesanan" class="fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto bg-white rounded-t-3xl shadow-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[85vh] overflow-hidden">
    
    {{-- DRAG HANDLE --}}
    <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeAllMobileSheets()">
        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
    </div>

    {{-- HEADER --}}
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 leading-tight">Suruhan Berhasil Dibuat!</h3>
                <span class="text-[11px] text-slate-500" id="resOrderCode">#SB-SURUH-8492</span>
            </div>
        </div>
        <button type="button" onclick="closeAllMobileSheets()" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- BODY SCROLL --}}
    <div class="p-5 overflow-y-auto space-y-4 text-xs">
        
        <div class="p-3 bg-sky-50 rounded-2xl border border-sky-100 flex items-start gap-2.5">
            <i class="fa-solid fa-bell text-sky-600 mt-0.5"></i>
            <p class="text-[11px] text-sky-900 leading-relaxed">
                Permintaan Anda telah masuk ke sistem. Anda dapat memantau penugasan mitra secara langsung di aplikasi.
            </p>
        </div>

        {{-- RINCIAN --}}
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 space-y-2.5">
            <div class="flex justify-between items-center text-slate-600">
                <span>Layanan:</span>
                <strong class="text-slate-900 font-bold" id="resLayanan">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Waktu Pelaksanaan:</span>
                <span class="text-slate-900 font-semibold" id="resWaktu">-</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Estimasi Budget:</span>
                <strong class="text-emerald-600 font-black text-sm" id="resBudget">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Preferensi Mitra:</span>
                <span class="text-slate-900 font-semibold capitalize" id="resGender">-</span>
            </div>
            <div class="pt-2 border-t border-slate-200 text-slate-600">
                <span class="block text-[10px] text-slate-400 mb-0.5">Catatan Pekerjaan:</span>
                <p class="text-[11px] text-slate-800 italic" id="resNote">-</p>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="space-y-2 pt-2">
            <a id="btnSendWaSheet" href="{{ route('customer.pesanan') }}" class="w-full py-3 rounded-2xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-center shadow-lg flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-list-check text-base"></i> Pantau Status Pesanan
            </a>

            <button type="button" onclick="closeAllMobileSheets(); showCustomerToast('Tersimpan', 'Pesanan berhasil disimpan di tab Pesanan', 'success')" class="w-full py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center transition-all">
                Kembali ke Beranda
            </button>
        </div>

    </div>

</div>


{{-- 2. BOTTOM SHEET: LACAK STATUS SURUHAN --}}
<div id="sheetLacakPesanan" class="fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto bg-white rounded-t-3xl shadow-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[85vh] overflow-hidden">
    
    <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeAllMobileSheets()">
        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
    </div>

    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-route"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-900">Lacak Status Suruhan</h3>
        </div>
        <button type="button" onclick="closeAllMobileSheets()" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <div class="p-5 overflow-y-auto space-y-4 text-xs">
        <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Masukkan Kode Suruhan / Nomor HP</label>
            <div class="flex gap-2">
                <input type="text" id="inputCekKode" placeholder="Contoh: #SB-1024 atau 0812..." class="flex-1 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <button type="button" onclick="lacakOrderAction()" class="px-4 py-2.5 bg-[#0E1D31] text-white rounded-xl font-bold text-xs hover:bg-[#173B67] transition-all">
                    Cek
                </button>
            </div>
        </div>

        {{-- CONTOH SURUHAN AKTIF SAAT INI --}}
        <div class="border-t border-slate-100 pt-3">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Pesanan Aktif Terbaru</span>
            
            <div class="bg-gradient-to-br from-slate-50 to-sky-50/40 rounded-2xl p-3.5 border border-slate-200 space-y-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                            Menunggu Mitra
                        </span>
                        <h4 class="text-xs font-bold text-slate-900 mt-1">Full Home Cleaning (2 Kamar)</h4>
                        <span class="text-[10px] text-slate-500">Tiket: #SB-9012 • Hari Ini, 16:30 WIB</span>
                    </div>
                    <strong class="text-xs font-extrabold text-slate-900">Rp 85.000</strong>
                </div>

                {{-- TIMELINE PROGRESS TRACKER --}}
                <div class="pt-2 border-t border-slate-200/80 space-y-2">
                    <div class="flex items-center gap-2 text-[11px]">
                        <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[9px]"><i class="fa-solid fa-check"></i></span>
                        <span class="text-slate-800 font-medium">Suruhan diterima oleh sistem</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px]">
                        <span class="w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[9px] animate-pulse"><i class="fa-solid fa-clock"></i></span>
                        <span class="text-amber-800 font-bold">Mencari mitra terdekat di sekitar lokasi</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                        <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-[9px]"><i class="fa-solid fa-motorcycle"></i></span>
                        <span>Mitra berangkat menuju lokasi Anda</span>
                    </div>
                </div>

                <a href="{{ route('customer.pengaduan') }}" class="block w-full py-2 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 font-bold rounded-xl text-center transition-all">
                    <i class="fa-solid fa-headset text-sky-600 mr-1"></i> Tanya CS / Pengaduan
                </a>
            </div>
        </div>
    </div>

</div>


{{-- 3. BOTTOM SHEET: GANTI LOKASI --}}
<div id="sheetLocation" class="fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto bg-white rounded-t-3xl shadow-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[70vh] overflow-hidden">
    
    <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeAllMobileSheets()">
        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
    </div>

    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-sm font-extrabold text-slate-900">Pilih Area Layanan</h3>
        <button type="button" onclick="closeAllMobileSheets()" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <div class="p-5 overflow-y-auto space-y-1.5 text-xs">
        @php
            $locations = [
                'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
                'Tangerang & Tangsel', 'Bekasi Kota & Kab', 'Depok', 'Bogor'
            ];
        @endphp
        @foreach($locations as $loc)
            <button type="button" onclick="selectLocation('{{ $loc }}')" class="w-full text-left px-4 py-3 rounded-xl hover:bg-sky-50 text-slate-800 font-medium flex items-center justify-between border border-transparent hover:border-sky-200 transition-all">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-slate-400"></i> {{ $loc }}
                </span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-[10px]"></i>
            </button>
        @endforeach
    </div>

</div>


{{-- 4. BOTTOM SHEET: NOTIFIKASI --}}
<div id="sheetNotif" class="fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto bg-white rounded-t-3xl shadow-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[75vh] overflow-hidden">
    
    <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeAllMobileSheets()">
        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
    </div>

    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-bell text-sky-600"></i> Notifikasi
        </h3>
        <button type="button" onclick="closeAllMobileSheets()" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <div class="p-5 overflow-y-auto space-y-2.5 text-xs">
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-emerald-700">Pemberitahuan Sistem</span>
                <span class="text-[9px] text-slate-400">1 jam lalu</span>
            </div>
            <p class="text-slate-800 text-[11px] leading-snug">Mitra SayaBantu kini aktif 24 jam untuk melayani kebutuhan mendesak Anda.</p>
        </div>
    </div>

</div>


{{-- 5. BOTTOM SHEET: DAFTAR MITRA (BOCAH SAYABANTU) --}}
<div id="sheetDaftarMitra" class="fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto bg-white rounded-t-3xl shadow-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[85vh] overflow-hidden">
    
    <div class="pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeAllMobileSheets()">
        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
    </div>

    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Gabung Mitra SayaBantu</h3>
                <span class="text-[10px] text-slate-500">Raih penghasilan jutaan rupiah per bulan</span>
            </div>
        </div>
        <button type="button" onclick="closeAllMobileSheets()" class="text-slate-400 hover:text-slate-600 p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <div class="p-5 overflow-y-auto space-y-3.5 text-xs">
        <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap Sesuai KTP</label>
            <input type="text" id="mitraNama" placeholder="Contoh: Budi Santoso" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Handphone Aktif</label>
            <input type="tel" id="mitraWa" placeholder="0812..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Keahlian Utama</label>
            <select id="mitraKeahlian" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="Bersih-bersih & Housekeeping">Bersih-bersih & Housekeeping</option>
                <option value="Angkut Barang & Pindahan">Angkut Barang & Pindahan</option>
                <option value="Driver & Antar Jemput">Driver & Antar Jemput</option>
                <option value="Suruhan Harian">Suruhan Harian</option>
                <option value="Suruhan Serbaguna (All Rounder)">Suruhan Serbaguna (All Rounder)</option>
            </select>
        </div>

        <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Wilayah Domisili</label>
            <input type="text" id="mitraDomisili" placeholder="Contoh: Jakarta Selatan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <button type="button" onclick="submitDaftarMitraAction()" class="w-full py-3 rounded-2xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-center shadow-lg transition-all">
            Kirim Formulir Pendaftaran
        </button>
    </div>

</div>


{{-- DYNAMIC TOAST POPUP --}}
<div id="mobileToast" class="fixed top-6 left-1/2 transform -translate-x-1/2 max-w-[360px] w-full px-4 z-50 pointer-events-none transition-all duration-300 opacity-0 -translate-y-4">
    <div class="bg-slate-900/95 text-white backdrop-blur-md rounded-2xl p-3 shadow-2xl border border-white/20 flex items-center gap-3">
        <div id="toastIcon" class="w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs">
            <i class="fa-solid fa-info"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h5 id="toastTitle" class="text-xs font-bold leading-tight truncate">Info</h5>
            <p id="toastMessage" class="text-[11px] text-slate-300 leading-snug mt-0.5 truncate">Pesan informasi</p>
        </div>
    </div>
</div>

<script>
    // Bottom Sheet Helpers
    function openBottomSheet(sheetId) {
        document.getElementById('mobileModalBackdrop').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('mobileModalBackdrop').classList.add('opacity-100');
            const sheet = document.getElementById(sheetId);
            if (sheet) {
                sheet.classList.remove('translate-y-full');
            }
        }, 10);
    }

    function closeAllMobileSheets() {
        const sheets = [
            'sheetKonfirmasiPesanan', 'sheetLacakPesanan', 
            'sheetLocation', 'sheetNotif', 'sheetDaftarMitra'
        ];
        sheets.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('translate-y-full');
        });
        
        const backdrop = document.getElementById('mobileModalBackdrop');
        backdrop.classList.remove('opacity-100');
        setTimeout(() => {
            backdrop.classList.add('hidden');
        }, 300);
    }

    function openLocationSheet() { openBottomSheet('sheetLocation'); }
    function openNotifSheet() { openBottomSheet('sheetNotif'); }
    function openLacakPesananModal() { openBottomSheet('sheetLacakPesanan'); }
    function openDaftarMitraModal() { openBottomSheet('sheetDaftarMitra'); }

    function selectLocation(loc) {
        document.getElementById('currentLocationText').innerText = loc;
        closeAllMobileSheets();
        showCustomerToast('Lokasi Dipilih', `Area disetel ke ${loc}`, 'success');
    }

    function lacakOrderAction() {
        const val = document.getElementById('inputCekKode').value.trim();
        if (!val) {
            showCustomerToast('Perhatian', 'Silakan masukkan kode pesanan', 'warning');
            return;
        }
        showCustomerToast('Pencarian Status', `Mengecek status tiket ${val}...`, 'info');
    }

    function submitDaftarMitraAction() {
        const nama = document.getElementById('mitraNama').value.trim();
        const wa = document.getElementById('mitraWa').value.trim();
        if (!nama || !wa) {
            showCustomerToast('Perhatian', 'Mohon lengkapi nama dan nomor handphone', 'warning');
            return;
        }
        closeAllMobileSheets();
        showCustomerToast('Pendaftaran Berhasil', `Terima kasih ${nama}, tim verifikasi SayaBantu akan menghubungi Anda!`, 'success');
    }

    // Dynamic Toast
    let toastTimeout;
    function showCustomerToast(title, msg, type = 'info') {
        const toast = document.getElementById('mobileToast');
        const titleEl = document.getElementById('toastTitle');
        const msgEl = document.getElementById('toastMessage');
        const iconEl = document.getElementById('toastIcon');

        titleEl.innerText = title;
        msgEl.innerText = msg;

        if (type === 'success') {
            iconEl.className = 'w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 text-xs';
            iconEl.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
        } else if (type === 'warning') {
            iconEl.className = 'w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-xs';
            iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
        } else {
            iconEl.className = 'w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs';
            iconEl.innerHTML = '<i class="fa-solid fa-info"></i>';
        }

        toast.classList.remove('opacity-0', '-translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', '-translate-y-4');
        }, 3200);
    }
</script>

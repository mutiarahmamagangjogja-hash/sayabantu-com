{{-- =========================================================
     CUSTOMER MODALS & INTERACTIVE OVERLAYS - SAYABANTU.COM
     Modal Sukses Buat Suruhan, Modal Daftar Mitra, Modal Lacak
========================================================= --}}

{{-- 1. MODAL SUKSES AJUKAN SURUHAN --}}
<div id="modalSuruhanSukses" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 invisible transition-all duration-200">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-200 p-6 text-center">
        
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-4 border border-emerald-100">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h3 class="text-lg font-extrabold text-slate-900">
            Suruhan Berhasil Diajukan!
        </h3>
        <p class="text-xs text-slate-500 mt-1">
            Pesanan Anda telah masuk ke sistem kami dan sedang disiarkan ke mitra terdekat.
        </p>

        {{-- KARTU RINGKASAN PESANAN --}}
        <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left text-xs space-y-2">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <span class="text-[10px] text-slate-400 font-semibold">KODE SURUHAN:</span>
                <span id="modalOrderCode" class="font-mono font-bold text-sky-700">#SB-2026-095</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Layanan:</span>
                <strong id="modalOrderService" class="text-slate-800">Full Home Cleaning</strong>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Jadwal:</span>
                <span id="modalOrderSchedule" class="text-slate-700">Hari ini, 15:30 WIB</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Estimasi Budget:</span>
                <strong id="modalOrderBudget" class="text-emerald-600">Rp 150.000</strong>
            </div>
            <div class="flex items-center justify-between pt-1 text-[10px] text-slate-400">
                <span>Status:</span>
                <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    Mencari Mitra Terdekat...
                </span>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-2">
            <a 
                id="orderConfirmationLink"
                href="{{ route('customer.pesanan') }}" 
                class="w-full py-3 px-4 rounded-2xl bg-[#0E1D31] hover:bg-[#173B67] text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-2"
            >
                <i class="fa-solid fa-list-check text-sm"></i> Pantau di Menu Pesanan
            </a>
            
            <button 
                type="button" 
                onclick="closeModal('modalSuruhanSukses')"
                class="w-full py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
            >
                Tutup Jendela
            </button>
        </div>

    </div>
</div>

{{-- 2. MODAL GABUNG JADI MITRA --}}
<div id="modalDaftarMitra" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 invisible transition-all duration-200">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-200 p-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-[#173B67] flex items-center justify-center text-base border border-sky-100">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Gabung Jadi Mitra SayaBantu</h3>
                    <p class="text-[10px] text-slate-500">Raih penghasilan harian dan jam kerja fleksibel</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalDaftarMitra')" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <form id="formDaftarMitra" onsubmit="handleDaftarMitra(event)" class="mt-4 space-y-3 text-xs">
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500 transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Handphone</label>
                    <input type="tel" required placeholder="0812-xxxx-xxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kota Domisili</label>
                    <select required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500 transition-colors">
                        <option value="">Pilih Kota</option>
                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                        <option value="Jakarta Pusat">Jakarta Pusat</option>
                        <option value="Jakarta Barat">Jakarta Barat</option>
                        <option value="Jakarta Timur">Jakarta Timur</option>
                        <option value="Tangerang Selatan">Tangerang Selatan</option>
                        <option value="Bekasi">Bekasi</option>
                        <option value="Depok">Depok</option>
                        <option value="Bogor">Bogor</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Keahlian / Kategori Layanan</label>
                <select required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-500 transition-colors">
                    <option value="">Pilih Kategori Utama</option>
                    <option value="Cleaning Service">Cleaning Service (Kebersihan Rumah)</option>
                    <option value="Angkut Barang / Pindahan">Angkut Barang & Pindahan</option>
                    <option value="Antar / Kurir Kilat">Antar / Kurir Kilat</option>
                    <option value="Suruhan Harian">Suruhan Harian</option>
                </select>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <button type="button" onclick="closeModal('modalDaftarMitra')" class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitMitra" class="flex-1 py-2.5 rounded-xl bg-[#173B67] hover:bg-[#1E4E8C] text-white font-bold transition-all shadow-md flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Pendaftaran
                </button>
            </div>
        </form>

    </div>
</div>

{{-- 3. MODAL LACAK PESANAN --}}
<div id="modalLacakPesanan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 invisible transition-all duration-200">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-200 p-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base border border-sky-100">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Lacak Status Suruhan</h3>
                    <p class="text-[10px] text-slate-500">Cek proses penugasan mitra secara langsung</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalLacakPesanan')" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <div class="mt-4 space-y-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Pesanan</label>
                <div class="flex gap-2">
                    <input type="text" id="inputLacakKode" placeholder="Contoh: #SB1024 atau #SB-2026-089" value="#SB1024" class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono focus:bg-white focus:outline-none focus:border-sky-500">
                    <button type="button" onclick="lacakPesananAction()" class="px-4 py-2 rounded-xl bg-[#173B67] text-white text-xs font-bold hover:bg-[#1E4E8C] transition-colors">
                        Lacak
                    </button>
                </div>
            </div>

            {{-- HASIL STATUS --}}
            <div id="hasilLacakBox" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-slate-800" id="lacakNomor">#SB1024</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-sky-100 text-sky-800 border border-sky-200" id="lacakStatus">
                        Diproses
                    </span>
                </div>
                <p class="text-slate-600 text-[11px]" id="lacakDetail">
                    Layanan <strong>Bersihkan Rumah</strong> • Mitra Budi Santoso sedang dalam perjalanan menuju lokasi Anda.
                </p>
                <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-[10px] text-slate-400">
                    <span>Estimasi Sampai: <strong class="text-slate-700">15 Menit</strong></span>
                    <a href="{{ route('admin.pesanan.detail', 1024) }}" class="text-sky-600 font-bold hover:underline">Lihat di Panel</a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- 4. MODAL NOTIFIKASI PENGUMUMAN CUSTOMER --}}
<div id="modalNotifikasiCustomer" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm opacity-0 invisible transition-all duration-200">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform scale-95 transition-transform duration-200 p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bell text-sky-600 text-sm"></i>
                <h3 class="text-sm font-bold text-slate-900">Pemberitahuan Terkini</h3>
            </div>
            <button type="button" onclick="closeModal('modalNotifikasiCustomer')" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
        <div class="mt-4 space-y-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <strong class="text-slate-800">Layanan Aktif 24 Jam</strong>
                <p class="text-slate-500 text-[11px]">Mitra kami siaga melayani pembersihan darurat dan angkut barang setiap hari pukul 07:00 - 24:00 WIB.</p>
            </div>
        </div>
    </div>
</div>

{{-- 5. TOAST NOTIFICATION CONTAINER --}}
<div id="customerToastContainer" class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] w-[90%] max-w-sm pointer-events-none flex flex-col gap-2 items-center"></div>

{{-- SCRIPT INTERAKSI MODAL & GLOBAL HELPERS --}}
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('opacity-0', 'invisible');
            const inner = modal.querySelector('.transform');
            if (inner) inner.classList.remove('scale-95');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            const inner = modal.querySelector('.transform');
            if (inner) inner.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('opacity-0', 'invisible');
            }, 150);
        }
    }

    function toggleMobileMenu() {
        const drawer = document.getElementById('mobileMenuDrawer');
        if (drawer) {
            drawer.classList.toggle('hidden');
        }
    }

    function openModalMitra() {
        openModal('modalDaftarMitra');
    }

    function openModalLacak() {
        openModal('modalLacakPesanan');
    }

    function toggleCustomerNotifModal() {
        openModal('modalNotifikasiCustomer');
    }

    function showCustomerToast(title, message, type = 'info') {
        const container = document.getElementById('customerToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        let iconHtml = '<i class="fa-solid fa-circle-info text-sky-400"></i>';
        let borderColor = 'border-sky-500/40';

        if (type === 'success') {
            iconHtml = '<i class="fa-solid fa-circle-check text-emerald-400"></i>';
            borderColor = 'border-emerald-500/40';
        } else if (type === 'warning') {
            iconHtml = '<i class="fa-solid fa-triangle-exclamation text-amber-400"></i>';
            borderColor = 'border-amber-500/40';
        }

        toast.className = `pointer-events-auto p-3.5 rounded-2xl bg-[#0E1D31] text-white shadow-2xl border ${borderColor} flex items-start gap-3 w-full transition-all duration-300 opacity-0 -translate-y-2`;
        toast.innerHTML = `
            <div class="text-base mt-0.5">${iconHtml}</div>
            <div class="min-w-0 flex-1">
                <h4 class="text-xs font-bold leading-none">${title}</h4>
                <p class="text-[11px] text-slate-300 mt-1 leading-snug">${message}</p>
            </div>
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', '-translate-y-2');
        });

        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    function handleCustomerSearch(keyword) {
        if (!keyword) return;
        const lower = keyword.toLowerCase();
        // Cek jika menekan enter
        document.querySelectorAll('.item-adajob, .item-category').forEach(el => {
            const text = el.textContent.toLowerCase();
            if (text.includes(lower)) {
                el.style.opacity = '1';
                el.style.transform = 'scale(1.03)';
            } else {
                el.style.opacity = '0.4';
                el.style.transform = 'scale(1)';
            }
        });
    }

    function handleDaftarMitra(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitMitra');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-xs"></i> Mengirim Data...';

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> Kirim Pendaftaran';
            closeModal('modalDaftarMitra');
            showCustomerToast('Pendaftaran Terkirim', 'Terima kasih! Tim SayaBantu akan memverifikasi pendaftaran Anda dalam 1x24 jam.', 'success');
            document.getElementById('formDaftarMitra').reset();
        }, 1200);
    }

    function lacakPesananAction() {
        const kode = document.getElementById('inputLacakKode').value.trim();
        const lacakNomor = document.getElementById('lacakNomor');
        const lacakStatus = document.getElementById('lacakStatus');
        const lacakDetail = document.getElementById('lacakDetail');

        if (!kode) {
            showCustomerToast('Input Kosong', 'Silakan ketik nomor pesanan Anda.', 'warning');
            return;
        }

        lacakNomor.textContent = kode.toUpperCase();
        lacakStatus.textContent = 'Diproses';
        lacakDetail.innerHTML = `Pesanan <strong>${kode.toUpperCase()}</strong> sedang ditangani mitra. Anda akan dihubungi oleh mitra kami.`;
        showCustomerToast('Status Ditemukan', `Status untuk pesanan ${kode.toUpperCase()} berhasil dimuat.`, 'success');
    }
</script>

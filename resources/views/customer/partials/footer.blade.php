{{-- =========================================================
     CUSTOMER FOOTER - SAYABANTU.COM
     Lengkap dengan Sosial Media, Kontak CS, & Link Navigasi
========================================================= --}}

<footer class="bg-[#0E1D31] text-white border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            
            {{-- KOLOM 1: TENTANG SAYABANTU --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#173B67] flex items-center justify-center text-white shadow-md border border-sky-400/30">
                        <i class="fa-solid fa-handshake-angle text-sky-400"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white">
                        SayaBantu<span class="text-sky-400">.com</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Platform penyedia jasa untuk kebutuhan harian seperti kebersihan, pindahan, pengantaran, dan suruhan umum.
                </p>
                <div class="flex items-center gap-3 pt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        CS Siap 24/7
                    </span>
                    <span class="text-[11px] text-slate-400">
                        Jam: 07:00 - 24:00 WIB
                    </span>
                </div>
            </div>

            {{-- KOLOM 2: LAYANAN UTAMA --}}
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 text-sky-400">
                    Layanan Populer
                </h4>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li>
                        <a href="#form-suruhan" onclick="selectServiceFromCard('Full Home Cleaning', 'Bersihkan seluruh ruangan rumah atau apartemen secara menyeluruh')" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-broom text-[10px] text-sky-400"></i> Full Home Cleaning
                        </a>
                    </li>
                    <li>
                        <a href="#form-suruhan" onclick="selectServiceFromCard('Pindahan / Angkut Barang', 'Jasa angkut barang pindahan kos atau rumah dengan aman')" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-truck-ramp-box text-[10px] text-sky-400"></i> Pindahan & Angkut Barang
                        </a>
                    </li>
                    <li>
                        <a href="#form-suruhan" onclick="selectServiceFromCard('Antar / Jemput Barang & Dokumen', 'Pengantaran paket atau dokumen kilat hari ini sampai')" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-motorcycle text-[10px] text-sky-400"></i> Antar / Jemput Kilat
                        </a>
                    </li>
                    <li>
                        <a href="#form-suruhan" onclick="selectServiceFromCard('Buang Sampah & Puing', 'Pengangkutan sampah besar atau sisa puing renovasi')" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-trash-can text-[10px] text-sky-400"></i> Buang Sampah & Puing
                        </a>
                    </li>
                </ul>
            </div>

            {{-- KOLOM 3: BANTUAN & INFORMASI --}}
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 text-sky-400">
                    Bantuan & Navigasi
                </h4>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li>
                        <button type="button" onclick="openModalLacak()" class="hover:text-white transition-colors flex items-center gap-2 text-left">
                            <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Lacak Status Pesanan
                        </button>
                    </li>
                    <li>
                        <button type="button" onclick="openModalMitra()" class="hover:text-white transition-colors flex items-center gap-2 text-left">
                            <i class="fa-solid fa-user-plus text-[10px]"></i> Pendaftaran Mitra Baru
                        </button>
                    </li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-[10px] text-sky-400"></i> Portal Panel Admin
                        </a>
                    </li>
                    <li>
                        <a href="#testimoni" class="hover:text-white transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-comments text-[10px]"></i> Ulasan Pelanggan
                        </a>
                    </li>
                    <li>
                        <button type="button" onclick="showCustomerToast('Syarat & Ketentuan', 'Layanan SayaBantu beroperasi legal dan diawasi ketentuan perlindungan konsumen.', 'info')" class="hover:text-white transition-colors flex items-center gap-2 text-left">
                            <i class="fa-solid fa-file-contract text-[10px]"></i> Syarat & Ketentuan
                        </button>
                    </li>
                </ul>
            </div>

            {{-- KOLOM 4: HUBUNGI KAMI --}}
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 text-sky-400">
                    Hubungi Layanan
                </h4>
                <p class="text-xs text-slate-400">
                    Punya pertanyaan atau butuh bantuan mendadak? Hubungi kami langsung:
                </p>
                <div class="space-y-2">
                    <a href="{{ route('customer.pengaduan') }}" class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 border border-sky-500/30 text-xs font-bold transition-colors w-full">
                        <i class="fa-solid fa-headset text-sm text-sky-400"></i>
                        <span>Pusat Layanan &amp; Pengaduan CS</span>
                    </a>
                    <div class="flex items-center gap-2 text-xs text-slate-400 px-1">
                        <i class="fa-regular fa-envelope text-slate-400"></i>
                        <span>support@sayabantu.com</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- BARIS HAK CIPTA & SOSIAL MEDIA --}}
        <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                © 2026 <strong>SayaBantu.com</strong> - All Rights Reserved. Terinspirasi dari konsep suruhan serbaguna.
            </div>
            <div class="flex items-center gap-3">
                <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#173B67] hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-brands fa-facebook-f text-xs"></i>
                </a>
                <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#173B67] hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-brands fa-tiktok text-xs"></i>
                </a>
                <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#173B67] hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-brands fa-instagram text-xs"></i>
                </a>
            </div>
        </div>

    </div>
</footer>

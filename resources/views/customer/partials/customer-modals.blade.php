{{-- =========================================================================
     SAYABANTU.COM - CUSTOMER & MITRA MODALS COMPONENT
     - Modal Login Dual-Role: Customer & Mitra
     - Modal Profil Customer
     - Modal Profil Mitra (Bocah SayaBantu)
     - Modal Detail Mitra / Bocah SayaBantu dengan Foto & Verifikasi
     - Modal Notifikasi & Lacak Suruhan
     - Modal Gated Notice (Hanya Muncul Jika Sudah Login)
========================================================================== --}}

{{-- BACKDROP OVERLAY --}}
<div id="customerBackdrop" onclick="closeAllCustomerModals()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden transition-opacity duration-300"></div>

{{-- =====================================================
     1. MODAL LOGIN DUAL-ROLE (CUSTOMER & MITRA)
====================================================== --}}
<div id="modalLoginCustomer" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[92%] sm:w-full max-w-[400px] mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80 max-h-[90vh] overflow-y-auto">
    
    {{-- MODAL HEADER --}}
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-sky-400/20 text-sky-300 flex items-center justify-center font-bold text-sm border border-sky-400/30">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold leading-tight" id="loginModalTitle">Masuk ke SayaBantu</h3>
                    <p class="text-[10.5px] text-slate-300">Pilih peran akun Anda untuk melanjutkan</p>
                </div>
            </div>
            <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- TAB SWITCHER: CUSTOMER VS MITRA (KONSISTEN & KONTRAS TINGGI) --}}
        <div class="grid grid-cols-2 gap-1.5 p-1 bg-black/25 rounded-xl mt-3 text-xs font-bold">
            <button
                type="button"
                id="tabBtnCustomer"
                onclick="switchLoginRole('customer')"
                class="py-1.5 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5 cursor-pointer font-bold"
            >
                <i class="fa-solid fa-user text-[11px]"></i> Customer
            </button>
            <button
                type="button"
                id="tabBtnMitra"
                onclick="switchLoginRole('mitra')"
                class="py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 cursor-pointer font-bold"
            >
                <i class="fa-solid fa-helmet-safety text-[11px]"></i> Mitra
            </button>
        </div>
    </div>

    {{-- MODAL BODY --}}
    <div class="p-4 space-y-3.5 text-xs text-slate-700">

        {{-- KONTEN ROLE: CUSTOMER --}}
        <div id="roleContentCustomer" class="space-y-3">
            <div id="loginCustomerModalAlert" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] leading-snug"></div>
            <form onsubmit="handleManualCustomerLogin(event)" class="space-y-2.5">
                <div>
                    <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Nomor Handphone</label>
                    <input 
                        type="tel" 
                        id="inputLoginCustomerPhone" 
                        placeholder="Contoh: 081234567890" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                        required
                    >
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10.5px] font-bold text-slate-600">Kata Sandi</label>
                        <a href="javascript:void(0)" onclick="handleForgotPasswordModal()" class="text-[10px] font-bold text-sky-600 hover:text-sky-700 hover:underline cursor-pointer">
                            Lupa Kata Sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="inputLoginCustomerPass" 
                            placeholder="Masukkan kata sandi" 
                            class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="toggleCustomerPassword('inputLoginCustomerPass', this)" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 focus:outline-none"
                            tabindex="-1"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                        >
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <button 
                    type="submit" 
                    class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs shadow-sm transition-all cursor-pointer"
                >
                    Masuk Akun Customer
                </button>
            </form>

            {{-- DIVIDER --}}
            <div class="flex items-center gap-2 text-slate-400 text-[10px]">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span>atau</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            {{-- MASUK DENGAN GOOGLE --}}
            <button
                type="button"
                onclick="handleGoogleAuth('masuk')"
                class="w-full py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs shadow-xs flex items-center justify-center gap-2.5 transition-all cursor-pointer"
            >
                <i class="fa-brands fa-google text-[14px] text-rose-500"></i> Masuk dengan Google
            </button>

            {{-- TAUTAN DAFTAR HANYA UNTUK CUSTOMER (TANPA GARIS BAWAH DAN TANPA UNDERLINE) --}}
            <div class="pt-2 text-center text-[11.5px]">
                <p class="text-slate-500">
                    Belum punya akun?
                    <button type="button" onclick="closeAllCustomerModals(); openModal('modalDaftarCustomer'); switchDaftarRole('customer');" class="text-sky-600 font-bold hover:text-sky-700 cursor-pointer ml-1">Daftar</button>
                </p>
            </div>
        </div>

        {{-- KONTEN ROLE: MITRA --}}
        <div id="roleContentMitra" class="hidden space-y-3">
            <div id="loginMitraModalAlert" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] leading-snug"></div>
            <form onsubmit="handleManualMitraLogin(event)" class="space-y-2.5">
                <div>
                    <label class="block text-[10.5px] font-bold text-slate-600 mb-1">ID Mitra / No. Handphone</label>
                    <input
                        type="text"
                        id="inputLoginMitraPhone"
                        placeholder="Contoh: #MTR-0042 atau 081398765432"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                        required
                    >
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10.5px] font-bold text-slate-600">PIN / Kata Sandi Mitra</label>
                        <a href="javascript:void(0)" onclick="handleForgotPasswordModal()" class="text-[10px] font-bold text-emerald-700 hover:text-emerald-800 cursor-pointer">
                            Lupa PIN?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="inputLoginMitraPass"
                            placeholder="Masukkan PIN / Sandi"
                            class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="toggleCustomerPassword('inputLoginMitraPass', this)" 
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 focus:outline-none"
                            tabindex="-1"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                        >
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition-all cursor-pointer"
                >
                    Masuk Portal Mitra
                </button>
            </form>

            <div class="pt-1.5 text-center text-[11px]">
                <p class="text-slate-600">
                    Tertarik cari penghasilan fleksibel?
                    <button type="button" onclick="closeAllCustomerModals(); openModal('modalDaftarCustomer'); switchDaftarRole('mitra');" class="text-emerald-700 font-bold hover:text-emerald-800 cursor-pointer ml-1">Daftar Jadi Mitra SayaBantu »</button>
                </p>
            </div>
        </div>

    </div>

</div>


{{-- =====================================================
     1B. MODAL DAFTAR (HALAMAN DAFTAR)
====================================================== --}}
<div id="modalDaftarCustomer" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[92%] sm:w-full max-w-[400px] mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80 max-h-[90vh] overflow-y-auto">

    {{-- MODAL HEADER --}}
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-sky-400/20 text-sky-300 flex items-center justify-center font-bold text-sm border border-sky-400/30">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold leading-tight" id="daftarModalTitle">Daftar Akun SayaBantu</h3>
                    <p class="text-[10.5px] text-slate-300">Pilih jenis pendaftaran yang diinginkan</p>
                </div>
            </div>
            <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- TAB SWITCHER: CUSTOMER VS MITRA (KONSISTEN & KONTRAS TINGGI) --}}
        <div class="grid grid-cols-2 gap-1.5 p-1 bg-black/25 rounded-xl mt-3 text-xs font-bold">
            <button
                type="button"
                id="tabBtnDaftarCustomer"
                onclick="switchDaftarRole('customer')"
                class="py-1.5 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5 cursor-pointer font-bold"
            >
                <i class="fa-solid fa-user text-[11px]"></i> Customer
            </button>
            <button
                type="button"
                id="tabBtnDaftarMitra"
                onclick="switchDaftarRole('mitra')"
                class="py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 cursor-pointer font-bold"
            >
                <i class="fa-solid fa-helmet-safety text-[11px]"></i> Mitra
            </button>
        </div>
    </div>

    {{-- MODAL BODY --}}
    <div class="p-4 space-y-3 text-xs text-slate-700">

        {{-- 1. KONTEN DAFTAR CUSTOMER (STEP SYSTEM) --}}
        <div id="roleContentDaftarCustomer" class="space-y-3">

            {{-- STEP 1: FORMULIR DATA & PASSWORD --}}
            <div id="registerStep1" class="space-y-3">
                <form onsubmit="handleRegisterStep1(event)" class="space-y-2.5">
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Nama Lengkap</label>
                        <input
                            type="text"
                            id="inputDaftarNama"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Nomor Handphone</label>
                        <input
                            type="tel"
                            id="inputDaftarPhone"
                            placeholder="Contoh: 081234567890"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Kata Sandi</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="inputDaftarPass"
                                placeholder="Minimal 6 karakter"
                                class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                                required
                                oninput="validatePasswordMatch()"
                            >
                            <button 
                                type="button" 
                                onclick="toggleCustomerPassword('inputDaftarPass', this)" 
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 focus:outline-none"
                                tabindex="-1"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                            >
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="inputDaftarPassConfirm"
                                placeholder="Ulangi kata sandi"
                                class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                                required
                                oninput="validatePasswordMatch()"
                            >
                            <button 
                                type="button" 
                                onclick="toggleCustomerPassword('inputDaftarPassConfirm', this)" 
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 focus:outline-none"
                                tabindex="-1"
                                aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi"
                            >
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                        {{-- Validasi teks instan --}}
                        <div id="passMatchIndicator" class="text-[10px] font-semibold mt-1 hidden"></div>
                    </div>

                    {{-- Checkbox Syarat & Ketentuan SayaBantu --}}
                    <div class="flex items-start gap-2 pt-0.5">
                        <input 
                            type="checkbox" 
                            id="checkTermsCustomer" 
                            class="mt-0.5 w-4 h-4 rounded text-[#0E1D31] border-slate-300 focus:ring-[#173B67] cursor-pointer"
                            required
                        >
                        <label for="checkTermsCustomer" class="text-[10.5px] text-slate-600 leading-tight select-none">
                            Dengan mendaftar, Anda menyetujui <a href="{{ route('customer.syarat-ketentuan') }}" target="_blank" class="text-sky-600 font-bold hover:underline">Syarat &amp; Ketentuan</a> serta <a href="{{ route('customer.kebijakan-privasi') }}" target="_blank" class="text-sky-600 font-bold hover:underline">Kebijakan Privasi</a> SayaBantu.
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span>Daftar Akun Customer</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </form>

                {{-- DIVIDER --}}
                <div class="flex items-center gap-2 text-slate-400 text-[10px]">
                    <div class="flex-1 h-px bg-slate-200"></div>
                    <span>atau</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                {{-- DAFTAR DENGAN GOOGLE --}}
                <button
                    type="button"
                    onclick="handleGoogleAuth('daftar')"
                    class="w-full py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-bold text-xs shadow-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                >
                    <i class="fa-brands fa-google text-[14px] text-rose-500"></i> Daftar dengan Google
                </button>
            </div>

        </div>

        {{-- 2. KONTEN DAFTAR MITRA (PROGRAM KEMITRAAN SAYABANTU) --}}
        <div id="roleContentDaftarMitra" class="hidden space-y-3">
            <div class="bg-gradient-to-br from-emerald-50 via-teal-50/50 to-white p-3.5 rounded-2xl border border-emerald-200 shadow-2xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[8.5px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Mitra SayaBantu
                    </span>
                    <span class="text-[9px] font-bold text-emerald-700 flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-[8px]"></i> Lolos Seleksi &amp; SKCK
                    </span>
                </div>
                <h4 class="text-xs font-black text-slate-900 leading-tight">
                    Daftar Sebagai Mitra Pekerja Lepas
                </h4>
                <p class="text-[10px] text-slate-600 leading-relaxed">
                    Dapatkan penghasilan harian fleksibel. Terima order cleaning, angkut barang, servis AC, belanja, dan pertukangan.
                </p>
            </div>

            {{-- Berkas & Dokumen Jaminan yang Dibutuhkan --}}
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2 text-[10px]">
                <div class="flex items-center justify-between font-bold text-slate-800 text-[10.5px]">
                    <span>Berkas &amp; Jaminan yang Wajib Diunggah:</span>
                    <span class="text-emerald-700 text-[9px]">4 Dokumen</span>
                </div>
                <div class="space-y-1.5 text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[8px] font-bold shrink-0">1</span>
                        <span>Foto <strong>e-KTP Asli</strong> &amp; Foto Selfie Memegang KTP</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[8px] font-bold shrink-0">2</span>
                        <span>Foto <strong>Surat SKCK</strong> / Surat Domisili RT/RW</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-[8px] font-bold shrink-0">3</span>
                        <span><strong>Foto Dokumen Jaminan Fisik</strong> (BPKB Motor / Ijazah Asli / KK)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[8px] font-bold shrink-0">4</span>
                        <span>Nomor Rekening Bank / E-Wallet Pencairan Saldo</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Buka Formulir Lengkap Registrasi Mitra --}}
            <a
                href="{{ route('mitra.daftar') }}"
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all cursor-pointer"
            >
                <i class="fa-solid fa-file-shield text-xs"></i>
                <span>Mulai Registrasi Mitra &amp; Upload Berkas »</span>
            </a>

            <p class="text-[9.5px] text-slate-400 text-center leading-snug">
                *Dokumen jaminan disimpan aman di brankas SayaBantu &amp; diserah-terimakan dengan tanda terima resmi bermaterai.
            </p>
        </div>

    </div>

    {{-- FOOTER MODAL --}}
    <div class="px-4 pb-4 text-center text-[11.5px] pt-1.5">
        <p class="text-slate-500">
            Sudah punya akun?
            <button type="button" onclick="closeAllCustomerModals(); openModal('modalLoginCustomer');" class="text-sky-600 font-bold hover:text-sky-700 cursor-pointer ml-1">Masuk</button>
        </p>
    </div>

</div>


{{-- =====================================================
     2. MODAL PROFIL CUSTOMER (KETIKA SUDAH LOGIN CUSTOMER)
====================================================== --}}
<div id="modalProfileCustomer" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80">
    
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white font-black text-base flex items-center justify-center shadow-md" id="profAvatarInitial">
                    {{ strtoupper(substr(Auth::user()?->name ?? 'C', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-bold leading-tight truncate" id="profCustomerName">{{ Auth::user()?->name ?? 'Customer' }}</h3>
                    <span class="text-[10px] text-slate-300" id="profCustomerPhone">{{ Auth::user()?->phone ?? '' }}</span>
                </div>
            </div>
            <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="flex items-center gap-1.5 mt-2.5">
            <span class="text-[9px] font-semibold text-emerald-300">● Akun Terverifikasi</span>
        </div>
    </div>

    <div class="p-4 space-y-3 text-xs">

        {{-- IDENTITAS SINGKAT --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i>
                <span class="font-semibold">Nama Lengkap</span>
                <strong class="ml-auto text-slate-900 font-bold" id="profDetailName">{{ Auth::user()?->name ?? 'Customer' }}</strong>
            </div>
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-envelope text-slate-400 w-4 text-center"></i>
                <span class="font-semibold">Email</span>
                <strong class="ml-auto text-slate-900 font-bold truncate" id="profDetailEmail">{{ Auth::user()?->email ?? '' }}</strong>
            </div>
            <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-mobile-screen-button text-slate-400 w-4 text-center"></i>
                <span class="font-semibold">Telepon</span>
                <strong class="ml-auto text-slate-900 font-bold" id="profDetailPhone">{{ Auth::user()?->phone ?? '' }}</strong>
            </div>
        </div>

        {{-- TOMBOL BUKA PROFIL LENGKAP --}}
        <button
            type="button"
            onclick="closeAllCustomerModals(); switchCustomerTab('profile');"
            class="w-full py-2.5 rounded-xl bg-[#173B67] hover:bg-[#0E1D31] text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors"
        >
            <i class="fa-solid fa-id-card"></i> Lihat Profil Lengkap
        </button>

        <div class="pt-1 border-t border-slate-100">
            <button
                type="button"
                onclick="logoutUserAction()"
                class="w-full py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center gap-2 transition-colors"
            >
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
            </button>
        </div>

    </div>

</div>


{{-- =====================================================
     3. MODAL PROFIL MITRA (KETIKA SUDAH LOGIN MITRA)
====================================================== --}}
<div id="modalProfileMitra" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80">
    
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#047857] text-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-full bg-emerald-400 text-slate-950 font-black text-base flex items-center justify-center shadow-md">
                    KL
                </div>
                <div>
                    <h3 class="text-sm font-bold leading-tight" id="profMitraName">Kala</h3>
                    <span class="text-[10px] text-emerald-200 font-mono">#SB-7615 • Mitra SayaBantu</span>
                </div>
            </div>
            <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    </div>

    <div class="p-4 space-y-3 text-xs">
        
        {{-- MITRA EARNINGS & STATUS --}}
        <div class="p-3 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-slate-600 font-medium text-[11px]">Status Kerja:</span>
                <span class="text-[10px] bg-emerald-600 text-white px-2.5 py-0.5 rounded-full font-extrabold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Online Siap Tugas
                </span>
            </div>
            <div class="flex items-center justify-between pt-1 border-t border-emerald-100">
                <span class="text-slate-600 font-medium text-[11px]">Penghasilan Hari Ini:</span>
                <strong class="text-emerald-700 text-sm font-black">Rp 450.000</strong>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-500">
                <span>Rating: ★ 4.9 (142 Tugas)</span>
                <span>Radius: 2.5 km (Menteng)</span>
            </div>
        </div>

        <div class="space-y-1.5 pt-1">
            {{-- CS SUPPORT FOR MITRA --}}
            <a href="{{ route('customer.pengaduan') }}" class="w-full text-left p-3 rounded-xl bg-sky-50 hover:bg-sky-100/80 border border-sky-200 flex items-center justify-between transition-colors text-sky-800">
                <span class="flex items-center gap-2.5 font-bold">
                    <i class="fa-solid fa-headset text-sky-600 w-4 text-base"></i> Hubungi Dispatcher / CS Bantuan
                </span>
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-200/70 text-sky-900">Online</span>
            </a>

            <button type="button" onclick="showCustomerToast('Job Sekitar', '3 tugas baru tersedia di radius Anda.', 'info')" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 border border-slate-200/80 flex items-center justify-between transition-colors">
                <span class="flex items-center gap-2.5 font-bold text-slate-800">
                    <i class="fa-solid fa-list-check text-sky-600 w-4"></i> Tugas Tersedia di Sekitar
                </span>
                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-800">3 Job</span>
            </button>
        </div>

        <div class="pt-2 border-t border-slate-100">
            <button 
                type="button" 
                onclick="logoutUserAction()" 
                class="w-full py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center gap-2 transition-colors"
            >
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar dari Mode Mitra
            </button>
        </div>

    </div>

</div>


{{-- =====================================================
     4. MODAL GATED NOTICE (MUNCUL JIKA TAMU KLIK FITUR TERKUNCI)
====================================================== --}}
<div id="modalWaGatedNotice" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">
    
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold leading-tight">Pusat Layanan Resmi</h3>
                    <p class="text-[10px] text-sky-200">Khusus Customer &amp; Mitra Terdaftar</p>
                </div>
            </div>
            <button type="button" onclick="closeAllCustomerModals()" class="text-sky-200 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    </div>

    <div class="p-4 space-y-3.5 text-xs text-slate-700">
        <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200 flex items-start gap-2.5">
            <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5 text-sm shrink-0"></i>
            <div>
                <strong class="text-xs font-bold text-amber-900 block">Akses Layanan Dilindungi</strong>
                <p class="text-[11px] text-amber-800 leading-relaxed mt-0.5">
                    Demi keamanan data dan kepastian penugasan, layanan Customer Care serta komunikasi mitra hanya terbuka untuk pengguna yang telah masuk sebagai <strong>Customer</strong> atau <strong>Mitra</strong>.
                </p>
            </div>
        </div>

        <div class="space-y-2 pt-1">
            <button 
                type="button" 
                onclick="closeAllCustomerModals(); switchLoginRole('customer'); openModal('modalLoginCustomer');" 
                class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all"
            >
                <i class="fa-solid fa-user"></i> Masuk sebagai Customer
            </button>
            <button 
                type="button" 
                onclick="closeAllCustomerModals(); switchLoginRole('mitra'); openModal('modalLoginCustomer');" 
                class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all"
            >
                <i class="fa-solid fa-helmet-safety"></i> Masuk sebagai Mitra
            </button>
        </div>
    </div>

</div>


{{-- =====================================================
     5. MODAL DETAIL MITRA / BOCAH SAYABANTU (DENGAN FOTO)
====================================================== --}}
<div id="modalDetailBocah" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[400px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">
    
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-sky-400"></i>
            <h3 class="text-sm font-bold">Profil Personil Mitra SayaBantu</h3>
        </div>
        <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <div class="p-4 space-y-3.5 text-xs text-slate-700 max-h-[75vh] overflow-y-auto">
        
        {{-- KARTU FOTO & BIODATA MITRA --}}
        <div class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="relative shrink-0">
                <img 
                    id="bocahDetailPhoto"
                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                    alt="Foto Mitra"
                    class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-md"
                    onerror="this.src='https://ui-avatars.com/api/?name=Mitra+Santo+Suruh&background=173B67&color=fff'"
                >
                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center text-[7px] text-white" title="Online">
                    <i class="fa-solid fa-check"></i>
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="text-sm font-bold text-slate-900 truncate" id="bocahDetailName">Kala</h4>
                <p class="text-[11px] text-sky-700 font-semibold" id="bocahDetailSpecialty">Spesialis: Pindahan & Home Cleaning</p>
                <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-500">
                    <span id="bocahDetailRating" class="text-amber-500 font-bold">★ 4.9 (142 Tugas)</span>
                    <span>•</span>
                    <span id="bocahDetailRegion" class="text-slate-600 font-medium">Jakarta Pusat</span>
                </div>
            </div>
        </div>

        {{-- STATUS PENUGASAN SISTEM (BUKAN KITA YANG MILIH) --}}
        <div class="p-3 bg-blue-50/80 border border-blue-200 rounded-xl space-y-1 text-[11px]">
            <strong class="font-bold text-[#173B67] flex items-center gap-1.5">
                <i class="fa-solid fa-wand-magic-sparkles text-sky-600"></i> Penugasan Otomatis oleh Sistem
            </strong>
            <p class="text-slate-600 leading-snug">
                Mitra ini adalah salah satu personil standby kami. Saat Anda mengajukan suruhan, sistem cerdas SayaBantu akan otomatis mencocokkan mitra terdekat dengan rating terbaik tanpa Anda harus repot memilih manual.
            </p>
        </div>

        {{-- VERIFIKASI IDENTITAS --}}
        <div class="space-y-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Verifikasi & Kredensial Resmi</span>
            <div class="grid grid-cols-2 gap-2 text-[10px]">
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-600"></i>
                    <span class="font-semibold text-slate-800">KTP Terverifikasi</span>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-file-shield text-emerald-600"></i>
                    <span class="font-semibold text-slate-800">SKCK Kepolisian</span>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-heart-pulse text-emerald-600"></i>
                    <span class="font-semibold text-slate-800">Bebas Narkoba</span>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-shield-virus text-emerald-600"></i>
                    <span class="font-semibold text-slate-800">Vaksin Lengkap</span>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="pt-2 border-t border-slate-100 flex gap-2">
            <button 
                type="button" 
                onclick="closeAllCustomerModals(); scrollToOrderForm();" 
                class="flex-1 py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs transition-colors text-center"
            >
                Buat Suruhan Sekarang »
            </button>
            <button 
                type="button" 
                onclick="closeAllCustomerModals()" 
                class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
            >
                Tutup
            </button>
        </div>

    </div>

</div>


{{-- =====================================================
     MODAL DETAIL LAYANAN MODERN (PERSIS SCREEN 2 REFERENSI USER)
====================================================== --}}
<div id="modalDetailServiceModern" class="fixed inset-x-0 bottom-0 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[430px] w-full mx-auto bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200/80 transition-all duration-300">
    
    {{-- TOP HERO IMAGE WITH NAV BUTTONS --}}
    <div class="relative h-60 bg-slate-900 overflow-hidden">
        <img id="detailServiceHeroImg" src="{{ asset('images/technician-card.jpg') }}" alt="Detail Service Hero" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-black/40"></div>
        
        {{-- TOP BAR BUTTONS --}}
        <div class="absolute top-4 inset-x-4 flex items-center justify-between z-10">
            <button type="button" onclick="closeModernServiceDetail()" class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-md text-white hover:bg-white/30 flex items-center justify-center transition-all cursor-pointer" aria-label="Tutup detail layanan">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
            <h4 class="text-sm font-bold text-white tracking-wide">Detail Layanan</h4>
            <button type="button" onclick="toggleFavoriteService(this)" class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-md text-white hover:text-rose-400 hover:bg-white/30 flex items-center justify-center transition-all">
                <i class="fa-regular fa-heart text-sm"></i>
            </button>
        </div>

        {{-- BADGE OVERLAY --}}
        <div class="absolute bottom-3 left-4 z-10">
            <span id="detailServiceBadge" class="px-2.5 py-1 rounded-full bg-sky-500 text-white text-[9px] font-extrabold shadow-sm">
                Top Rated
            </span>
        </div>
    </div>

    {{-- CONTENT BOTTOM SHEET --}}
    <div class="p-4 space-y-3.5 text-xs text-slate-700 max-h-[60vh] overflow-y-auto">
        
        <div>
            <h3 id="detailServiceTitle" class="text-base font-bold text-slate-900 leading-snug">HVAC Maintenance Service</h3>
            <div class="flex items-center gap-2 text-[10.5px] text-slate-500 mt-1">
                <span><i class="fa-solid fa-location-dot text-rose-500"></i> Jabodetabek</span>
                <span>•</span>
                <span class="font-bold flex items-center gap-1">
                    <i class="fa-solid fa-star text-yellow-400 text-[10px]"></i>                     <span>Informasi layanan katalog</span>
                </span>
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div>
            <h4 class="text-[11px] font-bold text-slate-900 mb-1">Deskripsi</h4>
            <p id="detailServiceDesc" class="text-[10.5px] leading-relaxed text-slate-500">
                Pembersihan dan pengecekan menyeluruh oleh teknisi berpengalaman, memastikan kinerja maksimal dan ruangan kembali sejuk.
            </p>
        </div>

        {{-- PILIHAN PAKET: BASIC / STANDARD / PREMIUM --}}
        <div>
            <h4 class="text-[11px] font-bold text-slate-900 mb-1.5">Pilihan Paket</h4>
            <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-xl text-center text-[10.5px] font-bold">
                <button type="button" onclick="selectServicePackage('basic', this)" id="pkgBtnBasic" class="py-1.5 rounded-lg bg-white text-[#173B67] shadow-xs transition-all">
                    Standar
                </button>
                <button type="button" onclick="selectServicePackage('standard', this)" id="pkgBtnStandard" class="py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all">
                    Lengkap
                </button>
                <button type="button" onclick="selectServicePackage('premium', this)" id="pkgBtnPremium" class="py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all">
                    Premium
                </button>
            </div>
            <p id="detailPkgNote" class="text-[9.5px] text-slate-400 mt-1.5 italic">
                Paket standar mencakup pembersihan dan pengerjaan dasar esensial.
            </p>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="pt-2">
            <button
                type="button"
                id="btnSelectServicePackage"
                onclick="confirmSelectedPackageOrder()"
                class="w-full py-3 rounded-2xl bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold text-xs shadow-md shadow-blue-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2"
            >
                <span>Pilih Standar (<span id="detailServiceBtnPrice">Rp 75.000</span>)</span>
            </button>
        </div>

    </div>

</div>

{{-- =====================================================
     6. MODAL KONFIRMASI SURUHAN
====================================================== --}}
<div id="modalKonfirmasiOrder" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[420px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">

    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-400 text-emerald-400 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold leading-tight">Pengajuan Terkirim ke Mitra!</h3>
                <span class="text-[10px] text-sky-200" id="confOrderCode">#SB-SURUH-8492</span>
            </div>
        </div>
        <button type="button" onclick="closeAllCustomerModals()" class="text-slate-300 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <div class="p-4 space-y-3 text-xs max-h-[80vh] overflow-y-auto">

        {{-- STATUS SAAT INI (LANGKAH 4 BERJALAN) --}}
        <div class="p-3 rounded-xl bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-[#173B67] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    Langkah 4: Tampil di Kebutuhan Mitra
                </span>
                <span class="text-[8.5px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-full border border-sky-200">
                    Aktif
                </span>
            </div>
            <p class="text-[9.5px] text-slate-600 leading-snug">
                Pengajuan Anda kini telah diteruskan ke halaman <strong>Kebutuhan Mitra</strong>. Mitra terdekat dapat melihat detail pekerjaan dan memilih untuk mengambil atau menawar pekerjaan ini.
            </p>
        </div>

        {{-- RINGKASAN DATA SURUHAN --}}
        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-2">
            <div class="flex justify-between items-center text-slate-600">
                <span>Layanan:</span>
                <strong class="text-slate-900 font-bold" id="confLayanan">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Jadwal Pengerjaan:</span>
                <span class="text-slate-900 font-semibold" id="confWaktu">-</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Metode &amp; Harga:</span>
                <strong class="text-emerald-600 font-extrabold text-sm" id="confBudget">-</strong>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>Pemesan:</span>
                <span class="text-slate-900 font-semibold" id="confPemesan">-</span>
            </div>
            <div class="pt-2 border-t border-slate-200 text-slate-600">
                <span class="text-[10px] text-slate-400 block mb-0.5">Catatan &amp; Lokasi:</span>
                <p class="text-[11px] text-slate-800 italic" id="confNote">-</p>
            </div>
        </div>

        {{-- STEPPER ALUR 11 TAHAPAN LENGKAP --}}
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-[10px] font-bold text-[#0E1D31]">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-route text-sky-600"></i> Tahapan Alur Pekerjaan
                </span>
                <span class="text-[9px] text-slate-500 font-medium">Langkah 4 dari 11</span>
            </div>

            <div class="space-y-1.5 text-[9.5px]">
                <div class="flex items-center gap-2 text-emerald-700 font-bold">
                    <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0">✓</span>
                    <span>1-3. Form Diisi, Metode Dipilih &amp; Pengajuan Terkirim</span>
                </div>
                <div class="flex items-center gap-2 text-sky-700 font-bold bg-sky-50/80 p-1 rounded-lg border border-sky-200/60">
                    <span class="w-4 h-4 rounded-full bg-sky-600 text-white flex items-center justify-center text-[8px] font-bold shrink-0">4</span>
                    <span class="flex-1">Mitra Melihat Kebutuhan Customer (Sedang Berjalan)</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[8px] shrink-0">5</span>
                    <span>Melakukan Negosiasi / Menyetujui Harga (Fitur Chat)</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[8px] shrink-0">6</span>
                    <span>Menunggu Konfirmasi Admin</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[8px] shrink-0">7</span>
                    <span>Melakukan Pembayaran Garansi Aman</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[8px] shrink-0">8</span>
                    <span>Mitra Melaksanakan Pekerjaan</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[8px] shrink-0">9-11</span>
                    <span>Konfirmasi Selesai, Penerusan Dana &amp; Ulasan</span>
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="space-y-2 pt-1">
            <a id="btnLanjutBayar" href="{{ route('customer.pembayaran', ['kode' => 'SB-SURUH-8492']) }}" class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-extrabold text-center shadow-md flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-forward-step"></i> Lanjut: Tunggu Mitra Menyetujui
            </a>
            <button
                type="button"
                onclick="closeAllCustomerModals(); if(typeof switchCustomerNavTab === 'function') switchCustomerNavTab('dashboard');"
                class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center transition-colors"
            >
                Tutup &amp; Pantau Status Suruhan
            </button>
        </div>

        <p class="text-[9px] text-center text-slate-400 leading-relaxed">
            <i class="fa-solid fa-shield-halved text-[8px] mr-1"></i> Notifikasi chat dan pembaruan status akan dikirimkan otomatis.
        </p>

    </div>

</div>


{{-- =====================================================
     7. MODAL LACAK SURUHAN
====================================================== --}}
<div id="modalLacakPesanan" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">

    <div class="bg-[#0E1D31] text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-sky-400"></i>
            <h3 class="text-sm font-bold">Lacak Status Suruhan</h3>
        </div>
        <button type="button" onclick="closeAllCustomerModals()" class="text-slate-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <div class="p-4 space-y-3 text-xs">
        <div class="flex gap-2">
            <input type="text" id="inputLacakKode" placeholder="Contoh: #SB-SURUH-9012" class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#173B67]">
            <button type="button" onclick="showCustomerToast('Lacak Suruhan', 'Mengecek status tiket suruhan di server...', 'info')" class="px-3.5 py-2 bg-[#173B67] text-white font-bold rounded-xl hover:bg-[#0E1D31]">
                Cek
            </button>
        </div>

        {{-- HASIL LACAK (DINAMIS) --}}
        <div id="boxLacakHasil" class="hidden p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
            <div class="flex justify-between items-start">
                <div>
                    <span id="lacakBadgeStatus" class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Status Suruhan</span>
                    <h4 id="lacakLayananNama" class="font-bold text-slate-900 mt-1">-</h4>
                    <span id="lacakTiketInfo" class="text-[10px] text-slate-500">Tiket: -</span>
                </div>
                <strong id="lacakBiaya" class="text-xs text-slate-900">Rp -</strong>
            </div>

            <div id="lacakProgresList" class="pt-2 border-t border-slate-200 space-y-1.5 text-[10px]">
            </div>
        </div>

        <div id="boxLacakKosong" class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-center space-y-1">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-base"></i>
            <p class="text-[10.5px] font-bold text-slate-700">Lacak Tiket Suruhan</p>
            <p class="text-[9px] text-slate-400">Masukkan nomor tiket pesanan Anda di atas untuk memantau status pengerjaan.</p>
        </div>
    </div>

</div>


{{-- =====================================================
     8. MODAL NOTIFIKASI
====================================================== --}}
<div id="modalNotifCustomer" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[390px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">
    <div class="bg-[#0E1D31] text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-regular fa-bell text-sky-400 text-sm"></i>
            <h3 class="text-sm font-bold">Pusat Notifikasi</h3>
        </div>
        <button type="button" onclick="closeAllCustomerModals()" class="text-slate-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>
    <div class="p-4 space-y-2.5 text-xs max-h-[60vh] overflow-y-auto">
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-700">Mitra di Area Anda</span>
                <span class="text-[9px] text-slate-400">1 jam lalu</span>
            </div>
            <p class="text-[11px] text-slate-800 leading-snug">10+ mitra personil standby di Jabodetabek siap menerima penugasan otomatis.</p>
        </div>
    </div>
</div>


{{-- =====================================================
     8B. MODAL REVIEW / PENILAIAN CUSTOMER
====================================================== --}}
<div id="modalReviewCustomer" class="fixed inset-x-4 bottom-8 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 max-w-[400px] w-full mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200">
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center font-bold text-sm border border-amber-400/30">
                <i class="fa-solid fa-star text-yellow-400"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold leading-tight">Beri Review &amp; Penilaian</h3>
                <p class="text-[10px] text-slate-300">Bagikan pengalaman Anda bersama Mitra SayaBantu</p>
            </div>
        </div>
        <button type="button" onclick="closeAllCustomerModals()" class="text-slate-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <form onsubmit="handleReviewSubmit(event)" class="p-4 space-y-3.5 text-xs text-slate-700">
        <input type="hidden" id="inputReviewOrderCode" value="">
        {{-- PILIH RATING BINTANG (1-5) --}}
        <div class="text-center py-1 bg-slate-50 rounded-xl border border-slate-200/80 p-3">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Nilai Kualitas Pelayanan</span>
            <div class="flex items-center justify-center gap-2 text-2xl" id="starRatingContainer">
                <button type="button" onclick="setStarRating(1)" class="star-btn text-amber-400 transition-transform hover:scale-125 focus:outline-none" data-val="1">★</button>
                <button type="button" onclick="setStarRating(2)" class="star-btn text-amber-400 transition-transform hover:scale-125 focus:outline-none" data-val="2">★</button>
                <button type="button" onclick="setStarRating(3)" class="star-btn text-amber-400 transition-transform hover:scale-125 focus:outline-none" data-val="3">★</button>
                <button type="button" onclick="setStarRating(4)" class="star-btn text-amber-400 transition-transform hover:scale-125 focus:outline-none" data-val="4">★</button>
                <button type="button" onclick="setStarRating(5)" class="star-btn text-amber-400 transition-transform hover:scale-125 focus:outline-none" data-val="5">★</button>
            </div>
            <input type="hidden" id="inputReviewRating" value="5">
            <span class="text-[11px] font-bold text-amber-600 mt-1 block" id="starRatingLabel">Luar Biasa (5 Bintang)</span>
        </div>

        {{-- PILIH LAYANAN --}}
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Layanan yang Diulas</label>
            <select id="inputReviewService" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#173B67]">
                <option value="Full Home Cleaning">Full Home Cleaning</option>
                <option value="Pindahan Kos &amp; Rumah">Pindahan Kos &amp; Rumah</option>
                <option value="Basic Daily Cleaning">Basic Daily Cleaning</option>
                <option value="Bersihkan Kamar Mandi">Bersihkan Kamar Mandi</option>
                <option value="Lainnya">Layanan Lainnya</option>
            </select>
        </div>

        {{-- NAMA PENGULAS --}}
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Nama Anda</label>
            <input type="text" id="inputReviewName" value="{{ Auth::user()?->name ?? 'Customer' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#173B67]" required>
        </div>

        {{-- KOMENTAR REVIEW --}}
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Ulasan Pengalaman</label>
            <textarea id="inputReviewText" rows="3" placeholder="Ceritakan bagaimana kerapian, kesopanan mitra, dan kepuasan Anda..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#173B67]" required></textarea>
        </div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Penilaian Sekarang
        </button>
    </form>
</div>


{{-- =====================================================
     9. TOAST NOTIFICATION
====================================================== --}}
<div id="customerToast" class="fixed top-5 left-1/2 -translate-x-1/2 max-w-[360px] w-full px-4 z-50 pointer-events-none transition-all duration-300 opacity-0 -translate-y-4">
    <div class="bg-slate-900/95 text-white backdrop-blur-md rounded-2xl p-3 shadow-xl border border-white/20 flex items-center gap-3">
        <div id="customerToastIcon" class="w-7 h-7 rounded-xl bg-sky-500 text-white flex items-center justify-center shrink-0 text-xs">
            <i class="fa-solid fa-info"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h5 id="customerToastTitle" class="text-xs font-bold leading-tight truncate">Info</h5>
            <p id="customerToastMessage" class="text-[11px] text-slate-300 leading-snug mt-0.5 truncate">Pesan informasi</p>
        </div>
    </div>
</div>


<script data-layout-script="true">
    // State Auth Customer & Mitra
    const AUTH_KEY = 'sb_customer_auth';

    function getCustomerAuth() {
        try {
            const data = localStorage.getItem(AUTH_KEY);
            let u = data ? JSON.parse(data) : null;
            if (u && (u.role === 'customer' || !u.role)) {
                if (u.isLoggedIn === undefined || u.isLoggedIn === null) u.isLoggedIn = true;
                return u;
            }
            // Auto-heal sesi customer agar pengguna tidak pernah terlempar ke form login
            // HANYA auto-heal saat berada di area customer authenticated (/customer/dashboard, /customer/pesanan, /customer/pembayaran)
            // JANGAN auto-heal di halaman pengunjung publik (/pengunjung, data-is-pengunjung)
            const isExplicitLogout = localStorage.getItem('sb_customer_logged_out') === 'true';
            const isPengunjungPage = window.location.pathname === '/pengunjung' ||
                                     window.location.pathname.includes('/pengunjung') ||
                                     document.body.getAttribute('data-is-pengunjung') === 'true';
            const isDashboard = window.location.pathname.includes('/customer/dashboard') ||
                                window.location.pathname.includes('/customer/pesanan') ||
                                window.location.pathname.includes('/customer/pembayaran');
            if (isDashboard && !isExplicitLogout && !isPengunjungPage) {
                if (u && u.name && u.role !== 'mitra') {
                    u.isLoggedIn = true;
                    return u;
                }
                @if(Auth::check())
                const serverUser = {
                    isLoggedIn: true,
                    id: @json(Auth::id()),
                    name: @json(Auth::user()->name),
                    phone: @json(Auth::user()->phone),
                    email: @json(Auth::user()->email),
                    role: @json(Auth::user()->role ?? 'customer')
                };
                try {
                    localStorage.setItem(AUTH_KEY, JSON.stringify(serverUser));
                } catch(e) {}
                return serverUser;
                @endif
            }
            return null;
        } catch (e) {
            return null;
        }
    }

    function setCustomerAuth(user) {
        if (!user) return;
        user.isLoggedIn = true;
        if (!user.role) user.role = 'customer';
        localStorage.removeItem('sb_customer_logged_out');
        localStorage.setItem(AUTH_KEY, JSON.stringify(user));
        refreshCustomerAuthUI();
    }

    function clearCustomerAuth() {
        localStorage.setItem('sb_customer_logged_out', 'true');
        localStorage.removeItem(AUTH_KEY);
        refreshCustomerAuthUI();
    }

    // =========================================================
    //  GUARD: WAJIB LOGIN CUSTOMER
    //  Dipakai untuk memblokir pengunjung umum (belum login / bukan
    //  role customer) dari mengakses fitur detail. Jika belum login,
    //  tampilkan popup login; jika popup tidak tersedia, alihkan ke
    //  halaman login.
    //  Mengembalikan true jika sudah login sebagai customer.
    // =========================================================
    function requireCustomerLogin(pesan) {
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        const sudahLogin = !!(user && user.isLoggedIn && user.role === 'customer');
        if (sudahLogin) return true;

        const modal = document.getElementById('modalLoginCustomer');
        if (modal && typeof openModal === 'function') {
            if (typeof switchLoginRole === 'function') switchLoginRole('customer');
            openModal('modalLoginCustomer');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Perlu Masuk Akun', pesan || 'Silakan masuk akun terlebih dahulu untuk mengakses fitur ini.', 'warning');
            }
        } else {
            window.location.href = "{{ route('customer.login') }}";
        }

        return false;
    }

    // Toggle Show/Hide Password
    function toggleCustomerPassword(inputId, trigger) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = typeof trigger === 'string' ? document.getElementById(trigger) : trigger.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.classList.add('text-sky-600');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.remove('text-sky-600');
                icon.classList.add('fa-eye');
            }
        }
    }

    // Forgot Password Handler
    function handleForgotPasswordModal() {
        closeAllCustomerModals();
        showCustomerToast(
            'Lupa Kata Sandi',
            'Silakan gunakan formulir Pengaduan CS untuk verifikasi dan reset kata sandi akun Anda.',
            'info'
        );
        smoothNavigate('{{ route("customer.pengaduan") }}');
    }

    // Live Password Match Validation
    function validatePasswordMatch() {
        const passEl = document.getElementById('inputDaftarPass');
        const confirmEl = document.getElementById('inputDaftarPassConfirm');
        const indicator = document.getElementById('passMatchIndicator');
        if (!passEl || !confirmEl || !indicator) return false;

        const pass = passEl.value;
        const confirm = confirmEl.value;

        if (!confirm) {
            indicator.className = 'text-[10px] font-semibold mt-1 hidden';
            indicator.innerHTML = '';
            return false;
        }

        indicator.classList.remove('hidden');
        if (pass === confirm) {
            indicator.className = 'text-[10px] font-semibold mt-1 text-emerald-600 flex items-center gap-1';
            indicator.innerHTML = '<i class="fa-solid fa-circle-check"></i> Kata sandi cocok';
            return true;
        } else {
            indicator.className = 'text-[10px] font-semibold mt-1 text-rose-600 flex items-center gap-1';
            indicator.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Kata sandi tidak sesuai';
            return false;
        }
    }

    async function handleRegisterStep1(e) {
        e.preventDefault();
        const nama = document.getElementById('inputDaftarNama') ? document.getElementById('inputDaftarNama').value.trim() : '';
        const phone = document.getElementById('inputDaftarPhone') ? document.getElementById('inputDaftarPhone').value.trim() : '';
        const pass = document.getElementById('inputDaftarPass') ? document.getElementById('inputDaftarPass').value : '';
        const confirm = document.getElementById('inputDaftarPassConfirm') ? document.getElementById('inputDaftarPassConfirm').value : '';
        const terms = document.getElementById('checkTermsCustomer');
        const submitButton = e.currentTarget.querySelector('button[type="submit"]');
        const originalButtonContent = submitButton ? submitButton.innerHTML : '';

        if (!nama) {
            showCustomerToast('Data Belum Lengkap', 'Nama lengkap wajib diisi.', 'warning');
            return;
        }
        if (!phone || phone.length < 9) {
            showCustomerToast('Nomor Tidak Valid', 'Masukkan nomor handphone aktif minimal 9 digit.', 'warning');
            return;
        }
        if (!pass || pass.length < 6) {
            showCustomerToast('Kata Sandi Kurang', 'Kata sandi minimal 6 karakter.', 'warning');
            return;
        }
        if (pass !== confirm) {
            showCustomerToast('Kata Sandi Tidak Sesuai', 'Pastikan konfirmasi kata sandi sama persis.', 'warning');
            return;
        }
        if (terms && !terms.checked) {
            showCustomerToast('Persetujuan Syarat', 'Anda wajib menyetujui Syarat & Ketentuan SayaBantu.', 'warning');
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan Akun...';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch('{{ route("customer.register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    name: nama,
                    phone: phone,
                    password: pass,
                    password_confirmation: confirm
                })
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Pendaftaran gagal disimpan.');
            }

            closeAllCustomerModals();
            window.location.href = result.redirect || "{{ route('customer.dashboard') }}";
        } catch (error) {
            showCustomerToast(
                'Pendaftaran Gagal',
                error instanceof Error ? error.message : 'Terjadi kesalahan saat menyimpan akun. Silakan coba lagi.',
                'error'
            );
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonContent;
            }
        }
    }

    // Role Switcher in Login Modal
    let currentLoginRole = 'customer';
    function switchLoginRole(role) {
        currentLoginRole = role;
        const btnCust = document.getElementById('tabBtnCustomer');
        const btnMitra = document.getElementById('tabBtnMitra');
        const contentCust = document.getElementById('roleContentCustomer');
        const contentMitra = document.getElementById('roleContentMitra');
        const title = document.getElementById('loginModalTitle');

        if (role === 'mitra') {
            btnMitra.className = 'py-1.5 rounded-lg transition-all text-center bg-white text-emerald-900 shadow-xs flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            btnCust.className = 'py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            contentMitra.classList.remove('hidden');
            contentCust.classList.add('hidden');
            if (title) title.innerText = 'Masuk Portal Mitra';
        } else {
            btnCust.className = 'py-1.5 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            btnMitra.className = 'py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            contentCust.classList.remove('hidden');
            contentMitra.classList.add('hidden');
            if (title) title.innerText = 'Masuk ke SayaBantu';
        }
    }

    // Role Switcher in Daftar Modal (Customer vs Mitra)
    let currentDaftarRole = 'customer';
    function switchDaftarRole(role) {
        currentDaftarRole = role;
        const btnCust = document.getElementById('tabBtnDaftarCustomer');
        const btnMitra = document.getElementById('tabBtnDaftarMitra');
        const contentCust = document.getElementById('roleContentDaftarCustomer');
        const contentMitra = document.getElementById('roleContentDaftarMitra');
        const title = document.getElementById('daftarModalTitle');

        if (role === 'mitra') {
            if (btnMitra) btnMitra.className = 'py-1.5 rounded-lg transition-all text-center bg-white text-emerald-900 shadow-xs flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            if (btnCust) btnCust.className = 'py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            if (contentMitra) contentMitra.classList.remove('hidden');
            if (contentCust) contentCust.classList.add('hidden');
            if (title) title.innerText = 'Daftar Mitra SayaBantu';
        } else {
            if (btnCust) btnCust.className = 'py-1.5 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            if (btnMitra) btnMitra.className = 'py-1.5 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 font-bold cursor-pointer';
            if (contentCust) contentCust.classList.remove('hidden');
            if (contentMitra) contentMitra.classList.add('hidden');
            if (title) title.innerText = 'Daftar Akun SayaBantu';
        }
    }

    // Quick Login Actions
    function quickLoginCustomerAction(name, phone) {
        setCustomerAuth({
            isLoggedIn: true,
            role: 'customer',
            name: name,
            phone: phone
        });
        closeAllCustomerModals();
        showCustomerToast('Berhasil Masuk', `Selamat datang, ${name}! Mengalihkan ke Dashboard Customer...`, 'success');
        if (!window.location.pathname.includes('/customer/dashboard')) {
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo("{{ route('customer.dashboard') }}");
                return;
            }
            window.location.href = "{{ route('customer.dashboard') }}";
        }
    }

    function quickLoginMitraAction(name, phone, specialty, mitraId = null) {
        localStorage.setItem('sb_mitra_auth', JSON.stringify({
            isLoggedIn: true,
            role: 'mitra',
            name: name,
            phone: phone,
            id: mitraId,
            mitra_id: mitraId,
            specialty: specialty,
            status: 'approved',
        }));
        document.cookie = "sb_mitra_logged_out=false; path=/; max-age=31536000";
        localStorage.removeItem('sb_mitra_logged_out');
        closeAllCustomerModals();
        showCustomerToast('Mitra Aktif', `Halo Mitra ${name}! Mengalihkan ke Portal Mitra...`, 'success');
        window.location.href = "{{ route('mitra.dashboard') }}";
    }

    function handleManualCustomerLogin(e) {
        e.preventDefault();
        const alertEl = document.getElementById('loginCustomerModalAlert');
        if (alertEl) alertEl.classList.add('hidden');

        const phone = document.getElementById('inputLoginCustomerPhone').value.trim();
        const pass = document.getElementById('inputLoginCustomerPass') ? document.getElementById('inputLoginCustomerPass').value : '';

        if (!phone) {
            showCustomerToast('Lengkapi Data', 'Masukkan nomor handphone aktif', 'warning');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('{{ route("customer.login.submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                phone: phone,
                password: pass
            })
        })
        .then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw new Error(data.message || 'Gagal masuk akun.');
            }
            return data;
        })
        .then((data) => {
            quickLoginCustomerAction(data.user?.name || 'Customer', data.user?.phone || phone);
        })
        .catch((err) => {
            if (alertEl) {
                alertEl.innerHTML = `
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-xs shrink-0"></i>
                        <div>
                            <strong class="font-bold block text-rose-900">Gagal Masuk</strong>
                            <span class="text-rose-700">${err.message || 'Nomor handphone atau kata sandi tidak cocok.'}</span>
                        </div>
                    </div>
                `;
                alertEl.classList.remove('hidden');
            }
            showCustomerToast('Gagal Masuk', err.message || 'Periksa kembali data Anda.', 'error');
        });
    }

    function handleManualMitraLogin(e) {
        e.preventDefault();
        const alertEl = document.getElementById('loginMitraModalAlert');
        if (alertEl) alertEl.classList.add('hidden');

        const idOrPhone = document.getElementById('inputLoginMitraPhone').value.trim();
        const pass = document.getElementById('inputLoginMitraPass') ? document.getElementById('inputLoginMitraPass').value : '';

        if (!idOrPhone) {
            showCustomerToast('Lengkapi Data', 'Masukkan ID Mitra atau nomor handphone', 'warning');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch('{{ route("mitra.login.submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                identifier: idOrPhone,
                pin: pass
            })
        })
        .then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error(data.message || 'Gagal masuk akun mitra.');
                err.status = res.status;
                err.mitraStatus = data.status;
                throw err;
            }
            return data;
        })
        .then((data) => {
            quickLoginMitraAction(
                data.mitra?.name || 'Mitra',
                data.mitra?.phone || idOrPhone,
                data.mitra?.specialty || 'Layanan Serbaguna',
                data.mitra?.mitra_id || data.mitra?.id || null
            );
        })
        .catch((err) => {
            if (err.mitraStatus === 'verifikasi') {
                if (alertEl) {
                    alertEl.innerHTML = `
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-amber-500 mt-0.5 text-xs shrink-0"></i>
                            <div>
                                <strong class="font-bold block text-amber-900">Menunggu Verifikasi &amp; ACC Admin</strong>
                                <span class="text-amber-800 leading-snug block mt-0.5">Pendaftaran akun Anda sedang dalam antrean verifikasi &amp; ACC oleh Tim Admin SayaBantu. Anda baru dapat masuk ke Portal Mitra setelah akun Anda disetujui oleh Admin.</span>
                            </div>
                        </div>
                    `;
                    alertEl.classList.remove('hidden');
                }
                showCustomerToast('Menunggu ACC Admin', 'Pendaftaran Anda sedang diverifikasi admin.', 'warning');
                return;
            }

            if (alertEl) {
                alertEl.innerHTML = `
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-xs shrink-0"></i>
                        <div>
                            <strong class="font-bold block text-rose-900">Gagal Masuk</strong>
                            <span class="text-rose-700 leading-snug block mt-0.5">${err.message || 'ID Mitra atau PIN salah.'}</span>
                        </div>
                    </div>
                `;
                alertEl.classList.remove('hidden');
            }
            showCustomerToast('Gagal Masuk', err.message || 'ID Mitra atau PIN tidak sesuai.', 'error');
        });
    }

    function logoutUserAction() {
        clearCustomerAuth();
        closeAllCustomerModals();
        fetch('{{ route("customer.logout") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).finally(() => {
            window.location.href = "{{ route('customer.index') }}";
        });
    }

    // Navigasi instan: berpindah halaman tanpa delay buatan
    function smoothNavigate(url) {
        if (typeof spaNavigateTo === 'function') {
            spaNavigateTo(url);
            return;
        }
        window.location.href = url;
    }

    // Contact Handler (Mengarahkan ke Pengaduan/CS resmi)
    function handleWaGatedClick() {
        const user = getCustomerAuth();
        if (user && user.isLoggedIn) {
            smoothNavigate('{{ route("customer.pengaduan") }}');
        } else {
            openModal('modalWaGatedNotice');
            showCustomerToast('Layanan Terkunci', 'Masuk sebagai Customer atau Mitra untuk menghubungi Customer Service.', 'warning');
        }
    }

    // Open Partner Detail Modal
    function openBocahModal(name, photo, specialty, rating, region) {
        const photoEl = document.getElementById('bocahDetailPhoto');
        const nameEl = document.getElementById('bocahDetailName');
        const specEl = document.getElementById('bocahDetailSpecialty');
        const ratingEl = document.getElementById('bocahDetailRating');
        const regionEl = document.getElementById('bocahDetailRegion');

        if (photoEl) photoEl.src = photo;
        if (nameEl) nameEl.innerText = name;
        if (specEl) specEl.innerText = specialty;
        if (ratingEl) ratingEl.innerText = rating;
        if (regionEl) regionEl.innerText = region;

        openModal('modalDetailBocah');
    }

    // Refresh UI Customer & Mitra (Guest vs Customer vs Mitra)
    function refreshCustomerAuthUI() {
        try {
            const cleanKey = 'sb_clean_reset_v9';
            if (!localStorage.getItem(cleanKey)) {
                localStorage.removeItem('sb_active_order');
                localStorage.removeItem('sb_customer_orders');
                localStorage.removeItem('sb_customer_pembayaran_selesai');
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const k = localStorage.key(i);
                    if (k && (k.startsWith('sb_chat_') || k.startsWith('sb_cached_') || k.startsWith('sb_order_'))) {
                        localStorage.removeItem(k);
                    }
                }
                localStorage.setItem(cleanKey, 'true');
            }
        } catch (e) {}

        const user = getCustomerAuth();
        const headerBtn = document.getElementById('headerAccountBtn');
        const headerLoginBtn = document.getElementById('headerLoginBtn');
        const headerDaftarBtn = document.getElementById('headerDaftarBtn');
        const mLoginBtn = document.getElementById('mobileHeaderLoginBtn');
        const mDaftarBtn = document.getElementById('mobileHeaderDaftarBtn');
        const mProfileBtn = document.getElementById('mobileHeaderProfileBtn');
        const mNotifBtn = document.getElementById('mobileHeaderNotifBtn');

        const isLoggedIn = !!(user && user.isLoggedIn);
        const isCustLoggedIn = isLoggedIn && user.role !== 'mitra';

        const isPengunjungExplicit = window.location.pathname === '/pengunjung' ||
                                     window.location.pathname.includes('/pengunjung') ||
                                     window.location.pathname === '/customer/beranda' ||
                                     document.body.getAttribute('data-is-pengunjung') === 'true';

        // Tombol publik: Masuk vs Profile
        if (isPengunjungExplicit) {
            if (isCustLoggedIn) {
                if (headerLoginBtn) headerLoginBtn.classList.add('hidden');
                if (headerDaftarBtn) headerDaftarBtn.classList.add('hidden');
                if (headerBtn) headerBtn.classList.remove('hidden');
                if (mLoginBtn) mLoginBtn.classList.add('hidden');
                if (mDaftarBtn) mDaftarBtn.classList.add('hidden');
                if (mProfileBtn) mProfileBtn.classList.remove('hidden');
                if (mNotifBtn) mNotifBtn.classList.add('hidden');
            } else {
                if (headerLoginBtn) headerLoginBtn.classList.remove('hidden');
                if (headerDaftarBtn) headerDaftarBtn.classList.remove('hidden');
                if (headerBtn) headerBtn.classList.add('hidden');
                if (mLoginBtn) mLoginBtn.classList.remove('hidden');
                if (mDaftarBtn) mDaftarBtn.classList.remove('hidden');
                if (mProfileBtn) mProfileBtn.classList.add('hidden');
                if (mNotifBtn) mNotifBtn.classList.add('hidden');
            }
        } else {
            if (headerLoginBtn) headerLoginBtn.classList.toggle('hidden', isLoggedIn);
            if (headerDaftarBtn) headerDaftarBtn.classList.toggle('hidden', isLoggedIn);
            if (headerBtn) headerBtn.classList.toggle('hidden', !isLoggedIn);
            if (mLoginBtn) mLoginBtn.classList.toggle('hidden', isLoggedIn);
            if (mDaftarBtn) mDaftarBtn.classList.toggle('hidden', isLoggedIn);
            if (mProfileBtn) mProfileBtn.classList.toggle('hidden', !isLoggedIn);
            if (mNotifBtn) mNotifBtn.classList.toggle('hidden', !isLoggedIn);
        }

        if (isLoggedIn && user && user.name) {
            const avatarInitial = document.getElementById('mobileHeaderAvatarInitial');
            if (avatarInitial) avatarInitial.innerText = user.name.charAt(0).toUpperCase();
        }

        // Pengunjung: ikon notifikasi disembunyikan jika belum login atau di halaman pengunjung
        const notifBtn = document.getElementById('headerNotifBtn');
        if (notifBtn) notifBtn.classList.toggle('hidden', isPengunjungExplicit || !isLoggedIn);

        // Header versi mobile (dipakai halaman mitra & detail layanan)
        const mWallet = document.getElementById('mobileHeaderWallet');
        if (mWallet) {
            // Saldo customer DITIADAKAN sesuai permintaan user ("tidak usah ada saldo untuk customer")
            mWallet.classList.add('hidden');
            mWallet.classList.remove('flex');
        }

        // Sinkronisasi Bottom Nav (Guest vs Customer: Dashboard, Layanan, Mitra, Tentang SayaBantu)
        const bNavGuest = document.getElementById('bottomNavGuest');
        const bNavCust = document.getElementById('bottomNavCustomer');
        const mNavGuest = document.getElementById('mNavGuest');
        const mNavCust = document.getElementById('mNavCustomer');

        const showGuestNav = isPengunjungExplicit || !isCustLoggedIn;

        if (bNavGuest) {
            bNavGuest.classList.toggle('hidden', !showGuestNav);
            if (bNavCust) bNavCust.classList.toggle('hidden', showGuestNav);
        } else if (bNavCust) {
            bNavCust.classList.remove('hidden');
        }
        if (mNavGuest) {
            mNavGuest.classList.toggle('hidden', !showGuestNav);
            if (mNavCust) mNavCust.classList.toggle('hidden', showGuestNav);
        } else if (mNavCust) {
            mNavCust.classList.remove('hidden');
        }

        // Greeting cards
        const guestGreeting = document.getElementById('guestGreetingCard');
        const vipGreeting = document.getElementById('vipGreetingCard');
        const mitraGreeting = document.getElementById('mitraGreetingCard');
        const custPengunjungBadge = document.getElementById('pengunjungCustomerBadge');

        // Specific widgets
        const vipWallet = document.getElementById('vipWalletCard');
        const vipOrderTracker = document.getElementById('vipActiveOrderCard');
        const mitraDashboardCard = document.getElementById('mitraDashboardCard');
        const memberBadgeForm = document.getElementById('vipBadgeForm');
        const guestLockNotice = document.getElementById('guestLockNotice');
        const guestFormOverlay = document.getElementById('guestFormOverlay');
        const formAlamat = document.getElementById('formAlamat');

        // Tidak ada saldo untuk customer!
        if (vipWallet) vipWallet.classList.add('hidden');

        if (isPengunjungExplicit) {
            // KHUSUS HALAMAN PENGUNJUNG PUBLIK
            if (guestGreeting) guestGreeting.classList.remove('hidden');
            if (vipGreeting) vipGreeting.classList.add('hidden');
            if (mitraGreeting) mitraGreeting.classList.add('hidden');

            if (vipOrderTracker) vipOrderTracker.classList.add('hidden');
            if (mitraDashboardCard) mitraDashboardCard.classList.add('hidden');

            if (isCustLoggedIn) {
                if (custPengunjungBadge) custPengunjungBadge.classList.remove('hidden');
                if (memberBadgeForm) memberBadgeForm.classList.remove('hidden');
                if (guestLockNotice) guestLockNotice.classList.add('hidden');
                if (guestFormOverlay) guestFormOverlay.classList.add('hidden');
            } else {
                if (custPengunjungBadge) custPengunjungBadge.classList.add('hidden');
                if (memberBadgeForm) memberBadgeForm.classList.add('hidden');
                if (guestLockNotice) guestLockNotice.classList.remove('hidden');
                if (guestFormOverlay) guestFormOverlay.classList.remove('hidden');
            }
        } else if (user && user.isLoggedIn) {

            if (user.role === 'mitra') {
                // Sesi Mitra terdeteksi di portal customer: alihkan langsung ke Portal Mitra
                try {
                    localStorage.setItem('sb_mitra_auth', JSON.stringify(user));
                    localStorage.removeItem(AUTH_KEY);
                } catch(e) {}
                window.location.replace("{{ route('mitra.dashboard') }}");
                return;
            } else {
                // LOGGED IN AS CUSTOMER
                const initial = user.name ? user.name.charAt(0).toUpperCase() : 'C';
                if (headerBtn) {
                    headerBtn.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 text-white flex items-center justify-center font-bold text-xs border border-white/40 shadow-sm">
                            ${initial}
                        </div>
                    `;
                }
                const mAvatar = document.getElementById('mobileHeaderAvatarInitial');
                if (mAvatar) mAvatar.innerText = initial;
                const cAvatar = document.getElementById('custHeaderAvatar');
                if (cAvatar) cAvatar.innerText = initial;
                const pAvatar = document.getElementById('profAvatarInitial');
                if (pAvatar) pAvatar.innerText = initial;

                if (guestGreeting) guestGreeting.classList.add('hidden');
                if (mitraGreeting) mitraGreeting.classList.add('hidden');
                if (vipGreeting) vipGreeting.classList.remove('hidden');

                if (vipOrderTracker) vipOrderTracker.classList.remove('hidden');
                if (mitraDashboardCard) mitraDashboardCard.classList.add('hidden');
                if (memberBadgeForm) memberBadgeForm.classList.remove('hidden');
                if (guestLockNotice) guestLockNotice.classList.add('hidden');
                if (guestFormOverlay) guestFormOverlay.classList.add('hidden');

                const vipNameText = document.getElementById('vipUserNameText');
                if (vipNameText) vipNameText.innerText = user.name;
                const custGreet = document.getElementById('custGreetingName');
                if (custGreet) custGreet.innerText = user.name;
                const vipCode = document.getElementById('vipUserCodeText');
                if (vipCode && user.id) vipCode.innerText = '#CUST-' + user.id;

                const profC = document.getElementById('profCustomerName');
                if (profC) profC.innerText = user.name;
                const profP = document.getElementById('profCustomerPhone');
                if (profP && user.phone) profP.innerText = user.phone;
                const profDetN = document.getElementById('profDetailName');
                if (profDetN) profDetN.innerText = user.name;
                const profDetP = document.getElementById('profDetailPhone');
                if (profDetP && user.phone) profDetP.innerText = user.phone;
                const profDetE = document.getElementById('profDetailEmail');
                if (profDetE) profDetE.innerText = user.email || (user.phone ? (user.phone + '@customer.sayabantu.com') : '');
                const inpRev = document.getElementById('inputReviewName');
                if (inpRev) inpRev.value = user.name;

                if (formAlamat && !formAlamat.value) {
                    formAlamat.value = "Jl. Sudirman No. 12, Jakarta Pusat (Rumah)";
                }
            }

        } else {
            // GUEST / NOT LOGGED IN
            if (headerBtn) {
                headerBtn.innerHTML = `<i class="fa-regular fa-user text-[15px]"></i>`;
            }
            if (guestGreeting) guestGreeting.classList.remove('hidden');
            if (vipGreeting) vipGreeting.classList.add('hidden');
            if (mitraGreeting) mitraGreeting.classList.add('hidden');

            if (vipOrderTracker) vipOrderTracker.classList.add('hidden');
            if (mitraDashboardCard) mitraDashboardCard.classList.add('hidden');
            if (memberBadgeForm) memberBadgeForm.classList.add('hidden');
            if (guestLockNotice) guestLockNotice.classList.remove('hidden');
            if (guestFormOverlay) guestFormOverlay.classList.remove('hidden');
        }

        if (typeof updateDrawerProfileUI === 'function') {
            updateDrawerProfileUI();
        }

        if (typeof syncCustomerViewsWithAuth === 'function') {
            syncCustomerViewsWithAuth(user);
        }
    }

    // Modal Helpers
    function openModal(id) {
        document.getElementById('customerBackdrop').classList.remove('hidden');
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function closeModernServiceDetail() {
        const modal = document.getElementById('modalDetailServiceModern');
        if (modal) modal.classList.add('hidden');
        const backdrop = document.getElementById('customerBackdrop');
        if (backdrop) backdrop.classList.add('hidden');
    }
    window.closeModernServiceDetail = closeModernServiceDetail;

    function closeAllCustomerModals() {
        const backdrop = document.getElementById('customerBackdrop');
        if (backdrop) backdrop.classList.add('hidden');
        const modals = [
            'modalLoginCustomer', 
            'modalDaftarCustomer',
            'modalProfileCustomer',
            'modalProfileMitra',
            'modalWaGatedNotice',
            'modalDetailBocah',
            'modalDetailServiceModern',
            'modalKonfirmasiOrder',
            'modalLacakPesanan',
            'modalNotifCustomer',
            'modalReviewCustomer'
        ];
        modals.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllCustomerModals();
        }
    });

    // Rating star handler & review submit
    let currentSelectedRating = 5;
    function setStarRating(val) {
        currentSelectedRating = val;
        const input = document.getElementById('inputReviewRating');
        if (input) input.value = val;
        const stars = document.querySelectorAll('#starRatingContainer .star-btn');
        stars.forEach((btn, i) => {
            if (i < val) {
                btn.classList.add('text-amber-400');
                btn.classList.remove('text-slate-300');
            } else {
                btn.classList.remove('text-amber-400');
                btn.classList.add('text-slate-300');
            }
        });
        const label = document.getElementById('starRatingLabel');
        if (label) {
            const labels = ['Kurang Memuaskan (1 Bintang)', 'Cukup (2 Bintang)', 'Baik (3 Bintang)', 'Sangat Baik (4 Bintang)', 'Luar Biasa (5 Bintang)'];
            label.innerText = labels[val - 1] || `${val} Bintang`;
        }
    }

    function openReviewModal(serviceName, orderCode = '', mitraName = '') {
        const user = getCustomerAuth();
        const nameInput = document.getElementById('inputReviewName');
        if (nameInput && user && user.name) {
            nameInput.value = user.name;
        }
        const svcInput = document.getElementById('inputReviewService');
        if (svcInput && serviceName) {
            svcInput.value = serviceName;
        }
        const orderInput = document.getElementById('inputReviewOrderCode');
        if (orderInput) {
            orderInput.value = (orderCode || '').replace('#', '');
        }
        setStarRating(5);
        openModal('modalReviewCustomer');
    }

    function handleReviewSubmit(e) {
        e.preventDefault();
        const name = document.getElementById('inputReviewName').value.trim() || 'Pelanggan';
        const service = document.getElementById('inputReviewService').value;
        const comment = document.getElementById('inputReviewText').value.trim() || 'Pengerjaan sangat baik dan memuaskan!';
        const rating = currentSelectedRating;
        const orderCode = (document.getElementById('inputReviewOrderCode')?.value || '').trim();

        if (orderCode) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch(`/api/orders/${encodeURIComponent(orderCode)}/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ rating, comment })
            }).catch(() => {});

            try {
                const reviews = JSON.parse(localStorage.getItem('sb_order_reviews') || '{}');
                reviews[orderCode] = { rating, comment, time: new Date().toISOString() };
                localStorage.setItem('sb_order_reviews', JSON.stringify(reviews));

                const rawList = localStorage.getItem('sb_customer_orders');
                if (rawList) {
                    const list = JSON.parse(rawList);
                    const item = list.find(o => (o.code || o.id || '').replace('#', '') === orderCode);
                    if (item) {
                        item.review_rating = rating;
                        item.review_comment = comment;
                        item.status = 'selesai';
                        localStorage.setItem('sb_customer_orders', JSON.stringify(list));
                    }
                }
            } catch(e) {}

            if (typeof renderReviewPesananDetailStatus === 'function') {
                renderReviewPesananDetailStatus(rating, comment, 'Baru saja');
            }
        }

        if (typeof addCustomerReviewToList === 'function') {
            addCustomerReviewToList(name, service, comment, rating);
        }

        closeAllCustomerModals();
        showCustomerToast('Penilaian Terkirim', `Terima kasih atas ulasan bintang ${rating} Anda untuk layanan ${service}!`, 'success');
        const textArea = document.getElementById('inputReviewText');
        if (textArea) textArea.value = '';
    }

    // Masuk / Daftar lewat Google (placeholder — belum terhubung penyedia OAuth)
    function handleGoogleAuth(mode) {
        closeAllCustomerModals();
        const aksi = mode === 'daftar' ? 'Pendaftaran' : 'Masuk';
        showCustomerToast('Google', aksi + ' dengan Google belum terhubung. Silakan gunakan nomor handphone terlebih dahulu.', 'info');
    }

    function handleAccountButtonClick() {
        const user = getCustomerAuth();
        if (user && user.isLoggedIn) {
            if (user.role === 'mitra') {
                openModal('modalProfileMitra');
            } else if (typeof openCustomerQuickProfile === 'function') {
                // Popup profil singkat (dibuka juga dari avatar "B" di header).
                openCustomerQuickProfile();
            } else {
                openModal('modalProfileCustomer');
            }
        } else {
            openModal('modalLoginCustomer');
        }
    }

    // Toast Notification
    let toastCustomerTimeout;
    function showCustomerToast(title, msg, type = 'info') {
        const toast = document.getElementById('customerToast');
        const titleEl = document.getElementById('customerToastTitle');
        const msgEl = document.getElementById('customerToastMessage');
        const iconEl = document.getElementById('customerToastIcon');

        if (!toast || !titleEl || !msgEl || !iconEl) return;

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

        clearTimeout(toastCustomerTimeout);
        toastCustomerTimeout = setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', '-translate-y-4');
        }, 3000);
    }

    // Mapping data visual layanan (berdasarkan referensi Screen 2)
    const serviceModernData = {
        'Full Home Cleaning': {
            title: 'House Cleaning Service',
            cat: 'Kebersihan',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/cleaner-woman.jpg") }}',
            desc: 'Pembersihan rumah menyeluruh meliputi kamar tidur, ruang tamu, menyapu, mengepel, membersihkan debu perabot, lap kaca jendela dan penataan ruangan.',
            prices: { basic: '85.000', standard: '140.000', premium: '220.000' },
            notes: {
                basic: 'Paket Standar untuk ruangan kos atau apartemen studio (1-2 jam).',
                standard: 'Paket Lengkap untuk rumah 2 kamar + ruang tamu + dapur bersih (3 jam).',
                premium: 'Paket Premium deep cleaning menyeluruh rumah besar + disinfektan steril.'
            }
        },
        'Basic Daily Cleaning': {
            title: 'Daily House Cleaning',
            cat: 'Kebersihan',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/cleaner-card.jpg") }}',
            desc: 'Bantuan harian sapu, pel, lap meja, cuci piring ringan, dan pembuangan sampah rumah tangga secara higienis.',
            prices: { basic: '50.000', standard: '80.000', premium: '120.000' },
            notes: {
                basic: 'Paket Standar sapu & pel lantai rutin hingga 50 m².',
                standard: 'Paket Lengkap sapu + pel + lap perabot + buang sampah (2 jam).',
                premium: 'Paket Premium pembersihan harian menyeluruh + cuci piring tumpuk.'
            }
        },
        'Bersihkan Kamar Mandi': {
            title: 'Deep Clean Kamar Mandi',
            cat: 'Kebersihan',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/cleaner-card.jpg") }}',
            desc: 'Kuras bak mandi, sikat kerak lantai kamar mandi membandel, kloset kinclong, dan sterilisasi dinding.',
            prices: { basic: '65.000', standard: '95.000', premium: '140.000' },
            notes: {
                basic: 'Paket Standar untuk 1 kamar mandi ukuran standar.',
                standard: 'Paket Lengkap untuk kamar mandi berkerak tebal + pembersih kerak profesional.',
                premium: 'Paket Premium 2 kamar mandi sekaligus + disinfektan anti bau & kuman.'
            }
        },
        'Pindahan': {
            title: 'Pindahan Kos & Rumah Service',
            cat: 'Pindah & Angkut',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/banner-moving.jpg") }}',
            desc: 'Bantuan tenaga kuat untuk packing kardus, angkat barang berat, lemari, kulkas, kasur, dan tata rapi ke kendaraan angkut.',
            prices: { basic: '150.000', standard: '250.000', premium: '400.000' },
            notes: {
                basic: 'Paket Standar 1 helper kuat untuk pindahan kos ringan (2 jam).',
                standard: 'Paket Lengkap 2 helper kuat untuk pindahan rumah/apartemen menengah.',
                premium: 'Paket Premium 2-3 helper + bantuan packing bubble wrap + penataan di tempat baru.'
            }
        },
        'Angkut Barang': {
            title: 'Angkut Barang & Beban Berat',
            cat: 'Pindah & Angkut',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/banner-moving.jpg") }}',
            desc: 'Bantu angkat perabotan berat antar lantai, tata ulang susunan lemari atau kulkas, dan bongkar muat barang.',
            prices: { basic: '80.000', standard: '130.000', premium: '200.000' },
            notes: {
                basic: 'Paket Standar untuk angkat 1-2 perabotan berat dalam rumah/gedung.',
                standard: 'Paket Lengkap untuk relokasi beberapa perabot besar.',
                premium: 'Paket Premium bongkar muat beban berat penuh seharian.'
            }
        },
        'Antar / Jemput': {
            title: 'Antar Jemput Dokumen & Barang Kilat',
            cat: 'Antar & Belanja',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/banner-delivery.jpg") }}',
            desc: 'Jasa kurir personal cepat untuk kirim dokumen penting, ambil barang ketinggalan, laundry kilat atau antar jemput barang.',
            prices: { basic: '50.000', standard: '80.000', premium: '130.000' },
            notes: {
                basic: 'Paket Standar jarak dekat (hingga 5 km).',
                standard: 'Paket Lengkap jarak menengah (hingga 15 km) kilat langsung sampai.',
                premium: 'Paket Premium pengantaran prioritas jarak jauh se-Jabodetabek.'
            }
        },
        'SanSu Food': {
            title: 'Belanja Pasar Subuh & Supermarket',
            cat: 'Antar & Belanja',
            badge: 'Katalog Layanan',
            heroImg: '{{ asset("images/banner-delivery.jpg") }}',
            desc: 'Bantu belanja sayur mayur dan daging segar ke pasar tradisional subuh atau supermarket terdekat sesuai catatan belanjaan Anda.',
            prices: { basic: '45.000', standard: '70.000', premium: '110.000' },
            notes: {
                basic: 'Paket Standar belanja 1 lokasi dekat dengan daftar belanja ringkas.',
                standard: 'Paket Lengkap belanja pasar tradisional subuh pilihin yang segar & tawar harga.',
                premium: 'Paket Premium belanja di beberapa titik toko sekaligus diantar langsung.'
            }
        },
    };

    let currentActiveModernService = null;
    let currentSelectedPkgType = 'basic';

    function openModernServiceDetail(serviceName) {
        // Pengunjung umum wajib masuk akun: jangan tampilkan detail, langsung arahkan ke halaman login
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            const redirectUrl = encodeURIComponent(window.location.pathname + window.location.search);
            const serviceParam = encodeURIComponent(serviceName || '');
            window.location.href = "{{ route('customer.login') }}?redirect=" + redirectUrl + "&layanan=" + serviceParam;
            return;
        }

        let data = serviceModernData[serviceName];
        if (!data) {
            // Fallback generic data
            data = {
                title: serviceName,
                cat: 'Layanan SayaBantu',
                badge: 'Katalog Layanan',
                heroImg: '{{ asset("images/technician-card.jpg") }}',
                desc: 'Layanan suruhan profesional SayaBantu. Dikerjakan langsung oleh mitra berpengalaman dengan penugasan otomatis.',
                prices: { basic: '60.000', standard: '100.000', premium: '150.000' },
                notes: {
                    basic: 'Paket standar pengerjaan dasar esensial.',
                    standard: 'Paket lengkap dengan jangkauan tugas lebih luas.',
                    premium: 'Paket prioritas bebas repot.'
                }
            };
        }

        currentActiveModernService = { name: serviceName, data: data };
        currentSelectedPkgType = 'basic';

        document.getElementById('detailServiceTitle').innerText = data.title;
        document.getElementById('detailServiceBadge').innerText = data.badge || data.cat;
        document.getElementById('detailServiceHeroImg').src = data.heroImg;
        document.getElementById('detailServiceDesc').innerText = data.desc;
        document.getElementById('detailPkgNote').innerText = data.notes.basic;
        document.getElementById('detailServiceBtnPrice').innerText = 'Rp ' + data.prices.basic;

        // Reset package buttons styling
        const pkgButtons = ['Basic', 'Standard', 'Premium'];
        pkgButtons.forEach(p => {
            const b = document.getElementById('pkgBtn' + p);
            if (b) {
                b.className = 'py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all';
            }
        });
        const btnBasic = document.getElementById('pkgBtnBasic');
        if (btnBasic) {
            btnBasic.className = 'py-1.5 rounded-lg bg-white text-[#173B67] shadow-xs transition-all';
        }

        const btnSelect = document.getElementById('btnSelectServicePackage');
        if (btnSelect) {
            btnSelect.innerHTML = `<span>Pilih Standar (Rp ${data.prices.basic})</span>`;
        }

        openModal('modalDetailServiceModern');
    }

    function selectServicePackage(pkgType, btn) {
        if (!currentActiveModernService) return;
        currentSelectedPkgType = pkgType;
        const data = currentActiveModernService.data;

        // Update button styles
        ['Basic', 'Standard', 'Premium'].forEach(p => {
            const b = document.getElementById('pkgBtn' + p);
            if (b) b.className = 'py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition-all';
        });
        btn.className = 'py-1.5 rounded-lg bg-white text-[#173B67] shadow-xs transition-all';

        const labelMap = { basic: 'Standar', standard: 'Lengkap', premium: 'Premium' };
        const price = data.prices[pkgType] || data.prices.basic;
        const note = data.notes[pkgType] || data.notes.basic;

        document.getElementById('detailPkgNote').innerText = note;
        const btnSelect = document.getElementById('btnSelectServicePackage');
        if (btnSelect) {
            btnSelect.innerHTML = `<span>Pilih ${labelMap[pkgType]} (Rp ${price})</span>`;
        }
    }

    function confirmSelectedPackageOrder() {
        if (!currentActiveModernService) return;

        // Validasi identitas pengguna: wajib login untuk memesan layanan
        const user = typeof getCustomerAuth === 'function' ? getCustomerAuth() : null;
        if (!user || !user.isLoggedIn || user.role !== 'customer') {
            closeAllCustomerModals();
            if (typeof switchLoginRole === 'function') switchLoginRole('customer');
            openModal('modalLoginCustomer');
            if (typeof showCustomerToast === 'function') {
                showCustomerToast('Perlu Masuk Akun', 'Silakan masuk sebagai Customer terlebih dahulu untuk memesan layanan.', 'warning');
            }
            return;
        }

        const sName = currentActiveModernService.name;
        const data = currentActiveModernService.data;
        const price = data.prices[currentSelectedPkgType] || data.prices.basic;
        const labelMap = { basic: 'Standar', standard: 'Lengkap', premium: 'Premium' };

        closeAllCustomerModals();

        // Populate order form. Di dashboard customer form-nya ber-id "custForm*",
        // sedangkan di beranda publik ber-id "form*". Coba keduanya supaya paket
        // yang dipilih benar-benar masuk ke formulir di halaman mana pun.
        const selLayanan = document.getElementById('custFormLayanan') || document.getElementById('formLayanan');
        const budgetInput = document.getElementById('custFormBudget') || document.getElementById('formBudget');
        const noteInput = document.getElementById('custFormNote') || document.getElementById('formNote');
        const formCard = document.getElementById('custOrderFormCard') || document.getElementById('orderFormCard');

        if (selLayanan && budgetInput) {
            // Find option or select generic
            let found = false;
            for (let i = 0; i < selLayanan.options.length; i++) {
                if (selLayanan.options[i].value === sName) {
                    selLayanan.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                for (let i = 0; i < selLayanan.options.length; i++) {
                    if (selLayanan.options[i].value.toLowerCase().includes(sName.toLowerCase())) {
                        selLayanan.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
            }
            budgetInput.value = price;
            if (noteInput && !noteInput.value) {
                noteInput.value = `Pilihan Paket: ${labelMap[currentSelectedPkgType]} (Rp ${price}). Mohon konfirmasi penugasan mitra terdekat.`;
            }

            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                formCard.classList.add('ring-4', 'ring-[#2563EB]');
                setTimeout(() => formCard.classList.remove('ring-4', 'ring-[#2563EB]'), 1500);
            }
            showCustomerToast('Paket Dipilih', `${sName} (Paket ${labelMap[currentSelectedPkgType]} - Rp ${price}) telah dimasukkan ke formulir!`, 'success');
        } else {
            const target = `/customer/dashboard?tab=dashboard&layanan=${encodeURIComponent(sName)}&budget=${encodeURIComponent(price)}#custOrderFormCard`;
            if (typeof spaNavigateTo === 'function') {
                spaNavigateTo(target);
            } else {
                window.location.href = target;
            }
        }
    }

    function toggleFavoriteService(btn) {
        const icon = btn.querySelector('i');
        if (icon) {
            if (icon.classList.contains('fa-regular')) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid', 'text-rose-500');
                showCustomerToast('Disukai', 'Layanan ditambahkan ke favorit Anda.', 'info');
            } else {
                icon.classList.remove('fa-solid', 'text-rose-500');
                icon.classList.add('fa-regular');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        refreshCustomerAuthUI();
    });
</script>

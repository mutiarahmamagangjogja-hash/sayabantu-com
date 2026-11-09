<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Akun Customer & Mitra - SayaBantu.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; background: #E5E7EB; color: #111827; margin: 0; padding: 0; }
        .mobile-app {
            width: 100%;
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            position: relative;
            overflow-x: hidden;
            background: #F7F9FC;
            box-shadow: 0 0 40px rgba(15,23,42,0.14);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .login-top-area {
            position: relative;
            overflow: hidden;
            color: white;
            background:
                radial-gradient(circle at 92% 4%, rgba(255,255,255,0.10), transparent 27%),
                radial-gradient(circle at 0% 42%, rgba(75,110,140,0.20), transparent 32%),
                linear-gradient(
                    180deg,
                    #0E1D31 0%,
                    #0E1D31 24%,
                    #11263D 38%,
                    #17334C 52%,
                    #294D65 65%,
                    #5F7C8E 77%,
                    #A7BAC6 87%,
                    #D9E3E9 94%,
                    #EDF2F6 97%,
                    #F7F9FC 100%
                );
            padding: 24px 20px 48px;
        }
    </style>

    {{-- AUTO-PURGE SCRIPT TO ENSURE CLEAN ZERO START --}}
    <script>
        (function autoCleanDummyData() {
            try {
                const CLEAN_KEY = "sb_zero_reset_v8";
                if (!localStorage.getItem(CLEAN_KEY)) {
                    localStorage.removeItem("sb_customer_orders");
                    localStorage.removeItem("sb_active_order");
                    localStorage.removeItem("sb_mitra_orders");
                    localStorage.removeItem("sb_mitra_bids");
                    localStorage.removeItem("sb_complaints");
                    localStorage.removeItem("sb_customer_pembayaran_selesai");
                    localStorage.setItem(CLEAN_KEY, "true");
                }
            } catch(e) {}
        })();
    </script>
</head>
<body>

<div class="mobile-app">
    
    <div>
        {{-- HEADER GRADIENT --}}
        <div class="login-top-area">
            <div class="flex items-center justify-end">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/15 text-white border border-white/25" id="headerRoleBadge">
                    Customer & Mitra
                </span>
            </div>

            <div class="mt-6 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-400 to-blue-600 flex items-center justify-center text-white font-black text-lg shadow-md mx-auto border border-white/20">
                    SB
                </div>
                <h2 class="text-xl font-extrabold text-white mt-3">Masuk ke SayaBantu</h2>
                <p class="text-xs text-white/70 mt-1">Pilih peran akun Anda untuk melanjutkan</p>

                {{-- DUAL ROLE TAB SWITCHER --}}
                <div class="max-w-[280px] mx-auto grid grid-cols-2 gap-1.5 p-1 bg-black/30 rounded-xl mt-4 text-xs font-bold">
                    <button 
                        type="button" 
                        id="tabCustomerBtn"
                        onclick="setLoginTab('customer')"
                        class="py-2 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5"
                    >
                        <i class="fa-solid fa-user text-[11px]"></i> Customer
                    </button>
                    <button 
                        type="button" 
                        id="tabMitraBtn"
                        onclick="setLoginTab('mitra')"
                        class="py-2 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5"
                    >
                        <i class="fa-solid fa-helmet-safety text-[11px]"></i> Mitra
                    </button>
                </div>
            </div>
        </div>

        {{-- FORM LOGIN CARD --}}
        <div class="px-5 -mt-6 relative z-10 space-y-4">
            
            {{-- NOTICE JIKA DIARAHKAN KARENA KLIK LAYANAN --}}
            <div id="loginNoticeLayanan" class="hidden p-3 rounded-2xl bg-sky-50 border border-sky-200 text-slate-800 text-xs shadow-xs flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 text-sm shrink-0"></i>
                <div class="leading-tight">
                    <span class="font-bold text-[#0E1D31] block">Masuk Akun Diperlukan</span>
                    <span class="text-[11px] text-slate-600 mt-0.5 block" id="loginNoticeLayananText">Silakan masuk akun terlebih dahulu untuk melihat detail dan memesan layanan.</span>
                </div>
            </div>

            {{-- TAB 1: KONTEN CUSTOMER --}}
            <div id="contentCustomerBox" class="space-y-4">
                {{-- ALERT ERROR LOGIN CUSTOMER --}}
                <div id="customerLoginAlert" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-2 shadow-xs transition-all">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-base shrink-0"></i>
                        <div class="flex-1">
                            <strong class="block text-xs font-bold text-rose-900" id="customerAlertTitle">Nomor Handphone Belum Terdaftar</strong>
                            <p class="text-[11px] text-rose-700 leading-relaxed mt-0.5" id="customerAlertMsg">
                                Nomor ini belum terdaftar sebagai customer SayaBantu. Anda wajib mendaftar akun terlebih dahulu sebelum masuk.
                            </p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-rose-200/80 flex items-center justify-between text-xs">
                        <button type="button" onclick="openRegisterModal()" class="font-extrabold text-[#173B67] hover:underline flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-user-plus text-[10px]"></i> Daftar Akun Customer Sekarang »
                        </button>
                        <button type="button" onclick="dismissAlert('customerLoginAlert')" class="text-slate-400 hover:text-slate-600 text-[10px] cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>

                {{-- MANUAL FORM CUSTOMER --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3.5 text-xs">
                    <form onsubmit="handleCustomerForm(event)" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Handphone Customer</label>
                            <input 
                                type="tel" 
                                id="inputPhone" 
                                placeholder="Contoh: 0812-3456-7890" 
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Kata Sandi</label>
                            <input 
                                type="password" 
                                id="inputPassword" 
                                placeholder="Masukkan kata sandi" 
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                                required
                            >
                        </div>
                        <button 
                            type="submit" 
                            id="btnSubmitCustomer"
                            class="w-full py-3 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk Akun Customer
                        </button>
                    </form>

                    <div class="pt-2 text-center text-[11px] text-slate-500 border-t border-slate-100">
                        Belum punya akun Customer?
                        <button type="button" onclick="openRegisterModal()" class="text-[#173B67] font-bold hover:underline ml-1 cursor-pointer">
                            Daftar di sini »
                        </button>
                    </div>
                </div>
            </div>

            {{-- TAB 2: KONTEN MITRA --}}
            <div id="contentMitraBox" class="hidden space-y-4">
                {{-- ALERT ERROR LOGIN MITRA --}}
                <div id="mitraLoginAlert" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-2 shadow-xs transition-all">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-base shrink-0"></i>
                        <div class="flex-1">
                            <strong class="block text-xs font-bold text-rose-900" id="mitraAlertTitle">Mitra Belum Terdaftar</strong>
                            <p class="text-[11px] text-rose-700 leading-relaxed mt-0.5" id="mitraAlertMsg">
                                ID atau nomor handphone mitra ini belum terdaftar di sistem. Anda wajib melakukan pendaftaran mitra terlebih dahulu sebelum dapat masuk portal.
                            </p>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-rose-200/80 flex items-center justify-between text-xs">
                        <a href="{{ route('mitra.daftar') }}" class="font-extrabold text-emerald-800 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-helmet-safety text-[10px]"></i> Buka Pendaftaran Mitra Baru »
                        </a>
                        <button type="button" onclick="dismissAlert('mitraLoginAlert')" class="text-slate-400 hover:text-slate-600 text-[10px] cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>

                {{-- MANUAL FORM MITRA --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3.5 text-xs">
                    <form onsubmit="handleMitraForm(event)" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">ID Mitra / No. Handphone Terdaftar</label>
                            <input 
                                type="text" 
                                id="inputMitraId" 
                                placeholder="#MTR-0042 atau 081398765432" 
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">PIN Mitra</label>
                            <input 
                                type="password" 
                                id="inputMitraPass" 
                                placeholder="Masukkan PIN / Sandi" 
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                                required
                            >
                        </div>
                        <button 
                            type="submit" 
                            id="btnSubmitMitra"
                            class="w-full py-3 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fa-solid fa-helmet-safety"></i> Masuk Portal Mitra
                        </button>
                    </form>

                    <div class="pt-2 text-center text-[11px] text-slate-500 border-t border-slate-100">
                        Tertarik jadi mitra SayaBantu?
                        <a href="{{ route('mitra.daftar') }}" class="text-emerald-700 font-bold hover:underline ml-1">
                            Daftar Jadi Mitra »
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center pt-1">
                <a href="{{ route('customer.index') }}" class="text-slate-500 hover:text-slate-800 text-xs font-medium">
                    ← Lanjut Jelajahi Layanan Tanpa Login (Tamu Publik)
                </a>
            </div>

        </div>
    </div>

    {{-- FOOTER: opsi daftar untuk pengguna baru --}}
    <div class="p-5 text-center text-xs text-slate-500" id="loginPageFooterText">
        <p>
            Belum punya akun Customer?
            <button type="button" onclick="openRegisterModal()" class="text-[#173B67] font-bold hover:underline ml-1 cursor-pointer">
                Daftar Sekarang »
            </button>
        </p>
    </div>

</div>

{{-- MODAL PENDAFTARAN CUSTOMER BARU --}}
<div id="registerCustomerBackdrop" onclick="closeRegisterModal()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden transition-opacity"></div>
<div id="modalRegisterCustomer" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[92%] sm:w-full max-w-[400px] mx-auto bg-white rounded-2xl shadow-2xl z-50 hidden overflow-hidden border border-slate-200 max-h-[90vh] overflow-y-auto">
    <div class="bg-gradient-to-r from-[#0E1D31] to-[#173B67] text-white p-4 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-sky-400/20 text-sky-300 flex items-center justify-center font-bold text-sm border border-sky-400/30">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold leading-tight">Daftar Akun Customer</h3>
                    <p class="text-[10.5px] text-slate-300">Buat akun untuk memesan layanan SayaBantu</p>
                </div>
            </div>
            <button type="button" onclick="closeRegisterModal()" class="text-slate-300 hover:text-white p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
    </div>

    <div class="p-4 space-y-3 text-xs text-slate-700">
        {{-- Alert Error Register Modal --}}
        <div id="regCustModalAlert" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] leading-tight"></div>

        <form onsubmit="handleDirectRegisterCustomer(event)" class="space-y-3">
            <div>
                <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="inputRegNama" 
                    placeholder="Contoh: Budi Santoso" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                    required
                >
            </div>
            <div>
                <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Nomor Handphone Aktif</label>
                <input 
                    type="tel" 
                    id="inputRegPhone" 
                    placeholder="Contoh: 081234567890" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                    required
                >
            </div>
            <div>
                <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Kata Sandi (Min. 6 Karakter)</label>
                <input 
                    type="password" 
                    id="inputRegPass" 
                    placeholder="Minimal 6 karakter" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                    required
                >
            </div>
            <div>
                <label class="block text-[10.5px] font-bold text-slate-600 mb-1">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    id="inputRegPassConfirm" 
                    placeholder="Ketik ulang kata sandi" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#173B67] focus:outline-none"
                    required
                >
            </div>

            <button 
                type="submit" 
                class="w-full py-2.5 rounded-xl bg-[#0E1D31] hover:bg-[#173B67] text-white font-bold text-xs shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5"
            >
                <i class="fa-solid fa-circle-check"></i>
                <span>Daftar Akun Customer</span>
            </button>
        </form>

        <div class="pt-2 text-center text-[11px] text-slate-500 border-t border-slate-100">
            Sudah pernah mendaftar?
            <button type="button" onclick="closeRegisterModal()" class="text-[#173B67] font-bold hover:underline ml-1 cursor-pointer">
                Masuk di sini »
            </button>
        </div>
    </div>
</div>

<script>
    function showAlert(id, title, msg) {
        const el = document.getElementById(id);
        if (!el) return;
        const t = el.querySelector('#' + (id === 'customerLoginAlert' ? 'customerAlertTitle' : 'mitraAlertTitle'));
        const m = el.querySelector('#' + (id === 'customerLoginAlert' ? 'customerAlertMsg' : 'mitraAlertMsg'));
        if (t) t.innerText = title;
        if (m) m.innerText = msg;
        el.classList.remove('hidden');
    }

    function dismissAlert(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    function openRegisterModal() {
        const backdrop = document.getElementById('registerCustomerBackdrop');
        const modal = document.getElementById('modalRegisterCustomer');
        const alertEl = document.getElementById('regCustModalAlert');
        if (alertEl) alertEl.classList.add('hidden');
        if (backdrop) backdrop.classList.remove('hidden');
        if (modal) modal.classList.remove('hidden');
        const phone = document.getElementById('inputPhone');
        const regPhone = document.getElementById('inputRegPhone');
        if (phone && regPhone && phone.value) regPhone.value = phone.value;
    }

    function closeRegisterModal() {
        const backdrop = document.getElementById('registerCustomerBackdrop');
        const modal = document.getElementById('modalRegisterCustomer');
        if (backdrop) backdrop.classList.add('hidden');
        if (modal) modal.classList.add('hidden');
    }

    function setLoginTab(role) {
        const btnCust = document.getElementById('tabCustomerBtn');
        const btnMitra = document.getElementById('tabMitraBtn');
        const boxCust = document.getElementById('contentCustomerBox');
        const boxMitra = document.getElementById('contentMitraBox');
        const badge = document.getElementById('headerRoleBadge');
        const footer = document.getElementById('loginPageFooterText');

        dismissAlert('customerLoginAlert');
        dismissAlert('mitraLoginAlert');

        if (role === 'mitra') {
            btnMitra.className = 'py-2 rounded-lg transition-all text-center bg-white text-emerald-900 shadow-xs flex items-center justify-center gap-1.5 cursor-pointer';
            btnCust.className = 'py-2 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 cursor-pointer';
            boxMitra.classList.remove('hidden');
            boxCust.classList.add('hidden');
            if (badge) badge.innerText = 'Portal Mitra';
            if (footer) {
                footer.innerHTML = '<p>Belum terdaftar sebagai Mitra? <a href="{{ route("mitra.daftar") }}" class="text-emerald-700 font-bold hover:underline ml-1">Daftar Jadi Mitra »</a></p>';
            }
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '{{ route("mitra.login") }}');
            }
        } else {
            btnCust.className = 'py-2 rounded-lg transition-all text-center bg-white text-[#0E1D31] shadow-xs flex items-center justify-center gap-1.5 cursor-pointer';
            btnMitra.className = 'py-2 rounded-lg transition-all text-center text-slate-300 hover:text-white flex items-center justify-center gap-1.5 cursor-pointer';
            boxCust.classList.remove('hidden');
            boxMitra.classList.add('hidden');
            if (badge) badge.innerText = 'Akun Customer';
            if (footer) {
                footer.innerHTML = '<p>Belum punya akun Customer? <button type="button" onclick="openRegisterModal()" class="text-[#173B67] font-bold hover:underline ml-1 cursor-pointer">Daftar Sekarang »</button></p>';
            }
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '{{ route("customer.login") }}');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = '{{ $role ?? "" }}' || (window.location.pathname.includes('/mitra') ? 'mitra' : 'customer');
        setLoginTab(initialRole);

        // Cek jika diarahkan dari klik layanan
        try {
            const params = new URLSearchParams(window.location.search);
            const layanan = params.get('layanan');
            if (layanan) {
                const noticeBox = document.getElementById('loginNoticeLayanan');
                const noticeTxt = document.getElementById('loginNoticeLayananText');
                if (noticeBox && noticeTxt) {
                    noticeTxt.innerText = `Silakan masuk akun terlebih dahulu untuk melihat detail dan memesan layanan ${decodeURIComponent(layanan)}.`;
                    noticeBox.classList.remove('hidden');
                }
            }
            if (params.get('daftar') === '1') {
                openRegisterModal();
            }
        } catch(e) {}
    });

    function loginCustomerAndRedirect(name, phone) {
        document.cookie = "sb_customer_logged_out=false; path=/; max-age=31536000";
        localStorage.removeItem('sb_customer_logged_out');
        localStorage.setItem('sb_customer_auth', JSON.stringify({
            isLoggedIn: true,
            name: name,
            phone: phone,
            role: 'customer'
        }));

        const params = new URLSearchParams(window.location.search);
        const redirect = params.get('redirect');
        const layanan = params.get('layanan');

        if (redirect) {
            let targetUrl = decodeURIComponent(redirect);
            if (layanan) {
                targetUrl += (targetUrl.includes('?') ? '&' : '?') + 'layanan=' + encodeURIComponent(layanan);
            }
            window.location.href = targetUrl;
            return;
        }

        window.location.href = "{{ route('customer.dashboard') }}";
    }

    function loginMitraAndRedirect(name, phone, specialty, mitraId = null) {
        document.cookie = "sb_mitra_logged_out=false; path=/; max-age=31536000";
        localStorage.removeItem('sb_mitra_logged_out');
        localStorage.setItem('sb_mitra_auth', JSON.stringify({
            isLoggedIn: true,
            name: name,
            phone: phone,
            id: mitraId,
            mitra_id: mitraId,
            specialty: specialty,
            role: 'mitra',
            status: 'approved'
        }));
        window.location.href = "{{ route('mitra.dashboard') }}";
    }

    function handleCustomerForm(e) {
        e.preventDefault();
        dismissAlert('customerLoginAlert');
        const phone = document.getElementById('inputPhone').value.trim();
        const pass = document.getElementById('inputPassword').value;

        if (!phone) {
            showAlert('customerLoginAlert', 'Nomor Handphone Kosong', 'Harap masukkan nomor handphone aktif Anda.');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memverifikasi Akun...';
        }

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
            loginCustomerAndRedirect(data.user?.name || 'Customer', data.user?.phone || phone);
        })
        .catch((err) => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Masuk Sekarang';
            }
            showAlert('customerLoginAlert', 'Gagal Masuk', err.message || 'Nomor handphone atau kata sandi tidak cocok.');
        });
    }

    function handleMitraForm(e) {
        e.preventDefault();
        dismissAlert('mitraLoginAlert');
        const idOrPhone = document.getElementById('inputMitraId').value.trim();
        const pass = document.getElementById('inputMitraPass').value;

        if (!idOrPhone) {
            showAlert('mitraLoginAlert', 'Identitas Kosong', 'Harap masukkan ID Mitra atau nomor handphone terdaftar.');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memeriksa Data Mitra...';
        }

        fetch('{{ route("mitra.login.submit") }}', {
            method: 'POST',
            credentials: 'same-origin',
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
                const error = new Error(data.message || 'Gagal masuk mitra.');
                error.status = res.status;
                error.mitraStatus = data.status;
                throw error;
            }
            return data;
        })
        .then((data) => {
            loginMitraAndRedirect(
                data.mitra?.name || 'Mitra',
                data.mitra?.phone || idOrPhone,
                data.mitra?.specialty || 'Layanan Serbaguna',
                data.mitra?.mitra_id || data.mitra?.id || null
            );
        })
        .catch((err) => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Masuk ke Portal Mitra';
            }
            if (err.mitraStatus === 'verifikasi') {
                showAlert('mitraLoginAlert', 'Menunggu Verifikasi & ACC Admin', err.message);
                return;
            }
            showAlert('mitraLoginAlert', 'Gagal Masuk Mitra', err.message || 'ID Mitra atau PIN keamanan salah.');
        });
    }

    function handleDirectRegisterCustomer(e) {
        e.preventDefault();
        const alertEl = document.getElementById('regCustModalAlert');
        const nama = document.getElementById('inputRegNama').value.trim();
        const phone = document.getElementById('inputRegPhone').value.trim();
        const pass = document.getElementById('inputRegPass').value;
        const confirm = document.getElementById('inputRegPassConfirm').value;

        if (!nama || nama.length < 2) {
            if (alertEl) {
                alertEl.innerText = 'Nama lengkap minimal 2 karakter.';
                alertEl.classList.remove('hidden');
            }
            return;
        }
        if (!phone || phone.length < 9) {
            if (alertEl) {
                alertEl.innerText = 'Nomor handphone aktif minimal 9 digit.';
                alertEl.classList.remove('hidden');
            }
            return;
        }
        if (!pass || pass.length < 6) {
            if (alertEl) {
                alertEl.innerText = 'Kata sandi minimal 6 karakter.';
                alertEl.classList.remove('hidden');
            }
            return;
        }
        if (pass !== confirm) {
            if (alertEl) {
                alertEl.innerText = 'Konfirmasi kata sandi tidak sama dengan kata sandi.';
                alertEl.classList.remove('hidden');
            }
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan ke Database...';
        }

        fetch('{{ route("customer.register") }}', {
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
        })
        .then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Pendaftaran gagal disimpan.');
            }
            return data;
        })
        .then((data) => {
            closeRegisterModal();
            loginCustomerAndRedirect(data.user?.name || nama, data.user?.phone || phone);
        })
        .catch((err) => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Daftar Sekarang';
            }
            if (alertEl) {
                alertEl.innerText = err.message || 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.';
                alertEl.classList.remove('hidden');
            }
        });
    }
</script>

</body>
</html>

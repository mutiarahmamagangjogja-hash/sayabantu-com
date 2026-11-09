{{-- =====================================================
     CATATAN AKSES HALAMAN CUSTOMER (TANPA PAKSA LOGIN)
     -----------------------------------------------------
     Sertakan di halaman khusus customer.

     Halaman ini TIDAK memaksa pengunjung untuk login dan TIDAK
     membuka popup/dialihkan ke halaman login. Yang tampil hanya
     baris teks pengingat biasa (bukan greeting card) di bawah
     header saat pengunjung belum masuk akun.

     Begitu pengguna masuk sebagai customer, teks pengingat akan
     hilang otomatis.

     Pemakaian:
       @include('customer.partials.guard-login')
       @include('customer.partials.guard-login', ['pesan' => 'Kustom...'])
====================================================== --}}
@php
    $pesanGuard = $pesan ?? 'Silakan masuk akun terlebih dahulu untuk mengakses halaman ini.';
@endphp
<script>
    (function() {
        var PESAN = @json($pesanGuard);
        var ID_BANNER = 'guardInfoBanner';

        function sudahLoginCustomer() {
            try {
                var raw = localStorage.getItem('sb_customer_auth');
                var user = raw ? JSON.parse(raw) : null;
                return !!(user && user.isLoggedIn && user.role === 'customer');
            } catch (e) {
                return false;
            }
        }

        function hapusBanner() {
            var lama = document.getElementById(ID_BANNER);
            if (lama && lama.parentNode) lama.parentNode.removeChild(lama);
        }

        function tampilkanBanner() {
            var area = document.querySelector('.customer-top-area');
            if (!area) return;

            hapusBanner();

            var banner = document.createElement('section');
            banner.id = ID_BANNER;
            banner.className = 'relative z-10 px-5 pb-3';
            banner.innerHTML =
                '<p class="text-[10px] text-white/70 leading-relaxed">' +
                    PESAN +
                '</p>';

            area.appendChild(banner);
        }

        function cekGuard() {
            if (sudahLoginCustomer()) {
                hapusBanner();
                return;
            }
            tampilkanBanner();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cekGuard);
        } else {
            cekGuard();
        }

        // Sinkronkan saat status login berubah (mis. baru selesai login).
        window.addEventListener('storage', function (e) {
            if (e && e.key === 'sb_customer_auth') cekGuard();
        });
    })();
</script>
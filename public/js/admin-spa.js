/**
 * SayaBantu.com - Admin SPA Navigation Engine
 * -------------------------------------------------------------
 * Pindah antar fitur admin tanpa reload halaman dan tanpa layar loading.
 *
 * Cara kerja:
 *  - Header, drawer, panel notifikasi, bottom navigation, dan seluruh modal
 *    berada di luar #sbAdminContent sehingga TIDAK ikut diganti. Hasilnya
 *    transisi terasa instan (tanpa kedip) dan state UI tetap hidup.
 *  - Halaman tujuan diambil via fetch, di-cache di memori, lalu hanya bagian
 *    #sbAdminContent (header judul + hero + konten + modal halaman) yang
 *    diganti.
 *  - Link internal admin diintersepsi; tombol back/forward browser tetap
 *    berfungsi karena memakai history.pushState.
 */
(function () {
    'use strict';

    var domCache = new Map();
    var isNavigating = false;

    // Dipakai saat halaman dimuat ulang penuh tanpa reload, misalnya dari
    // tombol back/forward yang mendarat di URL yang sama.
    var adminSpaForceRerender = false;

    // Selama jendela singkat setelah swap halaman, handler "tutup semua modal"
    // milik script halaman sebelumnya diabaikan agar modal halaman baru tidak
    // langsung ikut tertutup.
    var adminModalGuardUntil = 0;

    window.adminNavHistoryCount = window.adminNavHistoryCount || 0;
    window.adminDomCache = domCache;

    /* ---------------------------------------------------------
       Utility URL
    --------------------------------------------------------- */
    function toAdminLocalUrl(url) {
        if (!url) return null;
        try {
            var u = new URL(url, window.location.origin);
            if (u.origin === window.location.origin) {
                u.protocol = window.location.protocol;
                u.host = window.location.host;
            }
            return u;
        } catch (e) {
            return null;
        }
    }

    function normalizeAdminUrl(url) {
        var u = toAdminLocalUrl(url);
        if (!u) return url;
        return u.origin + u.pathname.replace(/\/+$/, '') + (u.search || '');
    }

    function cacheKeys(url) {
        var u = toAdminLocalUrl(url);
        if (!u) return [];
        return [
            u.href,
            normalizeAdminUrl(u.href),
            u.pathname + (u.search || ''),
            u.pathname.replace(/\/+$/, '') || '/'
        ];
    }

    function setAdminDomCache(url, html) {
        if (!html) return;
        cacheKeys(url).forEach(function (key) {
            domCache.set(key, html);
        });
    }

    function getAdminDomCache(url) {
        var keys = cacheKeys(url);
        for (var i = 0; i < keys.length; i++) {
            if (domCache.has(keys[i])) return domCache.get(keys[i]);
        }
        return null;
    }

    function invalidateAdminCache(pattern) {
        if (!pattern) {
            domCache.clear();
            return;
        }
        Array.from(domCache.keys()).forEach(function (key) {
            if (key.indexOf(pattern) !== -1) domCache.delete(key);
        });
    }

    function isInternalAdminRoute(url) {
        var u = toAdminLocalUrl(url);
        if (!u) return false;
        if (u.origin !== window.location.origin) return false;

        var p = u.pathname;
        if (p.indexOf('/admin') !== 0) return false;
        if (/^\/admin\/(logout|fast-login|login|api)/.test(p)) return false;

        return true;
    }

    /* ---------------------------------------------------------
       Indikator navigasi di bottom bar
    --------------------------------------------------------- */
    function updateAdminBottomNav(targetUrl) {
        var u = toAdminLocalUrl(targetUrl);
        if (!u) return;

        var path = u.pathname;
        var navItems = document.querySelectorAll('.bottom-navigation .bottom-nav-item');

        Array.prototype.forEach.call(navItems, function (item) {
            var href = item.getAttribute('href') || '';
            var isActive = false;

            if (href.indexOf('/admin/dashboard') !== -1 && (path === '/admin' || path === '/admin/dashboard')) {
                isActive = true;
            } else if ((href.indexOf('/admin/pengguna') !== -1 || href.indexOf('/admin/mitra') !== -1) &&
                (path.indexOf('/admin/pengguna') !== -1 || path.indexOf('/admin/mitra') !== -1)) {
                isActive = true;
            } else if ((href.indexOf('/admin/pesanan') !== -1 || href.indexOf('/admin/suruhan') !== -1) &&
                (path.indexOf('/admin/pesanan') !== -1 || path.indexOf('/admin/suruhan') !== -1)) {
                isActive = true;
            } else if (href.indexOf('/admin/pembayaran') !== -1 && path.indexOf('/admin/pembayaran') !== -1) {
                isActive = true;
            }

            item.classList.toggle('nav-active', isActive);
        });
    }

    /* ---------------------------------------------------------
       Pengambilan HTML halaman
    --------------------------------------------------------- */
    function fetchAdminHtml(url, cacheOnly) {
        var cached = getAdminDomCache(url);
        if (cached) return Promise.resolve(cached);

        return fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (res) {
                if (!res.ok && !cacheOnly && (res.status === 401 || res.status === 403)) {
                    return fetch('/admin/fast-login', { credentials: 'same-origin' })
                        .then(function () {
                            return fetch(url, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                credentials: 'same-origin'
                            });
                        });
                }
                return res;
            })
            .then(function (res) {
                if (!res || !res.ok) return null;
                return res.text();
            })
            .then(function (htmlText) {
                if (!htmlText) return null;

                if (htmlText.indexOf('adminPasswordInput') !== -1 || htmlText.indexOf('Masuk Admin') !== -1) {
                    if (cacheOnly) return null;
                    return fetch('/admin/fast-login', { credentials: 'same-origin' })
                        .then(function () {
                            return fetch(url, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                credentials: 'same-origin'
                            });
                        })
                        .then(function (res) {
                            return res.ok ? res.text() : null;
                        })
                        .then(function (retryHtml) {
                            if (retryHtml) setAdminDomCache(url, retryHtml);
                            return retryHtml;
                        });
                }

                setAdminDomCache(url, htmlText);
                return htmlText;
            })
            .catch(function () {
                return null;
            });
    }

    /* ---------------------------------------------------------
       Eksekusi script inline milik halaman tujuan
    --------------------------------------------------------- */
    // Nama fungsi yang wajib tersedia global agar handler onclick pada konten
    // hasil swap tetap bisa dipanggil.
    var PAGE_GLOBALS = [
        'filterOrders', 'filterPayment', 'filterDeals', 'filterComplaints',
        'filterPengguna', 'filterMitra', 'switchPenggunaTab', 'searchPengguna',
        'clearSearchPengguna', 'searchChats', 'clearSearchChats', 'openChatModal', 'closeChatModal',
        'filterLayanan', 'filterModerasi', 'filterKonflik', 'filterReview',
        'openCustomerDetailByIndex', 'openMitraDetail', 'closeMitraDetailModal',
        'openModalTolakMitra', 'closeModalTolakMitra', 'setujuiMitraById', 'suspendMitraById',
        'loadAndRenderAdminOrders', 'syncAdminMitraData', 'renderAdminComplaintList',
        'showSettingDetail', 'closeSettingDetail', 'switchLaporanTab', 'saveSettings'
    ];

    function runAdminPageScript(code) {
        if (!code || !code.trim()) return;

        var exportStr = '\n;' + PAGE_GLOBALS.map(function (name) {
            return "try { if (typeof " + name + " === 'function' && typeof window['" + name + "'] !== 'function') { window['" + name + "'] = " + name + "; } } catch(e) {}";
        }).join('');

        var script = document.createElement('script');
        script.textContent = code + exportStr;
        document.body.appendChild(script);
        script.remove();
    }

    /**
     * innerHTML TIDAK mengeksekusi / menyimpan elemen <script>. Karena itu script
     * inline milik halaman tujuan diambil langsung dari dokumen hasil fetch dan
     * dijalankan berurutan, supaya fungsi filter/tab/render tetap ada setelah
     * pindah fitur tanpa reload.
     */
    function runPageScriptsFromDocument(doc) {
        if (!doc) return;

        // Script halaman bisa berada di dalam #sbAdminContent, di wadah modal,
        // atau di akhir <body> (hasil @push('scripts')). Semuanya dijalankan,
        // kecuali script milik layout agar tidak dieksekusi dua kali.
        var scripts = [];

        Array.prototype.forEach.call(doc.querySelectorAll('script'), function (script) {
            if (script.src) return;
            if (script.hasAttribute('data-layout-script')) return;
            if (!script.textContent || !script.textContent.trim()) return;

            scripts.push(script.textContent);
        });

        // Dijalankan berurutan; satu script gagal tidak menghentikan yang lain.
        scripts.forEach(function (code) {
            try {
                runAdminPageScript(code);
            } catch (err) {
                console.error('Admin page script error:', err);
            }
        });
    }

    function runScriptsWithin(container) {
        if (!container) return;

        Array.from(container.getElementsByTagName('script')).forEach(function (script) {
            if (script.src) {
                // Script eksternal halaman (jarang dipakai) cukup dimuat ulang sekali
                if (!container.querySelector('script[data-reloaded-src="' + script.src + '"]')) {
                    var clone = document.createElement('script');
                    clone.src = script.src;
                    clone.setAttribute('data-reloaded-src', script.src);
                    document.body.appendChild(clone);
                }
                return;
            }

            if (!script.hasAttribute('data-layout-script')) {
                runAdminPageScript(script.textContent);
            }
        });
    }

    /* ---------------------------------------------------------
       Navigasi utama
    --------------------------------------------------------- */
    async function adminSpaNavigateTo(url, pushState) {
        if (pushState === undefined) pushState = true;

        var targetUrl = toAdminLocalUrl(url);
        if (!targetUrl) return false;

        if (typeof window.closeBurgerModalDirect === 'function') window.closeBurgerModalDirect();
        if (typeof window.closeAllModals === 'function') window.closeAllModals();

        // Simpan URL saat ini SEBELUM pushState mengubahnya
        var currentPath = window.location.pathname;
        var currentSearch = window.location.search;

        var isSamePage = targetUrl.pathname === currentPath &&
            targetUrl.search === currentSearch;

        if (isSamePage && !adminSpaForceRerender) return false;

        // Jika masih ada transisi berjalan, jalankan permintaan terbaru setelahnya.
        // Tanpa ini, tombol back/forward beruntun akan "diam" karena tertahan.
        if (isNavigating) {
            return new Promise(function (resolve) {
                var attempts = 0;
                var retry = function () {
                    attempts++;
                    if (!isNavigating || attempts > 40) {
                        resolve(adminSpaNavigateTo(targetUrl.href, pushState));
                    } else {
                        setTimeout(retry, 50);
                    }
                };
                setTimeout(retry, 50);
            });
        }

        isNavigating = true;

        try {
            var htmlText = await fetchAdminHtml(targetUrl.href, false);

            if (!htmlText) {
                window.location.href = targetUrl.href;
                return false;
            }

            var doc = new DOMParser().parseFromString(htmlText, 'text/html');
            var newContent = doc.getElementById('sbAdminContent');
            var curContent = document.getElementById('sbAdminContent');

            if (!newContent || !curContent) {
                window.location.href = targetUrl.href;
                return false;
            }

            if (doc.title) document.title = doc.title;

            // Pastikan wadah modal & script halaman lama tidak lagi inert/
            // dibekukan oleh script halaman sebelumnya.
            Array.prototype.forEach.call(
                document.querySelectorAll('[inert], #adminDynamicModals'),
                function (el) {
                    el.removeAttribute('inert');
                    if (el.id === 'adminDynamicModals') {
                        el.style.pointerEvents = '';
                    }
                }
            );

            if (pushState) {
                window.adminNavHistoryCount += 1;
                window.history.pushState({ adminSpa: true, url: targetUrl.href }, '', targetUrl.href);
            } else {
                // Back/forward: entri history aktif juga ditandai SPA agar back
                // berikutnya tetap bisa memulihkan konten dengan tepat.
                try {
                    window.history.replaceState({ adminSpa: true, url: window.location.href }, '', window.location.href);
                } catch (e) {}
            }

            // 1. Ganti isi konten utama
            curContent.innerHTML = newContent.innerHTML;

            // 2. Ganti modal dinamis milik halaman
            var newModals = doc.getElementById('adminDynamicModals');
            var curModals = document.getElementById('adminDynamicModals');
            if (curModals) {
                curModals.innerHTML = newModals ? newModals.innerHTML : '';
            }

            // 3. Sinkronkan status navigasi
            updateAdminBottomNav(targetUrl);
            if (typeof window.syncAdminDrawerActiveNav === 'function') {
                window.syncAdminDrawerActiveNav(targetUrl.pathname);
            }

            window.scrollTo(0, 0);

            // Kunci sebentar: script halaman sebelumnya sering memanggil
            // closeAllModals() saat DOMContentLoaded/history berubah.
            adminModalGuardUntil = Date.now() + 1500;

            // 4. Setelah render selesai (dan handler tutup modal bawaan selesai
            //    berjalan), jalankan script halaman baru dari dokumen hasil fetch.
            setTimeout(function () {
                var content = document.getElementById('sbAdminContent');
                var modals = document.getElementById('adminDynamicModals');

                // Script inline tidak ikut tersalin oleh innerHTML, jadi jalankan
                // dari dokumen sumber agar filter/tab tetap hidup.
                runPageScriptsFromDocument(doc);

                runScriptsWithin(content);
                runScriptsWithin(modals);

                // Pastikan seluruh modal halaman baru kembali tersembunyi.
                if (modals) {
                    Array.prototype.forEach.call(modals.querySelectorAll('.sb-overlay-backdrop'), function (modal) {
                        modal.classList.remove('is-open', 'opacity-100', 'pointer-events-auto');
                        modal.classList.add('invisible', 'opacity-0', 'pointer-events-none');
                        modal.setAttribute('aria-hidden', 'true');
                        modal.style.removeProperty('visibility');
                        modal.style.removeProperty('opacity');
                        modal.style.removeProperty('pointer-events');
                    });
                }
            }, 0);

            // 5. Segarkan data realtime
            if (window.SayaBantuRealtime && typeof window.SayaBantuRealtime.fetchSnapshot === 'function') {
                window.SayaBantuRealtime.fetchSnapshot();
            }

            document.dispatchEvent(new CustomEvent('admin:page-changed', {
                detail: { url: targetUrl.href, path: targetUrl.pathname }
            }));

            return true;
        } catch (err) {
            console.error('Admin SPA navigation error:', err);
            window.location.href = targetUrl.href;
            return false;
        } finally {
            isNavigating = false;
            adminSpaForceRerender = false;
        }
    }

    function handleAdminBackNavigation(event, fallbackUrl) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (window.adminNavHistoryCount > 0 && window.history.length > 1) {
            window.history.back();
        } else {
            adminSpaNavigateTo(fallbackUrl, false);
        }
    }

    /* ---------------------------------------------------------
       Prefetch
    --------------------------------------------------------- */
    function prefetchAdminUrl(url) {
        var u = toAdminLocalUrl(url);
        if (!u || !isInternalAdminRoute(u.href)) return;
        if (getAdminDomCache(u.href)) return;

        fetchAdminHtml(u.href, true);
    }

    var corePrefetched = false;

    function prefetchCoreAdminRoutes() {
        setAdminDomCache(window.location.href, document.documentElement.outerHTML);
        if (corePrefetched) return;
        corePrefetched = true;

        // Prefetch massal hanya saat browser menganggur agar tidak berebut
        // koneksi dengan request pertama yang diinisiasi user.
        var run = function () { prefetchAdminDrawerRoutes(); };

        if (window.requestIdleCallback) {
            window.requestIdleCallback(run, { timeout: 6000 });
        } else {
            setTimeout(run, 2500);
        }
    }

    function prefetchAdminDrawerRoutes() {
        var seen = {};
        var index = 0;
        var links = document.querySelectorAll(
            '#sbBurgerPanel a.admin-drawer-nav-item[href], .bottom-navigation a.bottom-nav-item[href]'
        );

        Array.prototype.forEach.call(links, function (link) {
            var href = link.getAttribute('href');
            if (!href || seen[href]) return;
            seen[href] = true;

            setTimeout(function () { prefetchAdminUrl(href); }, index * 400);
            index++;
        });
    }

    /* ---------------------------------------------------------
       Event binding
    --------------------------------------------------------- */
    document.addEventListener('click', function (e) {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (e.button !== 0) return;
        if (e.defaultPrevented) return;

        var link = e.target.closest('a');
        if (!link) return;

        var href = link.getAttribute('href');
        if (!href || href === '#' || /^(javascript:|tel:|mailto:|https:\/\/wa\.me)/.test(href)) return;
        if (link.getAttribute('target') === '_blank' || link.hasAttribute('download')) return;

        var url = toAdminLocalUrl(link.href);
        if (!url || !isInternalAdminRoute(url.href)) return;

        // Paksa render ulang walau URL-nya tampak sama (mis. halaman yang
        // sebelumnya ditutup oleh deep-link lain).
        adminSpaForceRerender = true;

        e.preventDefault();
        adminSpaNavigateTo(url.href, true);
    });

    /**
     * Klik di luar panel drawer / sheet menutup overlay tersebut.
     * Tanpa ini, backdrop yang masih terbuka bisa memblokir seluruh klik
     * pengguna (terasa seperti tombol tidak berfungsi).
     */
    document.addEventListener('click', function (e) {
        if (e.target.closest && e.target.closest('.sb-backdrop-click')) {
            if (typeof window.closeAllModals === 'function') window.closeAllModals();
            if (typeof window.closeBurgerModalDirect === 'function') window.closeBurgerModalDirect();
        }
    }, true);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (typeof window.closeBurgerModalDirect === 'function') window.closeBurgerModalDirect();
            if (typeof window.closeNotificationModalDirect === 'function') window.closeNotificationModalDirect();
            if (typeof window.closeProfileQuickModalDirect === 'function') window.closeProfileQuickModalDirect();
        }
    });

    window.addEventListener('popstate', function (event) {
        window.adminNavHistoryCount = Math.max(0, window.adminNavHistoryCount - 1);

        var state = event.state;

        // Back/forward bisa mendarat di URL yang sama dengan yang tampil,
        // jadi paksa render ulang isi halaman (tanpa reload penuh).
        adminSpaForceRerender = true;

        if (state && state.adminSpa && state.url) {
            adminSpaNavigateTo(state.url, false);
            return;
        }

        adminSpaNavigateTo(window.location.href, false);
    });

    document.addEventListener('mouseover', function (e) {
        var link = e.target.closest && e.target.closest('a');
        if (link && link.href && isInternalAdminRoute(link.href)) prefetchAdminUrl(link.href);
    }, { passive: true });

    // Prefetch saat drawer dibuka: user kemungkinan besar segera memilih menu
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest &&
            e.target.closest('button[aria-label="Menu Navigasi"], [data-action="open-burger"]');
        if (!trigger) return;

        setTimeout(prefetchAdminDrawerRoutes, 150);
    }, { passive: true });

    document.addEventListener('touchstart', function (e) {
        var link = e.target.closest && e.target.closest('a');
        if (link && link.href && isInternalAdminRoute(link.href)) prefetchAdminUrl(link.href);
    }, { passive: true });

    document.addEventListener('DOMContentLoaded', function () {
        prefetchCoreAdminRoutes();
        if (typeof window.syncAdminDrawerActiveNav === 'function') window.syncAdminDrawerActiveNav();
    });

    window.addEventListener('load', function () {
        if (typeof window.syncAdminDrawerActiveNav === 'function') window.syncAdminDrawerActiveNav();
    });

    prefetchCoreAdminRoutes();

    /* ---------------------------------------------------------
       Expose global API
    --------------------------------------------------------- */
    /**
     * Apakah modal boleh ditutup otomatis pada saat ini?
     */
    function adminModalCloseAllowed() {
        return Date.now() > adminModalGuardUntil;
    }

    window.adminModalCloseAllowed = adminModalCloseAllowed;
    window.adminSpaNavigateTo = adminSpaNavigateTo;
    window.adminNavigateTo = adminSpaNavigateTo;
    window.toAdminLocalUrl = toAdminLocalUrl;
    window.normalizeAdminUrl = normalizeAdminUrl;
    window.setAdminDomCache = setAdminDomCache;
    window.getAdminDomCache = getAdminDomCache;
    window.invalidateAdminCache = invalidateAdminCache;
    window.isInternalAdminRoute = isInternalAdminRoute;
    window.updateAdminBottomNav = updateAdminBottomNav;
    window.handleAdminBackNavigation = handleAdminBackNavigation;
    window.prefetchAdminUrl = prefetchAdminUrl;
    window.prefetchCoreAdminRoutes = prefetchCoreAdminRoutes;
    window.prefetchAdminDrawerRoutes = prefetchAdminDrawerRoutes;
})();

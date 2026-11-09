const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

const PAGES = [
    { nav: 'layanan', path: '/admin/layanan', tabs: '[onclick*="filter"]', items: '[data-kategori], .layanan-card, .service-card' },
    { nav: 'voucher', path: '/admin/voucher', tabs: '.voucher-stat-tab', items: '.voucher-card' },
    { nav: 'konfirmasi', path: '/admin/konfirmasi-pekerjaan', tabs: '.deal-stat-tab', items: '.deal-card' },
    { nav: 'pengaduan', path: '/admin/pengaduan', tabs: '.complaint-stat-tab, [onclick*="filterComplaint"]', items: '.complaint-card, [data-status]' },
    { nav: 'laporan', path: '/admin/laporan-analitik', tabs: '.report-tab, [onclick*="switchLaporan"]', items: '.report-section' },
];

(async () => {
    const b = await chromium.launch({ channel: 'msedge' });
    const p = await b.newPage({ viewport: { width: 430, height: 900 } });
    const out = [];

    for (const cfg of PAGES) {
        await p.goto('http://127.0.0.1:8000/admin/dashboard');
        await wait(2200);
        await p.click('button[aria-label="Menu Navigasi"]');
        await wait(450);
        await p.click(`#sbBurgerPanel a[data-nav="${cfg.nav}"]`);
        await wait(2400);

        const base = await p.evaluate(() => ({
            path: location.pathname,
            drawerHidden: getComputedStyle(document.getElementById('sbBurgerModal')).visibility === 'hidden',
            items: document.querySelectorAll('[data-status], [data-kategori], .voucher-card, .deal-card, .report-section').length,
        }));

        const tabs = await p.$$(cfg.tabs);
        const clicks = [];
        for (let i = 0; i < Math.min(tabs.length, 3); i++) {
            const all = await p.$$(cfg.tabs);
            const label = await all[i].evaluate((el) => (el.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 18));
            let clicked = true;
            try { await all[i].click({ timeout: 4000 }); } catch (e) { clicked = false; await all[i].evaluate((el) => el.click()).catch(() => { }); }
            await wait(500);
            const after = await p.evaluate(() => ({
                visible: Array.from(document.querySelectorAll('[data-status], [data-kategori], .voucher-card, .deal-card, .report-section')).filter((e) => e.offsetParent !== null && !e.classList.contains('hidden') && getComputedStyle(e).display !== 'none').length,
            }));
            clicks.push({ label, clicked, visible: after.visible });
        }

        out.push({ nav: cfg.nav, ...base, clicks });
    }

    console.log(JSON.stringify(out, null, 1));
    await b.close();
})();

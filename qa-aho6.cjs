const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');

(async () => {
    const b = await chromium.launch({ channel: 'msedge' });
    const p = await b.newPage();
    await p.goto('http://127.0.0.1:8000/admin/dashboard');
    await p.waitForTimeout(2000);
    const r = await p.evaluate(async () => {
        const code = await fetch('/js/admin-spa.js').then((r) => r.text());
        const out = {};
        try {
            const el = document.createElement('script');
            el.textContent = code;
            el.id = 'probeSpa';
            document.head.appendChild(el);
            out.connected = el.isConnected;
            out.textLenAfter = el.textContent.length;
            out.hasAttr = el.getAttribute('data-layout-script');
            out.winFn = typeof window.adminSpaNavigateTo;
            out.inGlobal = typeof adminSpaNavigateTo;
            out.tailwind = typeof window.tailwind;
            out.ownProps = Object.getOwnPropertyNames(window).filter((k) => /admin|burger/i.test(k)).slice(0, 10);
        } catch (e) { out.err = e.message; }
        return out;
    });
    console.log(JSON.stringify(r, null, 1));
    await b.close();
})();

 const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

(async () => {
    const b = await chromium.launch({ channel: 'msedge' });
    const p = await b.newPage({ viewport: { width: 430, height: 900 } });
    await p.goto('http://127.0.0.1:8000/admin/dashboard');
    await wait(2000);
    const cdp = await p.context().newCDPSession(p);
    const ev = async (expr) => (await cdp.send('Runtime.evaluate', { expression: expr, returnByValue: true, awaitPromise: true })).result.value;

    await p.click('button[aria-label="Menu Navigasi"]');
    await wait(320);
    await p.click('#sbBurgerPanel a[data-nav="voucher"]');
    await wait(1500);
    const afterVoucher = await ev('location.pathname + " | " + (document.querySelector("#sbAdminContent h2")||{}).textContent.trim()');

    await p.goBack();
    await wait(2500);
    const afterBack = await ev('location.pathname + " | " + (document.querySelector("#sbAdminContent h2")||{}).textContent.trim()');

    await p.goForward();
    await wait(2000);
    const afterForward = await ev('location.pathname + " | " + (document.querySelector("#sbAdminContent h2")||{}).textContent.trim()');

    console.log(JSON.stringify({ afterVoucher, afterBack, afterForward }, null, 1));
    await b.close();
})();

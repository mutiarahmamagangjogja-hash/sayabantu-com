const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 430, height: 900 } });
  await page.addInitScript(() => {
    window.__res = [];
    window.addEventListener('error', (e) => {
      window.__res.push('ERR ' + (e.message || '') + ' file=' + String(e.filename || '').replace(location.origin, '') + ':' + e.lineno + ':' + e.colno + ' tag=' + (e.target && e.target.tagName));
    }, true);
    // detect blocked inline execution via error event target script
    const obs = new MutationObserver(() => { });
  });
  await page.goto('http://127.0.0.1:8000/admin/voucher');
  await wait(3000);
  const r = await page.evaluate(() => ({ res: window.__res, spa: typeof window.adminSpaNavigateTo, tailwind: typeof window.tailwind }));
  console.log(JSON.stringify(r, null, 1));
  await browser.close();
})();

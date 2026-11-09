const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');
const vm = require('vm');
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

(async () => {
    const b = await chromium.launch({ channel: 'msedge' });
    const p = await b.newPage();
    await p.goto('http://127.0.0.1:8000/admin/dashboard');
    await wait(2000);
    const src = await p.evaluate(() => fetch('/js/admin-spa.js').then((r) => r.text()));
    const sandbox = { window: {}, document: { addEventListener() { }, querySelectorAll: () => [], getElementById: () => null, createElement: () => ({}) }, fetch: () => Promise.resolve(), console };
    sandbox.window = sandbox;
    sandbox.window.location = { origin: 'http://x', pathname: '/', search: '' };
    try { vm.runInNewContext(src, sandbox); console.log('vm ok, typeof spa:', typeof sandbox.adminSpaNavigateTo); }
    catch (e) { console.log('vm ERR:', e.message); }
    await b.close();
})();

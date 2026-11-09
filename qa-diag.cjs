const { chromium } = require('C:/Users/Msi/.vscode/extensions/danielsanmedium.dscodegpt-3.24.79/standalone/node_modules/patchright-core');
const fs = require('fs');
const vm = require('vm');
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

(async () => {
    const b = await chromium.launch({ channel: 'msedge' });
    const p = await b.newPage();
    await p.goto('http://127.0.0.1:8000/admin/dashboard');
    await wait(1500);
    const src = await p.evaluate(() => fetch('/js/admin-spa.js').then((r) => r.text()));
    fs.writeFileSync('qa-current-spa.js', src);

    // Run in a fake environment where everything is a no-op to see if top-level throws
    const handlers = {};
    const fakeEl = { addEventListener() { }, classList: { toggle() { }, add() { }, remove() { } }, setAttribute() { }, getAttribute: () => null, style: {} };
    const doc = {
        addEventListener(t, fn) { handlers[t] = fn; },
        querySelectorAll: () => [],
        querySelector: () => null,
        getElementById: () => null,
        createElement: () => fakeEl,
        documentElement: { outerHTML: '' },
        readyState: 'complete',
        title: 'x',
    };
    const win = {
        location: { origin: 'http://x', pathname: '/admin/dashboard', search: '', href: 'http://x/admin/dashboard', protocol: 'http:', host: 'x' },
        history: { pushState() { }, length: 1 },
        addEventListener(t, fn) { handlers['win:' + t] = fn; },
        requestIdleCallback: undefined,
        document: doc,
    };
    win.window = win;
    const sandbox = {
        window: win, document: doc, console, URL, Map, Set, Array, Object, Promise, setTimeout, CustomEvent: function () { }, fetch: () => new Promise(() => { }), Node: function () { },
    };
    try {
        vm.runInNewContext(src, sandbox);
        console.log('TOP-LEVEL OK; spa:', typeof win.adminSpaNavigateTo, 'handlers:', Object.keys(handlers));
        // Now trigger the click handler for a fake anchor pointing to /admin/voucher
        const anchor = {
            getAttribute: (n) => (n === 'href' ? '/admin/voucher' : null),
            hasAttribute: () => false,
            href: 'http://x/admin/voucher',
        };
        const fakeEvent = { metaKey: false, ctrlKey: false, shiftKey: false, altKey: false, button: 0, defaultPrevented: false, target: { closest: () => anchor }, preventDefault() { this.defaultPrevented = true; } };
        handlers['click'](fakeEvent);
        console.log('click handled, prevented:', fakeEvent.defaultPrevented);
    } catch (e) {
        console.log('TOP-LEVEL THROW:', e.message);
    }
    await b.close();
})();

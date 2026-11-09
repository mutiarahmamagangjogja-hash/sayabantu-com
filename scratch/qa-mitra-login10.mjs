export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`.slice(0, 300)));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  page.on('response', (r) => {
    if (r.url().includes('/api/mitra')) logs.push(`[api] ${r.status()} ${r.url()}`);
  });

  // inject an init script that runs on EVERY document, before page scripts
  await page.addInitScript(() => {
    window.__nav = [];
    const rec = (type, url) => {
      window.__nav.push({ type, url: String(url), stack: (new Error().stack || '').split('\n').slice(1, 6).join(' | ') });
      try { sessionStorage.setItem('__nav', JSON.stringify(window.__nav)); } catch (e) { }
    };
    const origReplace = window.location.replace.bind(window.location);
    const origAssign = window.location.assign.bind(window.location);
    try {
      window.location.replace = (u) => { rec('replace', u); return origReplace(u); };
      window.location.assign = (u) => { rec('assign', u); return origAssign(u); };
    } catch (e) { }
    const desc = Object.getOwnPropertyDescriptor(Location.prototype, 'href');
    try {
      Object.defineProperty(window.location, 'href', {
        configurable: true,
        get() { return desc.get.call(window.location); },
        set(v) { rec('href', v); desc.set.call(window.location, v); },
      });
    } catch (e) { }
  });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();
  await page.waitForTimeout(6000);

  const nav = await page.evaluate(() => sessionStorage.getItem('__nav')).catch(() => null);
  return { finalUrl: page.url(), nav, logs };
}

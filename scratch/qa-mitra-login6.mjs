export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`.slice(0, 400)));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  page.on('response', (r) => {
    if (r.url().includes('/api/mitra')) logs.push(`[api] ${r.status()} ${r.url()}`);
  });

  // instrument redirects right before login so we know who calls replace()
  await page.evaluate(() => {
    window.__redirects = [];
    const origReplace = window.location.replace.bind(window.location);
    const origAssign = window.location.assign.bind(window.location);
    try {
      window.location.replace = (u) => {
        window.__redirects.push({ type: 'replace', url: u, stack: new Error().stack });
        return origReplace(u);
      };
      window.location.assign = (u) => {
        window.__redirects.push({ type: 'assign', url: u, stack: new Error().stack });
        return origAssign(u);
      };
    } catch (e) { }
  });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();
  await page.waitForTimeout(6000);
  return { url: page.url(), logs };
}

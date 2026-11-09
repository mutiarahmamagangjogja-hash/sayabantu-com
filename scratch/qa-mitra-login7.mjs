export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`.slice(0, 400)));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  page.on('response', (r) => {
    if (r.url().includes('/api/mitra')) logs.push(`[api] ${r.status()} ${r.url()}`);
  });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();

  let dashProbe = null;
  const deadline = Date.now() + 6000;
  while (Date.now() < deadline) {
    await page.waitForTimeout(120);
    if (page.url().includes('/mitra/dashboard')) {
      dashProbe = await page.evaluate(() => {
        const tpl = Array.from(document.scripts).map((s) => s.textContent || '').find((t) => t.includes('CURRENT_MITRA_DATA = {'));
        return {
          dataText: tpl ? tpl.slice(0, 900) : null,
          hasData: !!window.CURRENT_MITRA_DATA,
          out: localStorage.getItem('sb_mitra_logged_out'),
        };
      }).catch((e) => ({ err: String(e).slice(0, 120) }));
      break;
    }
  }
  await page.waitForTimeout(3000);
  return { url: page.url(), dashProbe, logs };
}

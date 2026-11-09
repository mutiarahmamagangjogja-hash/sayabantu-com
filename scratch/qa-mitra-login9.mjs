export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`.slice(0, 300)));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  page.on('response', (r) => {
    if (r.url().includes('/api/mitra')) logs.push(`[api] ${r.status()} ${r.url()}`);
  });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();

  // freeze the dashboard document before its scripts run, so we can inspect it
  await page.route('**/mitra/dashboard', async (route) => {
    const resp = await route.fetch();
    const body = await resp.text();
    const m = body.match(/window\.CURRENT_MITRA_DATA = \{[\s\S]{0,700}?\};/);
    logs.push('[dash-html] ' + (m ? m[0].replace(/\s+/g, ' ') : 'NO DATA BLOCK'));
    await route.fulfill({ response: resp, body });
  });

  await page.waitForTimeout(6000);
  return { finalUrl: page.url(), logs };
}

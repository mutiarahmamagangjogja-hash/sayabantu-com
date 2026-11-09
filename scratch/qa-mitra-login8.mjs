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

  // wait for the dashboard document to actually commit, then read it before its JS runs
  await page.waitForURL('**/mitra/dashboard', { timeout: 8000 }).catch(() => { });
  const html = await page.content().catch(() => '');
  const m = html.match(/window\.CURRENT_MITRA_DATA = \{[\s\S]{0,700}?\};/);
  const guard = html.match(/<script>\s*\(function\(\)\s*\{[\s\S]{0,1200}?CURRENT_MITRA_DATA[\s\S]{0,900}?<\/script>/);
  await page.waitForTimeout(3000);
  return {
    finalUrl: page.url(),
    dataBlock: m ? m[0] : null,
    guardBlock: guard ? guard[0].slice(0, 1400) : null,
    logs,
  };
}

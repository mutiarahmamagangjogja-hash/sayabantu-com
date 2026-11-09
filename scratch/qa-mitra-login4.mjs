export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  page.on('response', (r) => {
    if (r.url().includes('/api/mitra/status')) logs.push(`[api] ${r.status()} ${r.url()}`);
  });
  await page.waitForTimeout(500);

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();
  await page.waitForTimeout(3000);

  // capture state right after landing on dashboard
  const snap = await page.evaluate(() => ({
    url: location.href,
    ls: { auth: localStorage.getItem('sb_mitra_auth'), out: localStorage.getItem('sb_mitra_logged_out') },
    data: window.CURRENT_MITRA_DATA,
  }));

  await page.waitForTimeout(5000);
  return { snap, finalUrl: page.url(), logs };
}

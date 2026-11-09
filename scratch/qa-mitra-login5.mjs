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

  // poll every 100ms for the first 3s to catch the dashboard state
  const samples = [];
  for (let i = 0; i < 30; i++) {
    await page.waitForTimeout(100);
    try {
      const s = await page.evaluate(() => ({
        url: location.pathname,
        data: window.CURRENT_MITRA_DATA || null,
        out: localStorage.getItem('sb_mitra_logged_out'),
        auth: localStorage.getItem('sb_mitra_auth'),
      }));
      samples.push(s);
    } catch (e) { samples.push({ err: String(e).slice(0, 80) }); }
  }
  return { samples: samples.filter((s, i) => i === 0 || JSON.stringify(s) !== JSON.stringify(samples[i - 1])), logs };
}

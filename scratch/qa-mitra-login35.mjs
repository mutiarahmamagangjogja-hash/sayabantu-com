export default async function run(page, ui) {
  const logs = [];

  // block dashboard entirely — we only want to know what the login page does next
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();
  await page.waitForTimeout(5000);

  const ls = await page.evaluate(() => ({
    url: location.pathname,
    auth: localStorage.getItem('sb_mitra_auth'),
    out: localStorage.getItem('sb_mitra_logged_out'),
    keys: Object.keys(localStorage),
  })).catch(() => null);

  return { ls, logs };
}

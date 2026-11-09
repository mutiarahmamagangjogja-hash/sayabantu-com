export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  await page.waitForTimeout(500);

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  const btn = page.locator('form:has(#inputMitraId) button[type="submit"]').first();
  await btn.click();
  await page.waitForTimeout(5000);

  return {
    url: page.url(),
    title: await page.title(),
    ls: await page.evaluate(() => ({ auth: localStorage.getItem('sb_mitra_auth'), out: localStorage.getItem('sb_mitra_logged_out') })),
    body: (await page.evaluate(() => document.body.innerText)).slice(0, 900),
    logs,
  };
}

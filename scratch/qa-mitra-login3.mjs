export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`));
  page.on('pageerror', (e) => logs.push(`[pageerror] ${e.message}`));
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });
  await page.waitForTimeout(500);

  const info = await page.evaluate(() => ({
    hasHandler: typeof window.handleMitraForm,
    hasRedirect: typeof window.loginMitraAndRedirect,
    hasSetTab: typeof window.setLoginTab,
    mitraBoxHidden: document.getElementById('contentMitraBox')?.className,
  }));

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();
  await page.waitForTimeout(6000);

  return {
    info,
    url: page.url(),
    alert: await page.evaluate(() => {
      const el = document.getElementById('mitraLoginAlert');
      return { hidden: el?.classList.contains('hidden'), text: el?.innerText };
    }),
    logs,
  };
}

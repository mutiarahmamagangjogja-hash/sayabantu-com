export default async function run(page, ui) {
  const logs = [];
  page.on('framenavigated', (f) => { if (f === page.mainFrame()) logs.push(`[nav] ${f.url()}`); });

  await page.locator('#inputMitraId').fill('SB-8239');
  await page.locator('#inputMitraPass').fill('123456');
  await page.locator('#btnSubmitMitra').click();

  let dash = '';
  await page.route('**/mitra/dashboard', async (route) => {
    const resp = await route.fetch();
    dash = await resp.text();
    await route.fulfill({ response: resp, body: dash });
  });
  await page.waitForTimeout(4000);

  // strip the inline scripts, keep the DOM, then drive the SPA code manually
  const scripts = [...dash.matchAll(/<script[^>]*>([\s\S]*?)<\/script>/gi)].map((m) => m[1]);
  const spa = scripts.find((s) => s.includes('mitraDomCache')) || '';
  const calls = [...spa.matchAll(/(?:window\.)?location\.(?:href|replace)\s*=\s*[^;\n]{0,80}|location\.replace\([^)]{0,80}\)/g)].map((m) => m[0]);

  return { url: page.url(), spaHasLoginLink: /mitra\/login/.test(spa), spaCalls: calls.slice(0, 30), logs };
}

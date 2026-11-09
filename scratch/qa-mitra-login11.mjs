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

  // capture the dashboard document and neutralize its scripts so it cannot bounce us
  let dashHtml = '';
  await page.route('**/mitra/dashboard', async (route) => {
    const resp = await route.fetch();
    dashHtml = await resp.text();
    await route.fulfill({ response: resp, body: dashHtml.replace(/<script[\s\S]*?<\/script>/gi, '<!-- script removed -->') });
  });

  await page.waitForTimeout(5000);

  const scripts = [...dashHtml.matchAll(/<script[^>]*>([\s\S]*?)<\/script>/gi)].map((m) => m[1]);
  const offenders = scripts
    .map((s, i) => ({ i, s }))
    .filter(({ s }) => /mitra\/login|mitra\.login|location\.(replace|href|assign)/.test(s))
    .map(({ i, s }) => ({ i, snippet: s.replace(/\s+/g, ' ').slice(0, 500) }));

  return { finalUrl: page.url(), scriptCount: scripts.length, offenders, logs };
}

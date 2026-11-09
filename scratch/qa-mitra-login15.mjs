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

  const scripts = [...dash.matchAll(/<script[^>]*>([\s\S]*?)<\/script>/gi)].map((m) => m[1]);
  const hits = scripts.map((s, i) => ({ i, hits: [...s.matchAll(/.{100}location\.(?:replace|assign|href).{0,140}/g)].map((m) => m[0].replace(/\s+/g, ' ')) }))
    .filter((x) => x.hits.length);
  return { url: page.url(), hits, logs };
}

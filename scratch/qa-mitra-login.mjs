export default async function run(page, ui) {
  const logs = [];
  page.on('console', (m) => logs.push(`[${m.type()}] ${m.text()}`));

  await page.waitForTimeout(800);
  const form = await ui.snapshot();
  return { url: page.url(), title: await page.title(), form, logs };
}

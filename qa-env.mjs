export default async function run(page, ui) {
  const results = [];
  const tryScope = async (label, setup) => {
    await page.reload({ waitUntil: 'domcontentloaded' }).catch(() => { });
    const r = await page.evaluate(setup).catch((e) => ({ error: String(e) }));
    results.push({ label, r });
  };
  // Main world
  await tryScope('mainWorld', () => {
    const s = Array.from(document.scripts).filter((x) => x.hasAttribute('data-layout-script'))[0];
    try { eval(s.textContent); } catch (e) { return 'ERR ' + e.message; }
    return typeof window.adminSpaNavigateTo;
  });
  // Isolated world via CDP not available in script helper reliably; instead test with a fresh worker
  const worker = await page.evaluate(async () => {
    const s = Array.from(document.scripts).filter((x) => x.hasAttribute('data-layout-script'))[0];
    const src = 'var window = self;' + s.textContent;
    const url = URL.createObjectURL(new Blob([src], { type: 'text/javascript' }));
    const w = new Worker(url, { type: 'classic' });
    const res = await new Promise((resolve) => {
      w.onerror = (e) => resolve('worker ERR ' + e.message + ' @' + e.lineno);
      w.onmessage = (ev) => resolve('worker ok ' + ev.data);
      setTimeout(() => { try { w.postMessage(0); } catch (e) { } resolve('worker timeout'); }, 1500);
      w.postMessage('probe');
    });
    return res;
  });
  return { results, worker };
}

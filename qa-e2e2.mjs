export default async function run(page, ui) {
  await page.waitForFunction(() => typeof window.adminSpaNavigateTo === 'function', null, { timeout: 30000 });

  const out = [];
  const targets = [
    ['/admin/layanan', 'layanan'],
    ['/admin/voucher', 'voucher'],
    ['/admin/pengaduan', 'pengaduan'],
    ['/admin/pengaturan', 'pengaturan'],
    ['/admin/dashboard', 'dashboard'],
  ];

  await page.evaluate(() => {
    window.__docMarker = 'alive';
    window.addEventListener('beforeunload', () => { window.__docMarker = 'unloading'; });
  });

  for (const [path, label] of targets) {
    const t0 = Date.now();
    await page.evaluate((p) => window.adminSpaNavigateTo('http://127.0.0.1:8000' + p), path);
    await page.waitForFunction((p) => location.pathname === p, path, { timeout: 15000 }).catch(() => { });
    await page.waitForTimeout(300);
    const state = await page.evaluate(() => ({
      path: location.pathname,
      marker: window.__docMarker,
      heading: (document.querySelector('#sbAdminContent h2') || {}).textContent?.replace(/\s+/g, ' ').trim() || null,
      headerAlive: !!document.querySelector('.mobile-app .admin-top-area header h1'),
      hash: document.getElementById('sbAdminContent').innerHTML.length,
    }));
    out.push({ label, ms: Date.now() - t0, ...state });
  }

  return out;
}

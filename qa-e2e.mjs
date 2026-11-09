const wait = (page, selector, timeout = 10000) => page.waitForSelector(selector, { state: 'attached', timeout });

export default async function run(page, ui) {
  await wait(page, '#sbBurgerPanel a[data-nav="layanan"]');

  const results = [];
  const items = [
    ['layanan', '/admin/layanan'],
    ['voucher', '/admin/voucher'],
    ['konfirmasi', '/admin/konfirmasi-pekerjaan'],
    ['pengaduan', '/admin/pengaduan'],
    ['chat', '/admin/monitoring-chat'],
    ['moderasi', '/admin/moderasi-kepatuhan'],
    ['laporan', '/admin/laporan-analitik'],
    ['pengaturan', '/admin/pengaturan'],
  ];

  for (const [nav, expectedPath] of items) {
    await page.waitForSelector('button[aria-label="Menu Navigasi"]');
    await page.click('button[aria-label="Menu Navigasi"]');
    await page.waitForSelector(`#sbBurgerPanel a[data-nav="${nav}"]`, { state: 'visible', timeout: 5000 });

    const t0 = Date.now();
    const navEvent = page.waitForNavigation({ timeout: 2000 }).then(() => true).catch(() => false);
    await page.evaluate((n) => document.querySelector(`#sbBurgerPanel a[data-nav="${n}"]`).click(), nav);
    const fullReload = await navEvent;

    // wait for the SPA content swap
    await page.waitForFunction((p) => location.pathname === p, expectedPath, { timeout: 8000 }).catch(() => { });
    await page.waitForTimeout(150);

    const state = await page.evaluate(() => ({
      path: location.pathname,
      title: document.title.replace(' - SayaBantu.com', ''),
      heading: (document.querySelector('#sbAdminContent h2') || {}).textContent?.replace(/\s+/g, ' ').trim() || null,
      headerAlive: !!document.querySelector('.mobile-app .admin-top-area header h1'),
      drawerHidden: getComputedStyle(document.getElementById('sbBurgerModal')).visibility,
      bodyUnlocked: !document.body.classList.contains('sb-scroll-locked'),
    }));
    results.push({ nav, expectedPath, ms: Date.now() - t0, fullReload, ...state });
  }

  // Bottom nav back to dashboard
  const t0 = Date.now();
  const navEvent = page.waitForNavigation({ timeout: 2000 }).then(() => true).catch(() => false);
  await page.evaluate(() => document.querySelector('.bottom-navigation a[title="Dashboard Admin"]').click());
  const fullReload = await navEvent;
  await page.waitForFunction(() => location.pathname === '/admin/dashboard', null, { timeout: 8000 }).catch(() => { });
  await page.waitForTimeout(150);
  const dash = await page.evaluate(() => ({ path: location.pathname, active: Array.from(document.querySelectorAll('.bottom-nav-item.nav-active')).map((e) => e.textContent.trim()).join(',') }));

  // Header panels
  await page.click('#sbNotifHeaderBtn');
  await page.waitForTimeout(300);
  const notif = await page.evaluate(() => getComputedStyle(document.getElementById('sbNotificationModal')).visibility);
  await page.keyboard.press('Escape');
  await page.waitForTimeout(250);
  await page.click('#sbAvatarHeaderBtn');
  await page.waitForTimeout(300);
  const avatar = await page.evaluate(() => getComputedStyle(document.getElementById('sbProfileQuickModal')).visibility);
  await page.keyboard.press('Escape');
  await page.waitForTimeout(250);

  // Back
  await page.goBack();
  await page.waitForTimeout(700);
  const back = await page.evaluate(() => ({ path: location.pathname }));

  await page.screenshot({ path: 'qa-final.png' });

  return { results, dash: { ...dash, ms: Date.now() - t0, fullReload }, notif, avatar, back };
}

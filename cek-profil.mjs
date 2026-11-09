# cek - profil.mjs
export default async function run(page, ui) {
  const base = 'http://127.0.0.1:8000';

  await page.goto(base + '/customer', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1200);

  // Login cepat sebagai customer (kalau belum login, halaman redirect ke /login)
  if (page.url().includes('/login')) {
    const snap = await ui.snapshot();
    const refLogin = snap.match(/@(e\d+) button "[^"]*Masuk sebagai Customer[^"]*"/)?.[1];
    if (refLogin) {
      await ui.click(refLogin);
    } else {
      await page.evaluate(() => {
        if (typeof loginCustomerAndRedirect === 'function') {
          loginCustomerAndRedirect('Budi Santoso', '0812-3456-7890');
        }
      });
    }
    await page.waitForTimeout(2000);
  }

  // Paksa ke tab profile
  await page.evaluate(() => {
    if (typeof switchCustomerTab === 'function') switchCustomerTab('profile');
  });
  await page.waitForTimeout(600);

  const info = await page.evaluate(() => {
    const root = document.getElementById('viewCustProfile');
    if (!root) return { ada: false };
    const headings = [...root.querySelectorAll('h3')].map(h => h.textContent.trim());
    return {
      ada: true,
      tampil: !root.classList.contains('hidden'),
      jumlahSection: headings.length,
      headings,
      adaTombolEdit: !!root.querySelector('button[onclick*="Edit Profil"]'),
      adaTambahAlamat: !!root.querySelector('button[onclick*="Tambah Alamat"]'),
      adaToggle: root.querySelectorAll('input[type=checkbox]').length,
      adaKeluar: !!root.querySelector('button[onclick="logoutUserAction()"]'),
      keluarText: root.querySelector('button[onclick="logoutUserAction()"]')?.textContent.trim(),
      nama: document.getElementById('profileFieldName')?.textContent.trim(),
      email: document.getElementById('profileFieldEmail')?.textContent.trim(),
      telepon: document.getElementById('profileFieldPhone')?.textContent.trim(),
    };
  });

  return info;
}

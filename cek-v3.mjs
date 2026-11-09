import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

// 1. FORM PESANAN: pastikan teks "Daftar / Masuk gratis" hilang
await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(2000)
const form = await page.evaluate(() => {
  const t = document.body.innerText
  return {
    adaDaftarMasukGratis: /Daftar \/ Masuk gratis/i.test(t),
    adaBelumPunyaAkun: /Belum punya akun/i.test(t),
  }
})
console.log('FORM PESANAN:', JSON.stringify(form))

// 2. MODAL LOGIN: cek opsi daftar + google
await page.click('#headerLoginBtn')
await page.waitForTimeout(700)
const login = await page.evaluate(() => {
  const m = document.getElementById('modalLoginCustomer')
  const t = m.innerText
  return {
    adaMasukGoogle: /Masuk dengan Google/i.test(t),
    adaBelumPunyaAkun: /Belum punya akun/i.test(t),
    adaTombolDaftar: /Daftar/.test(t),
    adaHalamanLoginLengkap: /Halaman Login Lengkap/i.test(t),
    adaAdmin: /admin/i.test(t),
    adaTabMitra: !!document.getElementById('tabBtnMitra'),
  }
})
console.log('\nMODAL MASUK:', JSON.stringify(login, null, 2))
await page.screenshot({ path: 'v3-login.png' })

// 3. klik Daftar -> buka modal daftar
await page.click('button[onclick*="modalDaftarCustomer"]')
await page.waitForTimeout(700)
const daftar = await page.evaluate(() => {
  const m = document.getElementById('modalDaftarCustomer')
  const tampil = m && !m.classList.contains('hidden')
  const t = m ? m.innerText : ''
  return {
    modalTerbuka: tampil,
    adaDaftarGoogle: /Daftar dengan Google/i.test(t),
    adaFormNama: !!document.getElementById('inputDaftarNama'),
    adaSudahPunyaAkun: /Sudah punya akun/i.test(t),
  }
})
console.log('\nMODAL DAFTAR:', JSON.stringify(daftar, null, 2))
await page.screenshot({ path: 'v3-daftar.png' })

// 4. NAV BAWAH di 4 halaman info: tidak ada yang berwarna
const infoPages = [['Tentang', '/tentang'], ['CaraKerja', '/cara-kerja'], ['Syarat', '/syarat-ketentuan'], ['Privasi', '/kebijakan-privasi']]
for (const [nama, u] of infoPages) {
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(1600)
  const nav = await page.evaluate(() => {
    const aktif = document.querySelector('.bottom-nav-item.nav-active')
    return { navBawahAktif: aktif ? aktif.innerText.trim() : 'TIDAK ADA' }
  })
  console.log(nav.navBawahAktif === 'TIDAK ADA' ? 'OK  ' + nama.padEnd(10) : 'GAGAL ' + nama, JSON.stringify(nav))
}

// 5. BURGER MENU: cek warna pada menu aktif
for (const [nama, u] of infoPages) {
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(1400)
  await page.click('.header-icon[onclick="openCustomerDrawer()"]')
  await page.waitForTimeout(500)
  const drawer = await page.evaluate(() => {
    const p = document.getElementById('customerBurgerPanel')
    const links = [...p.querySelectorAll('a')]
    const aktif = links.find(a => a.className.includes('bg-sky-500/20'))
    return { menuAktifDiDrawer: aktif ? aktif.innerText.trim() : 'TIDAK ADA' }
  })
  console.log('DRAWER ' + nama.padEnd(10), JSON.stringify(drawer))
  if (nama === 'CaraKerja') await page.screenshot({ path: 'v3-drawer.png' })
}

await browser.close()

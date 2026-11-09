import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const urls = [
  ['Dashboard', '/customer'],
  ['Layanan', '/layanan'],
  ['Mitra', '/customer/mitra'],
  ['Tentang', '/tentang'],
  ['CaraKerja', '/cara-kerja'],
  ['Syarat', '/syarat-ketentuan'],
  ['Privasi', '/kebijakan-privasi'],
]

for (const [nama, u] of urls) {
  const errs = []
  page.on('pageerror', e => errs.push(e.message))
  const r = await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded', timeout: 30000 })
  await page.waitForTimeout(1600)

  const d = await page.evaluate(() => {
    const navAktif = document.querySelector('.bottom-nav-item.nav-active')
    const bodyText = document.body.innerText
    return {
      navAktif: navAktif ? navAktif.innerText.trim() : 'TIDAK ADA',
      adaTulisanAdmin: /admin/i.test(bodyText),
      adaMasukCustomer: /Masuk Customer/i.test(bodyText),
      adaTidakMenemukan: /Tidak Menemukan yang Anda Cari/i.test(bodyText),
      adaLoginAdmin: /Login Admin/i.test(bodyText),
    }
  })
  console.log(nama.padEnd(10), 'HTTP ' + r.status(), JSON.stringify(d), errs.length ? 'ERR: ' + errs[0] : 'OK')
}

// uji burger menu
await page.goto('http://127.0.0.1:8123/tentang', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1200)
await page.click('.header-icon[onclick="openCustomerDrawer()"]')
await page.waitForTimeout(600)
const drawer = await page.evaluate(() => {
  const p = document.getElementById('customerBurgerPanel')
  return {
    teksPanel: p.innerText.replace(/\n+/g, ' | ').trim(),
  }
})
console.log('\n=== ISI DRAWER ===')
console.log(drawer.teksPanel)
await page.screenshot({ path: 'fix-drawer.png' })

// uji modal login
await page.goto('http://127.0.0.1:8123/tentang', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1200)
await page.click('#mobileHeaderLoginBtn')
await page.waitForTimeout(700)
const loginModal = await page.evaluate(() => {
  const m = document.getElementById('modalLoginCustomer')
  return { teks: m.innerText.replace(/\n+/g, ' | ').trim() }
})
console.log('\n=== ISI MODAL MASUK ===')
console.log(loginModal.teks)
await page.screenshot({ path: 'fix-login.png' })

// buka modal daftar
await page.click('button[onclick*="modalDaftarCustomer"]')
await page.waitForTimeout(700)
const daftarModal = await page.evaluate(() => {
  const m = document.getElementById('modalDaftarCustomer')
  return { teks: m.innerText.replace(/\n+/g, ' | ').trim() }
})
console.log('\n=== ISI MODAL DAFTAR ===')
console.log(daftarModal.teks)
await page.screenshot({ path: 'fix-daftar.png' })

await browser.close()

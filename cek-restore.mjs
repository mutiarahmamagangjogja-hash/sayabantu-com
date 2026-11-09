import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

// 1. cek halaman login lengkap masih ada & tanpa admin
const r = await page.goto('http://127.0.0.1:8123/login', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1500)
const loginPage = await page.evaluate(() => {
  const t = document.body.innerText
  return {
    judul: document.title,
    adaTabCustomer: t.includes('Customer'),
    adaTabMitra: t.includes('Mitra'),
    adaAdmin: /admin/i.test(t),
    panjangKonten: t.length,
  }
})
console.log('LOGIN LENGKAP:', r.status(), JSON.stringify(loginPage))
await page.screenshot({ path: 'restore-login-full.png' })

// 2. cek modal login kembali seperti awal
await page.goto('http://127.0.0.1:8123/tentang', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1200)
await page.click('#mobileHeaderLoginBtn')
await page.waitForTimeout(700)
const modal = await page.evaluate(() => {
  const m = document.getElementById('modalLoginCustomer')
  return {
    teks: m.innerText.replace(/\n+/g, ' | ').trim(),
    adaTabCustomer: !!document.getElementById('tabBtnCustomer'),
    adaTabMitra: !!document.getElementById('tabBtnMitra'),
    adaBlokMitra: !!document.getElementById('roleContentMitra'),
    adaGoogle: /Google/i.test(m.innerText),
    adaAdmin: /admin/i.test(m.innerText),
  }
})
console.log('\nMODAL LOGIN:', JSON.stringify(modal, null, 2))
await page.screenshot({ path: 'restore-modal-login.png' })

// 3. cek card mitra terakhir hilang
await page.goto('http://127.0.0.1:8123/customer/mitra', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1500)
const mitra = await page.evaluate(() => {
  const t = document.body.innerText
  return { adaKotaBelumAda: /Kota Anda Belum Ada/i.test(t) }
})
console.log('\nMITRA:', JSON.stringify(mitra))

await browser.close()

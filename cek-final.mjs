import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const urls = [
  ['Beranda', '/'],
  ['Layanan', '/layanan'],
  ['Mitra', '/customer/mitra'],
  ['Detail', '/customer/layanan'],
]

for (const [nama, u] of urls) {
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(2000)
  const d = await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const t = top.getBoundingClientRect()
    return {
      tinggiGradient: Math.round(t.height),
      batasBawahGradient: Math.round(t.bottom),
    }
  })
  console.log(nama.padEnd(9), JSON.stringify(d))
  await page.screenshot({ path: `shot-${nama.toLowerCase()}.png` })
}

// uji nav aktif + drawer terbuka di beranda
await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1500)
await page.click('.header-icon[onclick="openCustomerDrawer()"]')
await page.waitForTimeout(700)
await page.screenshot({ path: 'shot-drawer-beranda.png' })
const nav = await page.evaluate(() => {
  const aktif = document.querySelector('.bottom-nav-item.nav-active')
  return aktif ? aktif.innerText.trim() : 'TIDAK ADA'
})
console.log('nav aktif di beranda:', nav)
await browser.close()

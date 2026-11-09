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
  await page.waitForTimeout(1800)

  const d = await page.evaluate(() => {
    const navAktif = document.querySelector('.bottom-nav-item.nav-active')
    const notif = document.getElementById('headerNotifBtn') || document.getElementById('mobileHeaderNotifBtn')
    const navItems = [...document.querySelectorAll('.bottom-nav-item span')].map(s => s.innerText)
    const h1 = document.querySelector('.customer-top-area h1')
    return {
      status: document.title.slice(0, 30),
      navItems,
      navAktif: navAktif ? navAktif.innerText.trim() : 'TIDAK ADA',
      notifAdaDiHeader: !!notif,
      judul: h1 ? h1.innerText.replace(/\s+/g, ' ') : null,
      kontenAda: (document.querySelector('.content-under-gradient') || {}).innerText ? 'ya' : 'kosong',
    }
  })
  console.log('\n=== ' + nama + ' === HTTP ' + r.status())
  console.log(JSON.stringify(d))
  console.log('errors:', errs.length ? errs.slice(0, 3) : 'OK')
}

// uji isi burger menu
await page.goto('http://127.0.0.1:8123/tentang', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1200)
await page.click('.header-icon[onclick="openCustomerDrawer()"]')
await page.waitForTimeout(600)
const drawer = await page.evaluate(() => {
  const panel = document.getElementById('customerBurgerPanel')
  const links = [...panel.querySelectorAll('a')].map(a => a.innerText.trim())
  return { menuDrawer: links }
})
console.log('\n=== ISI BURGER MENU ===')
console.log(JSON.stringify(drawer, null, 2))
await page.screenshot({ path: 'rev-drawer.png' })

await browser.close()

import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const errors = []
page.on('console', m => { if (m.type() === 'error') errors.push(m.text()) })
page.on('pageerror', e => errors.push('PAGEERROR: ' + e.message))

const halaman = [
  ['Beranda', 'http://127.0.0.1:8123/'],
  ['Layanan', 'http://127.0.0.1:8123/layanan'],
  ['Mitra', 'http://127.0.0.1:8123/customer/mitra'],
  ['Detail', 'http://127.0.0.1:8123/customer/layanan'],
]

for (const [nama, url] of halaman) {
  const r = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 60000 })
  await page.waitForTimeout(1800)

  const info = await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const content = document.querySelector('.content-under-gradient')
    const notif = document.getElementById('headerNotifBtn') || document.getElementById('mobileHeaderNotifBtn')
    const burger = document.querySelector('.header-icon[onclick="openCustomerDrawer()"]')
    const drawer = document.getElementById('customerBurgerModal')
    const navAktif = document.querySelector('.bottom-nav-item.nav-active')
    const firstCard = document.querySelector('.content-under-gradient .card-sb, .content-under-gradient section')
    const rect = el => el ? el.getBoundingClientRect() : null
    const t = rect(top), c = rect(firstCard)
    return {
      adaTopArea: !!top,
      topAreaBottom: t ? Math.round(t.bottom) : null,
      cardPertamaTop: c ? Math.round(c.top) : null,
      cardPertamaBottom: c ? Math.round(c.bottom) : null,
      gradientMelewatiCardPertama: (t && c) ? Math.round(t.bottom - c.bottom) : null,
      notifTampil: notif ? !notif.classList.contains('hidden') : null,
      burgerAda: !!burger,
      drawerAda: !!drawer,
      navAktif: navAktif ? navAktif.querySelector('span')?.innerText : null,
      contentTertarik: content ? getComputedStyle(content).marginTop : null,
    }
  })
  console.log(nama.padEnd(9), r.status(), JSON.stringify(info))
}

// uji drawer di halaman mitra
await page.goto('http://127.0.0.1:8123/customer/mitra', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(1200)
await page.click('.header-icon[onclick="openCustomerDrawer()"]')
await page.waitForTimeout(600)
const drawerTerbuka = await page.evaluate(() => {
  const m = document.getElementById('customerBurgerModal')
  const p = document.getElementById('customerBurgerPanel')
  return {
    modalTerlihat: !m.classList.contains('invisible'),
    panelTergeser: !p.classList.contains('-translate-x-full'),
    jumlahMenu: document.querySelectorAll('#customerBurgerPanel a, #customerBurgerPanel button').length,
  }
})
console.log('drawer mitra:', JSON.stringify(drawerTerbuka))
await page.screenshot({ path: 'cek-drawer.png' })

console.log('\nconsole errors:', errors.length ? errors.slice(0, 6) : 'tidak ada')
await browser.close()

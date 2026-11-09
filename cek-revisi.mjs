import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const urls = [
  ['Dashboard', '/customer'],
  ['Layanan', '/layanan'],
  ['Mitra', '/customer/mitra'],
]

for (const [nama, u] of urls) {
  const errs = []
  page.on('pageerror', e => errs.push(e.message))
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(2200)

  const d = await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const brandH1 = document.querySelector('header h1')
    const dotCom = brandH1 ? brandH1.querySelector('span') : null
    const searchBtn = document.querySelector('.header-icon[onclick="toggleHeaderSearch()"]') ||
      document.querySelector('button[onclick="toggleHeaderSearch()"]')
    const notifBtn = document.getElementById('headerNotifBtn') || document.getElementById('mobileHeaderNotifBtn')
    const navAktif = document.querySelector('.bottom-nav-item.nav-active')
    const t = top ? top.getBoundingClientRect() : null

    const urutan = [...document.querySelectorAll('header button, header a')]
      .map(b => (b.getAttribute('onclick') || b.getAttribute('href') || '').slice(0, 30))
      .filter(Boolean)

    return {
      tinggiGradient: t ? Math.round(t.height) : null,
      brandText: brandH1 ? brandH1.innerText : null,
      dotComWarna: dotCom ? getComputedStyle(dotCom).color : null,
      brandWarna: brandH1 ? getComputedStyle(brandH1).color : null,
      searchAda: !!searchBtn,
      notifTampil: notifBtn ? getComputedStyle(notifBtn).display !== 'none' : null,
      navAktif: navAktif ? navAktif.innerText.trim() : 'TIDAK ADA',
      navBgAktif: navAktif ? getComputedStyle(navAktif).backgroundColor : null,
      urutanTombol: urutan.slice(0, 5),
    }
  })
  console.log('\n=== ' + nama + ' ===')
  console.log(JSON.stringify(d, null, 2))
  console.log('errors:', errs.length ? errs.slice(0, 3) : 'OK')
  await page.screenshot({ path: `final-${nama.toLowerCase()}.png` })
}
await browser.close()

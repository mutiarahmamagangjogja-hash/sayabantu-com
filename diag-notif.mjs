import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })
await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(2000)

const d = await page.evaluate(() => {
  const n = document.getElementById('headerNotifBtn')
  const headerBtns = [...document.querySelectorAll('header .header-icon')].map(b => ({
    onclick: b.getAttribute('onclick'),
    cls: b.className,
    display: getComputedStyle(b).display,
  }))
  return {
    notifAda: !!n,
    notifClass: n ? n.className : null,
    notifDisplay: n ? getComputedStyle(n).display : null,
    notifVisible: n ? getComputedStyle(n).visibility : null,
    semuaBtnHeader: headerBtns,
  }
})
console.log(JSON.stringify(d, null, 2))
await browser.close()

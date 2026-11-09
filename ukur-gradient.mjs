import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })
await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(2000)

const d = await page.evaluate(() => {
  const top = document.querySelector('.customer-top-area')
  const content = document.querySelector('.content-under-gradient')
  const cards = [...content.querySelectorAll('section, .card-sb')]
  const firstCard = cards.find(c => c.getBoundingClientRect().height > 20)
  const t = top.getBoundingClientRect()
  const f = firstCard ? firstCard.getBoundingClientRect() : null
  const cs = getComputedStyle(top)
  const ccs = getComputedStyle(content)
  return {
    paddingBottomTopArea: cs.paddingBottom,
    marginTopContent: ccs.marginTop,
    batasGradientY: Math.round(t.bottom),
    cardPertama: f ? { top: Math.round(f.top), bottom: Math.round(f.bottom), tinggi: Math.round(f.height) } : null,
    gradientMasukKeCard: f ? Math.round(t.bottom - f.top) : null,
    teksCardPertama: firstCard ? firstCard.innerText.split('\n')[0].slice(0, 40) : null,
  }
})
console.log(JSON.stringify(d, null, 2))
await browser.close()

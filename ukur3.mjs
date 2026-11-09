import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })
await page.goto('http://127.0.0.1:8123/layanan', { waitUntil: 'domcontentloaded', timeout: 60000 })
await page.waitForTimeout(2500)

const hasil = await page.evaluate(() => {
  const top = document.querySelector('.customer-top-area')
  const topContent = document.getElementById('pageTopContent')
  const katalog = document.getElementById('layananKatalogSection')
  const marker = document.getElementById('gradientEndMarker')
  const t = top.getBoundingClientRect()
  const tc = topContent.getBoundingClientRect()
  const k = katalog.getBoundingClientRect()
  const m = marker.getBoundingClientRect()
  return {
    topAreaTop: t.top,
    topAreaHeight: t.height,
    topAreaBottom: t.bottom,
    topContentTop: tc.top,
    topContentBottom: tc.bottom,
    katalogTop: k.top,
    katalogHeight: k.height,
    katalogBottom: k.bottom,
    markerTop: m.top,
    gradientBatas: t.bottom,
    // positif = gradient masih melewati dasar kartu; negatif = berhenti sebelum dasar kartu
    kelebihanTurun: t.bottom - k.bottom,
    selisihMarkerVsKatalogBottom: m.top - k.bottom,
  }
})
console.log(JSON.stringify(hasil, null, 2))
await page.screenshot({ path: 'cek-gradient.png' })
await browser.close()

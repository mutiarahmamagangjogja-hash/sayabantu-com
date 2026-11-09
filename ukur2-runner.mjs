import { chromium } from 'playwright'

export default async function run(page) {
  await page.setViewportSize({ width: 430, height: 900 })
  await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded', timeout: 60000 })
  await page.waitForTimeout(2500)

  return await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const katalog = document.getElementById('layananKatalogSection')
    const form = document.getElementById('orderFormCard')
    const t = top.getBoundingClientRect()
    const k = katalog.getBoundingClientRect()
    const f = form.getBoundingClientRect()

    return {
      topAreaHeight: t.height,
      topAreaBottom: t.bottom,
      katalogTop: k.top,
      katalogTengah: k.top + k.height / 2,
      katalogBottom: k.bottom,
      formTop: f.top,
      // Target: gradient berhenti di dasar kartu katalog (satu card penuh)
      targetBatasGradient: k.bottom,
      // Berapa px gradient melewati / kurang dari target
      kelebihanTurun: t.bottom - k.bottom,
    }
  })
}

if (import.meta.url === `file://${process.argv[1].replace(/\\/g, '/')}`) {
  const browser = await chromium.launch()
  const page = await browser.newPage()
  const hasil = await run(page)
  console.log(JSON.stringify(hasil, null, 2))
  await page.screenshot({ path: 'cek-gradient.png', fullPage: false })
  await browser.close()
}

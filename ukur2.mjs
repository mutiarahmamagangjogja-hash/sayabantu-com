export default async function run(page, ui) {
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
      // Target: gradient berhenti di tengah kartu katalog
      targetBatasGradient: k.top + k.height / 2,
      // Berapa px gradient sekarang melewati target
      kelebihanTurun: t.bottom - (k.top + k.height / 2),
    }
  })
}

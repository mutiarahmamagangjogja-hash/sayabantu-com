export default async function run(page, ui) {
  await page.setViewportSize({ width: 430, height: 900 })
  await page.goto('http://127.0.0.1:8123/', { waitUntil: 'domcontentloaded', timeout: 60000 })
  await page.waitForTimeout(2500)

  return await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const katalog = document.getElementById('layananKatalogSection')
    const topRect = top.getBoundingClientRect()
    const katRect = katalog.getBoundingClientRect()

    return {
      topArea: { top: topRect.top, bottom: topRect.bottom, height: topRect.height },
      katalogSection: { top: katRect.top, bottom: katRect.bottom, height: katRect.height },
      // Seberapa jauh gradient harus turun supaya pas di tengah kartu katalog
      jarakTopAreaKeTengahKatalog: (katRect.top + katRect.height / 2) - topRect.top,
      // Berapa px katalog berada di luar (di bawah) top-area saat ini
      katalogMulaiDiBawahTopArea: katRect.top - topRect.bottom,
      // Tinggi section layanan berikutnya (grid 8 item)
      gridHeight: katalog.querySelector('.grid')?.getBoundingClientRect().height,
    }
  })
}

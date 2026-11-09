import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const urls = [
  ['Dashboard', '/customer'],
  ['Layanan', '/layanan'],
  ['Mitra', '/customer/mitra'],
  ['Detail', '/customer/layanan'],
]

for (const [nama, u] of urls) {
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(2200)

  const d = await page.evaluate(() => {
    const top = document.querySelector('.customer-top-area')
    const content = document.querySelector('.content-under-gradient')
    const t = top.getBoundingClientRect()
    const cs = getComputedStyle(top)
    const ccs = content ? getComputedStyle(content) : null

    // elemen terakhir di dalam header (konten headernya)
    const headerKids = [...top.children].filter(c => !c.className.includes('header-circle'))
    const lastHeader = headerKids[headerKids.length - 1]
    const lh = lastHeader ? lastHeader.getBoundingClientRect() : null

    // kartu pertama setelah header
    const firstCard = content ? content.querySelector('section, .card-sb, div') : null
    const fc = firstCard ? firstCard.getBoundingClientRect() : null

    return {
      tinggiTopArea: Math.round(t.height),
      paddingBottom: cs.paddingBottom,
      marginTopContent: ccs ? ccs.marginTop : null,
      kontenHeaderBerakhir: lh ? Math.round(lh.bottom) : null,
      kartuPertamaMulai: fc ? Math.round(fc.top) : null,
      // ini jarak kosong yang terlihat antara akhir konten header dan kartu pertama
      ruangKosongTerlihat: (lh && fc) ? Math.round(fc.top - lh.bottom) : null,
      batasGradient: Math.round(t.bottom),
      // berapa px gradient turun ke bawah setelah konten header habis
      ekorGradientSetelahHeader: lh ? Math.round(t.bottom - lh.bottom) : null,
    }
  })
  console.log(nama.padEnd(10), JSON.stringify(d))
}
await browser.close()

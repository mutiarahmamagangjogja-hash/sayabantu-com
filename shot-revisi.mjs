import { chromium } from 'playwright'

const browser = await chromium.launch()
const page = await browser.newPage()
await page.setViewportSize({ width: 430, height: 900 })

const shots = [
  ['tentang', '/tentang'],
  ['carakerja', '/cara-kerja'],
  ['syarat', '/syarat-ketentuan'],
  ['privasi', '/kebijakan-privasi'],
]

for (const [nama, u] of shots) {
  await page.goto('http://127.0.0.1:8123' + u, { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(2000)
  await page.screenshot({ path: `rev-${nama}.png` })
}

// header dashboard (cek notifikasi hilang)
await page.goto('http://127.0.0.1:8123/customer', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(2000)
await page.screenshot({ path: 'rev-header.png', clip: { x: 0, y: 0, width: 430, height: 260 } })

await browser.close()

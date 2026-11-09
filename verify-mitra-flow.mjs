import { chromium } from 'playwright';

async function run() {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 430, height: 932 },
        userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1'
    });
    const page = await context.newPage();

    console.log('1. Testing Home / Visitor page...');
    await page.goto('http://127.0.0.1:8000/', { waitUntil: 'networkidle' });
    
    // Check if mobileHeaderDaftarBtn is visible
    const daftarBtn = await page.$('#mobileHeaderDaftarBtn');
    console.log('Daftar button in header exists:', !!daftarBtn);
    if (daftarBtn) {
        await daftarBtn.click();
        await page.waitForTimeout(300);
        await page.screenshot({ path: 'verify-modal-daftar-customer.png' });
        console.log('Saved verify-modal-daftar-customer.png');

        // Click Mitra tab in registration modal
        const tabMitra = await page.$('#tabBtnDaftarMitra');
        if (tabMitra) {
            await tabMitra.click();
            await page.waitForTimeout(300);
            await page.screenshot({ path: 'verify-modal-daftar-mitra-tab.png' });
            console.log('Saved verify-modal-daftar-mitra-tab.png');
        }
    }

    console.log('2. Testing Mitra Registration Page /mitra/daftar...');
    await page.goto('http://127.0.0.1:8000/mitra/daftar', { waitUntil: 'networkidle' });
    await page.screenshot({ path: 'verify-page-mitra-daftar.png' });
    console.log('Saved verify-page-mitra-daftar.png');

    console.log('3. Testing Mitra Portal Dashboard /mitra...');
    await page.goto('http://127.0.0.1:8000/mitra', { waitUntil: 'networkidle' });
    await page.screenshot({ path: 'verify-mitra-dashboard.png' });
    console.log('Saved verify-mitra-dashboard.png');

    console.log('4. Testing Mitra Tab Kebutuhan...');
    await page.click('#mitraNavKebutuhan');
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'verify-mitra-kebutuhan.png' });
    console.log('Saved verify-mitra-kebutuhan.png');

    console.log('5. Testing Mitra Tab Pekerjaan...');
    await page.click('#mitraNavPekerjaan');
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'verify-mitra-pekerjaan.png' });
    console.log('Saved verify-mitra-pekerjaan.png');

    console.log('6. Testing Mitra Tab Pesan...');
    await page.click('#mitraNavPesan');
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'verify-mitra-pesan.png' });
    console.log('Saved verify-mitra-pesan.png');

    await browser.close();
    console.log('All tests completed successfully!');
}

run().catch(err => {
    console.error('Error running test:', err);
    process.exit(1);
});

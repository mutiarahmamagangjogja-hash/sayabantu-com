import { chromium } from 'playwright';

async function run() {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    page.on('console', msg => console.log('BROWSER LOG:', msg.text()));
    page.on('pageerror', err => console.log('BROWSER PAGEERROR:', err.message));

    await page.goto('http://127.0.0.1:8000/');
    await page.waitForTimeout(500);

    // Call spaNavigateTo directly in page to see error
    const result = await page.evaluate(async () => {
        try {
            console.log('Testing spaNavigateTo to /cara-kerja');
            await window.spaNavigateTo('http://127.0.0.1:8000/cara-kerja', true);
            return { success: true, url: window.location.href };
        } catch (e) {
            return { success: false, error: e.message, stack: e.stack };
        }
    });

    console.log('spaNavigateTo result:', result);
    console.log('Page URL after call:', page.url());

    await browser.close();
}

run().catch(console.error);

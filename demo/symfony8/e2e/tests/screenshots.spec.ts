import { test, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { resolve } from 'node:path';

/** REQ-DEMO-013 — demo use-case card (title + form + barcode widget). */
const outDir = process.env.SCREENSHOT_DIR
  ? resolve(process.env.SCREENSHOT_DIR)
  : resolve(__dirname, '../../../../docs/images/demo');

function useCasePanel(page: import('@playwright/test').Page) {
  return page.locator('.barcode-demo-card').first();
}

test.beforeAll(() => {
  mkdirSync(outDir, { recursive: true });
});

test.describe('BarcodeInput screenshots (use-case context)', () => {
  test('overview — empty field + scan in demo card', async ({ page }) => {
    await page.goto('/demo/barcode/ean-scanner');
    const panel = useCasePanel(page);
    await expect(panel).toBeVisible();
    await expect(panel.locator('nowo-barcode-input [data-nowo-barcode-scan-trigger]')).toBeVisible();
    await panel.screenshot({ path: resolve(outDir, 'overview.png') });
  });

  test('interaction — code entered in demo card', async ({ page }) => {
    await page.goto('/demo/barcode/ean-scanner');
    const panel = useCasePanel(page);
    await expect(panel).toBeVisible();
    await panel.locator('nowo-barcode-input input').first().fill('40170725');
    await expect(panel.locator('nowo-barcode-input input').first()).toHaveValue('40170725');
    await panel.screenshot({ path: resolve(outDir, 'interaction.png') });
  });
});

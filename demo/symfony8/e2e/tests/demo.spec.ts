import { test, expect } from '@playwright/test';

test.describe('BarcodeInput demo', () => {
  test('EAN scanner form shows barcode widget', async ({ page }) => {
    const response = await page.goto('/demo/barcode/ean-scanner');
    expect(response?.ok()).toBeTruthy();
    await expect(page.locator('nowo-barcode-input').first()).toBeVisible();
    await expect(page.locator('[data-nowo-barcode-scan-trigger]').first()).toBeVisible();
  });

  test('manual entry accepts a code', async ({ page }) => {
    await page.goto('/demo/barcode/manual-only');
    const host = page.locator('nowo-barcode-input').first();
    await expect(host).toBeVisible();
    const input = host.locator('input').first();
    await input.fill('40170725');
    await expect(input).toHaveValue('40170725');
  });
});

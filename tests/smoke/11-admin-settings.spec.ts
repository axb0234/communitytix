import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Settings', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('settings page shows organisation form', async ({ page }) => {
    await page.goto('/admin/settings');
    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('input[name="tagline"]')).toBeVisible();
    await assertNoServerError(page);
  });

  test('settings page shows currency and timezone fields', async ({ page }) => {
    await page.goto('/admin/settings');
    await expect(page.locator('select[name="currency"], input[name="currency"]').first()).toBeVisible();
    await expect(page.locator('select[name="timezone"], input[name="timezone"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('settings page shows PayPal configuration section', async ({ page }) => {
    await page.goto('/admin/settings');
    const body = await page.textContent('body');
    expect(body).toMatch(/paypal/i);
    await assertNoServerError(page);
  });

  test('settings page has save buttons', async ({ page }) => {
    await page.goto('/admin/settings');
    const saveButtons = page.locator('button[type="submit"]');
    expect(await saveButtons.count()).toBeGreaterThan(0);
  });
});

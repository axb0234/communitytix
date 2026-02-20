import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Orders, RSVPs, Cash & POS', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('orders page shows filter controls', async ({ page }) => {
    await page.goto('/admin/orders');
    // Should have filter/search controls
    await expect(page.locator('select, input[type="search"], input[name="search"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('orders page has export button', async ({ page }) => {
    await page.goto('/admin/orders');
    const exportLink = page.locator('a[href*="export"]');
    await expect(exportLink.first()).toBeVisible();
  });

  test('order detail page loads', async ({ page }) => {
    await page.goto('/admin/orders');
    const orderLink = page.locator('a[href*="/orders/"]').first();
    if (await orderLink.isVisible()) {
      await orderLink.click();
      await page.waitForLoadState('networkidle');
      const body = await page.textContent('body');
      expect(body).toMatch(/order|CTX-/i);
      await assertNoServerError(page);
    }
  });

  test('RSVPs page shows data and filter', async ({ page }) => {
    await page.goto('/admin/rsvps');
    await expect(page.locator('body')).toContainText('RSVP');
    await assertNoServerError(page);
  });

  test('RSVPs page has export button', async ({ page }) => {
    await page.goto('/admin/rsvps');
    const exportLink = page.locator('a[href*="export"]');
    await expect(exportLink.first()).toBeVisible();
  });

  test('cash collections page loads with form', async ({ page }) => {
    await page.goto('/admin/cash');
    // Should have a form to record cash or a table of records
    await assertNoServerError(page);
    const body = await page.textContent('body');
    expect(body).toMatch(/cash|collection|record/i);
  });

  test('cash page has export button', async ({ page }) => {
    await page.goto('/admin/cash');
    const exportLink = page.locator('a[href*="export"]');
    await expect(exportLink.first()).toBeVisible();
  });

  test('POS page loads with form', async ({ page }) => {
    await page.goto('/admin/pos');
    await assertNoServerError(page);
    const body = await page.textContent('body');
    expect(body).toMatch(/card|door|pos|payment/i);
  });

  test('POS page has export button', async ({ page }) => {
    await page.goto('/admin/pos');
    const exportLink = page.locator('a[href*="export"]');
    await expect(exportLink.first()).toBeVisible();
  });
});

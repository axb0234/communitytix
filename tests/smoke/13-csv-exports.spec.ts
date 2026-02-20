import { test, expect } from '@playwright/test';
import { adminLogin } from './helpers';

test.describe('Admin CSV Exports', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('events export returns CSV', async ({ page }) => {
    await page.goto('/admin/events');
    const [download] = await Promise.all([
      page.waitForEvent('download', { timeout: 10000 }),
      page.locator('a[href*="events/export"]').first().click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
  });

  test('orders export returns CSV', async ({ page }) => {
    await page.goto('/admin/orders');
    const [download] = await Promise.all([
      page.waitForEvent('download', { timeout: 10000 }),
      page.locator('a[href*="orders/export"]').first().click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
  });

  test('RSVPs export returns CSV', async ({ page }) => {
    await page.goto('/admin/rsvps');
    const [download] = await Promise.all([
      page.waitForEvent('download', { timeout: 10000 }),
      page.locator('a[href*="rsvps/export"]').first().click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
  });

  test('cash export returns CSV', async ({ page }) => {
    await page.goto('/admin/cash');
    const [download] = await Promise.all([
      page.waitForEvent('download', { timeout: 10000 }),
      page.locator('a[href*="cash/export"]').first().click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
  });

  test('POS export returns CSV', async ({ page }) => {
    await page.goto('/admin/pos');
    const [download] = await Promise.all([
      page.waitForEvent('download', { timeout: 10000 }),
      page.locator('a[href*="pos/export"]').first().click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
  });
});

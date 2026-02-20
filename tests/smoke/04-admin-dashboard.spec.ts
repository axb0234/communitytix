import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('dashboard shows stat cards', async ({ page }) => {
    await expect(page.locator('.stat-card').first()).toBeVisible();
    const statCards = page.locator('.stat-card');
    const count = await statCards.count();
    expect(count).toBe(6);

    // Verify stat labels
    const body = await page.textContent('body');
    expect(body).toContain('Members');
    expect(body).toContain('Pending');
    expect(body).toContain('Upcoming Events');
    expect(body).toContain('Blog Posts');
    expect(body).toContain('Orders');
    expect(body).toContain('Revenue');
    await assertNoServerError(page);
  });

  test('dashboard shows recent orders table', async ({ page }) => {
    await expect(page.locator('body')).toContainText('Recent Orders');
    const ordersTable = page.locator('table').first();
    await expect(ordersTable).toBeVisible();
    await assertNoServerError(page);
  });

  test('dashboard shows upcoming events table', async ({ page }) => {
    await expect(page.locator('body')).toContainText('Upcoming Events');
    await assertNoServerError(page);
  });

  test('sidebar navigation links are visible', async ({ page }) => {
    const sidebar = page.locator('.sidebar');
    await expect(sidebar).toBeVisible();
    await expect(sidebar).toContainText('Dashboard');
    await expect(sidebar).toContainText('Home Page');
    await expect(sidebar).toContainText('Blog Posts');
    await expect(sidebar).toContainText('Events');
    await expect(sidebar).toContainText('Orders');
    await expect(sidebar).toContainText('RSVPs');
    await expect(sidebar).toContainText('Cash Collections');
    await expect(sidebar).toContainText('Card at Door');
    await expect(sidebar).toContainText('Members');
    await expect(sidebar).toContainText('Settings');
    await expect(sidebar).toContainText('Help & Guide');
    await expect(sidebar).toContainText('View Site');
  });
});

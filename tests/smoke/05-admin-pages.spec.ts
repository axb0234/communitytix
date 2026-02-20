import { test, expect } from '@playwright/test';
import { adminLogin, assertAdminPageLoads, assertNoServerError } from './helpers';

test.describe('Admin Pages - All Load Without Errors', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('home content page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/home-content', 'Home Page');
  });

  test('blog list page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/blog', 'Blog');
    // Should have blog post entries
    const cards = page.locator('.card');
    expect(await cards.count()).toBeGreaterThan(0);
  });

  test('blog create page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/blog/create');
    await expect(page.locator('input[name="title"]')).toBeVisible();
  });

  test('events list page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/events', 'Events');
    await expect(page.locator('table')).toBeVisible();
  });

  test('events create page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/events/create');
    await expect(page.locator('input[name="title"]')).toBeVisible();
  });

  test('orders page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/orders', 'Orders');
  });

  test('RSVPs page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/rsvps', 'RSVP');
  });

  test('cash collections page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/cash', 'Cash');
  });

  test('POS page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/pos', 'Card');
  });

  test('members page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/members', 'Members');
    await expect(page.locator('table')).toBeVisible();
  });

  test('settings page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/settings', 'Settings');
    await expect(page.locator('input[name="name"]')).toBeVisible();
  });

  test('help page loads', async ({ page }) => {
    await assertAdminPageLoads(page, '/admin/help', 'Help');
    // Should have contents section
    await expect(page.locator('body')).toContainText('Contents');
    await expect(page.locator('body')).toContainText('Dashboard');
  });
});

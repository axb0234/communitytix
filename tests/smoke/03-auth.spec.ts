import { test, expect } from '@playwright/test';
import { ADMIN_EMAIL, ADMIN_PASSWORD, assertNoServerError } from './helpers';

test.describe('Authentication', () => {
  test('successful login redirects to admin dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin');
    await expect(page.locator('.top-navbar')).toContainText('Dashboard');
    await expect(page.locator('.top-navbar')).toContainText('Demo Governor');
    await assertNoServerError(page);
  });

  test('failed login shows error message', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'wrong@example.com');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    // Should stay on login page with error
    expect(page.url()).toContain('/login');
    const body = await page.textContent('body');
    expect(body).toMatch(/credentials|invalid|failed|incorrect/i);
  });

  test('logout redirects to homepage', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin');
    // Logout - find the logout button in top navbar
    await page.locator('.top-navbar form button[type="submit"]').click();
    await page.waitForLoadState('networkidle');
    // Should redirect to homepage (not /admin)
    expect(page.url()).not.toContain('/admin');
    // Visiting admin again should redirect to login
    await page.goto('/admin');
    await page.waitForURL('**/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('unauthenticated access to admin redirects to login', async ({ page }) => {
    await page.goto('/admin');
    await page.waitForURL('**/login');
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('unauthenticated access to admin subpages redirects to login', async ({ page }) => {
    const protectedPages = ['/admin/events', '/admin/blog', '/admin/members', '/admin/settings', '/admin/orders'];
    for (const url of protectedPages) {
      await page.goto(url);
      await page.waitForURL('**/login');
    }
  });
});

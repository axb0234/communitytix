import { type Page, expect } from '@playwright/test';

export const DEMO_URL = 'https://demo.communitytix.org';
export const ADMIN_EMAIL = 'governor@democommunitytix.org';
export const ADMIN_PASSWORD = 'demo1';

/**
 * Login to the admin panel and return the authenticated page.
 */
export async function adminLogin(page: Page): Promise<void> {
  await page.goto('/login');
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/admin');
  await expect(page.locator('.content-wrapper')).toBeVisible();
}

/**
 * Logout from the admin panel.
 */
export async function adminLogout(page: Page): Promise<void> {
  await page.locator('form[action*="logout"] button[type="submit"]').click();
  await page.waitForURL('**/login');
}

/**
 * Assert page loaded without server errors (no 500, 404, etc.).
 */
export async function assertNoServerError(page: Page): Promise<void> {
  const body = await page.textContent('body');
  expect(body).not.toContain('Server Error');
  expect(body).not.toContain('500 | Server Error');
  expect(body).not.toContain('404 | Not Found');
}

/**
 * Assert an admin page loads with the sidebar and content area.
 */
export async function assertAdminPageLoads(page: Page, url: string, pageTitle?: string): Promise<void> {
  await page.goto(url);
  await expect(page.locator('.sidebar')).toBeVisible();
  await expect(page.locator('.content-wrapper')).toBeVisible();
  if (pageTitle) {
    await expect(page.locator('.top-navbar')).toContainText(pageTitle);
  }
  await assertNoServerError(page);
}

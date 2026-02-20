import { test, expect } from '@playwright/test';
import { assertNoServerError } from './helpers';

test.describe('Public Member Signup', () => {
  test('signup form validates required fields', async ({ page }) => {
    await page.goto('/join');
    // Submit empty form
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    // Should show validation errors (HTML5 or server-side)
    const body = await page.textContent('body');
    // Either browser validation prevents submission or we see error messages
    const url = page.url();
    expect(url).toContain('/join');
    await assertNoServerError(page);
  });

  test('signup form validates email format', async ({ page }) => {
    await page.goto('/join');
    await page.fill('input[name="first_name"]', 'Test');
    await page.fill('input[name="last_name"]', 'User');
    await page.fill('input[name="email"]', 'invalid-email');
    if (await page.locator('input[name="phone"]').isVisible()) {
      await page.fill('input[name="phone"]', '07700900000');
    }
    if (await page.locator('input[name="password"]').isVisible()) {
      await page.fill('input[name="password"]', 'testpass123');
    }
    if (await page.locator('input[name="password_confirmation"]').isVisible()) {
      await page.fill('input[name="password_confirmation"]', 'testpass123');
    }
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    // Should stay on the signup page (validation fail)
    expect(page.url()).toContain('/join');
  });

  test('signup form submits successfully with valid data', async ({ page }) => {
    await page.goto('/join');
    const timestamp = Date.now();
    await page.fill('input[name="first_name"]', 'Smoke');
    await page.fill('input[name="last_name"]', `Test${timestamp}`);
    await page.fill('input[name="email"]', `smoketest+member${timestamp}@example.com`);
    if (await page.locator('input[name="phone"]').isVisible()) {
      await page.fill('input[name="phone"]', '07700900001');
    }
    if (await page.locator('input[name="password"]').isVisible()) {
      await page.fill('input[name="password"]', 'SmokeTest123!');
    }
    if (await page.locator('input[name="password_confirmation"]').isVisible()) {
      await page.fill('input[name="password_confirmation"]', 'SmokeTest123!');
    }
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    const body = await page.textContent('body');
    // Should redirect to success page or show confirmation
    expect(body).toMatch(/thank|success|pending|approval|submitted|welcome/i);
  });
});

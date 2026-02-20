import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Members Management', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('members list shows table with members', async ({ page }) => {
    await page.goto('/admin/members');
    const table = page.locator('table');
    await expect(table).toBeVisible();
    // Should have member rows
    const rows = table.locator('tbody tr');
    expect(await rows.count()).toBeGreaterThan(0);
    await assertNoServerError(page);
  });

  test('members list has filter controls', async ({ page }) => {
    await page.goto('/admin/members');
    // Should have type/status filter dropdowns
    await expect(page.locator('select').first()).toBeVisible();
  });

  test('member detail page loads', async ({ page }) => {
    await page.goto('/admin/members');
    const memberLink = page.locator('a[href*="/members/"]').first();
    if (await memberLink.isVisible()) {
      await memberLink.click();
      await page.waitForLoadState('networkidle');
      const body = await page.textContent('body');
      expect(body).toMatch(/member|name|email|type|status/i);
      await assertNoServerError(page);
    }
  });

  test('member edit page loads', async ({ page }) => {
    await page.goto('/admin/members');
    const editLink = page.locator('a[href*="/members/"][href*="/edit"]').first();
    if (await editLink.isVisible()) {
      await editLink.click();
      await page.waitForLoadState('networkidle');
      await expect(page.locator('input[name="first_name"]')).toBeVisible();
      await assertNoServerError(page);
    }
  });
});

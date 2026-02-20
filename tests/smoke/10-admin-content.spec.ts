import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Home Content Management', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('home content page shows carousel and blocks sections', async ({ page }) => {
    await page.goto('/admin/home-content');
    const body = await page.textContent('body');
    expect(body).toMatch(/carousel/i);
    expect(body).toMatch(/content block/i);
    await assertNoServerError(page);
  });

  test('carousel create form loads', async ({ page }) => {
    await page.goto('/admin/home-content/carousel/create');
    await expect(page.locator('input[name="caption"], input[name="title"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('carousel edit page loads for existing item', async ({ page }) => {
    await page.goto('/admin/home-content');
    const editLink = page.locator('a[href*="/carousel/"][href*="/edit"]').first();
    if (await editLink.isVisible()) {
      await editLink.click();
      await page.waitForLoadState('networkidle');
      await assertNoServerError(page);
    }
  });

  test('content block create form loads', async ({ page }) => {
    await page.goto('/admin/home-content/blocks/create');
    await expect(page.locator('input[name="title"], input[name="heading"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('content block edit page loads for existing block', async ({ page }) => {
    await page.goto('/admin/home-content');
    const editLink = page.locator('a[href*="/blocks/"][href*="/edit"]').first();
    if (await editLink.isVisible()) {
      await editLink.click();
      await page.waitForLoadState('networkidle');
      await assertNoServerError(page);
    }
  });
});

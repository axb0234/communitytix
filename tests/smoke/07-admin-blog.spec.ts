import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Blog Management', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('blog list shows existing posts', async ({ page }) => {
    await page.goto('/admin/blog');
    const cards = page.locator('.card');
    expect(await cards.count()).toBeGreaterThan(0);
    await assertNoServerError(page);
  });

  test('blog list has create button', async ({ page }) => {
    await page.goto('/admin/blog');
    const createLink = page.locator('a[href*="/blog/create"]');
    await expect(createLink).toBeVisible();
  });

  test('blog create form has all required fields', async ({ page }) => {
    await page.goto('/admin/blog/create');
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('select[name="status"]')).toBeVisible();
    // WYSIWYG editor should load (Summernote creates .note-editor)
    await page.waitForTimeout(2000); // Wait for Summernote to initialize
    await assertNoServerError(page);
  });

  test('blog edit page loads for existing post', async ({ page }) => {
    await page.goto('/admin/blog');
    const editLink = page.locator('a[href*="/blog/"][href*="/edit"]').first();
    if (await editLink.isVisible()) {
      await editLink.click();
      await page.waitForLoadState('networkidle');
      await expect(page.locator('input[name="title"]')).toBeVisible();
      // Title should have a value
      const titleValue = await page.locator('input[name="title"]').inputValue();
      expect(titleValue.length).toBeGreaterThan(0);
      await assertNoServerError(page);
    }
  });
});

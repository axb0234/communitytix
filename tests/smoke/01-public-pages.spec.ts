import { test, expect } from '@playwright/test';
import { assertNoServerError } from './helpers';

test.describe('Public Site - Page Loading', () => {
  test('homepage loads with carousel and content', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/Riverside Community Centre/);
    // Carousel should be present
    await expect(page.locator('#heroCarousel')).toBeVisible();
    // Content blocks should be present
    await expect(page.locator('body')).toContainText('Our Community');
    await assertNoServerError(page);
  });

  test('homepage shows upcoming events section', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('body')).toContainText(/event/i);
    await assertNoServerError(page);
  });

  test('homepage shows latest blog posts', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('body')).toContainText(/blog|post/i);
    await assertNoServerError(page);
  });

  test('events listing page loads', async ({ page }) => {
    await page.goto('/events');
    await expect(page).toHaveTitle(/Events/);
    // Should have event cards
    await expect(page.locator('.card').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('events page shows upcoming and past tabs', async ({ page }) => {
    await page.goto('/events');
    const body = await page.textContent('body');
    expect(body).toMatch(/upcoming|past/i);
    await assertNoServerError(page);
  });

  test('blog listing page loads', async ({ page }) => {
    await page.goto('/blog');
    await expect(page).toHaveTitle(/Blog/);
    await expect(page.locator('.card').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('blog post detail page loads', async ({ page }) => {
    await page.goto('/blog');
    // Click a "Read More" link to a blog post
    const readMoreLink = page.locator('a:has-text("Read More"), a:has-text("Read more")').first();
    await expect(readMoreLink).toBeVisible();
    await readMoreLink.click();
    await page.waitForLoadState('networkidle');
    // Blog post page should have content
    const body = await page.textContent('body');
    expect(body!.length).toBeGreaterThan(200);
    await assertNoServerError(page);
  });

  test('member signup page loads', async ({ page }) => {
    await page.goto('/join');
    await expect(page).toHaveTitle(/Join|Sign|Member/i);
    await expect(page.locator('form')).toBeVisible();
    // Should have required fields
    await expect(page.locator('input[name="first_name"]')).toBeVisible();
    await expect(page.locator('input[name="last_name"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await assertNoServerError(page);
  });

  test('login page loads', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveTitle(/Login/);
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await assertNoServerError(page);
  });

  test('forgot password page loads', async ({ page }) => {
    await page.goto('/forgot-password');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
    await assertNoServerError(page);
  });
});

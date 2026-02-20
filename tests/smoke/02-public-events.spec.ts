import { test, expect } from '@playwright/test';
import { assertNoServerError } from './helpers';

test.describe('Public Site - Events', () => {
  test('free event detail page shows RSVP form', async ({ page }) => {
    await page.goto('/events');
    // Click "View Details" on a free event (look for "Free RSVP" badge nearby)
    const freeEventLink = page.locator('a:has-text("View Details")').first();
    await expect(freeEventLink).toBeVisible();

    // Try event detail pages to find a FREE one with RSVP form
    const viewLinks = page.locator('a:has-text("View Details"), a:has-text("View")');
    const count = await viewLinks.count();
    let foundFree = false;
    for (let i = 0; i < Math.min(count, 20); i++) {
      const href = await viewLinks.nth(i).getAttribute('href');
      if (!href) continue;
      await page.goto(href);
      const body = await page.textContent('body');
      if (body && body.includes('RSVP')) {
        foundFree = true;
        await expect(page.locator('input[name="name"]')).toBeVisible();
        break;
      }
    }
    expect(foundFree).toBe(true);
    await assertNoServerError(page);
  });

  test('ticketed event detail page shows ticket types and prices', async ({ page }) => {
    // Navigate directly to a known ticketed event
    await page.goto('/events/easter-family-fun-day');
    await expect(page.locator('body')).toContainText('Get Tickets');
    await expect(page.locator('body')).toContainText('GBP');
    // Should have ticket type names
    await expect(page.locator('body')).toContainText('Adult');
    await assertNoServerError(page);
  });

  test('PWYC event shows suggested amount buttons', async ({ page }) => {
    // Navigate directly to a known PWYC event
    await page.goto('/events/easter-family-fun-day');
    await expect(page.locator('body')).toContainText('Pay What You Can');
    // Should have suggested amount buttons
    await expect(page.locator('button:has-text("GBP 5.00")').first()).toBeVisible();
    await expect(page.locator('button:has-text("GBP 10.00")').first()).toBeVisible();
    await expect(page.locator('button:has-text("GBP 20.00")').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('RSVP submission on free event works', async ({ page }) => {
    // Navigate to a known free upcoming event
    await page.goto('/events');
    const viewLinks = page.locator('a:has-text("View Details"), a:has-text("View")');
    const count = await viewLinks.count();

    let foundFree = false;
    for (let i = 0; i < Math.min(count, 20); i++) {
      const href = await viewLinks.nth(i).getAttribute('href');
      if (!href) continue;
      await page.goto(href);
      const nameInput = page.locator('input[name="name"]');
      if (await nameInput.isVisible({ timeout: 1000 }).catch(() => false)) {
        foundFree = true;
        const timestamp = Date.now();
        await page.fill('input[name="name"]', `Smoke Test ${timestamp}`);
        await page.fill('input[name="email"]', `smoketest+${timestamp}@example.com`);
        if (await page.locator('input[name="phone"]').isVisible()) {
          await page.fill('input[name="phone"]', '07700900000');
        }
        const guestsInput = page.locator('input[name="guests"], select[name="guests"]');
        if (await guestsInput.isVisible()) {
          await guestsInput.fill('1');
        }
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        const responseBody = await page.textContent('body');
        expect(responseBody).toMatch(/thank|success|confirmed|rsvp/i);
        break;
      }
    }
    expect(foundFree).toBe(true);
  });

  test('event detail page shows location and date', async ({ page }) => {
    await page.goto('/events/easter-family-fun-day');
    const body = await page.textContent('body');
    expect(body).toContain('2026');
    expect(body).toContain('Riverside Community Hall');
    await assertNoServerError(page);
  });

  test('past events are visible on events page', async ({ page }) => {
    await page.goto('/events');
    await expect(page.locator('body')).toContainText('Past Events');
    await assertNoServerError(page);
  });
});

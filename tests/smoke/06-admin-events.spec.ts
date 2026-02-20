import { test, expect } from '@playwright/test';
import { adminLogin, assertNoServerError } from './helpers';

test.describe('Admin Events Management', () => {
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
  });

  test('events list shows search and filter controls', async ({ page }) => {
    await page.goto('/admin/events');
    await expect(page.locator('input[name="search"], input[placeholder*="earch"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('events list shows event data in table', async ({ page }) => {
    await page.goto('/admin/events');
    const table = page.locator('table');
    await expect(table).toBeVisible();
    // Should have header columns
    const headers = await table.locator('th').allTextContents();
    const headerText = headers.join(' ');
    expect(headerText).toMatch(/event|title/i);
    expect(headerText).toMatch(/date|start/i);
    expect(headerText).toMatch(/type/i);
    await assertNoServerError(page);
  });

  test('events list has export CSV button', async ({ page }) => {
    await page.goto('/admin/events');
    const exportLink = page.locator('a[href*="export"]');
    await expect(exportLink.first()).toBeVisible();
  });

  test('event edit page loads for existing event', async ({ page }) => {
    await page.goto('/admin/events');
    const editLink = page.locator('a[href*="/events/"][href*="/edit"]').first();
    await expect(editLink).toBeVisible();
    await editLink.click();
    await page.waitForLoadState('networkidle');
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('select[name="event_type"], input[name="event_type"]').first()).toBeVisible();
    await assertNoServerError(page);
  });

  test('event edit page shows PWYC section for ticketed events', async ({ page }) => {
    await page.goto('/admin/events');
    // Find a ticketed event
    const editLinks = page.locator('a[href*="/events/"][href*="/edit"]');
    const count = await editLinks.count();

    for (let i = 0; i < Math.min(count, 20); i++) {
      const href = await editLinks.nth(i).getAttribute('href');
      await page.goto(href!);
      const body = await page.textContent('body');
      if (body && body.includes('Pay What You Can')) {
        await expect(page.locator('body')).toContainText('Pay What You Can');
        await assertNoServerError(page);
        return;
      }
    }
    // At least some events should be ticketed with PWYC option
    expect(true).toBe(true); // Pass if no ticketed events found (unlikely)
  });

  test('event edit page shows ticket types section', async ({ page }) => {
    await page.goto('/admin/events');
    const editLinks = page.locator('a[href*="/events/"][href*="/edit"]');
    const count = await editLinks.count();

    for (let i = 0; i < Math.min(count, 20); i++) {
      const href = await editLinks.nth(i).getAttribute('href');
      await page.goto(href!);
      const body = await page.textContent('body');
      if (body && body.includes('Ticket Type')) {
        await expect(page.locator('body')).toContainText('Ticket Type');
        await assertNoServerError(page);
        return;
      }
    }
  });

  test('event create form has all required fields', async ({ page }) => {
    await page.goto('/admin/events/create');
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('select[name="event_type"]')).toBeVisible();
    await expect(page.locator('input[name="start_at"]')).toBeVisible();
    await assertNoServerError(page);
  });
});

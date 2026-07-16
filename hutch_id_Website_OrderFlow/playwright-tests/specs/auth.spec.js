import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage.js';
import { DashboardPage } from '../pages/DashboardPage.js';
import { ROLES, ROUTES } from '../utils/constants.js';

test.describe('Authentication & Authorization Flow', () => {
    test('Negatif: Login dengan email yang salah', async ({ page }) => {
        const loginPage = new LoginPage(page);
        await loginPage.navigate();
        await loginPage.login('invalid@hutch.id', 'wrongpassword');
        await expect(loginPage.errorMessage).toBeVisible();
    });
    test('Positif: Staf Penjualan bisa login & melihat Dashboard', async ({ page }) => {
        const loginPage = new LoginPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.STAF.email, ROLES.STAF.password);
        await expect(page).toHaveURL(new RegExp(ROUTES.DASHBOARD));
    });
    test('Positif: Administrator bisa login ke Admin Dashboard', async ({ page }) => {
        const loginPage = new LoginPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.ADMIN.email, ROLES.ADMIN.password);
        await expect(page).toHaveURL(new RegExp(ROUTES.ADMIN_DASHBOARD));
    });
    test('Positif: User bisa Logout dari Dashboard', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const dashboard = new DashboardPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.PEMILIK.email, ROLES.PEMILIK.password);
        await expect(page).toHaveURL(new RegExp(ROUTES.DASHBOARD));
        await dashboard.logout();
        await expect(page).toHaveURL(new RegExp(ROUTES.LOGIN));
    });
});

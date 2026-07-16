import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage.js';
import { UserManagementPage } from '../pages/UserManagementPage.js';
import { ROLES } from '../utils/constants.js';

test.describe('Admin User Management', () => {
    test('Negatif: Staf tidak bisa mengakses menu Manajemen User', async ({ page }) => {
        const loginPage = new LoginPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.STAF.email, ROLES.STAF.password);
        await page.goto('/admin/users');
        await expect(page.locator('body')).not.toContainText('Manajemen Pengguna');
    });
    test('Positif: Administrator dapat membuat akun Staff baru', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const userPage = new UserManagementPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.ADMIN.email, ROLES.ADMIN.password);
        await userPage.navigate();
        const timestamp = Date.now();
        await userPage.tambahUser("Staf Baru ${timestamp}", "staf${timestamp}@hutch.id", 'password123', 'staf_penjualan');
        await expect(userPage.successMessage).toBeVisible();
    });
});

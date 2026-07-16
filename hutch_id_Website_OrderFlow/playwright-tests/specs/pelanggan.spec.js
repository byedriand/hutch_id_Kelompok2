import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage.js';
import { PelangganPage } from '../pages/PelangganPage.js';
import { ROLES } from '../utils/constants.js';

test.describe('Pelanggan CRUD Flow', () => {
    test('Positif: Staf Penjualan dapat menambahkan Pelanggan baru', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const pelangganPage = new PelangganPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.STAF.email, ROLES.STAF.password);
        await pelangganPage.navigate();
        const timestamp = Date.now();
        await pelangganPage.tambahPelanggan("Toko QA ${timestamp}", "qa${timestamp}@test.com", "0812${timestamp}", 'Jl. Testing Otomatis No. 1');
        await expect(pelangganPage.successMessage).toBeVisible();
    });
});

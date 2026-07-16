import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage.js';
import { ProdukPage } from '../pages/ProdukPage.js';
import { ROLES } from '../utils/constants.js';

test.describe('Manajemen Produk/Stok', () => {
    test('Positif: Operator Gudang dapat menambahkan produk baru', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const produkPage = new ProdukPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.GUDANG.email, ROLES.GUDANG.password);
        await produkPage.navigate();
        const timestamp = Date.now();
        await produkPage.tambahProduk("Tas QA Testing ${timestamp}", 100000, 50);
        await expect(produkPage.successMessage).toBeVisible();
    });
});

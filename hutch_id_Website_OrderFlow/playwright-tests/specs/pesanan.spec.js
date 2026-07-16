import { test, expect } from '@playwright/test';
import { LoginPage } from '../pages/LoginPage.js';
import { PesananPage } from '../pages/PesananPage.js';
import { ROLES } from '../utils/constants.js';

test.describe('Order/Pesanan Business Flow', () => {
    test('Positif: Staf Penjualan berhasil membuat Pesanan (PO) baru', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const pesananPage = new PesananPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.STAF.email, ROLES.STAF.password);
        await pesananPage.navigate();
        await pesananPage.buatPesananBaru('Budi Bag Store', 'Tas Kanvas Custom', 5, 'Kirim cepat via JNE');
        await expect(pesananPage.successMessage).toBeVisible();
        await expect(pesananPage.successMessage).toContainText('berhasil');
    });
    test('Positif: Pemilik UMKM dapat mengonfirmasi Pesanan', async ({ page }) => {
        const loginPage = new LoginPage(page);
        const pesananPage = new PesananPage(page);
        await loginPage.navigate();
        await loginPage.login(ROLES.PEMILIK.email, ROLES.PEMILIK.password);
        await pesananPage.navigate();
        await pesananPage.konfirmasiPesananPertama();
        await expect(pesananPage.successMessage).toBeVisible();
        await expect(pesananPage.successMessage).toContainText('dikonfirmasi');
    });
});

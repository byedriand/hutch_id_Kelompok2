export class PelangganPage {
    constructor(page) {
        this.page = page;
        this.btnTambah = page.locator('a[href$="/pelanggan/create"]');
        this.inputNama = page.locator('input[name="nama"]');
        this.inputEmail = page.locator('input[name="email"]');
        this.inputTelepon = page.locator('input[name="telepon"]');
        this.inputAlamat = page.locator('textarea[name="alamat"]');
        this.simpanButton = page.locator('button[type="submit"]');
        this.successMessage = page.locator('.alert-success');
    }
    async navigate() { await this.page.goto('/pelanggan'); }
    async tambahPelanggan(nama, email, telepon, alamat) {
        await this.btnTambah.click();
        await this.inputNama.fill(nama);
        await this.inputEmail.fill(email);
        await this.inputTelepon.fill(telepon);
        await this.inputAlamat.fill(alamat);
        await this.simpanButton.click();
    }
}

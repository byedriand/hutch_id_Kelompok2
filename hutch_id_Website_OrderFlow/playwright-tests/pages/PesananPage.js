export class PesananPage {
    constructor(page) {
        this.page = page;
        this.btnBuatPesanan = page.locator('a[href$="/pesanan/create"]');
        this.pelangganSelect = page.locator('select[name="pelanggan_id"]');
        this.produkSelect = page.locator('select[name="produk_id[]"]').first();
        this.jumlahInput = page.locator('input[name="jumlah[]"]').first();
        this.catatanInput = page.locator('textarea[name="catatan"]');
        this.simpanButton = page.locator('button:has-text("Simpan")');
        this.successMessage = page.locator('.alert-success');
    }

    async navigate() { await this.page.goto('/pesanan'); }

    async buatPesananBaru(pelangganValue, produkValue, jumlah, catatan) {
        await this.btnBuatPesanan.click();
        await this.pelangganSelect.selectOption({ label: pelangganValue });
        await this.produkSelect.selectOption({ label: produkValue });
        await this.jumlahInput.fill(jumlah.toString());
        await this.catatanInput.fill(catatan);
        await this.simpanButton.click();
    }

    async konfirmasiPesananPertama() {
        const btnKonfirmasi = this.page.locator('form[action*="/confirm"] button').first();
        await btnKonfirmasi.click();
    }
}

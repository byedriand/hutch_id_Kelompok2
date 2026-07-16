export class ProdukPage {
    constructor(page) {
        this.page = page;
        this.btnTambah = page.locator('a[href$="/produk/create"]');
        this.inputNama = page.locator('input[name="nama"]');
        this.inputHarga = page.locator('input[name="harga_jual"]');
        this.inputStok = page.locator('input[name="stok"]');
        this.simpanButton = page.locator('button[type="submit"]');
        this.successMessage = page.locator('.alert-success');
    }
    async navigate() { await this.page.goto('/produk'); }
    async tambahProduk(nama, harga, stok) {
        await this.btnTambah.click();
        await this.inputNama.fill(nama);
        await this.inputHarga.fill(harga.toString());
        await this.inputStok.fill(stok.toString());
        await this.simpanButton.click();
    }
}

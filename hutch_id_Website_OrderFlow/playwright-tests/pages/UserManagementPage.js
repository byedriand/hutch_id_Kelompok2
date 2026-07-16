export class UserManagementPage {
    constructor(page) {
        this.page = page;
        this.btnTambah = page.locator('a[href$="/admin/users/create"]');
        this.inputNama = page.locator('input[name="name"]');
        this.inputEmail = page.locator('input[name="email"]');
        this.inputPassword = page.locator('input[name="password"]');
        this.selectRole = page.locator('select[name="role"]');
        this.simpanButton = page.locator('button[type="submit"]');
        this.successMessage = page.locator('.alert-success');
    }
    async navigate() { await this.page.goto('/admin/users'); }
    async tambahUser(nama, email, password, role) {
        await this.btnTambah.click();
        await this.inputNama.fill(nama);
        await this.inputEmail.fill(email);
        await this.inputPassword.fill(password);
        await this.selectRole.selectOption({ value: role });
        await this.simpanButton.click();
    }
}

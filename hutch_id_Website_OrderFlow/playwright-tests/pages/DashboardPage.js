export class DashboardPage {
    constructor(page) {
        this.page = page;
        this.navbarLogout = page.locator('form[action$="/logout"] button');
    }
    async logout() {
        await this.navbarLogout.click();
    }
}

import * as puppeteer from 'puppeteer';
import config from '../config';
import {BrowserContextConfig, Resolution} from '../types';
import {User} from '../user';

export class Browser {

    private config: BrowserContextConfig;
    private browser: puppeteer.Browser;
    private pageBase: puppeteer.Page;

    constructor(config: BrowserContextConfig) {
        this.config = config;
    }

    init = async () => {
        await this.resetBrowser();
    }

    login = async (user) => {
        await this.resetBrowser();
        await Promise.all([
            this.performLogin(user, this.pageBase, this.config.urlBase),
        ]);
    }

    private performLogin = async (user: User, page: puppeteer.Page, baseUrl: string) => {
        await page.bringToFront();
        await page.goto(`${baseUrl}/index.php/login`, {waitUntil: 'networkidle0'});
        //TODO: move to config
        await page.type('#user', user.uid);
        await page.type('#password', user.password);
        const inputElement = await page.$('input[type=submit]');
        await inputElement.click();
        await page.waitForNavigation({waitUntil: 'networkidle2'});
        return await page.waitForSelector('#header');
    }

    private resetBrowser = async () => {
        if (this.browser) {
            await this.browser.close();
        }
        this.browser = await puppeteer.launch({
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
            headless: this.config.headless,
            slowMo: this.config.slowMo,
        });
        this.pageBase = await this.browser.newPage();
        this.pageBase.setDefaultNavigationTimeout(60000);
    }

    resolutions = (): Array<Resolution> => {
        return this.config.resolutions;
    }

    goto = async (url: string) => {
        return this.pageBase.goto(url);
    }

    static default() {
        return new Browser(config);
    }

}

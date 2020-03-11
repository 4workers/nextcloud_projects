import * as puppeteer from 'puppeteer';
import config from '../config';
import {BrowserContextConfig, Resolution} from '../types';

export class Browser {

    private config: BrowserContextConfig;
    private browser: puppeteer.Browser;
    private pageBase: puppeteer.Page;

    constructor(config: BrowserContextConfig) {
        this.config = config;
    }

    init = async (test) => {
        await this.resetBrowser();
    }

    resetBrowser = async () => {
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

    static default() {
        return new Browser(config);
    }
}

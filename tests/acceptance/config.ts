import {BrowserContextConfig} from './types';

export default {

    resolutions: [
        {title: 'mobile', w: 360, h: 480},
        {title: 'narrow', w: 800, h: 600},
        {title: 'normal', w: 1024, h: 768},
        {title: 'wide', w: 1920, h: 1080},
        {title: 'qhd', w: 2560, h: 1440},
    ],

    urlBase: 'http://localhost:8888/',

    urlChange: 'http://localhost:8889/',

    outputDirectory: 'out',

    headless: true,

    slowMo: 0,

} as BrowserContextConfig

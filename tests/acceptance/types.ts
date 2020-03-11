export interface Resolution {
    title: string
    w: number
    h: number
}
export interface BrowserContextConfig {
    /**
     * Define resolutions to be tested when diffing screenshots
     */
    readonly resolutions: Array<Resolution>

    /**
     * URL that holds the base branch
     */
    readonly urlBase: string

    /**
     * URL that holds the branch to be diffed
     */
    readonly urlChange: string

    /**
     * Path to output directory for screenshot files
     */
    outputDirectory: string

    headless: boolean,

    slowMo: number
}

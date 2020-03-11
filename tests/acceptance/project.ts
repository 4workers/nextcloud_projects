export class Project {
    /**
     *
     * @param projectId the projectId as in the cloud system
     * @param ownerId the ownerId as in the cloud system
     * @param name
     * @param nextcloudProjectId the projectId as in nextcloud
     * @param url url in nextcloud
     */
    constructor(
        readonly projectId: string,
        readonly ownerId: string,
        readonly name: string,
        readonly nextcloudProjectId?: string,
        readonly url?: string,
    ) {}

}
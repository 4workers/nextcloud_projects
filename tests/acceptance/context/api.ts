import {AxiosInstance} from 'axios'
import {Project} from './project';

export class Api {

    private connection: AxiosInstance;


    constructor(connection: AxiosInstance) {
        this.connection = connection;
    }

    addProject = async (
        project: Project,
    ): Promise<Project> => {
        const response = await this.connection({
            method: 'POST',
            url: `/projects/${project.ownerId}`,
            data: {
                name: project.name,
                'foreign-id': project.projectId,
            },
        })
        const data = response.data
        const url = response.headers['content-location']
        return new Project(data.id, data.name, project.projectId, url)
    }
}

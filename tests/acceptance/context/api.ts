import {Project} from '../project';
import axios, { AxiosBasicCredentials, AxiosRequestConfig, AxiosInstance } from 'axios'
import {User} from '../user';
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

    createUser = async (user: User): Promise<void> => {
        await this.connection({
            method: 'POST',
            url: '/ocs/v1.php/cloud/users',
            data: {
                userId: user.uid,
                password: 'password',
            },
        })
    }

    deleteUser = async (user: User): Promise<void> => {
        await this.connection({
            method: 'DELETE',
            url: `/ocs/v1.php/cloud/users/${user.uid}`
        })
    }

    static default (): Api {
        //TODO: load from config
        const auth: AxiosBasicCredentials = {
            username: 'mrrobot',
            password: 'supercomplicatedpassword'
        }
        const config: AxiosRequestConfig = {
            auth,
            baseURL: 'http:/localhost:8888'
        }
        const connection = axios.create(config);
        return new Api(connection);
    }
}

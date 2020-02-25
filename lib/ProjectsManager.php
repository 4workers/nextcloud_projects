<?php
declare(strict_types=1);

namespace OCA\Projects;

use OCP\Files\Storage\IStorage;
use OCP\IUser;

class ProjectsManager implements ProjectsBackend
{
    /**
     * @var ProjectsBackend[]
     */
    private $backends = [];

    public function registerBackend(string $storageType, ProjectsBackend $backend)
    {
        $this->backends[$storageType] = $backend;
    }

    /**
     * @return ProjectsBackend[]
     */
    private function getBackends(): array
    {
        return $this->backends;
    }

    public function listProjects(IUser $user): array
    {
        $items = array_reduce(
            $this->getBackends(), function (array $items, ProjectsBackend $backend) use ($user) {
                return array_merge($items, $backend->listProjects($user));
            }, []
        );
        return $items;
    }

}

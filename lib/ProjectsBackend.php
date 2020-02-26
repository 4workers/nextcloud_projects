<?php
declare(strict_types=1);

namespace OCA\Projects;

use OCP\Files\Node;
use OCP\IUser;

interface ProjectsBackend
{
    /**
     * List all projects
     *
     * @param  IUser $user
     * @return ProjectItem[]
     */
    public function listProjects(IUser $user): array;

}

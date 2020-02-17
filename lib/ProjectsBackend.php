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

    /**
     * @param  IUser $user
     * @param  int   $fileId
     * @return Node|null
     */
    public function getProjectNodeById(IUser $user, int $fileId);
}

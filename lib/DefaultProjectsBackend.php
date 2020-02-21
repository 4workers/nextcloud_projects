<?php
declare(strict_types=1);

namespace OCA\Projects;

use OCA\Projects\Sabre\ProjectSymlink;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IUser;

class DefaultProjectsBackend implements ProjectsBackend
{

    /**
     * @var IRootFolder
     */
    private $rootFolder;
    /**
     * @var ProjectsStorage
     */
    private $projectsStorage;

    public function __construct(
        ProjectsStorage $projectsStorage,
        IRootFolder $rootFolder)
    {
        $this->rootFolder = $rootFolder;
        $this->projectsStorage = $projectsStorage;
    }

    /**
     * @param array $items
     * @param IUser $user
     * @return ProjectSymlink[]
     */
    private function mapToProjects(array $items, IUser $user): array
    {
        return array_map(
            function (Folder $folder) use ($user) {
                return new ProjectSymlink($folder);
            }, $items
        );
    }

    public function listProjects(IUser $user): array
    {
        $entries = $this->projectsStorage->allUserProjects($user->getUID());
        return $this->mapToProjects($entries, $user);
    }

}

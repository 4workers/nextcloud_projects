<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use OCA\Files_Trashbin\Trash\ITrashManager;
use OCA\Projects\ProjectsManager;
use OCP\IUser;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\ICollection;

class ProjectsHome implements ICollection {

	/** @var array */
	private $principalInfo;

	/** @var IUser */
	private $user;
    /**
     * @var ProjectsManager
     */
    private $projectsManager;

    public function __construct(
	    ProjectsManager $projectsManager,
		array $principalInfo,
		IUser $user
	) {
		$this->principalInfo = $principalInfo;
		$this->user = $user;
        $this->projectsManager = $projectsManager;
    }

	public function delete() {
		throw new Forbidden();
	}

	public function getName(): string {
		list(, $name) = \Sabre\Uri\split($this->principalInfo['uri']);
		return $name;
	}

	public function setName($name) {
		throw new Forbidden('Permission denied to rename this trashbin');
	}

	public function createFile($name, $data = null) {
		throw new Forbidden('Not allowed to create files in the trashbin');
	}

	public function createDirectory($name) {
		throw new Forbidden('Not allowed to create folders in the trashbin');
	}

	public function getChild($name) {
		if ($name === 'restore') {
//			return new RestoreFolder();
		}
		if ($name === 'trash') {
//			return new TrashRoot($this->user, $this->trashManager);
		}

		throw new NotFound();
	}

	public function getChildren(): array {
        return $entries = $this->projectsManager->listProjects($this->user);

        $children = array_map(function (ProjectItem $entry) {
            if ($entry->getType() === FileInfo::TYPE_FOLDER) {
                return new ProjectFolder($this->projectsManager, $entry);
            }
        }, $entries);

        return $children;
	}

	public function childExists($name): bool {
		return $name === 'restore' || $name === 'trash';
	}

	public function getLastModified(): int {
		return 0;
	}
}

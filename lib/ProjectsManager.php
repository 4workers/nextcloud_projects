<?php
declare(strict_types=1);

namespace OCA\Projects;

use OCP\Files\Storage\IStorage;
use OCP\IUser;

class ProjectsManager implements ProjectsBackend {
	/** @var ProjectsBackend[] */
	private $backends = [];

	public function registerBackend(string $storageType, ProjectsBackend $backend) {
		$this->backends[$storageType] = $backend;
	}

	/**
	 * @return ProjectsBackend[]
	 */
	private function getBackends(): array {
		return $this->backends;
	}

	public function listProjects(IUser $user): array {
		$items = array_reduce($this->getBackends(), function (array $items, ProjectsBackend $backend) use ($user) {
			return array_merge($items, $backend->listProjects($user));
		}, []);
		return $items;
	}

	private function getBackendForItem(ProjectItem $item) {
		return $item->getProjectBackend();
	}

	public function listProjectFolder(ProjectItem $folder): array {
		return $this->getBackendForItem($folder)->listProjectFolder($folder);
	}

	public function removeItem(ProjectItem $item) {
		$this->getBackendForItem($item)->removeItem($item);
	}

	/**
	 * @param IStorage $storage
	 * @return ITrashBackend
	 * @throws BackendNotFoundException
	 */
	public function getBackendForStorage(IStorage $storage): ProjectsBackend {
		$fullType = get_class($storage);
		$foundType = array_reduce(array_keys($this->backends), function ($type, $registeredType) use ($storage) {
			if (
				$storage->instanceOfStorage($registeredType) &&
				($type === '' || is_subclass_of($registeredType, $type))
			) {
				return $registeredType;
			} else {
				return $type;
			}
		}, '');
		if ($foundType === '') {
			throw new BackendNotFoundException("Projects backend for $fullType not found");
		} else {
			return $this->backends[$foundType];
		}
	}

	public function getProjectNodeById(IUser $user, int $fileId) {
		foreach ($this->backends as $backend) {
			$item = $backend->getProjectNodeById($user, $fileId);
			if ($item !== null) {
				return $item;
			}
		}
		return null;
	}

}

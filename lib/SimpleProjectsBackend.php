<?php
declare(strict_types=1);

namespace OCA\Projects;

use OC\Files\Filesystem;
use OC\Files\SimpleFS\SimpleFolder;
use OC\Files\View;
use OCA\Files_Trashbin\Helper;
use OCA\Files_Trashbin\Storage;
use OCA\Files_Trashbin\Trashbin;
use OCA\Projects\Sabre\ProjectSymlink;
use OCP\Files\IAppData;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\Storage\IStorage;
use OCP\IUser;

class SimpleProjectsBackend implements ProjectsBackend
{

    /**
     * @var IAppData 
     */
    private $appData;
    /**
     * @var IRootFolder
     */
    private $rootFolder;

    public function __construct(IAppData $appData, IRootFolder $rootFolder)
    {
        $this->appData = $appData;
        $this->rootFolder = $rootFolder;
    }

    /**
     * @param  array $items
     * @param  IUser $user
     * @return ProjectSymlink[]
     */
    private function mapToProjectFolder(array $items, IUser $user): array
    {
        return array_map(
            function (SimpleFolder $file) use ($user) {
                $folder = $this->rootFolder->getUserFolder($user->getUID())->getById('569');
                return new ProjectSymlink($file, $folder[0]);
            }, $items
        );
    }

    public function listProjects(IUser $user): array
    {
        $entries = $this->appData->getDirectoryListing('/projects/' . $user->getUID());
        return $this->mapToProjectFolder($entries, $user);

    }

    public function listTrashFolder(ITrashItem $folder): array
    {
        $user = $folder->getUser();
        $entries = Helper::getTrashFiles($folder->getTrashPath(), $user->getUID());
        return $this->mapToProjectFolder($entries, $user, $folder);
    }

    public function restoreItem(ITrashItem $item)
    {
        Trashbin::restore($item->getTrashPath(), $item->getName(), $item->isRootItem() ? $item->getDeletedTime() : null);
    }

    public function removeItem(ITrashItem $item)
    {
        $user = $item->getUser();
        if ($item->isRootItem()) {
            $path = substr($item->getTrashPath(), 0, -strlen('.d' . $item->getDeletedTime()));
            Trashbin::delete($path, $user->getUID(), $item->getDeletedTime());
        } else {
            Trashbin::delete($item->getTrashPath(), $user->getUID(), null);
        }

    }

    public function moveToTrash(IStorage $storage, string $internalPath): bool
    {
        if (!$storage instanceof Storage) {
            return false;
        }
        $normalized = Filesystem::normalizePath($storage->getMountPoint() . '/' . $internalPath, true, false, true);
        $view = Filesystem::getView();
        if (!isset($this->deletedFiles[$normalized]) && $view instanceof View) {
            $this->deletedFiles[$normalized] = $normalized;
            if ($filesPath = $view->getRelativePath($normalized)) {
                $filesPath = trim($filesPath, '/');
                $result = \OCA\Files_Trashbin\Trashbin::move2trash($filesPath);
            } else {
                $result = false;
            }
            unset($this->deletedFiles[$normalized]);
        } else {
            $result = false;
        }

        return $result;
    }

    public function getProjectNodeById(IUser $user, int $fileId)
    {
        try {
            $userFolder = $this->rootFolder->getUserFolder($user->getUID());
            $trash = $userFolder->getParent()->get('files_trashbin/files');
            $trashFiles = $trash->getById($fileId);
            if (!$trashFiles) {
                return null;
            }
            return $trashFiles ? array_pop($trashFiles) : null;
        } catch (NotFoundException $e) {
            return null;
        }
    }
}

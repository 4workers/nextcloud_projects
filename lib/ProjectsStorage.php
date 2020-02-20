<?php

declare(strict_types=1);

namespace OCA\Projects;


use DomainException;
use OCA\Projects\Database\ProjectRootLink;
use OCA\Projects\Database\ProjectRootLinkMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Files\FileInfo;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\IUser;
use OCP\Lock\ILockingProvider;

class ProjectsStorage
{

    /**
     * @var ProjectRootLinkMapper
     */
    private $projectsRootLinksMapper;
    /**
     * @var IRootFolder
     */
    private $rootFolder;

    public function __construct(ProjectRootLinkMapper $projectsRootLinksMapper, IRootFolder $rootFolder)
    {
        $this->projectsRootLinksMapper = $projectsRootLinksMapper;
        $this->rootFolder = $rootFolder;
    }

    public function root(string $uid): FileInfo
    {
        $root = null;
        try {
            $link = $this->projectsRootLinksMapper->findByUser($uid);
            $nodes = $this->rootFolder->getUserFolder($uid)->getById($link->getNodeId());
            if (count($nodes) > 1) {
                throw new DomainException('Projects root can be only one');
            }
            if (!count($nodes)) {
                $root = $this->createProjectRoot($uid);
            } else {
                $root = $nodes[0];
            }
        } catch (DoesNotExistException $e) {
            $root = $this->createProjectRoot($uid);
        }
        return $root;
    }

    public function findProjectByNode(Node $node): ?Node
    {
        $currentNode = $node;
        $foreignId = $this->propertiesStorage->foreignId($currentNode);
        if ($foreignId) {
            return $node;
        }
        try {
            $currentNode = $currentNode->getParent();
        } catch (NotFoundException $e) {
            return null;
        }
        return $this->findProjectByNode($currentNode);
    }

    private function createProjectRoot(string $uid): FileInfo
    {
        //TODO: wrap in transaction
        try {
            $root = $this->rootFolder->getUserFolder($uid)->get(getenv('PROJECTS_ROOT'));
        } catch (NotFoundException $e) {
            $root = $this->rootFolder->getUserFolder($uid)->newFolder(getenv('PROJECTS_ROOT'));
        }
        $uid = $root->getOwner()->getUID();
        try {
            $link = $this->projectsRootLinksMapper->findByUser($uid);
        } catch (DoesNotExistException $e) {
            $link = new ProjectRootLink();
            $link->setOwner($uid);
        }
        $link->setNodeId($root->getId());
        $this->projectsRootLinksMapper->insertOrUpdate($link);
        return $root;
    }

}
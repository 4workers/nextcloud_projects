<?php

declare(strict_types=1);

namespace OCA\Projects;


use DomainException;
use OCA\Projects\Database\ProjectLink;
use OCA\Projects\Database\ProjectLinkMapper;
use OCA\Projects\Database\ProjectRootLink;
use OCA\Projects\Database\ProjectRootLinkMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Files\FileInfo;
use OCP\Files\Folder;
use OCP\Files\InvalidPathException;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

class ProjectsStorage
{

    /**
     * @var ProjectRootLinkMapper
     */
    private $projectRootLinkMapper;
    /**
     * @var IRootFolder
     */
    private $userRootFolder;
    /**
     * @var ProjectLinkMapper
     */
    private $projectLinkMapper;

    public function __construct(
        ProjectRootLinkMapper $projectRootLinkMapper,
        ProjectLinkMapper $projectLinkMapper,
        IRootFolder $userRootFolder)
    {
        $this->projectRootLinkMapper = $projectRootLinkMapper;
        $this->userRootFolder = $userRootFolder;
        $this->projectLinkMapper = $projectLinkMapper;
    }

    public function projectsRoot(string $uid): Folder
    {
        $root = null;
        try {
            $link = $this->projectRootLinkMapper->findByUser($uid);
            $nodes = $this->userRootFolder->getUserFolder($uid)->getById($link->getNodeId());
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

    /**
     * @param int $id
     * @return int
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws NotFoundException
     */
    public function getForeignIdByNodeId(int $id): string
    {
        $link = $this->projectLinkMapper->findByNodeId($id);
        return $link->getForeignId();
    }

    private function createProjectRoot(string $uid): FileInfo
    {
        //TODO: wrap in transaction
        try {
            $root = $this->userRootFolder->getUserFolder($uid)->get(getenv('PROJECTS_ROOT'));
        } catch (NotFoundException $e) {
            $root = $this->userRootFolder->getUserFolder($uid)->newFolder(getenv('PROJECTS_ROOT'));
        }
        $uid = $root->getOwner()->getUID();
        try {
            $link = $this->projectRootLinkMapper->findByUser($uid);
            $link->setNodeId($root->getId());
            $this->projectRootLinkMapper->update($link);
        } catch (DoesNotExistException $e) {
            $link = new ProjectRootLink();
            $link->setOwner($uid);
            $link->setNodeId($root->getId());
            $this->projectRootLinkMapper->insert($link);
        }
        return $root;
    }

    public function allUserProjects(string $uid)
    {
        return array_map(function (ProjectLink $link) {
            return $this->getNodeById($link->getNodeId());
        }, $this->projectLinkMapper->findByUser($uid));
    }

    public function createProject(string $uid, string $name, string $foreignId): Folder
    {
        $root = $this->projectsRoot($uid);
        $projectNode = $root->newFolder($name);
        $link = new ProjectLink();
        $link->setRootId($root->getId());
        $link->setOwner($projectNode->getOwner()->getUID());
        $link->setNodeId($projectNode->getId());
        $link->setForeignId($foreignId);
        $this->projectLinkMapper->insert($link);
        return $projectNode;
    }

    private function getNodeById($id)
    {
        //TODO: what to do if there is to nodes with the same id?
        $nodes = $this->userRootFolder->getById($id);
        $projectNode = $nodes[0];
        return $projectNode;
    }

    public function unlink(Node $node): void
    {
        //TODO: what if link not exists
        $this->projectLinkMapper->deleteByNodeId($node->getId());
    }

    /**
     * If node belongs to a project return
     * @param Node $node
     */
    public function getProjectByNode(Node $node): Folder
    {
        try {
            $nodeId = $node->getId();
            if (is_null($nodeId)) throw new NotFoundException('Node is outside of a project');
            $link = $this->projectLinkMapper->findByNodeId($nodeId);
            return $this->getNodeById($link->getNodeId());
        } catch (DoesNotExistException $e) {
            $parent = $node->getParent();
        }
        return $this->getProjectByNode($parent);
    }

    public function isProject(Node $node)
    {
        $link = $this->projectLinkMapper->findByNodeId($node->getId());
        //TODO: What if node not exists
        return !!$link;
    }

}
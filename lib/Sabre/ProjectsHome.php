<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use OCA\Projects\ProjectsManager;
use OCP\IUser;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\ICollection;

class ProjectsHome implements ICollection
{

    /**
     * @var array
     */
    private $principalInfo;

    /**
     * @var IUser
     */
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

    public function delete()
    {
        throw new Forbidden();
    }

    public function getName(): string
    {
        list(, $name) = \Sabre\Uri\split($this->principalInfo['uri']);
        return $name;
    }

    public function setName($name)
    {
        throw new Forbidden('Permission denied to rename the projects root');
    }

    public function createFile($name, $data = null)
    {
        throw new Forbidden('Not allowed to create files in the projects root');
    }

    public function createDirectory($name)
    {
        throw new Forbidden('Not allowed to create folders in the projects root');
    }

    public function getChild($name)
    {
        throw new NotFound();
    }

    public function getChildren(): array
    {
        return $this->projectsManager->listProjects($this->user);
    }

    public function childExists($name): bool
    {
        throw new NotImplemented();
    }

    public function getLastModified(): int
    {
        return 0;
    }
}

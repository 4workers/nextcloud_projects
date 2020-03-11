<?php
declare(strict_types=1);


namespace OCA\Projects;


use OCA\Projects\Exception\InvalidConfigException;

class ProjectsRootPath
{
    /**
     * @var array|false|string
     */
    private $path;
    /**
     * @var array
     */
    private $appInfo;

    /**
     * ProjectsRootPath constructor.
     * @param array|false|string $path
     * @param array $appInfo
     */
    public function __construct(string $path, array $appInfo)
    {
        $this->path = $path;
        $this->appInfo = $appInfo;
    }

    public function __toString(): string
    {
        if ($this->path) return $this->path;
        $projectsRootPath = $this->appInfo['squeegee']['projects']['root'];
        if (!$projectsRootPath) {
            throw new InvalidConfigException('Default projects root path not defined. Add it to appinfo.xml.');
        }
    }

}
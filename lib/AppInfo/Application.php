<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use OCA\Projects\ProjectsManager;
use OCA\Projects\SimpleProjectsBackend;
use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IInitialStateService;
use OCA\DAV\CalDAV\Proxy\ProxyMapper;
use OCA\DAV\Connector\Sabre\Principal;

class Application extends App
{

    public function __construct()
    {
        parent::__construct('projects');

        $container = $this->getContainer();

        $container->registerService(
            'principalBackend', function () {
                return new Principal(
                    \OC::$server->getUserManager(),
                    \OC::$server->getGroupManager(),
                    \OC::$server->getShareManager(),
                    \OC::$server->getUserSession(),
                    \OC::$server->getAppManager(),
                    \OC::$server->query(ProxyMapper::class),
                    \OC::$server->getConfig()
                );
            }
        );

        $server = $container->getServer();

        $projectsManager = $server->query(ProjectsManager::class);
        $projectsBackend = $server->query(SimpleProjectsBackend::class);
        $projectsManager->registerBackend(OCP\Files\Storage\IStorage::class, $projectsBackend);

    }

    public function register()
    {
        //        /* @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $this->getContainer()->query(IEventDispatcher::class);
        $eventDispatcher->addListener(
            'OCA\Files::loadAdditionalScripts', function () {
                script('projects', 'filelist_plugin');
                style('projects', 'filelist');
                /* @var IInitialStateService $state */
                $state = $this->getContainer()->query(IInitialStateService::class);
                $state->provideInitialState('projects', 'project-icon', image_path('projects', 'folder.svg'));
            }
        );
    }

}

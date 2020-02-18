<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use OC;
use OCA\Projects\Hooks;
use OCA\Projects\ProjectsManager;
use OCA\Projects\ProjectStorage;
use OCA\Projects\PropertiesStorage;
use OCA\Projects\SimpleProjectsBackend;
use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IContainer;
use OCP\IDBConnection;
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
            PropertiesStorage::class, function ($c) {
            return new PropertiesStorage($c->query(IDBConnection::class));
        }
        );

        $container->registerService(
            ProjectStorage::class, function ($c) {
            return new ProjectStorage($c->query(PropertiesStorage::class));
        }
        );

        $container->registerService(
            'principalBackend', function () {
                return new Principal(
                    OC::$server->getUserManager(),
                    OC::$server->getGroupManager(),
                    OC::$server->getShareManager(),
                    OC::$server->getUserSession(),
                    OC::$server->getAppManager(),
                    OC::$server->query(ProxyMapper::class),
                    OC::$server->getConfig()
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
        /* @var IEventDispatcher $eventDispatcher */
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
        $eventDispatcher->addListener('OCP\Share::preShare', [Hooks::class, 'preShare']);
    }

}

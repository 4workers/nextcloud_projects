<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use GuzzleHttp\Client;
use OC;
use OCA\DAV\CalDAV\Proxy\ProxyMapper;
use OCA\DAV\Connector\Sabre\Principal;
use OCA\Projects\Connector\Connector;
use OCA\Projects\Database\ProjectRootLinkMapper;
use OCA\Projects\DefaultProjectsBackend;
use OCA\Projects\Hooks;
use OCA\Projects\ProjectsManager;
use OCA\Projects\ProjectsStorage;
use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IDBConnection;

class Application extends App
{

    public function __construct()
    {
        parent::__construct('projects');

        $container = $this->getContainer();

        $container->registerService(
            ProjectRootLinkMapper::class, function ($c) {
                return new ProjectRootLinkMapper($c->query(IDBConnection::class));
            }
        );

        $container->registerService(
            ProjectStorage::class, function ($c) {
                $rootFolder = $c->query('ServerContainer')->getRootFolder();
                return new ProjectsStorage(
                    $c->query(ProjectRootLinkMapper::class),
                    $c->query(ProjectLinkMapper::class),
                    $rootFolder
                );
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

        $container->registerService(
            Connector::class, function ($c) {
            return new Connector(new Client(['base_uri' => getenv('WURTH_CONNECTOR_URL')]));
        }
        );

        $server = $container->getServer();

        $projectsManager = $server->query(ProjectsManager::class);
        $projectsBackend = $server->query(DefaultProjectsBackend::class);
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
            }
        );
        $eventDispatcher->addListener('OCP\Share::preShare', [Hooks::class, 'preShare']);
        $eventDispatcher->addListener('\OCP\Files::preDelete', [Hooks::class, 'preDelete']);
        $eventDispatcher->addListener('\OCP\Files::postDelete', [Hooks::class, 'postDelete']);
        $eventDispatcher->addListener('\OCP\Files::preRename', [Hooks::class, 'preRename']);

        $eventDispatcher = $this->getContainer()->query(IEventDispatcher::class);
        $eventDispatcher->addListener('\OCP\Files::postCreate', [\OCA\Projects\Connector\Hooks::class, 'postCreateFile']);
    }

}

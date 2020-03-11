<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use GuzzleHttp\Client;
use OC;
use OCA\DAV\CalDAV\Proxy\ProxyMapper;
use OCA\DAV\Connector\Sabre\Principal;
use OCA\Projects\Connector\Connector;
use OCA\Projects\Connector\Hooks;
use OCA\Projects\Database\ProjectLinkMapper;
use OCA\Projects\Database\ProjectRootLinkMapper;
use OCA\Projects\DefaultProjectsBackend;
use OCA\Projects\FileHooks;
use OCA\Projects\ProjectsManager;
use OCA\Projects\ProjectsRootPath;
use OCA\Projects\ProjectsStorage;
use OCA\Projects\UserHooks;
use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IDBConnection;
use OCP\User\Events\UserCreatedEvent;

class Application extends App
{

    public function __construct()
    {
        parent::__construct('squeegee');

        $container = $this->getContainer();

        $container->registerService(
            ProjectsRootPath::class, function ($c) {
            $server = $c->getServer();
            $appManager = $server->getAppManager();
            $appInfo = $appManager->getAppInfo('squeegee');
            return new ProjectsRootPath(getenv('SQUEEGEE_PROJECTS_ROOT'), $appInfo);
        }
        );

        $container->registerService(
            ProjectRootLinkMapper::class, function ($c) {
                return new ProjectRootLinkMapper($c->query(IDBConnection::class));
            }
        );

        $container->registerService(
            ProjectsStorage::class, function ($c) {
                $rootFolder = $c->query('ServerContainer')->getRootFolder();
                return new ProjectsStorage(
                    $c->query(ProjectRootLinkMapper::class),
                    $c->query(ProjectLinkMapper::class),
                    $rootFolder,
                    $c->query(ProjectsRootPath::class),
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
            return new Connector(new Client(['base_uri' => getenv('SQUEEGEE_CONNECTOR_URL')]));
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
//                TODO:Load app name from app info
                script('squeegee', 'filelist_plugin');
                style('squeegee', 'filelist');
            }
        );
        $eventDispatcher->addListener('OCP\Share::preShare', [FileHooks::class, 'preShare']);
        $eventDispatcher->addListener('\OCP\Files::preDelete', [FileHooks::class, 'preDelete']);
        $eventDispatcher->addListener('\OCP\Files::postDelete', [FileHooks::class, 'postDelete']);
        $eventDispatcher->addListener('\OCP\Files::preRename', [FileHooks::class, 'preRename']);
        $eventDispatcher->addListener(UserCreatedEvent::class, [UserHooks::class, 'postCreate']);

        $eventDispatcher = $this->getContainer()->query(IEventDispatcher::class);
        $eventDispatcher->addListener('\OCP\Files::postCreate', [Hooks::class, 'postCreateFile']);
    }

}

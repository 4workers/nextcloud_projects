<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IInitialStateService;

class Application extends App
{

    public function __construct()
    {
        parent::__construct('projects');
    }

    public function register()
    {
//        /* @var IEventDispatcher $eventDispatcher */
        $eventDispatcher = $this->getContainer()->query(IEventDispatcher::class);
        $eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
            script('projects', 'filelist_plugin');
            style('projects', 'filelist');
            /* @var IInitialStateService $state */
            $state = $this->getContainer()->query(IInitialStateService::class);
            $state->provideInitialState('projects', 'project-icon', image_path('projects', 'folder.svg'));
        });
    }

}

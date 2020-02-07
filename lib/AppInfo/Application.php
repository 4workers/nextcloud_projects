<?php

declare(strict_types=1);

namespace OCA\Projects\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;

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
        });
    }

}

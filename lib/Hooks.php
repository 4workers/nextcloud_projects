<?php

declare(strict_types=1);

namespace OCA\Projects;

use OC;
use OC\Files\Filesystem;
use OCP\EventDispatcher\GenericEvent;
use OCP\Share\IShare;
use Throwable;
use OC\HintException;

class Hooks
{

    public static function preShare($event)
    {
        try {
            /**
 * @var IShare $share 
*/
            /**
 * @var GenericEvent $event 
*/
            $share = $event->getSubject();
            $storage = OC::$server->query(ProjectStorage::class);
            /**
 * @var ProjectStorage $storage 
*/
            $projectsRoot = $storage->root();
            $target = $projectsRoot->getPath() . '/' . $share->getTarget();
            $target = Filesystem::normalizePath($target);
            $share->setTarget($target);
        } catch (Throwable $e) {
            //We should catch all exceptions and throw uncatchable exception
            throw new HintException($e);
        }
    }

}
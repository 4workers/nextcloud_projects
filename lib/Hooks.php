<?php

declare(strict_types=1);

namespace OCA\Projects;

use OC;
use OC\Files\Filesystem;
use OCP\Files\NotFoundException;
use Throwable;
use OC\HintException;

class Hooks
{

    public static function preShare($event)
    {
        try {
            // @var IShare $share
            // @var GenericEvent $event
            $share = $event->getSubject();
            $storage = OC::$server->query(ProjectsStorage::class);
            $node = $share->getNode();
            try {
                $storage->find($node->getId());
            } catch (NotFoundException $e) {
                //Don't change target if node is not project
                return;
            }
            // @var ProjectStorage $storage
            $projectsRoot = $storage->root($share->getSharedWith());
            $userFolder = OC::$server->getUserFolder($share->getSharedWith());
            $target = $userFolder->getRelativePath($projectsRoot->getPath()) . '/' . $share->getTarget();
            $target = Filesystem::normalizePath($target);
            $share->setTarget($target);
        } catch (Throwable $e) {
            //We should catch all exceptions and throw uncatchable exception
            throw new HintException($e);
        }
    }

}
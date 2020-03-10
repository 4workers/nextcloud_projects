<?php

declare(strict_types=1);

namespace OCA\Projects;

use OC;
use OCA\Projects\Exception\ProjectsRootAlreadyExistsException;
use OCP\User\Events\UserCreatedEvent;
use Throwable;
use OC\HintException;

class UserHooks
{

    public static function postCreate(UserCreatedEvent $event)
    {
        try {
            $uid = $event->getUid();
            /** @var ProjectsStorage $storage */
            $storage = OC::$server->query(ProjectsStorage::class);
            $storage->createProjectsRoot($uid);
        } catch (ProjectsRootAlreadyExistsException $e) {
            //This event calls two times for some reason by nextcloud
            //so we ignore second one
        } catch (Throwable $e) {
            //We should catch all exceptions and throw uncatchable exception
            throw new HintException($e);
        }
    }

}
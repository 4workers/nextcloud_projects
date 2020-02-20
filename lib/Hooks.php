<?php

declare(strict_types=1);

namespace OCA\Projects;

use OC;
use OC\Files\Filesystem;
use OCP\EventDispatcher\GenericEvent;
use OCP\Files\ForbiddenException;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use Throwable;
use OC\HintException;

class Hooks
{

    public static function preShare($event)
    {
        try {
            /** @var IShare $share */
            /** @var GenericEvent $event */
            $share = $event->getSubject();
            $storage = OC::$server->query(ProjectsStorage::class);
            $node = $share->getNode();
            try {
                $storage->find($node->getId());
            } catch (NotFoundException $e) {
                //Don't change target if node is not project
                return;
            }
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

    public static function preDelete($event)
    {
        try {
            /** @var GenericEvent $event */
            $subjects = $event->getSubject();
            $subjects = is_array($subjects) ? $subjects : [$subjects];
            $storage = OC::$server->query(ProjectsStorage::class);
            foreach ($subjects as $subject) {
                /** @var Node $subject */
                if ($subject->getType() !== Node::TYPE_FOLDER) continue;
                $projectsRoot = $storage->root($subject->getOwner()->getUID());
                if (static::equalOrContains($subject, $projectsRoot)) {
                    throw new ForbiddenException('Project root can\'t be deleted');
                }
            }
        } catch (Throwable $e) {
            //Only this exception uncatchable. nextcloud ignore any other exception
            throw new HintException($e);
        }
    }

    private static function equalOrContains(OC\Files\Node\Folder $node, OC\Files\Node\Folder $subNode)
    {
        if ($node->getId() === $subNode->getId()) return true;
        if ($node->isSubNode($subNode)) return true;
        return false;
    }
}
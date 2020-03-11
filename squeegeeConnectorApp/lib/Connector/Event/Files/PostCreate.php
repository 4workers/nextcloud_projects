<?php

declare(strict_types=1);

namespace OCA\Projects\Connector\Event\Files;

use OCA\Projects\Connector\Event\General as GeneralEvent;
use OCA\Projects\ProjectsStorage;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

class PostCreate extends GeneralEvent
{

    public static function create(Node $node, ProjectsStorage $storage)
    {
        try {
            $projectNode = $storage->getProjectByNode($node);
        } catch (NotFoundException $e) {
            return new static('', []);
        }
        return new static(
            'nodeAdded', [
            'user' => $node->getOwner()->getUID(),
            'id' => $node->getId(),
            'name' => $node->getName(),
            'type' => $node->getType(),
            'project_id' => $projectNode->getId()
            ]
        );
    }
}
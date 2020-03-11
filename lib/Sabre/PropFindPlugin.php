<?php

declare(strict_types=1);

namespace OCA\Projects\Sabre;

use OCA\Projects\ProjectsStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use Sabre\DAV\INode;
use Sabre\DAV\PropFind;
use Sabre\DAV\Server;
use Sabre\DAV\ServerPlugin;
use OCA\DAV\Connector\Sabre\Directory;

class PropFindPlugin extends ServerPlugin
{

    //TODO: move to appinfo.xml
    const PROPERTY_FOREIGN_ID = '{https://squeegee.com/ns}foreign-id';
    const PROPERTY_IS_PROJECT = '{https://squeegee.com/ns}is-project';

    /**
     * @var Server
     */
    private $server;
    /**
     * @var ProjectsStorage
     */
    private $projectsStorage;

    public function __construct(
        ProjectsStorage $projectsStorage
    ) {
        $this->projectsStorage = $projectsStorage;
    }

    public function initialize(Server $server)
    {
        $this->server = $server;

        $this->server->on('propFind', [$this, 'propFind']);
    }


    public function propFind(PropFind $propFind, INode $node)
    {
        if (!($node instanceof Directory)) { return;
        }
        try {
            $foreignId = $this->projectsStorage->getForeignIdByNodeId((int)$node->getFileId());
        } catch (DoesNotExistException $e) {
            return;
        }

        $propFind->handle(
            self::PROPERTY_FOREIGN_ID, function () use ($foreignId) {
                return $foreignId;
            }
        );
        $propFind->handle(
            self::PROPERTY_IS_PROJECT, function () use ($foreignId) {
                return true;
            }
        );
    }

}

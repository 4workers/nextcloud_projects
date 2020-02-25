<?php
declare(strict_types=1);

namespace OCA\Projects\Database;

use OCP\AppFramework\Db\Entity;

class ProjectLink extends Entity
{

    protected $rootId;
    protected $owner;
    protected $nodeId;
    protected $foreignId;

    public function __construct()
    {
        $this->addType('nodeId', 'integer');
        $this->addType('rootId', 'integer');
    }
}
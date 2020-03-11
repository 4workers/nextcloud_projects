<?php
declare(strict_types=1);

namespace OCA\Projects\Database;

use OCP\AppFramework\Db\Entity;

class ProjectRootLink extends Entity
{

    protected $owner;
    protected $nodeId;

    public function __construct()
    {
        $this->addType('nodeId', 'integer');
    }
}
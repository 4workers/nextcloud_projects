<?php

declare(strict_types=1);

namespace OCA\Projects;


use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\IDBConnection;

class PropertiesStorage
{

    private $connection;

    public function __construct(IDBConnection $connection)
    {
        $this->connection = $connection;
    }

    public function foreignId(Node $node): ?string
    {
        $path = $node->getPath();

        $sql = 'SELECT * FROM `*PREFIX*properties` WHERE `userid` = ? AND `propertypath` = ?';
        //TODO: move property name to config
        $sql .= ' AND `propertyname` = "{http://owncloud.org/ns}foreign-id"';

        try {
            $owner = $node->getOwner();
            $ownerId = $owner->getUID();
        } catch (NotFoundException $e) {
            return null;
        }

        $whereValues = [$ownerId, $path];
        $whereTypes = [null, null];

        $result = $this->connection->executeQuery(
            $sql,
            $whereValues,
            $whereTypes
        );

        $props = [];
        while ($row = $result->fetch()) {
            $props[$row['propertyname']] = $row['propertyvalue'];
        }

        $result->closeCursor();

        return $props['{http://owncloud.org/ns}foreign-id'] ?? null;
    }

}
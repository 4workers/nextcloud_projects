<?php
declare(strict_types=1);

namespace OCA\Projects\Database;

use OCP\AppFramework\Db\Entity;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;
use OCP\IUser;

class ProjectLinkMapper extends QBMapper {

    private $table = 'projects_links';

    public function __construct(IDBConnection $db) {
        parent::__construct($db, $this->table);
    }


    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find(int $id) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->table)
            ->where(
                $qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
            );

        return $this->findEntity($qb);
    }


    public function findAll($limit=null, $offset=null) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->table)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $this->findEntities($qb);
    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function findByNodeId(int $nodeId): Entity
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->table)
            ->where(
                $qb->expr()->eq('node_id', $qb->createNamedParameter($nodeId, IQueryBuilder::PARAM_STR))
            );

        return $this->findEntity($qb);
    }

    public function findByUser(string $uid, $limit=null, $offset=null)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->table)
            ->where(
                $qb->expr()->eq('owner', $qb->createNamedParameter($uid, IQueryBuilder::PARAM_STR))
            )
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $this->findEntities($qb);
    }

}

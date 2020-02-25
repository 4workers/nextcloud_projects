<?php

declare(strict_types=1);

namespace OCA\Projects\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1Date20200218123214 extends SimpleMigrationStep
{

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array   $options
     */
    public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options)
    {
    }

    /**
     * @param  IOutput $output
     * @param  Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param  array   $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
    {
        /**
    * @var ISchemaWrapper $schema 
*/
        //TODO: add foreign key constrains
        $schema = $schemaClosure();

        if (!$schema->hasTable('projects_roots_links')) {
            $table = $schema->createTable('projects_roots_links');
            $table->addColumn(
                'id', 'bigint', [
                'autoincrement' => true,
                'notnull' => true,
                'length' => 5,
                ]
            );
            $table->addColumn(
                'owner', 'string', [
                'notnull' => true,
                'length' => 64,
                ]
            );
            $table->addColumn(
                'node_id', 'bigint', [
                'notnull' => true,
                'length' => 11,
                ]
            );
            $table->setPrimaryKey(['id']);
            $table->addIndex(['node_id'], 'node_id_index');
            $table->addUniqueIndex(['owner'], 'owner_index');
        }

        if (!$schema->hasTable('projects_links')) {
            $table = $schema->createTable('projects_links');
            $table->addColumn(
                'id', 'bigint', [
                'autoincrement' => true,
                'notnull' => true,
                'length' => 5,
                ]
            );
            $table->addColumn(
                'owner', 'string', [
                'notnull' => true,
                'length' => 64,
                ]
            );
            $table->addColumn(
                'root_id', 'integer', [
                'notnull' => true,
                'length' => 4,
                ]
            );
            $table->addColumn(
                'node_id', 'bigint', [
                'notnull' => true,
                'length' => 11,
                ]
            );
            $table->addColumn(
                'foreign_id', 'string', [
                'notnull' => true,
                'length' => 64,
                ]
            );
            $table->setPrimaryKey(['id']);
            $table->addIndex(['root_id'], 'root_id_index');
            $table->addIndex(['node_id'], 'node_id_index');
        }
        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array   $options
     */
    public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options)
    {
    }
}

<?php

namespace AegisFang\Database\Table;

use AegisFang\Database\Connection;
use AegisFang\Database\Query;
use AegisFang\Log\Logger;
use Exception;
use PDO;
use PDOException;

/**
 * Class Builder
 * @package AegisFang\Database\Table
 */
class Builder
{
    protected PDO $pdo;
    protected string $dbName;
    protected string $table;
    protected string $id;
    protected array $columns;
    protected array $relationships;
    protected string $statement;
    protected const CREATETABLE = 'CREATE TABLE IF NOT EXISTS';
    protected const DROPTABLE = 'DROP TABLE';
    protected const PRIMARYKEY = 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';

    /**
     * Builder constructor.
     *
     * @param $table
     * @param Blueprint $blueprint
     */
    public function __construct($table, Blueprint $blueprint)
    {
        $config = require getenv('APP_CONFIG');

        /**
         * @var Connection $connection
         */
        $connection = new $config['db_driver']();

        $this->pdo = $connection->get();
        $this->dbName = $connection->getName();
        $this->table = $table;
        $this->id = $blueprint->id ?: 'id';
        $this->columns = $blueprint->columns;
        $this->relationships = $blueprint->relationships;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        if ($this->tableExists()) {
            return false;
        }

        $this->statement = self::CREATETABLE . " `{$this->table}` (";
        $this->setColumns();
        $this->closeTable();

        return $this->execute();
    }

    /**
     * Create foreign key relationships.
     */
    public function createRelationships(): void
    {
        $i = 0;
        foreach ($this->relationships as $relationship) {
            $this->statement = 'ALTER TABLE ' . $relationship[2] . ' ADD';
            $this->statement .= ' KEY ' .
                                $relationship[0] . '_' . $relationship[1] . '_' . $i . '(' . $relationship[0] . ');';

            $this->execute();

            $i++;
        }

        foreach ($this->relationships as $relationship) {
            $this->statement = 'ALTER TABLE ' . $this->table . ' ADD FOREIGN KEY(' . $relationship[0]
                               . ') REFERENCES ' . $relationship[2] . '(' . $relationship[1] . ');';

            $this->execute();
        }
    }

    public function tableExists(): bool
    {
        $query = new Query();
        $query->select('*')
            ->from('information_schema.tables')
            ->where('table_schema', $this->dbName)
            ->where('table_name', $this->table)
            ->limit(1);

        return (bool) $query->execute()->fetch();
    }

    /**
     * @return void
     */
    public function setColumns(): void
    {
        $i = 0;
        $len = count($this->columns);
        $this->statement .= "{$this->id} " . self::PRIMARYKEY . ', ';
        foreach ($this->columns as $column => $options) {
            $this->statement .= "{$column} ";
            foreach ($options as $option) {
                $this->statement .= " {$option}";
            }

            if ($i !== $len - 1) {
                $this->statement .= ', ';
            }
            $i++;
        }
    }

    /**
     * @return void
     */
    public function closeTable(): void
    {
        $this->statement .= ')';
    }

    /**
     * @param $table
     *
     * @return bool
     */
    public static function destroy($table): bool
    {
        $drop = new self($table, new Blueprint());
        $drop->statement(self::DROPTABLE . " {$table}");

        return $drop->execute();
    }

    /**
     * @param $statement
     *
     * @return void
     */
    protected function statement($statement): void
    {
        $this->statement = $statement;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $statement = $this->pdo->prepare($this->statement);

        Logger::getLogger()->debug(
            'Last query',
            ['Query' => $statement]
        );

        $statement->execute();

        return true;
    }
}

<?php

namespace AegisFang\Database\Table;

use AegisFang\Database\Connection;
use PDO;
use PDOException;

/**
 * Class Builder
 * @package AegisFang\Database\Table
 */
class Builder
{
    protected PDO $pdo;
    protected string $table;
    protected string $id;
    protected array $columns;
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
        $connection = new Connection();
        $this->pdo = $connection->get();
        $this->table = $table;
        $this->id = $blueprint->id ?: 'id';
        $this->columns = $blueprint->columns;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        // try to select 1 from tablename limit 1. if no error it exists, need to fail.
        $this->statement = self::CREATETABLE . " `{$this->table}` (\r\n";
        $this->setColumns();
        $this->closeTable();

        return $this->execute();
    }

    /**
     * @return void
     */
    public function setColumns(): void
    {
        $i = 0;
        $len = count($this->columns);
        $this->statement .= "{$this->id} " . self::PRIMARYKEY . ",\r\n";
        foreach ($this->columns as $column => $options) {
            $this->statement .= "{$column} ";
            foreach ($options as $option) {
                $this->statement .= " {$option}";
            }

            if ($i !== $len - 1) {
                $this->statement .= ",\r\n";
            }
            $i++;
        }
    }

    /**
     * @return void
     */
    public function closeTable(): void
    {
        $this->statement .= "\r\n)";
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
        try {
            $result = $statement->execute();
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }
}

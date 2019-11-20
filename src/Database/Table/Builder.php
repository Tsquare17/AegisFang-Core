<?php

namespace AegisFang\Database\Table;

use AegisFang\Database\Connection;

/**
 * Class Builder
 * @package AegisFang\Database\Table
 */
class Builder
{
    protected $pdo;
    protected $table;
    protected $id;
    protected $columns;
    protected $relationships;
    protected $statement;
    protected const CREATETABLE = 'CREATE TABLE IF NOT EXISTS';
    protected const DROPTABLE = 'DROP TABLE';
    protected const PRIMARYKEY = 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';

    public function __construct($table, Blueprint $blueprint)
    {
        $connection = new Connection();
        $this->pdo = $connection->get();
        $this->table = $table;
        $this->id = $blueprint->id ?: 'id';
        $this->columns = $blueprint->columns;
    }

    public function createTable()
    {
        $this->statement = self::CREATETABLE . " `{$this->table}` (\r\n";
        $this->setColumns();
        $this->closeTable();

        return $this->execute();
    }

    public function destroy()
    {
        $this->statement = self::DROPTABLE . " {$this->table}";

        return $this->execute();
    }

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

    public function closeTable()
    {
        $this->statement .= "\r\n)";
    }

    public function execute()
    {
        $statement = $this->pdo->prepare($this->statement);
        // TODO: Check if table exists on execute or destroy
        try {
            $result = $statement->execute();
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }
}

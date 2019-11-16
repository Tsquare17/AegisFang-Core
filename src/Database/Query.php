<?php

namespace AegisFang\Database;

use PDO;

/**
 * Class Query
 * @package AegisFang\Database
 */
class Query
{
    protected $pdo;
    protected $select;
    protected $columns;
    protected $from;
    protected $where;
    protected $having;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function select($columns = ['*']): Query
    {
        $this->select = 'SELECT ';
        $count = count($columns);
        foreach ($columns as $i => $column) {
            $this->columns .= $column;
            if ($i !== $count - 1) {
                $this->columns .= ', ';
            } else {
                $this->columns .= ' ';
            }
        }

        return $this;
    }

    public function from($table): Query
    {
        $this->from = $table;

        return $this;
    }

    public function where($column): Query
    {
        $this->where = $column;

        return $this;
    }

    public function having($value): Query
    {
        $this->having = $value;

        return $this;
    }

    public function run()
    {
        $query = $this->select;
        $query .= $this->columns;
        $query .= $this->from;
        $query .= $this->where;
        $query .= $this->having;

        $statement = $this->pdo->prepare($query);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }
}

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

    /**
     * Query constructor.
     *
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function select($columns = ['*']): self
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

    /**
     * @param $table
     *
     * @return $this
     */
    public function from($table): self
    {
        $this->from = $table;

        return $this;
    }

    /**
     * @param $column
     *
     * @return $this
     */
    public function where($column): self
    {
        $this->where = $column;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function having($value): self
    {
        $this->having = $value;

        return $this;
    }

    /**
     * @return mixed
     */
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

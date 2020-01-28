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
    protected $table;
    protected $command;
    protected array $columns = [];
    protected $values;
    protected $from;
    protected $where;
    protected $having;
    public const SELECT = 'SELECT';
    public const INSERT = 'INSERT INTO';
    public const VALUES = 'VALUES';

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
     * @param $table
     *
     * @return $this
     */
    public function table($table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function select($columns = ['*']): self
    {
        $this->command = self::SELECT;
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

    public function insert($values): self
    {
        $this->command = self::INSERT;

        $this->values = $values;

        return $this;
    }

    public function into($columns): self
    {
        $this->columns[] = $columns;
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

    public function execute()
    {
        if ($this->command === self::SELECT) {
            return $this->runSelect();
        }

        if ($this->command === self::INSERT) {
            return $this->runInsert();
        }
    }

    public function runInsert()
    {
        $query = "{$this->command} `{$this->table}` ({...$this->columns}) VaLUES {...$this->values}";
        var_dump($query);
    }

    /**
     * @return mixed
     */
    public function runSelect()
    {
        $query = $this->command;
        $query .= $this->columns;
        $query .= $this->from;
        $query .= $this->where;
        $query .= $this->having;

        $statement = $this->pdo->prepare($query);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }
}

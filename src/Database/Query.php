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
    protected array $statement = [];
    protected $limit;
    protected $last;
    public const EOL = "\r\n";
    public const SELECT = 'SELECT ';
    public const FROM = 'FROM ';
    public const WHERE = 'WHERE ';
    public const AND = 'AND ';
    public const INSERT = 'INSERT INTO ';
    public const VALUES = 'VALUES ';
    public const LIMIT = 'LIMIT ';

    /**
     * Query constructor.
     *
     * @param $pdo
     */
    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->get();
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
//        $this->command = self::SELECT;
//        $count = count($columns);
//        foreach ($columns as $i => $column) {
//            $this->columns .= $column;
//            if ($i !== $count - 1) {
//                $this->columns .= ', ';
//            } else {
//                $this->columns .= ' ';
//            }
//        }
        $this->command = self::SELECT;
        $this->statement[] = self::SELECT . $columns . self::EOL;
        $this->last = self::SELECT;

        return $this;
    }

    public function insert($columns, $values): self
    {
        $this->command = self::INSERT;
        $this->statement[] = "({...$this->columns}) VALUES {...$this->values}";
        $this->last = self::INSERT;
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function from($table): self
    {
        //$this->from = $table;
        $this->statement[] = self::FROM . $table . self::EOL;
        $this->last = self::FROM;

        return $this;
    }

    /**
     * @param $column
     *
     * @return $this
     */
    public function where($column): self
    {
        // should be something like, append to statement  WHERE {$statement}
        // all of these need to append or push into an array maybe, and then assemble at the end.
        if ($this->last === self::WHERE) {
            $this->statement[] = self::AND . $column . self::EOL;
            $this->last = self::WHERE;

            return $this;
        }
        $this->statement[] = self::WHERE . $column . self::EOL;
        $this->last = self::WHERE;

        //$this->where = $column;

        return $this;
    }

    /**
     * @param $limit
     *
     * @return $this
     */
    public function limit($limit): self
    {
        $this->statement[] = self::LIMIT . $limit;

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
        // $query = "{$this->command} `{$this->table}` ({...$this->columns}) VALUES {...$this->values}";
        $query = $this->pdo->query(...$this->statement);

        return $query->execute();
    }

    /**
     * @return mixed
     */
    public function runSelect()
    {
        $this->statement[] = ';';
        $query = $this->pdo->query(implode($this->statement));

        $result = $query->fetchAll();

        return $result;
    }
}

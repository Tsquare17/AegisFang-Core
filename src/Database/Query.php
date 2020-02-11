<?php

namespace AegisFang\Database;

use PDO;

/**
 * Class Query
 * @package AegisFang\Database
 */
class Query
{
    protected PDO $pdo;
    protected string $table;
    protected string $command;
    protected string $columns;
    protected array $values;
    protected string $from;
    protected string $where;
    protected array $statement = [];
    protected $limit;
    protected $last;
    public const EOL = "\r\n";
    public const SELECT = 'SELECT ';
    public const FROM = 'FROM ';
    public const WHERE = 'WHERE ';
    public const AND = 'AND ';
    public const INSERT = 'INSERT INTO';
    public const VALUES = ' VALUES ';
    public const LIMIT = 'LIMIT ';

    /**
     * Query constructor.
     *
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
     * @param string $columns
     *
     * @return $this
     */
    public function select($columns = '*'): self
    {
        $this->command = self::SELECT;
        $this->statement[] = self::SELECT . $columns . self::EOL;
        $this->last = self::SELECT;

        return $this;
    }

    /**
     * @param $columns
     * @param $values
     *
     * @return $this
     */
    public function insert($columns, $values): self
    {
        $this->command = self::INSERT;
        $this->columns = '(`' . implode('`,`', $columns) . '`)';
        $this->values = $values;
        $this->last = self::INSERT;

        return $this;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function into(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function from(string $table): self
    {
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
        if ($this->last === self::WHERE) {
            $this->statement[] = self::AND . $column . self::EOL;
            $this->last = self::WHERE;

            return $this;
        }
        $this->statement[] = self::WHERE . $column . self::EOL;
        $this->last = self::WHERE;

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

        return false;
    }

    public function runInsert(): bool
    {
        $insert = "{$this->command} `{$this->table}` {$this->columns}" . self::VALUES . $this->getValuePlaceholders();

        return $this->pdo->prepare($insert)->execute($this->values);
    }

    /**
     * Generate the appropriate number of value placeholders.
     *
     * @return string
     */
    private function getValuePlaceholders()
    {
        $values = '(';
        $count = count($this->values);
        for ($i = 0; $i < $count; $i++) {
            $values .= '?';
            if ($i !== $count - 1) {
                $values .= ',';
            }
        }
        $values .= ')';

        return $values;
    }

    /**
     * @return mixed
     */
    public function runSelect()
    {
        $this->statement[] = ';';
        $query = $this->pdo->query(implode($this->statement));

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

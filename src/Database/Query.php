<?php

namespace AegisFang\Database;

use AegisFang\Log\Logger;
use PDO;

/**
 * Class Query
 * @package AegisFang\Database
 */
class Query
{
    protected PDO $pdo;
    protected string $table;
    protected int $fetchMode;
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
        $config = require getenv('APP_CONFIG');

        /**
         * @var Connection $connection
         */
        $connection = new $config['db_driver']();

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
        $columns = $this->formatColumn($columns);

        $this->command = self::SELECT;
        $this->statement[] = self::SELECT . $columns . ' ';
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
        $this->statement[] = self::FROM . $table . ' ';
        $this->last = self::FROM;

        return $this;
    }

    /**
     * @param $column
     *
     * @param $value
     *
     * @return $this
     */
    public function where($column, $value): self
    {
        if ($this->last === self::WHERE) {
            $this->statement[] = self::AND . $column . ' = \'' . $value . '\' ';
            $this->last = self::WHERE;

            return $this;
        }
        $this->statement[] = self::WHERE . $column . ' = \'' . $value . '\' ';
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

        $logger = Logger::getLogger();
        $logger->debug(
            'Last query: ' . $insert,
        );

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
        $query = $this->pdo->query(implode($this->statement));

        $logger = Logger::getLogger();
        $logger->debug(
            'Last query: ' . $query->queryString,
        );

        $query->setFetchMode($this->getFetchMode());

        return $query->fetchAll();
    }

    /**
     * Surround columns with backticks, if missing.
     *
     * @param string $column
     *
     * @return string
     */
    protected function formatColumn(string $column): string
    {
        $set = explode('.', $column);

        if (!is_array($set)) {
            return $column;
        }

        $formatted = '';
        foreach ($set as $col) {
            if (!strpos($col, '`')) {
                $formatted .= '`' . $col . '`';
            } else {
                $formatted .= $col;
            }
        }

        return $formatted;
    }

    public function setFetchMode(int $fetchMode): self
    {
        $this->fetchMode = $fetchMode;
    }

    public function getFetchMode(): int
    {
        return $this->fetchMode ?? PDO::FETCH_ASSOC;
    }
}

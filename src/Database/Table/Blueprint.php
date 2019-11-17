<?php

namespace AegisFang\Database\Table;

/**
 * Class Blueprint
 * @package AegisFang\Database\Table
 */
class Blueprint
{
    protected $table;

    protected $columns = [];

    /**
     * Blueprint constructor.
     *
     * @param $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @param $column
     * @param int $chars
     *
     * @return $this
     */
    public function string($column, $chars = 255): self
    {
        $this->columns[$column] = ['varchar' => $chars];

        return $this;
    }

    /**
     * @param $column
     * @param string $signed
     * @param bool $autoIncrement
     *
     * @return $this
     */
    public function int($column, $signed = 'unsigned', $autoIncrement = true): self
    {
        $this->columns[$column] = ['int' => [$signed, $autoIncrement]];

        return $this;
    }
}

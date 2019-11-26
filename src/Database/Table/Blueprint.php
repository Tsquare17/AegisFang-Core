<?php

namespace AegisFang\Database\Table;

/**
 * Class Blueprint
 * @package AegisFang\Database\Table
 */
class Blueprint
{
    public $id;
    public $columns = [];
    public $relationships = [];

    /**
     * @param $id
     *
     * @return $this
     */
    public function id($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param $column
     * @param int $chars
     *
     * @return $this
     */
    public function string($column, $chars = 255): self
    {
        $this->columns[$column] = ["VARCHAR({$chars})"];

        return $this;
    }

    /**
     * @param $column
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function tinyint($column, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('TINYINT', $column, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param $column
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function int($column, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('INT', $column, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param $column
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function bigint($column, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('BIGINT', $column, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param $type
     * @param $column
     * @param $unsigned
     * @param $notNull
     * @param $autoincrement
     *
     * @return $this
     */
    public function intType($type, $column, $unsigned, $notNull, $autoincrement): self
    {
        $value = $type;
        $value = $unsigned ? $value . ' UNSIGNED' : $value;
        $value = $notNull ? $value . ' NOT NULL' : $value;
        $value = $autoincrement ? $value . ' AUTO_INCREMENT' : $value;

        $this->columns[$column] = [$value];

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function foreign($id): self
    {
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function references($id): self
    {
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function on($id): self
    {
    }

    /**
     * @param $action
     *
     * @return $this
     */
    public function onDelete($action): self
    {
    }
}

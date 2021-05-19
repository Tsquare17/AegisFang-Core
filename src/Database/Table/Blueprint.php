<?php

namespace AegisFang\Database\Table;

/**
 * Class Blueprint
 * @package AegisFang\Database\Table
 */
class Blueprint
{
    public $id;
    public array $columns = [];
    public array $relationships = [];

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
     * @param int $length
     *
     * @return $this
     */
    public function string($column, $length = 255): self
    {
        $this->columns[$column] = ["VARCHAR({$length})"];

        return $this;
    }

    /**
     * @param $column
     * @param int $length
     *
     * @return $this
     */
    public function text($column, $length = 65535): self
    {
        $this->columns[$column] = ["TEXT({$length})"];

        return $this;
    }

    /**
     * @return $this
     */
    public function unique(): self
    {
        end($this->columns);

        $key = key($this->columns);

        $this->columns[$key] = [$this->columns[$key][0] . ' UNIQUE'];

        reset($this->columns);

        return $this;
    }

    /**
     * @param $column
     * @param null|int $length
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function tinyint($column, $length = null, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('TINYINT', $column, $length, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param $column
     * @param null|int $length
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function int($column, $length = null, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('INT', $column, $length, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param $column
     * @param null|int $length
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function bigint($column, $length = null, $unsigned = false, $notNull = false, $autoincrement = false): self
    {
        return $this->intType('BIGINT', $column, $length, $unsigned, $notNull, $autoincrement);
    }

    /**
     * @param string $type
     * @param $column
     * @param null|int $length
     * @param bool $unsigned
     * @param bool $notNull
     * @param bool $autoincrement
     *
     * @return $this
     */
    public function intType($type, $column, $length, $unsigned, $notNull, $autoincrement): self
    {
        $value = $type;
        $value = $length ? $value . '(' . $length . ')' : $value;
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
    public function references($id): self
    {
        // Set foreign key on last registered column, referencing $id.
        end($this->columns);

        $key = key($this->columns);

        $this->relationships[] = [
            $key,
            $id,
        ];

        reset($this->columns);

        return $this;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    public function on(string $table): self
    {
        end($this->relationships);

        $key = key($this->relationships);

        $this->relationships[$key][] = $table;

        reset($this->relationships);

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function onUpdate($id): self
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

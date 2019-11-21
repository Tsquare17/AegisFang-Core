<?php

namespace AegisFang\Database\Migrations;

use AegisFang\Database\Table\Builder;

abstract class Migration
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    abstract public function table();

    /**
     * @return bool
     */
    public function make()
    {
        $blueprint = $this->table();
        $table = new Builder($this->table, $blueprint);

        return $table->createTable();
    }

    /**
     * @return bool
     */
    public function unmake(): bool
    {
        return Builder::destroy($this->table);
    }
}

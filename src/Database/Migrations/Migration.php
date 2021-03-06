<?php

namespace AegisFang\Database\Migrations;

use AegisFang\Database\Table\Blueprint;
use AegisFang\Database\Table\Builder;

/**
 * Class Migration
 * @package AegisFang\Database\Migrations
 */
abstract class Migration
{
    protected string $tableName;

    /**
     * Migration constructor.
     *
     * @param $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param Blueprint $blueprint
     *
     * @return mixed
     */
    abstract public function table(Blueprint $blueprint);

    /**
     * @return bool
     */
    public function make(): bool
    {
        $table = new Builder($this->tableName, $this->table(new Blueprint()));

        $isCreated = $table->createTable();

        $table->createRelationships();

        return $isCreated;
    }

    /**
     * @return bool
     */
    public function unmake(): bool
    {
        return Builder::destroy($this->tableName);
    }
}

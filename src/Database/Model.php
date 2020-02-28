<?php

namespace AegisFang\Database;

use AegisFang\Utils\Strings;

/**
 * Class Model
 * @package AegisFang\Database
 */
class Model
{
    protected Query $query;

    /**
     * Model constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get the assumed name of the table.
     *
     * @return string
     */
    public function getTable(): string
    {
        $class = explode('\\', get_class($this));

        return Strings::pascalToSnake(end($class));
    }

    /**
     * Get a row by id.
     *
     * @param $id
     *
     * @return array
     */
    public function findById($id): array
    {
        $query = new Query();
        $query->select()
            ->from($this->getTable())
            ->where(Strings::getSingular($this->getTable()) . '_id', $id);

        return $query->execute()->fetch();
    }
}

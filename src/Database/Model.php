<?php

namespace AegisFang\Database;

use AegisFang\Utils\Strings;

class Model
{
    protected Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getTable(): string
    {
        $class = explode('\\', get_class($this));

        return Strings::pascalToSnake(end($class));
    }

    public function findById($id): array
    {
        $query = new Query();
        $query->select()
            ->from($this->getTable())
            ->where(Strings::getSingular($this->getTable()) . '_id', $id);
        return $query->execute();
    }
}

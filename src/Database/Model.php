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
}

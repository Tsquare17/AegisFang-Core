<?php

namespace AegisFang\Database;

use PDO;

/**
 * Interface Connection
 * @package AegisFang\Database
 */
interface Connection
{

    /**
     * @return PDO
     */
    public function get(): PDO;

    /**
     * @return Query
     */
    public function query(): Query;

    /**
     * Get the database name.
     *
     * @return string
     */
    public function getName(): string;
}

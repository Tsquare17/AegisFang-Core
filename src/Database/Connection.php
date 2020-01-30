<?php

namespace AegisFang\Database;

use PDO;
use PDOException;

/**
 * Class Connection
 * @package AegisFang\Database
 */
class Connection
{
    protected $pdo;
    protected $dbName;

    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->dbName = getenv('DB_NAME');
        try {
            $options = getenv('DB_OPTIONS') ?: [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];
            $this->pdo = new PDO(
                getenv('DB_CONNECTION') . ':host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                $options
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function get(): PDO
    {
        return $this->pdo;
    }

    /**
     * @return Query
     */
    public function query(): Query
    {
        return new Query($this->pdo);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->dbName;
    }
}

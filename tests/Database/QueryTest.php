<?php

namespace AegisFang\Tests;

use AegisFang\Database\Query;
use PHPUnit\Framework\TestCase;
use AegisFang\Database\Connection;

class QueryTest extends TestCase
{
    protected $pdo;

    protected $query;

    public function setUp(): void
    {
        $this->pdo = new Connection();
    }

    /** @test */
    public function can_create_query_object(): void
    {
        $this->query = $this->pdo->query();

        $this->assertInstanceOf(Query::class, $this->query);
    }
}

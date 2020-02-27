<?php

namespace AegisFang\Tests;

use AegisFang\Database\Query;
use AegisFang\Database\Table\Blueprint;
use AegisFang\Database\Table\Builder;
use PHPUnit\Framework\TestCase;
use AegisFang\Database\Connection;

class QueryTest extends TestCase
{
    protected Connection $conn;

    protected Query $query;

    public function setUp(): void
    {
        $blueprint = new Blueprint();
        $blueprint->id('test_id');
        $blueprint->string('test_string', 100);
        $blueprint->int('test_int', true, true);
        $table = new Builder('test_table', $blueprint);
        $table->createTable();

        $config = require getenv('APP_CONFIG');
        $this->conn = new $config['db_driver']();
        $this->query = $this->conn->query();
    }

    /** @test */
    public function can_insert_row(): void
    {
        $insert = $this->query->insert(['test_string', 'test_int'], ['test val', 2])
            ->into('test_table')
            ->execute();

        $this->assertTrue($insert);
    }

    /** @test */
    public function can_select_inserted_row(): void
    {
        $row = $this->query->select()
            ->from('test_table')
            ->where('test_int', 2)
            ->execute();

        $expected = [
            'test_id' => '1',
            'test_string' => 'test val',
            'test_int' => '2',
        ];

        $this->assertEquals($expected, $row[0]);
    }

    public static function tearDownAfterClass(): void
    {
        Builder::destroy('test_table');
    }
}

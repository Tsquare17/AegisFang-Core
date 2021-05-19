<?php

namespace AegisFang\Tests;

use AegisFang\Database\Table\Blueprint;
use AegisFang\Database\Table\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    /** @test */
    public function can_create_table(): void
    {
        $blueprint = new Blueprint();
        $blueprint->id('test_id');
        $blueprint->string('testcol', 100);
        $blueprint->int('testint', true, true);
        $blueprint->text('text_col');
        $table = new Builder('aegistest', $blueprint);
        $isCreated = $table->createTable();

        $this->assertTrue($isCreated);
    }

    /** @test */
    public function can_create_table_with_all_integer_types(): void
    {
        $blueprint = new Blueprint();

        $blueprint->tinyint('tiny_col');
        $blueprint->int('int_col');
        $blueprint->bigint('big_col');

        $table = new Builder('int_test', $blueprint);
        $isCreated = $table->createTable();

        $this->assertTrue($isCreated);
    }

    /** @test */
    public function can_create_table_with_relationship(): void
    {
        $blueprint = new Blueprint();
        $blueprint->int('foo_id');
        $table = new Builder('foo', $blueprint);
        $table->createTable();

        $blueprint = new Blueprint();
        $blueprint->int('foo_id')
            ->references('foo_id')
            ->on('foo');
        $table = new Builder('bar', $blueprint);
        $isCreated = $table->createTable();
        $table->createRelationships();

        // Need to check to see if the relationship actually exists.
        // Need to set on update delete maybe. deleting table fails because of foreign key constraint
        $this->assertTrue($isCreated);
    }

    /** @test */
    public function can_delete_table(): void
    {
        $isDestroyed = Builder::destroy('aegistest');

        $this->assertTrue($isDestroyed);

        Builder::destroy('int_test');
        Builder::destroy('bar');
        Builder::destroy('foo');
    }
}

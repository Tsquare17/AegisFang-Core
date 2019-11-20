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
        $table = new Builder('aegistest', $blueprint);
        $isCreated = $table->createTable();

        $this->assertTrue($isCreated);
    }

    /** @test */
    public function can_delete_table(): void
    {
        $blueprint = new Blueprint();
        $table = new Builder('aegistest', $blueprint);
        $isDestroyed = $table->destroy();

        $this->assertTrue($isDestroyed);
    }
}
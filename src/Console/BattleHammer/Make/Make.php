<?php

namespace AegisFang\Console\BattleHammer\Make;

use Symfony\Component\Console\Command\Command;

abstract class Make extends Command
{
    public function __construct($name, $description)
    {
        $this->setDescription($description);
        parent::__construct('make:' . $name);
    }
}

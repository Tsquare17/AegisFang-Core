<?php

namespace AegisFang\Console\BattleHammer\Auth;

use Symfony\Component\Console\Command\Command;

class Auth extends Command
{
    public function __construct($name, $description)
    {
        $this->setDescription($description);
        parent::__construct('auth:' . $name);
    }
}

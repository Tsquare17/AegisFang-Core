<?php

namespace AegisFang\Console\BattleHammer\Auth;

use Symfony\Component\Console\Command\Command;

/**
 * Class Auth
 * @package AegisFang\Console\BattleHammer\Auth
 */
class Auth extends Command
{
    /**
     * Auth constructor.
     *
     * @param $name
     * @param $description
     */
    public function __construct($name, $description)
    {
        $this->setDescription($description);
        parent::__construct('auth:' . $name);
    }
}

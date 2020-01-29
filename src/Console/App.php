<?php

namespace AegisFang\Console;

use AegisFang\Console\BattleHammer\Auth\KeyGen;
use AegisFang\Console\BattleHammer\Make\MakeController;
use AegisFang\Console\BattleHammer\Migrate\Migrate;
use AegisFang\Container\Container;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct(Container $container, $version)
    {
        parent::__construct('AegisFang', $version);

        $container->set('BattleHammer', $this);
        $this->add(new MakeController());
        $this->add(new Migrate($container));
        $this->add(new KeyGen($container));
    }
}

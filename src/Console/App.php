<?php

namespace AegisFang\Console;

use AegisFang\Console\BattleHammer\Auth\KeyGen;
use AegisFang\Console\BattleHammer\Make\MakeController;
use AegisFang\Console\BattleHammer\Make\MakeMigration;
use AegisFang\Console\BattleHammer\Make\MakeModel;
use AegisFang\Console\BattleHammer\Migrate\Migrate;
use AegisFang\Console\BattleHammer\Setup\Setup;
use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct($version)
    {
        parent::__construct('AegisFang', $version);

        $this->add(new MakeController());
        $this->add(new MakeMigration());
        $this->add(new MakeModel());
        $this->add(new Migrate());
        $this->add(new KeyGen());
        $this->add(new Setup());
    }
}

<?php

namespace AegisFang\Console\BattleHammer\Make;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Make
{
    public function __construct()
    {
        parent::__construct('controller', 'Generate a controller.');
    }

    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Oh yeah!</>');
    }
}

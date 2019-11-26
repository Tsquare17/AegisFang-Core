<?php

namespace AegisFang\Console\BattleHammer\Migrate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
    public function __construct()
    {
        $this->setDescription('Run an/all migration');
        parent::__construct('migrate');
    }

    public function configure(): void
    {
        $this->addArgument('migration file', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Oh yeah!</>');
    }
}

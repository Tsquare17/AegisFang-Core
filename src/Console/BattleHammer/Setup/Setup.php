<?php

namespace AegisFang\Console\BattleHammer\Setup;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends Command
{
    public function __construct()
    {
        $this->setDescription('Application Setup.');
        parent::__construct('setup');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $basePath = getcwd() . '/';
        $env = file_get_contents($basePath . '.env');

        preg_match('/(^APP_PATH).*\\n+/m', $env, $match);

        if (!empty($match)) {
            $new = str_replace($match[0], 'APP_PATH=' . $basePath . PHP_EOL, $env);
            $success = file_put_contents($basePath . '.env', $new);
        } else {
            // TODO: Make sure APP_CONFIG is set after APP_PATH.
            $success = file_put_contents(
                $basePath . '.env',
                PHP_EOL . 'APP_PATH=' . $basePath,
                FILE_APPEND
            );
        }

        if ($success) {
            $output->writeln('<info>Success.</>');
            return 0;
        }

        $output->writeln('<error>Setup failed.</>');
        return 1;
    }
}

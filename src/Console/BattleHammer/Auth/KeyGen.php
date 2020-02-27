<?php

namespace AegisFang\Console\BattleHammer\Auth;

use AegisFang\Container\Container;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyGen extends Auth
{
    protected Container $container;

    public function __construct()
    {
        parent::__construct('keygen', 'Generate the application key.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $key = 'APP_KEY=' . bin2hex(random_bytes(32)) . PHP_EOL;
        } catch (\Exception $e) {
            $output->writeln("<error>{{$e->getMessage()}}</>");
            return 1;
        }

        $basePath = getcwd() . '/';
        $env = file_get_contents($basePath . '.env');

        preg_match('/(^APP_KEY).*\\n+/m', $env, $match);

        if (!empty($match)) {
            $new = str_replace($match[0], $key, $env);
            $success = file_put_contents($basePath . '.env', $new);
        } else {
            $success = file_put_contents($basePath . '.env', PHP_EOL . $key, FILE_APPEND);
        }

        if ($success) {
            $output->writeln('<info>New app key generated.</>');
            return 0;
        }

        $output->writeln('<error>Failed to generate new app key.</>');
        return 1;
    }
}

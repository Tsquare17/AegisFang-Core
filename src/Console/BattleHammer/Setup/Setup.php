<?php

namespace AegisFang\Console\BattleHammer\Setup;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Setup
 * @package AegisFang\Console\BattleHammer\Setup
 */
class Setup extends Command
{
    /**
     * Setup constructor.
     */
    public function __construct()
    {
        $this->setDescription('Application Setup.');
        parent::__construct('setup');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $basePath = getcwd() . '/';
        $env = file_get_contents($basePath . '.env');

        preg_match('/(^APP_PATH).*\\n+/m', $env, $match);
        preg_match('/(^APP_CONFIG).*\\n+/m', $env, $configMatch);

        $defaultConfig = 'APP_CONFIG=${APP_PATH}config/config.php';

        if (!empty($match)) {
            $new = str_replace($match[0], 'APP_PATH=' . $basePath . PHP_EOL, $env);
            if (trim($configMatch[0]) !== $defaultConfig) {
                $output->writeln(
                    '<question>APP_CONFIG is not set to the default path. '
                    . 'If this was not intentional, replace it with the default path: '
                    . $defaultConfig . '</>'
                );
            }

            $success = file_put_contents($basePath . '.env', $new);
        } else {
            $newEnv = file_get_contents($basePath . '.env');
            $newEnv = str_replace($configMatch[0], '', $newEnv);
            file_put_contents($basePath . '.env', $newEnv);

            $success = file_put_contents(
                $basePath . '.env',
                PHP_EOL . 'APP_PATH=' . $basePath . "\r\n" . $configMatch[0],
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

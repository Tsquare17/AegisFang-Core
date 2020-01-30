<?php

namespace AegisFang\Console\BattleHammer\Migrate;

use AegisFang\Container\Container;
use AegisFang\Database\Table\Blueprint;
use AegisFang\Database\Table\Builder;
use Dotenv\Dotenv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
    protected Container $container;
    protected Dotenv $config;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = Dotenv::create($this->container->getBasePath());
        $this->config->load();
        $this->setDescription('Run an/all migration');
        parent::__construct('migrate');
    }

    public function configure(): void
    {
        $this->addArgument('migration file', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationsPath = $this->container->getBasePath() . 'database/migrations';
        $files = array_diff(scandir($migrationsPath), array('.', '..'));

        // loop through, instantiate, and try to run, if fail, roll back through completed and destroy.
        foreach ($files as $file) {
            $className = $this->filenameSnakeToCamel($file);
            $tableName = $this->getTableName($file);

            $builder = new Builder($tableName, new Blueprint());
            if ($builder->tableExists()) {
                $output->writeln("<error>The table '{$tableName}' already exists.</>");
                return 0;
            }

            include_once $migrationsPath . '/' . $file;

            $migration = new $className($tableName);
            $success = $migration->make();

            if ($success) {
                $output->writeln("<info>Created table {$tableName}</>");
            } else {
                $output->writeln("<error>Failed to create table {$tableName}</>");
            }
        }

        return 0;
    }

    /**
     * Convert a php file name to camel case, excluding the extension.
     *
     * @param string $file
     *
     * @return string
     */
    protected function filenameSnakeToCamel(string $file): string
    {
        return lcfirst(
            str_replace('.php', '', str_replace('_', '', ucwords($file, '_')))
        );
    }

    private function getTableName(string $file): string
    {
        return str_replace(['create_', '_table', '.php'], '', $file);
    }
}

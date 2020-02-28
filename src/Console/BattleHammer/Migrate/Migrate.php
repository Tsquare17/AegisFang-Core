<?php

namespace AegisFang\Console\BattleHammer\Migrate;

use AegisFang\Database\Table\Blueprint;
use AegisFang\Database\Table\Builder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Migrate
 * @package AegisFang\Console\BattleHammer\Migrate
 */
class Migrate extends Command
{
    /**
     * Migrate constructor.
     */
    public function __construct()
    {
        $this->setDescription('Run an/all migration');
        parent::__construct('migrate');
    }

    /**
     * Set arguments.
     */
    public function configure(): void
    {
        $this->addArgument('migration file', InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO: Add ability to run a single migration.

        $migrationsPath = getcwd() . '/database/migrations';
        $files = array_diff(scandir($migrationsPath), array('.', '..'));

        foreach ($files as $file) {
            $className = $this->filenameSnakeToPascal($file);
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
    protected function filenameSnakeToPascal(string $file): string
    {
        return lcfirst(
            str_replace('.php', '', str_replace('_', '', ucwords($file, '_')))
        );
    }

    /**
     * Extract the name to use for the table from the migration filename.
     *
     * @param string $file
     *
     * @return string
     */
    private function getTableName(string $file): string
    {
        return str_replace(['create_', '_table', '.php'], '', $file);
    }
}

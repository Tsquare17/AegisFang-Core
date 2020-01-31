<?php

namespace AegisFang\Console\BattleHammer\Make;

use AegisFang\Container\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AegisFang\Utils\Strings;

class MakeMigration extends Make
{
    protected Container $container;
    protected string $stubsPath;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->stubsPath = dirname(__DIR__) . '/Make/stubs';
        parent::__construct('migration', 'Generate a migration.');
    }

    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrationsPath = $this->container->getBasePath() . 'database/migrations/';

        $name = $input->getArgument('name');
        $migrationFileName = 'create_' . $name . '_table.php';
        $migrationClassName = Strings::snakeToPascal($name);
        $newFile = $migrationsPath . $migrationFileName;

        if (file_exists($newFile)) {
            $output->writeln("<error>File {$migrationFileName} already exists.</>");
            return 0;
        }

        $stub = $this->getMigrationStub();

        $singular = Strings::getSingular($name);

        $step1 = $this->replaceStubSnakeCaseSingular($stub, $singular);
        $step2 = $this->replaceStubSnakeCase($step1, $name);
        $replacedStub = $this->replaceStubPascalCase($step2, $migrationClassName);

        $write = file_put_contents($newFile, $replacedStub);

        if (!$write) {
            $output->writeln("<error>Failed to write file {$newFile}</>");
            return 0;
        }

        $output->writeln("<info>Created migration {$migrationFileName}</>");

        return 0;
    }

    /**
     * Get the contents of the migration stub file.
     *
     * @return string
     */
    public function getMigrationStub(): string
    {
        return file_get_contents($this->stubsPath . '/migrations/migration.stub');
    }
}

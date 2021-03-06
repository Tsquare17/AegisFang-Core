<?php

namespace AegisFang\Console\BattleHammer\Make;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MakeModel
 * @package AegisFang\Console\BattleHammer\Make
 */
class MakeModel extends Make
{
    protected string $stubsPath;

    /**
     * MakeModel constructor.
     */
    public function __construct()
    {
        $this->stubsPath = dirname(__DIR__) . '/Make/stubs';
        parent::__construct('model', 'Generate a model.');
    }

    /**
     * Set arguments.
     */
    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $modelsPath = getcwd() . '/app/models/';

        $model = $input->getArgument('name');

        $newFile = $modelsPath . $model . '.php';

        if (file_exists($newFile)) {
            $output->writeln("<error>File {$model}.php already exists.</>");
            return 0;
        }

        $stub = $this->getModelsStub();

        $step1 = $this->replaceName($stub, $model);
        $replacedStub = $this->replaceStubPascalCase($step1, $model);

        $write = file_put_contents($newFile, $replacedStub);

        if (!$write) {
            $output->writeln("<error>Failed to write file {$newFile}</>");
            return 0;
        }

        $output->writeln("<info>Created model {$model}</>");

        return 0;
    }

    /**
     * Get the contents of the Controller stub file.
     *
     * @return string
     */
    public function getModelsStub(): string
    {
        return file_get_contents($this->stubsPath . '/models/Model.stub');
    }
}

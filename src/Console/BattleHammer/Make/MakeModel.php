<?php

namespace AegisFang\Console\BattleHammer\Make;

use AegisFang\Container\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends Make
{
    protected Container $container;
    protected string $stubsPath;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->stubsPath = dirname(__DIR__) . '/Make/stubs';
        parent::__construct('model', 'Generate a model.');
    }

    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $modelsPath = $this->container->getBasePath() . 'app/models/';

        $newModelName = $model = $input->getArgument('name');
        if (strpos(strrev($input->getArgument('name')), strrev('Model')) !== 0) {
            $newModelName .= 'Model';
        } else {
            $model = str_replace('Model', '', $model);
        }

        $newFile = $modelsPath . $newModelName . '.php';

        if (file_exists($newFile)) {
            $output->writeln("<error>File {$newModelName}.php already exists.</>");
            return 0;
        }

        $stub = $this->getModelsStub();

        $step1 = $this->replaceName($stub, $model);
        $replacedStub = $this->replaceStubPascalCase($step1, $newModelName);

        $write = file_put_contents($newFile, $replacedStub);

        if (!$write) {
            $output->writeln("<error>Failed to write file {$newFile}</>");
            return 0;
        }

        $output->writeln("<info>Created model {$newModelName}</>");

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

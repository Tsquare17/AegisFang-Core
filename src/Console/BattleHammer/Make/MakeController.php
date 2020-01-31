<?php

namespace AegisFang\Console\BattleHammer\Make;

use AegisFang\Container\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Make
{
    protected Container $container;
    protected string $stubsPath;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->stubsPath = dirname(__DIR__) . '/Make/stubs';
        parent::__construct('controller', 'Generate a controller.');
    }

    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $controllersPath = $this->container->getBasePath() . 'app/controllers/';

        $newControllerName = $input->getArgument('name') . 'Controller';
        $newFile = $controllersPath . $newControllerName . '.php';

        if (file_exists($newFile)) {
            $output->writeln("<error>File {$newControllerName}.php already exists.</>");
            return 0;
        }

        $stub = $this->getControllerStub();

        $replacedStub = $this->replaceStubPascalCase($stub, $newControllerName);

        $write = file_put_contents($newFile, $replacedStub);

        if (!$write) {
            $output->writeln("<error>Failed to write file {$newFile}</>");
            return 0;
        }

        $output->writeln("<info>Created controller {$newControllerName}</>");

        return 0;
    }

    /**
     * Get the contents of the Controller stub file.
     *
     * @return string
     */
    public function getControllerStub(): string
    {
        return file_get_contents($this->stubsPath . '/controllers/Controller.stub');
    }
}

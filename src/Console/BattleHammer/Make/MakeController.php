<?php

namespace AegisFang\Console\BattleHammer\Make;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MakeController
 * @package AegisFang\Console\BattleHammer\Make
 */
class MakeController extends Make
{
    protected string $stubsPath;

    /**
     * MakeController constructor.
     */
    public function __construct()
    {
        $this->stubsPath = dirname(__DIR__) . '/Make/stubs';
        parent::__construct('controller', 'Generate a controller.');
    }

    /**
     * Set arguments.
     */
    public function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the controller.');
        $this->addOption('restful', 'r', InputOption::VALUE_NONE, 'Create RESTFul controller.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $controllersPath = getcwd() . '/app/controllers/';

        $newControllerName = $input->getArgument('name');
        if (strpos(strrev($input->getArgument('name')), strrev('Controller')) !== 0) {
            $newControllerName .= 'Controller';
        }

        $newFile = $controllersPath . $newControllerName . '.php';

        if (file_exists($newFile)) {
            $output->writeln("<error>File {$newControllerName}.php already exists.</>");
            return 0;
        }

        if ($input->getOption('restful')) {
            $stub = $this->getRestControllerStub();
        } else {
            $stub = $this->getControllerStub();
        }

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
     * Get the contents of the controller stub file.
     *
     * @return string
     */
    public function getControllerStub(): string
    {
        return file_get_contents($this->stubsPath . '/controllers/Controller.stub');
    }

    /**
     * Get the contents of the RESTful controller stub file.
     *
     * @return string
     */
    public function getRestControllerStub(): string
    {
        return file_get_contents($this->stubsPath . '/controllers/RestController.stub');
    }
}

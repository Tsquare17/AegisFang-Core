<?php

namespace AegisFang\Console\BattleHammer\Make;

use Symfony\Component\Console\Command\Command;

/**
 * Class Make
 * @package AegisFang\Console\BattleHammer\Make
 */
abstract class Make extends Command
{
    /**
     * Make constructor.
     *
     * @param $name
     * @param $description
     */
    public function __construct($name, $description)
    {
        $this->setDescription($description);
        parent::__construct('make:' . $name);
    }

    /**
     * Replace Name placeholders.
     *
     * @param string $stub
     * @param string $new
     *
     * @return string
     */
    public function replaceName(string $stub, string $new): string
    {
        return preg_replace('/\$\:n\:\$/', $new, $stub);
    }

    /**
     * Replace PascalCase stub placeholders.
     *
     * @param string $stub
     *
     * @param string $new
     * @return string
     */
    public function replaceStubPascalCase(string $stub, string $new): string
    {
        return preg_replace('/\$\:\$/', $new, $stub);
    }

    /**
     * Replace snake_case stub placeholders.
     *
     * @param string $stub
     *
     * @param string $new
     * @return string
     */
    public function replaceStubSnakeCase(string $stub, string $new): string
    {
        return preg_replace('/\$\:\:\$/', $new, $stub);
    }

    /**
     * Replace snake_case singular placeholders.
     *
     * @param string $stub
     *
     * @param string $new
     * @return string
     */
    public function replaceStubSnakeCaseSingular(string $stub, string $new): string
    {
        return preg_replace('/\$\:s\:\$/', $new, $stub);
    }
}

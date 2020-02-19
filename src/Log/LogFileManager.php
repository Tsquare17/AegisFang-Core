<?php

namespace AegisFang\Log;

use Monolog\Formatter\LineFormatter;

class LogFileManager
{
    protected LineFormatter $formatter;

    public function __construct()
    {
        $this->formatter = new LineFormatter(
            null,
            'Y-m-d H:i:s',
            false,
            true
        );
    }

    /**
     * Get the log file line formatter.
     *
     * @return LineFormatter
     */
    public function getFormat(): LineFormatter
    {
        return $this->formatter;
    }

    /**
     * Get the absolute path to the log file.
     *
     * @return string
     */
    public function getPathToLog(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../var/log/' . $this->getLogFileName();
    }

    /**
     * Get the name of the log file.
     *
     * @return string
     */
    private function getLogFileName(): string
    {
        return 'aegisfang.log';
    }
}

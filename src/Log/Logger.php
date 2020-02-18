<?php

namespace AegisFang\Log;

use Psr\Log\LoggerInterface;

abstract class Logger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
        $this->writeToLog(__FUNCTION__, $message, $context);
    }

    /**
     * @inheritDoc
     */
    abstract public function log($level, $message, array $context = []);

    /**
     * Write to log.
     *
     * @param       $level
     * @param       $message
     * @param array $context
     */
    abstract protected function writeToLog($level, $message, array $context = []);

    /**
     * Get the logger set to be used in the application configuration file.
     *
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        $config = require $_SERVER['DOCUMENT_ROOT'] . '/../config/config.php';

        return new $config['logger']();
    }
}

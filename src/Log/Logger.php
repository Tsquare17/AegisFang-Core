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
    public function log($level, $message, array $context = []): void
    {
        switch ($level) {
            case 'emergency':
                $this->emergency($message, $context);
                break;
            case 'alert':
                $this->alert($message, $context);
                break;
            case 'critical':
                $this->critical($message, $context);
                break;
            case 'error':
                $this->error($message, $context);
                break;
            case 'warning':
                $this->warning($message, $context);
                break;
            case 'notice':
                $this->notice($message, $context);
                break;
            case 'info':
                $this->info($message, $context);
                break;
            case 'debug':
                $this->debug($message, $context);
        }
    }

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
        $configPath = getenv('APP_CONFIG');

        if (file_exists($configPath)) {
            $config = require $configPath;

            return new $config['logger']();
        }

        return new LogToFile();
    }
}

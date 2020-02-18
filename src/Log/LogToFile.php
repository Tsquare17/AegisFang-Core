<?php

namespace AegisFang\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

class LogToFile extends Logger implements LoggerInterface
{
    protected LogFileManager $logFileManager;

    public function __construct()
    {
        $this->logFileManager = new LogFileManager();
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
    protected function writeToLog($level, $message, array $context = []): void
    {
        if ($this->shouldLog($level)) {
            $this->logDriver(strtoupper($level))->{$level}($message, $context);
        }
    }

    /**
     * Get the log driver.
     *
     * @param $level
     *
     * @return LoggerInterface
     */
    protected function logDriver($level): LoggerInterface
    {
        $streamHandler = new StreamHandler(
            $this->logFileManager->getPathToLog(),
            $this->getLogLevel($level)
        );
        $streamHandler->setFormatter($this->logFileManager->getFormat());

        return new MonoLog($this->getLogChannel(), [
            $streamHandler
        ]);
    }

    /**
     * Get the log channel.
     *
     * @return string
     */
    protected function getLogChannel(): string
    {
        return 'AegisFang';
    }

    /**
     * Get the Monolog integer value of a log level.
     *
     * @param $level
     *
     * @return int
     */
    protected function getLogLevel($level): int
    {
        return constant(Monolog::class . '::' . $level);
    }

    /**
     * If APP_LOG_LEVEL is set to a level equal to or below the log event level.
     *
     * @param $level
     *
     * @return bool
     */
    private function shouldLog($level): bool
    {
        $applicationLogLevel = getenv('APP_LOG_LEVEL') ?: 'DEBUG';

        return $this->getLogLevel($applicationLogLevel) <= $this->getLogLevel(strtoupper($level));
    }
}

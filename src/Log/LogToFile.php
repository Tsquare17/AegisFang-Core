<?php

namespace AegisFang\Log;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

class LogToFile extends Logger
{
    protected LogFileManager $logFileManager;

    protected string $logChannel;

    public function __construct(string $logChannel = 'AegisFang')
    {
        $this->logFileManager = new LogFileManager();
        $this->logChannel = $logChannel;
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
        $streamHandler = new RotatingFileHandler(
            $this->logFileManager->getPathToLog(),
            $this->getMaxFiles(),
            $this->getLogLevel($level)
        );
        $streamHandler->setFormatter($this->logFileManager->getFormat());

        return new MonoLog($this->logChannel, [
            $streamHandler
        ]);
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
    protected function shouldLog($level): bool
    {
        $applicationLogLevel = getenv('APP_LOG_LEVEL') ?: 'DEBUG';

        return $this->getLogLevel($applicationLogLevel) <= $this->getLogLevel(strtoupper($level));
    }

    protected function getMaxFiles(): int
    {
        return 10;
    }
}

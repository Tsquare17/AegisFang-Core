<?php

namespace AegisFang\Log;

use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array()): void
    {
        // TODO: Implement emergency() method.
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array()): void
    {
        // TODO: Implement alert() method.
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array()): void
    {
        // TODO: Implement critical() method.
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array()): void
    {
        // TODO: Implement error() method.
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array()): void
    {
        // TODO: Implement warning() method.
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array()): void
    {
        // TODO: Implement notice() method.
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array()): void
    {
        // TODO: Implement info() method.
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array()): void
    {
        // TODO: Implement debug() method.
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array()): void
    {
        // TODO: Implement log() method.
    }
}

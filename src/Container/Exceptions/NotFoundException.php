<?php

namespace AegisFang\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 * @package AegisFang\Container\Exceptions
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * NotFoundException constructor.
     *
     * @param                $id
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($id, $code = 0, Exception $previous = null)
    {
        $message = "Dependency {$id} is not registered.";
        parent::__construct($message, $code, $previous);
    }
}

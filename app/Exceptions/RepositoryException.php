<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class RepositoryException
 * This is a custom exception for repository.
 */
class RepositoryException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Error", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

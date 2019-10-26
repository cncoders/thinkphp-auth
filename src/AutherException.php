<?php
namespace cncoders\auth;

use Throwable;

class AutherException extends \Exception
{
    /**
     * 抛出异常
     *
     * AutherException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
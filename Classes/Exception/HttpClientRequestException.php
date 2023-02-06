<?php

namespace Sto\Mediaoembed\Exception;

use RuntimeException;
use Throwable;

class HttpClientRequestException extends RuntimeException
{
    public function __construct(string $message, int $httpCode, Throwable $previous = null, private readonly string $errorDetails = '')
    {
        parent::__construct($message, $httpCode, $previous);
    }

    public function getErrorDetails(): string
    {
        return $this->errorDetails;
    }
}

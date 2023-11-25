<?php

namespace Aloefflerj\YetAnotherController\Controller\Exceptions;

class HttpMethodDoesNotExist extends \Exception
{
    public function __construct(string $methodName, int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct("Method ({$methodName}) was not found in the given context", $code, $previous);
    }
}

<?php

namespace Aloefflerj\YetAnotherController\Controller\Exceptions;

class RouteNotFound extends \Exception
{
    public function __construct(string $requiredUrl, string $message = 'Route not found. Acessed url was ({{url}}).')
    {
        $message = preg_replace('/\{\{url\}\}/', $requiredUrl, $message);
        parent::__construct($message);
    }
}

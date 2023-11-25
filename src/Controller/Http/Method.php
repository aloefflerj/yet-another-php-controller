<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\Exceptions\HttpMethodDoesNotExist;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';

    public static function getAllPossibleValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function try(string $method): self
    {
        $foundMethod = self::tryFrom($method);
        if (is_null($foundMethod))
            throw new HttpMethodDoesNotExist($method);

        return $foundMethod;
    }
}

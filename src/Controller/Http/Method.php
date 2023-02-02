<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';

    public static function getAllPossibleValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}

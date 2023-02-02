<?php

namespace Aloefflerj\YetAnotherController\Controller\Helpers;

trait UriHelper
{
    public function assertUri(string $uri): bool
    {
        return preg_match("/\w+:(\/?\/?)[^\s]+/", $uri);
    }
}
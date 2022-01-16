<?php

namespace Aloefflerj\YetAnotherController\Controller\Helpers;

trait HttpHelper
{
    public function getHttpMethods(): array
    {
        return ['get', 'post', 'put', 'delete', 'options'];
    }
}
<?php

namespace Aloefflerj\YetAnotherController\Controller\Config;

use Psr\Http\Message\MessageInterface;

class ControllerSetup
{
    public function submitHeaders(MessageInterface $message): void
    {
        foreach ($message->getHeaders() as $headerKey => $headerValues) {
            $headerValues = implode(';', $headerValues);
            header("{$headerKey}: {$headerValues}");
        }
    }
}

<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

class StreamBuilder
{
    public function buildStreamFromPHPInput(): Stream
    {
        return new Stream(fopen('php://input', 'r'));
    }
}

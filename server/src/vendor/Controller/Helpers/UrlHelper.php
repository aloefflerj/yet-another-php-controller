<?php

namespace Aloefflerj\FedTheDog\Controller\Helpers;

trait UrlHelper
{
    /**
     * Get current url path
     *
     * @return string|null
     */
    public function getUriPath(): ?string
    {
        return $_SERVER['REQUEST_URI'] ?? null;
    }

    /**
     * Get request method
     *
     * @return string
     */
    private function getRequestMethod(): ?string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}
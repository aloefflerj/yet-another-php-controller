<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\PSR\UriInterface;

class Uri
// class Uri implements UriInterface
{

    private string $completeUri;
    private string $scheme;
    private string $authority;
    private string $userInfo;

    /**
     * @throws \Exception
     */
    public function __construct(string $uri = "")
    {
        $validUri = preg_match("/\w+:(\/?\/?)[^\s]+/", $uri);

        if (!$validUri) {
            throw new \Exception('This is not a valid uri');
        }

        $this->completeUri = $uri;
        $this->split();
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return $this->authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    # helper functions

    private function split(): void
    {
        $this->scheme = explode(':', $this->completeUri)[0];
        $this->authority = $this->splitToAuthority();
        $this->userInfo = $this->splitToUserInfo();
    }

    private function splitToAuthority(): string
    {
        $authorityArr = explode(':', $this->completeUri);
        $authority = "$authorityArr[1]";

        if (isset($authorityArr[2])) {
            $authority .= ":" . $authorityArr[2];
        }

        $authority = str_replace('//', '', $authority);

        if (strpos($authority, '/')) {
            $authority = explode('/', $authority)[0];
        }

        return $authority;
    }

    private function splitToUserInfo()
    {
        $userInfo = '';
        if (strpos($this->completeUri, '@')) {
            $userInfo = explode('@', $this->authority)[0];
        }

        return $userInfo;
    }
}

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
    private string $host;
    private ?int $port;
    private string $path;

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

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    # HELPER FUNCTIONS #

    private function split(): void
    {
        $this->scheme = explode(':', $this->completeUri)[0];
        $this->authority = $this->splitToAuthority();
        $this->userInfo = $this->splitToUserInfo();
        $this->host = $this->splitToHost();
        $this->port = $this->splitToPort();
        $this->path = $this->splitToPath();
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

    private function splitToUserInfo(): string
    {
        $userInfo = '';
        if (strpos($this->completeUri, '@')) {
            $userInfo = explode('@', $this->authority)[0];
        }

        return $userInfo;
    }

    private function splitToHost(): string
    {
        $host = '';
        $authority = $this->authority;

        if (strpos($authority, '@')) {
            $host = explode('@', $authority)[1];
        }

        if (strpos($authority, ':')) {
            $host = explode(':', $authority)[0];
        }

        return strtolower($host);
    }

    private function splitToPort(): ?int
    {
        $port = null;
        $authority = $this->authority;

        if (strpos($authority, ':') && !strpos($authority, '@')) {
            $port = explode(':', $authority)[1];
            if (strpos($port, '/')) {
                $port = explode('/', $port)[0];
            }
        }

        return $port;
    }

    private function splitToPath(): string
    {
        $path = explode('/', $this->completeUri)[3];
        $path = "/{$path}/";
        return $path;
    }
}

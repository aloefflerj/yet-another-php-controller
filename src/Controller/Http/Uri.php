<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\Helpers\StringHelper;
use Aloefflerj\YetAnotherController\Controller\Helpers\UriHelper;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    use Schemes;
    use StringHelper;
    use UriHelper;

    private string $completeUri;
    private string $scheme;
    private string $authority;
    private string $userInfo;
    private string $host;
    private ?int $port;
    private string $path;
    private string $query;
    private string $fragment;

    /**
     * @throws \Exception
     */
    public function __construct(string $uri = "")
    {
        if (empty($uri)) {
            $this->completeUri = $uri;
            return;
        }

        if (!$this->assertUri($uri)) {
            throw new \InvalidArgumentException("'$uri' is not a valid uri");
        }

        $this->completeUri = $uri;
    }

    public function __toString()
    {
        if (empty(trim($this->completeUri))) {
            return '';
        }

        return $this->glueElements();
    }

    private function loadUriAttributes(): void
    {
        if (empty($this->completeUri)) {
            $this->emptyAttributes();
            return;
        }

        $this->splitUriToAttributes();
    }

    public function withScheme($scheme): self
    {
        if (!preg_match(
            '/^[\da-z][\da-z\-]{1,20}$/',
            $scheme
        )) {
            throw new \InvalidArgumentException('This scheme in not valid');
        }

        if (!in_array($scheme, $this->getValidSchemes())) {
            throw new \InvalidArgumentException('This scheme is not supported');
        }

        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function withUserInfo($user, $password = '')
    {
        $clone = clone $this;
        $clone->userInfo = "{$user}:{$password}";

        return $clone;
    }

    public function withHost($host): self
    {
        if (!preg_match(
            '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/',
            $host
        )) {
            throw new \InvalidArgumentException('Invalid host');
        }

        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort($port): self
    {
        if (!is_null($port) && !preg_match('/^\d{1,4}$/', $port)) {
            throw new \InvalidArgumentException('Invalid uri port');
        }

        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    public function withPath($path): self
    {
        if (!preg_match('/(\/[a-z0-9]*).*/', $path)) {
            throw new \InvalidArgumentException('Invalid uri path');
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withQuery($query): self
    {
        if (!preg_match('/^([^=]+=[^=]+&)+[^=]+(=[^=]+)?$/', $query)) {
            throw new \InvalidArgumentException('Invalid uri query');
        }

        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    public function withFragment($fragment): self
    {
        $fragment = filter_var($fragment);

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    public function getScheme(): string
    {
        if (!isset($this->scheme))
            $this->loadUriAttributes();

        return $this->scheme;
    }

    public function getAuthority(): string
    {
        if (!isset($this->authority))
            $this->loadUriAttributes();

        return $this->authority;
    }

    public function getUserInfo(): string
    {
        if (!isset($this->userInfo))
            $this->loadUriAttributes();

        return $this->userInfo;
    }

    public function getHost(): string
    {
        if (!isset($this->host))
            $this->loadUriAttributes();

        return $this->host;
    }

    public function getPort(): ?int
    {
        if (!isset($this->port))
            $this->loadUriAttributes();

        return $this->port;
    }

    public function getPath(): string
    {
        if (!isset($this->path))
            $this->loadUriAttributes();

        return $this->path;
    }

    public function getQuery(): string
    {
        if (!isset($this->query))
            $this->loadUriAttributes();

        return $this->query;
    }

    public function getFragment(): string
    {
        if (!isset($this->fragment))
            $this->loadUriAttributes();

        return $this->fragment;
    }

    public function getCompleteUri(): string
    {
        if (!isset($this->completeUri))
            $this->loadUriAttributes();

        return $this->completeUri;
    }

    # HELPER FUNCTIONS #

    private function emptyAttributes(): void
    {
        $this->scheme       = '';
        $this->authority    = '';
        $this->userInfo     = '';
        $this->host         = '';
        $this->port         = null;
        $this->path         = '';
        $this->query        = '';
        $this->fragment     = '';
        $this->completeUri  = '';
    }

    private function splitUriToAttributes(): void
    {
        $this->scheme       = explode(':', $this->completeUri)[0];
        $this->authority    = $this->splitToAuthority();
        $this->userInfo     = $this->splitToUserInfo();
        $this->host         = $this->splitToHost();
        $this->port         = $this->splitToPort();
        $this->path         = $this->splitToPath();
        $this->query        = $this->splitToQuery();
        $this->fragment     = $this->splitToFragment();
        $this->completeUri  = $this->glueElements();
    }

    private function splitToAuthority(): string
    {
        $authorityArr = explode(':', $this->completeUri);
        if (!isset($authorityArr[1]))
            return '';

        $authority = "$authorityArr[1]";

        if (isset($authorityArr[2])) {
            $authority .= ":" . $authorityArr[2];
        }

        if (isset($authorityArr[3])) {
            $authority .= ":" . $authorityArr[3];
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
        $authority = $this->authority;
        $host = $authority;

        if (strpos($authority, '@')) {
            $host = explode('@', $authority)[1];
            return strtolower(
                $this->getValueUpToTheCharacter($host, ':')
            );
        }

        $host = $this->getValueUpToTheCharacter($host, ':');

        return strtolower($host);
    }

    private function splitToPort(): ?int
    {
        $port = null;
        $authority = $this->authority;

        if (strpos($authority, ':')) {
            $authorityArr = explode(':', $authority);
            $port = end($authorityArr);
            if (strpos($port, '@'))
                $port = null;
        }

        if (strpos($port, '/')) {
            $port = explode('/', $port)[0];
        }

        if (empty($port))
            $port = null;

        if (!is_numeric($port) && !is_null($port))
            throw new \Exception("Port: '{$port}' should be a numeric value");
        
        if (is_string($port))
            $port = (int)$port;

        return $port;
    }

    private function splitToPath(): string
    {
        $pathArr = explode('/', $this->completeUri);
        $pathArr = array_slice($pathArr, 3);
        $path = '/' . implode('/', $pathArr);

        if (strpos($path, '?')) {
            $path = explode('?', $path)[0];
        }

        return $path;
    }

    private function splitToQuery(): string
    {
        $uri = $this->completeUri;
        if (!strpos($uri, '?')) {
            return '';
        }

        if (strpos($uri, '#')) {
            $uri = explode('#', $uri)[0];
        }

        $query = explode('?', $uri)[1];
        return $query;
    }

    private function splitToFragment(): string
    {
        if (!strpos($this->completeUri, '#')) {
            return '';
        }

        return explode('#', $this->completeUri)[1];
    }

    private function glueElements(): string
    {
        $scheme     = $this->getScheme() ?? '';
        $authority  = $this->getAuthority() ?? '';
        $path       = $this->getPath() ?? '';
        $query      = $this->getQuery() ? "?{$this->getQuery()}" : '';
        $fragment   = $this->getFragment() ? "#{$this->getFragment()}" : '';

        return "{$scheme}://{$authority}{$path}{$query}{$fragment}";
    }
}

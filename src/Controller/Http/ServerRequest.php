<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Psr\Http\Message\UploadedFileInterface as MessageUploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    private array $attributes = [];
    /**
     * @var UploadedFileInterface[]
     */
    private array $uploadedFiles = [];

    public function getServerParams()
    {
        return $_SERVER;
    }

    public function getCookieParams()
    {
        return $_COOKIE;
    }

    public function withCookieParams(array $cookies)
    {
        $_COOKIE = $cookies;
        $clone = clone $this;
        return $clone;
    }

    public function getQueryParams()
    {
        $queryParams = $this->getUri()->getQuery();
        if (empty($queryParams))
            return [];

        preg_match_all('/([^[\?|&]+)=([^&]*)/', $queryParams, $queryParamsMatches);
        $queryParamsMatches = $queryParamsMatches[0];

        $formattedQueryParams = [];
        foreach ($queryParamsMatches as $queryKeyAndValue) {
            [$queryKey, $queryValue] = explode('=', $queryKeyAndValue);
            $formattedQueryParams[$queryKey] = $queryValue;
        }

        return $formattedQueryParams;
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        if (empty($query)) {
            return $clone;
        }

        $queryString = '';
        $firstQuery = true;
        foreach ($query as $queryKey => $queryValue) {

            if ($firstQuery) {
                $queryString .= "?{$queryKey}={$queryValue}";
                $firstQuery = false;
                continue;
            }

            $queryString .= "&{$queryKey}={$queryValue}";
        }

        $clone->uri = $clone->uri->withQuery($queryString);
        return $clone;
    }

    public function getParsedBody()
    {
        if ($this->isOfFormContentType()) {
            return (object)$_POST;
        }

        if ($this->isOfJsonContentType()) {
            return (object)json_decode($this->getBody());
        }

        throw new \Exception("There is currently no implementation to parse content of type '{$this->getHeader('Content-Type')[0]}'");
        return null;
    }

    public function withParsedBody($data)
    {
        $data = match (gettype($data)) {
            'object' => $this->parseObjectBody($data),
            'array' => $this->parseArrayBody($data),
            'null' => $data,
            default => throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null")
        };

        $clone = clone $this->withBody($data);
        return $clone;
    }

    private function parseObjectBody(object $objectData): object
    {
        if ($this->isOfFormContentType()) {
            $arrayData = (array)$objectData;
            return $this->parseArrayBody($arrayData);
        }

        if ($this->isOfJsonContentType()) {
            return json_encode($objectData);
        }

        throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null");
    }

    private function parseArrayBody(array $arrayData): object
    {
        if ($this->isOfFormContentType()) {
            foreach ($arrayData as $dataKey => $dataValue) {
                $_POST[$dataKey] = $dataValue;
            }
            return (object)$_POST;
        }

        if ($this->isOfJsonContentType()) {
            return json_encode($arrayData);
        }

        throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null");
    }

    private function isOfFormContentType()
    {
        return in_array('application/x-www-form-urlencoded', $this->getHeader('Content-Type')) ||
            in_array('multipart/form-data', $this->getHeader('Content-Type'));
    }

    private function isOfJsonContentType()
    {
        return in_array('application/json', $this->getHeader('Content-Type'));
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }

    /**
     * @return UploadedFileInterface[]
     */
    public function getUploadedFiles()
    {
        if (empty($_FILES)) {
            return $this->uploadedFiles;
        }

        $formattedFiles = $this->formatGlobalFilesVarIntoFilesArray($_FILES);
        $uploadedFiles = $this->hydrateUploadedFilesFromArrayOfFiles($formattedFiles);

        $this->uploadedFiles = $uploadedFiles;
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $uploadedFileInterfaceName = UploadedFileInterface::class;

        foreach ($uploadedFiles as $uploadedFile) {
            if (!is_a($uploadedFile, $uploadedFileInterfaceName)) {
                throw new \InvalidArgumentException("All items from array must contain an instance of {$uploadedFileInterfaceName}");
            }
        }

        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    private function formatGlobalFilesVarIntoFilesArray(array $filesGlobalVar): array
    {
        $formattedFiles = [];
        foreach ($filesGlobalVar as $filesKey => $filesWithInfos) {
            foreach ($filesWithInfos as $infoKey => $infoValue) {
                if (is_scalar($infoValue)) {
                    $infoValue = [$infoValue];
                }

                for ($i = 0; $i < count($infoValue); $i++) {
                    $formattedFiles[$filesKey][$i][$infoKey] = $infoValue[$i];
                }
            }
        }

        return $formattedFiles;
    }

    /**
     * @param array $formattedFiles
     * @return MessageUploadedFileInterface[]
     */
    private function hydrateUploadedFilesFromArrayOfFiles(array $formattedFiles): array
    {
        $hydratedUploadedFiles = [];
        foreach ($formattedFiles as $fileGroupName => $fileGroup) {
            foreach ($fileGroup as $fileGroupIndex => $fileInfo) {
                $hydratedUploadedFiles[] = UploadedFile::buildFromFileInfo($fileInfo, $fileGroupName, $fileGroupIndex);
            }
        }
        return $hydratedUploadedFiles;
    }
}

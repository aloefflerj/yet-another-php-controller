<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\ServerRequest;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\UploadedFile;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class ServerRequestTest extends TestCase
{
    const FILE1_UPLOAD_MOCK = [
        'name' => 'StreamDummyFile.txt',
        'full_path' => 'StreamDummyFile.txt',
        'type' => 'text/plain',
        'tmp_name' => __DIR__ . '/StreamDummyFile.txt',
        'error' => 0,
        'size' => 56,
    ];

    const FILE2_UPLOAD_MOCK = [
        'name' => [
            'StreamDummyFile.txt',
            'StreamDummyFile.log',
        ],
        'full_path' => [
            'StreamDummyFile.txt',
            'StreamDummyFile.log',
        ],
        'type' => [
            'text/plain',
            'text/plain',
        ],
        'tmp_name' => [
            __DIR__ . '/StreamDummyFile.txt',
            __DIR__ . '/StreamDummyFile.log',
        ],
        'error' => [
            0,
            0,
        ],
        'size' => [
            56,
            60,
        ]
    ];

    public function testServerParams(): void
    {
        $serverRequest = new ServerRequest('GET');
        $this->assertEquals($_SERVER, $serverRequest->getServerParams());
    }

    public function testCookieParams(): void
    {
        $_COOKIE['planet'] = 'mars';
        $serverRequest = new ServerRequest('GET');
        $this->assertEquals($_COOKIE, $serverRequest->getCookieParams());

        $_COOKIE['planet'] = 'mars';
        $serverRequest = new ServerRequest('GET');
        $serverRequest = $serverRequest->withCookieParams(['planet' => 'venus']);
        $this->assertEquals(['planet' => 'venus'], $serverRequest->getCookieParams());
    }

    public function testQueryParams(): void
    {
        $serverRequest = new ServerRequest('GET', new Uri('http://universe.com?star=sun&galaxy=milky-way'));
        $this->assertEquals([
            'star' => 'sun',
            'galaxy' => 'milky-way'
        ], $serverRequest->getQueryParams());

        $serverRequest = $serverRequest->withQueryParams([
            'star' => 'alpheratz',
            'galaxy' => 'andromeda'
        ]);
        $this->assertEquals([
            'star' => 'alpheratz',
            'galaxy' => 'andromeda'
        ], $serverRequest->getQueryParams());
    }

    public function testParsedBody(): void
    {
        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $_POST['aang'] = 'air';
        $this->assertEquals((object)$_POST, $serverRequest->getParsedBody());

        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'multipart/form-data');
        $_POST['katara'] = 'water';
        $this->assertEquals((object)$_POST, $serverRequest->getParsedBody());

        $jsonStructure = [
            'aang' => 'air',
            'katara' => 'water'
        ];
        $json = json_encode($jsonStructure, JSON_PRETTY_PRINT);
        $resource = fopen('php://memory', 'r+');
        fputs($resource, $json);

        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'), new Stream($resource));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'application/json');
        $this->assertEquals((object)$jsonStructure, $serverRequest->getParsedBody());

        $this->expectException(\Exception::class);
        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'), new Stream($resource));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'text/csv');
        $serverRequest->getParsedBody();

        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'multipart/form-data');
        unset($_POST);
        $_POST['aang'] = 'value';
        $newBody = new \stdClass();
        $newBody->katara = 'water';
        $serverRequest = $serverRequest->withParsedBody($newBody);
        $expectedBody = $_POST + array($newBody);
        $this->assertEquals((object)$expectedBody, $serverRequest->getParsedBody());

        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'application/json');
        $serverRequest = $serverRequest->withParsedBody($jsonStructure);
        $this->assertEquals((object)$jsonStructure, $serverRequest->getParsedBody());

        $this->expectException(\InvalidArgumentException::class);
        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'), new Stream($resource));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'text/csv');
        $serverRequest = $serverRequest->withParsedBody('invalid argument');

        $this->expectException(\Exception::class);
        $serverRequest = new ServerRequest('POST', new Uri('http://test.com'), new Stream($resource));
        $serverRequest = $serverRequest->withHeader('Content-Type', 'text/csv');
        $serverRequest = $serverRequest->withParsedBody($jsonStructure);
    }

    public function testAttributes(): void
    {
        $serverRequest = new ServerRequest('POST');
        $_SESSION['user_name'] = 'aang';
        $_SESSION['user_role'] = 'airbender';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $serverRequest = $serverRequest->withAttribute('session', $_SESSION)->withAttribute('ip_address', $_SERVER['REMOTE_ADDR']);

        $attributes = $serverRequest->getAttributes();
        $this->assertEquals([
            'user_name' => 'aang',
            'user_role' => 'airbender'
        ], $serverRequest->getAttributes()['session']);

        $this->assertEquals('127.0.0.1', $serverRequest->getAttributes()['ip_address']);

        $defaultAttributeValue = $serverRequest->getAttribute('host', '[empty-host]');
        $this->assertEquals('[empty-host]', $defaultAttributeValue);

        $serverRequest = $serverRequest->withAttribute('host', 'localhost');
        $attributeValue =  $serverRequest->getAttribute('host', '[empty-host]');
        $this->assertEquals('localhost', $attributeValue);

        $serverRequest = $serverRequest->withoutAttribute('host');
        $defaultAttributeValue = $serverRequest->getAttribute('host', '[empty-host]');
        $this->assertEquals('[empty-host]', $defaultAttributeValue);
    }

    #[DataProvider('singleFileUploadMockFromPhpFilesGlobalVarPriveder')]
    #[DataProvider('multipleFilesUploadMockFromPhpFilesGlobalVarProvider')]
    public function testUploadedFilesByFilesGlobalVar(array $globalFilesVarResultAfterFileUpload): void
    {
        $_FILES = $globalFilesVarResultAfterFileUpload;
        $serverRequest = new ServerRequest('POST');
        $uploadedFiles = $serverRequest->getUploadedFiles();

        $expected = $globalFilesVarResultAfterFileUpload;

        foreach ($uploadedFiles as $uploadedFile) {
            /** @var UploadedFile $uploadedFile */
            $fileGroupKey = $uploadedFile->getFileGroupKey();
            $fileGroupIndex = $uploadedFile->getFileGroupIndex();

            $expectedName = is_scalar($expected[$fileGroupKey]['name']) ?
                $expected[$fileGroupKey]['name'] :
                $expected[$fileGroupKey]['name'][$fileGroupIndex];

            $expectedType = is_scalar($expected[$fileGroupKey]['type']) ?
                $expected[$fileGroupKey]['type'] :
                $expected[$fileGroupKey]['type'][$fileGroupIndex];

            $expectedError = is_scalar($expected[$fileGroupKey]['error']) ?
                $expected[$fileGroupKey]['error'] :
                $expected[$fileGroupKey]['error'][$fileGroupIndex];

            $expectedSize = is_scalar($expected[$fileGroupKey]['size']) ?
                $expected[$fileGroupKey]['size'] :
                $expected[$fileGroupKey]['size'][$fileGroupIndex];

            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
            $this->assertEquals($expectedName, $uploadedFile->getClientFilename());
            $this->assertEquals($expectedType, $uploadedFile->getClientMediaType());
            $this->assertEquals($expectedError, $uploadedFile->getError());
            $this->assertEquals($expectedSize, $uploadedFile->getSize());
        }
        $_FILES = [];
    }

    #[DataProvider('manuallyUploadFilesProvider')]
    public function testUploadedFilesByManuallyUploadingThem(array $filesPaths, array $filesNames): void
    {
        foreach ($filesPaths as $i => $filePath) {
            $resource = fopen($filePath, 'r+');
            $stream = new Stream($resource);
            $uploadedFiles[$i] = new UploadedFile($stream, $filesNames[$i]);
            fclose($resource);
        }

        $serverRequest = new ServerRequest('POST');
        $serverRequest = $serverRequest->withUploadedFiles($uploadedFiles);

        foreach ($serverRequest->getUploadedFiles() as $i => $uploadedFile) {
            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
            $this->assertEquals($filesNames[$i], $uploadedFile->getClientFilename());
        }
    }

    public static function singleFileUploadMockFromPhpFilesGlobalVarPriveder(): array
    {
        return ['single-file-upload-mock' => [["file1" => self::FILE1_UPLOAD_MOCK]]];
    }

    public static function multipleFilesUploadMockFromPhpFilesGlobalVarProvider(): array
    {
        return [
            'multiple-files-upload-mock' => [
                ["file1" => self::FILE1_UPLOAD_MOCK, "file2" => self::FILE2_UPLOAD_MOCK],
            ]
        ];
    }

    public static function manuallyUploadFilesProvider(): array
    {
        return [
            'single-dummy-file' => [
                [__DIR__ . '/StreamDummyFile.txt'],
                ['StreamDummyFile.txt']
            ],
            'multiple-dummy-file' => [
                [__DIR__ . '/StreamDummyFile.txt', __DIR__ . '/StreamDummyFile.log'],
                ['StreamDummyFile.txt', 'StreamDummyFile.log']
            ]
        ];
    }
}

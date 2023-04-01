<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class MessageTest extends TestCase
{
    private ?MessageInterface $message;
    private ?StreamInterface $stream;
    const DEFAULT_PROTOCOL = '1.0';
    const NEW_PROTOCOL = '1.1';

    protected function setUp(): void
    {
        $this->message = new Message();
        $this->stream = new Stream(fopen('php://temp', 'r+'));
    }

    protected function tearDown(): void
    {
        $this->message = null;
        $this->stream = null;
    }

    #[DataProvider('messageProtocolsProvider')]
    public function testProtocolVersion(string $protocol): void
    {
        $message = $this->message;

        $this->assertEquals('1.0', $message->getProtocolVersion());

        $message = $message->withProtocolVersion($protocol);
        $this->assertEquals($protocol, $message->getProtocolVersion());
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testGetHeaders(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;

        $this->assertEquals($messageInfo->getHeaders, $message->getHeaders());
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testHasHeader(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;

        $headersSubject = $messageInfo->headersToTest;

        foreach ($headersSubject as $i => $headerSubject) {
            $this->assertEquals($messageInfo->hasHeader[$i], $message->hasHeader($headerSubject));
        }
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testGetHeader(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;
        
        $headersSubject = $messageInfo->headersToTest;

        foreach ($headersSubject as $i => $headerSubject) {
            $this->assertEquals($messageInfo->getHeader[$i], $message->getHeader($headerSubject));
        }
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testGetHeaderLine(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;
        
        $headersSubject = $messageInfo->headersToTest;

        foreach ($headersSubject as $i => $headerSubject) {
            $this->assertEquals($messageInfo->getHeaderLine[$i], $message->getHeaderLine($headerSubject));
        }
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testWithHeader(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;
        
        $headersSubject = $messageInfo->getHeaders;
        $headersSubject['content-type'] = ['text-html'];

        $message = $message->withHeader('content-type', 'text-html');

        $this->assertEquals($headersSubject, $message->getHeaders());
        $this->assertEquals(true, $message->hasHeader('content-type'));
        $this->assertEquals(['text-html'], $message->getHeader('content-type'));
        $this->assertEquals('text-html', $message->getHeaderLine('content-type'));
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testWithAddedHeader(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;
        
        $headersToTest = $messageInfo->headersToTest;
        $headers = $messageInfo->getHeaders;
        $headers['content-type'][] = 'text-html';

        $message = $message->withAddedHeader('content-type', 'text-html');

        $this->assertEquals($headers, $message->getHeaders());
        foreach ($headersToTest as $headerToTest) {
            $expected = isset($headers[$headerToTest]) ? $headers[$headerToTest] : [];
            $this->assertEquals($expected, $message->getHeader($headerToTest));
        }
    }

    #[DataProvider('emptyHeadersProvider')]
    #[DataProvider('singleHeaderProvider')]
    #[DataProvider('multipleHeaderProvider')]
    public function testWithoutHeader(object $messageInfo): void
    {
        /** @var MessageInterface $message */
        $message = $messageInfo->message;

        $headersSubject = $messageInfo->getHeaders;
        unset($headersSubject['content-type']);

        $message = $message->withoutHeader('content-type');

        $this->assertEquals($headersSubject, $message->getHeaders());
        $this->assertEquals(false, $message->hasHeader('content-type'));
        $this->assertEquals([], $message->getHeader('content-type'));
        $this->assertEquals('', $message->getHeaderLine('content-type'));
    }

    public function testBody(): void
    {
        $message = $this->message;
        $stream = $this->stream;

        $michaelFamousSaying = 'You miss 100% of the shots you don\'t take';
        $stream->write($michaelFamousSaying);

        $message = $message->withBody($stream);
        $this->assertEquals($michaelFamousSaying, $message->getBody());
    }

    /**
     * @return string[]
     */
    public static function messageProtocolsProvider(): array
    {
        return [
            'default-protocol' => [self::DEFAULT_PROTOCOL],
            'new-protocol' => [self::NEW_PROTOCOL]
        ];
    }

    public static function emptyHeadersProvider(): array
    {
        return ['empty-headers' => [self::buildMessageHeadersInfoClass()]];
    }

    public static function singleHeaderProvider(): array
    {
        $contentTypeKey = 'content-type';
        $contentTypeValue = ['application/json'];

        $userAgentKey = 'user-agent';
        $userAgentValue = [];

        $message = new Message();
        $message = $message->withHeader($contentTypeKey, $contentTypeValue[0]);

        $headersToTest = [$contentTypeKey, $userAgentKey];
        $headersValues = [$contentTypeKey => $contentTypeValue, $userAgentKey => $userAgentValue];
        $hasHeader = [true, false];
        $getHeaders = [$contentTypeKey => $contentTypeValue];
        $getHeader = [$contentTypeValue, $userAgentValue];
        $getHeaderLine = [$contentTypeValue[0], ''];

        return [
            'single-headers' => [
                self::buildMessageHeadersInfoClass(
                    $message,
                    $headersToTest,
                    $headersValues,
                    $hasHeader,
                    $getHeaders,
                    $getHeader,
                    $getHeaderLine
                )
            ]
        ];
    }

    public static function multipleHeaderProvider(): array
    {
        $contentTypeKey = 'content-type';
        $contentTypeValue = ['application/json'];

        $userAgentKey = 'user-agent';
        $userAgentValue = ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)'];

        $message = new Message();
        $message = $message->withHeader($contentTypeKey, $contentTypeValue[0]);
        $message = $message->withHeader($userAgentKey, $userAgentValue[0]);

        $headersToTest = [$contentTypeKey, $userAgentKey];
        $headersValues = [$contentTypeKey => $contentTypeValue, $userAgentKey => $userAgentValue];
        $hasHeader = [true, true];
        $getHeaders = [$contentTypeKey => $contentTypeValue, $userAgentKey => $userAgentValue];
        $getHeader = [$contentTypeValue, $userAgentValue];
        $getHeaderLine = [$contentTypeValue[0], $userAgentValue[0]];

        return [
            'multiple-headers' => [
                self::buildMessageHeadersInfoClass(
                    $message,
                    $headersToTest,
                    $headersValues,
                    $hasHeader,
                    $getHeaders,
                    $getHeader,
                    $getHeaderLine
                )
            ]
        ];
    }

    private static function buildMessageHeadersInfoClass(
        Message $message = new Message(),
        array $headersToTest = ['content-type'],
        array $headersValues = [[]],
        array $hasHeader = [false],
        array $getHeaders = [],
        array $getHeader = [[]],
        array $getHeaderLine = ['']
    ): object {

        $messageInfo = new class(
            $message,
            $headersToTest,
            $headersValues,
            $hasHeader,
            $getHeaders,
            $getHeader,
            $getHeaderLine
        )
        {
            public function __construct(
                public Message $message,
                public array $headersToTest,
                public array $headersValues,
                public array $hasHeader,
                public array $getHeaders,
                public array $getHeader,
                public array $getHeaderLine
            ) {
            }
        };

        return $messageInfo;
    }
}

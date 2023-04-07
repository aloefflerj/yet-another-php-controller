<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Response;
use Aloefflerj\YetAnotherController\Controller\Http\StatusCodeToReason;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
    use StatusCodeToReason;

    #[DataProvider('responseCasesProvider')]
    public function testResponseIsCorrectlyInstantiated(
        int $status,
        array $headers,
        StreamInterface $body,
        string $version,
        string $reason
    ): void {
        $response = new Response(
            $status,
            $headers,
            $body,
            $version,
            $reason
        );

        // $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(Response::class, $response);
    }

    #[DataProvider('responseCasesProvider')]
    public function testResponseStatusCodeIsCorrectlySetted(
        int $status,
        array $headers,
        StreamInterface $body,
        string $version,
        string $reason
    ): void {
        $response = new Response(
            $status,
            $headers,
            $body,
            $version,
            $reason
        );
        $this->assertEquals($status, $response->getStatusCode());
    }

    #[DataProvider('responseCasesProvider')]
    public function testResponseReasonPhraseIsCorrectlySetted(
        int $status,
        array $headers,
        StreamInterface $body,
        string $version,
        string $reason
    ): void {
        $response = new Response(
            $status,
            $headers,
            $body,
            $version,
            $reason
        );

        if (empty($reason)) {
            $reason = $this->getReasonPhraseByCode($status);
        }

        $this->assertEquals($reason, $response->getReasonPhrase());
    }

    #[DataProvider('responseCasesProvider')]
    public function testStatusDoesNotExists(
        int $status,
        array $headers,
        StreamInterface $body,
        string $version,
        string $reason
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status code');

        $response = new Response(
            100000,
            $headers,
            $body,
            $version,
            $reason
        );
    }

    #[DataProvider('responseCasesProvider')]
    public function testWithStatusCodeWorksCorrectly(
        int $status,
        array $headers,
        StreamInterface $body,
        string $version,
        string $reason
    ): void {
        $response = new Response(
            $status,
            $headers,
            $body,
            $version,
            $reason
        );

        $response = $response->withStatus($status);
        $expectedReasonPhrase = $this->getReasonPhraseByCode($status);
        
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($expectedReasonPhrase, $response->getReasonPhrase());

        $response = $response->withStatus($status, $reason);

        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals($reason, $response->getReasonPhrase());
    }

    public static function responseCasesProvider(): array
    {
        return [
            'simple-response-case' => [
                'statusCode' => 200,
                'headers' => ['content-type' => ['application/json']],
                'body' => Stream::buildFromString(
                    json_encode((object)['user' => 'holy jesus'])
                ),
                'version' => '1.1',
                'reason' => 'OK'
            ],
            'response-without-reason-case' => [
                'statusCode' => 404,
                'headers' => ['content-type' => ['application/json']],
                'body' => Stream::buildFromString(
                    json_encode((object)['user' => 'holy jesus'])
                ),
                'version' => '1.1',
                'reason' => ''
            ]
        ];
    }
}

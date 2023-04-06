<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Response;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends TestCase
{
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

    public static function responseCasesProvider(): array
    {
        return ['simple-response-case' => [
            'statusCode' => 200,
            'headers' => ['content-type' => ['application/json']],
            'body' => Stream::buildFromString(
                json_encode((object)['user' => 'holy jesus'])
            ),
            'version' => '1.1',
            'reason' => 'OK'
        ]];
    }
}

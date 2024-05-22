<?php

namespace Testes\Http;

use Hugo\Psr7\Http\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    private ResponseInterface $response;
    private int $statusCode = 200;

    protected function setUp(): void
    {
        $this->response = new Response($this->statusCode);
    }

    public function testGetStatusCodeDeveRetornarInteiro()
    {
        $statusCode = $this->response->getStatusCode();

        self::assertIsInt($statusCode);
        self::assertEquals($this->statusCode, $statusCode);
    }

    public function testWithStatusCodeDeveRetornarNovaInstancia()
    {
        $responseNovo = $this->response->withStatus(404);

        self::assertNotEquals($responseNovo, $this->response);
        self::assertEquals('Not Found', $responseNovo->getReasonPhrase());
    }

    public function testGetreasonPhraseDeveRetornarString()
    {
        $reasonPhrase = $this->response->getReasonPhrase();
        
        self::assertIsString($reasonPhrase);
        self::assertEquals("OK", $reasonPhrase);
    }
}

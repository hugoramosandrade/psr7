<?php

namespace Testes\Http;

use Hugo\Psr7\Http\Request;
use Hugo\Psr7\Http\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    private RequestInterface $request;
    private string $requestTarget;
    private string $uri = "http://localhost:3001/?msg=teste&type=success";
    private array $headers = ["NAME" => "value"];
    private string $body = '{"name": "user", "email": "user@email.com.br"}';

    protected function setUp(): void
    {
        $this->request = new Request('POST', $this->uri, $this->headers, $this->body);
    }

    public function testGetRequestTargetDeveRetornarString()
    {
        $requestTarget = $this->request->getRequestTarget();

        self::assertIsString($requestTarget);
        self::assertStringContainsString($this->request->getUri()->getQuery(), $requestTarget);
    }

    public function testWithRequestTargetDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withRequestTarget('/rota');

        self::assertNotEquals($requestNovo, $this->request);
        self::assertInstanceOf(RequestInterface::class, $requestNovo);
    }

    public function testGetMethodDeveRetornarString()
    {
        $method = $this->request->getMethod();
        
        self::assertIsString($method);
        self::assertEquals('POST', $method);
    }
    
    public function testWithMethodDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withMethod('GET');

        self::assertNotEquals($requestNovo, $this->request);
        self::assertIsString($requestNovo->getMethod());
        self::assertEquals('GET', $requestNovo->getMethod());
    }

    public function testGetUriDeveRetornarInstanciaDeUriInterface()
    {
        $uri = $this->request->getUri();

        self::assertInstanceOf(UriInterface::class, $uri);
    }

    public function testWithUriDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withUri(new Uri("http://localhost:3001/?msg=teste"));

        self::assertNotEquals($requestNovo, $this->request);
        self::assertInstanceOf(RequestInterface::class, $requestNovo);
    }
}

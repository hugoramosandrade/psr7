<?php

namespace Testes\Http;

use Psr\Http\Message\{ServerRequestInterface, UploadedFileInterface};
use Hugo\Psr7\Http\{ServerRequest, UploadedFile};
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    private ServerRequestInterface $request;
    private array $headers = [
        "NAME" => "value"
    ];
    private array $stream = [
        "name" => "teste",
        "email" => "teste@email.com"
    ];
    /** @var UploadedFileInterface[] */
    private array $uploadedFiles;
    private array $cookieParams = [
        "Coockie_1" => "valor1",
        "Coockie_2" => "valor2"
    ];

    protected function setUp(): void
    {
        $this->uploadedFiles = [
            new UploadedFile(__DIR__."../file/teste.csv", 1, 0, "teste.csv", "text/csv")
        ];
        $this->request = new ServerRequest(
            'POST',
            'http://localhost:3001/',
            $this->headers,
            json_encode($this->stream),
            '1.1',
            $this->headers,
            $this->uploadedFiles,
            $this->cookieParams
        );
    }

    public function testGetServerParamsDeveRetornarArray()
    {
        $serverParams = $this->request->getServerParams();

        self::assertIsArray($serverParams);
        self::assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys(
            $this->headers, 
            $serverParams, 
            array_keys($this->headers)
        );
    }

    public function testGetCookieParamsDeveRetornarArrya()
    {
        $cookieParams = $this->request->getCookieParams();

        self::assertIsArray($cookieParams);
        self::assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys(
            $this->cookieParams, 
            $cookieParams, 
            array_keys($this->cookieParams)
        );
    }

    public function testWithCookieParamsDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withCookieParams(['Cookie_3' => 'valor_3']);

        self::assertNotEquals($requestNovo, $this->request);
    }
}

<?php

namespace Testes\Http;

use Psr\Http\Message\{ServerRequestInterface, UploadedFileInterface};
use Hugo\Psr7\Http\{ServerRequest, UploadedFile};
use PHPUnit\Framework\TestCase;
use stdClass;

class ServerRequestTest extends TestCase
{
    private ServerRequestInterface $request;
    private array $headers = [
        "NAME" => ["value"]
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
            new UploadedFile(__DIR__."/../file/teste.csv", 1, 0, "teste.csv", "text/csv")
        ];
        $this->request = new ServerRequest(
            'POST',
            'http://localhost:3001/?msg=teste',
            $this->headers,
            json_encode($this->stream),
            '1.1',
            $this->headers,
            $this->uploadedFiles,
            $this->cookieParams
        );

        $request = $this->request->withAttribute('Teste', 'valor do atributo');
        $this->request = $request->withAttribute('Teste2', 'valor2');
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

    public function testGetQueryParamsDeveRetornarUmArray()
    {
        $queryParams = $this->request->getQueryParams();

        self::assertIsArray($queryParams);
        self::assertArrayHasKey('msg', $queryParams);
        self::assertEquals('teste', $queryParams['msg']);
    }

    public function testWithQueryParamsDeveRetornarUmaNovaInstancia()
    {
        $requestNovo = $this->request->withQueryParams(['msg' => 'teste2']);

        self::assertNotEquals($requestNovo, $this->request);
        self::arrayHasKey('msg', $requestNovo->getQueryParams());
        self::assertEquals('teste2', $requestNovo->getQueryParams()['msg']);
    }

    public function testGetUploadedFilesDeveRetornarArray()
    {
        $uploadedFiles = $this->request->getUploadedFiles();

        self::assertIsArray($uploadedFiles);
        self::assertInstanceOf(UploadedFileInterface::class, $uploadedFiles[0]);
    }

    public function testWithUploadedFilesDeveRetornarUmaNovaInstancia()
    {
        $uploadedFiles = [
            new UploadedFile(__DIR__."/../file/teste2.csv", 1, 0, "teste.csv", "text/csv")
        ];
        $requestNovo = $this->request->withUploadedFiles($uploadedFiles);

        self::assertNotEquals($requestNovo, $this->request);
        self::assertInstanceOf(UploadedFileInterface::class, $requestNovo->getUploadedFiles()[0]);
    }

    public function testGetParsedBodyDeveRetornarObjeto()
    {
        $parsedBody = $this->request->getParsedBody();

        self::assertIsArray($parsedBody);
    }

    public function testWithParsedBodyDeveRetornarUmaNovaInstancia()
    {
        $body = ["name" => "user", "email" => "user@email.com"];
        $requestNovo = $this->request->withParsedBody($body);

        self::assertNotEquals($requestNovo, $this->request);
    }

    public function testGetAttributesDeveRetornarArray()
    {
        $attributes = $this->request->getAttributes();

        self::assertIsArray($attributes);
        self::assertEquals('valor do atributo', $attributes['Teste']);
    }

    public function testGetAttributeDeveRetornarString()
    {
        $atribute = $this->request->getAttribute('Teste');
        self::assertEquals('valor do atributo', $atribute);
        self::assertNull($this->request->getAttribute('Teste3'));
    }

    public function testWithAttributeDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withAttribute('Novo', 'valor novo');

        self::assertNotEquals($requestNovo, $this->request);
    }

    public function testWithoutAttributeDeveRetornarNovaInstancia()
    {
        $requestNovo = $this->request->withoutAttribute('Teste');

        self::assertNotEquals($requestNovo, $this->request);
        self::assertArrayNotHasKey('Teste', $requestNovo->getAttributes());
    }
}

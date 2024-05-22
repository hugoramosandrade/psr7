<?php

namespace Testes\Http;

use Hugo\Psr7\Http\Message;
use Hugo\Psr7\Http\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class MessageTest extends TestCase
{
    private MessageInterface $message;
    private array $headers = ["NAME" => ["value1", "value2"], 'HEADER2' => ['valor1', 'valor2']];
    private string $stream = '{"name": "user", "email": "user@email.com.br"}';

    protected function setUp(): void
    {
        $this->message = new Message($this->headers, $this->stream);
    }

    public function testGetProtocolVersionDeveRetornarString()
    {
        $protocolVersion = $this->message->getProtocolVersion();

        self::assertIsString($protocolVersion);
        self::assertEquals('1.1', $protocolVersion);
    }

    public function testWithProtocolVersionDeveRetornarNovaInstancia()
    {
        $messageNova = $this->message->withProtocolVersion('1.0');

        self::assertNotEquals($messageNova, $this->message);
        self::assertEquals('1.0', $messageNova->getProtocolVersion());
    }

    public function testGetHeadersDeveRetornarArray()
    {
        $headers = $this->message->getHeaders();

        self::assertIsArray($headers);
        self::assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys(
            $this->headers,
            $headers,
            array_keys($this->headers)
        );
    }

    public function testHasHeaderDeveRetornarBoleano()
    {
        $hasHeader = $this->message->hasHeader('NAme');

        self::assertIsBool($hasHeader);
        self::assertTrue($hasHeader);
    }

    public function testGetHeaderLineDeveRetornarString()
    {
        $headerLine = $this->message->getHeaderLine('Name');

        self::assertIsString($headerLine);
        self::assertEquals(implode(', ', $this->headers['NAME']), $headerLine);
    }

    public function testWithHeaderDeveRetornarNovaInstancia()
    {
        $messageNova = $this->message->withHeader('Novo', 'teste');

        self::assertNotEquals($messageNova, $this->message);
        self::assertEquals('teste', $messageNova->getHeaderLine('novo'));
    }

    public function testWithAddedHeaderDeveRetornarNovaInstancia()
    {
        $messageNova = $this->message->withAddedHeader('Novo', 'teste');

        self::assertNotEquals($messageNova, $this->message);
        self::assertEquals('teste', $messageNova->getHeaderLine('novo'));
    }

    public function testWithoutHeaderDeveRetornarNovaInstancia()
    {
        $messageNova =  $this->message->withoutHeader('header2');

        self::assertNotEquals($messageNova, $this->message);
        self::assertTrue($this->message->hasHeader('header2'));
        self::assertFalse($messageNova->hasHeader('header2'));
    }

    public function testGetBodyDeveRetornarInstanciaDeStreamInterface()
    {
        $body = $this->message->getBody();
        self::assertInstanceOf(StreamInterface::class, $body);
        self::assertIsString($body->getContents());
    }

    public function testWithBodyDeveRetornarNovaInstancia()
    {
        $stream = Stream::create('{"campo": "valor teste"}');
        $messageNova = $this->message->withBody($stream);

        self::assertNotEquals($messageNova, $this->message);
    }
}

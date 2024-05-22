<?php

namespace Testes\Http;

use Hugo\Psr7\Http\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamTest extends TestCase
{
    private StreamInterface $stream;
    private string $body = '{"name": "Teste"}';

    protected function setUp(): void
    {
        $this->stream = Stream::create($this->body);
    }

    public function testClose()
    {
        $clone = clone $this->stream;

        $clone->close();
        self::assertFalse($clone->isSeekable());
        self::assertFalse($clone->isWritable());
        self::assertFalse($clone->isReadable());
    }

    public function testDetachDeveRetornarResource()
    {
        $stream = clone $this->stream;
        $resource = $stream->detach();

        self::assertIsResource($resource);
        self::assertFalse($stream->isSeekable());
        self::assertFalse($stream->isWritable());
        self::assertFalse($stream->isReadable());
    }

    public function testGetSizeDeveRetornarInteiro()
    {
        $size = $this->stream->getSize();

        self::assertIsInt($size);
    }

    public function testTellDeveRetornarInteiro()
    {
        $position = $this->stream->tell();

        self::assertIsInt($position);
    }

    public function testEofDeveRetornarBool()
    {
        $isEof = $this->stream->eof();

        self::assertFalse($isEof);
    }

    public function testRewind()
    {
        $this->stream->rewind();

        $this->expectNotToPerformAssertions();
    }

    public function testWriteDeveRetornarInteiro()
    {
        $stream = clone $this->stream;
        $int = $stream->write('"teste');

        self::assertIsInt($int);
    }

    public function testReadDeveRetornarString()
    {
        $content = $this->stream->read($this->stream->getSize());
        $this->stream->rewind();

        self::assertIsString($content);
    }

    public function testGetContentsDeveRetornarString()
    {
        $content = $this->stream->getContents();
        $this->stream->rewind();

        self::assertIsString($content);
    }

    public function testGetMetaDeveRetornarArray()
    {
        $meta = $this->stream->getMetadata();

        self::assertIsArray($meta);
    }
}

<?php

namespace Testes\Http;

use Hugo\Psr7\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileTest extends TestCase
{
    private UploadedFileInterface $uploadedFile;
    private string $path = __DIR__."/../file/teste.csv";
    private int $size = 1;
    private int $error = 0;
    private string $fileName = 'teste.csv';
    private string $mediaType = 'text/csv';

    protected function setUp(): void
    {
        $this->uploadedFile = new UploadedFile(
            $this->path,
            $this->size,
            $this->error,
            $this->fileName,
            $this->mediaType
        );
    }

    public function testGetStreamDeveRetornarInstanciaStreamInterface()
    {
        $stream = $this->uploadedFile->getStream();

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertIsString($stream->getContents());
    }

    public function testMoveTo()
    {
        $this->uploadedFile->moveTo(__DIR__."/../file/teste_movido.csv");
        $uploadedFile = new UploadedFile(
            __DIR__."/../file/teste_movido.csv",
            $this->size,
            $this->error,
            'teste_movido.csv',
            $this->mediaType
        );
        $uploadedFile->moveTo($this->path);

        $this->expectNotToPerformAssertions();
    }

    public function testGetSizeDeveRetornarInteiro()
    {
        $size = $this->uploadedFile->getSize();

        self::assertIsInt($size);
        self::assertEquals($this->size, $size);
    }

    public function testGetErrorDeveRetornarInteiro()
    {
        $error = $this->uploadedFile->getError();

        self::assertIsInt($error);
        self::assertEquals($this->error, $error);
    }

    public function testGetClientFileNameDeveRetornarString()
    {
        $fileName = $this->uploadedFile->getClientFilename();

        self::assertIsString($fileName);
        self::assertEquals($this->fileName, $fileName);
    }

    public function testClientMediaTypeDeveRetornarString()
    {
        $mediaType = $this->uploadedFile->getClientMediaType();

        self::assertIsString($mediaType);
        self::assertEquals($this->mediaType, $mediaType);
    }
}

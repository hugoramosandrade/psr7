<?php

namespace Hugo\Psr7\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    private const ERRORS = [
        \UPLOAD_ERR_OK => 1,
        \UPLOAD_ERR_INI_SIZE => 1,
        \UPLOAD_ERR_FORM_SIZE => 1,
        \UPLOAD_ERR_PARTIAL => 1,
        \UPLOAD_ERR_NO_FILE => 1,
        \UPLOAD_ERR_NO_TMP_DIR => 1,
        \UPLOAD_ERR_CANT_WRITE => 1,
        \UPLOAD_ERR_EXTENSION => 1,
    ];

    private ?string $file = null;
    private bool $moved = false;
    private ?StreamInterface $stream = null;

    public function __construct(
        StreamInterface|string $streamOrFile,
        private int $size,
        private int $error,
        private ?string $clientFilename = null,
        private ?string $clientMediaType = null
    )
    {
        if (!isset(self::ERRORS[$error])) {
            throw new \InvalidArgumentException('Upload file error status must be one of the "UPLOAD_ERR_*" constants');
        }

        if ($this->error === \UPLOAD_ERR_OK) {
            if (is_string($streamOrFile)) {
                $this->file = $streamOrFile;
            } else if ($streamOrFile instanceof StreamInterface) {
                $this->stream = $streamOrFile;
            }
        }
    }

    /** @throws \RuntimeException if is moved or not ok */
    private function validateActive(): void
    {
        if ($this->error !== \UPLOAD_ERR_OK)
            throw new \RuntimeException("Cannot retrieve stream due to upload error");

        if ($this->moved) 
            throw new \RuntimeException("Cannot retrien stream after it has already been moved");
    }

    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if (!is_null($this->stream)) return $this->stream;

        if (false === $resource = fopen($this->file, 'r')) {
            throw new \RuntimeException("The file '{$this->file}' cannot be opened: ". (error_get_last()['message'] ?? ''));
        }

        return Stream::create($resource);
    }

    public function moveTo(string $targetPath): void
    {
        $this->validateActive();
        if ($targetPath === '')
            throw new \InvalidArgumentException("Invalid path provided. Path must be a non-empty string");

        if (!is_null($this->file)) {
            $this->moved = PHP_SAPI === 'cli' ? rename($this->file, $targetPath) : move_uploaded_file($this->file, $targetPath);

            if (!$this->moved)
                throw new \RuntimeException("Uploaded file could not be moved to {$targetPath}: " . (error_get_last()['message'] ?? ''));
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }

            if (false === $resource = fopen($targetPath, 'w')) {
                throw new \RuntimeException("The file {$targetPath}: " . (error_get_last()['message'] ?? ''));
            }

            $dest = Stream::create($resource);

            while ($stream->eof()) {
                if (!$dest->write($stream->read(1048576))) {
                    break;
                }
            }

            $this->moved = true;
        }
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}

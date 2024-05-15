<?php

namespace Hugo\Psr7\Http;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    /** @var resource|null */
    private $stream;
    private bool $seekable;
    private bool $readable;
    private bool $writable;
    private mixed $uri;
    private ?int $size = null;
    private const READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    public function __construct($body)
    {
        if (!is_resource($body)) {
            throw new \InvalidArgumentException("The argument passed must be a resource");
        }

        $this->stream = $body;
        $meta = stream_get_meta_data($this->stream);
        $this->seekable = $meta['seekable'] && fseek($this->stream, 0, SEEK_CUR);
        $this->readable = isset(self::READ_WRITE_HASH['read'][$meta['mode']]);
        $this->writable = isset(self::READ_WRITE_HASH['write'][$meta['mode']]);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
             return $this->getContents();
        } catch (\Throwable $e) {
            if (is_array($errorHandler = set_error_handler('var_dump'))) {
                $errorHandler = $errorHandler[0] ?? null;
            }
            restore_error_handler();
             return '';
        }
        return '';
    }

    public static function create($body = ''): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_string($body)) {
            $resource = fopen('php://memory', 'r+');
            fwrite($resource, $body);
            fseek($resource, 0);
            $body = $resource;
        }

        return new self($body);
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            if (is_resource($this->stream)) {
                fclose($this->stream);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    private function getUri()
    {
        if ($this->uri !== false) {
            $this->uri = $this->getMetadata('uri') ?? false;
        }

        return $this->uri;
    }

    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        // clear cahce
        if ($uri = $this->getUri()) {
            clearstatcache(true, $uri);
        }

        $stats = fstat($this->stream);
        if(isset($stats['size'])) {
            return $stats['size'];
        }

        return null;
    }

    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (false === $result = ftell($this->stream)) {
            throw new \RuntimeException("Unable to determine stream position: ".(error_get_last()['message']?? null));
        }

        return $result;
    }

    public function eof(): bool
    {
        return !isset($this->stream) || feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }

        if (!$this->seekable) {
            throw new \RuntimeException("Stream is not seekable");
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException("Unable to seek to stream position {$offset} with whence ".var_export($whence, true));
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream detached");
        }

        if (!$this->writable) {
            throw new RuntimeException("Cannot write to a non-wriitable strem");
        }

        $this->size = null;

        if (false === $result = fwrite($this->stream, $string)) {
            throw new \RuntimeException("Unable to write to stream: " . (error_get_last()['message'] ?? ''));
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Stream is detached');
        }

        if (!$this->readable) {
            throw new \RuntimeException("Cannot read from a non-readable stream");
        }

        if (false === $result = fread($this->stream, $length)) {
            throw new \RuntimeException("Unable to read from stream: " . (error_get_last()['message'] ?? ''));
        }

        return $result;
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }

        $exeption = null;

        set_error_handler(static function($type, $message) use (&$exeption) {
            throw $exeption = new \RuntimeException("Unable to read stream contents: " . $message);
        });

        try {
            return stream_get_contents($this->stream);
        } catch (\Throwable $e) {
            throw $e === $exeption ? $e : new \RuntimeException("Unable to read stream contents: ". $e->getMessage(), 0, $e);
        } finally {
            restore_error_handler();
        }
    }

    public function getMetadata(?string $key = null)
    {
        if ($key !== null && !is_string($key)) {
            throw new \InvalidArgumentException("Metadata key must be a string");
        }

        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}

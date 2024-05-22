<?php

namespace Hugo\Psr7\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    protected array $headers = [];
    protected array $headerNames = [];
    protected string $protocol = '1.1';
    protected ?StreamInterface $stream = null;

    public function __construct(
        ?array $headers = [],
        string|StreamInterface|Null $stream = null
    )
    {
        $this->setHeaders($headers);
        if ($stream instanceof StreamInterface) {
            $this->stream = $stream;
        } elseif (is_string($stream)) {
            $this->stream = Stream::create($stream);
        }
    }

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }
        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headerNames[strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]);
    }

    public function getHeader(string $name): array
    {
        $header = strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $normalized = strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $name;
        if (is_string($value)) {
            $new->headers[$name] = [$value];
        } elseif(is_array($value)) {
            $new->headers[$name] = $value;
        } else {
            throw new \InvalidArgumentException("The value argument must be type of array or string, " . gettype($value) . " given");
        }

        return $new;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $new = clone $this;
        if (is_string($value)) {
            $new->setHeaders([$name => [$value]]);
        } else if (is_array($value)){
            $new->setHeaders([$name => $value]);
        } else {
            throw new \InvalidArgumentException("The value argument must be type of array or string, " . gettype($value) . " given");
        }

        return $new;
    }

    protected function setHeaders( array $headers):void
    {
        foreach ($headers as $header => $value) {
            if (is_int($header)) {
                $header = (string) $header;
            }

            $normalized = strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $normalized = strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];
        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if ($this->stream === null) {
            $this->stream = Stream::create();
        }

        return $this->stream;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;
        return $new;
    }
}

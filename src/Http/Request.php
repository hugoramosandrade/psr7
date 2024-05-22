<?php

namespace Hugo\Psr7\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    protected ?string $requestTarget = null;
    protected UriInterface $uri;

    public function __construct(
        protected string $method,
        UriInterface|string $uri,
        array $headers = [],
        string|StreamInterface|null $body = null,
        string $version = '1.1'
    )
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        if ($body instanceof StreamInterface) {
            $this->stream = $body;
        } else if ($body !== '' && $body !== null) {
            $this->stream = Stream::create($body);
        }
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) return $this->requestTarget;

        if ('' === $target = $this->uri->getPath()) $target = '/';

        if ($this->uri->getQuery() !== '') $target .= '?' . $this->uri->getQuery();

        return $target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if (preg_match('#\s#', $requestTarget))
            throw new \InvalidArgumentException("Invalid request target provided. Cannot contain whitespace");

        $new = clone $this;
        $new->requestTarget = $requestTarget;

        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $new = clone $this;
        $new->method = $method;

        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri) return $this;

        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost || !$this->hasHeader('Host'))
            $new->updateHostFromUri();

        return $new;
    }

    protected function updateHostFromUri(): void
    {
        if ('' === $host = $this->uri->getHost()) {
            return;
        }

        if (null !== ($port = $this->uri->getPort())) {
            $host .= ':' . $port;
        }

        if (isset($this->headerNames['host'])) {
            $header = $this->headerNames['host'];
        } else {
            $this->headerNames['host'] = $header = 'Host';
        }

        $this->headers = [$header => [$host]] + $this->headers;
    }
}

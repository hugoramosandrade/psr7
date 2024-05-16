<?php

namespace Hugo\Psr7\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    private array $attributes = [];
    private array $coockieParams = [];
    private array|object|null $parsedBody;
    private array $queryParams = [];
    /** @var UploadedFileInterface[] */
    private array $uploadedFiles = [];

    public function __construct(
        string $method,
        string|UriInterface $uri,
        array $headers = [],
        string|StreamInterface|null $body = null,
        string $version = '1.1',
        private array $serverParams = []
    )
    {
        if (is_string($uri)) $uri = new Uri($uri);
        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;
        parse_str($uri->getQuery(), $this->queryParams);

        if (!$this->hasHeader('Host')) $this->updateHostFromUri();
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->coockieParams;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->coockieParams = $cookies;
        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /** @return UploadedFileInterface[] */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        if (!is_array($data) && !is_object($data) && !is_null($data))
            throw new \InvalidArgumentException('First parameter to withParsedBody MUST be object, array or null');

        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes))
            return $default;

        return $this->attributes[$name];
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $new  = clone $this;
        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        if (!isset($this->attributes[$name])) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$name]);

        return $new;
    }
}

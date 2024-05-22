<?php

namespace Testes\Http;

use Hugo\Psr7\Http\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriTest extends TestCase
{
    private UriInterface $uri;
    private string $url = 'http://localhost:3001/?msg=teste';

    protected function setUp(): void
    {
        $this->uri = new Uri($this->url);
    }

    public function testGetSchemeDeveRetornarString()
    {
        $scheme = $this->uri->getScheme();

        self::assertIsString($scheme);
    }

    public function testGetAuthorityDeveretornarString()
    {
        $authority = $this->uri->getAuthority();

        self::assertIsString($authority);
    }

    public function testGetUserInfoDeveretornarString()
    {
        $userInfo = $this->uri->getUserInfo();

        self::assertIsString($userInfo);
    }

    public function testGetHostDeveretornarString()
    {
        $host = $this->uri->getHost();

        self::assertIsString($host);
    }

    public function testGetPortDeveretornarInt()
    {
        $port = $this->uri->getPort();
        self::assertIsInt($port);
    }
}

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

    public function testGetPathDeveRetornarString()
    {
        $path = $this->uri->getPath();

        self::assertIsString($path);
        self::assertEquals('/', $path);
    }

    public function testGetQueryDeveRetornarString()
    {
        $query = $this->uri->getQuery();

        self::assertIsString($query);
        self::assertEquals('msg=teste', $query);
    }

    public function testGetFragmentDeveRetornarString()
    {
        $fragment = $this->uri->getFragment();

        self::assertIsString($fragment);
    }

    public function testWithSchemeDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withScheme('https');

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
    }

    public function testWithUserInfoDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withUserInfo(' ');
        
        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
    }

    public function testWithHostDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withHost('127.0.0.1');

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
        self::assertEquals('127.0.0.1', $uriNovo->getHost());
    }

    public function testWithPortDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withPort("8080");

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
        self::assertEquals('8080', $uriNovo->getPort());
    }

    public function testWithPathDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withPath("/rota");

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
        self::assertEquals('/rota', $uriNovo->getPath());
    }

    public function testWithQueryDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withQuery("msg=novo");

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
        self::assertEquals("msg=novo", $uriNovo->getQuery());
    }

    public function testWithFragmentDeveRetornarUriInterface()
    {
        $uriNovo = $this->uri->withFragment('link');

        self::assertNotEquals($uriNovo, $this->uri);
        self::assertInstanceOf(UriInterface::class, $uriNovo);
        self::assertEquals('link', $uriNovo->getFragment());
    }
}

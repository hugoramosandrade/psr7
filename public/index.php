<?php

use Hugo\Psr7\Filesystem\Event;
use Hugo\Psr7\Filesystem\EventTypes;
use Hugo\Psr7\Filesystem\Watcher;
use Hugo\Psr7\Http\Middleware\MiddlewareInterface;
use Hugo\Psr7\Http\ServerRequest;
use Swoole\Http\{Server, Request, Response};

require_once "../vendor/autoload.php";

$host = "0.0.0.0";
$port = 3001;

$server =  new Server($host,$port);
$server->on('request', function (Request $request, Response $response) {
    $middleware = new MiddlewareInterface;
    $middleware->process($request);
    $response->header('Content-Type', 'text/html; charset=utf-8');
    $response->end('Hello, world!');
});

$server->on("start", function () use ($port, $host, $server) {
    echo "Server running on http://{$host}:{$port}".PHP_EOL;
    go(function () use ($server) {
        $currentDir = getcwd();
        $watcher = new Watcher("{$currentDir}/../", callback: function (Event $event) use ($server) {
            $server->reload();
        });
        $watcher->watch();
    });
});

$server->on('AfterReload', function() use ($port, $host) {
    echo "Server restarted, running on http://{$host}:{$port}".PHP_EOL;
});

$server->start();
// $requestServer = new ServerRequest($_SERVER['REQUEST_METHOD'], $_REQUEST['REQUEST_URI'], $headers,file_get_contents('php://input'));
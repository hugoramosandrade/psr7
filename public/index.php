<?php

use Hugo\Psr7\Http\ServerRequest;

require_once "../vendor/autoload.php";

// dd();

$headers = [];
foreach ($_SERVER as $key => $value) {
    if (str_starts_with($key, 'HTTP_'))
        $headers[$key] = $value;
}

dd($headers);

$requestServer = new ServerRequest($_SERVER['REQUEST_METHOD'], $_REQUEST['REQUEST_URI'], $headers,file_get_contents('php://input'));
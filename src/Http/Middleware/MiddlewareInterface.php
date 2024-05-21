<?php

namespace Hugo\Psr7\Http\Middleware;

use Hugo\Psr7\Http\ServerRequest;
use Hugo\Psr7\Http\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request;

class MiddlewareInterface
{
    public function process(Request $request)
    {
        /** @var UploadedFileInterface[] */
        // $uploadedFiles = [];
        // foreach ($request->files as $key => $files) {
        //     if (array_is_list($files)) {
        //         foreach ($files as $file) {
        //             $uploadedFiles[$key][] = new UploadedFile($file['tmp_name'], $file['size'], $file['error'], $file['name'], $file['type']);
        //         }
        //     } else {
        //         $uploadedFiles[$key] = new UploadedFile($files['tmp_name'], $files['size'], $files['error'], $files['name'], $files['type']);
        //     }
        // }

        // $url = $request->server['remote_addr'].":".$request->server['server_port'].$request->server['path_info']."?".$request->server['query_string'];
        // $serverRequest = new ServerRequest($request->getMethod(),$url, $request->header, $request->rawContent(), $request->server['server_protocol'], $request->server, $uploadedFiles);
        dump($request->cookie);
    }
}

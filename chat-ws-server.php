<?php
/**
 * Created by PhpStorm.
 * User: vbychkovskyy
 * Date: 15/11/16
 * Time: 16:50
 */
use \Ratchet\Server\IoServer;
use \Ratchet\Http\HttpServer;
use \Ratchet\WebSocket\WsServer;
use \App\Chat;

require __DIR__ . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8082
);
$server->run();
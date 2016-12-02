<?php
/**
 * Created by PhpStorm.
 * User: vbychkovskyy
 * Date: 15/11/16
 * Time: 16:50
 */
use \Ratchet\Server\IoServer;
use \App\Chat;

require __DIR__ . '/vendor/autoload.php';

$server = IoServer::factory(
    new Chat(),
    8081
);
$server->run();
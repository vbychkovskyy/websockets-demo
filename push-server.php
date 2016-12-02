<?php
/**
 * Created by PhpStorm.
 * User: vbychkovskyy
 * Date: 15/11/16
 * Time: 16:50
 */

use \App\Pusher;

require __DIR__ . '/vendor/autoload.php';

$loop   = React\EventLoop\Factory::create();
$pusher = new Pusher();

// Listen for the web server to make a ZeroMQ push after request
$context = new React\ZMQ\Context($loop);
/** @var ZMQSocket $pull */
$pull = $context->getSocket(ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($pusher, 'onPostEvent'));

// Set up our WebSocket server for clients wanting real-time updates
$webSock = new React\Socket\Server($loop);
$webSock->listen(8083, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $pusher
            )
        )
    ),
    $webSock
);

$loop->run();
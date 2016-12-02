<?php
/**
 * Created by PhpStorm.
 * User: vbychkovskyy
 * Date: 15/11/16
 * Time: 17:48
 */

namespace app;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        //echo "Subscribe";
        //var_dump($topic);
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {

    }

    public function onOpen(ConnectionInterface $conn) {
        //echo "Open";
        //var_dump($conn);
    }

    public function onClose(ConnectionInterface $conn) {

    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

    }

    public function onPostEvent($entry) {
        $entryData = json_decode($entry, true);
        //echo "PostEvent\n" . $entry;
        //var_dump($this->subscribedTopics);
        if(!empty($this->subscribedTopics[$entryData['event_type']])) {
            $topic = $this->subscribedTopics[$entryData['event_type']];
            $topic->broadcast($entryData);
        }
    }
}
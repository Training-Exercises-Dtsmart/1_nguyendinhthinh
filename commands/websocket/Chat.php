<?php

namespace app\commands\websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $clientIp = $conn->remoteAddress; // Get client IP address
        echo "New connection! ({$conn->resourceId}) from $clientIp\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $clientIp = $from->remoteAddress; // Get client IP address
        $messageWithIp = "From $clientIp: $msg";

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($messageWithIp);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $clientIp = $conn->remoteAddress; // Get client IP address
        echo "Connection {$conn->resourceId} from $clientIp has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
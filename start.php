<?php

use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

// Create a Websocket server
$ws_worker = new Worker('websocket://0.0.0.0:8282');

$users = array();

$left = NULL;
$right = NULL;

// Emitted when new connection come
$ws_worker->onConnect = function ($connection) use (&$users, &$left, &$right) {
    echo "New connection\n";
    $users[] = $connection;
    
    if ($left == NULL) {
        $left = $connection;
        $connection->send('left');
    } else if ($right == NULL) {
        $right = $connection;
        $connection->send('right');
    }
    
    showUsers();
};

// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) {
    // Send hello $data
    $connection->send('Hello ' . $data);
};

// Emitted when connection closed
$ws_worker->onClose = function ($connection) use (&$users, &$left, &$right){
    echo "Connection closed\n";

    if ($connection == $left) {
        $left = NULL;
    } else if ($connection == $right) {
        $right = NULL;
    }

    unset($users[array_search($connection, $users)]);
};

function showUsers() {
    global $users;
    echo "Connected users: \n";
    foreach ($users as $user) {
        echo $user->getRemoteIP() . "\n";
    }
}

// Run worker
Worker::runAll();
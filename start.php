<?php

use Workerman\Worker;
use Workerman\Timer;


require_once __DIR__ . '/vendor/autoload.php';
include_once 'functions.php';

// Create a Websocket server
$ws_worker = new Worker('websocket://0.0.0.0:8282');

$users = array();
$changeState = 0.025;

$gameTimer;

$left = NULL;
$right = NULL;

// Emitted when new connection come
$ws_worker->onConnect = function ($connection) use (&$users, &$left, &$right) {
    echo "New connection\n";
    $users[] = $connection;
    
    if ($left == NULL) {
        $left = $connection;
        $connection->send(json_encode_objs(array('event' => 'setRole', 'role' => 'left')));
    } else if ($right == NULL) {
        $right = $connection;
        $connection->send(json_encode(array('event' => 'setRole', 'role' => 'right')));
    }
    
    showUsers();
};

// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) use (&$paddleP1_Y, &$paddleP2_Y, &$left, &$right, &$changeState, &$gameTimer, &$updateBall) {
    $data = json_decode($data);
    switch($data->event) {
        case 'movePaddle':
            if ($connection == $left) {
                $paddleP1_Y = $data->y;
                $info = array('event' => 'moveOpponent', 'role' => 'left', 'y' => $paddleP1_Y );
                $right->send(json_encode_objs($info));
            } else if ($connection == $right) {
                $paddleP2_Y = $data->y;
                $info = array('event' => 'moveOpponent', 'role' => 'right', 'y' => $paddleP2_Y);
                $left->send(json_encode_objs($info));
            }
            break;

        case 'startGame':
            if ($left != NULL && $right != NULL) {
                $gameTimer =  Timer::add($changeState, function($updateBall, $left, $right, $connection)
                {
                    $data = $updateBall($connection);
                    $left->send($data);
                    $right->send($data);
                }, array($updateBall, $left, $right, $connection));
                }
            break;
        case 'stopGame':
            if ($left != NULL && $right != NULL) {
                Timer::del($gameTimer);
            }
            break;
    }

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
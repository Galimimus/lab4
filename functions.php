<?php

$canvasWidth = 900;
$canvasHeight = 550;

$ballDirection_X = 2;
$ballDirection_Y = 2;

$ball_R = 10;
$ballStart_X = $canvasWidth / 2;
$ballStart_Y = $canvasHeight / 2;
$ballSpeedStart_X = 2;
$ballSpeedStart_Y = 2;
$ball_X = $ballStart_X;
$ball_Y = $ballStart_Y;

$p2points = 0;
$p1points = 0;

$paddleWidth = 30;
$paddleHeight = 100;

$paddleP1_Y = 300;
$paddleP2_Y = 100;
$paddleP1_X = 10;
$paddleP2_X = 860;

$finishScore = 10;




$ballMove = function() use (&$ball_X, &$ball_Y, &$ballDirection_X, &$ballDirection_Y) {
    // echo "Ball X: " . $ball_X . " Ball Y: " . $ball_Y . ";". "\n";

    $ball_X += $ballDirection_X;
    $ball_Y += $ballDirection_Y;
};



$updateResult = function() use (&$ballDirection_X, &$ballDirection_Y, &$p1points, &$p2points, &$ballOutsideLeft, &$ballOutsideRight, &$moveBalltoStartPosition) {
    if ($ballOutsideLeft()) {
        $moveBalltoStartPosition();
        $p2points++;
        $ballDirection_X = 2;
        $ballDirection_Y = 2;
    } else if ($ballOutsideRight()) {
        $moveBalltoStartPosition();
        $p1points++;
        $ballDirection_X = 2;
        $ballDirection_Y = 2;
    } else {
        return;
    }

    $result = array(
        "event" => "updateResult",
        "p1points" => $p1points,
        "p2points" => $p2points
    );

    return $result;
};

$moveBalltoStartPosition = function() use (&$ball_X, &$ball_Y, &$canvasWidth, &$canvasHeight) {
    $ball_X = $canvasWidth / 2;
    $ball_Y = $canvasHeight / 2;
};


$ballOutsideLeft = function() use (&$ball_X, &$ball_R) { 
    return $ball_X + $ball_R <= 0;
};

$ballOutsideRight = function() use (&$ball_X, &$ball_R, &$canvasWidth) { 
    return $ball_X - $ball_R >= $canvasWidth;
};

$ballBounceFromBottom = function() use (&$ball_Y, &$ball_R, &$canvasHeight) { 
    return $ball_Y + $ball_R >= $canvasHeight;
};

$ballBounceFromTop = function() use (&$ball_Y, &$ball_R) { 
    return $ball_Y - $ball_R <= 0;
};


$updateMove = function () use (&$ball_X, &$ball_Y, &$ballDirection_X, &$ballDirection_Y, &$ballBounceFromBottom, &$ballBounceFromTop, &$ballisBetweenPaddle, &$paddleP2_Y, &$paddleP1_Y, &$paddleHeight, &$paddleP2_X, &$paddleP1_X, &$paddleWidth) {
    if ($ballBounceFromBottom()) {
        echo "Bounce from Bottom";
        $ballDirection_Y = -$ballDirection_Y;
    }
    if ($ballBounceFromTop()) {
        echo "Bounce from Top";
        $ballDirection_Y = -$ballDirection_Y;
    }
    if (ballisBetweenPaddle($ball_Y, $paddleP2_Y, $paddleP2_Y + $paddleHeight) && ($ball_X == $paddleP2_X - $paddleP1_X)) {
        echo "Bounce from Right Paddle";
        $ballDirection_X = -$ballDirection_X;
        //doubleBallSpeed();
    }
    if (ballisBetweenPaddle($ball_Y, $paddleP1_Y, $paddleP1_Y + $paddleHeight) && ($ball_X == $paddleP1_X + $paddleWidth + $paddleP1_X)) {
        echo "Bounce from Left Paddle";
        $ballDirection_X = -$ballDirection_X;
        //doubleBallSpeed();
    }
};




 function ballisBetweenPaddle($value, $min, $max) {
    return $value >= $min && $value <= $max;
    };



$updateBall = function($connection) use (&$ball_X, &$ball_Y, &$ballMove, &$updateMove, &$updateResult, &$finishScore, &$gameTimer, &$SendToBoth) {
    $ballMove();
    $res = $updateResult();
    print_r($res);
    if($res != null) {
        if($res["p1points"] == $finishScore || $res["p2points"] == $finishScore) {
            $SendToBoth(json_encode($res));
            $SendToBoth(json_encode("Game Over"));
            Timer::del($gameTimer);
            return;
        }else{
            $SendToBoth(json_encode_objs($res));
        }   
    }
    $updateMove();
    
    $result = array(
        "event" => "updateBall",
        "ball_X" => $ball_X,
        "ball_Y" => $ball_Y
    );
    return json_encode_objs($result);
};

$SendToBoth = function ($data) use (&$left, &$right) {
    $left->send($data);
    $right->send($data);
};


function json_encode_objs($item)
{
    if (is_object($item) || (is_array($item)&&isAssoc($item))) {
        $pieces = array();
        foreach ($item as $k => $v) {
            $pieces[] = '"'.$k.'":' . json_encode_objs($v);
        }
        return '{' . implode(',', $pieces) . '}';
    } else if(is_array($item)) {
        $pieces = array();
        foreach ($item as $k => $v) {
            $pieces[] = json_encode_objs($v);
        }
        return '[' . implode(',', $pieces) . ']';
    }else{
        return json_encode($item);
    }
}


function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}



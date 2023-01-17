<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Pong Game</title>
    <!--<link rel="shortcut icon" href="./img/PingPongIcon.jpg">-->
    <link rel="stylesheet" href="./style.css">
</head>

<body onload="openSocket()">
    <h1>Ping Pong Game</h1>
    <div class="result">
        <h2>Player 1: <span class="p1points">0</span></h2>
        <h2>Player 2: <span class="p2points">0</span></h2>
    </div>
    <canvas id="pingPongCanvas" width="900" height="550"></canvas>
    <a href="./index.php">Exit</a>
    <a id="btn" onclick = 'ws.send("{\"event\":\"startGame\"}")'>Start game</a>
    <script
        src="./script.js">
    </script>
</body>

</html>
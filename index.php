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

<body>
    <div class="container">
        <h1>Ping Pong Game</h1>
        <a class="playButton" onclick='check()'>Play</a>
    </div>
    <script>
        check = () => {
            if ((window.innerWidth <= 800) && (window.innerHeight <= 960)) {
                console.log("error");
            } else {
                window.location.href = "./game.php";
                console.log("desktop");
            }
        }
    </script>
</body>

</html>
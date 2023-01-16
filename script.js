
var ws;
function openSocket() {
    ws = new WebSocket("ws://127.0.0.1:8282");
    ws.onopen = function() {
        console.log("WebSocket opened.");
    };
    ws.onmessage = function(event) {
        console.log("WebSocket message received: " + event.data);
    };
    ws.onclose = function() {
        console.log("WebSocket closed.");
    };
}

function sendMessage() {
    var message = document.getElementById("message").value;
    ws.send(message);
    console.log("WebSocket message sent: " + message);
}

const pingPongCanvas = document.getElementById("pingPongCanvas");
const ctx = pingPongCanvas.getContext("2d");
const p1points = document.querySelector("span.p1points");
const p2points = document.querySelector("span.p2points");

const canvasHeight = pingPongCanvas.height;
const canvasWidth = pingPongCanvas.width;

const paddleWidth = 30;
const paddleHeight = 100;
const paddleP1_X = 10;
let paddleP1_Y = 300;
const paddleP2_X = 860;
let paddleP2_Y = 100;
const paddleStart_Y = (canvasHeight - paddleHeight) / 2;
const ball_R = 10;
const ballStart_X = canvasWidth / 2;
const ballStart_Y = canvasHeight / 2;
const ballSpeedStart_X = 2;
const ballSpeedStart_Y = 2;
const changeState = 20;

let ball_X = ballStart_X;
let ball_Y = ballStart_Y;
let ballDirection_X = ballSpeedStart_X;
let ballDirection_Y = ballSpeedStart_Y;

drawPaddle = (x, y) => {
    ctx.fillStyle = "#FF0000";
    ctx.fillRect(x, y, paddleWidth, paddleHeight);
}

drawPaddle(paddleP1_X, paddleP1_Y);
drawPaddle(paddleP2_X, paddleP2_Y);

drawBall = (ballStart_X, ballStart_Y, ball_R) => {
    ctx.beginPath();
    ctx.arc(ballStart_X, ballStart_Y, ball_R, 0, Math.PI * 2, true);
    ctx.closePath();
    ctx.fillStyle = "#000000";
    ctx.fill();
}

clearCanvas = () => {
    ctx.clearRect(0, 0, canvasWidth, canvasHeight);
}

drawActualState = () => {
    clearCanvas();
    drawBall(ball_X, ball_Y, ball_R);
    drawPaddle(paddleP1_X, paddleP1_Y);
    drawPaddle(paddleP2_X, paddleP2_Y);
}

//Ball moving
ballOutsideLeft = () => ball_X + ball_R <= 0;
ballOutsideRight = () => ball_X - ball_R >= canvasWidth;
ballBounceFromBottom = () => ball_Y + ball_R >= canvasHeight;
ballBounceFromTop = () => ball_Y - ball_R <= 0;

ballisBetweenPaddle = (value, min, max) => value >= min && value <= max;

updateResult = () => {
    if (ballOutsideLeft()) {
        moveBalltoStartPosition();
        p2points.innerText++;
        ballDirection_X = 2;
        ballDirection_Y = 2;
    } else if (ballOutsideRight()) {
        moveBalltoStartPosition();
        p1points.innerText++;
        ballDirection_X = 2;
        ballDirection_Y = 2;
    }
}

doubleBallSpeed = () => {
    ballDirection_X = 2 * ballDirection_X;
    ballDirection_Y = 2 * ballDirection_Y;
}

updateMove = () => {
    if (ballBounceFromBottom()) {
        console.log("Bounce from Bottom");
        ballDirection_Y = -ballDirection_Y;
    }
    if (ballBounceFromTop()) {
        console.log("Bounce from Top");
        ballDirection_Y = -ballDirection_Y;
    }
    if (ballisBetweenPaddle(ball_Y, paddleP2_Y, paddleP2_Y + paddleHeight) && (ball_X == paddleP2_X - paddleP1_X)) {
        console.log("Bounce from Right Paddle");
        ballDirection_X = -ballDirection_X;
        //doubleBallSpeed();
    }
    if (ballisBetweenPaddle(ball_Y, paddleP1_Y, paddleP1_Y + paddleHeight) && (ball_X == paddleP1_X + paddleWidth + paddleP1_X)) {
        console.log("Bounce from Left Paddle");
        ballDirection_X = -ballDirection_X;
        //doubleBallSpeed();
    }
}

ballMove = () => {
    ball_X += ballDirection_X;
    ball_Y += ballDirection_Y;
}

moveBalltoStartPosition = () => {
    ball_X = ballStart_X;
    ball_Y = ballStart_Y;
}

setInterval(updateStateAndDrawState = () => {
    ballMove();
    updateResult();
    updateMove();
    drawActualState();
}, changeState);

//Paddle moving
const paddle_Y_max = 450;
const paddle_Y_min = 0;
const paddle_Y_steps = 10;
let keys;

document.addEventListener("keydown", function(e) {
    keys = (keys || []);
    keys[e.keyCode] = true;
    //KeyA
    if (keys[65] && paddleP1_Y !== paddle_Y_min) {
        paddleP1_Y -= paddle_Y_steps;
    }
    //KeyZ
    if (keys[90] && paddleP1_Y !== paddle_Y_max) {
        paddleP1_Y += paddle_Y_steps;
    }
    //KeyK
    // if (keys[75] && paddleP2_Y !== paddle_Y_min) {
    //     paddleP2_Y -= paddle_Y_steps;
    // }
    // //KeyM
    // if (keys[77] && paddleP2_Y !== paddle_Y_max) {
    //     paddleP2_Y += paddle_Y_steps;
    // }
}, false);

document.addEventListener("keyup", function(e) {
    keys[e.keyCode] = false;
    stop();
}, false);


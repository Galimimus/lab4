
var ws;
var role = "none";
function openSocket() {
    ws = new WebSocket("ws://127.0.0.1:8282");
    ws.onopen = function() {
        console.log("WebSocket opened.");
    };
    ws.onmessage = function(e) {
        var temp_data = e.data 
        console.log(temp_data);
        var parse_data = JSON.parse(temp_data);
        event_type = parse_data.event;
        console.log(event_type);
        switch(event_type) {
            case "updateBall":
                ball_X = parse_data.ball_X;
                ball_Y = parse_data.ball_Y;
                drawActualState();
                break;
            case "updateResult":
                p1points.innerText = parse_data.p1points;
                p2points.innerText = parse_data.p2points;
                if(parse_data.p1points == 10 || parse_data.p2points == 10) {
                    alert("Game over!");
                    p1points.innerText = 0;
                    p2points.innerText = 0;
                }
                break;
            case "setRole":
                role = parse_data.role;
                console.log("Role: " + role);
                break;
            case "moveOpponent":
                if(parse_data.role == "left") {
                    paddleP1_Y = parse_data.y;
                } else if(parse_data.role == "right") {
                    paddleP2_Y = parse_data.y;
                }
                drawActualState();
            break;
        }
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

//Paddle moving
const paddle_Y_max = 450;
const paddle_Y_min = 0;
const paddle_Y_steps = 10;
let keys;

document.addEventListener("keydown", function(e) {
    keys = (keys || []);
    keys[e.keyCode] = true;
    let responseData;
    //KeyA
    if (keys[65]) {
        if(role === "left" && paddleP1_Y !== paddle_Y_min){
            paddleP1_Y -= paddle_Y_steps;
            responseData = {
                "event": "movePaddle",
                "y": paddleP1_Y
            };
            ws.send(JSON.stringify(responseData));
        }else if(role === "right" && paddleP2_Y !== paddle_Y_min){
            paddleP2_Y -= paddle_Y_steps;
            responseData = {
                "event": "movePaddle",
                "y": paddleP2_Y
            };
            ws.send(JSON.stringify(responseData));
        }
    }
    //KeyZ
    if (keys[90]) {

        if(role === "left" && paddleP1_Y !== paddle_Y_max){
            paddleP1_Y += paddle_Y_steps;
            responseData = {
                "event": "movePaddle",
                "y": paddleP1_Y
            };
            ws.send(JSON.stringify(responseData));
        }else if(role === "right" && paddleP2_Y !== paddle_Y_max){

            paddleP2_Y += paddle_Y_steps;
            responseData = {
                "event": "movePaddle",
                "y": paddleP2_Y
            };
            ws.send(JSON.stringify(responseData));
        }
    }


}, false);

document.addEventListener("keyup", function(e) {
    keys[e.keyCode] = false;
    stop();
}, false);


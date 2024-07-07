var difficulty = weblsget("difficulty");
var timemin = 0;
var timesec = 0;
var width = innerWidth * 0.6;
var height = innerHeight * 0.85;
var nowarray = [];
var blocklist = [];
var blockarray = [];
function block(key) {
    if (key == "a") {
        var div = doccreate("div");
        div.classList.add("typea");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [1, 1, 1, 1],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "b") {
        var div = doccreate("div");
        div.classList.add("typeb");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 1, 0, 0],
            [0, 1, 1, 1],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "c") {
        var div = doccreate("div");
        div.classList.add("typec");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 0, 0, 1],
            [0, 1, 1, 1],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "d") {
        var div = doccreate("div");
        div.classList.add("typed");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 1, 1, 0],
            [0, 1, 1, 0],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "e") {
        var div = doccreate("div");
        div.classList.add("typee");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 0, 1, 1],
            [0, 1, 1, 0],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "f") {
        var div = doccreate("div");
        div.classList.add("typef");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 0, 1, 0],
            [0, 1, 1, 1],
            [0, 0, 0, 0]
        ];
    }
    else if (key == "g") {
        var div = doccreate("div");
        div.classList.add("typeg");
        docappendchild("main", div);
        blockarray = [
            [0, 0, 0, 0],
            [0, 1, 1, 0],
            [0, 0, 1, 1],
            [0, 0, 0, 0]
        ];
    }
    else {
        conlog("[ERROR]error key", "red", "15");
    }
}
function test(key) {
    if (key) {
        conlog("teststart", "green", "15");
        block("a");
        block("b");
        block("c");
        block("d");
        block("e");
        block("f");
        block("g");
    }
}
for (var i = 0; i < 17; i = i + 1) {
    nowarray.push([]);
    for (var j = 0; j < 10; j = j + 1) {
        nowarray[i].push(0);
    }
}
docgetid("main").style.width = width + "px";
docgetid("main").style.height = height + "px";
docgetid("difficulty").innerHTML = difficulty;
setInterval(function () {
    timesec = timesec + 1;
    if (timesec == 60) {
        timemin = timemin + 1;
        timesec = 0;
    }
    var min = timemin.toString();
    var sec = timesec.toString();
    if (timesec < 10) {
        sec = "0" + sec;
    }
    if (timemin < 10) {
        min = "0" + min;
    }
    docgetid("time").innerHTML = "\n        \u6642\u9593: ".concat(min, ":").concat(sec, "\n    ");
}, 1000);
function left() {
}
function right() {
}
function rotate() {
}
function down() {
}
function downtobottom() {
}
function stop() {
}
function start() {
    return;
}
document.addEventListener("keydown", function (event) {
    if (event.key == "ArrowLeft") {
        event.preventDefault();
        left();
    }
    if (event.key == "ArrowRight") {
        event.preventDefault();
        right();
    }
    if (event.key == "ArrowUp") {
        event.preventDefault();
        rotate();
    }
    if (event.key == "ArrowDown") {
        event.preventDefault();
        down();
    }
    if (event.key == " ") {
        event.preventDefault();
        downtobottom();
    }
});
docgetid("share").onclick = function () {
};
docgetid("stop").onclick = function () {
    if (docgetid("stop").value == "暫停遊戲") {
        stop()(docgetid("stop")).value = "繼續遊戲";
    }
    else {
        start()(docgetid("stop")).value = "暫停遊戲";
    }
};
docgetid("cancel").onclick = function () {
    stop();
    if (confirm("是否要放棄遊戲?")) {
        location.href = "index.html";
    }
    else {
        start();
    }
};
test(false);
startmacossection();

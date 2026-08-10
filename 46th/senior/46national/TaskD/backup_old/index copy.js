var traintypedata;
var stationdata;
var traindata;
var station = "<option value='all'>所有</option>";
var traintype = "<option value='all'>所有</option>";
oldajax("GET", "api.php?traintypelist=").onload = function () { traintypedata = JSON.parse(this.responseText); };
oldajax("GET", "api.php?stationlist=").onload = function () { stationdata = JSON.parse(this.responseText); };
oldajax("GET", "api.php?trainlist=").onload = function () { traindata = JSON.parse(this.responseText); };
setTimeout(function () {
    for (var i = 0; i < stationdata.length; i = i + 1) {
        station = station + "<option value=\"".concat(stationdata[i][1], "\">").concat(stationdata[i][2], "</option>");
    }
    for (var i = 0; i < traintypedata.length; i = i + 1) {
        traintype = traintype + "<option value=\"".concat(traintypedata[i][0], "\">").concat(traintypedata[i][1], "</option>");
    }
    docgetid("start").innerHTML = station;
    docgetid("end").innerHTML = station;
    docgetid("traintype").innerHTML = traintype;
    docgetid("date");
    docgetid("submit").onclick = function () {
        docgetid("start");
        docgetid("end");
        docgetid("traintype");
        docgetid("date");
    };
    startmacossection();
}, 100);
setTimeout(function () {
    oldajax("GET", "api.php?trainlist=").onload = function () {
        var data = JSON.parse(this.responseText);
        var trainlist = data[0];
        var stoplist = data[1];
        var stoplistfilterdata = [];
        var _loop_1 = function (i) {
            var id = trainlist[i][0];
            var stoplistfilter = stoplist.filter(function (event) { return event[1] == id; });
            var traintypename = "";
            var stationname = "";
            var week = "";
            for (var j = 0; j < traintypedata.length; j = j + 1) {
                if (traintypedata[j][0] == trainlist[i][1]) {
                    traintypename = traintypedata[j][1];
                }
            }
            for (var j = 0; j < stationdata.length; j = j + 1) {
                if (stationdata[j][0] == stoplistfilter[0][2]) {
                    stationname = stationdata[j][2];
                }
            }
            stoplistfilterdata.push(stoplistfilter);
            if (trainlist[i][3] == "1") {
                week = "一";
            }
            else if (trainlist[i][3] == "2") {
                week = "二";
            }
            else if (trainlist[i][3] == "3") {
                week = "三";
            }
            else if (trainlist[i][3] == "4") {
                week = "四";
            }
            else if (trainlist[i][3] == "5") {
                week = "五";
            }
            else if (trainlist[i][3] == "6") {
                week = "六";
            }
            else {
                week = "日";
            }
            var tr = doccreate("tr");
            tr.innerHTML = "\n                <td class=\"td\">".concat(traintypename, "</td>\n                <td class=\"td\">").concat(trainlist[i][2], "</td>\n                <td class=\"td\">\u767C\u8ECA\u7AD9</td>\n                <td class=\"td\">\u7D42\u9EDE\u7AD9</td>\n                <td class=\"td\">\u9810\u8A08\u767C\u8ECA\u6642\u9593</td>\n                <td class=\"td\">\u9810\u8A08\u5230\u9054\u6642\u9593</td>\n                <td class=\"td\">\u884C\u99DB\u6642\u9593</td>\n                <td class=\"td\">\u7968\u50F9</td>\n                <td class=\"td\">\n                    <input type=\"button\" class=\"bluebutton ticketbutton\" value=\"\u8A02\u7968\">\n                </td>\n            ");
            docappendchild("table", tr);
        };
        for (var i = 0; i < trainlist.length; i = i + 1) {
            _loop_1(i);
        }
        docgetall(".ticketbutton").forEach(function (event) {
            event.onclick = function () { };
        });
    };
}, 100);
startmacossection();

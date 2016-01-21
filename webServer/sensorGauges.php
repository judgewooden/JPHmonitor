<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id='Description'>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <!-- jQWidgets CSS -->
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.2/jquery.tipsy.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">
    <script type="text/javascript">
        $(document).ready(function () {
            var elem= new Array();
            var initializeValues = function() {
                loadValues("aqua1");
                loadValues("aqua2");
                loadValues("aqua3");
                loadValues("aqua4");
                loadValues("aqua5");
                loadValues("aqua6");
                loadValues("aqua7");
                loadValues("aqua8");
                loadValues("aqua9");
                loadValues("aqua0");
                loadValues("aquaA");
                loadValues("aquaB");
                loadValues("aquaC");
                loadValues("aquaD");
                loadValues("tem1");
                loadValues("tem2");
                loadValues("tem3");
                loadValues("ard1");
                loadValues("ard2");
                loadValues("ard3");
                loadValues("p1");
                loadValues("p2");
                loadValues("p3");
                loadValues("r1");
                loadValues("room1");
                loadValues("room2");
                loadValues("room3");
                console.log(elem);
            };
            var loadValues = function(id) {
                var msglist = document.getElementById(id);
                if (msglist == null) {
                    console.log("Can not find element:", id);
                    return
                }
                //console.log(msglist);
                var table = msglist.getAttribute("data-source");
                var column = msglist.getAttribute("data-column");
                var ttarget = msglist.getAttribute("data-time-target");
                var indicator = msglist.getAttribute("data-indicator");
                if ( table == null || column == null || ttarget == null || indicator == null) {
                    console.log(id, " T:",table," C:",column, " t:",ttarget, "i:",indicator);
                    console.log("could not find relevant data- components");
                } else {
                    var temp = {
                        id: id,
                        table: table,
                        column: column,
                        indicator: indicator,
                        ttarget: ttarget
                    };
                    elem.push(temp);
                    //console.log(temp);
                }
            };
            var updateValues = function() {
                for (var key in elem) {
                    surl="sensorSQLdash.php?id="+key+"&source="+elem[key].table+"&column="+elem[key].column;
                    //console.log(surl);
                    jQuery.getJSON(surl).done(function(response) {
                        if ( response.length > 0) {
                            //console.log(response);
                            ckey=response[0].id;
                            if ( response[0].timestamp == undefined) {
                                console.log("no answer")
                                $("#"+elem[ckey].id).html("---");
                                $("#"+elem[ckey].ttarget).html("No Value");
                                return
                            }

                            if ( elem[ckey].indicator == "home/away" ) {
                                    if ( response[0].value == "1")
                                        val1="Home"
                                    else
                                        val1="Away"
                            } else {
                                if ( elem[ckey].indicator == "power") {
                                    if ( response[0].value == "1")
                                        val1="On"
                                    else
                                        val1="Off"
                                } else
            val1=afronden(parseFloat(response[0].value).toFixed(1)) + "" + elem[ckey].indicator;
                            }
                            $("#"+elem[ckey].id).html(val1);
                            var t = response[0].timestamp.split(/[- :]/);
                            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                            $("#"+elem[ckey].ttarget).html(time_ago(d));
                            //console.log("answer:", val1, d);
                        } else {
                            console.log("php/sql result");
                        }
                    });
                }
            };
            initializeValues();
            updateValues();
            var ttimer = setInterval(function () {
                updateValues();
            }, 5000);
            function time_ago(time) {
                switch (typeof time) {
                    case 'number': break;
                    case 'string': time = +new Date(time); break;
                    case 'object': if (time.constructor === Date) time = time.getTime(); break;
                    default: time = +new Date();
                }
                var time_formats = [
                    [60, 'seconds', 1], // 60
                    [120, '1 minute ago', '1 minute from now'], // 60*2
                    [3600, 'minutes', 60], // 60*60, 60
                    [7200, '1 hour ago', '1 hour from now'], // 60*60*2
                    [86400, 'hours', 3600], // 60*60*24, 60*60
                    [172800, 'Yesterday', 'Tomorrow'], // 60*60*24*2
                    [604800, 'days', 86400], // 60*60*24*7, 60*60*24
                    [1209600, 'Last week', 'Next week'], // 60*60*24*7*4*2
                    [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
                    [4838400, 'Last month', 'Next month'], // 60*60*24*7*4*2
                    [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
                    [58060800, 'Last year', 'Next year'], // 60*60*24*7*4*12*2
                    [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
                    [5806080000, 'Last century', 'Next century'], // 60*60*24*7*4*12*100*2
                    [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
                ];
                var seconds = (+new Date() - time) / 1000,
                    token = 'ago', list_choice = 1;

                if (seconds < 5 && seconds > 0) {
                    return 'Just now'
                }
                if (seconds < 0) {
                    seconds = Math.abs(seconds);
                    token = 'from now';
                    list_choice = 2;
                }
                var i = 0, format;
                while (format = time_formats[i++])
                    if (seconds < format[0]) {
                        if (typeof format[2] == 'string')
                            return format[list_choice];
                        else
                            return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
                    }
                return time;
            };
            var afronden = function( inputValue ) {
               if (inputValue > 100)
                       return  Math.round(inputValue * 1) / 1;
               else {
                   if (inputValue > 10)
                       return  Math.round(inputValue * 10) / 10;
                   else {
                       if (inputValue > 1)
                           return Math.round(inputValue * 100) / 100;
                       else
                           return Math.round(inputValue * 1000) / 1000;
                   }
                }
            }
        });
    </script>
</head>
<body style="background:white;">
<?php include("menubar.html"); ?>
<div class="container-fluid">
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Next Update</div>
            <div class="panel-body dash-value" id="update">(TBC)</div>
            <div class="panel-footer dash-time" id="r1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Raspi Temp</div>
            <div class="panel-body dash-value" id="r1" data-source="RaspiTemp1"
                            data-column="Value" data-indicator="C", data-time-target="r1t"></div>
            <div class="panel-footer dash-time" id="r1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Current Power</div>
            <div class="panel-body dash-value" id="p1" data-source="Power1"
                            data-column="Power1"data-indicator="W", data-time-target="p1t"></div>
            <div class="panel-footer dash-time" id="p1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Current Power 2</div>
            <div class="panel-body dash-value" id="p2" data-source="Power1"
                            data-column="Power2"data-indicator="W", data-time-target="p2t"></div>
            <div class="panel-footer dash-time" id="p2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Total Power</div>
            <div class="panel-body dash-value" id="p3" data-source="Power1"
                            data-column="Energy"data-indicator="kwh", data-time-target="p3t"></div>
            <div class="panel-footer dash-time" id="p3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Room Temp</div>
            <div class="panel-body dash-value" id="room1" data-source="Nest"
                                data-column="Temperature" data-indicator="C", data-time-target="room1t"></div>
            <div class="panel-footer dash-time" id="room1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Room Humidity</div>
            <div class="panel-body dash-value" id="room2" data-source="Nest"
                                data-column="Humidity" data-indicator="%", data-time-target="room2t"></div>
            <div class="panel-footer dash-time" id="room2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Home/Away</div>
            <div class="panel-body dash-value" id="room3" data-source="Nest"
                                data-column="Away" data-indicator="home/away", data-time-target="room3t"></div>
            <div class="panel-footer dash-time" id="room3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Power</div>
            <div class="panel-body dash-value" id="ard1" data-source="ArduinoMonitor1"
                            data-column="Power" data-indicator="power", data-time-target="ard1t"></div>
            <div class="panel-footer dash-time" id="ard1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Flow</div>
            <div class="panel-body dash-value" id="ard2" data-source="ArduinoMonitor1"
                            data-column="LitersPerMinute" data-indicator="lpm", data-time-target="ard2t"></div>
            <div class="panel-footer dash-time" id="ard2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 refs</div>
            <div class="panel-body dash-value" id="ard3" data-source="ArduinoMonitor1"
                            data-column="FlowPerSecond" data-indicator="rps", data-time-target="ard3t"></div>
            <div class="panel-footer dash-time" id="ard3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Inflow Temp</div>
            <div class="panel-body dash-value" id="tem1" data-source="ManifoldTemp1"
                            data-column="InFlow" data-indicator="C", data-time-target="tem1t"></div>
            <div class="panel-footer dash-time" id="tem1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Temp</div>
            <div class="panel-body dash-value" id="tem2" data-source="ManifoldTemp1"
                            data-column="OutFlow1" data-indicator="C", data-time-target="tem2t"></div>
            <div class="panel-footer dash-time" id="tem2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 2 Temp</div>
            <div class="panel-body dash-value" id="tem3" data-source="ManifoldTemp1"
                            data-column="OutFlow2" data-indicator="C", data-time-target="tem3t"></div>
            <div class="panel-footer dash-time" id="tem3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">CPU Temp</div>
            <div class="panel-body dash-value" id="aqua1" data-source="SpeedfanMonitor1"
                            data-column="CPUTemp" data-indicator="C", data-time-target="aqua1t"></div>
            <div class="panel-footer dash-time" id="aqua1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">NB Temp</div>
            <div class="panel-body dash-value" id="aqua2" data-source="SpeedfanMonitor1"
                            data-column="NBTemp" data-indicator="C", data-time-target="aqua2t"></div>
            <div class="panel-footer dash-time" id="aqua2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">SB Temp</div>
            <div class="panel-body dash-value" id="aqua3" data-source="SpeedfanMonitor1"
                            data-column="SBTemp" data-indicator="C", data-time-target="aqua3t"></div>
            <div class="panel-footer dash-time" id="aqua3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">System Temp</div>
            <div class="panel-body dash-value" id="aqua4" data-source="SpeedfanMonitor1"
                            data-column="SystemTemp" data-indicator="C", data-time-target="aqua4t"></div>
            <div class="panel-footer dash-time" id="aqua4t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Water Temp</div>
            <div class="panel-body dash-value" id="aqua5" data-source="SpeedfanMonitor1"
                            data-column="OPT_FAN_1" data-indicator="C", data-time-target="aqua5t"></div>
            <div class="panel-footer dash-time" id="aqua5t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU1 Temp</div>
            <div class="panel-body dash-value" id="aqua6" data-source="SpeedfanMonitor1"
                            data-column="GPU1Temp" data-indicator="C", data-time-target="aqua6t"></div>
            <div class="panel-footer dash-time" id="aqua6t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU2 Temp</div>
            <div class="panel-body dash-value" id="aqua7" data-source="SpeedfanMonitor1"
                            data-column="GPU2Temp" data-indicator="C", data-time-target="aqua7t"></div>
            <div class="panel-footer dash-time" id="aqua7t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU3 Temp</div>
            <div class="panel-body dash-value" id="aqua8" data-source="SpeedfanMonitor1"
                            data-column="GPU3Temp" data-indicator="C", data-time-target="aqua8t"></div>
            <div class="panel-footer dash-time" id="aqua8t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU4 Temp</div>
            <div class="panel-body dash-value" id="aqua9" data-source="SpeedfanMonitor1"
                            data-column="GPU4Temp" data-indicator="C", data-time-target="aqua9t"></div>
            <div class="panel-footer dash-time" id="aqua9t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core1 Temp</div>
            <div class="panel-body dash-value" id="aqua0" data-source="SpeedfanMonitor1"
                            data-column="Core1Temp" data-indicator="C", data-time-target="aqua0t"></div>
            <div class="panel-footer dash-time" id="aqua0t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core2 Temp</div>
            <div class="panel-body dash-value" id="aquaA" data-source="SpeedfanMonitor1"
                            data-column="Core2Temp" data-indicator="C", data-time-target="aquaAt"></div>
            <div class="panel-footer dash-time" id="aquaAt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core3 Temp</div>
            <div class="panel-body dash-value" id="aquaB" data-source="SpeedfanMonitor1"
                            data-column="Core3Temp" data-indicator="C", data-time-target="aquaBt"></div>
            <div class="panel-footer dash-time" id="aquaBt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core4 Temp</div>
            <div class="panel-body dash-value" id="aquaC" data-source="SpeedfanMonitor1"
                            data-column="Core4Temp" data-indicator="C", data-time-target="aquaCt"></div>
            <div class="panel-footer dash-time" id="aquaCt"></div>
        </div>
    </div>
</div>

</body>
</html>
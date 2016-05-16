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

            //
            // Loop throught the all elements with class name <dash-value> and
            // load the unique <id> into an array for processing.
            //
            var initializeValues = function() {
                var dash = document.getElementsByClassName("dash-value");
                var i;
                for (var i = 1; i < dash.length; i++) {
                    loadValues(dash[i].id);
                    console.log(dash[i].id);
                }
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
                            // figure out how to change colour on age?
                            $("#"+elem[ckey].ttarget).html(time_ago(d));
                            //console.log("answer:", val1, d);
                        } else {
                            console.log("php/sql ERROR result");
                        }
                    });
                }
            };
            // re-write this rubish ! -- think about the way to make 1 constant for loop value
            initializeValues();
            updateValues();
            var uinterval=10000;
            var nupdateDate=new Date();
            nupdateDate=new Date();
            nupdateDate=new Date(nupdateDate.getTime() + uinterval)
            var nowDate=new Date();
            var IamInProgress = false;
            $("#updateX").html(uinterval/1000);
            var ttimer = setInterval(function () {
                if (IamInProgress)
                    return;
                IamInProgress=true;
                nowDate=new Date();
                secondsLeft=nupdateDate.getSeconds()-nowDate.getSeconds();
                if (secondsLeft<=0) {
                    nupdateDate=new Date();
                    nupdateDate=new Date(nupdateDate.getTime() + uinterval)
                    updateValues();
                }
                $("#updatet").html(time_ago(nupdateDate));
                //$("#pbV").html(time_ago(nupdateDate));
               //$("#pbL").attr('aria-valuenow',secondsLeft);
                percentage=secondsLeft/((uinterval-1000)/1000)*100;
                $("#pbL").width(percentage+"%").attr('aria-valuenow',percentage);
                IamInProgress=false;
            }, 100);
            function time_ago(time) { // add a parameter to add red items that are old than X time
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
<div style="position:relative;width:100%;height:7px"></div>
        <div class="container-fluid" style="width:100%">
         <div class="progress">
          <div class="progress-bar" id="pbL" role="progressbar" aria-valuenow="10"
          aria-valuemin="0" aria-valuemax="90" style="width:100%">
            <span class="sr-only" id="pbV">Next Update</span>
          </div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Next Auto Update</div>
            <div class="panel-body dash-value" id="updateX">(TBC)</div>
            <div class="panel-footer dash-time" id="updatet"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">pihost Temp</div>
            <div class="panel-body dash-value" id="r1" data-source="RaspiTemp1"
                            data-column="Value" data-indicator="C" data-time-target="r1t"></div>
            <div class="panel-footer dash-time" id="r1t"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">kastpi Temp</div>
            <div class="panel-body dash-value" id="r2" data-source="RaspiTemp2"
                            data-column="Value" data-indicator="C" data-time-target="r2t"></div>
            <div class="panel-footer dash-time" id="r2t"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Current Power</div>
            <div class="panel-body dash-value" id="p1" data-source="Power1"
                            data-column="Power1"data-indicator="W" data-time-target="p1t"></div>
            <div class="panel-footer dash-time" id="p1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Current Power 2</div>
            <div class="panel-body dash-value" id="p2" data-source="Power1"
                            data-column="Power2"data-indicator="W" data-time-target="p2t"></div>
            <div class="panel-footer dash-time" id="p2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Total Power</div>
            <div class="panel-body dash-value" id="p3" data-source="Power1"
                            data-column="Energy"data-indicator="kwh" data-time-target="p3t"></div>
            <div class="panel-footer dash-time" id="p3t"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Room Temp</div>
            <div class="panel-body dash-value" id="room1" data-source="Nest"
                                data-column="Temperature" data-indicator="C" data-time-target="room1t"></div>
            <div class="panel-footer dash-time" id="room1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Room Humidity</div>
            <div class="panel-body dash-value" id="room2" data-source="Nest"
                                data-column="Humidity" data-indicator="%" data-time-target="room2t"></div>
            <div class="panel-footer dash-time" id="room2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Home/Away</div>
            <div class="panel-body dash-value" id="room3" data-source="Nest"
                                data-column="Away" data-indicator="home/away" data-time-target="room3t"></div>
            <div class="panel-footer dash-time" id="room3t"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Power</div>
            <div class="panel-body dash-value" id="ard1p" data-source="ArduinoMonitor1"
                            data-column="Power" data-indicator="power" data-time-target="ard1tp"></div>
            <div class="panel-footer dash-time" id="ard1tp"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Flow</div>
            <div class="panel-body dash-value" id="ard1lpm" data-source="ArduinoMonitor1"
                            data-column="LitersPerMinute" data-indicator="lpm" data-time-target="ard1tlpm"></div>
            <div class="panel-footer dash-time" id="ard1tlpm"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 refs</div>
            <div class="panel-body dash-value" id="ard1fps" data-source="ArduinoMonitor1"
                            data-column="FlowPerSecond" data-indicator="rps" data-time-target="ard1tfps"></div>
            <div class="panel-footer dash-time" id="ard1tfps"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 2 Power</div>
            <div class="panel-body dash-value" id="ard2p" data-source="ArduinoMonitor2"
                            data-column="Power" data-indicator="power" data-time-target="ard2tp"></div>
            <div class="panel-footer dash-time" id="ard2tp"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 2 Flow</div>
            <div class="panel-body dash-value" id="ard2lpm" data-source="ArduinoMonitor2"
                            data-column="LitersPerMinute" data-indicator="lpm" data-time-target="ard2tlpm"></div>
            <div class="panel-footer dash-time" id="ard2tlpm"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 2 refs</div>
            <div class="panel-body dash-value" id="ard2fps" data-source="ArduinoMonitor2"
                            data-column="FlowPerSecond" data-indicator="rps" data-time-target="ard2tfps"></div>
            <div class="panel-footer dash-time" id="ard2tfps"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Inflow Temp</div>
            <div class="panel-body dash-value" id="tem1" data-source="ManifoldTemp1"
                            data-column="InFlow" data-indicator="C" data-time-target="tem1t"></div>
            <div class="panel-footer dash-time" id="tem1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Loop 1 Temp</div>
            <div class="panel-body dash-value" id="tem2" data-source="ManifoldTemp1"
                            data-column="OutFlow1" data-indicator="C" data-time-target="tem2t"></div>
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
                            data-column="CPUTemp" data-indicator="C" data-time-target="aqua1t"></div>
            <div class="panel-footer dash-time" id="aqua1t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">NB Temp</div>
            <div class="panel-body dash-value" id="aqua2" data-source="SpeedfanMonitor1"
                            data-column="NBTemp" data-indicator="C" data-time-target="aqua2t"></div>
            <div class="panel-footer dash-time" id="aqua2t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">SB Temp</div>
            <div class="panel-body dash-value" id="aqua3" data-source="SpeedfanMonitor1"
                            data-column="SBTemp" data-indicator="C" data-time-target="aqua3t"></div>
            <div class="panel-footer dash-time" id="aqua3t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">System Temp</div>
            <div class="panel-body dash-value" id="aqua4" data-source="SpeedfanMonitor1"
                            data-column="SystemTemp" data-indicator="C" data-time-target="aqua4t"></div>
            <div class="panel-footer dash-time" id="aqua4t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Water Temp</div>
            <div class="panel-body dash-value" id="aqua5" data-source="SpeedfanMonitor1"
                            data-column="OPT_FAN_1" data-indicator="C" data-time-target="aqua5t"></div>
            <div class="panel-footer dash-time" id="aqua5t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU1 Temp</div>
            <div class="panel-body dash-value" id="aqua6" data-source="SpeedfanMonitor1"
                            data-column="GPU1Temp" data-indicator="C" data-time-target="aqua6t"></div>
            <div class="panel-footer dash-time" id="aqua6t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU2 Temp</div>
            <div class="panel-body dash-value" id="aqua7" data-source="SpeedfanMonitor1"
                            data-column="GPU2Temp" data-indicator="C" data-time-target="aqua7t"></div>
            <div class="panel-footer dash-time" id="aqua7t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU3 Temp</div>
            <div class="panel-body dash-value" id="aqua8" data-source="SpeedfanMonitor1"
                            data-column="GPU3Temp" data-indicator="C" data-time-target="aqua8t"></div>
            <div class="panel-footer dash-time" id="aqua8t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">GPU4 Temp</div>
            <div class="panel-body dash-value" id="aqua9" data-source="SpeedfanMonitor1"
                            data-column="GPU4Temp" data-indicator="C" data-time-target="aqua9t"></div>
            <div class="panel-footer dash-time" id="aqua9t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core1 Temp</div>
            <div class="panel-body dash-value" id="aqua0" data-source="SpeedfanMonitor1"
                            data-column="Core1Temp" data-indicator="C" data-time-target="aqua0t"></div>
            <div class="panel-footer dash-time" id="aqua0t"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core2 Temp</div>
            <div class="panel-body dash-value" id="aquaA" data-source="SpeedfanMonitor1"
                            data-column="Core2Temp" data-indicator="C" data-time-target="aquaAt"></div>
            <div class="panel-footer dash-time" id="aquaAt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core3 Temp</div>
            <div class="panel-body dash-value" id="aquaB" data-source="SpeedfanMonitor1"
                            data-column="Core3Temp" data-indicator="C" data-time-target="aquaBt"></div>
            <div class="panel-footer dash-time" id="aquaBt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Core4 Temp</div>
            <div class="panel-body dash-value" id="aquaC" data-source="SpeedfanMonitor1"
                            data-column="Core4Temp" data-indicator="C" data-time-target="aquaCt"></div>
            <div class="panel-footer dash-time" id="aquaCt"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Closet Temp</div>
            <div class="panel-body dash-value" id="kastt" data-source="KastTemperature1"
                            data-column="Temperature" data-indicator="C" data-time-target="kasttt"></div>
            <div class="panel-footer dash-time" id="kasttt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Closet Humidity</div>
            <div class="panel-body dash-value" id="kasth" data-source="KastTemperature1"
                            data-column="Humidity" data-indicator="%" data-time-target="kastht"></div>
            <div class="panel-footer dash-time" id="kastht"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Air Temp</div>
            <div class="panel-body dash-value" id="airt" data-source="AirTemperature1"
                            data-column="Temperature" data-indicator="C" data-time-target="airtt"></div>
            <div class="panel-footer dash-time" id="airtt"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Air Humidity</div>
            <div class="panel-body dash-value" id="airh" data-source="AirTemperature1"
                            data-column="Humidity" data-indicator="%" data-time-target="airht"></div>
            <div class="panel-footer dash-time" id="airht"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Rad in-Temp</div>
            <div class="panel-body dash-value" id="radti" data-source="RadiatorTemp1"
                            data-column="Temperature" data-indicator="C" data-time-target="radtit"></div>
            <div class="panel-footer dash-time" id="radtit"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-warning dash-title">Rad out-Temp</div>
            <div class="panel-body dash-value" id="radto" data-source="RadiatorTemp1"
                            data-column="Humidity" data-indicator="C" data-time-target="radtot"></div>
            <div class="panel-footer dash-time" id="radtot"></div>
        </div>

        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Hash</div>
            <div class="panel-body dash-value" id="hashAqua" data-source="EthereumMiningAqua"
                            data-column="hashrate" data-indicator="C" data-time-target="hashAquat"></div>
            <div class="panel-footer dash-time" id="hashAquat"></div>
        </div>
        <div class="panel panel-primary col-xs-1 dash-box">
            <div class="panel-heading dash-title">Hash Calc</div>
            <div class="panel-body dash-value" id="hashAquaC" data-source="EthereumMiningAqua"
                            data-column="hashrate_calculated" data-indicator="C" data-time-target="hashAquaCt"></div>
            <div class="panel-footer dash-time" id="hashAquaCt"></div>
        </div>
    </div>
</div>
</body>
</html>

<!---
setx GPU_MAX_ALLOC_PERCENT 100
setx GPU_USE_SYNC_OBJECTS 1
ethminer -F http://eth-eu.dwarfpool.com:80/0x03145c9f20af9272cc87ee62c27b608c3b004f6a/AQUA/douwe.jong@gmail.com -G -t 4
pause
 -->
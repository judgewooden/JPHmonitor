<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="utf-8" http-equiv="encoding">
    <title>Flow Analysis</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
    <script src="http://d3js.org/d3.v2.js"></script>
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.2/jquery.tipsy.js"></script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>


    <script type="text/javascript" src="sensorGraph.js"></script>
    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">
</head>

<body>
<?php include("menubar.html"); ?>
<div id="graph1" class="aGraph" style="position:relative;width:100%;height:400px"></div>
<div id="graph2" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>
<div id="graph3" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>

<script>
	var source1 = {
    "Name":"Flow-Raw",
    "Settings":{
        "graphAutoUpdate":1,
        "graphUpdateInterval":10,
        "graphSecondsToShow":7200,
        "graphLeftLegend":"Degrees (C)",
        "graphLeftMax":0,
        "graphLeftMin":0,
        "graphRightLegend":"Liters (pm)",
        "graphRightMax":0,
        "graphRightMin":0,
        "graphTitle":"Arduino Flow - Last Two Hours",
        "graphInterpolation":"linear",
        "graphTickLine":1,
        "graphSensors":[
            {
                "Name":"Flow",
                "Unit":"ArduinoMonitor1",
                "Sensor":"Litersperminute",
                "Axis":"Right",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.20",
                "Smoothing":"30.00"
            },
            {
                "Name":"Flow-Raw",
                "Unit":"ArduinoMonitor1",
                "Sensor":"LitersPerMinute",
                "Axis":"Right",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.00",
                "Smoothing":"1.00"
            },
            {
                "Name":"Water",
                "Unit":"SpeedfanMonitor1",
                "Sensor":"OPT_FAN_1",
                "Axis":"Left",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.10",
                "Smoothing":"2.00"
            }
        ]
    }
};
    var l1 = new LineGraph({containerId: 'graph1', data: source1});

    var source2 = {
        "Name":"Flow - 2days",
        "Settings":{
            "graphAutoUpdate":1,
            "graphUpdateInterval":10,
            "graphSecondsToShow":172800,
            "graphLeftLegend":"Liters (pm)",
            "graphLeftMax":0,
            "graphLeftMin":0,
            "graphRightLegend":"Liters (pm)",
            "graphRightMax":0,
            "graphRightMin":0,
            "graphTitle":"Arduino Flow - Last 2 days",
            "graphInterpolation":"linear",
            "graphTickLine":0,
            "graphSensors":[
                {
                    "Name":"Flow",
                    "Unit":"ArduinoMonitor1",
                    "Sensor":"LitersPerMinute",
                    "Axis":"Left",
                    "Interpolation":"linear",
                    "Frequency":"15.00",
                    "Filter":"0.50",
                    "Smoothing":"15.00"
                }
            ]
        }
    };
    var l2 = new LineGraph({containerId: 'graph2', data: source2});

    var source3 = {
        "Name":"Flow - 10min",
        "Settings":{
            "graphAutoUpdate":1,
            "graphUpdateInterval":10,
            "graphSecondsToShow":600,
            "graphLeftLegend":"Liters (pm)",
            "graphLeftMax":0,
            "graphLeftMin":0,
            "graphRightLegend":"Liters (pm)",
            "graphRightMax":0,
            "graphRightMin":0,
            "graphTitle":"Arduino Flow - Last 10 minutes",
            "graphInterpolation":"linear",
            "graphTickLine":0,
            "graphSensors":[
                {
                    "Name":"Flow-Raw",
                    "Unit":"ArduinoMonitor1",
                    "Sensor":"LitersPerMinute",
                    "Axis":"Right",
                    "Interpolation":"linear",
                    "Frequency":"15.00",
                    "Filter":"-1.00",
                    "Smoothing":"1.00"
                }
            ]
        }
    };
    var l3 = new LineGraph({containerId: 'graph3', data: source3});

</script>
</body>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="utf-8" http-equiv="encoding">
    <title>JPH Dashboard - Aqua Flow Graph</title>
    <script src="http://d3js.org/d3.v2.js"></script>
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.2/jquery.tipsy.js"></script>
    <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">

    <script type="text/javascript" src="sensorGraph.js"></script>
    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">
</head>

<body>
<?php include("menubar.html"); ?>
<div id="graph1" class="aGraph" style="position:relative;width:100%;height:400px"></div>

<script>
	var source1 = {
    "Name":"Overview",
    "Settings":{
        "graphAutoUpdate":1,
        "graphUpdateInterval":10,
        "graphSecondsToShow":7200,
        "graphLeftLegend":"Liters (pm)",
        "graphLeftMax":0,
        "graphLeftMin":0,
        "graphRightLegend":"Degrees (C)",
        "graphRightMax":0,
        "graphRightMin":0,
        "graphTitle":"Overview - Last Two Hours",
        "graphInterpolation":"linear",
        "graphTickLine":1,
        "graphSensors":[
            {
                "Name":"Flow",
                "Unit":"ArduinoMonitor1",
                "Sensor":"Litersperminute",
                "Axis":"Left",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.20",
                "Smoothing":"30.00"
            },
            {
                "Name":"RaspTemp",
                "Unit":"RaspiTemp1",
                "Sensor":"Value",
                "Axis":"Right",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.70",
                "Smoothing":"30.00"
            },
            {
                "Name":"WaterTemp",
                "Unit":"SpeedfanMonitor1",
                "Sensor":"OPT_FAN_1",
                "Axis":"Right",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.20",
                "Smoothing":"30.00"
            },
            {
                "Name":"AquaGPU",
                "Unit":"SpeedfanMonitor1",
                "Sensor":"GPU1Temp",
                "Axis":"Right",
                "Interpolation":"linear",
                "Frequency":"15.00",
                "Filter":"0.70",
                "Smoothing":"30.00"
            },
            {
                "Name": "Room",
                "Unit": "Nest",
                "Sensor": "Temperature",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "120.00",
                "Filter": "0.20",
                "Smoothing": "30.00"
            }
        ]
    }
}
	var l1 = new LineGraph({containerId: 'graph1', data: source1});

</script>
</body>
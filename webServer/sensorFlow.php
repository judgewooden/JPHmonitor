<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<div id="graph2" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>
<div id="graph3" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>

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
            "graphTitle":"Last Two Hours",
            "graphInterpolation":"linear",
            "graphTickLine":0,
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
                    "Frequency": "600.00",
                    "Filter": "0.20",
                    "Smoothing": "30.00"
                }
            ]
        }
    }
    var l1 = new LineGraph({containerId: 'graph1', data: source1});

    var source2 = {
        "Name": "Power 2hours",
        "Settings": {
            "graphAutoUpdate": 1,
            "graphUpdateInterval": 10,
            "graphSecondsToShow": 7200,
            "graphLeftLegend": "Power (W)",
            "graphLeftMax": 0,
            "graphLeftMin": 0,
            "graphRightLegend": "Power (W)",
            "graphRightMax": 0,
            "graphRightMin": 0,
            "graphTitle": "Power - Last Two Hours",
            "hideXAxis": 0,
            "graphInterpolation": "linear",
            "graphTickLine": 0,
            "graphSensors": [
                {
                    "Name": "Power1",
                    "Unit": "Power1",
                    "Sensor": "Power1",
                    "Axis": "Left",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "0.20",
                    "Smoothing": "30.00"
                },
                {
                    "Name": "Power2",
                    "Unit": "Power1",
                    "Sensor": "Power2",
                    "Axis": "Left",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "0.20",
                    "Smoothing": "30.00"
                }
            ]
        }
    };
    var l2 = new LineGraph({containerId: 'graph2', data: source2});

    var source3 = {
        "Name": "Hash 2hours",
        "Settings": {
            "AutoUpdate": 1,
            "UpdateInterval": 120,
            "SecondsToShow": 7200,
            "LeftLegend": "",
            "LeftMax": 0,
            "LeftMin": 0,
            "RightLegend": "MHash/s",
            "RightMax": 0,
            "RightMin": 0,
            "Title": "Ethereum Mining - Last Two Hours",
            "Interpolation": "linear",
            "TickLine": 0,
            "HideDateLabel": 0,
            "HideLegend": 0,
            "HideXAxis": 0,
            "HideAxisLeft": 0,
            "HideAxisRight": 0,
            "HideButtons": 0,
            "HideLeftControls": 0,
            "HideRightControls": 0,
            "graphSensors": [
                {
                    "Name": "Hashrate-AQUA",
                    "Unit": "EthereumMiningAqua",
                    "Sensor": "hashrate",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "340.00",
                    "Filter": "0.00",
                    "Smoothing": "1.00"
                },
                {
                    "Name": "Hashrate-MAD",
                    "Unit": "EthereumMiningMAD",
                    "Sensor": "hashrate",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "340.00",
                    "Filter": "0.00",
                    "Smoothing": "1.00"
                }
            ]
        }
    };
    var l3 = new LineGraph({containerId: 'graph3', data: source3});

</script>
</body>
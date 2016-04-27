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
    "Name": "CoolRoom 2hours",
    "Settings": {
        "AutoUpdate": 1,
        "UpdateInterval": 10,
        "SecondsToShow": 7200,
        "LeftLegend": "Degrees ",
        "LeftMax": 0,
        "LeftMin": 0,
        "RightLegend": "Degrees (C)",
        "RightMax": 0,
        "RightMin": 0,
        "Title": "Cool room - Last two hours ",
        "Interpolation": "linear",
        "TickLine": 1,
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
                "Name": "Binnen",
                "Unit": "KastTemperature1",
                "Sensor": "Temperature",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "15.00",
                "Filter": "-1.00",
                "Smoothing": "1.00"
            },
            {
                "Name": "Buiten",
                "Unit": "AirTemperature1",
                "Sensor": "Temperature",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "15.00",
                "Filter": "-1.00",
                "Smoothing": "1.00"
            },
            {
                "Name": "Water-to-AQUA",
                "Unit": "ManifoldTemp1",
                "Sensor": "InFlow",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "15.00",
                "Filter": "-1.00",
                "Smoothing": "1.00"
            },
            {
                "Name": "Water-from-AQUA",
                "Unit": "ManifoldTemp1",
                "Sensor": "OutFlow2",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "15.00",
                "Filter": "-1.00",
                "Smoothing": "1.00"
            },
            {
                "Name": "Water-in-AQUA",
                "Unit": "SpeedfanMonitor1",
                "Sensor": "OPT_FAN_1",
                "Axis": "Right",
                "Interpolation": "linear",
                "Frequency": "15.00",
                "Filter": "-1.00",
                "Smoothing": "1.00"
            }
        ]
    }
}
    var l1 = new LineGraph({containerId: 'graph1', data: source1});

    var source2 = {
        "Name": "CoolRoom 2days",
        "Settings": {
            "AutoUpdate": 0,
            "UpdateInterval": 10,
            "SecondsToShow": 172800,
            "LeftLegend": "Humidity (%)",
            "LeftMax": 0,
            "LeftMin": 0,
            "RightLegend": "Degrees (C)",
            "RightMax": 0,
            "RightMin": 0,
            "Title": "Cool Room - Last Two Days",
            "Interpolation": "linear",
            "TickLine": 1,
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
                    "Name": "Outside-Temp",
                    "Unit": "AirTemperature1",
                    "Sensor": "Temperature",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "240.00",
                    "Filter": "0.50",
                    "Smoothing": "15.00"
                },
                {
                    "Name": "Outside-Humidity",
                    "Unit": "AirTemperature1",
                    "Sensor": "Humidity",
                    "Axis": "Left",
                    "Interpolation": "linear",
                    "Frequency": "240.00",
                    "Filter": "1.00",
                    "Smoothing": "15.00"
                },
                {
                    "Name": "Inside-Temp",
                    "Unit": "KastTemperature1",
                    "Sensor": "Temperature",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "240.00",
                    "Filter": "0.50",
                    "Smoothing": "15.00"
                },
                {
                    "Name": "Inside-Humidity",
                    "Unit": "KastTemperature1",
                    "Sensor": "Humidity",
                    "Axis": "Left",
                    "Interpolation": "linear",
                    "Frequency": "240.00",
                    "Filter": "1.00",
                    "Smoothing": "15.00"
                }
            ]
        }
    };
    var l2 = new LineGraph({containerId: 'graph2', data: source2});

    var source3 = {
        "Name": "CoolRoom 10min",
        "Settings": {
            "AutoUpdate": 1,
            "UpdateInterval": 10,
            "SecondsToShow": 600,
            "LeftLegend": "Humidity (%)",
            "LeftMax": 0,
            "LeftMin": 0,
            "RightLegend": "Degrees (C)",
            "RightMax": 0,
            "RightMin": 0,
            "Title": "Cool Room Temps - Last 10 minutes",
            "Interpolation": "linear",
            "TickLine": 1,
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
                    "Name": "Outside-Temp",
                    "Unit": "AirTemperature1",
                    "Sensor": "Temperature",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "-1.00",
                    "Smoothing": "1.00"
                },
                {
                    "Name": "Ouside-Raspi",
                    "Unit": "RaspiTemp2",
                    "Sensor": "Value",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "-1.00",
                    "Smoothing": "1.00"
                },
                {
                    "Name": "Inside-Temp",
                    "Unit": "KastTemperature1",
                    "Sensor": "Temperature",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "-1.00",
                    "Smoothing": "1.00"
                },
                {
                    "Name": "House-Raspi",
                    "Unit": "RaspiTemp1",
                    "Sensor": "Value",
                    "Axis": "Right",
                    "Interpolation": "linear",
                    "Frequency": "15.00",
                    "Filter": "-1.00",
                    "Smoothing": "1.00"
                }
            ]
        }
    };
    var l3 = new LineGraph({containerId: 'graph3', data: source3});

</script>
</body>
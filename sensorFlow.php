<html>
<head>
    <title>JPH Dashboard - Aqua Flow Graph</title>
    <!-- TODO Load all of these using a CDN and put it in a php-include file-->
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
	var source1 = { "Name":"Graph1",
        "Settings": {
            "graphTitle":"Analysis1 - Last Two Hours",
            "graphLeftLegend":"Liters (pm)",
            "graphRightLegend":"Degrees (C)",
            "graphInterpolation":"before",
            "graphSecondsToShow":7200,
            "graphAutoUpdate":1,
            "graphUpdateInterval":5,
            "graphTickLine":1
        },
        "Sensors": [{
            "Name":"Flow",
            "Source":"ArduinoMonitor1",
            "Column":"litersperminute",
            "AxisLocation":"Left",
            "Interpolation":"linear",
            "UpdateGapSeconds":"15",
            "FilterTolerance":"0.2",
            "LPFsmoothing":"30"
        },{
            "Name":"RaspTemp",
            "Source":"RaspiTemp1",
            "Column":"Value",
            "AxisLocation":"Right",
            "Interpolation":"linear",
            "UpdateGapSeconds":"15",
            "FilterTolerance":"0.7",
            "LPFsmoothing":"15"
        }]
    }
	var l1 = new LineGraph({containerId: 'graph1', data: source1});

something = window.open("data:text/json," + encodeURI(JSON.stringify(source1)), "_blank");

</script>
</body>
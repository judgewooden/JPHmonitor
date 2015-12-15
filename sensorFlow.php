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
	var source1 = {};
    	// The source of the data and how to display it
        source1["sensorDisplayName"] = ["Flow", "RaspiTemp"];
        source1["sensorSource"] = ["ArduinoMonitor1", "RaspiTemp1"];
        source1["sensorColumn"] = ["litersperminute", "Value"];
        source1["sensorAxisLocation"] = ["Left", "Right"];
        source1["sensorInterpolation"] = ["linear", "linear"];
        source1["sensorUpdateGapSeconds"] = [15, 15];
        source1["sensorFilterTolerance"] = [.2, .7];
        source1["sensorLPFsmoothing"] = [30, 15];

    	// Instructions for the graph
        source1["graphTitle"] = "Arduino Flow (Last Two Hours)";
        source1["graphLeftLegend"] = "Liters (pm)";
        //source1["graphLeftMin"] = 0;
        //source1["graphLeftMax"] = 5;
        source1["graphRightLegend"] = "Degrees (C)";
        //source1["graphRightMax"] = 49;
        //source1["graphRightMin"] = 46;
        source1["graphInterpolation"] = ["linear"];
    	source1["graphSecondsToShow"] = 7200;
    	source1["graphAutoUpdate"] = 1;
    	source1["graphUpdateInterval"] = 5;     // Will update the graph every X seconds (default:2)
    	source1["graphTickLine"] = 1;
    	var l1 = new LineGraph({containerId: 'graph1', data: source1});

</script>
</body>
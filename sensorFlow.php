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
<div id="graph2" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>
<div id="graph3" class="aGraph" style="float:left;position:relative;width:49%;height:200px"></div>
<div id="graph4" class="aGraph" style="float:left;position:relative;width:100%;height:400px"></div>

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
        source1["graphLeftMin"] = 0;
        source1["graphLeftMax"] = 5;
        source1["graphRightLegend"] = "Degrees (C)";
        //source1["graphRightMax"] = 49;
        //source1["graphRightMin"] = 46;
        source1["graphInterpolation"] = ["linear"];
    	source1["graphSecondsToShow"] = 7200;
    	source1["graphAutoUpdate"] = 1;
    	source1["graphUpdateInterval"] = 5;     // Will update the graph every X seconds (default:2)
    	source1["graphTickLine"] = 1;
    	var l1 = new LineGraph({containerId: 'graph1', data: source1});
/*
    var source2 = {};
        // The source of the data and how to display it
        source2["sensorDisplayName"] = ["Flow"];
        source2["sensorSource"] = ["ArduinoMonitor1"];
        source2["sensorColumn"] = ["LitersPerMinute"];
        source2["sensorAxisLocation"] = ["Left"];
        source2["sensorInterpolation"] = ["basis"];
        source2["sensorUpdateGapSeconds"] = [600];
        source2["sensorFilterTolerance"] = [0.5];
        source2["sensorLPFsmoothing"] = [15.0];

        // Instructions for the graph
        source2["graphTitle"] = "Arduino Flow 2-days (not updating)";   // Title of grap top left
        source2["graphLeftLegend"] = "Liters (pm)";
        //source2["graphLeftMin"] = 0;
        //source2["graphLeftMax"] = 1;
        //source2["graphLeftLegend"] = "Degrees (C)";
        //source2["graphRightMin"] = -0.1;
        //source2["graphRightMax"] = 1.1;
        source2["graphRightLegend"] = "Power: 1=on / 0=off";
        source2["graphSecondsToShow"] = 172800;
        source2["graphAutoUpdate"] = 0;
        source2["graphTickLine"] = 0;
        var l2 = new LineGraph({containerId: 'graph2', data: source2});

    var source3 = {};
        // The source of the data and how to display it
        source3["sensorDisplayName"] = ["Flow-Raw"];
        source3["sensorSource"] = ["ArduinoMonitor1"];
        source3["sensorColumn"] = ["LitersPerMinute"];
        source3["sensorAxisLocation"] = ["Right"];
        source3["sensorInterpolation"] = ["basis"];
        source3["sensorUpdateGapSeconds"] = [10];
        source3["sensorFilterTolerance"] = [-1];
        source3["sensorLPFsmoothing"] = [1.0];

        // Instructions for the graph
        source3["graphTitle"] = "Arduino Flow 5 minutes";   // Title of grap top left
        source3["graphLeftLegend"] = "Liters (pm)";
        //source3["graphLeftMin"] = 0;
        //source3["graphLeftMax"] = 1;
        //source3["graphLeftLegend"] = "Degrees (C)";
        source3["graphRightMin"] = 0;
        source3["graphRightMax"] = 5;
        source3["graphRightLegend"] = "Liters (pm)";
        source3["graphSecondsToShow"] = 300;
        source3["graphAutoUpdate"] = 1;
        source3["graphTickLine"] = 0;
        var l3 = new LineGraph({containerId: 'graph3', data: source3});
/*
    var source3 = {};
    	// The source of the data and how to display it
        source3["sensorDisplayName"] = ["ServerTemp"];
    	source3["sensorSource"] = ["RaspiTemp1"];
    	source3["sensorColumn"] = ["Value"];
    	source3["sensorAxisLocation"] = ["Right"];
        source3["sensorUpdateGapSeconds"] = [10];
        source3["sensorFilterTolerance"] = [-1];
        source3["sensorLPFsmoothing"] = [1.0];
    	// Instructions for how the graph
    	source3["graphSecondsToShow"] = 0;
    	source3["graphTitle"] = "Server Temp 24-hours (not updating)";
    	source3["graphTickLine"] = 0;
    	source3["graphRightMin"] = 46;
    	source3["graphRightMax"] = 44;
    	source3["graphUpdateInterval"] = 30;
    	var l3 = new LineGraph({containerId: 'graph3', data: source3});
*/

</script>
</body>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Dynamic Graphs</title>
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

<script>
function searchToObject(search) {
    return search.substring(1).split("&").reduce(function(result, value) {
        var parts = value.split('=');
        if (parts[0]) result[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
        return result;
    }, {})
}
var obj = searchToObject(window.location.search);
var result = JSON.parse(obj.search);
var l1 = new LineGraph({containerId: 'graph1', data: JSON.parse(obj.search)});
</script>
</body>
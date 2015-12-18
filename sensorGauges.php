<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>Gauges</title>
    <link rel="stylesheet" href="../../jqwidgets/styles/jqx.base.css" type="text/css" />

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <!-- jQWidgets CSS -->
    <link href="jqwidgets/styles/jqx.base.css" rel="stylesheet">
    <link href="jqwidgets/styles/jqx.bootstrap.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdraw.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgauge.js"></script>
    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">
    <style type="text/css">
        #ArduinoFlowValue {
            background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #fafafa), color-stop(100%, #f3f3f3));
            background-image: -webkit-linear-gradient(#fafafa, #f3f3f3);
            background-image: -moz-linear-gradient(#fafafa, #f3f3f3);
            background-image: -o-linear-gradient(#fafafa, #f3f3f3);
            background-image: -ms-linear-gradient(#fafafa, #f3f3f3);
            background-image: linear-gradient(#fafafa, #f3f3f3);
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -ms-border-radius: 3px;
            -o-border-radius: 3px;
            border-radius: 3px;
            -webkit-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }
        #raspiTempValue {
            background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #fafafa), color-stop(100%, #f3f3f3));
            background-image: -webkit-linear-gradient(#fafafa, #f3f3f3);
            background-image: -moz-linear-gradient(#fafafa, #f3f3f3);
            background-image: -o-linear-gradient(#fafafa, #f3f3f3);
            background-image: -ms-linear-gradient(#fafafa, #f3f3f3);
            background-image: linear-gradient(#fafafa, #f3f3f3);
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -ms-border-radius: 3px;
            -o-border-radius: 3px;
            border-radius: 3px;
            -webkit-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
            padding: 10px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#arduinoFlowGauge').jqxGauge({
                ranges: [{ startValue: 0, endValue: 2, style: { fill: '#e02629', stroke: '#e02629' }, endWidth: 5, startWidth: 1 },
                         { startValue: 2, endValue: 4, style: { fill: '#ff8000', stroke: '#ff8000' }, endWidth: 10, startWidth: 5 },
                         { startValue: 4, endValue: 6, style: { fill: '#fbd109', stroke: '#fbd109' }, endWidth: 13, startWidth: 10 },
                         { startValue: 6, endValue: 7, style: { fill: '#4bb648', stroke: '#4bb648' }, endWidth: 16, startWidth: 13 }],
                ticksMinor: { interval: .5, size: '3%' },
                ticksMajor: { interval: 1, size: '9%' },
                value: 0,
                max: 7,
                labels: { visible: true, position: 'inside', interval: 1 },
                colorScheme: 'scheme05',
                animationDuration: 1,
                caption: { offset: [0, 35], value: 'No Last Value', position: 'top' },
            });
            $('#raspiTempGauge').jqxGauge({
                ranges: [{ startValue: 0, endValue: 20, style: { fill: '#4bb648', stroke: '#e02629' }, endWidth: 5, startWidth: 1 },
                         { startValue: 20, endValue: 60, style: { fill: '#fbd109', stroke: '#fbd109' }, endWidth: 13, startWidth: 5 },
                         { startValue: 60, endValue: 80, style: { fill: '#e02629', stroke: '#e02629' }, endWidth: 16, startWidth: 13 }],
                ticksMinor: { interval: 5, size: '3%' },
                ticksMajor: { interval: 10, size: '9%' },
                value: 0,
                max: 100,
                labels: { visible: true, position: 'inside', interval: 10 },
                colorScheme: 'scheme02',
                animationDuration: 1,
                caption: { offset: [0, 35], value: 'No Last Value', position: 'top' },
            });
            $('#arduinoFlowGauge').on('valueChanging', function (e) {
                $('#ArduinoFlowValue').text(e.args.value.substring(0,4) + ' lpm');
            });
            $('#raspiTempGauge').on('valueChanging', function (e) {
                $('#raspiTempValue').text(e.args.value.substring(0,4) + ' C');
            });
            var ttimer = setInterval(function () {
                url="sensorSQLvalue.php?source=ArduinoMonitor1&column=litersperminute&UpdateGapSeconds=15";
                //console.log(url);
                jQuery.getJSON(url).done(function(response) {
                    if ( response.length > 0 ) {
                        $('#arduinoFlowGauge').jqxGauge('caption', { offset: [0, -55], value: response[0].timestamp, position: 'bottom' });
                        $('#arduinoFlowGauge').jqxGauge('value', response[0].value);
                    }
                    else
                        $('#arduinoFlowGauge').jqxGauge('value', 0);
                });
                url="sensorSQLvalue.php?source=RaspiTemp1&column=Value&UpdateGapSeconds=15";
                //console.log(url);
                jQuery.getJSON(url).done(function(response) {
                    if ( response.length > 0 ) {
                        $('#raspiTempGauge').jqxGauge('caption', { offset: [0, -55], value: response[0].timestamp, position: 'bottom' });
                        $('#raspiTempGauge').jqxGauge('value', response[0].value);
                    }
                    else
                        $('#raspiTempGauge').jqxGauge('value', 0);
                });
            }, 5000);

        });
    </script>
</head>
<body style="background:white;">
<?php include("menubar.html"); ?>
    <div id="ArduinoFlow1" style="position: relative;">
        <div style="float: left;" id="arduinoFlowGauge"></div>
        <div id="ArduinoFlowValue" style="position: absolute; top: 256px; left: 125px; font-family: Sans-Serif; text-align: center; font-size: 17px; width: 100px;"></div>
        <div style="position: absolute; top: 230px; left: 125px; font-family: Sans-Serif; text-align: center; font-size: 17px; width: 100px;">Arduino Flow</div>
        <div style="margin-left: 60px; float: left;" id="raspiTempGauge"></div>
        <div id="raspiTempValue" style="position: absolute; top: 256px; left: 537px; font-family: Sans-Serif; text-align: center; font-size: 17px; width: 100px;"></div>
        <div style="position: absolute; top: 230px; left: 537px; font-family: Sans-Serif; text-align: center; font-size: 17px; width: 100px;">Raspi Temp</div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>Cauges</title>
    <link rel="stylesheet" href="../../jqwidgets/styles/jqx.base.css" type="text/css" />

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <!-- jQWidgets CSS -->
    <link href="jqwidgets/styles/jqx.base.css" rel="stylesheet">
    <link href="jqwidgets/styles/jqx.bootstrap.css" rel="stylesheet">
<!--
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../scripts/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
-->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdraw.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxchart.core.js"></script>
    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">

    <script type="text/javascript">
        $(document).ready(function () {
            // prepare the data
            var secondsToShow = 600;
            var source =
            {
                datatype: "csv",
                datafields: [
                    { name: 'Date' },
                    { name: 'S&P 500' },
                    { name: 'NASDAQ' }
                ],
                url: '../sampledata/nasdaq_vs_sp500.txt'
            };
            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            // prepare jqxChart settings
            var settings = {
                title: "U.S. Stock Market Index Performance",
                description: "NASDAQ Composite compared to S&P 500",
                enableAnimations: true,
                showLegend: true,
                padding: { left: 10, top: 5, right: 10, bottom: 5 },
                titlePadding: { left: 50, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                {
                    dataField: 'timestamp',
                    type: 'date',
                    baseUnit: 'second',
                    initInterval
                    valuesOnTicks: true,
                    minValue: '01-01-2014',
                    maxValue: '01-01-2015',
                    tickMarks: {
                        visible: true,
                        interval: 1,
                        color: '#BCBCBC'
                    },
                    unitInterval: 1,
                    gridLines: {
                        visible: true,
                        interval: 3,
                        color: '#BCBCBC'
                    },
                    labels: {
                        angle: -45,
                        rotationPoint: 'topright',
                        offset: { x: 0, y: -25 }
                    }
                },
                valueAxis:
                {
                    visible: true,
                    title: { text: 'Flow Details<br>' },
                    tickMarks: { color: '#BCBCBC' }
                },
                colorScheme: 'scheme04',
                seriesGroups:
                    [
                        {
                            type: 'line',
                            series: [
                                    { dataField: 'S&P 500', displayText: 'S&P 500' },
                                ]
                        }
                    ]
            };
            // setup the chart
            $('#chartContainer').jqxChart(settings);
        });
    </script>
</head>
<body class='default'>
<?php include("menubar.html"); ?>
    <div id='chartContainer' style="width:850px; height:500px">
    </div>
</body>
</html>
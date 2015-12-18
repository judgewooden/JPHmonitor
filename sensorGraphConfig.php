<html>
<head>
    <title id='Description'>Custom Graph</title>
    <link rel="stylesheet" href="../../jqwidgets/styles/jqx.base.css" type="text/css" />

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <!-- jQWidgets CSS -->
    <link href="jqwidgets/styles/jqx.base.css" rel="stylesheet">
    <link href="jqwidgets/styles/jqx.bootstrap.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdata.export.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxlistbox.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxnumberinput.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgrid.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgrid.columnsresize.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgrid.export.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxgrid.edit.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxdocking.js"></script>
    <script type="text/javascript" src="../../jqwidgets/jqxwindow.js"></script>
    <link rel="stylesheet" type="text/css" href="sensorGraphStyle.css">

</head>
<body>
<?php include("menubar.html"); ?>
<div id="content">
    <script type="text/javascript">
        $(document).ready(function () {
            /*
             * some default values
             */
            var axes=["Left","Right"];
            var customInterpolations=["linear","step"];
            var validSensors=[];
            var generaterow = function () {
                var row = {};
                row["Name"] = "-blank-";
                row["Source"] = "RaspiTemp1";
                row["Column"] = "Value";
                row["AxisLocation"] = "Left";
                row["Interpolation"] = "linear";
                row["UpdateGapSeconds"] = 5;
                row["FilterTolerance"] = 0;
                row["LPFsmoothing"] = 1;
                return row;
            }

            // Download the valid tables from SQL (these should contain all the sensors)
            var sourceSQL =
            {
                datatype: "json",
                datafields: [
                    { name: 'TABLE_NAME' },
                    { name: 'COLUMN_NAME' }
                ],
                url: "sensorSQLmeta.php"
            };
            var dataAdapterSQL = new $.jqx.dataAdapter(sourceSQL, { autoBind: true,
                loadError: function (xhr, status, error) {
                    alert('Error loading "' + sourceSQL.url + '" : ' + error);
                },
                loadComplete: function (response) {
                    lastvalue="";
                    for (var key in response) {
                        if (response[key].TABLE_NAME != lastvalue) {
                            lastvalue=response[key].TABLE_NAME;
                            validSensors.push(lastvalue);
                        }
                    }
                }
            });

            var sourceGraphs =
            {
                datatype: "json",
                datafields: [
                    { name: 'Name' },
                    { name: 'Settings' }
                ],
                url: "graphList.json"
            };
            var dataAdapterGraph = new $.jqx.dataAdapter(sourceGraphs, {
                loadError: function (xhr, status, error) {
                    alert('Error loading "' + sourceGraphs.url + '" : ' + error);
                }
            });

            // Create a jqxListBox
            $("#graphList").jqxListBox({ source: dataAdapterGraph,
                displayMember: "Name", valueMember: "Settings", width: 120, height: 470});
            $("#graphList").on('select', function (event) {
                if (event.args) {
                    var item = event.args.item;
                    if (item) {
                        $("#graphAutoUpdate").val(item.value.graphAutoUpdate);
                        $("#graphUpdateInterval").val(item.value.graphUpdateInterval);
                        $("#graphSecondsToShow").val(item.value.graphSecondsToShow);
                        $("#graphLeftLegend").val(item.value.graphLeftLegend);
                        $("#graphLeftMax").val(item.value.graphLeftMax);
                        $("#graphLeftMin").val(item.value.graphLeftMin);
                        $("#graphRightLegend").val(item.value.graphRightLegend);
                        $("#graphRightMax").val(item.value.graphRightMax);
                        $("#graphRightMin").val(item.value.graphRightMin);
                        $("#graphTitle").val(item.value.graphTitle);
                        $("#graphInterpolation").val(item.value.graphInterpolation);
                        $("#graphTickLine").val(item.value.graphTickLine);
                        var sourceSensors = {
                            localdata: item.value.Sensors,
                            datatype: "array",
                            datafields: [
                                { name: 'Name', type: 'string' },
                                { name: 'Source', type: 'string' },
                                { name: 'Column', type: 'string' },
                                { name: 'AxisLocation', type: 'string' },
                                { name: 'Interpolation', type: 'string' },
                                { name: 'UpdateGapSeconds', type: 'number' },
                                { name: 'FilterTolerance', type: 'number' },
                                { name: 'LPFsmoothing', type: 'number' }
                            ]
                        };
                        var dataAdapterSensors = new $.jqx.dataAdapter(sourceSensors, {
                            loadError: function (xhr, status, error) {
                                alert('Error loading "' + sourceSensors.url + '" : ' + error);
                            }
                        });
                        $("#sensorsGrid").jqxGrid( {
                            width: 755,
                            source: dataAdapterSensors,
                            columnsresize: true,
                            editable: true,
                            showtoolbar: true,
                            rendertoolbar: function (toolbar) {
                                var me = this;
                                var container = $("<div style='margin: 5px;'></div>");
                                toolbar.append(container);
                                container.append('<input id="addrowbutton" type="button" value="Add New Sensor" />');
                                container.append('<input style="margin-left: 5px;" id="deleterowbutton" type="button" value="Delete Selected Sensor" />');
                                $("#addrowbutton").jqxButton();
                                $("#addrowbutton").on('click', function (event) {
                                    if (event.handled !== true) {
                                            //put your code here
                                    var datarow = generaterow();
                                    var commit = $("#sensorsGrid").jqxGrid('addrow', null, datarow);
                                        event.handled = true;
                                    }
                                    return false;
                                });
                                $("#deleterowbutton").jqxButton();
                                $("#deleterowbutton").on('click', function () {
                                    var selectedrowindex = $("#sensorsGrid").jqxGrid('getselectedrowindex');
                                    var rowscount = $("#sensorsGrid").jqxGrid('getdatainformation').rowscount;
                                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                        var id = $("#sensorsGrid").jqxGrid('getrowid', selectedrowindex);
                                        var commit = $("#sensorsGrid").jqxGrid('deleterow', id);
                                    }
                                });
                            },
                            columns: [
                                { text: 'Name', datafield: 'Name', width: 120,
                                    validation: function (cell, value) {
                                        if (value.length < 1) {
                                            return { result: false, message: "Can not be empty" };
                                        }
                                        return true;
                                    }
                                },
                                { text: 'Unit', columntype: 'dropdownlist', datafield: 'Source', width: 120,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: validSensors });
                                    },
                                    cellvaluechanging: function (row, datafield, columntype, oldvalue, newvalue) {
                                        if (newvalue != oldvalue) {
                                        // TODO: There is a bug where I can no reset the value
                                        //   $("#sensorsGrid").jqxGrid('setcellvalue', row, "Value", "");
                                        };
                                    }
                                },
                                { text: 'Sensor', columntype: 'dropdownlist', datafield: 'Column', width: 120,
                                    initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                        var currentSensor = $('#sensorsGrid').jqxGrid('getcellvalue', row, "Source");
                                        var currentColumn = editor.val();

                                        var validColumns = new Array();
                                        for (var key in dataAdapterSQL.records) {
                                            if (dataAdapterSQL.records[key].TABLE_NAME == currentSensor) {
                                                if (dataAdapterSQL.records[key].COLUMN_NAME != "Timestamp")
                                                    validColumns.push(dataAdapterSQL.records[key].COLUMN_NAME);
                                            }
                                        };

                                        editor.jqxDropDownList({ autoDropDownHeight: true, source: validColumns });
                                        if (currentColumn != "") {
                                            var index = validColumns.indexOf(currentColumn);
                                            editor.jqxDropDownList('selectIndex', index);
                                        }
                                    },
                                    validation: function (cell, value) {
                                        var currentSensor = $('#sensorsGrid').jqxGrid('getcellvalue', cell.row, "Source");
                                        for (var key in dataAdapterSQL.records) {
                                            if (dataAdapterSQL.records[key].TABLE_NAME == currentSensor) {
                                                if (dataAdapterSQL.records[key].COLUMN_NAME == value) {
                                                    return true;
                                                }
                                            }
                                        }
                                        return { result: false, message: "You must select a valid Sensor" };
                                    }
                                },
                                { text: 'Axis', columntype: 'dropdownlist', datafield: 'AxisLocation', width: 60,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: axes });
                                    }
                                },
                                { name: 'Interpolation', columntype: 'dropdownlist', datafield: 'Interpolation', width: 100,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: customInterpolations });
                                    }
                                },
                                { text: 'Frequency', datafield: 'UpdateGapSeconds', width: 85,
                                    align: 'right', cellsalign: 'right', columntype: 'numberinput',
                                    validation: function (cell, value) {
                                        if (value < 0) {
                                            return { result: false, message: "Can not be negative" };
                                        }
                                        return true;
                                    }
                                },
                                { text: 'Filter', datafield: 'FilterTolerance', width: 60,
                                    align: 'right', cellsalign: 'right', columntype: 'numberinput',
                                    createeditor: function (row, cellvalue, editor) {
                                        editor.jqxNumberInput({ decimalDigits: 1, digits: 2 });
                                    },
                                    validation: function (cell, value) {
                                        if (value < 0) {
                                            return { result: false, message: "Can not be negative" };
                                        }
                                        return true;
                                    }
                                },
                                { text: 'Smoothing', datafield: 'LPFsmoothing', width: 90,
                                    align: 'right', cellsalign: 'right', columntype: 'numberinput',
                                    createeditor: function (row, cellvalue, editor) {
                                        editor.jqxNumberInput({ decimalDigits: 2, digits: 3 });
                                    },
                                    validation: function (cell, value) {
                                        if (value < 1) {
                                            return { result: false, message: "Must be 1 or greater" };
                                        }
                                        return true;
                                    }
                                }
                            ]
                        });
                        $("#jsonExport").click(function () {
                            var toexport = JSON.parse($("#sensorsGrid").jqxGrid('exportdata', 'json'));
                            var graphAutoUpdate=$("#graphAutoUpdate").val();
                            var graphUpdateInterval= $("#graphUpdateInterval").val();
                            var graphSecondsToShow= $("#graphSecondsToShow").val();
                            var graphLeftLegend= $("#graphLeftLegend").val();
                            var graphLeftMax= $("#graphLeftMax").val();
                            var graphLeftMin= $("#graphLeftMin").val();
                            var graphRightLegend= $("#graphRightLegend").val();
                            var graphRightMax= $("#graphRightMax").val();
                            var graphRightMin= $("#graphRightMin").val();
                            var graphTitle= $("#graphTitle").val()
                            var graphInterpolation= $("#graphInterpolation").val();
                            var graphTickLine= $("#graphTickLine").val();
                            var answer = {
                                Name: "Graph1",
                                Settings: {
                                    graphAutoUpdate: graphAutoUpdate,
                                    graphUpdateInterval: graphUpdateInterval,
                                    graphSecondsToShow: graphSecondsToShow,
                                    graphLeftLegend: graphLeftLegend,
                                    graphLeftMax: graphLeftMax,
                                    graphLeftMin: graphLeftMin,
                                    graphRightLegend: graphRightLegend,
                                    graphRightMax: graphRightMax,
                                    graphRightMin: graphRightMin,
                                    graphTitle: graphTitle,
                                    graphInterpolation: graphInterpolation,
                                    graphTickLine: graphTickLine
                                },
                                Sensors: toexport
                            };
                            var jsonpretty=JSON.stringify(answer);

                            var newWindow = window.open('', '', 'width=800, height=500, resizable=1'),
                            document = newWindow.document.open(),
                            pageContent =
                                     '<!DOCTYPE html>' +
                                     '<html>' +
                                     '<head>' +
                                     '<meta charset="utf-8" />' +
                                     '<title>Safe your Graph</title>' +
                                     '</head>' +
                                     '<body>' + jsonpretty + '</body></html>';
                            document.write(pageContent);
                            document.close();
                        });
                    }
                }
            });
        });
    </script>
</div>

<div id="graphList" style="float:left;position:relative;margin-left:5px;margin-top:10px;width:120px">
</div>

<div id="graphDetails" class="container" style="float:left;position:relative;margin-left:5px;margin-top:10px;width:400px;height:480px;border:1px solid black">
    <div class="row" margin-bottom: 3px>
        <div class="col-1 well">Graph</div>
    </div>
    <div class="row">
        <div class="col-xs-3" style="text-align:right">Title:</div>
        <div class="col-xs-9">
            <input type="text" style="width:250px" id="graphTitle">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Seconds:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphSecondsToShow">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Auto:</div>
        <div class="col-xs-9">
            <select type="text" id="graphAutoUpdate">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Interval:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphUpdateInterval">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Line Tics:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphTickLine">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Interpolation:</div>
        <div class="col-xs-9">
            <select id="graphInterpolation" name="graphInterpolation">
                <option value="linear">linear</option>
                <option value="step-before">step-before</option>
                <option value="step-after">step-after</option>
                <option value="basis">basis</option>
                <option value="basis-open">basis-open</option>
                <option value="basis-closed">basis-closed</option>
                <option value="cardinal">cardinal</option>
                <option value="cardinal-open">cardinal-open</option>
                <option value="cardinal-closed">cardinal-closed</option>
                <option value="monotone">monotone</option>
                <option value="custom">custom</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-12" style="text-decoration:underline;">Left Legend</div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Minimum:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphLeftMin">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Msximum:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphLeftMax">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Legend:</div>
        <div class="col-xs-9">
            <input type="text" style="width:180px" id="graphLeftLegend">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-12" style="text-decoration:underline;">Right Legend</div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Minimum:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphRightMin">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Maximum:</div>
        <div class="col-xs-9">
            <input type="number" style="width:80px" id="graphRightMax">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Legend:</div>
        <div class="col-xs-9">
            <input type="text" style="width:180px" id="graphRightLegend">
        </div>
    </div>
</div>

<div id="sensorsGrid" style="float:left;position:relative;margin-left:5px;margin-top:10px;width:400px">
</div>

<div style='float:left;position:relative;margin-left:10px;margin-top:10px'>
    <input type="button" value="JSON" id='jsonExport' />
</div>

</body>
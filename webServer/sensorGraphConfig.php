<!DOCTYPE html>
<html lang="es">
<head>
    <title id='Description'>Custom Graph</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
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
            var validUnits=[];
            var generaterow = function () {
                var row = {};
                row["Name"] = "-blank-";
                row["Unit"] = "RaspiTemp1";
                row["Sensor"] = "Value";
                row["Axis"] = "Left";
                row["Interpolation"] = "linear";
                row["Frequency"] = 5;
                row["Filter"] = 0;
                row["Smoothing"] = 1;
                return row;
            }
            var generatejson = function(naam) {
                var sensorarray = JSON.parse($("#sensorsGrid").jqxGrid('exportdata', 'json'));
                var graphAutoUpdate=+$("#graphAutoUpdate").val();
                var graphUpdateInterval= +$("#graphUpdateInterval").val();
                var graphSecondsToShow= +$("#graphSecondsToShow").val();
                var graphLeftLegend= $("#graphLeftLegend").val();
                var graphLeftMax= +$("#graphLeftMax").val();
                var graphLeftMin= +$("#graphLeftMin").val();
                var graphRightLegend= $("#graphRightLegend").val();
                var graphRightMax= +$("#graphRightMax").val();
                var graphRightMin= +$("#graphRightMin").val();
                var graphTitle= $("#graphTitle").val()
                var graphInterpolation= $("#graphInterpolation").val();
                var graphTickLine= +$("#graphTickLine").val();
                var graphHideDateLabel= +$("#graphHideDateLabel").val();
                var graphHideLegend= +$("#graphHideLegend").val();
                var graphHideXAxis= +$("#graphHideXAxis").val();
                var graphHideAxisLeft= +$("#graphHideAxisLeft").val();
                var graphHideAxisRight= +$("#graphHideAxisRight").val();
                var graphHideButtons= +$("#graphHideButtons").val();
                var graphHideLeftControls= +$("#graphHideLeftControls").val();
                var graphHideRightControls= +$("#graphHideRightControls").val();
                var answe3 = {
                    Name: naam,
                    Settings: {
                        AutoUpdate: graphAutoUpdate,
                        UpdateInterval: graphUpdateInterval,
                        SecondsToShow: graphSecondsToShow,
                        LeftLegend: graphLeftLegend,
                        LeftMax: graphLeftMax,
                        LeftMin: graphLeftMin,
                        RightLegend: graphRightLegend,
                        RightMax: graphRightMax,
                        RightMin: graphRightMin,
                        Title: graphTitle,
                        Interpolation: graphInterpolation,
                        TickLine: graphTickLine,
                        HideDateLabel: graphHideDateLabel,
                        HideLegend: graphHideLegend,
                        HideXAxis: graphHideXAxis,
                        HideAxisLeft: graphHideAxisLeft,
                        HideAxisRight: graphHideAxisRight,
                        HideButtons: graphHideButtons,
                        HideLeftControls: graphHideLeftControls,
                        HideRightControls: graphHideRightControls,
                        graphSensors: sensorarray
                    }
                }
                console.log(answe3);
                return answe3;
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
                            validUnits.push(lastvalue);
                        }
                    }
                }
            });

            // Download the list of pre-configured graphs
            var sourceGraphs =
            {
                datatype: "json",
                datafields: [
                    { name: 'Name' },
                    { name: 'Settings' },
                    { name: 'Sensors' }
                ],
                url: "graphList.json"
            };
            var dataAdapterGraph = new $.jqx.dataAdapter(sourceGraphs, {
                loadError: function (xhr, status, error) {
                    alert('Error loading "' + sourceGraphs.url + '" : ' + error);
                },
                loadComplete: function (response) {
                    console.log("Raw: ", response);
                }
            });

            // Create a jqxListBox
            $("#graphList").jqxListBox({ source: dataAdapterGraph,
                displayMember: "Name", valueMember: "Settings", width: 120, height: 470});
            $("#graphList").on('select', function (event) {
                if (event.args) {
                    var item = event.args.item;
                    if (item) {
                        $("#graphAutoUpdate").val(item.value.AutoUpdate);
                        $("#graphUpdateInterval").val(item.value.UpdateInterval);
                        $("#graphSecondsToShow").val(item.value.SecondsToShow);
                        $("#graphLeftLegend").val(item.value.LeftLegend);
                        $("#graphLeftMax").val(item.value.LeftMax);
                        $("#graphLeftMin").val(item.value.LeftMin);
                        $("#graphRightLegend").val(item.value.RightLegend);
                        $("#graphRightMax").val(item.value.RightMax);
                        $("#graphRightMin").val(item.value.RightMin);
                        $("#graphTitle").val(item.value.Title);
                        $("#graphInterpolation").val(item.value.Interpolation);
                        $("#graphTickLine").val(item.value.TickLine);
                        $("#graphHideDateLabel").val(item.value.HideDateLabel);
                        $("#graphHideLegend").val(item.value.HideLegend);
                        $("#graphHideXAxis").val(item.value.HideXAxis);
                        $("#graphHideAxisLeft").val(item.value.HideAxisLeft);
                        $("#graphHideAxisRight").val(item.value.HideAxisRight);
                        $("#graphHideButtons").val(item.value.HideButtons);
                        $("#graphHideLeftControls").val(item.value.HideLeftControls);
                        $("#graphHideRightControls").val(item.value.HideRightControls);
                        var sourceSensors = {
                            localdata: item.value.graphSensors,
                            datatype: "array",
                            datafields: [
                                { name: 'Name', type: 'string' },
                                { name: 'Unit', type: 'string' },
                                { name: 'Sensor', type: 'string' },
                                { name: 'Axis', type: 'string' },
                                { name: 'Interpolation', type: 'string' },
                                { name: 'Frequency', type: 'number' },
                                { name: 'Filter', type: 'number' },
                                { name: 'Smoothing', type: 'number' }
                            ]
                        };
                        var dataAdapterSensors = new $.jqx.dataAdapter(sourceSensors, {
                            loadError: function (xhr, status, error) {
                                alert('Error loading "' + sourceSensors.url + '" : ' + error);
                            },
                            loadComplete: function (response) {
                                //console.log(response);
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
                                { text: 'Unit', columntype: 'dropdownlist', datafield: 'Unit', width: 120,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: validUnits });
                                    },
                                    cellvaluechanging: function (row, datafield, columntype, oldvalue, newvalue) {
                                        if (newvalue != oldvalue) {
                                        // TODO: There is a bug where I can no reset the value
                                        //   $("#sensorsGrid").jqxGrid('setcellvalue', row, "Sensor", "");
                                        };
                                    }
                                },
                                { text: 'Sensor', columntype: 'dropdownlist', datafield: 'Sensor', width: 120,
                                    initeditor: function (row, cellvalue, editor, celltext, cellwidth, cellheight) {
                                        var currentSensor = $('#sensorsGrid').jqxGrid('getcellvalue', row, "Unit");
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
                                        var currentSensor = $('#sensorsGrid').jqxGrid('getcellvalue', cell.row, "Unit");
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
                                { text: 'Axis', columntype: 'dropdownlist', datafield: 'Axis', width: 60,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: axes });
                                    }
                                },
                                { name: 'Interpolation', columntype: 'dropdownlist', datafield: 'Interpolation', width: 100,
                                    createeditor: function (row, value, editor) {
                                        editor.jqxDropDownList({ source: customInterpolations });
                                    }
                                },
                                { text: 'Frequency', datafield: 'Frequency', width: 85,
                                    align: 'right', cellsalign: 'right', columntype: 'numberinput',
                                    validation: function (cell, value) {
                                        if (value < 0) {
                                            return { result: false, message: "Can not be negative" };
                                        }
                                        return true;
                                    }
                                },
                                { text: 'Filter', datafield: 'Filter', width: 60,
                                    align: 'right', cellsalign: 'right', columntype: 'numberinput',
                                    createeditor: function (row, cellvalue, editor) {
                                        editor.jqxNumberInput({ decimalDigits: 1, digits: 2 });
                                    }
                                },
                                { text: 'Smoothing', datafield: 'Smoothing', width: 90,
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
                        document.getElementById('graphWindow').style.visibility='visible';
                        $("#graphWindow").click(function (event) {
                            if (event.handled !== true) {
                                answer = generatejson("Graph1");
                                var myurl = JSON.stringify(answer);
                                var url="sensorGraphCustom.php?search=" + encodeURI( myurl );
                                var newWindow = window.open(url, '', 'width=800, height=500, resizable=0');
                                event.handled = true;
                            }
                            return false;
                        });
                        document.getElementById('jsonExport').style.visibility='visible';
                        $("#jsonExport").click(function (event) {
                            if (event.handled !== true) {
                                var naam = window.prompt("Name your Graph","Graph1");
                                if ( naam == null ) naam = "Graph1";
                                answer = generatejson(naam);
                                console.log("answer: ", answer);
                                var newWindow = window.open('', '', 'scrollbars=1, location=0, status=0, titlebar=0, toolbar=0, width=800, height=700, resizable=1'),
                                document = newWindow.document.open(),
                                pageContent =
                                         '<!DOCTYPE html>' +
                                         '<html>' +
                                         '<head>' +
                                         '<meta http-equiv="content-type" content="application/json; charset=utf-8" />' +
                                         '<title>Save your Graph</title>' +
                                         '</head>' +
                                         '<body><pre>' +
                                         JSON.stringify(answer, null, '\t'); +
                                         '</pre></body></html>';
                                document.write(pageContent);
                                document.close();
                                event.handled = true;
                            }
                            return false;
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
            <input type="text" style="width:260px" id="graphTitle">
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Seconds:</div>
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphSecondsToShow">
        </div>
        <div class="col-xs-3" style="text-align:right">Legend:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideLegend">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Auto:</div>
        <div class="col-xs-3">
            <select type="text" id="graphAutoUpdate">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="col-xs-3" style="text-align:right">Buttons:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideButtons">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Interval:</div>
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphUpdateInterval">
        </div>
        <div class="col-xs-3" style="text-align:right">X-Axis:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideXAxis">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Line Tics:</div>
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphTickLine">
        </div>
        <div class="col-xs-3" style="text-align:right">Date-Label:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideDateLabel">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
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
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphLeftMin">
        </div>
        <div class="col-xs-3" style="text-align:right">Left Axis:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideAxisLeft">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Maximum:</div>
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphLeftMax">
        </div>
        <div class="col-xs-3" style="text-align:right">Controls:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideLeftControls">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
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
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphRightMin">
        </div>
        <div class="col-xs-3" style="text-align:right">Right Axis:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideAxisRight">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top:3px">
        <div class="col-xs-3" style="text-align:right">Maximum:</div>
        <div class="col-xs-3">
            <input type="number" style="width:80px" id="graphRightMax">
        </div>
        <div class="col-xs-3" style="text-align:right">Controls:</div>
        <div class="col-xs-3">
            <select type="text" id="graphHideRightControls">
                <option value="1">Hide</option>
                <option value="0">Show</option>
            </select>
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

<div style='float:left;position:relative;margin-left:10px;margin-top:10px;visibility:hidden'>
    <input type="button" value="JSON" id='jsonExport' />
</div>

<div style='float:left;position:relative;margin-left:10px;margin-top:10px;visibility:hidden'>
    <input type="button" value="Graph" id='graphWindow' />
</div>

</body>
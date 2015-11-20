/***
	Courtesy: http://bl.ocks.org/benjchristensen/2657838
**/

/*
 * Create and draw a new graph
 *
 * Arguments:
 *	containerId => id of Containter to insert SVG
 *
 *  data => containing:
 *
 *  Array of Data Elements (Mandatory)
 *  ----------------------------------
 *  displayNames  => Name of the data series to the users on the Graph
 *  sensorSource  => The Database table containing the data (Primary key = 'Timestamp')
 *  sensorColumn  => The name of the SQL Column that function will plot in sensorSource
 *  yAxisLocation => Explain to what Axis this data series should be bound, values are "Left" or "Right"
 *
 *  Fields to control the behaviour of the Graph (Optional)
 *  -------------------------------------------------------
 *  graphSecondsToShow  => The X axis range in seconds, (will load data from SQL in this range)
 *                         If 0 a static graph will be loaded with all the data stored in the SQL
 *  graphAutoUpdate     => If 1 (the graph will auto update - if 0 it is static)
 *                         If 1 the graph will update every <grapUpdateInterval> seconds with new
 *                         data from SQL (default=0)
 *  graphUpdateInterval => Will update the graph every X seconds (default:2)
 *  graphTitle          => Title of graph top middle (default:"")
 *  graphTickLine       => Number of horizontal lines to show per tick (default:0);
 *  graphLeftMin        => The minimum value to show on the left Axis. If=0 auto adjust to data range. (default:0)
 *  graphLeftMax        => The maximum value to show on the left Axis. If=0 auto adjust to data range. (default:0)
 *  graphRightMin       => The minimum value to show on the right Axis. If=0 auto adjust to data range. (default:0)
 *  graphRightMax       => The maximum value to show on the right Axis. If=0 auto adjust to data range. (default:0)
 *  grepLeftLegend      => The legend to show for data on the left access
 *  grepRightLegend     => The legend to show for data on the right access
 *
 */

 function LineGraph(argsMap) {
 	/* *************************************************************** */
	/* public methods */
	/* *************************************************************** */
	var self = this;

	/* *************************************************************** */
	/* private variables */
	/* *************************************************************** */
	// the div we insert the graph into
	var containerId;
	var container;

	// Detail some behavior
	var myBehavior = {};
	var legendFontSize = 12;
	var transitionDuration = 300;

	// Details of the data
	var data = [];      // D3 data for each line
	var meta = {};      // meta data describing data for each line

	// D3 functions
	var parseDate = d3.time.format("%Y-%m-%d %H:%M:%S").parse;
	var bisectDate = d3.bisector(function(d) { return d.timestamp; }).right;

	// define dimensions
	var margin = [ 20, 40, 30, 40]; // margins (top, right, bottom, left)
	var w, h; // Width & height

	// D3 structures
	var graph;
	var x, xAxis;
	var yLeft, yAxisLeft, yRight, yAxisRight, haveYAxisLeft, haveYAxisRight;
	var color = d3.scale.category10();
	var drawline, linesGroup, lines, linesGroupText;
	var hoverContainer, hoverLine, hoverLineXOffset, hoverLineYOffset,
														hoverLineGroup;
	var lineFunctionSeriesIndex;    // special bodge !!! pay attention to it

	// user behavior
	var menuButtons = [['update','Updating'], ['pause','Pause']];
	var updatePaused = 'update';
	var userCurrentlyInteracting = false;
	var currentUserPositionX = -1;

	// scrolling graph fields
	var myInterval;
	var minTime = new Date();
	var maxTime = new Date();
	var inProgress = false;

	/* *************************************************************** */
	/* Initiationzation and Validation */
	/* *************************************************************** */

	var _init = function() {
		containerId = getRequiredVar(argsMap, 'containerId');
		container = document.querySelector('#' + containerId);

		loadData(getRequiredVar(argsMap, 'data'));
		debug("Continues with processData()");

	}

	/*
	 * Manager Data Update
	 */

	this.refreshData = function() {
		maxTime = new Date();
		minTime = new Date(maxTime .getTime()
								- 1000 * myBehavior.secondsToShow);

		if ( inProgress ){
			redrawAxes(false);
			redrawLines(false);
			return;
		}

		inProgress = true;

		// build a single query
		var myurl = [];
		for (var key in data) {
			//debug(key);

			lastelem = data[key].values.length - 1;

			if (lastelem < 1) {
				queryTime=minTime.toMysqlFormat();
			} else {
				queryTime=data[key].values[lastelem].timestamp.toMysqlFormat();
			};
			var temp = {
				table: meta.tables[key],
				column: meta.columns[key],
				key: key,
				time: queryTime
			};
			myurl.push(temp);
		}
		myurl = JSON.stringify(myurl);
		u="sensorSQLupdate.php?query=" + encodeURI( myurl );
		console.log(u);
		// process the result
		d3.json(u, function(answer) {
			for (row = 0; row < answer.length; row++) {
				var key=answer[row][0];
				var temp = {
					timestamp: parseDate(answer[row].timestamp),
					value: +answer[row].value
				};
				data[key].values.push(temp);
			}

			redrawAxes(false);
			redrawLines(false);

//			$(container).trigger('LineGraph:dataModification');

			//pop old data from our cache
			var elem=0;
			for (var key in data) {
				for (elem in data[key].values) {
					v1=new Date(data[key].values[elem].timestamp .getTime());
					if (v1>minTime) {
						break;
					}
				}
				if (elem > 0 ) {
					data[key].values.splice(0, elem);
				}
			}
			inProgress = false;
		});
	}

	/*
	 * Prepare the data for D3
	 */
	var processData = function(rawdata) {
		var loopValue=-1;
		var lastValue=0;
		$.each(rawdata, function(key, value) {
			var temp = {
				name: meta.names[key],
				table: meta.tables[key],
				column: meta.columns[key],
				yaxis: meta.yaxes[key],
				values: $.each(value.values, function(k, d){
					d.timestamp=parseDate(d.timestamp);
					d.value=+d.value;

					loopValue++;
					//if (loopValue>0) {
						console.log(loopValue, d.value, lastValue, d.value - lastValue)
					//}

					return { k, d };
				})
			};
			console.log(temp);
			data.push(temp);
		});

		// remove repeating data elements
		// TODO: FIX IT YOU IDIOT !!!!
		/*
		var elem=0;
		for (var key in data) {
			var tollerance=5;
			var lastvalue=0;
			//for (elem = 0; elem < data[key].values.length; elem++) {
			for (elem in data[key].values) {
				console.log(elem, data[key].values[elem].value, data[key].values[elem].value - lastvalue);
				if (elem==0) {
						lastvalue = data[key].values[elem].value;
				} else {
					if ( Math.abs(data[key].values[elem].value - lastvalue) < tollerance ) {
						lastvalue = data[key].values[elem].value;
						console.log("remove");
						data[key].values.splice(elem, 1);
					} else {
						lastvalue = data[key].values[elem].value;
					}
				}
			}
			for (elem in data[key].values) {
				console.log(elem, data[key].values[elem].value);
			}
		}
		*/

 		initDimensions();

		if ( myBehavior.autoUpdate == 1 ) {
			if ( myBehavior.secondsToShow != 0 ) {
				maxTime = new Date();
				minTime.setSeconds(maxTime.getSeconds() - myBehavior.secondsToShow);
			}
			else {
				console.log("Expected secondsToShow to be non zero")
			}
		}

 		createGraph();


	}

	/*
	 * Load all the data from SQL using defers before plotting
	 */
	var loadData = function(dataMap) {

		// Load data for graph behavior
		myBehavior.secondsToShow = +getOptionalVar(dataMap,	'graphSecondsToShow', "5");
		myBehavior.autoUpdate = +getOptionalVar(dataMap, 'graphAutoUpdate', "0");
		myBehavior.tickLine = +getOptionalVar(dataMap, 'graphTickLine', "");
		myBehavior.axisLeftMin = +getOptionalVar(dataMap, 'graphLeftMin', "");
		myBehavior.axisLeftMax = +getOptionalVar(dataMap, 'graphLeftMax', "");
		myBehavior.axisRightMin = +getOptionalVar(dataMap, 'graphRightMin', "");
		myBehavior.axisRightMax = +getOptionalVar(dataMap, 'graphRightMax', "");
		myBehavior.interval = +getOptionalVar(dataMap, 'graphUpdateInterval', "2");
		myBehavior.title = getOptionalVar(dataMap, 'graphTitle', "");
		myBehavior.axisLeftLegend = getOptionalVar(dataMap, 'grepLeftLegend', "");
		myBehavior.axisRightLegend = getOptionalVar(dataMap, 'grepRightLegend', "");
		//TODO: program the following

		myBehavior.hideLegend = getOptionalVar(dataMap, 'graphHideLegend', "");

		// Load graph meta data
		meta.names = getRequiredVar(dataMap, 'displayName',
										"Need to plot something");
		meta.tables = getRequiredVar(dataMap, 'sensorSource',
										"Need to get data from somewhere");
		meta.columns = getRequiredVar(dataMap, 'sensorColumn',
										"Need to have value to show");
		meta.yaxes = getOptionalVar(dataMap, 'yAxisLocation',
										"Left");
		// Load graph raw data
		var rawdata = [];
		var u;
		var defers = [], defer;
		// TODO rewrite to be a multi element query
		for ( index = 0; index < meta.names.length; ++index ) {
			u="sensorSQLinitial.php?source=" + meta.tables[index] +
				"&column=" + meta.columns[index];
//TODO			"&index=" + index;
			if ( myBehavior.secondsToShow != 0 ) {
				u = u + "&seconds=" + myBehavior.secondsToShow;
			}
			console.log(u);
			defer = $.ajax({
				type : "GET",
				dataType : "json",
				url: u,
				success: function(fromSQL) {
				 	rawdata.push({
						values: fromSQL
					});
				}
			});
			defers.push(defer);
		}
		$.when.apply(window, defers).done(function(){
			processData(rawdata);
		});
	}

	/*
	 * Creates the SVG elements
	 */
	var createGraph = function() {
		// Add an SVG element with the desired dimensions and margin.
		graph = d3.select("#" + containerId).append("svg:svg")
			.attr("class", "line-graph")
			.attr("width", w + margin[1] + margin[3])
			.attr("height", h + margin[0] + margin[2])
			.append("svg:g")
			.attr("transform", "translate(" + margin[3] + "," +
												margin[0] + ")");


		if (myBehavior.title != "" ) {
			title = graph.append("svg:g")
				.attr("class", "title-group")
					.append("text")
					.attr("class", "title")
	        		.attr("x", (w / 2))
	        		.attr("y", 0 - 5)
	        		.attr("text-anchor", "middle")
	        		.text(myBehavior.title);
	    }

		initX();

		// Add the x-axis.
		graph.append("svg:g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + h + ")")
			.call(xAxis);

		initY();

		// Add the y-axis to the left
		if (hasYaxisLeft) {
			leftYaxis = graph.append("svg:g")
				.attr("class", "y axis left")
				.attr("transform", "translate(-5,0)")
				.call(yAxisLeft);

			if (myBehavior.axisLeftLegend != "") {
				leftYaxislegend = leftYaxis.append("text")
					.attr("class", "y-left-legend")
			    	.attr("transform", "rotate(-90)")
			    	.attr("y", 4)
			    	.attr("x", -8)
			    	.attr("dy", ".71em")
			   		.style("text-anchor", "end")
			    	.text(myBehavior.axisLeftLegend);
			}
		}

		// Add the y-axis to the right
		if (hasYaxisRight) {
			rightYaxis = graph.append("svg:g")
				.attr("class", "y axis right")
				.attr("transform", "translate(" + (w+10) + ",0)")
				.call(yAxisRight)

			if (myBehavior.axisRightLegend != "") {
				rightYaxislegend = rightYaxis.append("text")
					.attr("class", "y-right-legend")
			    	.attr("transform", "rotate(-90)")
			    	.attr("y", -12)
			    	.attr("x", -8)
			    	.attr("dy", ".71em")
			   		.style("text-anchor", "end")
			    	.text(myBehavior.axisRightLegend);
			}
		}

		// Remember to use the bodge !!!
		lineFunctionSeriesIndex  = -1;

		// Create automated color domain
		color.domain(meta.names);

		// Create the line function() !!! Remember lineFunctionSeriesIndex bodge
		var prevPrevVal = 0;
      	var prevVal = 0;
      	var curVal = 0;
      	drawline = d3.svg.line()
            .interpolate("bundle")
			.x( function(d, i) { return x(d.timestamp); })
			.y( function(d, i) {
				if ( i == 0 ) {
					lineFunctionSeriesIndex++;
				}
				if ( meta.yaxes[lineFunctionSeriesIndex]  == "Right" ) {
					return yRight(d.value);
				} else {
					return yLeft(d.value);

		  // TODO (think of a more elligent way to do this) moving average
          /*
          if (i == 0) {
              prevPrevVal  = yLeft(d.value);
              prevVal = yLeft(d.value);
              curVal =  yLeft(d.value);
          } else if (i == 1) {
              prevPrevVal = prevVal;
              prevVal = curVal;
              curVal = (prevVal + yLeft(d.value)) / 2.0;
          } else {
              prevPrevVal = prevVal;
              prevVal = curVal;
              curVal = (prevPrevVal + prevVal + yLeft(d.value)) / 3.0;
          }
          return curVal;
          */

				}
			});


		// Draw the line
		lines = graph.append("svg:g")
			.attr("class", "lines")
			.selectAll("path")
			.data(data);

		// Create a hover line
		hoverContainer = container.querySelector('g .lines');

		$(container).mouseleave(function(event) {
			handleMouseOutGraph(event);
		});

		$(container).mousemove(function(event) {
			handleMouseOverGraph(event);
		});

		linesGroup = lines.enter().append("g")
			.attr("class", function(d, i) {
				return "line_group series_" + i;
			});

		linesGroup.append("path")
			.attr("class", function(d, i) {
				return "line series_" + i;
			})
			.attr("fill", "none")
			.attr("stroke", function(d, i) {
				return color(meta.names[i]);
			})
			.attr("d", function(d, i) {
				return drawline(d.values)
			})
			.on('mouseover', function(d,i) {
				handleMouseOverLine(d,i);
			});

		// add line label to line group
		linesGroupText = linesGroup.append("svg:text");
		linesGroupText.attr("class", function(d, i) {
			return "line_label series_" + i;
		})
		.text(function(d, i) {
			return "";
		});

		hoverLineGroup = graph.append("svg:g")
			.attr("class", "hover-line");

		hoverLine = hoverLineGroup
			.append("svg:line")
			.attr("x1", 10).attr("x2", 10)
			.attr("y1", 0).attr("y2", h);

		hoverLine.classed("hide", true);

		// Call functions to do additional data
		createDateLabel();
		createLegend();
		// only show menu if we are updating
		if ( myBehavior.autoUpdate == 1 ) {
			createMenuButtons();
		}
		setValueLabelsToLatest();

		// window resize listener
		var TO = false;
		$(window).resize(function(){
			if(TO !== false)
				clearTimeout(TO);
				TO = setTimeout(handleWindowResizeEvent, 200);
		});

		if ( myBehavior.autoUpdate == 1 ) {
			if ( myBehavior.secondsToShow != 0 ) {
				myInterval = setInterval(function () {
					self.refreshData();
				}, myBehavior.interval * 1000);
			}
		}

		console.log("We have finished: ", myBehavior);
	}


	/**
	* Called when the window is resized to redraw graph accordingly.
	*/
	var handleWindowResizeEvent = function() {
		//debug("Window Resize Event [" + containerId + "] => resizing graph")
		initDimensions();
		initX();
		initY();

		// reset width/height of SVG
		d3.select("#" + containerId + " svg")
			.attr("width", w + margin[1] + margin[3])
			.attr("height", h + margin[0] + margin[2]);

		// OOO reset transform of x axis
		graph.selectAll("g .x.axis")
			.attr("transform", "translate(0," + h + ")");

		if (hasYaxisRight) {
			graph.selectAll("g .y.axis.right")
				.attr("transform", "translate(" + (w+10) + ",0)");
		}

		legendFontSize = 12;
		graph.selectAll("text.legend.name")
			.attr("font-size", legendFontSize);
		graph.selectAll("text.legend.value")
			.attr("font-size", legendFontSize);

		graph.select('text.date-label')
			.transition()
			.duration(transitionDuration)
			.ease("linear")
			.attr("x", w);

		if (myBehavior.title != "" ) {
			graph.select('text.title')
				.transition()
				.duration(transitionDuration)
				.ease("linear")
	        	.attr("x", (w / 2));
	    }

		redrawAxes(true);
		redrawLines(true);
		redrawLegendPosition(true);
		setValueLabelsToLatest(true);
	}

	var redrawLines = function(withTransition) {
		lineFunctionSeriesIndex  =-1; // Remember this bodge !!!

		// redraw lines
		if(withTransition) {
			graph.selectAll("g .lines path")
				.transition()
					.duration(transitionDuration)
					.ease("linear")
					.attr("d", function(d, i) {
						return drawline(d.values)
					})
					.attr("transform", null);
		} else {
			graph.selectAll("g .lines path")
				.attr("d", function(d, i) {
					return drawline(d.values)
				})
				.attr("transform", null);
		}
	}

	/**
     * Create menu buttons
	 */
	var createMenuButtons = function() {
		var cumulativeWidth = 0;

		var buttonMenu = graph.append("svg:g")
				.attr("class", "menu-group")
			.selectAll("g")
				.data(menuButtons)
			.enter().append("g")
				.attr("class", "menu-buttons")
			.append("svg:text")
				.attr("class", "menu-button")
				.text(function(d, i) {
					return d[1];
				})
				.attr("font-size", "12")
				.attr("fill", function(d) {
					if (d[0] == updatePaused ) {
						return "black";
					} else {
						return "blue";
					}
				})
				.classed("selected", function(d) {
					if (d[0] == updatePaused ) {
						return true;
					} else {
						return false;
					}
				})
				.attr("x", function(d, i) {
					var returnX = cumulativeWidth;
					cumulativeWidth += this.getComputedTextLength()+5;
					return returnX;
				})
				.attr("y", -4)
				.on('click', function(d, i) {
					handleMouseClickMenuButton(this, d, i);
				});
	}

	var handleMouseClickMenuButton = function(button, buttonData, index) {
		var cumulativeWidth = 0;

		if(index == 0) {
			// start update
			updatePaused='update';
			myInterval = setInterval(function () {
				self.refreshData();
			}, myBehavior.interval * 1000);
		} else if(index == 1){
			updatePaused='pause';
			// pause update
			clearInterval( myInterval );
		}

		graph.selectAll('.menu-button')
			.text(function(d, i) {
				if (i == 0) {
					if (updatePaused == "update" ) {
						return "Updating";
					} else {
						return "Update";
					}
				} else {
					if (updatePaused == "update" ) {
						return "Pause";
					} else {
						return "Paused";
					}
				}
			})
			.attr("font-size", "12")
			.attr("fill", function(d) {
				if (d[0] == updatePaused ) {
					return "black";
				} else {
					return "blue";
				}
			})
			.classed("selected", function(d) {
				if (d[0] == updatePaused ) {
					return true;
				} else {
					return false;
				}
			})
			.attr("x", function(d, i) {
				var returnX = cumulativeWidth;
				cumulativeWidth += this.getComputedTextLength()+5;
				return returnX;
			})
	}

	/**
	 * Create a legend that displays the name of each line with appropriate colo
	 * and allows for showing the current value when doing a mouseOver
	 */
	var createLegend = function() {

		var legendLabelGroup = graph.append("svg:g")
			.attr("class", "legend-group")
			.selectAll("g")
			.data(meta.names)
			.enter().append("g")
			.attr("class", "legend-labels");

		legendLabelGroup.append("svg:text")
			.attr("class", "legend name")
			.text(function(d, i) {
				return d;
			})
			.attr("font-size", legendFontSize)
			.attr("fill", function(d, i) {
				return color(meta.names[i]);
			})
			.attr("y", function(d, i) {
				return h+28;
			})

		legendLabelGroup.append("svg:text")
			.attr("class", "legend value")
			.attr("font-size", legendFontSize)
			.attr("fill", function(d, i) {
				return color(meta.names[i]);
				})
			.attr("y", function(d, i) {
				return h+28;
			})
	}

	var redrawLegendPosition = function(animate) {
		var legendText = graph.selectAll('g.legend-group text');
			if(animate) {
				legendText.transition()
					.duration(transitionDuration)
					.ease("linear")
					.attr("y", function(d, i) {
						return h+28;
					});
			} else {
				legendText.attr("y", function(d, i) {
					return h+28;
				});
			}
	}

	/**
	 * Create a data label
	 */
	var createDateLabel = function() {
		var date = new Date();
		var buttonGroup = graph.append("svg:g")
			.attr("class", "date-label-group")
			.append("svg:text")
			.attr("class", "date-label")
			.attr("text-anchor", "end")
			.attr("font-size", "10")
			.attr("y", -4)
			.attr("x", w)
			.text(date.toDateString() + " " + date.toLocaleTimeString());
	}

	/**
	 * Called when a user mouses over a line.
	 */
	var handleMouseOverLine = function(lineData, index) {
//		debug("MouseOver line [" + containerId + "] => " + index);
		userCurrentlyInteracting = true;
	}

	/**
	* Called when a user mouses over the graph.
	*/
	var handleMouseOverGraph = function(event) {
		var mouseX = event.pageX-hoverLineXOffset;
		var mouseY = event.pageY-hoverLineYOffset;

/*
		debug("MouseOver graph [" + containerId + "] => x: " + mouseX +
			" y: " + mouseY + "  height: " + h + " event.clientY: " +
			event.clientY + " offsetY: " + event.offsetY + " pageY: " +
			event.pageY + " hoverLineYOffset: " + hoverLineYOffset);
*/

		if(mouseX >= 0 && mouseX <= w && mouseY >= 0 && mouseY <= h) {
			hoverLine.classed("hide", false);

			// set position of hoverLine
			hoverLine.attr("x1", mouseX).attr("x2", mouseX);
			displayValueLabelsForPositionX(mouseX);

			// user is interacting
			currentUserPositionX = mouseX;
		} else {
			handleMouseOutGraph(event)
		}
	}

	/**
	* Called when a user mouses moves out the graph.
	*/
	var handleMouseOutGraph = function(event) {

		hoverLine.classed("hide", true);
		setValueLabelsToLatest();

		userCurrentlyInteracting = false;
		currentUserPositionX = -1;
	}

	/**
	 * Set the value labels to whatever the latest data point is.
	 */
	var setValueLabelsToLatest = function(withTransition) {
		displayValueLabelsForPositionX(w, withTransition);
	}

	/**
	 * Convert back from an X position on the graph to a data value from
	 *	the given array (one of the lines)
	 * Return {value: value, date, date}
	 */

	var getValueForPositionXFromData = function(xPosition, index) {
		var xValue = x.invert(xPosition);
//		debug("Start get Value. Position: " + xPosition + " Index: " + index);
		var i = bisectDate(data[index].values, xValue, 1);
		if (i>1) {
			//console.log(data[index].values[i-1].timestamp,
			//		data[index].values[i-1].value);
			var v = Math.round(data[index].values[i-1].value * 10) / 10;
		} else {
			var v = 0;
		}
		return {value: v, date: xValue };
	}

	/**
	 * Display the data values at position X in the legend value labels.
	 */
	var displayValueLabelsForPositionX = function(xPosition, withTransition) {
		var animate = false;
		if(withTransition != undefined) {
			if(withTransition) {
				animate = true;
			}
		}

//		debug("Label: [" + containerId + "], " + xPosition);

		var dateToShow;
		var labelValueWidths = [];

		graph.selectAll("text.legend.value")
			.text(function(d, i) {
				var valuesForX = getValueForPositionXFromData(xPosition, i);
					dateToShow = valuesForX.date;
					return valuesForX.value;
			})
			.attr("x", function(d, i) {
				labelValueWidths[i] = this.getComputedTextLength();
			})

		// position label names
		var cumulativeWidth = 0;
		var labelNameEnd = [];

		graph.selectAll("text.legend.name")
			.attr("x", function(d, i) {
				var returnX = cumulativeWidth;
					cumulativeWidth += this.getComputedTextLength()
						+4+labelValueWidths[i]+8;
					labelNameEnd[i] = returnX + this.getComputedTextLength()+5;
				return returnX;
			})

		cumulativeWidth = cumulativeWidth - 8;

		if(cumulativeWidth > w) {
			legendFontSize = legendFontSize-1;

			graph.selectAll("text.legend.name")
				.attr("font-size", legendFontSize);

			graph.selectAll("text.legend.value")
				.attr("font-size", legendFontSize);

			displayValueLabelsForPositionX(xPosition);
			return;
		}

		graph.selectAll("text.legend.value")
			.attr("x", function(d, i) {
				return labelNameEnd[i];
			})

		graph.select('text.date-label')
			.text(dateToShow.toDateString() + " "
				+ dateToShow.toLocaleTimeString())

		if(animate) {
			graph.selectAll("g.legend-group g")
				.transition()
				.duration(transitionDuration)
				.ease("linear")
				.attr("transform", "translate(" + (w-cumulativeWidth) +",0)")
		} else {
			graph.selectAll("g.legend-group g")
				.attr("transform", "translate(" + (w-cumulativeWidth) +",0)")
		}
	}

	/*
	 * Allow re-initialzing the y function at any time
	 */
	var initY = function() {

		hasYaxisLeft=false;
		hasYaxisRight=false;
		for ( index = 0; index < meta.yaxes.length; ++index ) {
			if ( meta.yaxes[index] == 'Left' ) {
				hasYaxisLeft = true;
			}
			if ( meta.yaxes[index] == 'Right' ) {
				hasYaxisRight = true;
			}
		}

		if (hasYaxisLeft) {
			yLeft = d3.scale
				.linear()
				.domain([
					d3.min(data.filter( function (f) {
						return f.yaxis == 'Left';
					}), function(m) {
						lValue=d3.min(m.values, function(v) {
								return v.value;
						});
						if ( lValue < myBehavior.axisLeftMin || myBehavior.axisLeftMin == 0)
							return lValue;
						else
							return myBehavior.axisLeftMin;
						//return d3.min( lValue, myBehavior.axisLeftMin );
					}),
					d3.max(data.filter( function (f) {
						return f.yaxis == 'Left';
					}), function(m) {
						lValue=d3.max(m.values, function(v) {
							return v.value;
						});
						if ( lValue > myBehavior.axisLeftMax || myBehavior.axisLeftMax == 0 )
							return lValue;
						else
							return myBehavior.axisLeftMax;
					})
				])
				.range([h, 0])
				.nice();

			yAxisLeft = d3.svg.axis().scale(yLeft).orient("left");
		}

		if (hasYaxisRight) {
			yRight = d3.scale
				.linear()
				.domain([
					d3.min(data.filter( function (f) {
						return f.yaxis == 'Right';
					}), function(m) {
						lValue=d3.min(m.values, function(v) {
								return v.value;
						});
						if ( lValue < myBehavior.axisRightMin || myBehavior.axisRightMin == 0)
							return lValue;
						else
							return myBehavior.axisRightMin;
					}),
					d3.max(data.filter( function (f) {
						return f.yaxis == 'Right';
					}), function(m) {
						lValue=d3.max(m.values, function(v) {
							return v.value;
						});
						if ( lValue > myBehavior.axisRightMax || myBehavior.axisRightMax == 0)
							return lValue;
						else
							return myBehavior.axisRightMax;
					})
				])
				.range([h, 0])
				.nice();

			yAxisRight = d3.svg.axis().scale(yRight).orient("right");
		}
	}

	/*
	 * Allow re-initialzing the x function at any time
	 */
	var initX = function() {

		// TODO : CHECK THIS
		// if ( myBehavior.secondsToShow != 0 ) {
		if ( myBehavior.autoUpdate != 0 ) {
			//debug("Start:" + minTime + " End:" + maxTime);
			x = d3.time.scale()
				.domain([minTime,maxTime])
				.range([0, w]);
		} else {
			x = d3.time.scale()
				.domain([
					d3.min(data, function(m) {
						return d3.min(m.values, function(v) {
							return v.timestamp;
						});
					}),
					d3.max(data, function(m) {
						return d3.max(m.values, function(v) {
							return v.timestamp;
						});
					})
				])
				.range([0, w]);
		}

		if ( myBehavior.tickLine != 0 ) {
			xAxis = d3.svg.axis()
				.scale(x)
				.tickSize(-h)
				.tickSubdivide(myBehavior.tickLine);
		} else {
			xAxis = d3.svg.axis()
				.scale(x);
		}
	}

	var redrawAxes = function(withTransition) {
		initY();
		initX();

		if(withTransition) {
		// slide x-axis to updated location
			graph.selectAll("g .x.axis").transition()
				.duration(transitionDuration)
				.ease("linear")
				.call(xAxis)

			if (hasYaxisLeft) {
				graph.selectAll("g .y.axis.left").transition()
					.duration(transitionDuration)
					.ease("linear")
					.call(yAxisLeft)
			}
			if (hasYaxisRight) {
				graph.selectAll("g .y.axis.right").transition()
					.duration(transitionDuration)
					.ease("linear")
					.call(yAxisRight)
			}
		} else {
			graph.selectAll("g .x.axis")
				.call(xAxis)

			if (hasYaxisLeft) {
				graph.selectAll("g .y.axis.left")
					.call(yAxisLeft)
			}

			if (hasYaxisRight) {
				graph.selectAll("g .y.axis.right")
					.call(yAxisRight)
			}
		}
	}

	/*
	 * Set height/width dimensions based on container
	 */
	var initDimensions = function() {
		// automatically size to the container using JQuery to get width/height
		w = $("#" + containerId).width() - margin[1] - margin[3]; // width
		h = $("#" + containerId).height() - margin[0] - margin[2]; // height

		// make sure to use offset() and not position() as we want it relative
		//	to the document, not its parent
		hoverLineXOffset = margin[3]+$(container).offset().left;
		hoverLineYOffset = margin[0]+$(container).offset().top;
	}

	/*
	 * Return the value from argsMap for key or throw error if no value found
	 */
 	var getRequiredVar = function(argsMap, key, message) {
		if(!argsMap[key]) {
			if(!message) {
				throw new Error(key + " is required")
			} else {
				throw new Error(message)
			}
		} else {
			return argsMap[key]
		}
	}

	/*
	 * Return the value from argsMap for key or defaultValue if no value found
	 */
	var getOptionalVar = function(argsMap, key, defaultValue) {
		if(!argsMap[key]) {
			return defaultValue
		} else {
			return argsMap[key]
		}
	}

	/*
	 * programmers stuff
	 */
	var error = function(message) {
		console.log("ERROR: ", message)
	}

	var debug = function(message) {
		console.log("DEBUG: ",  message)
	}

	/*
	 * function to create SQL date format
	 */
	function twoDigits(d) {
		if(0 <= d && d < 10) return "0" + d.toString();
		if(-10 < d && d < 0) return "-0" + (-1*d).toString();
		return d.toString();
	}

	Date.prototype.toMysqlFormat = function() {
    	return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate()) + " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
	};


/* *************************************************************** */
/* execute init now that everything is defined */
/* *************************************************************** */
	_init();
}


<!DOCTYPE html>
	<head>
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
		<style>
			html,body {
				margin:0px;
				padding:0px;
				width:100%;
				height:100%;
			}
			
			#line_graph,#bar_chart {
				width:100%;
			}
			
			#line_graph, #bar_chart {
				background-color:lightGray;
				height:500px;
				width:820px;
				float:left;
			}
		
			#graph_area {
				height:100%;
				float:left;
			}

			.axis path,
			.axis line {
			  fill: none;
			  stroke: #000;
			  shape-rendering: crispEdges;
			}

			.x.axis path {
			  
			}

			
			#chart_area {
				width:70%;
				height:100%;
				background-color:beige;
			}
			
			#controls_area {
				background-color:lightBlue;
				width:20%;
				height:100%;
				float:left;
			}
		</style>
	</head>
	<body>
		<div id="viz_area">
			<div id="line_graph">

			</div>
			<div id="controls_area">
				Category
				<select id="categories"></select><br /><br />
				Last
				<div id="days_slider"></div>
				<div id="days_label"></div>
			</div>
			<div id="bar_area">
			
			</div>
		</div>
		
		<script>
			// create the vizLib prototype
			var VizLib = function() {
				// stores the news articles categories
				var Categories = new Array();
				// stores all the needed metadata for the articles of a given category, given a time range
				var ArticlesHits = new Array();
				// stores the number of hits that came from different states for a given article
				var ArticleStates = new Array();
				// the end time for our filtered time range. by default it is set to now's time, as a Unix timestamp
				var endTime = Math.round(new Date().getTime() / 1000);
				// the default beginning time for our filtered time range. by default it is set to yesterday's time, as a Unix timestamp
				var startTime = endTime - (24 * 60 * 60);
				// the minimum start time that a user can go back to
				var MIN_START_TIME = endTime - (31 * 24 * 60 * 60);
				// stores the Unix timestamps of all the days within the filtered date range
				var DayStamps = new Array();
				// refined articles hits data
				var RefinedArticlesHits = new Array();
				// article ids that are active in the visualization
				var ArticlesIDsCounts = new Array();
				
				// private function to populate the categories dropdown box
				var populateCategoriesDropdown = function() {
					// populate the categories dropdown box based on the Categories array
					$("#categories").append("<option id=9999>[SELECT CATEGORY]</option>");
					$.each(Categories,function(index,el){
						$("#categories").append("<option id=" + el.id + ">" + el.name + "</option>");
					});
				};
				
				// private function to bind event handlers to the different categories in the dropdown
				var bindCategoriesEventHandlers = function() {
					$("#categories").change(function(){
						// when changing categories, get all the pertinent articles data for the selected category
						$("#categories option:selected").each(function(index,el){
							getHitsData(el.id);
						});
					});
				};
				
				// given a category id, gets all the pertinent articles and their needed metadata in json
				// by default, this function asks for 31 days worth of data, so as to not stress the server with big requests
				var getHitsData = function(category_id) {
					$.ajax({
						url:"getArticlesMetrics.php",
						data:{category_id:category_id,start_time:MIN_START_TIME,end_time:endTime}
					}).done(function(data){
						ArticlesHits = JSON.parse(data);
						dayifyArticlesTimes();
						refineArticlesData();
					});
				}
				
				// populate date stamps for the days that are included in the filtered range
				var populateDayStamps = function(endTime,days) {
					DayStamps = new Array();
					for (i=days;i>=1;i--) {
						DayStamps.push(endTime - (24 * 60 * 60));
						endTime-=(24 * 60 * 60);
					}
				};
				
				// ready the articles metadata to be plotted on the line graph
				var refineArticlesData = function() {
					RefinedArticlesHits = new Array();
					// populate an array for each day stamp with articles hits metadata for that day
					for (daystamp in DayStamps) {
						RefinedArticlesHits[DayStamps[daystamp]] = new Array();
						for(hit in ArticlesHits) {
							if(ArticlesHits[hit].time_visited == DayStamps[daystamp]) {
								RefinedArticlesHits[DayStamps[daystamp]].push(ArticlesHits[hit]);
							}
						}
					}
					
					for (daystamp in RefinedArticlesHits) {
						articlesCounts = {};
						for(hit in RefinedArticlesHits[daystamp]) {
							if(articlesCounts.hasOwnProperty(RefinedArticlesHits[daystamp][hit].article_id)) {
								articlesCounts[RefinedArticlesHits[daystamp][hit].article_id] += 1;
							} else {
								articlesCounts[RefinedArticlesHits[daystamp][hit].article_id] = 1;
							}
						}
									
						for(hit in RefinedArticlesHits[daystamp]) {
							RefinedArticlesHits[daystamp][hit].count = articlesCounts[RefinedArticlesHits[daystamp][hit].article_id];
						}
						
						for(article_id in articlesCounts) {
							var instances = 0;
							var read_time = 0;
							for(hit in RefinedArticlesHits[daystamp]) {
								if(article_id == RefinedArticlesHits[daystamp][hit].article_id) {
									instances++;
									read_time+=parseInt(RefinedArticlesHits[daystamp][hit].read_time);
									if(instances > 1) {
										delete RefinedArticlesHits[daystamp][hit];
									}
								}
							}

							for(hit in RefinedArticlesHits[daystamp]) {
								if(article_id == RefinedArticlesHits[daystamp][hit].article_id) {
									RefinedArticlesHits[daystamp][hit].read_time = (read_time / RefinedArticlesHits[daystamp][hit].count);
								}
							}
						}
					}
				}
				
				var dayifyArticlesTimes = function() {
					for(hit in ArticlesHits) {
						ArticlesHits[hit].time_visited = parseInt(ArticlesHits[hit].time_visited / (3600 * 24));
						ArticlesHits[hit].time_visited = ArticlesHits[hit].time_visited * (24 * 60 * 60 * 1000);
					}
				};
				
				var dayifyDayStamps = function() {
					for(daystamp in DayStamps) {
						DayStamps[daystamp] = parseInt(DayStamps[daystamp] / (3600 * 24));
						DayStamps[daystamp] = DayStamps[daystamp] * (24 * 60 * 60 * 1000);
					}
				}
				
				// given an article id, gets the count of hits that came for the subject article spread across different US states
				this.getArticleStates = function(article_id) {
					$.ajax({
						url:"getArticleStatesMetrics.php",
						data:{article_id:article_id}
					}).done(function(data){
						ArticleStates = JSON.parse(data);
					});
				}
				
				// sets the start and end times that define the date range 
				this.setTimeRange = function(days) {
					startTime = endTime - (days * 24 * 60 * 60);
					populateDayStamps(endTime,days);
					dayifyDayStamps();
					refineArticlesData();
					makeLineGraph(RefinedArticlesHits);
				};
				
				this.getRefinedArticlesHits = function() {
					return RefinedArticlesHits;
				}
				
				this.getDayStamps = function() {
					return DayStamps;
				}
				
				// gets the time start and end times that are defined in the date range
				this.getTimeRange = function() {
					return {startTime:startTime,endTime:endTime};
				};

				// creates the line graph
				var makeLineGraph = function(lineData) {
					var margin = {top: 20, right: 80, bottom: 30, left: 50},
						width = 840 - margin.left - margin.right,
						height = 500 - margin.top - margin.bottom;
					
					var parseDate = d3.time.format("%Y%m%d").parse;
					
					var x = d3.time.scale()
						.range([0,width]);
							
					var y = d3.scale.linear()
						.range([height,0]);
							
					var color = d3.scale.category20c();
					
					var xAxis = d3.svg.axis()
						.scale(x)
						.orient("bottom");
					
					var yAxis = d3.svg.axis()
						.scale(y)
						.orient("left");
							
					var line = d3.svg.line()
						.interpolate("basis")
						.x(function(d){ return x(d.time_visited);})
						.y(function(d){ return y(d.count);})

					$("#line_graph").html("");
					
					var svg = d3.select("#line_graph").append("svg")
						.attr("id","svg_line_graph")
						.attr("width", width + margin.left + margin.right)
						.attr("height", height + margin.top + margin.bottom)
						.append("g")
						.attr("transform","translate(" + margin.left + "," + margin.top + ")");

					// format date values into month-day-year
					for(line in lineData) {
						lineData[line].forEach(function(d,i){
							//date_object = new Date(d.time_visited);
							//d.formatted_time = (date_object.getMonth()+1)+"-"+date_object.getDate()+"-"+date_object.getFullYear();
						});
					}
					
					x.domain(d3.extent(DayStamps,function(d){
						return new Date(d);
					}));
					
					z = d3.extent(DayStamps, function(d) { 
					date_object = new Date(d);
					return date_object; 
					
					});
					console.log(z);
				
	
					max_hits = min_hits = 0;
					
					for(line in lineData) {
						lineData[line].forEach(function(d,i){
							max_hits = max_hits >= d.count ? max_hits : d.count; 
						});
					}
					
					y.domain([min_hits,max_hits]);
					
					svg.append("g")
					  .attr("class", "x axis")
					  .attr("transform", "translate(0," + height + ")")
					  .call(xAxis);

					svg.append("g")
					  .attr("class", "y axis")
					  .call(yAxis)
					.append("text")
					  .attr("transform", "rotate(-90)")
					  .attr("y", 6)
					  .attr("dy", ".71em")
					  .style("text-anchor", "end")
					  .text("Hits");

					/*
					var article = svg.selectAll(".article")
					  .data(articles)
					  .enter().append("g")
					  .attr("class", "article");					  
					*/
		
					console.log(lineData);
					color.domain(d3.keys(lineData));
				
					
					

				}
				
				// gets the article categories, loads them in their corresponding dropdown box, and binds the category options to certain event handlers
				this.setArticlesCategories = function() {
					$.ajax({url:"getCategories.php"})
					 .done(function(data){
						Categories = JSON.parse(data);
						populateCategoriesDropdown();
						bindCategoriesEventHandlers();
					 });
				};

			}

			// create a new instance of the visualization library
			viz = new VizLib();
			
			// load the articles categories into an array
			viz.setArticlesCategories();
			
			$(function() {
				// populates the days slider, with a min value of 1 day and a max value of 31 days. 
				$( "#days_slider" ).slider({
					min: 1,
					max: 31,
					create: function() {
						// sets the label to a default of "1 day"
						$("#days_label").html("1 day");
						// set the time range to one day back by default
						viz.setTimeRange(1);

					},
					slide: function(e,t) {
						// clause to be correct grammatically with the usage of day (singular) vs. days (plural)
						t.value>1?$("#days_label").html(t.value + " days"):$("#days_label").html(t.value + " day");
						// set the time range according to the values set in the slider
						viz.setTimeRange(t.value);
					}
				});
			});			
			
			
		</script>
	</body>
</html>
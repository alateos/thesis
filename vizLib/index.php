<!DOCTYPE html>
	<head>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
		<style>
			html,body {
				margin:0px;
				padding:0px;
				width:100%;
				height:100%;
				-moz-user-select: none;
				-webkit-user-select: none;
				-ms-user-select: none;
			}
			
			#line_graph,#categories_bar {
				width:100%;
			}
			
			#categories_viz_area {
				display:none;
			}
			
			#line_graph {
				background-color:white;
				height:600px;
				width:840px;
				float:left;
			}
			
			#bar_area {
				width:20%;
				background-color:white;
				float:left;
				margin-left:100px;
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

			.line {
			  stroke-width: 1px;
			}
			
			#chart_area {
				width:70%;
				height:100%;
				background-color:beige;
			}
			
			#controls_area {
				background-color:lightGray;
				width:20%;
				height:240px;
				left:40px;
				top:70%;
				position:absolute;
				padding:10px;
				cursor:move;
				opacity:0.8;
				box-shadow: 5px 5px 2px gray;
			}
			
			#articles_conditional_controls,#categories_conditional_controls {
				display:none;
			}
			
			#article_details {
				position:absolute;
				width:300px;
				height:450px;
				background-color:lightBlue;
				-webkit-box-shadow: 10px 10px 5px -6px rgba(0,0,0,0.35);
				-moz-box-shadow: 10px 10px 5px -6px rgba(0,0,0,0.35);
				box-shadow: 10px 10px 5px -6px rgba(0,0,0,0.35);
				overflow:hidden;
				display:none;
			}
			
			#article_image {
				width:100%;
				height:200px;
			}
			
			#article_title {
				font-size:22px;
				font-family:calibri;
				margin:7px;
			}
			
			#article_excerpt {
				font-size:12px;
				font-family:calibri;
				padding:10px;
			}
			
			#status {

			}
			
			#articles_viz_area,#categories_viz_area {
				float:left;
			}
			
			a,a:visited {
				text-decoration:none;
				color:steelBlue;
			}
			
			a:hover {
				color:blue;
			}
			
			th {
				color:steelBlue;
			}

			th:hover {
				font-weight:bold;
				color:blue;
			}
			
			#articles_controls {
				position:absolute;
				left:0px;
				padding:5px;
				background-color:lightGray;
				top:-28px;
				box-shadow: 5px 0px 2px gray;
				cursor:pointer;
			}
			
			.categories_controls {
				display:none;
			}
			
			#categories_controls {
				position:absolute;
				left:94px;
				padding:5px;
				background-color:#C2D4E0;
				top:-28px;
				box-shadow: 5px 0px 2px gray;
				cursor:pointer;
			}
			
			#articles_days_label {
				margin-top:10px;
			}
			
			#bar_popup {
				width:100px;
				height:20px;
				background-color:steelBlue;
				color:white;
				opacity:0.9;
				padding:5px;
				position:absolute;
				z-index:100;
				display:none;
				text-align:center;
			}
			
			#article_popup {
				width:500px;
				height:40px;
				border:1px solid black;
				font-size:18px;
				opacity:0.9;
				padding:5px;
				position:absolute;
				z-index:100;
				text-align:center;
				background-color:steelBlue;
				color:white;
				display:none;
			}
		</style>
	</head>
	<body>
		<script src="http://d3js.org/d3.v3.js"></script>
		<div id="article_details">
			<div id="article_image_area">
				<img id="article_image" src="" alt="IMAGE NOT AVAILABLE" />
			</div>
			<div id="article_title"></div>
			<div id="article_excerpt"></div>
		</div>
		<div id="controls_area">
			<div id="articles_controls">
				ARTICLES
			</div>
			<div id="categories_controls">
				CATEGORIES
			</div>
			<br />
			<div class="articles_controls">
				<div id="category_pick">
					Category
					<select id="categories"></select><br /><br />
				</div>
				Status: <span id="status"></span><br /><br />
				<div id="details_on_hover">
					Show Article Details on Hover <input type="checkbox" id="article_details_checkbox" /><br /><br />
				</div>
				<div id="articles_conditional_controls">
					Type
					<select class="types">
						<option value="hits" selected>Hits</option>
						<option value="read_time">Read Time</option>
					</select><br /><br />
					<div id="articles_days_slider"></div>
					<div id="articles_days_label"></div>
				</div>
			</div>
			<div class="categories_controls">
				Status: <span id="status"></span><br /><br />
				<div id="categories_conditional_controls">
					Type
					<select class="types">
						<option value="hits" selected>Hits</option>
						<option value="read_time">Read Time</option>
					</select><br /><br />
					<div id="categories_days_slider"></div>
					<div id="categories_days_label"></div>
				</div>
			</div>
		</div>
		<div id="articles_viz_area">
			<div id="line_graph">

			</div>
			<div id="bar_area">

			</div>
		</div>
		
		<div id="categories_viz_area">
			<div id="categories_bars">

			</div>
		</div>
		
		<div id="bar_popup"></div>
		<div id="article_popup"></div>
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
				var  endTime = Math.round(new Date().getTime() / 1000);
				// the default beginning time for our filtered time range. by default it is set to yesterday's time, as a Unix timestamp
				var startTime = endTime - (24 * 60 * 60);
				// the maximum start time that a user can go back to - equal to now
				var MAX_END_TIME = Math.round(new Date().getTime() / 1000);
				// the minimum start time that a user can go back to
				var MIN_START_TIME = endTime - (31 * 24 * 60 * 60);
				// stores the Unix timestamps of all the days within the filtered date range
				var DayStamps = new Array();
				// refined articles hits data
				var RefinedArticlesHits = new Array();
				// copy of refined articles hits data
				var RefinedArticlesHitsCopy = new Array();
				// article ids that are active in the visualization
				var ArticlesIDsCounts = new Array();
				// categories hits and read_time data
				var CategoriesMetrics = new Array();
				// number of chosen days to go back to
				var num_days_back = 1;
				// contains the number of days difference specified in the articles slider
				var days_spread = 0;
				// contains the title of the selected article
				var selected_article_title = "";
				// contains the url of the selected article
				var selected_article_url = "";
				// articles measure type - by default set to 'hits'
				var articles_measure_type = "hits";
				// categories measure type - by default set to 'hits'
				var categories_measure_type = "hits";
				// flag to denote whether to show the article details on hover
				var article_details = 0;
				// flag to denote whether the categories data was loaded
				var categories_data_loaded = 0;
				// pointer to current object
				viz_object = this;
				
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
							$("#9999").remove();
							$(".articles_controls #status").css({"color":"red","font-weight":"bold"}).html("Loading...");
						});
					});
				};
				
				// private function to bind event handlers to the different types of stats in the dropdown
				var bindTypesEventHandlers = function() {
					$("#articles_conditional_controls .types").change(function(){
						$("#articles_conditional_controls .types option:selected").each(function(index,el){
							makeLineGraph(RefinedArticlesHits,el.value);
							articles_measure_type = el.value;
						});					
					});
					
					$("#categories_conditional_controls .types").change(function(){
						$("#categories_conditional_controls .types option:selected").each(function(index,el){
							makeCategoriesBarChart(CategoriesMetrics,el.value);
							categories_measure_type = el.value;
						});					
					});
				};
								
				// given a category id, gets all the pertinent articles and their needed metadata in json
				// by default, this function asks for 31 days worth of data, so as to not stress the server with big requests
				var getHitsData = function(category_id) {
					$.ajax({
						url:"getArticlesMetrics.php",
						data:{category_id:category_id,start_time:MIN_START_TIME,end_time:MAX_END_TIME}
					}).done(function(data){
						ArticlesHits = JSON.parse(data);
						//refineArticlesData(1);
						//generateHitsMetrics();
						slider_start = $( "#articles_days_slider" ).slider("values")[0];
						slider_end = $( "#articles_days_slider" ).slider("values")[1];
						viz_object.setArticlesTimeRange(slider_start,slider_end);
						$(".articles_controls #status").css({"color":"green","font-weight":"bold"}).html("Loaded");
						$("#articles_conditional_controls").show();
					});
				}
				
				this.loadCategories = function() {
					if(categories_data_loaded == 0) viz_object.getCategoriesData();
				}
				
				// given a beginning and end time in Unix timestamp fetch the metrics for all categories
				this.getCategoriesData = function() {
					$(".categories_controls #status").css({"color":"red","font-weight":"bold"}).html("Loading...");
					$.ajax({
						url:"getCategoriesMetrics.php",
						data:{start_time:startTime,end_time:endTime}
					}).done(function(data){
						// categories data has been loaded
						categories_data_loaded = 1;
						CategoriesMetrics = JSON.parse(data);
						console.log(CategoriesMetrics);
						$("#categories_conditional_controls").show();
						$(".categories_controls #status").css({"color":"green","font-weight":"bold"}).html("Loaded");
						makeCategoriesBarChart(CategoriesMetrics,categories_measure_type);
					});
				}
				
				// populate date stamps for the days that are included in the filtered range
				var populateDayStamps = function(end_point,days_spread) {
					DayStamps = new Array();
					if(days_spread == 0) days_spread=1;
					for (i= days_spread>=0?days_spread:(days_spread*24);i>=0;i--) {
						DayStamps.push(end_point);
						end_point-= days_spread>=0 ? (24 * 60 * 60) : (60 * 60);
					}
				};
				
				// ready the articles metadata to be plotted on the line graph
				var refineArticlesData = function(days_spread) {
					RefinedArticlesHits = new Array();
					// populate an array for each day stamp with articles hits metadata for that day
					for (daystamp in DayStamps) {
						RefinedArticlesHits[DayStamps[daystamp]] = new Array();
						RefinedArticlesHitsCopy[DayStamps[daystamp]] = new Array();
						for(hit in ArticlesHits) {
							article_date = new Date(ArticlesHits[hit].time_visited * 1000);
							article_day = article_date.getDate();
							article_month = article_date.getMonth();
							article_hour = article_date.getHours();
							daystamp_date = new Date(DayStamps[daystamp]);
							daystamp_day = daystamp_date.getDate();
							daystamp_hour = daystamp_date.getHours();
							daystamp_month = daystamp_date.getMonth();
							if(article_day == daystamp_day && article_month == daystamp_month) {
								if(days_spread>=0) {
									ArticlesHits[hit].time_visited = DayStamps[daystamp] /1000;
									RefinedArticlesHits[DayStamps[daystamp]].push(ArticlesHits[hit]);
									RefinedArticlesHitsCopy[DayStamps[daystamp]].push(ArticlesHits[hit]);
								} else {
									if(article_hour == daystamp_hour) {
										ArticlesHits[hit].time_visited = DayStamps[daystamp] /1000;
										RefinedArticlesHits[DayStamps[daystamp]].push(ArticlesHits[hit]);
										RefinedArticlesHitsCopy[DayStamps[daystamp]].push(ArticlesHits[hit]);
									}
								}
							}
						}
					}
				}
				
				var generateHitsMetrics = function() {
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
									if(RefinedArticlesHits[daystamp][hit].read_time > 0) {
										instances++;
										read_time+=parseInt(RefinedArticlesHits[daystamp][hit].read_time);
										if(instances > 1) {
											delete RefinedArticlesHits[daystamp][hit];
										}
									} else {
										RefinedArticlesHits[daystamp][hit].count -= 1;
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
				};
				
				var hourfyDayStamps = function() {
					for(daystamp in DayStamps) {
						DayStamps[daystamp] = parseInt(DayStamps[daystamp] / (60 * 60));
						DayStamps[daystamp] = DayStamps[daystamp] * (60 * 60 * 1000);
					}
				}
				
				/** given an article id, gets the count of hits that came for the subject article spread across different US states
				var getArticleStates = function(article_id) {
					$.ajax({
						url:"getArticleStatesMetrics.php",
						data:{article_id:article_id}
					}).done(function(data){
						console.log(data);
						makeStatesBars(JSON.parse(data));
					});
				}
				*/
				
				// given an article id, gets the count of hits that came for the subject article spread across different US states
				var getArticleStates = function(article_id) {
					statesHits = {};
					for (daystamp in RefinedArticlesHitsCopy) {
						for(hit in RefinedArticlesHitsCopy[daystamp]) {
							if(article_id == RefinedArticlesHitsCopy[daystamp][hit].article_id && RefinedArticlesHitsCopy[daystamp][hit].read_time > 0) {
								region = RefinedArticlesHitsCopy[daystamp][hit].region;
								if(statesHits.hasOwnProperty(region)) {
									hits = statesHits[region].hits + 1;
									read_time = parseInt(RefinedArticlesHitsCopy[daystamp][hit].read_time) + statesHits[region].read_time;
									expected_read_time = parseInt(RefinedArticlesHitsCopy[daystamp][hit].expected_read_time);
									statesHits[region] = {hits:hits,read_time:read_time,expected_read_time:parseInt(RefinedArticlesHitsCopy[daystamp][hit].expected_read_time),name:region};
								} else {
									statesHits[region] = {hits:1,read_time:parseInt(RefinedArticlesHitsCopy[daystamp][hit].read_time),expected_read_time:parseInt(RefinedArticlesHitsCopy[daystamp][hit].expected_read_time),name:region};
								}
							}
						}
					}
					return statesHits;
				};
				
				// sets the start and end times that define the date range for articles 
				this.setArticlesTimeRange = function(days,days1) {
					days_spread=(days1-days);
					if(days_spread == 0) {
						endTime = MAX_END_TIME;
						startTime = endTime - (24 * 60 * 60);
					} else {
						endTime = MAX_END_TIME - ((days-1) * (24 * 60 * 60));
						startTime = endTime - (days_spread * (24 * 60 * 60));					
					}

					populateDayStamps(endTime,days_spread);
					hourfyDayStamps();
					refineArticlesData(days_spread);
					generateHitsMetrics();
					makeLineGraph(RefinedArticlesHits,articles_measure_type);
					num_days_back = days;
				};
				
				// sets the start and end times that define the date range for categories
				this.setCategoriesTimeRange = function(days) {
					startTime = endTime - (days * 24 * 60 * 60);
					num_days_back = days;
					viz_object.getCategoriesData();
				}
				
				this.getRefinedArticlesHits = function() {
					return RefinedArticlesHits;
				}
				
				this.getDayStamps = function() {
					return DayStamps;
				}
				
				// gets the time start and end times that are defined in the date range
				this.getTimeRange = function() {
					return {startTime:MIN_START_TIME,endTime:MAX_END_TIME};
				};

				// creates the bar graph based on states metrics
				var makeStatesBars = function(barData,passed_article_title,passed_article_url) {
					$("#bar_area").html("");
					ArticleStates = new Array();
					for(datum in barData) {
						ArticleStates.push(barData[datum]);
					}
					
					console.log(ArticleStates);
					
					max_state_hits = d3.max(ArticleStates,function(d){return d.hits});
					
					article_title = d3.select("#bar_area").append("h3");
					article_title.append("a").attr("href",passed_article_url).attr("target","_blank").html(passed_article_title);
					
					states_table = d3.select("#bar_area").append("table").style("border-collapse","collapse");
					
					table_head = states_table.append("thead");
					
					header_row = states_table.append("tr");
				
					table_head.append("th").text("STATE").style("text-align","left").attr("data-sorted",0).style("cursor","n-resize");
					table_head.append("th").text("TOTAL HITS").attr("data-sorted",0).style("cursor","n-resize");
					table_head.append("th").text("ATTENTION SPAN").attr("data-sorted",0).style("cursor","n-resize");
					
					table_body = states_table.append("tbody");
					
					states_row = table_body.selectAll("tr")
								.data(ArticleStates)
								.enter().append("tr");
								
					states_row.append("td")
							  .text(function(d){return d.name});
					
					states_row.append("td").append("svg")
							  .style("height",10)
							  .style("width",function(d){return (d.hits/max_state_hits) * 100})
							  .append("rect")
							  .attr("width",function(d){return (d.hits/max_state_hits) * 100})
							  .attr("height",10)
							  .style("fill","gray")
							  .on("mouseenter",function(d) {
								$("#bar_popup").show();
								$("#bar_popup").html(d.hits + " hits");
								$("#bar_popup").css({"top":(d3.event.pageY-30)+"px","left":(d3.event.pageX-100)+"px"});
							  })
							  .on("mouseleave",function(d) {
								$("#bar_popup").hide();
							  });
							  
					
					states_row.append("td").append("svg")
							  .style("height",10)
							  .style("width",function(d){
								average_read_time = (d.read_time/d.hits) <= d.expected_read_time ? (d.read_time/d.hits): d.expected_read_time;
								return (average_read_time/d.expected_read_time) * 100;
							  })
							  .append("rect")
							  .attr("width",function(d){
								average_read_time = (d.read_time/d.hits) <= d.expected_read_time ? (d.read_time/d.hits): d.expected_read_time;
								return (average_read_time/d.expected_read_time) * 100;
							  })
							  .attr("height",10)
							  .style("fill","darkGray")
							  .on("mouseenter",function(d) {
								$("#bar_popup").show();
								$("#bar_popup").html(parseInt(d.read_time/d.hits) + " seconds");
								$("#bar_popup").css({"top":(d3.event.pageY-30)+"px","left":(d3.event.pageX-100)+"px"});
							  })
							  .on("mouseleave",function(d) {
								$("#bar_popup").hide();
							  });

					d3.selectAll("tbody tr").sort(function(a,b){
						return d3.ascending(a.name,b.name);
					});
					
					d3.selectAll("thead th").on("click",function(){
						if(this.textContent == "STATE") {
							column = this;
							d3.selectAll("tbody tr").sort(function(a,b){
								if(parseInt($(column).attr("data-sorted")) == 0) {
									return d3.descending(a.name,b.name);
								} else {
									return d3.ascending(a.name,b.name);
								}
							});						
							$(this).attr("data-sorted",$(this).attr("data-sorted") == 0 ? 1:0); 
						}
						
						if(this.textContent == "TOTAL HITS") { 
							column = this;
							d3.selectAll("tbody tr").sort(function(a,b){
								if(parseInt($(column).attr("data-sorted")) == 0) {
									return parseInt(b.hits)-parseInt(a.hits);
								} else {
									return parseInt(a.hits)-parseInt(b.hits);
								}
							});		
							$(this).attr("data-sorted",$(this).attr("data-sorted") == 0 ? 1:0); 
						}
						
						if(this.textContent == "ATTENTION SPAN") { 
							column = this;
							d3.selectAll("tbody tr").sort(function(a,b){
								if(parseInt($(column).attr("data-sorted")) == 0) {
									return parseInt(b.read_time/b.hits)-parseInt(a.read_time/a.hits);
								} else {
									return parseInt(a.read_time/a.hits)-parseInt(b.read_time/b.hits);
								}
							});		
							$(this).attr("data-sorted",$(this).attr("data-sorted") == 0 ? 1:0); 
						}
					});
				}
				
				// creates the categories bar chart 
				var makeCategoriesBarChart = function(barData,type) {
					// define margins
					var margin = {top: 20, right: 20, bottom: 20, left: 50},
						width = 840 - margin.left - margin.right,
						height = 600 - margin.top - margin.bottom;
					
					var y = d3.scale.linear()
						.range([height,0]);
						
					max_hits = 0;
					min_hits = 0;
					
					max_read_time = 0;
					min_read_time = 0;
					
					for(d in CategoriesMetrics) {
						if(type == "read_time") {
							max_read_time = max_read_time >= CategoriesMetrics[d].read_time ? max_read_time : CategoriesMetrics[d].read_time; 						
						} else {
							max_hits = max_hits >= CategoriesMetrics[d].hits ? max_hits : CategoriesMetrics[d].hits; 
						}
					}
					
					console.log("max hits " + max_hits);
					console.log("max read time " + max_read_time);
					
					if(type == "read_time") {
						y.domain([min_read_time,max_read_time]);							
					} else {
						y.domain([min_hits,max_hits]);					
					}
				}
				
				// creates the line graph
				var makeLineGraph = function(lineData,type) {
					var margin = {top: 20, right: 20, bottom: 20, left: 50},
						width = 840 - margin.left - margin.right,
						height = 600 - margin.top - margin.bottom;
					
					var parseDate = d3.time.format("%Y%m%d").parse;
					
					var formatPercent = d3.format(".0%");
					
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
							
					var line_function = d3.svg.line()
						.interpolate("basis")
						.x(function(d) { return x(d.date); })
						.y(function(d) { 
							if(type == "read_time") {
								return y(d.read_time);
							} else {
								return y(d.count); 							
							}
						});

					var area_function = d3.svg.area()
						.interpolate("basis")
						.x(function(d) { return x(d.x); })
						.y0(height)
						.y1(function(d) { 
							return y(d.y);
						});					
					
					var stack_area = d3.svg.area()
						.x(function(d) { return x(d.x); })
						.y0(function(d) { return y(d.y0); })
						.y1(function(d) { return y(d.y0 + d.y); });
					
					var stack = d3.layout.stack()
						.values(function(d) { return d.values; });
					
					var ReadiedLineData = new Array();
						
					$("#line_graph").html("");
					
					var svg = d3.select("#line_graph").append("svg")
						.attr("id","svg_line_graph")
						.attr("width", "100%")
						.attr("height", height + margin.top + margin.bottom)
						.append("g")
						.attr("transform","translate(" + margin.left + "," + margin.top + ")");
						
					temp_min_date = endTime;
															
					// ready data for line creation
					for(line in lineData) {
						lineData[line].forEach(function(d,i){
							temp_min_date = temp_min_date > parseInt(d.time_visited) ? parseInt(d.time_visited) : temp_min_date;
							date_object = new Date(d.time_visited*1000);
							found = false;
							data_index = 0;
							for(readied_line in ReadiedLineData) {
								if(ReadiedLineData[readied_line].article_id == d.article_id) {
									found = true;
									data_index = readied_line;
								}
							}
							
							if(found == true) {
									ReadiedLineData[data_index].url = d.url;
									ReadiedLineData[data_index].article_title = d.title;
									ReadiedLineData[data_index].article_excerpt = d.sample_text;
									ReadiedLineData[data_index].article_pic_url = d.pic;							
								if(type == "read_time") {
									ReadiedLineData[data_index].values.push({x:date_object,y:d.read_time});
								} else {
									ReadiedLineData[data_index].values.push({x:date_object,y:d.count});
								}
							} else {
									i = ReadiedLineData.push({article_id:d.article_id,values:new Array()});
									ReadiedLineData[data_index].url = d.url;
									ReadiedLineData[i-1].article_title = d.title;
									ReadiedLineData[i-1].article_excerpt = d.sample_text;
									ReadiedLineData[i-1].article_pic_url = d.pic;
								if(type == "read_time") {
									ReadiedLineData[i-1].values.push({x:date_object,y:d.read_time});																
								} else {
									ReadiedLineData[i-1].values.push({x:date_object,y:d.count});								
								}
							}

						});
					}	
		
					x.domain(d3.extent(viz_object.getDayStamps()));
					color.domain(ReadiedLineData.map(function(d){return d.article_id}));
					
					max_hits = 0;
					min_hits = 0;
					
					max_read_time = 0;
					min_read_time = 0;
					
					/**
					for(line in lineData) {
						lineData[line].forEach(function(d,i){
							if(type == "read_time") {
								max_read_time = max_read_time >= d.read_time ? max_read_time : d.read_time; 						
							} else {
								max_hits = max_hits >= d.count ? max_hits : d.count; 
							}
						});
					}
					
					max_hits = 200;
					
					if(type == "read_time") {
						y.domain([min_read_time,max_read_time]);							
					} else {
						y.domain([min_hits,max_hits]);					
					}
					*/
					
					date_keys = d3.keys(lineData);
					hit_totals = new Array();
					
					// fill data for missing dates
					date_keys.forEach(function(populated_date){
						y_hits = 0;
						ReadiedLineData.forEach(function(d){
							found = false;
							d.values.forEach(function(e){
								if((e.x.getTime()) == parseInt(populated_date)) { 
									found = true;
									y_hits+=e.y;
								}
							});
							
							if(found == false) {
								d.values.push({x:new Date(parseInt(populated_date)),y:0});
								d.values.sort(function(a,b){
									return b.x-a.x;
								});
							}
						});
						hit_totals.push(y_hits);
					});

					//console.log(ReadiedLineData.map(function(d){ return d.values}));
					
					y.domain([0,d3.max(hit_totals)]);
					
					stack(ReadiedLineData);
					
					y_axis_text = type == "read_time" ? "Read Time (sec.)":"Hits";
					
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
					  .text(y_axis_text);
					

					var article = svg.selectAll(".article")
						.data(ReadiedLineData)
						.enter().append("g")
						.attr("class","article");
						
					article.append("path")
						.attr("d",function(d) {return stack_area(d.values); })
						.attr("id",function(d){ return d.article_id})
						.attr("class", "article")
						.style("opacity",0.6)
						.style("cursor","pointer")
						.style("fill","lightGray")
						.style("stroke","black")
						.style("z-index",100)
						.on("mouseenter",function(d){
							$(".title_text").hide();
							this.style.fill = "steelblue";
							d3.select("#t_"+this.id).style("text-decoration","underline");
							if(article_details != 1) {
								$("#article_popup").show();
								article_title_left = d3.event.pageX > 450 ? d3.event.pageX-350:d3.event.pageX;
								$("#article_popup").css({"left":article_title_left+"px","top":(d3.event.pageY-55)+"px"});
								$("#article_popup").html(d.article_title);
							}
							if(article_details == 1) {
								article_details_top = d3.event.pageY > 450 ? d3.event.pageY-450: d3.event.pageY+1;
								$("#article_title").html(d.article_title);
								$("#article_excerpt").html(d.article_excerpt.substring(0,300)+"...");
								$("#article_image").attr("src",d.article_pic_url);
								$("#article_details").css({left:(d3.event.pageX)+"px",top:(article_details_top)+"px"});
								$("#article_details").show();
							}
						})
						.on("mouseleave",function(d){
							$(".title_text").show();
							if(article_details != 1) {
								$("#article_popup").hide();
							}
							$("#article_details").hide();this.style.fill = "lightGray";
							d3.select("#t_"+this.id).style("text-decoration","none");
						})
						.on("click",function(d) { makeStatesBars(getArticleStates(d.article_id),d.article_title,d.url); 
							//window.open(d.values[0].url); 
						});
					
					  article.append("text")
						  .attr("class","title_text")
						  .attr("id",function(d){ return "t_" + d.article_id})
						  .datum(function(d) { return {article_title: d.article_title, value: d.values[d.values.length - 1]}; })
						  .attr("transform", function(d) { return "translate(" + x(d.value.x) + "," + y(d.value.y0 + d.value.y / 2) + ")"; })
						  .attr("x", 150)
						  .attr("dy", ".35em")
						  .style("opacity","1")
						  .style("font-size","12px")
						  .style("z-index","10")
						  .text(function(d) { return d.article_title; });
						
					/**
					var article = svg.selectAll(".article")
						.data(ReadiedLineData)
						.enter().append("g")
					    .attr("id",function(d) { return d.article_id })
						.attr("class", "article")
						.attr("opacity","0.6")
						.style("cursor","pointer")
						.style("fill","lightGray")
						.on("mouseenter",function(d){
							this.style.fill = "steelblue";
							if(article_details == 1) {
								article_details_top = d3.event.pageY > 450 ? d3.event.pageY-450: d3.event.pageY+1;
								$("#article_title").html(d.article_title);
								$("#article_excerpt").html(d.article_excerpt.substring(0,300)+"...");
								$("#article_image").attr("src",d.article_pic_url);
								$("#article_details").css({left:(d3.event.pageX)+"px",top:(article_details_top)+"px"});
								$("#article_details").show();
							}
						})
						.on("mouseleave",function(){$("#article_details").hide();this.style.fill = "lightGray";})
						.on("click",function(d) { makeStatesBars(getArticleStates(d.article_id),d.article_title,d.url); 
							//window.open(d.values[0].url); 
						});
					
					current_line_color = "";
					
					article.append("path")
					  .attr("class", "line")
					  .attr("d", function(d) { return area_function(d.values); })
					  .on("mouseover",function(d) { 
						current_line_color = this.style.stroke;
						this.style.stroke = "black";
						this.style.strokeWidth = "2px";
					  })
					  .on("mouseout",function() { 
						this.style.stroke = current_line_color; 
						this.style.strokeWidth = "1px"; 
					  })
					  .style("stroke", function(d) { return "black" });
					*/
					console.log(ReadiedLineData);
					console.log(lineData);
				}
				
				// gets the article categories, loads them in their corresponding dropdown box, and binds the category options to certain event handlers
				this.setArticlesCategories = function() {
					$.ajax({url:"getCategories.php"})
					 .done(function(data){
						Categories = JSON.parse(data);
						populateCategoriesDropdown();
						bindCategoriesEventHandlers();
						bindTypesEventHandlers();
						$("#article_details_checkbox").on("change",function(){
							article_details = this.checked ? 1:0;
						});
					 });
				};

			}

			// create a new instance of the visualization library
			viz = new VizLib();
			
			// load the articles categories into an array
			viz.setArticlesCategories();
			
			$(function() {
				// populates the days slider, with a min value of 1 day and a max value of 31 days. 
				$( "#articles_days_slider" ).slider({
					min: 1,
					max: 31,
					range: true,
					create: function() {
						// sets the label to a default of "1 day"
						$("#articles_days_label").html("1 day");
					},
					slide: function(e,t) {
						if(t.values[0] == 1) {
							// clause to be correct grammatically with the usage of day (singular) vs. days (plural)
							t.value>1?$("#articles_days_label").html("Last " + t.value + " days"):$("#articles_days_label").html("Last " + t.value + " day");
							// set the time range according to the values set in the slider
						} else {
							today = new Date();
							endDate = (today.getTime()/1000) - ((t.values[0]-1) * (24 * 60 * 60));
							endDate = new Date(endDate * 1000);
							endDate = endDate.getMonth() + "-" + endDate.getDate() + "-" + endDate.getFullYear();
							begDate = (today.getTime()/1000) - ((t.values[1]-1) * (24 * 60 * 60));
							begDate = new Date(begDate * 1000);
							begDate = begDate.getMonth() + "-" + begDate.getDate() + "-" + begDate.getFullYear();
							if(begDate != endDate) {
								$("#articles_days_label").html("Between " + begDate + " and " + endDate);
							} else {
								$("#articles_days_label").html("On " + begDate);
							}
						}
						viz.setArticlesTimeRange(t.values[0],t.values[1]);
					}
				});
				
				$( "#categories_days_slider" ).slider({
					min: 1,
					max: 31,
					create: function() {
						// sets the label to a default of "1 day"
						$("#categories_days_label").html("Last 1 day");
					},
					change: function(e,t) {
						// clause to be correct grammatically with the usage of day (singular) vs. days (plural)
						t.value>1?$("#categories_days_label").html(t.value + " days"):$("#categories_days_label").html(t.value + " day");
						// set the time range according to the values set in the slider
						viz.setCategoriesTimeRange(t.value);
					}
				});
				
				// make controls area draggable
				$("#controls_area").draggable();
				
				$("#articles_controls").click(function(){
					$("#controls_area").css("background-color","lightGray");
					$("#details_on_hover").show();
					$("#category_pick").show();
					$("#articles_viz_area").show();
					$(".categories_controls").hide();
					$(".articles_controls").show();
					$("#categories_viz_area").hide();
				});
				
				$("#categories_controls").click(function(){
					$("#controls_area").css("background-color","#C2D4E0");
					$("#details_on_hover").hide();
					$("#category_pick").hide();
					$("#articles_viz_area").hide();
					$(".articles_controls").hide();
					$(".categories_controls").show();
					$("#categories_viz_area").show();
					viz.loadCategories();
				});
			});			
			
			
		</script>
	</body>
</html>
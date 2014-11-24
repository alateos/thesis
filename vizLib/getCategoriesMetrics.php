<?php
	/**
		Returns the following metadata for all categories in JSON:
		- Number of hits
		- Average read time
	*/

	// the DSN for the database
	define(DSN,'mysql:host=localhost;dbname=alaini5_news_viz');

	// the edit username for the database
	define(EDIT_USERNAME, "alaini5_newsread");

	// the edit password for the database
	define(EDIT_PASSWORD,"mbn@7600");

	// connect to database
	try {
		// create a new PDO connection to the database
		$db = new PDO(DSN,EDIT_USERNAME,EDIT_PASSWORD);
	} catch(PDOExecption $e) {
		print "Error!: " . $e->getMessage() . "<br />";
		die();
	}

	// associate array to contain the categories' metrics
	$categories_metrics = array();

	// get the start visit time in Unix time
	$start_time = $_GET["start_time"];

	// get the end visit time in Unix time
	$end_time = $_GET["end_time"];

	// get all relevant metadata for the categories visited 
	// within our specified time period
	$sql = sprintf("select category_name, category.category_id as 'category_id', count(hit.id) as 'hits', avg(hit.read_time) as 'read_time'
					from article,category, hit
					where hit.article_id=article.article_id
					and article.category_id=category.category_id
					and time_visited >= %d and time_visited <= %d
					group by category.category_id order by category_name ASC",
					$start_time,$end_time); 

	// populate the $categories_metrics array with all the metadata
	foreach($db->query($sql) as $row) {
		$categories_metrics[$row["category_name"]]["category_id"] = $row["category_id"];
		$categories_metrics[$row["category_name"]]["category_name"] = $row["category_name"];
		$categories_metrics[$row["category_name"]]["hits"] = $row["hits"];
		$categories_metrics[$row["category_name"]]["read_time"] = $row["read_time"];
	}

	// close database connection
	$db = null;

	// encode final results in JSON format
	$categories_metrics = json_encode($categories_metrics);

	print $categories_metrics;
?>
<?php
	/**
		Returns the following metadata for all articles for 
		a given time period from a given category in JSON:
		- Time visited
		- Article ID
		- Article URL
		- Article title
		- Article sample text
		- Article featured image URL
		- Region of visitor
		- Actual read time of article
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

	// associate array to contain all the articles' metadata
	$articles = array();

	// get the category id from query string
	$category_id = $_GET["category_id"];

	// get the start visit time of the articles for this category in Unix time
	$start_time = $_GET["start_time"];

	// get the end visit time of the articles for this category in Unix time
	$end_time = $_GET["end_time"];

	// get all relevant metadata for the articles visited 
	// within our specified category and time period
	$sql = sprintf("select hit.id as 'hit_id', expected_read_time,time_visited, hit.article_id, 
			region, read_time, article.article_id, category_id, 
			article_url, title, sample_text,sample_pic from article,hit 
			where hit.article_id=article.article_id and 
			category_id=%d and time_visited >= %d and time_visited <= %d",
			$category_id,$start_time,$end_time); 
	
	// populate the $aritcle array with all the metadata
	foreach($db->query($sql) as $row) {
		$articles[$row["hit_id"]]["article_id"] = $row["article_id"];
		$articles[$row["hit_id"]]["time_visited"] = $row["time_visited"];
		$articles[$row["hit_id"]]["url"] = $row["article_url"];
		$articles[$row["hit_id"]]["title"] = $row["title"];
		$articles[$row["hit_id"]]["sample_text"] = $row["sample_text"];
		$articles[$row["hit_id"]]["pic"] = $row["sample_pic"];
		$articles[$row["hit_id"]]["region"] = $row["region"];
		$articles[$row["hit_id"]]["read_time"] = $row["read_time"];
		$articles[$row["hit_id"]]["expected_read_time"] = $row["expected_read_time"];
	}

	// close database connection
	$db = null;

	// encode final results in JSON format
	$articles = json_encode($articles);

	print $articles;
?>
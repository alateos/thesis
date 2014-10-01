<?php
	/**
		Returns the number of hits and what country they 
		came from for a given article id in JSON format
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

	// associative array to store the article hits 
	// and country of origin given an article
	$article_countries = array();
	
	// get the article id from query string
	$article_id = $_GET["article_id"];

	// get the sum of hits for a given article given their country of origin
	$sql = sprintf("select distinct country, count(hit.id) 
		as 'hits' from hit where  hit.article_id=%d 
		group by country",$article_id);
	
	// retrieve the number of hits and country name
	foreach($db->query($sql) as $row) {
		$article_countries[$row["country"]]["hits"] = $row["hits"];
		$article_countries[$row["country"]]["name"] = $row["country"];
	}
	
	// close database connection
	$db = null;
	
	// encode the array in json format
	$article_countries = json_encode($article_countries);
	
	print $article_countries;
?>
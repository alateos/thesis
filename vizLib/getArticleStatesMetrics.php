<?php
	/**
		Returns the number of hits and what states they 
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
	// and state of origin given an article
	$article_states = array();
	
	// get the article id from query string
	$article_id = $_GET["article_id"];

	// get the sum of hits for a given article given their state of origin
	$sql = sprintf("select distinct region, count(hit.id) 
		as 'hits' from hit where  hit.article_id=%d 
		group by region",$article_id);
	
	// retrieve the number of hits and state name
	foreach($db->query($sql) as $row) {	
		$article_states[$row["region"]]["hits"] = $row["hits"];
		$article_states[$row["region"]]["name"] = $row["region"];
	}
	
	// close database connection
	$db = null;
	
	// encode the array in json format
	$article_states = json_encode($article_states);
	
	print $article_states;
?>
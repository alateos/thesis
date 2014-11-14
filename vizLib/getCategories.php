<?php
	/**
		Returns the category ids and names in JSON format
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
	
	// get all categories
	$sql = "select * from category order by category_name ASC";

	// associative array to store categories
	$categories = array();
	
	// retrieve the id and name of each category and store it in the array
	$counter = 0;
	foreach($db->query($sql) as $row) {
		$categories[$counter]["id"] = $row["category_id"]; 
		$categories[$counter]["name"] = $row["category_name"];
		$counter++;
	}
	
	// close database connection
	$db = null;
	
	// encode the array in json format
	$categories = json_encode($categories);
	
	print $categories;
?>
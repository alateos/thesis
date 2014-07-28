<?php
	// get the connection strings to the database
	include("constants.php");

	try {
		// create a new PDO connection to the database
		$db = new PDO(DSN,EDIT_USERNAME,EDIT_PASSWORD);
	} catch(PDOExecption $e) {
		print "Error!: " . $e->getMessage() . "<br />";
		die();
	}
?>
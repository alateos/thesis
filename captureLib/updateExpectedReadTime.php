<?php
	/**
		This page is accessed via an AJAX call. It updates the expected read time of an article given its id
	*/
	include("wp-content/themes/twentyfourteen/captureLib/lib/db_connect.php");
	$expected_read_time = $_POST["expected_read_time"];
	$article_id = $_POST["article_id"];
	
	// if an expected read time and hit id are provided then update the expected read time in the database
	if($expected_read_time > 0 && $article_id > 0) {
		$stmt = $db->prepare("update article set expected_read_time=:expected_read_time where article_id=:article_id");
		$stmt->bindParam(':expected_read_time',$expected_read_time);
		$stmt->bindParam(':article_id',$article_id);
		$stmt->execute();
	}
?>
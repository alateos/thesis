<?php
	/**
		This page is accessed via an AJAX call. It updates the read time given an article id.
	*/
	include("wp-content/themes/twentyfourteen/captureLib/lib/db_connect.php");
	$read_time = $_POST["read_time"];
	$hit_id = $_POST["hit_id"];
	
	// if a read time and hit id are provided then update the read time in the database
	if($read_time > 0 && $hit_id > 0) {
		$stmt = $db->prepare("update hit set read_time=:read_time where id=:hit_id");
		$stmt->bindParam(':read_time',$read_time);
		$stmt->bindParam(':hit_id',$hit_id);
		$stmt->execute();
	}
?>
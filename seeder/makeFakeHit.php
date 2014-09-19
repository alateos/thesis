<?php
	/**
		The sole purpose of this file is to visit a random article on the 
		fake news site, causing hits to register
	*/
	
	$TESTING_IP = "98.172.153.142";
	
	// 290 is the id of the last fake article that was created
	$article_id = rand(1,290);
	
	// cause a fake visit only if testing ip address is registered here
	if( $_SERVER['REMOTE_ADDR'] == $TESTING_IP) {
		file_get_contents("http://alainibrahim.com/fakenewssite/?p=" . $article_id);
	}
?>
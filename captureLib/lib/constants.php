<?php
	// the DSN for the database
	define(DSN,'mysql:host=localhost;dbname=alaini5_news_viz');

	// the edit username for the database
	define(EDIT_USERNAME, "alaini5_newsedit");
	
	// the edit password for the database
	define(EDIT_PASSWORD,"mbn@7600");
	
	// the amount of time that needs to pass before we categorize the page hit as a new one. 
	// this value is in seconds and will be set to 10 minutes by default
	define(WAIT_LIMIT,20);
	
	// the number of characters that should be excerpted from the article
	define(EXCERPT_CHARS,500);
	
	// defines whether we are in test or production mode
	define(PRODUCTION_ENV,false);
?>
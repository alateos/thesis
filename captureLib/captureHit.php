<?php
	/**
		This is the all-inclusive file to be included within the CMS
	*/

	// include the database connection library
	include("lib/db_connect.php");
	
	// include the Hit class which registers most of the needed metadata of the site visit
	include("Hit.php");
	
	// include the Article class which registers metadata pertinent to the visited article
	include("Article.php");
	
	// include the Category class which registers metadata pertinent to the category of the visited article
	include("Category.php");
	
	// include the hook that returns metadata using prebuilt Wordpress functions
	include("wordpressHook.php");
	
	// get the article id from Wordpress
	$article_id = $wp_query->post->ID;
	
	// create a new hook into pre-built Wordpress functions
	$wordpressHook = new wordpressHook($article_id);
	
	// create a new hit
	$hit = new Hit($db);
	
	// retrieve the newly created hit ID from the database
	// to be inserted in the page's Javascript code for tracking of read time
	$hit_id = $hit->register($article_id);
	
	// if the hit registers
	if($hit_id) {
		// array to be passed to the Article class to populate the database with the article's metadata
		$article_metadata["article_id"] = $article_id;
		$article_metadata["category_id"] = $wordpressHook->getCategoryID();
		$article_metadata["url"] = $wordpressHook->getURL();
		$article_metadata["title"] = $wordpressHook->getTitle();
		$article_metadata["sample_text"] = $wordpressHook->getSampleText();
		$article_metadata["sample_pic"] = $wordpressHook->getSamplePic();
		
		// array to be passed to the Category class to populate the database with the category's metadata
		$category_metadata["category_id"] = $wordpressHook->getCategoryID();
		$category_metadata["category_name"] = $wordpressHook->getCategoryName();
		
		// create a new instance of an article and supply it with the pertinent article metadata
		$article = new Article($db,$article_metadata);
		// create a new instance of a category and supply it with the pertinent category metadata
		$category = new Category($db,$category_metadata);
	}
	
	// close database connection
	$db = null;
?>
<?php
	// include the database connection library
	include("../../../lib/db_connect.php");
	
	// include the Hit class which registers most of the needed metadata of the site visit
	include("Hit.php");
	
	// include the Article class which registers metadata pertinent to the visited article
	include("Article.php");
	
	// include the Category class which registers metadata pertinent to the category of the visited article
	include("Category.php");
	
	// create a new hit
	$hit = new Hit($db);
	
	// retrieve the newly created hit ID from the database
	// to be inserted in the page's Javascript code for tracking of read time
	$hit_id = $hit->register(1);

	// if the hit registers
	if($hit_id) {
		// populating a mock article with fake data
		$article_metadata["article_id"] = 1;
		$article_metadata["category_id"] = 1;
		$article_metadata["url"] = "http://alainibrahim.com/thesis/code";
		$article_metadata["title"] = "alain is testing an article";
		$article_metadata["sample_text"] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ullamcorper mattis ipsum, ut commodo metus lacinia vitae. Quisque aliquet tincidunt velit, vitae sollicitudin ante bibendum nec.";
		$article_metadata["sample_pic"] = "http://alainibrahim.com/thesis/code/pic.jpg";
		
		// populating a mock category
		$category_metadata["category_id"] = 2;
		$category_metadata["category_name"] = "Thesis";
		
		$article = new Article($db,$article_metadata);
		$category = new Category($db,$category_metadata);
	}
?>
<!DOCTYPE html> 
	<head>
		<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	</head>
	<body>
		<div id="test">
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ullamcorper mattis ipsum, ut commodo metus lacinia vitae. Quisque aliquet tincidunt velit, vitae sollicitudin ante bibendum nec. Integer interdum metus quis nulla cursus, quis elementum orci semper. Sed sit amet felis elit. Pellentesque consequat pharetra ante pellentesque blandit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam blandit turpis et sem dictum, sed dapibus velit consectetur. Sed nec accumsan nibh. Vivamus eu lectus non turpis interdum elementum ac vel diam. Donec nec dui et libero vestibulum consequat. Duis tempus ipsum libero, vel molestie dui commodo nec. Donec odio metus, tempus at leo commodo, pellentesque vehicula sapien. Aenean ac sapien vel felis molestie feugiat vitae quis felis.
		</div>

		<?php if($hit_id) { include("readTime.php"); } ?>
	</body>
</html>
<?php
	/**
		Class used to abstract the built-in and needed Wordpress functions
	*/
	class wordpressHook {
		// the article id to be set by the contructor
		private $article_id = "";
	
		// set the article id in the constructor
		public function __construct($article_id) {
			$this->article_id = $article_id;
		}
		
		// get the name of the category from Wordpress
		public function getCategoryName() {
			$category_name = get_the_category()[0]->cat_name;
			return $category_name;
		}
		
		// get the id of the category from Wordpress
		public function getCategoryID() {
			return 	get_cat_id($this->getCategoryName());
		}
		
		// get the url of the article from Wordpress
		public function getURL() {
			return get_permalink($this->article_id);		
		}
		
		// get the title of the article from Wordpress
		public function getTitle() {
			return get_the_title($this->article_id);		
		}
		
		// get an excerpt from the read article from Wordpress. The number of characters excerpted is based on the constant EXCERPT_CHARS
		public function getSampleText() {
			$sample_text = get_post($this->article_id,ARRAY_A);
			$sample_text = strip_tags($sample_text["post_content"]);
			$sample_text = substr($sample_text,0,EXCERPT_CHARS);
			return $sample_text;
		}
		
		// get the article's featured image URI from Wordpress
		public function getSamplePic() {
			$sample_pic = wp_get_attachment_image_src( get_post_thumbnail_id( $this->article_id ), 'single-post-thumbnail' );
			$sample_pic = $sample_pic[0];
			if(strlen($sample_pic) < 5) {
				$sample_pic = "none";
			}
			return $sample_pic;
		}
	}
?>
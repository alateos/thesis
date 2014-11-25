<?php
	/**
		This class is responsible for registering the metadata pertinent to an article
	*/
	class Article {
		private $db = "";
		
		/**
			assign database instance in constructor
		*/
		public function __construct($db,$metadata = Array()) {
			$this->db=$db;
			$this->register($metadata);
		}
		
		/**
			registers the article's title,category,url,sample text, and a sample image
		*/
		private function register($metadata) {
			// get article id
			$article_id = $metadata["article_id"];
			
			// insert the article metadata into the database
			$category_id = $metadata["category_id"];
			$article_url = $metadata["url"];
			$title = $metadata["title"];
			$sample_text = $metadata["sample_text"];
			$sample_pic = $metadata["sample_pic"];
			$publish_date = $metadata["publish_date"];
			$sql = "insert into article(article_id,publish_date,category_id,article_url,title,sample_text,sample_pic) values (:article_id,:publish_date,:category_id,:article_url,:title,:sample_text,:sample_pic)";
			$sth = $this->db->prepare($sql);

			$sth->bindParam(":article_id",$article_id,PDO::PARAM_INT);
			$sth->bindParam(":category_id",$category_id,PDO::PARAM_INT);
			$sth->bindParam(":article_url",$article_url,PDO::PARAM_STR);
			$sth->bindParam(":title",$title,PDO::PARAM_STR);
			$sth->bindParam(":sample_text",$sample_text,PDO::PARAM_STR);
			$sth->bindParam(":sample_pic",$sample_pic,PDO::PARAM_STR);
			$sth->bindParam(":publish_date",$publish_date,PDO::PARAM_STR);
			$sth->execute();
		}
	}
?>
<?php
	/**
		This class is responsible for registering the metadata pertinent to a category
	*/
	class Category {
		private $db = "";
		
		/**
			assign database instance in constructor
		*/
		public function __construct($db,$metadata = Array()) {
			$this->db=$db;
			$this->register($metadata);
		}
		
		/**
			registers the category's name
		*/
		private function register($metadata) {
			// get category id
			$category_id = $metadata["category_id"];
			// get category name
			$category_name = $metadata["category_name"];
			
			// insert category into database
			$sql = "insert into category(category_id,category_name) values(:category_id,:category_name)";
			$sth = $this->db->prepare($sql);
			$sth->bindParam(":category_id",$category_id,PDO::PARAM_INT);
			$sth->bindParam(":category_name",$category_name,PDO::PARAM_STR);
			$sth->execute();
		}
	}
?>
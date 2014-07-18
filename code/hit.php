<?php
	class Hit {
		private $db = "";
		
		public function __construct($db) {
			$this->db=$db;
		}
		
		public function registerHit() {
			$sql = sprintf("insert into hit(ip,time_visited,article_id,timezone,country,region,read_time) values('%s','%s','%d','%s','%s','%s','%d')",$ip,$time_visited,$article_id,$timezone,$country,$region,$read_time);
		}
		
		private function checkLastVisited() {
		
		}
		
		public function getIPCountryRegion() {
			$remote_addr = $_SERVER["REMOTE_ADDR"];
			$x_forwarded_for = $_SERVER["HTTP_X_FORWARDED_FOR"];
			$data = array();
			
			$url1 = "http://www.telize.com/geoip/" . $remote_addr; 
			$url2 = "http://www.telize.com/geoip/" . $x_forwarded_for; 

			// Get the country for the ip behind the first exposed remote address (from www.telize.com)
			$results1 = file_get_contents($url1);
			$results1 = json_decode($results1);
			$country1 = $results1->country;
			$region1 = $results1->region;

			// Get the country for the ip behind the second exposed remote address (from www.telize.com)
			$results2 = file_get_contents($url2);
			$results2 = json_decode($results2);
			$country2 = $results2->country;
			$region2 = $results2->region;
			
			if(strlen($country1) > 0 && strlen($country2) > 0) {
				$country = $country2;
				$region = $region2;
				$real_ip = $x_forwarded_for;
				$data[0] = $country;
				$data[1] = $real_ip;
				$data[2] = $region;
			} else {
				$country = $country1;
				$region = $region1;
				$real_ip = $remote_addr;
				$data[0] = $country;
				$data[1] = $real_ip;
				$data[2] = $region;
			}
			
			return $data;
		}
	}
?>
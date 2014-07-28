<?php
	class Hit {
		private $db = "";
		
		public function __construct($db) {
			$this->db=$db;
		}
		
		public function registerHit($article_id) {
			$data = $this->getHitData();
			$country = $data[0];
			$ip = $data[1];
			$region = $data[2];
			$timezone = $data[3];
			$time_visited = $data[4];
			
			$last_visit_time = $this->checkLastVisited($ip,$article_id);

			if(($time_visited - $last_visit_time) > WAIT_LIMIT) {
				$sql = sprintf("insert into hit(ip,time_visited,article_id,timezone,country,region,read_time) values('%s','%d','%d','%s','%s','%s','%d')",$ip,$time_visited,$article_id,$timezone,$country,$region,$read_time);

				$this->db->query($sql);	
			}
			
			return $this->db->lastInsertID();
		}
		
		private function checkLastVisited($ip,$article_id) {
			$sql = "select * from hit where article_id=$article_id and ip='$ip' order by time_visited desc limit 1";
			foreach($this->db->query($sql) as $row) {
				$last_visited = intval($row["time_visited"]);
			}
			return $last_visited;
		}
		
		private function setTimezone($timezone) {
			date_default_timezone_set($timezone);
		}
		
		private function getHitData() {
			$remote_addr = $_SERVER["REMOTE_ADDR"];
			$x_forwarded_for = $_SERVER["HTTP_X_FORWARDED_FOR"];
			$data = array();
			
			$url1 = "http://www.telize.com/geoip/" . $remote_addr; 
			$url2 = "http://www.telize.com/geoip/" . $x_forwarded_for; 

			// Get the country for the ip behind the first exposed remote address (from www.telize.com)
			if($remote_addr) {
				$results1 = file_get_contents($url1);
				$results1 = json_decode($results1);
				$country1 = $results1->country;
				$region1 = $results1->region;
				$timezone1 = $results1->timezone;
				$ip = $remote_addr;
			}
			
			// Get the country for the ip behind the second exposed remote address (from www.telize.com)
			if($x_forwarded_for) {
				$results2 = file_get_contents($url2);
				$results2 = json_decode($results2);
				$country2 = $results2->country;
				$region2 = $results2->region;
				$timezone2 = $results2->timezone;
				$ip = $x_forwarded_for;
			}
			
			if(strlen($country1) > 0 && strlen($country2) > 0) {
				$data[0] = $country2;
				$data[1] = $x_forwarded_for;
				$data[2] = $region2;
				$data[3] = $timezone2;
			} else {
				$data[0] = $country1;
				$data[1] = $remote_addr;
				$data[2] = $region1;
				$data[3] = $timezone1;
			}
			
			// Get timezone
			$this->setTimezone($data[3]);
			
			// Get timestamp of visit
			$date = new DateTime("NOW");
			$timestamp = $date->format('U');
			$data[4] = $timestamp;
			
			return $data;
		}
	}
?>
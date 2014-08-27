<script>
	// the average number of words per minute the user is expected to spend reading an article
	var AVERAGE_WPM = 200;
	// the maximum amount of time the user is expected to take to read the article
	var maxTimeToSpend = 0;
	
	/**
		Gets the number of words from the article based on common tags used
	*/
	var getNumberOfWords = function() {
		var total_words = 0;
		var tags = ["span","div","article","p","section"];
		
		// Go through each one of the designated tags. 
		// these are the common tags that contain
		// article content. Then, get the number of 
		// words under the pertinent tag.
		tags.forEach(function(d) {
			try {
				if(Array.prototype.slice.call($(d),0).length > 1) { 
					$.each($(d),function(index,value){
						total_words+=$(value).text().split(' ').length;	
					});
				} else {
					total_words+=$(d).text().split(' ').length;					
				}
			} catch(e) {

			}	
		});		
		return total_words;
	}
	
	/**
	Creates a Timer prototype that will keep track of time
	*/
	var Timer = function() {
		var seconds = 0;
		var timerRef = null;
		this.countTime = function() {
			timerRef = setInterval(function(){ 
				seconds+=1000;
			},1000);
		}
		this.stopCount = function() {
			clearInterval(timerRef);
		}
		this.getCount = function() {
			return seconds;
		}
	}
	
	/** 
		Gets the time spent in front of the article.
		If it is greater than the expected time, then use the expected time. Else, use the computed time.
	*/
	var getTimeSpent = function() {
		timer.stopCount();
		var words = getNumberOfWords();
		var maxTimeToSpend = (words/AVERAGE_WPM)*60;
		var calculableTime = timer.getCount()/1000;
		if(parseInt(calculableTime) > parseInt(maxTimeToSpend)) {
			return maxTimeToSpend;
		} else {
			return calculableTime;
		}
	}
	
	/**
		Sends the time spent on the article to the server
	*/
	var sendTime = function() {
		$.ajax({
		  url: "updateReadTime.php",
		  type:"post",
		  data: {hit_id:<?php echo $hit_id ?>,read_time:getTimeSpent()}
		}).done(function() {

		});
	}
	
	/**
		initiate the timer and the bindings on load
	*/
	$( document ).ready(function() {
		// create a new Timer object
		timer = new Timer();
		// begin counting
		timer.countTime();
		// have the time sent to the server once the window
		// closes or if the user navigates to another page
		if(!!window.chrome) {
			window.onbeforeunload = sendTime;
		} else {
			window.onunload = sendTime;
		}
	});
</script>
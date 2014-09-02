<script>
	/**
		This is the Javascript library that registers the read time of an article.
		It is suffixed with a .php extension since the script requires the hit id to be provided by php script.
	*/

	// the average number of words per minute the user is expected to spend reading an article
	var AVERAGE_WPM = 200;
	// the maximum amount of time the user is expected to take to read the article
	var maxTimeToSpend = 0;
	// flag to denote if the user has moved the mouse. if yes, the value is 1
	var mouse_moved = 0;
	// flag to denote if the user has scrolled the page. if yes, then the value is greater than 0
	var page_scroll_counter = 0;
	// flag to denote whether current tab is active or not. if yes, the value is 1
	var current_tab_active = 0;
	
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
		tags.forEach(function(d,area) {
			area = ".entry-content";
			d = area + " " + d;
			try {
				if(Array.prototype.slice.call($(d),0).length > 1) { 
					$.each($(d),function(index,value){
						words_length = $(value).text().trim().split(' ').length;
						if(words_length > 1) {
							total_words+=words_length;		
						}
					});
				} else {
					words_length = $(d).text().trim().split(' ').length;
					if(words_length > 1) {
						total_words+=words_length;		
					}
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
		// number of seconds counted
		var seconds = 0;
		// reference to the Timer object
		var timerRef = null;
		
		/** increase the timed counter */
		this.countTime = function() {
			timerRef = setInterval(function(){ 
				seconds+=1000;
			},1000);
		}
		
		/** stop the timed counter */
		this.stopCount = function() {
			clearInterval(timerRef);
		}
		
		/** get the number of seconds that have elapsed */
		this.getCount = function() {
			return seconds;
		}
	}
	
	/** 
		Gets the time spent in front of the article.
		If it is greater than the expected time, then use the expected time. Else, use the computed time.
	*/
	var getTimeSpent = function() {
		// stop the timer
		timer.stopCount();
		// get total number of words in text
		var words = getNumberOfWords();
		// get the expected maximum time to read the text
		var maxTimeToSpend = (words/AVERAGE_WPM)*60;
		// get the number of seconds spent reading the text
		var calculableTime = timer.getCount()/1000;
		// if the expected number of seconds has been exceeded, then return the expected number of seconds as the final measure.
		// else, return the actual time spent reading the text
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
		// have the time sent to the server once the window
		// closes or if the user navigates to another page
		if(!!window.chrome) {
			window.onbeforeunload = sendTime;
		} else {
			window.onunload = sendTime;
		}
		
		$("body").on("mousemove",function(){
			// if the user moves the mouse, start the timer. Also, cancel the event binding to both 'mousemove' and 'scroll'
			if(!mouse_moved) {
				mouse_moved = true;
				// create a new Timer object
				timer = new Timer();
				// begin counting
				timer.countTime();
			} else {
				$("body").unbind("mousemove");
				$(document).unbind("scroll");
			}
		});

		$(document).on("scroll",function(){
			// if the user starts scrolling the page, start the timer. Also, cancel the event binding to both 'mousemove' and 'scroll'
			if(page_scroll_counter == 1) {
				$("body").unbind("mousemove");
				$(document).unbind("scroll");				
			} else {
				// create a new Timer object
				timer = new Timer();
				// begin counting
				timer.countTime();
				page_scroll_counter++;
			}
		});
		
		// stop counting if user switches to another tab
		$(window).on("blur",function() {
			timer.stopCount();
		});
		
		// resume counting if user switches back to current tab
		$(window).on("focus",function() {
			timer.countTime();
		});
	});
</script>
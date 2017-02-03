<?php
	require "fbsdk/src/Facebook/autoload.php";
	
	// Facebook API credentials
	$fb = new Facebook\Facebook([
	  'app_id'                => '557340154442996',
	  'app_secret'            => 'f7e77bd1a4162810728679fd003fd817',
	  'default_graph_version' => 'v2.5',
	]);
	$access_token = "557340154442996|1vBSuTNdGQrbUAah4lGfzwP68lU";
	
	// Database details
	$hostname = "localhost";
	$username = "root";
	$password = "";
	$max_execution_time = "180"; //maximum execution time eg 180 for 180 seconds/ 3 minutes 

	// Database and table names
	$db = "fb_db";
	$posts_table = "posts";
	$videos_table = "videos";

	// Facebook page to export
	$page = "LostMelbourne"; //eg LostMelbourne for facebook.com/LostMelbourne
	$maxPosts = 300; //number of text-only/ photo with text posts to fetch
	$maxVideos = 10; //number of video posts to fetch
?>
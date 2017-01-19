<?php
	require "fbsdk/src/Facebook/autoload.php";
	
	$fb = new Facebook\Facebook([
	  'app_id'                => '',
	  'app_secret'            => '',
	  'default_graph_version' => 'v2.5',
	]);
	
	$hostname = "localhost";
	$username = "root";
	$password = "";
	
	$max_execution_time = "1200"; //300 = 5 minutes

	$db = "fb_db";
	$posts_table = "posts";
	$videos_table = "videos";

?>
<?php

require_once "tables.php";	

if ($db_selected && $postsDropped && $videosDropped) {
	// Setup MySQL library
	require_once 'meekrodb.2.3.class.php';
	DB::$user = $username;
	DB::$password = $password;
	DB::$dbName = $db;
	
	// Process videos
	processVideos();
	
	// Process posts
	processPosts();
}

function processVideos() {
	$fb = $GLOBALS['fb'];
	$page = $GLOBALS['page'];
	$access_token = $GLOBALS['access_token'];
	$videos_table = $GLOBALS['videos_table'];
	$maxPosts = $GLOBALS['maxPosts'];
	$maxVideos = $GLOBALS['maxVideos'];
	

	$videosEdge = $fb->get(
		$page . '/videos?fields=description,source,updated_time,likes.summary(true),comments.summary(true),shares',
		$access_token
	);
	$videosEdge = $videosEdge->getGraphEdge();

	$videoCount = 0;
	$stopVideosLoop = false;

	do {
		foreach($videosEdge as $video){
			if (++$videoCount > $maxVideos) {
				$stopVideosLoop = true;

				break;
			}

			// Calculate progress as we process a post
			$percent = intval($videoCount/($maxVideos+$maxPosts) * 100)."%";
			
			// Update progress bar via Javascript
			echo '<script>
			document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
			document.getElementById("information").innerHTML="<p>'.$videoCount.'/'.$maxVideos.' videos(s) processed</p>";
			</script>';

			// Insert into database
			DB::insert($videos_table, array(
			  'id' =>  $videoCount,
			  'fb_id' => ( isset( $video['id'] ) ? $video['id'] : '' ),
			  'description' => ( isset( $video['description'] ) ? $video['description'] : '' ),
			  'url' => ( isset( $video['source'] ) ? $video['source'] : '' ),
			  'posted_time' => ( isset( $video['updated_time'] ) ? $video['updated_time']->format('Y-m-d H:i:s') : '' ),
			  'likes_count' => ( isset( $video['likes'] ) ? $video["likes"]->getTotalCount() : 0 ),
			  'comments_count' => ( isset( $video['comments'] ) ? $video["comments"]->getTotalCount() : 0 )
			));
		}
	} while (!$stopVideosLoop && $videosEdge = $fb->next($videosEdge));
	
	echo '<script language="javascript">document.getElementById("information").innerHTML="<p>Facebook page exported to database successfully</p>"</script>';
	echo "<p>Videos processed</p>";
}

function processPosts() {
	$fb = $GLOBALS['fb'];
	$page = $GLOBALS['page'];
	$access_token = $GLOBALS['access_token'];
	$posts_table = $GLOBALS['posts_table'];
	$maxVideos = $GLOBALS['maxVideos'];
	$maxPosts = $GLOBALS['maxPosts'];
	
	// Temporary array for places
	$places = array();
	// Landmarks in Melbourne City
	$json = json_decode(file_get_contents("landmarks.json"), true);
	foreach ($json["data"] as $item) {
		array_push($places,  array("name" => $item[10], "latitude" => $item[11][1], "longitude" => $item[11][2]));
	}
	
	$postsEdge = $fb->get(
		$page . '/feed?fields=full_picture,picture,message,created_time,likes.summary(true),comments.summary(true),shares',
		$access_token
	);
	$postsEdge = $postsEdge->getGraphEdge();

	$postCount = 0;
	$stopPostsLoop = false;

	echo "<div class=\"fb-feed\">";

	do {
		foreach($postsEdge as $post){
			if (++$postCount > $maxPosts) {
				$stopPostsLoop = true;

				// Scroll to bottom of page where a success message is shown
				echo '<script>window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);</script>';

				break;
			}

			// Calculate progress as we process a post
			$percent = intval((($maxVideos+$postCount)/($maxVideos+$maxPosts))*100)."%";

			// Update progress bar via Javascript
			echo '<script>
			document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
			document.getElementById("information").innerHTML="<p>'.$postCount.'/'.$maxPosts.' post(s) processed</p>";
			</script>';
			
			// Print the post
			echo "<div class=\"fb-update\">";
				echo "<h2>#" . $postCount . " Posted on " . $post['created_time']->format('Y-m-d H:i:s') . "</h2>";
				echo (isset( $post['message'] ) ? "<p>" . $post['message'] . "</p>" : "<p>[Photo post]</p>" );
				
				$placeName = null;
				$latitude = null;
				$longitude = null;
				if (isset( $post['message'] )) {
					// Check if post has any suburbs mentioned
					foreach ($places as $place) {
						if (strpos($post['message'], $place['name'])) {
							$placeName = $place["name"];
							$latitude = $place["latitude"];
							$longitude = $place["longitude"];
							
							break;
						}
					}
					
				}

				if (isset($post['full_picture'])) {
					echo "<br><img src='" . $post['full_picture'] . "' height='50%' width='50%'>";
				}
				echo "<h2>Likes: " . ( isset( $post['likes'] ) ? $post["likes"]->getTotalCount() : 0 ) . "</h2>";
				echo "<h2>Comments: " . ( isset( $post['comments'] ) ? $post["comments"]->getTotalCount() : 0 ) . "</h2>";
				echo "<h2>Shares: " . ( isset( $post['shares'] ) ? json_decode($post['shares'])->count : 0 ) . "</h2>";
				
				// Insert into database
				DB::insert($posts_table, array(
				  'id' =>  $postCount,
				  'fb_id' => ( isset( $post['id'] ) ? $post['id'] : '' ),
				  'content' => ( isset( $post['message'] ) ? $post['message'] : '' ),
				  'image' => ( isset( $post['full_picture'] ) ? $post['full_picture'] : '' ),
				  'posted_date' => ( isset( $post['created_time'] ) ? $post['created_time']->format('Y-m-d H:i:s') : ''),
				  'likes_count' => ( isset( $post['likes'] ) ? $post["likes"]->getTotalCount() : 0 ),
				  'comments_count' => ( isset( $post['comments'] ) ? $post["comments"]->getTotalCount() : 0 ),
				  'shares_count' => ( isset( $post['shares'] ) ? json_decode($post['shares'])->count : 0 ),
				  'place' => $placeName,
				  'latitude' => $latitude,
				  'longitude' => $longitude 
				));
			
			echo "</div>"; // close fb-update div
		}
	} while (!$stopPostsLoop && $postsEdge = $fb->next($postsEdge));

	echo "<h1>All posts have been inserted into the database</h1>";

	// Update progress status to completed
	echo '<script language="javascript">document.getElementById("information").innerHTML="<p>Facebook page exported to database successfully</p>"</script>';

	echo "</div>";
}

?>
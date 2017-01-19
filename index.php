<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title></title>
		
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/base.css" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/cloud.css" />
		
	</head>

	<body>
		<div id="wrapper">
				<div id="main">
					<div id="clouds">
						<div class="cloud x1"></div>
						<!-- Time for multiple clouds to dance around -->
						<div class="cloud x2"></div>
						<div class="cloud x3"></div>
						<div class="cloud x4"></div>
						<div class="cloud x5"></div>
					</div>
					
					<div class="overlay">
						<div class="container">
							<h1>Lost Melbourne</h1>
														
							<!-- Progress bar holder -->
							<div id="progress" style="width:700px;border:1px solid #ccc;margin:0 auto;"></div>

							<!-- Progress information -->
							<div id="information" style="width:200px;margin:0 auto;"></div>
							
							<?php								
								require_once "config.php";
								ini_set('max_execution_time', $max_execution_time);
								require_once "tables.php";

								// Setup MySQL library
								require_once 'meekrodb.2.3.class.php';
								DB::$user = $username;
								DB::$password = $password;
								DB::$dbName = $db;
								
								// Temporary array for all places
								$places = array();
								
								// Landmarks in Melbourne City
								$url = 'landmarks.json';
								$json = json_decode(file_get_contents($url), true);
								
								echo "<p>Landmarks loaded successfully</p>";
								
								foreach ($json["data"] as $item) {
									array_push($places,  array("name" => $item[10], "latitude" => $item[11][1], "longitude" => $item[11][2]));
								}
								
								$videosEdge = $fb->get(
									'LostMelbourne/videos?fields=description,source,updated_time,likes.summary(true),comments.summary(true),shares',
									'CAAH65d3stPQBAIeF2JYvBVqeOMyN1mkcgfYZC16NsL17ayZAHnuWEGAu7mr9LqmC2JhPv0aYL6oaCZBIgZCn8vX7ZAR7JShPFriQpXafZA5WdCB0iwvM1Y9U7TVwZBrjKiioOJ0Pjj1t54veMKYxtZB8ZB8ABJINj6qZAFNQrC8r5cXzMc1VItKqB5'
								);
								$videosEdge = $videosEdge->getGraphEdge();

								
								$maxVideosPages = 10;
								$videosPageCount = 0;
								$videoCount = 1;

								do {
									foreach($videosEdge as $video){
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
											
										$videoCount++;
									}
								} while ($videoCount < $maxVideosPages && $videosEdge = $fb->next($videosEdge));
								
								echo "<p>Videos processed</p>";
								
								$postsEdge = $fb->get(
									'LostMelbourne/feed?fields=full_picture,picture,message,created_time,likes.summary(true),comments.summary(true),shares',
									'CAAH65d3stPQBAIeF2JYvBVqeOMyN1mkcgfYZC16NsL17ayZAHnuWEGAu7mr9LqmC2JhPv0aYL6oaCZBIgZCn8vX7ZAR7JShPFriQpXafZA5WdCB0iwvM1Y9U7TVwZBrjKiioOJ0Pjj1t54veMKYxtZB8ZB8ABJINj6qZAFNQrC8r5cXzMc1VItKqB5'
								);
								$postsEdge = $postsEdge->getGraphEdge();
								
								$maxPostsPages = 10;
								$postsPageCount = 0;
								$postCount = 1;

								echo "<div class=\"fb-feed\">";
								
								// 1755
								$total = 1760;
								
								do {
									foreach($postsEdge as $post){
										// Calculate the percentation
										$percent = intval($postCount/$total * 100)."%";
										
										// Javascript for updating the progress bar and information
										echo '<script language="javascript">
										document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
										document.getElementById("information").innerHTML="<p>'.$postCount.' post(s) processed</p>";
										</script>';
										
										echo "<div class=\"fb-update\">";
										
											echo "<h2>Posted on " . $post['created_time']->format('Y-m-d H:i:s') . "</h2>";
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
										
										$postCount++;
									}
								} while ($postsPageCount < $maxPostsPages && $postsEdge = $fb->next($postsEdge));
								
								echo "<h1>All posts have been inserted into the database</h1>";
								
								// Update progress status
								echo '<script language="javascript">document.getElementById("information").innerHTML="<p>Process completed</p>"</script>';

								echo "</div>";
							?>
						
						</div>
					</div>
				</div>

				<footer>
					<div class="container">

					</div>
				</footer>
			</div>
	</body>
</html>
<?php
	$postsDropped = false;
	$videosDropped = false;

	// Connect to database
	$db_handler = mysql_connect($hostname, $username, $password);
	if (!$db_handler) {
		die('<p>Could not connect: ' . mysql_error() . '</p>');
	}
	echo "<p>Connected to database successfully<br /></p>";
	
	// Check if database exists
	$db_selected = mysql_select_db($db, $db_handler);
	if (!$db_selected) {
		// If we couldn't, then it either doesn't exist, or we can't see it.
		$sql = 'CREATE DATABASE ' . $db;  
		
		if (mysql_query($sql, $db_handler)) {
			echo "<p>Database <i>" . $db . "</i> created successfully<br /></p>";
		} else {
			echo '<p>Error creating database: ' . mysql_error() . "<br /></p>";
		}
	} else {
		if ($result = mysql_query("SHOW TABLES", $db_handler))
		{
		    while($row = mysql_fetch_array($result, MYSQL_NUM))
		    {
		        mysql_query('DROP TABLE IF EXISTS '.$row[0], $db_handler);
		    }

		    echo "<p>All tables deleted</p>";
		}
	}
	
	// Delete posts table if already exists, then (re)create the table
	if (mysql_query("DROP TABLE IF EXISTS " . $posts_table, $db_handler)) {
		// echo "<p>Table <i>" . $posts_table . "</i> deleted successfully<br /></p>";
		
		$sql = "CREATE TABLE IF NOT EXISTS " . $posts_table . " (
			id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			fb_id VARCHAR(1000),
			content LONGTEXT,
			image VARCHAR(2048),
			posted_date TIMESTAMP,
			likes_count int UNSIGNED,
			comments_count int UNSIGNED,
			shares_count int UNSIGNED,
			place MEDIUMTEXT NULL,
			latitude DOUBLE(100,10) NULL,
			longitude DOUBLE(100,10) NULL
		)";
		
		if (mysql_query($sql, $db_handler)) {
			echo "<p>Table <i>" . $posts_table . "</i> created successfully<br /></p>";

			$postsDropped = true;
		} else {
			 echo '<p>Error creating table <i>' . $posts_table . '</i>: ' . mysql_error() . "<br /></p>";
		}
	} else {
		echo '<p>Error deleting table <i>' . $posts_table . '</i>: ' . mysql_error() . "<br /></p>";
	}

	// Delete videos table if already exists, then (re)create the table
	if (mysql_query("DROP TABLE IF EXISTS " . $videos_table, $db_handler)) {
		// echo "<p>Table <i>" . $videos_table . "</i> deleted successfully<br /></p>";
		
		$sql = "CREATE TABLE IF NOT EXISTS " . $videos_table . " (
			id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			fb_id VARCHAR(1000),
			description LONGTEXT,
			url VARCHAR(2048),
			posted_time TIMESTAMP,
			likes_count int UNSIGNED,
			comments_count int UNSIGNED
		)";
		
		if (mysql_query($sql, $db_handler)) {
			echo "<p>Table <i>" . $videos_table . "</i> created successfully<br /></p>";

			$videosDropped = true;
		} else {
			 echo '<p>Error creating table <i>' . $videos_table . '</i>: ' . mysql_error() . "<br /></p>";
		}
	} else {
		echo '<p>Error deleting table <i>' . $videos_table . '</i>: ' . mysql_error() . "<br /></p>";
	}
	
	mysql_close($db_handler);

?>
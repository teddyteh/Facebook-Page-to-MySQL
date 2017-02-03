<?php
	require_once "config.php";
	ini_set('max_execution_time', $max_execution_time);
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title><?php echo $page ?></title>
		
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
						<div class="cloud x2"></div>
						<div class="cloud x3"></div>
						<div class="cloud x4"></div>
						<div class="cloud x5"></div>
					</div>
					
					<div class="overlay">
						<div class="container">
							<h1><?php echo $page ?></h1>
														
							<!-- Progress bar holder -->
							<div id="progress" style="width:600px;border:1px solid #ccc;margin:0 auto;"></div>

							<!-- Progress information -->
							<div id="information" style="width:600px;margin:0 auto;"></div>
							
							<?php
								require "process.php";
							?>
						
						</div>
					</div>
				</div>

				<footer>
					<div class="container"></div>
				</footer>
			</div>
	</body>
</html>
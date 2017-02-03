<?php
	/* $url = 'https://data.melbourne.vic.gov.au/api/views/j5vt-ppat/rows.json?accessType=DOWNLOAD';
	$content = file_get_contents($url);
	$json = json_decode($content, true);
	
	foreach ($json["data"] as $item) {
		echo $item[10] . "<br>";
		echo $item[11][1] . ",";
		echo $item[11][2] . "<br><br>";
	} */
	
	// Temporary array for all places
	$places = array();
								
	// Landmarks in Melbourne City
	$url = 'https://data.melbourne.vic.gov.au/api/views/j5vt-ppat/rows.json?accessType=DOWNLOAD';
	$json = json_decode(file_get_contents($url), true);
	
	foreach ($json["data"] as $item) {
		array_push($places,  array("name" => $item[10], "latitude" => $item[11][1], "longitude" => $item[11][2]));
	}
	
	$suburbToDb = null;
	$latitude = null;
	$longitude = null;
	
	$string = "Beautiful view of the Alexandra Gardens, looking towards the Yarra River up to Princes Bridge and Flinders St Railway Station. Circa 1891 - 1914 . Photo source: Gift; Mr. Geoff Emmanuel; 2008. State Library of Victoria.";
	// Check if post has any suburbs mentioned
	foreach ($places as $place) {
		if (strpos($string, $place["name"])) {
			$suburbToDb = $place["name"];
			$latitude = $place["latitude"];
			$longitude = $place["longitude"];
			
			break;
		}		
	}
	
	echo $suburbToDb . "<br>";
	echo $latitude . "," . $longitude;
?>
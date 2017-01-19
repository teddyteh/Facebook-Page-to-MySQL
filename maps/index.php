<!DOCTYPE html>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
  <script src="http://maps.google.com/maps/api/js?sensor=false" 
          type="text/javascript"></script>
</head> 
<body>
  <div id="map" style="width: 500px; height: 400px;"></div>

  <script type="text/javascript">
    <?php
		require_once "../config.php";
		
		// Setup MySQL library
		require_once '../meekrodb.2.3.class.php';
		DB::$user = $username;
		DB::$password = $password;
		DB::$dbName = $db;
		
		$results = DB::query("SELECT * FROM " . $posts_table . " WHERE place IS NOT NULL");

		$places = array();
		
		foreach ($results as $place) {
			array_push($places, array($place["id"], $place["place"], $place["latitude"], $place["longitude"]));
		}
		
		echo "var locations = " . json_encode($places). ";";
	?>

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(-37.8141, 144.9633),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][2], locations[i][3]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent("Post id: " + locations[i][0] + " | Tagged location: " + locations[i][1]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
</body>
</html>
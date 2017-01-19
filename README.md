# Facebook-Page-to-MySQL
A script to export all posts that were published by a Facebook page to a MySQL database.

## Screenshots
![](h)

## How to use
- To use this script, simply extract all files to the root of a web server.
- All configurations can be done in config.php. This includes Facebook app details, database credentials and generated table names.

## Note
- Table creation script (table.php) has been included in the main script index.php.
- meekrodb.2.3.class.php is a MySQL library for PHP which we use to handle database actions.
- landmarks.json is a dataset of popular places along with their coordinates in JSON format, which we use to do pattern matching with in the main script (index.php)
- suburbs.txt is a list of all suburbs in Melbourne. Although unused for now, it may be useful to get a larger set of coordinates when looking for places.
- /maps demonstrates how to use the Google Maps API, given a set of coordinates we got from the dataset mentioned earlier

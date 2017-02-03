# Facebook-Page-to-MySQL
A script to export all posts that were published by a Facebook page to a MySQL database.

## Screenshots
####Main UI
![main](http://i.imgur.com/SPkWSUP.png)
#### Success message
![success message](http://i.imgur.com/LCQepLH.png)
#### Database
![MySQL database](http://i.imgur.com/kwlsUit.png)

## How to use
- To use this script, simply extract all files to the root of a web server.
- All configurations can be done in config.php. This includes Facebook API credentials, database details, and generated database and table names.

## Notes
- tables.php drops all tables in the specified database (in config.php) and recreates videos and posts tables before main script executes
- meekrodb.2.3.class.php is a MySQL library which we use to handle database actions.
- landmarks.json is a dataset of popular places along with their coordinates in JSON format, which we use to do pattern matching while processing posts.
- suburbs.txt is a list of all suburbs in Melbourne. Although unused for now, it may be useful to get a larger set of coordinates when looking for places.
- /maps demonstrates how to use the Google Maps API, given a set of coordinates we got from the dataset mentioned earlier

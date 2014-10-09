<?php

require_once('../../../FirePHPCore/fb.php');
# Include required geoPHP library and define wkb_to_json function
include_once('geoPHP/geoPHP.inc');
require_once ('../../../../mysql_connect_phpsql.php'); // Connect to the database.
// Polygon WKT example
$polygon = geoPHP::load('POLYGON((1 1,5 1,5 5,1 5,1 1),(2 2,2 3,3 3,3 2,2 2))','wkt');
$area = $polygon->getArea();
$centroid = $polygon->getCentroid();
$centX = $centroid->getX();
$centY = $centroid->getY();
echo "<br /> Message from testgeophp print out available drivers <br />  ";

print_r(PDO::getAvailableDrivers());
echo "<br /> =========================================<br />  ";
$db = new PDO('sqlite:testDB.db');

echo "<br /> After call to PDO =========================================<br />  ";
fb($db,"After call to PDO" );
print "This polygon has an area of ".$area." and a centroid with X=".$centX." and Y=".$centY;

// MultiPoint json example
print "<br/>";
$json = 
'{
   "type": "MultiPoint",
   "coordinates": [
       [100.0, 0.0], [101.0, 1.0]
   ]
}';

$multipoint = geoPHP::load($json, 'json');
$multipoint_points = $multipoint->getComponents();
$first_wkt = $multipoint_points[0]->out('wkt');

print "This multipoint has ".$multipoint->numGeometries()." points. The first point has a wkt representation of ".$first_wkt;
?>
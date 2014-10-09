<?php
/**
 * Title:   SQLite to GeoJSON (Requires https://github.com/phayes/geoPHP)
 * Notes:   Query a SQLite table or view (with a WKB GEOMETRY field) and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc. Use QGIS to OGR to convert your GIS data to SQLite.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */
require_once('../../../FirePHPCore/fb.php');
# Include required geoPHP library and define wkb_to_json function
include_once('geoPHP/geoPHP.inc');

//require_once ('../../../../mysql_connect_phpsql.php'); // Connect to the database.

function wkb_to_json($wkb) {
    $geom = geoPHP::load($wkb,'wkb');
    return $geom->out('json');
}
/* $message = "from Rose: ";
fb($message,'Available drivers');

print_r(PDO::getAvailableDrivers());
*/
# Connect to SQLite database
$conn = new PDO('sqlite:cdta_gis.sqlite');
// fb($conn,' command to PDO query ');

# Build SQL SELECT statement and return the geometry as a GeoJSON element
$sql = 'SELECT *, GEOMETRY AS wkb FROM cdta_bike_racks';
// fb($sql,'command to sql query ');
# Try query or error
$rs = $conn->query($sql);

// fb($rs,' result of query ');
if (!$rs) {
//	fb(!$rs,'NOT result to sql query ');
    echo 'An SQL error occured.\n';
    exit;
}
//fb($rs,' Build json ');
# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);
$count=0;
//fb($geojson,' json collection ');
# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove wkb and geometry fields from properties
    unset($properties['wkb']);
    unset($properties['GEOMETRY']);
    $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode(wkb_to_json($row['wkb'])),
         'properties' => $properties
    );
    if($count ==0 ){
  fb($count,"In loop");
//    fb($properties,"In loop");
    // fb($row,"In loop");
    $count=1;
    
}
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}
//fb($geojson,' At end');
header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
?>
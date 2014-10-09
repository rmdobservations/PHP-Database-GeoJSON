<?php
/**
 * Title:   MySQL to GeoJSON (Requires https://github.com/phayes/geoPHP)
 * Notes:   Query a MySQL table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */

# Include required geoPHP library and define wkb_to_json function
include_once('demo/geoPHP/geoPHP.inc');
require_once('../../FirePHPCore/fb.php');
# Include required geoPHP library and define wkb_to_json function
#require_once ('../../../mysql_connect_phpsql.php'); // Connect to the database.


function wkb_to_json($wkb) {
    $geom = geoPHP::load($wkb,'wkb');
    return $geom->out('json');
}

# Connect to MySQL database
$conn = new PDO('mysql:host=localhost;dbname=mydatabase','myusername','mypassword');

# Build SQL SELECT statement and return the geometry as a WKB element
#$sql = 'SELECT *, AsWKB(SHAPE) AS wkb FROM parcels';
$sql = 'SELECT *, AsWKB(SHAPE) AS wkb FROM parcels';
$query = "SELECT upload_id, file_name, ROUND(file_size/1024) AS fs, description, DATE_FORMAT(date_entered, '%M %e, %Y') AS d FROM uploads ORDER BY date_entered DESC";
fb($sql,'try query or error ');
fb($query,'try query or error ');
# Try query or error
$rs = $conn->query($sql);
fb($rs,'try query or error ');

if (!$rs) {

    echo 'An SQL error occured. \n';
    exit;
}

# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove wkb and geometry fields from properties
    unset($properties['wkb']);
    unset($properties['SHAPE']);
    $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode(wkb_to_json($row['wkb'])),
         'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
?>
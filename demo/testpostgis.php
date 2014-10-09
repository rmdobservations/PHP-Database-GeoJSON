<?php

require_once('../../../FirePHPCore/fb.php');
# Include required geoPHP library and define wkb_to_json function
include_once('geoPHP/geoPHP.inc');
$host =     'localhost';
$database = 'phayes';
$table =    'test';
$column =   'geom';
$user =     'phayes';
$pass =     'supersecret';

$connection = pg_connect("host=$host dbname=$database user=$user password=$pass");

// Working with PostGIS and Extended-WKB
// ----------------------------

// Using asBinary and GeomFromWKB in PostGIS
$result = pg_fetch_all(pg_query($connection, "SELECT asBinary($column) as geom FROM $table"));
foreach ($result as $item) {
  $wkb = pg_unescape_bytea($item['geom']); // Make sure to unescape the hex blob
  $geom = geoPHP::load($wkb, 'ewkb'); // We now a full geoPHP Geometry object

  // Let's insert it back into the database
  $insert_string = pg_escape_bytea($geom->out('ewkb'));
  pg_query($connection, "INSERT INTO $table ($column) values (GeomFromWKB('$insert_string'))");
}

// Using a direct SELECT and INSERTs in PostGIS without using wrapping functions
$result = pg_fetch_all(pg_query($connection, "SELECT $column as geom FROM $table"));
foreach ($result as $item) {
  $wkb = pack('H*',$item['geom']);   // Unpacking the hex blob
  $geom = geoPHP::load($wkb, 'ewkb'); // We now have a geoPHP Geometry

  // To insert directly into postGIS we need to unpack the WKB
  $unpacked = unpack('H*', $geom->out('ewkb'));
  $insert_string = $unpacked[1];
  pg_query($connection, "INSERT INTO $table ($column) values ('$insert_string')");
}

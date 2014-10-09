
<?php
require_once('../../../FirePHPCore/fb.php');
//require_once ('../../../../mysql_connect_phpsql.php'); // Connect to the database.
$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
echo "<br /> =========================================<br />  ";
fb($json,"initial data set");



echo "<br /> before var dump =========================================<br />  ";
var_dump(json_decode($json));

echo "<br /> ===before var dump with true flag======================================<br />  ";
var_dump(json_decode($json, true));
echo "<br /> =========================================<br />  ";
?>

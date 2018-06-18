<?php
/*	
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
//database
$DB_HOST=$url["host"];
$DB_USERNAME=$url["user"];
$DB_PASSWORD=$url["pass"];
$DB_NAME=substr($url["path"], 1);
*/
$DB_HOST=getenv("db_host");
$DB_USERNAME=getenv("db_user");
$DB_PASSWORD=getenv("db_pass");
$DB_NAME=getenv("db_db");

//get connection
//(MySQLi Object-Oriented)

global $conn;
echo "DB HOST " . $DB_HOST . "<br>";
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if(!$conn){
	die("Connection failed: " . $conn->error);
}
else
	$acentos = $conn->query("SET NAMES 'utf8'");


?>
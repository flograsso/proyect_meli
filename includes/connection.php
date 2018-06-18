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
$DB_USERNAME=getenv("branded_azd5889f");
$DB_PASSWORD=getenv("n-2(NBApr5~e");
$DB_NAME=getenv("branded_melidb");

//get connection
//(MySQLi Object-Oriented)

global $conn;
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if(!$conn){
	die("Connection failed: " . $conn->error);
}
else
	$acentos = $conn->query("SET NAMES 'utf8'");


?>
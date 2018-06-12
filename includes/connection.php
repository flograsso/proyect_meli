<?php

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
//database
$DB_HOST=$url["host"];
$DB_USERNAME=$url["user"];
$DB_PASSWORD=$url["pass"];
$DB_NAME=substr($url["path"], 1);

//get connection
//(MySQLi Object-Oriented)

global $conn;
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if(!$conn){
	die("Connection failed: " . $conn->error);
}

$acentos = $conn->query("SET NAMES 'utf8'");


?>
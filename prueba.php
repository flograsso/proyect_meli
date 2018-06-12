<!DOCTYPE html>
    <html lang="en" >

<p> Hola </p>
<?php
//require '../Meli/meli.php';
//require '../configApp.php';


$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
echo $db;
echo $server;
$conn = new mysqli($server, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";



$sql = "SELECT * FROM `tabla1` WHERE 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id:".$row['idPregunta']."<br>";
        echo "preg:".$row['pregunta']."<br>";
        echo "resp:".$row['respuesta']."<br>";
        echo "demor:".$row['demora']."<br>";
    }
} else {
    echo "0 results";
}


$conn->close();

?>

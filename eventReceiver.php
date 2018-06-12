<!DOCTYPE html>
    <html lang="en" >
<?php
header("Content-Type: text/html;charset=utf-8");

include ("includes/example_login.php");
require_once ("includes/phpFunctions.php");
global $conn;

$data = json_decode(file_get_contents('php://input'), true);
http_response_code(200);


$topic=($data["topic"]);
$resource=($data["resource"]);

switch($topic) 
{
    case "questions":
        $resource= preg_replace("/[^0-9]/","", $resource);
        procesarPregunta($resource);
        break;

    default:
        $str = "{";
        foreach ($data as $key => $value) {
            $str=$str ."$key" . ":" . "$value" . ",";
        }
        $str=$str."}";
        setValueDb("preguntas","idPregunta,textoPregunta,estadoPregunta,fechaRecibida,textoRespuesta,fechaRespuesta,idUsuario,idItem,demoraRtaSeg","'1','$topic','$resource',NULL,'$str',NULL,NULL,NULL,NULL");
}

$date1='2018-04-05';
$date2='2018-04-05';


$conn->close;








?>
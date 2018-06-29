<!DOCTYPE html>
    <html lang="en" >
<?php
header("Content-Type: text/html;charset=utf-8");

include ("includes/example_login.php");
require_once ("includes/phpFunctions.php");
global $conn;

$data = json_decode(file_get_contents('php://input'), true);
http_response_code(200);

if (isset($data["topic"]))
    $topic=($data["topic"]);

if (isset($data["resource"]))
    $resource=($data["resource"]);

switch($topic) 
{
    case "questions":
        $resource= preg_replace("/[^0-9]/","", $resource);
        procesarPregunta($resource);
        $conn->close;
        break;
    case "messages":
        procesarMensaje($resource);
        $conn->close;
        break;
    case "orders_v2":
        $resource= preg_replace("/[^0-9]/","", $resource);
        procesarOrden($resource);
        $conn->close;
        break;
    case "getQuestionNumber":
        echo getValueConditionDb('questions',"estadoPregunta='UNANSWERED'","count(idPregunta)");
        $conn->close;
        break;
    default:
        if (isset($data["received"])){
            $date = ($data["received"]);
            procesarNotification($topic,$date,$resource);
            $conn->close;
        }
        break;
    }

    echo "Ok";











?>

<?php
//setting header to json
//header('Content-Type: application/json');

require_once ('phpFunctions.php');
include ("example_login.php");

$METHOD=$_POST["method"];

switch ($METHOD)
{
    case "executeQuery":
        $path=$_POST["query"];
        echo json_encode(getMeli($path));
        break;
    case "getQuestionsUnanswered":
        echo getValueConditionDb('questions',"`estadoPregunta`='ANSWERED'");
        break;

	default:
		break;

}

?>



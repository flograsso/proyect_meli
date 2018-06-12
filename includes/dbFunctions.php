
<?php

require_once ('connection.php');


function setValueDb($table, $fields, $values)
{
    global $conn;
    $sql="INSERT INTO `$table` ($fields) VALUES ($values);";
    $conn->query($sql);

}

function updateValueDb($table,$field,$newValue,$conditionField,$conditionValue)
{
    global $conn;
    $sql="UPDATE `$table` SET `$field`='$newValue' WHERE `$conditionField`='$conditionValue' ;";
    $conn->query($sql);
}

function updateLastValueDb($table, $field, $value)
{
    global $conn;
    $sql="UPDATE `$table` SET `$field`='$value' WHERE 1 LIMIT 1 ;";
    $conn->query($sql); 
}

function getAllValuesDb($table)
{
    global $conn;
    $sql="SELECT * FROM `$table` WHERE 1;";
    $result = $conn->query($sql);
    $outp = array();
    $outp = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($outp);
}

function checkExistsValue($table,$field,$value)
{
    global $conn; 
    $sql="SELECT * FROM `$table` WHERE `$field`='$value';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
       return true;
    else
        return false;
    
}





?>
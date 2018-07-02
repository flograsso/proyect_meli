
<?php

require_once ('connection.php');

function deleteValueDb($table,$conditionField,$conditionValue)
{
    global $conn;
    $sql="DELETE FROM `$table` WHERE $conditionField='$conditionValue';";
    $conn->query($sql);
}

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

function getValueConditionDb($table,$condition,$select)
{
    global $conn;
    $sql="SELECT $select FROM `$table` WHERE $condition;";
    $result = $conn->query($sql);
    $outp = array();
    $outp = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($outp);
}

function checkExistsValue($table,$field,$value)
{
    global $conn; 
    if ($stmt = $conn->prepare('SELECT * FROM `$table` WHERE ' . $field . '=?')) {
        $stmt->bind_param("s", $value);
        $result = $stmt->execute();
    }        
    
    if ($result->num_rows > 0)
    {
        echo "true";
        return true;

    }
    
 else
 {  
    echo "false";
     return false;
 }

    
}






?>
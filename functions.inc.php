<?php

function connectToMySQL()
{
    include("variables.inc.php");
    return new mysqli($host, $user, $pass, $db);
}


function cleanup_db_query($mysqli, $stmt)
{
    $stmt->close();
    $mysqli->close();
}

function simple_select($query)
{
    $mysqli = connectToMySQL();
    $stmt = $mysqli->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    cleanup_db_query($mysqli, $stmt);
    return $result;
}

?>
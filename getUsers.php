<?php
include("functions.inc.php");

function fetchUsers()
{
    $result = simple_select('SELECT DISTINCT `name` FROM `users`');

    $returnVal = "";
    while ($row = $result->fetch_assoc()) {
        $returnVal .= $row['name'] . ",";
    }
    return rtrim($returnVal, ",");
}

echo fetchUsers();

?>
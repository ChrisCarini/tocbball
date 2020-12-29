<?php
include("functions.inc.php");

function fetchLifetimeStats()
{
    $result = simple_select('SELECT DISTINCT *, SUM(totalPoints) AS total, 30*COUNT(*) AS totalAttempts, SUM(totalPoints)/(30*COUNT(*)) as percent FROM `stats` GROUP BY `shooterName` ORDER BY `percent` DESC');

    $returnVal = "";
    while ($row = $result->fetch_assoc()) {
        $returnVal .= $row['shooterName'] . "|" . $row['total'] . "|" . $row['totalAttempts'] . ",";
    }
    return rtrim($returnVal, ",");
}

echo fetchLifetimeStats();

?>

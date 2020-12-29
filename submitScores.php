<?php
include("functions.inc.php");

function userExists($name)
{
    $mysqli = connectToMySQL();
    $stmt = $mysqli->prepare("SELECT * FROM `users` WHERE `name` = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;

    cleanup_db_query($mysqli, $stmt);

    return $num_rows > 0;
}

function addUser($name)
{
    $mysqli = connectToMySQL();
    $stmt = $mysqli->prepare("INSERT INTO `users` (`id`, `name`, `dateadded`) VALUES ('', ?, NOW())");
    $stmt->bind_param("s", $name);

    if (!$stmt->execute()) {
        $err = "Error Occurred Inserting User(" . $name . ") into Database; exiting";
        echo $err;
        cleanup_db_query($mysqli, $stmt);
        exit($err);
    }
    cleanup_db_query($mysqli, $stmt);
}

function submitScore($shooterName, $scorerName, $round1Score, $round2Score, $round3Score)
{
    $mysqli = connectToMySQL();
    $stmt = $mysqli->prepare("INSERT INTO `stats` (`id`, `shooterName`, `scorerName`, `round1Points`, `round2Points`, `round3Points`, `totalPoints`, `dateadded`) VALUES ('', ?, ?, ?, ?, ?, ?, NOW())");
    $total_points = $round1Score + $round2Score + $round3Score;
    $stmt->bind_param("ssssss", $shooterName, $scorerName, $round1Score, $round2Score, $round3Score, $total_points);

    if (!$stmt->execute()) {
        $err = "Error Occurred Inserting Score into Database; exiting";
        echo $err;
        cleanup_db_query($mysqli, $stmt);
        exit($err);
    }

    cleanup_db_query($mysqli, $stmt);
    return "Score Submitted Successfully!!";
}

$shooterName = preg_replace('/\s+/', '', $_POST['shooterSelect']); // Remove all whitespace from POST var
$scorerName = preg_replace('/\s+/', '', $_POST['scorerSelect']);  // Remove all whitespace from POST var
$round1Score = intval($_POST['round1Select']);
$round2Score = intval($_POST['round2Select']);
$round3Score = intval($_POST['round3Select']);


// Check Inputs, Respond w/ Error if appropriate
foreach (array($shooterName, $scorerName) as $key => $personName) {
    if (!userExists($personName)) {
        addUser($personName);
    }
}
foreach (array($round1Score, $round2Score, $round3Score) as $key => $roundScore) {
    if (!is_numeric($roundScore) || ($roundScore < 0 || $roundScore > 10)) {
        $err = "Invalid Score: " . $roundScore . "; please enter a number between 0 and 10";
        echo $err;
        exit($err);
    }
}

// Inputs Good, Insert into Database
echo $shooterName . "-" . $scorerName . "-" . $round1Score . ":" . $round2Score . ":" . $round3Score;
echo submitScore($shooterName, $scorerName, $round1Score, $round2Score, $round3Score);

?>
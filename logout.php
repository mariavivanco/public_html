<?php
// SOURCE: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Initialize the session
session_start();

// save the state of the game into the database!!!
// Include config file
require_once "connectToDB.php";

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$puzzle = $_SESSION["puzzle"];
$guessedAnswers = $_SESSION["guessedWordList"];

// UPDATE PUZZLE
// Prepare an update statement
$sql = "UPDATE users SET puzzle = ? WHERE id = ?";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("si", $param_puzzle, $param_id);

    // Set parameters
    $param_puzzle = $puzzle;
    $param_id = $_SESSION["id"];

    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Puzzle updated successfully. Do nothing.
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    $stmt->close();
}

// UPDATE GUESSED ANSWERS
// Prepare an update statement
$sql = "UPDATE users SET guessedAnswers = ? WHERE id = ?";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("si", $param_guessedAnswers, $param_id);

    // Set parameters
    $param_guessedAnswers = serialize($guessedAnswers);
    $param_id = $_SESSION["id"];

    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Guessed answers updated successfully. Destroy the session, and redirect to landing page
        session_destroy();
        header("location: index.html");
        exit();
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    $stmt->close();
}


// Close connection
$mysqli->close();
?>

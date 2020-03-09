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

if(isset($_SESSION["jsonPuzzle"])) {
    $puzzle = $_SESSION["jsonPuzzle"];
}
else {
  echo "Oops! Something went wrong. Please try again later.";
}

if(isset($_SESSION["guessedWords"])) {
  $guessedWords = $_SESSION["guessedWords"];
}
else {
  $guessedWords = [];
}

// UPDATE PUZZLE
// Prepare an update statement
$sql = "UPDATE users SET currentPuzzle = ? WHERE id = ?";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("ss", $param_puzzle, $param_id);

    // Set parameters
    $param_puzzle = serialize($puzzle);
    $param_id = $_SESSION["id"];

    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Password updated successfully. Destroy the session, and redirect to login page
        session_destroy();
        header("location: login.php");
        exit();
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }



// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location: index.html");
exit;
?>

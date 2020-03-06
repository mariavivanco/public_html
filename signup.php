<?php
// SOURCE https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Connect to db
require_once "connectToDB.php";

// Define the username and error vars
$username = "";
$invalid_username = "";

// If a post request has been submitted by the form,
// check if the username is valid.
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if the username is valid.
    if(empty(trim($_POST["username"]))){
        $invalid_username = "Please enter a username.";
    } else{
        // Create the select statement that tries to get the username from the database.
        $sql = "SELECT id FROM users WHERE username = ?";

        // If the statement can be prepared for execution
        if($stmt = $mysqli->prepare($sql)){
            // Indicate that the username in the incomming statement is a string
            $stmt->bind_param("s", $param_username);

            // Remove whitespace from the left and right of the input!
            $param_username = trim($_POST["username"]);

            // Query the database with the statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();

                // if the number of rows in the result is 1, this username is already in the database
                if($stmt->num_rows == 1){
                    $invalid_username = "This username is already taken.";
                } else{
                    // the username is not already in the database! so remove whitespace from the username
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Something went wrong...";
            }

            // Close statement
            $stmt->close();
        }
    }

    // If the username is not empty and not already in the database, now actually add it to the database.
    if(empty($invalid_username)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username) VALUES (?)";

        if($stmt = $mysqli->prepare($sql)){
            // Indicate username is a string
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Execute the insert statement, i.e., add the username to the database.
            if($stmt->execute()){
                // If the username was successfully added, redirect to the next page.
                header("location: generatePuzzleJSON.php");
            } else{
                // The statement didn't successfully execute...
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection to the db
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create an Account</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="signup.css">
</head>

<body>
    <div class="wrapper">
        <h2>Create an account.</h2>
        <form action="" method="post">
            <div class="form-group <?php echo (!empty($invalid_username)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control">
                <span class="help-block"><?php echo $invalid_username; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</body>

</html>

<?php
// SOURCE https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Connect to db
require_once "connectToDB.php";

// Define the username and error vars
$username = "";
$password = "";
$invalid_username = "";
$invalid_password = "";
$invalid_confirm_password = "";

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

    // Check if the password is valid.
    // (not empty and more than 6 characters)
    if(empty(trim($_POST["password"]))){
        $invalid_password = "Please enter a password. Must be at least 6 characters.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $invalid_password = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    // Not empty, and matches the password.
    if(empty(trim($_POST["confirm_password"]))){
        $invalid_confirm_password = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($invalid_password) && ($password != $confirm_password)){
            $invalid_confirm_password = "Passwords do not match.";
        }
    }

    // If the username is not empty and not already in the database, now actually add it to the database.
    if(empty($invalid_username) && empty($invalid_password) && empty($invalid_confirm_password)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Indicate username is a string
            $stmt->bind_param("ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Execute the insert statement, i.e., add the username to the database.
            if($stmt->execute()){
                // If the username was successfully added, redirect to the next page.
                header("location: login.php");
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
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
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
            <div class="form-group <?php echo (!empty($invalid_password)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $invalid_password; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($invalid_confirm_password)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $invalid_confirm_password; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</body>

</html>

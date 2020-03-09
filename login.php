<?php
// SOURCE https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect her to game page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: generatePuzzleJSON.php");
    exit;
}

// Include config file
require_once "connectToDB.php";

// Define the username and error vars
$username = "";
$password = "";
$invalid_username = "";
$invalid_password = "";
$puzzle = [];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $invalid_username = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $invalid_password = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($invalid_username) && empty($invalid_password)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, puzzle, guessedAnswers FROM users WHERE username = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password, $puzzle, $guessedAnswers);

                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            // Need to store the id and username for log out
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["currentPuzzle"] = unserialize($puzzle);
                            $_SESSION["guessedAnswers"] = unserialize($guessedAnswers);

                            // Redirect user to game page
                            header("location: generatePuzzleJSON.php");
                        } else{
                            // Display an error message if password is not valid
                            $invalid_password = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $invalid_username = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <form action="" method="post">
            <div class="form-group <?php echo (!empty($invalid_username)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $invalid_username; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($invalid_password)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $invalid_password; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<?php
session_start();
include "config.php"; 
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);



if (isset($_POST['submit']) && $_POST['submit'] == 'Login') {
    $username = mysqli_real_escape_string($db_connection, $_POST['username']);
    $password = mysqli_real_escape_string($db_connection, $_POST['password']);

    // Check if the user exists
    $query = "SELECT * FROM customer WHERE email = '$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $user['email'];
            $_SESSION['customerID'] = $user['customerID']; // Store the customerID in the session

            header("Location: index.php"); // Redirect to the main page after login
            exit;
        } else {
            // Validation
            echo "<h2>Incorrect email or password.</h2>";
        }
    } else {
        // User does not exist
        echo "<h2>User not found.</h2>";
    }
}

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    // If user is logged in, redirect to the main page
    header("Location: index.php");
    exit;
}
?>

<h1>Customer Login</h1>
<form method="post" action="login.php">
    <label for="username">Username:</label>
    <input type="email" name="username" required><br>
    <label for="password">Password:</label>
    <input type="password" name="password" required><br>
    <input type="submit" name="submit" value="Login">
</form>

</body>
</html>
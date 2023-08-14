<!DOCTYPE html>
<html>
  <head>
    <title>BnB example system</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <h1>BIT608 Web Programming </h1>
    <h2>Assessment case study web application temporary launch page</h2>
    <ul>
      <li><a href="converted_template/">BnB Home</a>
      <li><a href="registercustomer.php">Register Customer</a>
      <li><a href="listcustomers.php">Customer listing</a>
      <li><a href="listrooms.php">Rooms listing</a>
      <li><a href="listbookings.php">Bookings listing</a>
      <li><a href="login.php">Login</a>
      <?php
session_start();

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
    // User is logged in
    echo "<h1>Welcome, " . $_SESSION['username'] . "</h1>";
    echo "<a href='logout.php'>Logout</a>"; // Add logout link
} 

?>
    </ul>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking</title>
</head>
<body>
<?php
include "config.php"; 
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check SQL connection
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; 
}

// Validation
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
    echo "<h2>Invalid Booking ID</h2>"; 
    exit;
}

// Query
$query = 'SELECT b.bookingID, b.checkInDate, b.checkOutDate, r.roomname, c.firstname, c.lastname FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          INNER JOIN customer c ON b.customerID = c.customerID
          WHERE b.bookingID=' . $id;

$result = mysqli_query($db_connection, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Booking Details View</h1>
<a href='listbookings.php'>Return to Booking Listing</a>
<a href='index.php'>Return to Main Page</a>

<?php

// Check Booking
if ($rowcount > 0) {
    echo '<div style="border: 1px solid black; padding: 10px">';
    echo "<h2>Room Detail #$id</h2>";
    $row = mysqli_fetch_assoc($result);
    echo "<p>Room Name: " . $row['roomname'] . "</p>";
    echo "<p>Check-in Date: " . $row['checkInDate'] . "</p>";
    echo "<p>Check-out Date: " . $row['checkOutDate'] . "</p>";
    echo "<p>Contact Number: " . $row['contactNumber'] . "</p>";
    echo "<p>Extras: " . $row['extras'] . "</p>";
    echo "<p>Room Review: " . $row['roomReview'] . "</p>";
    echo '</div>';
} else {
    echo "<h2>No Booking found</h2>"; 
}

mysqli_free_result($result);
mysqli_close($db_connection); 
?>
</body>
</html>
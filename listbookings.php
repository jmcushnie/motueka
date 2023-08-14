<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Bookings</title>
</head>
<body>

<?php
include "config.php";
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//Check connection
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; 
}

//Query
$query = 'SELECT b.bookingID, b.checkInDate, b.checkOutDate, r.roomname, c.firstname, c.lastname FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          INNER JOIN customer c ON b.customerID = c.customerID';
$result = mysqli_query($db_connection, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Current Bookings</h1>
    <a href="createbooking.php">Make a booking</a> |
    <a href="index.php">Return to Main Page</a> <br>
    <br>
    <table border="1">
        <tr>
            <th>Booking (Room, Dates)</th>
            <th>Customer</th>
            <th>Action</th>
        </tr>

        <?php

        // Check for bookings
        if ($rowcount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $bookingID = $row['bookingID'];
                $roomname = $row['roomname'];
                $checkInDate = $row['checkInDate'];
                $checkOutDate = $row['checkOutDate'];
                $customerName = $row['firstname'] . ' ' . $row['lastname'];
                echo '<tr>';
                echo '<td>' . $roomname . ', ' . $checkInDate . ', ' . $checkOutDate . '</td>';
                echo '<td>' . $customerName . '</td>';
                echo '<td>';
                echo '<a href="viewbooking.php?id=' . $bookingID . '">View</a> ';
                echo '<a href="editbooking.php?id=' . $bookingID . '">Edit</a> ';
                echo '<a href="' . $bookingID . '">Manage Reviews</a> ';
                echo '<a href="deletebooking.php?id=' . $bookingID . '">Delete</a> ';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">No bookings found!</td></tr>';
        }
        mysqli_free_result($result);
        mysqli_close($db_connection);
        ?>
    </table>
        
</body>
</html>
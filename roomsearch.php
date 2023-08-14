<?php
include "config.php"; 
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check connection
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = mysqli_real_escape_string($db_connection, $_GET['startDate']);
    $endDate = mysqli_real_escape_string($db_connection, $_GET['endDate']);

    // Find available rooms within date range
    $query = "SELECT * FROM room WHERE roomID NOT IN (SELECT roomID FROM booking WHERE checkin >= '$startDate' AND checkout <= '$endDate')";
    $result = mysqli_query($db_connection, $query);

    $availableRooms = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $availableRooms[] = $row;
    }

    
    echo json_encode($availableRooms);
}

mysqli_close($db_connection);
?>
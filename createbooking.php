<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Booking</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
    />
</head>
<body>

<?php
session_start();

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: login.php");
    exit;
}
//Get customer ID

$customerID = $_SESSION['customerID'];
include "config.php";
$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check connection
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; 
}

$error = 0; // Initialize error flag
$msg = ''; // Initialize error message


// Check if the form was submitted for creating the booking
if (isset($_POST['submit']) && !empty($_POST['submit']) && $_POST['submit'] == 'Add Booking') {
    // Validate data
    var_dump($_POST);
    if (isset($_POST['room']) && !empty($_POST['room']) && is_numeric($_POST['room'])) {
        $roomID = mysqli_real_escape_string($db_connection, $_POST['room']);
    } else {
        $error++; 
        $msg .= 'Invalid Room ID. '; 
        $roomID = 0;
    }

    // Validate contactNumber
if (isset($_POST['contactNumber']) && !empty($_POST['contactNumber'])) {
    $contactNumber = mysqli_real_escape_string($db_connection, $_POST['contactNumber']);
} else {
    $error++;
    $msg .= 'Contact Number is required. ';
}

    $checkInDate = date('Y-m-d', strtotime($_POST['checkInDate']));
    $checkOutDate = date('Y-m-d', strtotime($_POST['checkOutDate']));
    $bookingExtras = isset($_POST['bookingExtras']) ? mysqli_real_escape_string($db_connection, $_POST['bookingExtras']) : null;

    // Save the booking data if the error flag is still clear and room ID is greater than 0
    if ($error == 0 && $roomID > 0) {
        // Prepare and execute the SQL query to insert the booking data
        $query = "INSERT INTO booking (customerID, roomID, checkInDate, checkOutDate, contactNumber, bookingExtras) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db_connection, $query); 
        mysqli_stmt_bind_param($stmt, 'iissss', $customerID, $roomID, $checkInDate, $checkOutDate, $contactNumber, $bookingExtras);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking created successfully.</h2>";
    } else {
        echo "<h2>$msg</h2>";
    }
}

// Fetch rooms from the database
$roomQuery = "SELECT * FROM room";
$roomResult = mysqli_query($db_connection, $roomQuery);
?>


    <h1>Create Booking</h1>
    <a href="listbookings.php">Return to Booking Listing</a> |
    <a href="index.php">Return to Main Page</a> <br><br>
    <form method="post" action="createbooking.php">
    <label for="room">Room:</label>
        <select name="room" required>
            <option value="">Select a Room</option>
            <?php
            // Fetch rooms from the database and display them in the dropdown
            $query = "SELECT * FROM room";
            $result = mysqli_query($db_connection, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['roomID'] . '">' . $row['roomname'] . '</option>';
            }
            ?>
        </select>
        <br />
    <br>
    <label for="checkInDate">Check-in Date:</label>
      <input type="date" name="checkInDate" id="checkInDate" required />

      <br />

      <label for="checkOutDate">Check-out Date:</label>
      <input type="date" name="checkOutDate" id="checkOutDate" required />

      <br />

      <label for="contactNumber">Contact Number:</label>
      <input type="tel" name="contactNumber" pattern="[0-9]{3}[\s]?[0-9]{3}[\s]?[0-9]{4}" required />
      <br />

      <label for="bookingExtras">Booking Extras:</label>
      <textarea name="bookingExtras" rows="4" cols="50"></textarea>
      <br /> 
      
      <input type="submit" name="submit" value="Add Booking">
</form>


<?php
if (isset($_GET['search']) && $_GET['search'] == 'Search Availability') {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    // Fetch rooms
    $availabilityQuery = "SELECT r.roomID, r.roomname, r.roomtype, r.beds
                          FROM room r
                          WHERE r.roomID NOT IN (
                              SELECT b.roomID
                              FROM booking b
                              WHERE b.checkInDate <= '$endDate'
                                AND b.checkOutDate >= '$startDate'
                          )";
    $availabilityResult = mysqli_query($db_connection, $availabilityQuery);
}

?>
<h2>Search for Room Availability</h2>
<form id="availabilityForm" method="GET" action="">
    <label for="startDate">Start Date:</label>
    <input type="date" name="startDate" id="startDate" required />
    <label for="endDate">End Date:</label>
    <input type="date" name="endDate" id="endDate" required />
    <button type="submit" name="search" value="Search Availability">Search Availability</button>
</form>

<?php if (isset($availabilityResult) && mysqli_num_rows($availabilityResult) > 0) { ?>
    <table border="1">
        <tr>
            <th>Room #</th>
            <th>Room Name</th>
            <th>Room Type</th>
            <th>Beds</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($availabilityResult)) {
            echo '<tr>';
            echo '<td>' . $row['roomID'] . '</td>';
            echo '<td>' . $row['roomname'] . '</td>';
            echo '<td>' . $row['roomtype'] . '</td>';
            echo '<td>' . $row['beds'] . '</td>';
            echo '</tr>';
        }
        ?>
    </table>
<?php } elseif (isset($availabilityResult) && mysqli_num_rows($availabilityResult) == 0) { ?>
    <p>No available rooms for the selected dates</p>
<?php } ?>
<?php
mysqli_free_result($roomResult);
mysqli_close($db_connection);
?>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="/script.js"></script>

</body>
</html>
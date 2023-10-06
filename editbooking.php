<!DOCTYPE HTML>
<html>
<head>
  <title>Edit Booking</title>
</head>
<body>

<?php
include "config.php"; 
include "cleaninput.php";

$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
$error = 0;

// Check connection
if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error();
  exit; 
};

// Retrieve the bookingID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) || !is_numeric($id)) {
        echo "<h2>Invalid Booking ID</h2>"; 
        exit;
    }
}

// Check if the form was submitted for updating the booking
if (isset($_POST['submit']) && !empty($_POST['submit']) && $_POST['submit'] == 'Update') {
    // Validate incoming data
    // Type conversion for roomID
    if (isset($_POST['room']) && !empty($_POST['room']) && is_numeric($_POST['room'])) {
        $roomID = cleanInput($_POST['room']);
    } else {
        $error++; 
        $msg .= 'Invalid Room ID. '; 
        $roomID = 0;
    }

    // Check-in date
    $checkInDate = cleanInput($_POST['checkInDate']);

    // Check-out date
    $checkOutDate = cleanInput($_POST['checkOutDate']);

    // Contact number
    if (isset($_POST['contactNumber']) && !empty($_POST['contactNumber'])) {
        if (preg_match('/^[0-9]{10}$/', $_POST['contactNumber'])) {
            $contactNumber = cleanInput($_POST['contactNumber']);
        } else {
            $error++; 
            $msg .= 'Invalid Contact Number. ';
            $contactNumber = '';
        }
    } else {
        $error++; 
        $msg .= 'Contact Number is required. ';
        $contactNumber = '';
    }

    // Booking extras
    $bookingExtras = cleanInput($_POST['bookingExtras']);

    // Save the booking data if the error flag is still clear and booking ID is greater than 0
    if ($error == 0 && $id > 0) {
        $query = "UPDATE booking SET roomID=?, checkInDate=?, checkOutDate=?, contactNumber=?, bookingExtras=? WHERE bookingID=?";
        $stmt = mysqli_prepare($db_connection, $query); // Prepare the query
        mysqli_stmt_bind_param($stmt, 'issisi', $roomID, $checkInDate, $checkOutDate, $contactNumber, $bookingExtras, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking details updated.</h2>";
    } else {
        echo "<h2>$msg</h2>";
    }
}

// Find booking to edit by using the bookingID
$query = 'SELECT b.bookingID, b.checkInDate, b.checkOutDate, b.contactNumber, b.bookingExtras, r.roomID, r.roomname, r.roomtype, r.beds FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          WHERE b.bookingID=' . $id;
$result = mysqli_query($db_connection, $query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);
?>

<h1>Edit Booking</h1>
<a href="listbookings.php">Return to Booking Listing</a> |
<a href="index.php">Return to Main Page</a> <br><br>
<form method="post" action="editbooking.php?id=<?php echo $id; ?>">
    <label for="room">Room:</label>
    <select name="room" required>
        <option value="">Select a Room</option>
        <?php
        // Fetch rooms and display 
        $roomQuery = "SELECT * FROM room";
        $roomResult = mysqli_query($db_connection, $roomQuery);
        while ($roomRow = mysqli_fetch_assoc($roomResult)) {
            $selected = ($roomRow['roomID'] == $row['roomID']) ? 'selected' : '';
            echo '<option value="' . $roomRow['roomID'] . '" ' . $selected . '>' . $roomRow['roomname'] . ', ' . $roomRow['roomtype'] . ', ' . $roomRow['beds'] . '</option>';
        }
        ?>
    </select>
    <br>
    <label for="checkInDate">Check-in Date:</label>
    <input type="date" name="checkInDate" value="<?php echo $row['checkInDate']; ?>" required><br>

    <label for="checkOutDate">Check-out Date:</label>
    <input type="date" name="checkOutDate" value="<?php echo $row['checkOutDate']; ?>" required><br>

    <label for="contactNumber">Contact Number:</label>
    <input type="tel" name="contactNumber" pattern="[0-9]{3}[\s]?[0-9]{3}[\s]?[0-9]{4}" value="<?php echo $row['contactNumber']; ?>" required><br>

    <label for="bookingExtras">Booking Extras:</label>
    <textarea name="bookingExtras" rows="4" cols="50"><?php echo $row['bookingExtras']; ?></textarea><br>

    <label for="roomReview">Room Review:</label>
    <textarea name="roomReview" rows="4" cols="50"><?php echo $row['roomReview']; ?></textarea><br>

    <input type="submit" name="submit" value="Update">
</form>
<form method="post" action="deletebooking.php?id=<?php echo $id; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" name="submit" value="Delete Booking">
</form>
<?php
} else {
    echo "<h2>Booking not found with that ID</h2>";
}
mysqli_close($db_connection);
?>
</body>
</html>

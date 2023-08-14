<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Booking</title>
</head>
<body>
<?php
include "config.php";
include "cleaninput.php";

$db_connection = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) || !is_numeric($id)) {
        echo "<h2>Invalid Booking ID</h2>";
        exit;
    }
}

if (isset($_POST['submit']) && !empty($_POST['submit']) && $_POST['submit'] == 'Delete') {
    $error = 0;
    $msg = 'Error: ';

    if (isset($_POST['id']) && !empty($_POST['id']) && is_integer(intval($_POST['id']))) {
        $id = cleanInput($_POST['id']); 
    } else {
        $error++;
        $msg .= 'Invalid Booking ID ';
        $id = 0;  
    }

    if ($error == 0 && $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($db_connection, $query);
        
        if (!$stmt) {
            die("Error in preparing statement: " . mysqli_error($db_connection));
        }
        
        mysqli_stmt_bind_param($stmt, 'i', $id); 
        
        if (!mysqli_stmt_execute($stmt)) {
            die("Error in executing statement: " . mysqli_error($db_connection));
        }
        
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking deleted.</h2>";     
    } else { 
        echo "<h2>$msg</h2>".PHP_EOL;
    }
}

$query = 'SELECT b.*, r.roomname FROM booking b
          INNER JOIN room r ON b.roomID = r.roomID
          WHERE b.bookingID='.$id;
$result = mysqli_query($db_connection, $query);
$rowcount = mysqli_num_rows($result); 
?>

<h2><a href='listbookings.php'>[Return to the Booking listing]</a> | <a href='index.php'>[Return to the main page]</a></h2>

<?php

if ($rowcount > 0) {  
    echo "<fieldset><legend>Booking detail #$id</legend><dl>"; 
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
    echo "<dt>Check-in Date:</dt><dd>".$row['checkInDate']."</dd>".PHP_EOL;
    echo "<dt>Check-out Date:</dt><dd>".$row['checkOutDate']."</dd>".PHP_EOL;
    echo "<dt>Contact Number:</dt><dd>".$row['contactNumber']."</dd>".PHP_EOL;
    echo "<dt>Extras:</dt><dd>".$row['bookingExtras']."</dd>".PHP_EOL;
    echo "</dl></fieldset>".PHP_EOL;
    ?>
    <form method="POST" action="deletebooking.php">
    <h2>Are you sure you want to delete this booking?</h2>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="submit" name="submit" value="Delete">
    <a href="listbookings.php">[Cancel]</a>
    </form>
<?php    
} else {
    echo "<h2>No booking found</h2>";
}
mysqli_free_result($result);
mysqli_close($db_connection); 
?>
</body>
</html>

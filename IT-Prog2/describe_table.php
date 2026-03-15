<?php
include("connection.php");

$result = mysqli_query($con, "DESCRIBE borrowed_books");
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . ' - ' . $row['Type'] . '<br>';
}
?>
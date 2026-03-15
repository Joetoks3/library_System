<?php
include("connection.php");

$sql = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(100) NOT NULL,
    action VARCHAR(255) NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";

$result = mysqli_query($con, $sql);

if ($result) {
    echo "Activity Logs Table Created Successfully";
} else {
    echo "Error: " . mysqli_error($con);
}
?>
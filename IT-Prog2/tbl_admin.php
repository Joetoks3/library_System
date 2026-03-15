<?php
        include("connection.php");

        $sql = "CREATE TABLE admin (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB ";

        $result = mysqli_query($con,$sql);

        if ($result) {
            echo "Admin Table Created";
        } else {
            echo "Error: " . mysqli_error($con);
        }
?>
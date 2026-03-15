<?php
        include("connection.php");

        $sql = "CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            email_address VARCHAR(100),
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

        $result = mysqli_query($con,$sql);

        if ($result) {
                echo "Table Success";
            } else {
                echo "Table Error" . mysqli_error($con);
            }

?>
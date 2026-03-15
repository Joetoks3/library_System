<?php
        include("connection.php");

        $sql = "CREATE TABLE books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                genre VARCHAR(100),
                description TEXT,
                year INT,
                category VARCHAR(100),
                quantity INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB ";

        $result = mysqli_query($con,$sql);

        if ($result) {
            echo "Admin Table Created";
        } else {
            echo "Error: " . mysqli_error($con);
        }
?>
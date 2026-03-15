<?php
        include("connection.php");

        $sql = "CREATE TABLE IF NOT EXISTS borrowed_books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                book_id INT NOT NULL,
                borrow_date DATE NOT NULL,
                due_date DATE NOT NULL,
                return_date DATE,
                status VARCHAR(20) NOT NULL DEFAULT 'borrowed',
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB";

        $result = mysqli_query($con,$sql);

        if ($result) {
            echo "Borrowed Books Table Created";
        } else {
            echo "Error: " . mysqli_error($con);
        }
?>
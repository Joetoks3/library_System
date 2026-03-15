<?php
        include("connection.php");

        $sql = "CREATE DATABASE db_library_system";
        $result = mysqli_query($con, $sql);

        if ($result) {
            echo "Database Success";
        } else {
            echo "Database Error" . mysqli_error($con);
        }


?>
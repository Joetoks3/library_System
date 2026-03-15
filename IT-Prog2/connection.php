<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_library_system";

    $con = mysqli_connect($servername, $username, $password,$dbname);

    if($con) {
        // echo "Connection Success";
    } else {
        echo "Connection Error" . mysqli_error($con);
    }

?>
<?php
session_start();
include("connection.php");

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from users table
    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if(password_verify($password, $user['password'])){
            // Check role
            if($user['role'] == 'admin'){
                $_SESSION['admin'] = $user['id'];
                header("Location: adminDashboard.php");
                exit();
            } else {
                $_SESSION['user'] = $user['id'];
                header("Location: userDashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid Username or Password'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Username or Password'); window.location='login.php';</script>";
    }
}
?>

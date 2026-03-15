<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($con, $sql)) {
        header("Location: adminUsers.php?message=User deleted successfully");
    } else {
        header("Location: adminUsers.php?message=Error deleting user: " . mysqli_error($con));
    }
} else {
    header("Location: adminUsers.php?message=Invalid request");
}
?>
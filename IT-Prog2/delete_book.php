<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM books WHERE id = $id";
    if (mysqli_query($con, $sql)) {
        header("Location: adminBooks.php?message=Book deleted successfully");
    } else {
        header("Location: adminBooks.php?message=Error deleting book: " . mysqli_error($con));
    }
} else {
    header("Location: adminBooks.php?message=Invalid request");
}
?>
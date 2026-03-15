<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

if (!isset($_GET['book_id'])) {
    echo json_encode(['error' => 'Book ID not provided']);
    exit();
}

$user_id = $_SESSION['user'];
$book_id = $_GET['book_id'];

// Check if user already borrowed this book and hasn't returned it, or has a pending request
$query = "SELECT status FROM borrowed_books WHERE user_id = '$user_id' AND book_id = '$book_id' AND status IN ('borrowed', 'pending') ORDER BY id DESC LIMIT 1";
$result = mysqli_query($con, $query);

$status = null;
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $status = $row['status'];
}

echo json_encode(['status' => $status]);
?>
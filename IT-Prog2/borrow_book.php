<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if (!isset($_POST['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Book ID not provided']);
    exit();
}

$user_id = $_SESSION['user'];
$book_id = $_POST['book_id'];

// Check if book is available
$book_query = "SELECT * FROM books WHERE id = '$book_id'";
$book_result = mysqli_query($con, $book_query);
$book = mysqli_fetch_assoc($book_result);

if (!$book) {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
    exit();
}

if ($book['quantity'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Book is out of stock']);
    exit();
}

// Check if user already borrowed this book
$borrow_check = "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND book_id = '$book_id' AND status = 'Borrowed'";
$borrow_result = mysqli_query($con, $borrow_check);

if (mysqli_num_rows($borrow_result) > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already borrowed this book']);
    exit();
}

// Calculate due date (14 days from now)
$borrow_date = date('Y-m-d');
$due_date = date('Y-m-d', strtotime('+14 days'));

// Insert borrow record with pending status
$insert_query = "INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) VALUES ('$user_id', '$book_id', '$borrow_date', '$due_date', 'pending')";

if (mysqli_query($con, $insert_query)) {
    // Note: Quantity is NOT decremented here - it will be decremented when admin approves
    echo json_encode(['success' => true, 'message' => 'Book borrow request submitted successfully. Waiting for admin approval.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit borrow request. Please try again.']);
}
?>
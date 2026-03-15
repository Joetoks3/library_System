<?php
include("connection.php");

$result = mysqli_query($con, "SELECT bb.id, u.firstname, u.lastname, b.title, bb.borrow_date, bb.due_date, bb.status FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id ORDER BY bb.borrow_date DESC LIMIT 1");
if($result) {
    echo 'Query OK';
} else {
    echo mysqli_error($con);
}
?>
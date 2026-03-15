<?php
// Issue Book functionality
if (isset($_POST['issue_book'])) {
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $book_id = mysqli_real_escape_string($con, $_POST['book_id']);
    $borrow_date = mysqli_real_escape_string($con, $_POST['borrow_date']);
    $due_date = mysqli_real_escape_string($con, $_POST['due_date']);

    // Check if book is available
    $book_q = mysqli_query($con, "SELECT quantity FROM books WHERE id = '$book_id'");
    $book = mysqli_fetch_assoc($book_q);
    if ($book['quantity'] > 0) {
        $sql = "INSERT INTO borrowed_books (user_id, book_id, borrow_date, due_date, status) VALUES ('$user_id', '$book_id', '$borrow_date', '$due_date', 'borrowed')";
        if (mysqli_query($con, $sql)) {
            // Decrement quantity
            mysqli_query($con, "UPDATE books SET quantity = quantity - 1 WHERE id = '$book_id'");
            echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Book issued successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error: " . mysqli_error($con) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle'></i> Book not available.</div>";
    }
}

// Handle find user for issue
$selected_issue_user_id = null;
$issue_user_name = '';
if (isset($_POST['find_issue_user'])) {
    $issue_username = mysqli_real_escape_string($con, $_POST['issue_username']);
    $user_q = mysqli_query($con, "SELECT id, firstname, lastname FROM users WHERE username = '$issue_username'");
    if ($user = mysqli_fetch_assoc($user_q)) {
        $selected_issue_user_id = $user['id'];
        $issue_user_name = $user['firstname'] . ' ' . $user['lastname'];
    } else {
        echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> User not found.</div>";
    }
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <form method="POST" class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Issue New Book</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Enter Username</label>
                        <input type="text" name="issue_username" class="form-control" value="<?php echo isset($_POST['issue_username']) ? $_POST['issue_username'] : ''; ?>" placeholder="Enter username to find user" required>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" name="find_issue_user" class="btn btn-info w-100">
                            <i class="bi bi-search"></i> Find User
                        </button>
                    </div>

                    <?php if (isset($selected_issue_user_id) && $selected_issue_user_id) { ?>
                    <div class="col-12 mb-3">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            <strong>Selected User:</strong> <?php echo htmlspecialchars($issue_user_name); ?>
                        </div>
                        <input type="hidden" name="user_id" value="<?php echo $selected_issue_user_id; ?>">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Select Book</label>
                        <select name="book_id" class="form-select" required>
                            <option value="">Choose a book to issue...</option>
                            <?php
                            $books_q = mysqli_query($con, "SELECT id, title, author, quantity FROM books WHERE quantity > 0 ORDER BY title");
                            while ($b = mysqli_fetch_assoc($books_q)) {
                                echo "<option value='{$b['id']}'>{$b['title']} by {$b['author']} ({$b['quantity']} available)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="borrow_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" name="issue_book" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-plus-circle"></i> Issue Book
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>
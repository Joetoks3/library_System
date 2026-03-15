<?php
// Return Book functionality
if (isset($_POST['return_book'])) {
    $borrow_id = mysqli_real_escape_string($con, $_POST['borrow_id']);
    $return_date = mysqli_real_escape_string($con, $_POST['return_date']);

    $sql = "UPDATE borrowed_books SET return_date = '$return_date', status = 'returned' WHERE id = '$borrow_id'";
    if (mysqli_query($con, $sql)) {
        // Get book_id to increment quantity
        $borrow_q = mysqli_query($con, "SELECT book_id FROM borrowed_books WHERE id = '$borrow_id'");
        $borrow = mysqli_fetch_assoc($borrow_q);
        mysqli_query($con, "UPDATE books SET quantity = quantity + 1 WHERE id = '{$borrow['book_id']}'");
        echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Book returned successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error: " . mysqli_error($con) . "</div>";
    }
}

// Handle find user for return
$selected_user_id = null;
$user_name = '';
if (isset($_POST['find_user'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $user_q = mysqli_query($con, "SELECT id, firstname, lastname FROM users WHERE username = '$username'");
    if ($user = mysqli_fetch_assoc($user_q)) {
        $selected_user_id = $user['id'];
        $user_name = $user['firstname'] . ' ' . $user['lastname'];
    } else {
        echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> User not found.</div>";
    }
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <form method="POST" class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-arrow-left-circle"></i> Return Book</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Enter Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" placeholder="Enter username to find user" required>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" name="find_user" class="btn btn-info w-100">
                            <i class="bi bi-search"></i> Find User
                        </button>
                    </div>

                    <?php if (isset($selected_user_id) && $selected_user_id) { ?>
                    <div class="col-12 mb-3">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            <strong>Selected User:</strong> <?php echo htmlspecialchars($user_name); ?>
                        </div>
                        <input type="hidden" name="return_user_id" value="<?php echo $selected_user_id; ?>">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Select Borrowed Book</label>
                        <select name="borrow_id" class="form-select" required>
                            <option value="">Choose a book to return...</option>
                            <?php
                            $borrowed_q = mysqli_query($con, "SELECT bb.id, b.title, bb.due_date FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.user_id = '$selected_user_id' AND bb.status = 'borrowed' ORDER BY bb.due_date");
                            while ($br = mysqli_fetch_assoc($borrowed_q)) {
                                $due_status = strtotime($br['due_date']) < time() ? ' (OVERDUE)' : '';
                                echo "<option value='{$br['id']}'>{$br['title']} - Due: {$br['due_date']}{$due_status}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Return Date</label>
                        <input type="date" name="return_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" name="return_book" class="btn btn-warning btn-lg w-100">
                            <i class="bi bi-arrow-left-circle"></i> Return Book
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>
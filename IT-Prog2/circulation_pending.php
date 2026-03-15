<?php
// Handle approve/reject actions
if (isset($_POST['approve_request'])) {
    $borrow_id = mysqli_real_escape_string($con, $_POST['borrow_id']);

    // Get book details
    $borrow_q = mysqli_query($con, "SELECT bb.*, b.quantity FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.id = '$borrow_id' AND bb.status = 'pending'");
    if ($borrow = mysqli_fetch_assoc($borrow_q)) {
        if ($borrow['quantity'] > 0) {
            // Update status to borrowed and decrement quantity
            mysqli_query($con, "UPDATE borrowed_books SET status = 'borrowed' WHERE id = '$borrow_id'");
            mysqli_query($con, "UPDATE books SET quantity = quantity - 1 WHERE id = '{$borrow['book_id']}'");

            // Log activity
            mysqli_query($con, "INSERT INTO activity_logs (user_id, action, details, timestamp) VALUES ('{$borrow['user_id']}', 'Book Approved', 'Borrow request approved for book ID: {$borrow['book_id']}', NOW())");

            echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Borrow request approved successfully!</div>";
        } else {
            echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle'></i> Cannot approve: Book is no longer available.</div>";
        }
    }
}

if (isset($_POST['reject_request'])) {
    $borrow_id = mysqli_real_escape_string($con, $_POST['borrow_id']);

    // Update status to rejected
    mysqli_query($con, "UPDATE borrowed_books SET status = 'rejected' WHERE id = '$borrow_id'");

    // Get borrow details for logging
    $borrow_q = mysqli_query($con, "SELECT * FROM borrowed_books WHERE id = '$borrow_id'");
    if ($borrow = mysqli_fetch_assoc($borrow_q)) {
        // Log activity
        mysqli_query($con, "INSERT INTO activity_logs (user_id, action, details, timestamp) VALUES ('{$borrow['user_id']}', 'Book Rejected', 'Borrow request rejected for book ID: {$borrow['book_id']}', NOW())");
    }

    echo "<div class='alert alert-info'><i class='bi bi-x-circle'></i> Borrow request rejected.</div>";
}

// Fetch pending requests
$pending_requests = mysqli_query($con, "SELECT bb.id, bb.borrow_date, bb.due_date, u.firstname, u.lastname, u.username, b.title, b.author, b.quantity FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id WHERE bb.status = 'pending' ORDER BY bb.borrow_date DESC");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-hourglass"></i> Pending Borrow Requests</h5>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($pending_requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Request Date</th>
                                    <th>Due Date</th>
                                    <th>Available</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($request = mysqli_fetch_assoc($pending_requests)): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($request['username']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                                        <td><?php echo htmlspecialchars($request['author']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($request['borrow_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($request['due_date'])); ?></td>
                                        <td>
                                            <?php if ($request['quantity'] > 0): ?>
                                                <span class="badge bg-success"><?php echo $request['quantity']; ?> available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="borrow_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" name="approve_request" class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to approve this borrow request?')"
                                                            <?php echo $request['quantity'] <= 0 ? 'disabled' : ''; ?>>
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="borrow_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" name="reject_request" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to reject this borrow request?')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-hourglass text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No Pending Requests</h4>
                        <p class="text-muted">All borrow requests have been processed.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
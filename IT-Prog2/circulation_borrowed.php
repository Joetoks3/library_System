<?php
// Fetch all borrowed books with all statuses
$borrowed_books = mysqli_query($con, "SELECT bb.id, u.firstname, u.lastname, u.username, b.title, bb.borrow_date, bb.due_date, bb.return_date, bb.status, DATEDIFF(bb.due_date, CURDATE()) as days_left FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id ORDER BY bb.borrow_date DESC");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Circulation Records</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchBorrowed" class="form-control" placeholder="Search borrowed books...">
                </div>

                <?php if(mysqli_num_rows($borrowed_books) > 0): ?>
                <div class="table-responsive">
                    <table id="borrowedTable" class="table table-bordered table-striped">
                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($book = mysqli_fetch_assoc($borrowed_books)): ?>
                            <tr class="<?php
                                if ($book['status'] == 'borrowed') {
                                    echo $book['days_left'] < 0 ? 'table-danger' : ($book['days_left'] <= 3 ? 'table-warning' : '');
                                } elseif ($book['status'] == 'pending') {
                                    echo 'table-info';
                                } elseif ($book['status'] == 'rejected') {
                                    echo 'table-secondary';
                                } elseif ($book['status'] == 'returned') {
                                    echo 'table-success';
                                }
                            ?>">
                                <td><?php echo $book['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($book['firstname'] . ' ' . $book['lastname']); ?></strong><br>
                                    <small class="text-muted">@<?php echo htmlspecialchars($book['username']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo $book['borrow_date']; ?></td>
                                <td><?php echo $book['due_date']; ?></td>
                                <td><?php echo $book['return_date'] ? $book['return_date'] : '-'; ?></td>
                                <td>
                                    <?php
                                    switch($book['status']) {
                                        case 'pending':
                                            echo '<span class="badge bg-info">Pending</span>';
                                            break;
                                        case 'borrowed':
                                            if($book['days_left'] < 0) {
                                                echo '<span class="badge bg-danger">Overdue</span>';
                                            } elseif($book['days_left'] <= 3) {
                                                echo '<span class="badge bg-warning">Due Soon</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Active</span>';
                                            }
                                            break;
                                        case 'returned':
                                            echo '<span class="badge bg-secondary">Returned</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="badge bg-dark">Rejected</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-light text-dark">' . ucfirst($book['status']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if($book['status'] == 'borrowed'): ?>
                                        <button class="btn btn-success btn-sm" onclick="confirmReturn(<?php echo $book['id']; ?>)">
                                            <i class="bi bi-arrow-left-circle"></i> Return
                                        </button>
                                    <?php elseif($book['status'] == 'pending'): ?>
                                        <span class="text-muted">Waiting for approval</span>
                                    <?php elseif($book['status'] == 'rejected'): ?>
                                        <span class="text-muted">Request rejected</span>
                                    <?php elseif($book['status'] == 'returned'): ?>
                                        <span class="text-muted">Already returned</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No circulation records found.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality for borrowed books
document.getElementById('searchBorrowed').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#borrowedTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
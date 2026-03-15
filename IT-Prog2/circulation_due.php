<?php
// Fetch books due soon (within 3 days)
$due_soon_books = mysqli_query($con, "SELECT bb.id, u.firstname, u.lastname, u.username, b.title, bb.borrow_date, bb.due_date, DATEDIFF(bb.due_date, CURDATE()) as days_left FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id WHERE bb.status = 'borrowed' AND bb.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) ORDER BY bb.due_date ASC");

// Fetch overdue books
$overdue_books = mysqli_query($con, "SELECT bb.id, u.firstname, u.lastname, u.username, b.title, bb.borrow_date, bb.due_date, DATEDIFF(CURDATE(), bb.due_date) as days_overdue FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id WHERE bb.status = 'borrowed' AND bb.due_date < CURDATE() ORDER BY bb.due_date ASC");
?>

<div class="row">
    <div class="col-12">
        <!-- Overdue Books Alert -->
        <?php if(mysqli_num_rows($overdue_books) > 0): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong><?php echo mysqli_num_rows($overdue_books); ?> books are currently overdue!</strong>
            Please contact users immediately to return these books.
        </div>
        <?php endif; ?>

        <!-- Due Soon Alert -->
        <?php if(mysqli_num_rows($due_soon_books) > 0): ?>
        <div class="alert alert-warning mb-3">
            <i class="bi bi-clock-history"></i>
            <strong><?php echo mysqli_num_rows($due_soon_books); ?> books are due within 3 days.</strong>
            Consider sending reminders to these users.
        </div>
        <?php endif; ?>

        <!-- Overdue Books Section -->
        <?php if(mysqli_num_rows($overdue_books) > 0): ?>
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Overdue Books</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-danger">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($overdue = mysqli_fetch_assoc($overdue_books)): ?>
                            <tr class="table-danger">
                                <td><?php echo $overdue['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($overdue['firstname'] . ' ' . $overdue['lastname']); ?></strong><br>
                                    <small class="text-muted">@<?php echo htmlspecialchars($overdue['username']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($overdue['title']); ?></td>
                                <td><?php echo $overdue['borrow_date']; ?></td>
                                <td><?php echo $overdue['due_date']; ?></td>
                                <td>
                                    <span class="badge bg-danger fs-6">
                                        <?php echo $overdue['days_overdue']; ?> day<?php echo $overdue['days_overdue'] != 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="confirmReturn(<?php echo $overdue['id']; ?>)">
                                        <i class="bi bi-arrow-left-circle"></i> Return
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Due Soon Books Section -->
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Books Due Soon (Next 3 Days)</h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($due_soon_books) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Book</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Days Left</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($due_book = mysqli_fetch_assoc($due_soon_books)): ?>
                            <tr class="<?php echo $due_book['days_left'] <= 1 ? 'table-danger' : 'table-warning'; ?>">
                                <td><?php echo $due_book['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($due_book['firstname'] . ' ' . $due_book['lastname']); ?></strong><br>
                                    <small class="text-muted">@<?php echo htmlspecialchars($due_book['username']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($due_book['title']); ?></td>
                                <td><?php echo $due_book['borrow_date']; ?></td>
                                <td><?php echo $due_book['due_date']; ?></td>
                                <td>
                                    <span class="badge <?php echo $due_book['days_left'] <= 1 ? 'bg-danger' : 'bg-warning'; ?>">
                                        <?php echo $due_book['days_left']; ?> day<?php echo $due_book['days_left'] != 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="confirmReturn(<?php echo $due_book['id']; ?>)">
                                        <i class="bi bi-arrow-left-circle"></i> Return
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> No books are due in the next 3 days.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
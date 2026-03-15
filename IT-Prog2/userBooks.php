<?php
session_start();
include("connection.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user'];
$user_query = mysqli_query($con, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Get all books from database
$query = "SELECT * FROM books ORDER BY title ASC";
$books = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Library Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            font-family: 'Times New Roman', sans-serif;
            overflow-x: hidden;
            background: #f4f6f9;
        }

        .wrapper {
            display: flex;
        }

        /* SIDEBAR */
        #sidebar {
            width: 250px;
            min-height: 100vh;
            background: #dc3545;
            color: white;
            padding: 20px;
            transition: all .35s ease;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        #sidebar.collapsed {
            width: 70px;
            padding: 20px 10px;
            align-items: center;
        }

        .toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            color: #dc3545;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            font-size: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all .3s;
        }

        .toggle-btn:hover {
            transform: rotate(90deg);
        }

        #sidebar h3 {
            margin-top: 70px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #sidebar h3 i {
            margin-right: 10px;
        }

        #sidebar.collapsed h3 span {
            display: none;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        #sidebar ul li {
            margin-bottom: 15px;
        }

        #sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 6px;
            transition: .3s;
        }

        #sidebar ul li a i {
            min-width: 25px;
            text-align: center;
            font-size: 18px;
        }

        #sidebar ul li a span {
            flex: 1;
            margin-left: 10px;
            white-space: nowrap;
        }

        #sidebar.collapsed ul li span {
            display: none;
        }

        /* SUB MENU */
        .menu-header {
            color: white;
            font-weight: 600;
            padding: 10px;
            display: flex;
            align-items: center;
            border-radius: 6px;
            transition: .3s;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .menu-header:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .menu-header i {
            min-width: 25px;
            text-align: center;
            font-size: 18px;
            margin-right: 10px;
        }

        .menu-header span {
            flex: 1;
            margin-left: 10px;
            white-space: nowrap;
        }

        .toggle-icon {
            transition: transform 0.3s;
            margin-left: auto;
        }

        .menu-header.expanded .toggle-icon {
            transform: rotate(180deg);
        }

        .sub-menu {
            list-style: none;
            padding-left: 20px;
            margin: 0;
            display: none;
        }

        .sub-menu li {
            margin-bottom: 10px;
        }

        .sub-menu li a {
            padding: 8px 10px;
            font-size: 14px;
        }

        #sidebar.collapsed .menu-header {
            display: none;
        }

        #sidebar.collapsed .sub-menu {
            display: none;
        }

        /* CONTENT */
        #content {
            flex: 1;
            margin-left: 25px;
            padding: 40px;
            transition: all .35s ease;
        }

        #content.sidebar-collapsed {
            margin-left: 70px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: #dc3554;
            color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: hidden;
        }

        .content-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .content-header h2 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            z-index: 1;
            position: relative;
        }

        .content-header p {
            margin: 5px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
            z-index: 1;
            position: relative;
        }

        .content-header .header-icon {
            font-size: 3rem;
            opacity: 0.8;
            margin-right: 15px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            50% { transform: translate(-50%, -50%) rotate(180deg); }
        }

        /* SEARCH BAR */
        .search-container {
            position: relative;
            z-index: 2;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            padding: 12px 45px 12px 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            font-size: 14px;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.8);
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }

        .search-bar input::placeholder {
            color: #666;
        }

        .search-bar i {
            position: absolute;
            right: 15px;
            color: #666;
            font-size: 16px;
            pointer-events: none;
        }

        /* BOOK CARDS */
        .book-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: .3s;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-cover {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .book-cover i {
            font-size: 4rem;
        }

        /* BOOK MODAL */
        .book-cover-modal {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .book-cover-modal i {
            font-size: 5rem;
        }

        /* ALERTS */
        .alert {
            border-radius: 10px;
            border: none;
        }

        @media (max-width: 991px) {
            #sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                z-index: 1000;
                height: 100vh;
            }
            
            #sidebar.active {
                left: 0;
            }
            
            #content {
                margin-left: 0;
                padding: 40px 20px;
            }

            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .search-bar input {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <h3>
            <i class="bi bi-book"></i>
            <span>Library Manager</span>
        </h3>

        <ul>
            <li>
                <a href="userDashboard.php">
                    <i class="bi bi-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="userBooks.php" class="active">
                    <i class="bi bi-book"></i>
                    <span>Browse Books</span>
                </a>
            </li>
            <li>
                <button class="menu-header" onclick="toggleSubMenu()">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </button>
                <ul class="sub-menu" id="profile-submenu">
                    <li><a href="userMyBooks.php"><i class="bi bi-book-half"></i> <span>My Books</span></a></li>
                    <li><a href="userProfile.php"><i class="bi bi-person-circle"></i> <span>Account Info</span></a></li>
                </ul>
            </li>
            <li>
                <a href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- CONTENT -->
    <div id="content">
        <!-- HEADER -->
        <div class="content-header">
            <div>
                <h2><i class="bi bi-book header-icon"></i>Book Catalog</h2>
                <p>Browse our complete collection of books</p>
            </div>
            <div class="search-container">
                <div class="search-bar">
                    <input type="text" id="bookSearch" placeholder="Search books by title, author, or genre...">
                    <i class="bi bi-search"></i>
                </div>
            </div>
        </div>

        <!-- BOOKS GRID -->
        <div class="row">
            <?php if(mysqli_num_rows($books) > 0): ?>
                <?php while($book = mysqli_fetch_assoc($books)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card book-card">
                        <div class="book-cover">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($book['title']); ?></h6>
                            <p class="card-text text-muted small mb-1">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <?php if (!empty($book['genre'])): ?>
                            <p class="card-text text-muted small mb-2">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($book['genre']); ?>
                            </p>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-boxes me-1"></i><?php echo $book['quantity']; ?> available
                                    </small>
                                </div>
                                <button class="btn btn-primary w-100 view-book-btn" 
                                        data-book-id="<?php echo $book['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($book['title']); ?>"
                                        data-author="<?php echo htmlspecialchars($book['author']); ?>"
                                        data-genre="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>"
                                        data-description="<?php echo htmlspecialchars($book['description'] ?? 'No description available.'); ?>"
                                        data-quantity="<?php echo $book['quantity']; ?>">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No books in catalog</h4>
                        <p class="text-muted">Books will appear here once added to the system</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- BOOK DETAILS MODAL -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookModalLabel">Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="book-cover-modal">
                            <i class="bi bi-book-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 id="modal-book-title"></h4>
                        <p class="text-muted mb-2">
                            <i class="bi bi-person me-1"></i><span id="modal-book-author"></span>
                        </p>
                        <p class="text-muted mb-3">
                            <i class="bi bi-tag me-1"></i><span id="modal-book-genre"></span>
                        </p>
                        <div class="mb-3">
                            <h6>Description:</h6>
                            <p id="modal-book-description" class="text-muted"></p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-success" id="modal-book-quantity"></span>
                            <div id="borrow-status"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="borrow-btn" style="display: none;">
                    <i class="bi bi-book-half me-1"></i>Borrow Book
                </button>
            </div>
        </div>
    </div>
</div>

<!-- BORROW CONFIRMATION MODAL -->
<div class="modal fade" id="borrowConfirmModal" tabindex="-1" aria-labelledby="borrowConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="borrowConfirmModalLabel">Confirm Borrow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit a borrow request for "<span id="confirm-book-title"></span>"?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Note:</strong> Your request will be sent to the admin for approval. You will be notified once it's approved.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-borrow-btn">
                    <i class="bi bi-send me-1"></i>Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("content");
        if (window.innerWidth < 992) {
            sidebar.classList.toggle("active");
        } else {
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("sidebar-collapsed");
        }
    }

    function toggleSubMenu() {
        const sub = document.getElementById('profile-submenu');
        sub.style.display = sub.style.display === 'none' || sub.style.display === '' ? 'block' : 'none';
    }

    // Search functionality
    document.getElementById('bookSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const bookCards = document.querySelectorAll('.book-card');

        bookCards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const author = card.querySelector('.card-text').textContent.toLowerCase();
            const genre = card.querySelectorAll('.card-text')[1] ? card.querySelectorAll('.card-text')[1].textContent.toLowerCase() : '';

            if (title.includes(searchTerm) || author.includes(searchTerm) || genre.includes(searchTerm)) {
                card.closest('.col-lg-3, .col-md-4, .col-sm-6').style.display = 'block';
            } else {
                card.closest('.col-lg-3, .col-md-4, .col-sm-6').style.display = 'none';
            }
        });

        // Show "no results" message if no books match
        const visibleCards = Array.from(bookCards).filter(card =>
            card.closest('.col-lg-3, .col-md-4, .col-sm-6').style.display !== 'none'
        );

        const noResultsDiv = document.querySelector('.col-12 .text-center');
        if (visibleCards.length === 0 && searchTerm !== '') {
            if (!document.getElementById('no-search-results')) {
                const noResults = document.createElement('div');
                noResults.id = 'no-search-results';
                noResults.className = 'col-12';
                noResults.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No books found</h4>
                        <p class="text-muted">Try searching with different keywords</p>
                    </div>
                `;
                document.querySelector('.row').appendChild(noResults);
            }
            noResultsDiv.style.display = 'none';
        } else {
            const noResultsElement = document.getElementById('no-search-results');
            if (noResultsElement) {
                noResultsElement.remove();
            }
            if (document.querySelectorAll('.book-card').length === 0) {
                noResultsDiv.style.display = 'block';
            }
        }
    });

    // Book modal functionality
    let currentBookId = null;

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-book-btn') || e.target.closest('.view-book-btn')) {
            const btn = e.target.classList.contains('view-book-btn') ? e.target : e.target.closest('.view-book-btn');
            
            // Populate modal with book data
            document.getElementById('modal-book-title').textContent = btn.dataset.title;
            document.getElementById('modal-book-author').textContent = btn.dataset.author;
            document.getElementById('modal-book-genre').textContent = btn.dataset.genre || 'Not specified';
            document.getElementById('modal-book-description').textContent = btn.dataset.description;
            document.getElementById('modal-book-quantity').textContent = btn.dataset.quantity + ' available';
            
            currentBookId = btn.dataset.bookId;
            
            // Check if user can borrow this book
            checkBorrowStatus(btn.dataset.bookId, btn.dataset.quantity);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bookModal'));
            modal.show();
        }
    });

    function checkBorrowStatus(bookId, quantity) {
        // Check if user already borrowed this book
        fetch('check_borrow_status.php?book_id=' + bookId)
            .then(response => response.json())
            .then(data => {
                const borrowBtn = document.getElementById('borrow-btn');
                const borrowStatus = document.getElementById('borrow-status');
                
                if (data.status === 'borrowed') {
                    borrowBtn.style.display = 'none';
                    borrowStatus.innerHTML = '<span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Already Borrowed</span>';
                } else if (data.status === 'pending') {
                    borrowBtn.style.display = 'none';
                    borrowStatus.innerHTML = '<span class="badge bg-info"><i class="bi bi-hourglass me-1"></i>Pending Approval</span>';
                } else if (quantity > 0) {
                    borrowBtn.style.display = 'inline-block';
                    borrowStatus.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Available</span>';
                } else {
                    borrowBtn.style.display = 'none';
                    borrowStatus.innerHTML = '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>';
                }
            })
            .catch(error => {
                console.error('Error checking borrow status:', error);
                document.getElementById('borrow-btn').style.display = 'none';
                document.getElementById('borrow-status').innerHTML = '<span class="badge bg-secondary">Status Unknown</span>';
            });
    }

    // Handle borrow button click
    document.getElementById('borrow-btn').addEventListener('click', function() {
        const bookTitle = document.getElementById('modal-book-title').textContent;
        document.getElementById('confirm-book-title').textContent = bookTitle;
        
        // Close book modal and open confirm modal
        const bookModal = bootstrap.Modal.getInstance(document.getElementById('bookModal'));
        bookModal.hide();
        
        const confirmModal = new bootstrap.Modal(document.getElementById('borrowConfirmModal'));
        confirmModal.show();
    });

    // Handle confirm borrow
    document.getElementById('confirm-borrow-btn').addEventListener('click', function() {
        if (!currentBookId) return;
        
        // Submit borrow request
        const formData = new FormData();
        formData.append('book_id', currentBookId);
        
        fetch('borrow_book.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and show success message
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('borrowConfirmModal'));
                confirmModal.hide();
                
                // Show success alert
                showAlert('Borrow request submitted successfully! Waiting for admin approval.', 'success');
                
                // Refresh page after short delay
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert(data.message || 'Failed to borrow book. Please try again.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error borrowing book:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

</body>
</html>
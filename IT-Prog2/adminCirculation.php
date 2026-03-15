<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch books due soon (within 3 days)
$due_soon_books = mysqli_query($con, "SELECT bb.id, u.firstname, u.lastname, b.title, bb.borrow_date, bb.due_date, DATEDIFF(bb.due_date, CURDATE()) as days_left FROM borrowed_books bb JOIN users u ON bb.user_id = u.id JOIN books b ON bb.book_id = b.id WHERE bb.status = 'borrowed' AND bb.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) ORDER BY bb.due_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Circulation</title>
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

        #sidebar.active {
            position: absolute;
            z-index: 999;
            left: 0;
            top: 0;
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
            transition: all 0.3s ease;
            position: relative;
        }

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        #sidebar ul li a i {
            min-width: 25px;
            text-align: center;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        #sidebar ul li a:hover i {
            transform: scale(1.1);
        }

        #sidebar ul li a span {
            flex: 1;
            margin-left: 10px;
            white-space: nowrap;
        }

        /* Submenu Arrow Styles */
        .submenu-arrow {
            margin-left: auto;
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .has-submenu.active .submenu-arrow {
            transform: rotate(180deg);
        }

        #sidebar.collapsed .submenu-arrow {
            display: none;
        }

        #sidebar.collapsed ul li span {
            display: none;
        }

        /* Submenu Styles */
        .has-submenu {
            position: relative;
        }

        .submenu {
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            margin-top: 5px;
        }

        .has-submenu.active .submenu {
            display: block;
        }

        .submenu li {
            margin: 0;
        }

        .submenu li a {
            padding: 8px 15px 8px 35px;
            font-size: 14px;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            transition: .3s;
        }

        .submenu li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .submenu li a i {
            min-width: 20px;
            font-size: 16px;
        }

        .submenu li a span {
            margin-left: 8px;
            font-size: 13px;
        }

        #sidebar.collapsed .submenu {
            position: absolute;
            left: 100%;
            top: 0;
            background: #dc3545;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            min-width: 180px;
            z-index: 1000;
        }

        #sidebar.collapsed .has-submenu.active .submenu {
            display: block;
        }

        /* CONTENT */
        #content {
            flex: 1;
            padding: 40px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-bar {
            position: relative;
            width: 260px;
        }

        .search-bar input {
            width: 100%;
            padding: 8px 35px 8px 12px;
            border-radius: 25px;
            border: 1px solid #ccc;
        }

        .search-bar i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
        }

        .notification-btn {
            font-size: 22px;
            cursor: pointer;
            color: #333;
        }

        .dropdown-menu {
            min-width: 250px;
            border-radius: 12px;
            padding: 0;
        }

        .dropdown-header {
            background: #f8f9fa;
            font-weight: 600;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-item {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item i {
            color: #dc3545;
            font-size: 18px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
            transition: .3s;
            padding: 25px 20px;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            text-decoration: none;
        }

        .stat-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 10px;
        }

        .book-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
        }

        .book-card img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .view-btn {
            border-radius: 30px;
            padding: 6px 18px;
            font-weight: 500;
            transition: .3s;
        }

        .view-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 10px rgba(220, 53, 69, .4);
        }

        .table thead th {
            background: #007bff;
            color: white;
        }

        .table-warning thead th {
            background: #ffc107;
            color: #212529;
            border-color: #ffecb5;
        }

        .table-danger {
            background-color: #ffffff;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        /* Tab Styles */
        .nav-tabs .nav-link {
            border: none;
            border-radius: 8px 8px 0 0;
            font-weight: 500;
            color: #6c757d;
            padding: 12px 20px;
            margin-right: 4px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            background-color: #f8f9fa;
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            background: white;
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
                    <a href="adminDashboard.php">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="adminBooks.php">
                        <i class="bi bi-book"></i>
                        <span>Books</span>
                    </a>
                </li>
                <li>
                    <a href="adminUsers.php">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="has-submenu">
                    <a href="adminCirculation.php" class="submenu-toggle">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Circulation</span>
                        <i class="bi bi-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="adminCirculation.php#pending">
                                <i class="bi bi-hourglass"></i>
                                <span>Pending Requests</span>
                            </a>
                        </li>
                        <li>
                            <a href="adminCirculation.php#return">
                                <i class="bi bi-arrow-left-circle"></i>
                                <span>Return Book</span>
                            </a>
                        </li>
                        <li>
                            <a href="adminCirculation.php#due">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Due Books</span>
                            </a>
                        </li>
                        <li>
                            <a href="adminCirculation.php#borrowed">
                                <i class="bi bi-list-ul"></i>
                                <span>All Borrowed</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="adminActivityLogs.php">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Logs</span>
                    </a>
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
            <div class="content-header">
                <h2>Manage Circulation</h2>
                <div class="search-container">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search borrowed books">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>

            <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

            <!-- Circulation Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="circulationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                <i class="bi bi-clock-history me-2"></i>Pending Requests
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="return-tab" data-bs-toggle="tab" data-bs-target="#return" type="button" role="tab" aria-controls="return" aria-selected="false">
                                <i class="bi bi-arrow-return-left me-2"></i>Return Book
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="due-tab" data-bs-toggle="tab" data-bs-target="#due" type="button" role="tab" aria-controls="due" aria-selected="false">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Due Books
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="borrowed-tab" data-bs-toggle="tab" data-bs-target="#borrowed" type="button" role="tab" aria-controls="borrowed" aria-selected="false">
                                <i class="bi bi-journal-bookmark me-2"></i>All Borrowed
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="circulationTabContent">
                        <!-- Pending Requests Tab -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <?php include 'circulation_pending.php'; ?>
                        </div>

                        <!-- Return Book Tab -->
                        <div class="tab-pane fade" id="return" role="tabpanel" aria-labelledby="return-tab">
                            <?php include 'circulation_return.php'; ?>
                        </div>

                        <!-- Due Books Tab -->
                        <div class="tab-pane fade" id="due" role="tabpanel" aria-labelledby="due-tab">
                            <?php include 'circulation_due.php'; ?>
                        </div>

                        <!-- All Borrowed Tab -->
                        <div class="tab-pane fade" id="borrowed" role="tabpanel" aria-labelledby="borrowed-tab">
                            <?php include 'circulation_borrowed.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            if (window.innerWidth < 992) {
                sidebar.classList.toggle("active");
            } else {
                sidebar.classList.toggle("collapsed");
                // Save state
                const isCollapsed = sidebar.classList.contains("collapsed");
                localStorage.setItem("sidebarCollapsed", isCollapsed);
            }
        }

        // Load state on page load
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
            if (isCollapsed && window.innerWidth >= 992) {
                sidebar.classList.add("collapsed");
            }

            // Submenu toggle functionality
            const submenuToggle = document.querySelector('.submenu-toggle');
            const submenu = document.querySelector('.submenu');

            if (submenuToggle && submenu) {
                submenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const hasSubmenu = this.parentElement;
                    hasSubmenu.classList.toggle('active');
                });

                // Close submenu when clicking outside
                document.addEventListener('click', function(e) {
                    const hasSubmenu = document.querySelector('.has-submenu');
                    if (!hasSubmenu.contains(e.target)) {
                        hasSubmenu.classList.remove('active');
                    }
                });
            }

            // Handle URL hash for tabs
            function activateTabFromHash() {
                const hash = window.location.hash.substring(1); // Remove the '#'
                if (hash) {
                    // Use Bootstrap's tab system to activate the correct tab
                    const targetTab = document.querySelector(`[data-bs-target="#${hash}"]`);
                    if (targetTab) {
                        const tab = new bootstrap.Tab(targetTab);
                        tab.show();
                    }
                }
            }

            // Activate tab on page load
            setTimeout(activateTabFromHash, 100); // Small delay to ensure Bootstrap is ready

            // Handle hash changes (browser back/forward)
            window.addEventListener('hashchange', activateTabFromHash);

            // Update URL hash when tabs are clicked
            const tabLinks = document.querySelectorAll('#circulationTabs .nav-link');
            tabLinks.forEach(link => {
                link.addEventListener('shown.bs.tab', function() {
                    const target = this.getAttribute('data-bs-target').substring(1); // Remove the '#'
                    window.location.hash = target;
                });
            });
        });

        // Return confirmation
        function confirmReturn(id) {
            // Create modal if it doesn't exist
            if (!document.getElementById('returnModal')) {
                const modalHTML = `
                    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="returnModalLabel">Confirm Return</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to mark this book as returned? This will update the return date and status.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="borrow_id" id="returnBorrowId">
                                        <input type="hidden" name="return_date" value="${new Date().toISOString().split('T')[0]}">
                                        <button type="submit" name="return_book" class="btn btn-success">Confirm Return</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);
            }

            document.getElementById('returnBorrowId').value = id;
            new bootstrap.Modal(document.getElementById('returnModal')).show();
        }
    </script>
</body>
</html>
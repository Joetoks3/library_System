<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch stats
$total_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM books"));
$total_users = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users"));
$borrowed_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status='Borrowed'"));
$overdue_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status='Borrowed' AND due_date < CURDATE()"));
$due_soon_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status='Borrowed' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"));

// Fetch recent activity
$recent_borrowed = mysqli_query($con, "SELECT bb.*, b.title, u.firstname, u.lastname FROM borrowed_books bb 
                                      JOIN books b ON bb.book_id = b.id 
                                      JOIN users u ON bb.user_id = u.id 
                                      WHERE bb.status='Borrowed' 
                                      ORDER BY bb.borrow_date DESC LIMIT 5");

// Fetch recent returns
$recent_returns = mysqli_query($con, "SELECT bb.*, b.title, u.firstname, u.lastname FROM borrowed_books bb 
                                     JOIN books b ON bb.book_id = b.id 
                                     JOIN users u ON bb.user_id = u.id 
                                     WHERE bb.status='Returned' 
                                     ORDER BY bb.return_date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Admin Dashboard</title>
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
            background: #dc3545;
            color: white;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .text-teal {
            color: #20c997 !important;
        }

        .btn-outline-primary:hover, .btn-outline-success:hover, .btn-outline-info:hover, .btn-outline-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
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
                    <a href="#">
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
            <!-- HEADER -->
            <div class="content-header">
                <h2>Library Dashboard</h2>
                <div class="search-container">
                    <div class="search-bar">
                        <input type="text" placeholder="Search books or users">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #007bff;">
                        <div class="stat-title">
                            <i class="bi bi-journal-bookmark text-primary"></i>
                            <span>Total Books</span>
                        </div>
                        <div class="stat-number text-primary"><?php echo $total_books; ?></div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #28a745;">
                        <div class="stat-title">
                            <i class="bi bi-people text-success"></i>
                            <span>Total Users</span>
                        </div>
                        <div class="stat-number text-success"><?php echo $total_users; ?></div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #dc3545;">
                        <div class="stat-title">
                            <i class="bi bi-book-half text-danger"></i>
                            <span>Borrowed Books</span>
                        </div>
                        <div class="stat-number text-danger"><?php echo $borrowed_books; ?></div>
                    </a>
                </div>
            </div>

            <!-- SECOND ROW OF STATS -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #17a2b8;">
                        <div class="stat-title">
                            <i class="bi bi-clock-history text-info"></i>
                            <span>Due Soon (3 days)</span>
                        </div>
                        <div class="stat-number text-info"><?php echo $due_soon_books; ?></div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #6f42c1;">
                        <div class="stat-title">
                            <i class="bi bi-graph-up text-purple"></i>
                            <span>Available Books</span>
                        </div>
                        <div class="stat-number text-purple"><?php echo $total_books - $borrowed_books; ?></div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="card bg-white text-dark stat-card" style="border-top: 5px solid #ffc107;">
                        <div class="stat-title">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <span>Overdue Books</span>
                        </div>
                        <div class="stat-number text-warning"><?php echo $overdue_books; ?></div>
                    </a>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-white">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-lightning text-warning"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="adminBooks.php" class="btn btn-outline-primary w-100 p-3">
                                        <i class="bi bi-plus-circle d-block fs-2 mb-2"></i>
                                        Add New Book
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="adminUsers.php" class="btn btn-outline-success w-100 p-3">
                                        <i class="bi bi-person-plus d-block fs-2 mb-2"></i>
                                        Add New User
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="adminCirculation.php#pending" class="btn btn-outline-info w-100 p-3">
                                        <i class="bi bi-hourglass d-block fs-2 mb-2"></i>
                                        Pending Requests
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="adminCirculation.phpre#due" class="btn btn-outline-warning w-100 p-3">
                                        <i class="bi bi-exclamation-triangle d-block fs-2 mb-2"></i>
                                        View Due Books
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT ACTIVITY -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-white">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-arrow-right-circle text-success"></i> Recent Borrows</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php while($borrow = mysqli_fetch_assoc($recent_borrowed)): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($borrow['title']); ?></strong><br>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($borrow['firstname'] . ' ' . $borrow['lastname']); ?>
                                            </small>
                                        </div>
                                        <small class="text-success">
                                            <i class="bi bi-calendar"></i> <?php echo date('M d', strtotime($borrow['borrow_date'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php if(mysqli_num_rows($recent_borrowed) == 0): ?>
                                <div class="list-group-item text-center text-muted">
                                    <i class="bi bi-info-circle"></i> No recent borrows
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-white">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-arrow-left-circle text-primary"></i> Recent Returns</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php while($return = mysqli_fetch_assoc($recent_returns)): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($return['title']); ?></strong><br>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($return['firstname'] . ' ' . $return['lastname']); ?>
                                            </small>
                                        </div>
                                        <small class="text-primary">
                                            <i class="bi bi-calendar-check"></i> <?php echo date('M d', strtotime($return['return_date'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php if(mysqli_num_rows($recent_returns) == 0): ?>
                                <div class="list-group-item text-center text-muted">
                                    <i class="bi bi-info-circle"></i> No recent returns
                                </div>
                                <?php endif; ?>
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
        });
    </script>
</body>
</html>
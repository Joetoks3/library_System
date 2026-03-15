<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle search
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$where_clause = $search_term ? "AND CONCAT(u.firstname, ' ', u.lastname) LIKE '%$search_term%'" : '';

// Fetch recent activities (last 50) with search filter
$activities = mysqli_query($con, "
    SELECT 'issue' as type, bb.id, bb.borrow_date as date, u.firstname, u.lastname, b.title, NULL as return_date
    FROM borrowed_books bb
    JOIN users u ON bb.user_id = u.id
    JOIN books b ON bb.book_id = b.id
    WHERE bb.status = 'borrowed' $where_clause

    UNION ALL

    SELECT 'return' as type, bb.id, bb.return_date as date, u.firstname, u.lastname, b.title, bb.return_date
    FROM borrowed_books bb
    JOIN users u ON bb.user_id = u.id
    JOIN books b ON bb.book_id = b.id
    WHERE bb.status = 'returned' AND bb.return_date IS NOT NULL $where_clause

    ORDER BY date DESC
    LIMIT 50
");

// Get activity counts (these remain the same regardless of search)
$total_issues = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status = 'borrowed'"));
$total_returns = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status = 'returned'"));
$overdue_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE status = 'borrowed' AND due_date < CURDATE()"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
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

        .activity-item {
            border: none;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: .3s;
        }

        .activity-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .activity-issue {
            background: #d4edda;
            color: #155724;
        }

        .activity-return {
            background: #d1ecf1;
            color: #0c5460;
        }

        .timeline {
            position: relative;
            padding-left: 60px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 15px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dc3545;
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
                <h2>Activity Logs</h2>
                <div class="search-container">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search by user name" value="<?php echo htmlspecialchars($search_term); ?>">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-white text-dark stat-card" style="border-top: 5px solid #28a745;">
                        <div class="stat-title">
                            <i class="bi bi-plus-circle text-success"></i>
                            <span>Total Issues</span>
                        </div>
                        <div class="stat-number text-success"><?php echo $total_issues; ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-white text-dark stat-card" style="border-top: 5px solid #17a2b8;">
                        <div class="stat-title">
                            <i class="bi bi-arrow-left-circle text-info"></i>
                            <span>Total Returns</span>
                        </div>
                        <div class="stat-number text-info"><?php echo $total_returns; ?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-white text-dark stat-card" style="border-top: 5px solid #dc3545;">
                        <div class="stat-title">
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                            <span>Currently Overdue</span>
                        </div>
                        <div class="stat-number text-danger"><?php echo $overdue_count; ?></div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if(mysqli_num_rows($activities) > 0): ?>
                            <?php while($activity = mysqli_fetch_assoc($activities)): ?>
                            <div class="timeline-item">
                                <div class="activity-item card bg-white">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon <?php echo $activity['type'] == 'issue' ? 'activity-issue' : 'activity-return'; ?>">
                                                <i class="bi <?php echo $activity['type'] == 'issue' ? 'bi-plus-circle' : 'bi-arrow-left-circle'; ?>"></i>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <strong><?php echo htmlspecialchars($activity['firstname'] . ' ' . $activity['lastname']); ?></strong>
                                                            <?php echo $activity['type'] == 'issue' ? 'borrowed' : 'returned'; ?>
                                                            <strong>"<?php echo htmlspecialchars($activity['title']); ?>"</strong>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar"></i>
                                                            <?php echo date('M d, Y h:i A', strtotime($activity['date'])); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge <?php echo $activity['type'] == 'issue' ? 'bg-success' : 'bg-info'; ?>">
                                                        <?php echo ucfirst($activity['type']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">
                                    <?php echo $search_term ? 'No activities found for "' . htmlspecialchars($search_term) . '"' : 'No Activities Yet'; ?>
                                </h5>
                                <p class="text-muted">
                                    <?php echo $search_term ? 'Try searching with a different name.' : 'Activity logs will appear here as books are issued and returned.'; ?>
                                </p>
                                <?php if ($search_term): ?>
                                    <a href="adminActivityLogs.php" class="btn btn-outline-primary">
                                        <i class="bi bi-x-circle"></i> Clear Search
                                    </a>
                                <?php endif; ?>
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

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();

                searchTimeout = setTimeout(() => {
                    // Update URL with search parameter
                    const url = new URL(window.location);
                    if (searchTerm) {
                        url.searchParams.set('search', searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }, 500); // Debounce for 500ms
            });
        });
    </script>
</body>
</html>
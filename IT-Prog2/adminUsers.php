<?php
session_start();
include("connection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle add user
if (isset($_POST['add_user'])) {
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($con, $_POST['role']);

    $sql = "INSERT INTO users (firstname, lastname, email_address, username, password, role) VALUES ('$firstname', '$lastname', '$email', '$username', '$password', '$role')";
    if (mysqli_query($con, $sql)) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . mysqli_error($con);
    }
}

// Check for message from delete
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
                <h2>Manage Users</h2>
                <div class="search-container">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search users">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>

            <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

            <!-- Add User Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Add New User</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>

            <!-- Users List -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Existing Users</h5>
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $users_q = mysqli_query($con, "SELECT * FROM users");
                            while ($u = mysqli_fetch_assoc($users_q)) {
                                echo "<tr>
                                    <td>{$u['id']}</td>
                                    <td>{$u['firstname']}</td>
                                    <td>{$u['lastname']}</td>
                                    <td>{$u['username']}</td>
                                    <td>{$u['email_address']}</td>
                                    <td>{$u['role']}</td>
                                    <td><a href='#' class='btn btn-danger btn-sm' data-id='{$u['id']}' onclick='confirmDelete({$u['id']})'>Delete</a></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this user? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
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

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Delete confirmation
        function confirmDelete(id) {
            document.getElementById('confirmDeleteBtn').href = 'delete_user.php?id=' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
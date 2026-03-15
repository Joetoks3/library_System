<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Times New Roman', sans-serif;
            background: #f4f6f9;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
        }

        #sidebar {
            width: 250px;
            min-height: 100vh;
            background: #dc3545;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: all 0.35s ease;
            position: relative;
        }

        #sidebar.collapsed {
            width: 70px;
            padding: 20px 10px;
            align-items: center;
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
            transition: all 0.3s;
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

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        #sidebar.collapsed ul li span {
            display: none;
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
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            z-index: 1001;
        }

        .toggle-btn:hover {
            transform: rotate(90deg);
        }

        #content {
            flex: 1;
            padding: 40px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {
            position: relative;
            width: 250px;
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
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        table th,
        table td {
            vertical-align: middle !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
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
                    <a href="dashboard.php">
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="books.php">
                        <i class="bi bi-book"></i>
                        <span>Books</span>
                    </a>
                </li>
                <li>
                    <a href="profile.php">
                        <i class="bi bi-person"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="borrowed.php" class="active">
                        <i class="bi bi-book-half"></i>
                        <span>Books Borrowed</span>
                    </a>
                </li>
                <li>
                    <a href="due-soon.php">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Due Soon</span>
                    </a>
                </li>
                <li>
                    <a href="issue-return.php">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Issue / Return</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php">
                        <i class="bi bi-bar-chart"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="content-header">
                <h2>
                    <i class="bi bi-book-half"></i>
                    Borrowed Books
                </h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search borrowed books">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card bg-primary text-white">
                        <h6>Total Borrowed</h6>
                        <h2>12</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-warning text-dark">
                        <h6>Due Soon</h6>
                        <h2>3</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-danger text-white">
                        <h6>Overdue</h6>
                        <h2>1</h2>
                    </div>
                </div>
            </div>

            <div class="card table-card">
                <div class="card-header bg-white">
                    <strong>Borrowed Books List</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Book ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date Borrowed</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>B001</td>
                                    <td>The Art of War</td>
                                    <td>Sun Tzu</td>
                                    <td>May 10 2026</td>
                                    <td>May 20 2026</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>B002</td>
                                    <td>Atomic Habits</td>
                                    <td>James Clear</td>
                                    <td>May 05 2026</td>
                                    <td>May 15 2026</td>
                                    <td><span class="badge bg-warning text-dark">Due Soon</span></td>
                                </tr>
                                <tr>
                                    <td>B003</td>
                                    <td>Rich Dad Poor Dad</td>
                                    <td>Robert Kiyosaki</td>
                                    <td>Apr 25 2026</td>
                                    <td>May 05 2026</td>
                                    <td><span class="badge bg-danger">Overdue</span></td>
                                </tr>
                            </tbody>
                        </table>
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
            }
        }
    </script>
</body>
</html>
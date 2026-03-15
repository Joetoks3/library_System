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

// Get user's borrowed books (all statuses)
$user_books = mysqli_query($con, "SELECT bb.*, b.title, b.author, b.genre, DATEDIFF(bb.due_date, CURDATE()) as days_left FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.user_id = '$user_id' ORDER BY bb.borrow_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books - Library Management System</title>

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
            padding: 40px;
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

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
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
                <a href="userBooks.php">
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
                    <li><a href="userMyBooks.php" class="active"><i class="bi bi-book-half"></i> <span>My Books</span></a></li>
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
                <h2><i class="bi bi-book-half header-icon"></i>My Books</h2>
                <p>View all your borrowed books and requests</p>
            </div>
        </div>

        <!-- BOOKS GRID -->
        <div class="row">
            <?php if(mysqli_num_rows($user_books) > 0): ?>
                <?php while($book = mysqli_fetch_assoc($user_books)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card book-card position-relative">
                        <div class="status-badge">
                            <?php
                            switch($book['status']) {
                                case 'pending':
                                    echo '<span class="badge bg-info"><i class="bi bi-hourglass"></i> Pending</span>';
                                    break;
                                case 'borrowed':
                                    if($book['days_left'] < 0) {
                                        echo '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</span>';
                                    } elseif($book['days_left'] <= 3) {
                                        echo '<span class="badge bg-warning"><i class="bi bi-clock"></i> Due Soon</span>';
                                    } else {
                                        echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>';
                                    }
                                    break;
                                case 'returned':
                                    echo '<span class="badge bg-secondary"><i class="bi bi-arrow-left-circle"></i> Returned</span>';
                                    break;
                                case 'rejected':
                                    echo '<span class="badge bg-dark"><i class="bi bi-x-circle"></i> Rejected</span>';
                                    break;
                            }
                            ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($book['title']); ?></h6>
                            <p class="card-text text-muted small mb-1">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <?php if (!empty($book['genre'])): ?>
                            <p class="card-text text-muted small mb-3">
                                <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($book['genre']); ?>
                            </p>
                            <?php endif; ?>

                            <div class="mt-auto">
                                <div class="row text-center mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Borrowed</small>
                                        <strong><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Due</small>
                                        <strong><?php echo date('M d, Y', strtotime($book['due_date'])); ?></strong>
                                    </div>
                                </div>

                                <?php if($book['status'] == 'borrowed'): ?>
                                    <div class="text-center">
                                        <?php if($book['days_left'] < 0): ?>
                                            <span class="badge bg-danger w-100">
                                                <?php echo abs($book['days_left']); ?> day<?php echo abs($book['days_left']) != 1 ? 's' : ''; ?> overdue
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info w-100">
                                                <?php echo $book['days_left']; ?> day<?php echo $book['days_left'] != 1 ? 's' : ''; ?> left
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($book['status'] == 'pending'): ?>
                                    <div class="text-center">
                                        <span class="badge bg-info w-100">Waiting for admin approval</span>
                                    </div>
                                <?php elseif($book['status'] == 'rejected'): ?>
                                    <div class="text-center">
                                        <span class="badge bg-dark w-100">Request was rejected</span>
                                    </div>
                                <?php elseif($book['status'] == 'returned'): ?>
                                    <div class="text-center">
                                        <span class="badge bg-secondary w-100">Returned on <?php echo date('M d, Y', strtotime($book['return_date'])); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-book-half text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No Books Yet</h4>
                        <p class="text-muted">You haven't borrowed any books yet. <a href="userBooks.php">Browse available books</a></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- BOOTSTRAP JS -->
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

    function toggleSubMenu() {
        const sub = document.getElementById('profile-submenu');
        sub.style.display = sub.style.display === 'none' || sub.style.display === '' ? 'block' : 'none';
    }
</script>

</body>
</html>
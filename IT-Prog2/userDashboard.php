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

        // Get user statistics
        $total_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM books"));
        $available_books = mysqli_num_rows(mysqli_query($con, "SELECT * FROM books WHERE quantity > 0"));
        $user_borrowed = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'borrowed'"));
        $user_overdue = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'borrowed' AND due_date < CURDATE()"));
        $user_due_soon = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'borrowed' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"));

        // Get user's recently borrowed books
        $recent_borrowed = mysqli_query($con, "SELECT bb.*, b.title, b.author FROM borrowed_books bb JOIN books b ON bb.book_id = b.id WHERE bb.user_id = '$user_id' ORDER BY bb.borrow_date DESC LIMIT 6");

        // Get recommended books (books not borrowed by user)
        $recommended_books = mysqli_query($con, "SELECT * FROM books WHERE id NOT IN (SELECT book_id FROM borrowed_books WHERE user_id = '$user_id') AND quantity > 0 ORDER BY RAND() LIMIT 6");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System Dashboard</title>

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

        #sidebar ul li span {
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

        /* BELL */
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

        /* STAT CARDS */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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

        /* BOOK CARD */
        .book-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .book-card img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .book-cover {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
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
                <h2>Welcome back, <?php echo htmlspecialchars($user['firstname'] ?? 'User'); ?>!</h2>
                <p class="text-muted mb-0">Here's what's happening with your library account</p>
            </div>

            <div class="search-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search books or authors">
                    <i class="bi bi-search"></i>
                </div>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <a href="userBooks.php" class="card bg-white text-dark stat-card" style="border-top: 5px solid #007bff;">
                    <div class="stat-title">
                        <i class="bi bi-journal-bookmark text-primary"></i>
                        <span>Available Books</span>
                    </div>
                    <div class="stat-number text-primary"><?php echo $available_books; ?></div>
                </a>
            </div>

            <div class="col-md-4 mb-3">
                <a href="borrowed.php" class="card bg-white text-dark stat-card" style="border-top: 5px solid #28a745;">
                    <div class="stat-title">
                        <i class="bi bi-book-half text-success"></i>
                        <span>My Books</span>
                    </div>
                    <div class="stat-number text-success"><?php echo $user_borrowed; ?></div>
                </a>
            </div>

            <div class="col-md-4 mb-3">
                <a href="borrowed.php" class="card bg-white text-dark stat-card" style="border-top: 5px solid <?php echo $user_overdue > 0 ? '#dc3545' : '#ffc107'; ?>;">
                    <div class="stat-title">
                        <i class="bi bi-exclamation-triangle <?php echo $user_overdue > 0 ? 'text-danger' : 'text-warning'; ?>"></i>
                        <span><?php echo $user_overdue > 0 ? 'Overdue' : 'Due Soon'; ?></span>
                    </div>
                    <div class="stat-number <?php echo $user_overdue > 0 ? 'text-danger' : 'text-warning'; ?>"><?php echo $user_overdue > 0 ? $user_overdue : $user_due_soon; ?></div>
                </a>
            </div>
        </div>

        <!-- RECOMMENDED BOOKS CAROUSEL -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Recommended for You</h5>
                        <a href="userBooks.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div id="bookCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php
                                // Get recommended books (books not borrowed by user, ordered by popularity)
                                $recommended_query = mysqli_query($con, "
                                    SELECT b.*, 
                                           COUNT(bb.book_id) as borrow_count,
                                           CASE WHEN bb.user_id IS NULL THEN 0 ELSE 1 END as user_borrowed
                                    FROM books b 
                                    LEFT JOIN borrowed_books bb ON b.id = bb.book_id AND bb.user_id = '$user_id'
                                    WHERE bb.user_id IS NULL OR bb.return_date IS NOT NULL
                                    GROUP BY b.id
                                    ORDER BY borrow_count DESC, b.title ASC
                                    LIMIT 12
                                ");
                                
                                $books = mysqli_fetch_all($recommended_query, MYSQLI_ASSOC);
                                $chunks = array_chunk($books, 4); // 4 books per slide
                                
                                foreach ($chunks as $index => $chunk) {
                                    $active = $index === 0 ? 'active' : '';
                                    echo "<div class='carousel-item $active'>";
                                    echo "<div class='row'>";
                                    
                                    foreach ($chunk as $book) {
                                        $genre_badge = !empty($book['genre']) ? "<span class='badge bg-secondary'>{$book['genre']}</span>" : "";
                                        echo "
                                        <div class='col-md-3 mb-3'>
                                            <div class='card book-card h-100'>
                                                <div class='card-body text-center'>
                                                    <div class='book-cover mb-3'>
                                                        <i class='bi bi-book-fill text-primary' style='font-size: 3rem;'></i>
                                                    </div>
                                                    <h6 class='card-title mb-2'>{$book['title']}</h6>
                                                    <p class='card-text text-muted small mb-2'>{$book['author']}</p>
                                                    $genre_badge
                                                </div>
                                                <div class='card-footer bg-transparent'>
                                                    <a href='userBooks.php?borrow={$book['id']}' class='btn btn-primary btn-sm w-100'>Borrow</a>
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    
                                    echo "</div></div>";
                                }
                                
                                if (empty($books)) {
                                    echo "<div class='carousel-item active'>";
                                    echo "<div class='text-center py-5'>";
                                    echo "<i class='bi bi-book text-muted' style='font-size: 3rem;'></i>";
                                    echo "<p class='text-muted mt-3'>No books available for recommendation</p>";
                                    echo "</div></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        const button = event.target.closest('.menu-header');
        const sub = document.getElementById('profile-submenu');
        sub.style.display = sub.style.display === 'none' || sub.style.display === '' ? 'block' : 'none';
        button.classList.toggle('expanded');
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const bookCards = document.querySelectorAll('.book-card');
        
        bookCards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const author = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
            
            if (title.includes(searchTerm) || author.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Notification dropdown auto-close after click
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const dropdown = bootstrap.Dropdown.getInstance(this.closest('.dropdown-toggle'));
            if (dropdown) {
                dropdown.hide();
            }
        });
    });
</script>

</body>
</html>
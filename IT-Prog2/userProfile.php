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

if (!$user) {
    header("Location: login.php");
    exit();
}

// Handle profile update
if(isset($_POST['update_profile'])){
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    
    // Update user information
    mysqli_query($con, "UPDATE users SET firstname='$firstname', lastname='$lastname', email_address='$email' WHERE username='$user_id'");
    
    // Log activity
    mysqli_query($con, "INSERT INTO activity_logs (user_id, action, details, timestamp) VALUES ('$user_id', 'Update Profile', 'Updated profile information', NOW())");
    
    $success_message = "Profile updated successfully!";
    
    // Refresh user data
    $user_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$user_id'");
    $user = mysqli_fetch_assoc($user_query);
    if (!$user) {
        header("Location: login.php");
        exit();
    }
}

// Get user's borrowing statistics
$borrowed_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'borrowed'"));
$returned_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'returned'"));
$overdue_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'borrowed' AND due_date < CURDATE()"));
$total_borrowed = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id'"));
$returned_on_time = mysqli_num_rows(mysqli_query($con, "SELECT * FROM borrowed_books WHERE user_id = '$user_id' AND status = 'returned' AND return_date <= due_date"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Library Management System</title>
    
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

        /* PROFILE CARD */
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
        }

        /* STATS CARDS */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 25px 20px;
            text-align: center;
            transition: .3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: white;
            font-weight: 500;
        }

        /* FORM STYLES */
        .form-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* ALERTS */
        .alert {
            border-radius: 10px;
            border: none;
        }

        @media (max-width: 768px) {
            #sidebar {
                width: 70px;
            }
            
            #content {
                margin-left: 70px;
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
                <h2><i class="bi bi-person text-primary me-2"></i>My Profile</h2>
                <p class="text-muted mb-0">Manage your account information and view your library statistics</p>
            </div>
        </div>

        <!-- ALERTS -->
        <?php if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- PROFILE CARD -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-4">
                <div class="card profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h4>
                        <p class="mb-0"><?php echo htmlspecialchars($user['email_address']); ?></p>
                        <small>Member since <?php echo $user['created_at'] ? date('M Y', strtotime($user['created_at'])) : 'N/A'; ?></small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <h4 class="mb-3">Your Library Statistics</h4>
                <div class="row" id="statistics">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="stat-number"><?php echo $total_borrowed; ?></div>
                            <div class="stat-label">Total Borrowed</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="stat-number"><?php echo $returned_on_time; ?></div>
                            <div class="stat-label">Returned On Time</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="stat-number"><?php echo $borrowed_count; ?></div>
                            <div class="stat-label">Currently Borrowed</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card <?php echo $overdue_count > 0 ? 'bg-danger' : 'bg-warning'; ?> text-white">
                            <div class="stat-number"><?php echo $overdue_count; ?></div>
                            <div class="stat-label">Overdue Books</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT PROFILE FORM -->
        <div class="row">
            <div class="col-12">
                <div class="card form-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" 
                                           value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" 
                                           value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email_address']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Member Since</label>
                                    <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($user['created_at'])); ?>" readonly disabled>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" name="update_profile" class="btn btn-update">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
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
        sidebar.classList.toggle("collapsed");
    }

    function toggleSubMenu() {
        const sub = document.getElementById('profile-submenu');
        sub.style.display = sub.style.display === 'none' || sub.style.display === '' ? 'block' : 'none';
    }
</script>

</body>
</html>
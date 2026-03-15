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
                <span>Home</span>
            </a>
        </li>

        <li>
            <a href="userBooks.php">
                <i class="bi bi-book"></i>
                <span>Books</span>
            </a>
        </li>

        <li>
            <!-- Collapsible My Profile -->
            <a href="#profileSubmenu" data-bs-toggle="collapse" class="d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person"></i> My Profile</span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <ul class="collapse list-unstyled ps-3" id="profileSubmenu">
                <li><a href="account_info.php" class="d-block py-1">Account Info</a></li>
                <li><a href="userStatus.php" class="d-block py-1">Borrowed Books</a></li>
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

<!-- include("sidebar.php"); -->
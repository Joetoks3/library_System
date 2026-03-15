<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Library Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    section {
      padding: 60px 0;
    }
    .form-card {
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      padding: 30px;
      background: white;
    }
    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }
    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-md navbar-light pt-5 pb-4">
  <div class="container-xxl">
    <a class="navbar-brand" href="LIBRARY MANAGEMENT.php">
      <span class="text-secondary fw-bold text-danger">LIBRARY MANAGEMENT SYSTEM</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end align-center" id="main-nav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-danger" href="#">About The System</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="review.php">Reviews</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="#">Get in Touch</a>
        </li>
        <li class="nav-item ms-2 d-none d-md-inline">
          <a class="btn btn-secondary btn-danger" href="Create.php">Create an Account</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- LOGIN SECTION -->
<section id="login">
  <div class="container-lg">
    <div class="row g-4 justify-content-center align-items-center">

      <!-- LOGIN FORM -->
      <div class="col-md-5">
        <div class="form-card">
          <h3 class="text-danger fw-bold mb-4 text-center">Login to Your Account</h3>
          <form action="login_process.php" method="POST">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <div class="d-grid mt-4">
              <button type="submit" name="login" class="btn btn-danger btn-lg">Login</button>
            </div>
            <div class="text-center mt-3">
              Don't have an account? <a href="signup.php" class="text-danger fw-bold">Create Here</a>
            </div>
          </form>
        </div>
      </div>

      <!-- IMAGE -->
      <div class="col-md-5 text-center d-none d-md-block">
        <img src="artofwar.jpg" class="img-fluid" alt="Library Book">
      </div>

    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
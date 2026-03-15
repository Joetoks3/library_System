<?php
session_start();
include("connection.php");

if(isset($_POST['register'])){

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email_address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(firstname,lastname,email_address,username,password)
            VALUES('$firstname','$lastname','$email','$username','$hashed_password')";
    $result = mysqli_query($con, $sql);

    if($result){
        $_SESSION['message'] = "Account Created Successfully";
        header("location: login.php");
        exit();
    } else {
        echo "Error: ".mysqli_error($con);
    }
}
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIBRARY MANAGEMENT SYSTEM - Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      section {
        padding: 60px 0;
      }

      .form-card {
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        background: white;
      }
    </style>
  </head>

  <body>

  <?php include("navbar.php"); ?>

  <section id="create-account">
    <div class="container-lg">
      <div class="row g-4 justify-content-center align-items-center">

        <div class="col-md-5">
          <div class="form-card">
            <h3 class="text-danger fw-bold mb-4 text-center">Create Your Account</h3>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="firstname" class="form-control" placeholder="Enter your first name">
              </div>
              <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="lastname" class="form-control" placeholder="Enter your last name">
              </div>
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="text" name="email_address" class="form-control" placeholder="Enter your email">
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username">
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-danger btn-lg" name="register">Create Account</button>
              </div>
              <div class="text-center mt-3">
                Already have an account? <a href="LOGIN.php" class="text-danger fw-bold">Login Here</a>
              </div>
            </form>
          </div>
        </div>

        <div class="col-md-5 text-center d-none d-md-block">
          <img src="artofwar.jpg" class="img-fluid" alt="Library Book">
        </div>

      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
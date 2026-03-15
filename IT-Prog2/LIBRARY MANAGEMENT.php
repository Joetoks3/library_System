<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LIBRARY MANAGEMENT SYSTEM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
  <style>
    section{
      padding: 60px 0;
    }
  </style>
</head>
<body>

  <!-- navbar -->
  <nav class="navbar navbar-expand-md navbar-light pt-5 pb-4">
    <div class="container-xxl">
      <!-- navbar brand / title -->
      <a class="navbar-brand" href="LIBRARY MANAGEMENT.php">
        <span class="text-secondary fw-bold text-danger">
          LIBRARY MANAGEMENT SYSTEM
        </span>
      </a>
      <!-- toggle button for mobile nav -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- navbar links -->
      <div class="collapse navbar-collapse justify-content-end align-center" id="main-nav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-danger" href="system.php">About The System</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="review.php">Reviews</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="#">Get in Touch</a>
          </li>
          <li class="nav-item d-md-none">
            <a class="nav-link text-danger" href="#">Pricing</a>
          </li>
          <li class="nav-item ms-2 d-none d-md-inline">
            <a class="btn btn-secondary btn-danger" href="Create.php">Create an Account</a>
          </li>
          
        </ul>
      </div>
    </div>
  </nav>

  <!-- main image & intro text -->
  <section id="intro">
    <div class="container-lg">
      <div class="row g-4 justify-content-center align-items-center">
        <div class="col-md-5 text-center text-md-start">
          <h1>
            <div class="display-2 fw-bold">LIBRARY MANAGEMENT SYSTEM</div>
            <div class="display-5 text-danger">Your Digital Library Solution</div>
          </h1>
          <p class="lead my-4 text-danger">Manage your library efficiently with our easy-to-use system.</p>
          <a href="LOGIN.php" class="btn btn-secondary btn-lg btn-danger">LOGIN NOW</a>
        </div>
        <div class="col-md-5 text-center d-none d-md-block">
          <img src="artofwar.jpg" class="img-fluid" alt="ebook">
        </div>
      </div>
    </div>
  </section>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>
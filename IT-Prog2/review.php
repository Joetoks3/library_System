<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LIBRARY MANAGEMENT SYSTEM - REVIEWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    section {
      padding: 60px 0;
    }
    .review-card {
      height: 100%;
      transition: all 0.3s ease;
    }
    .review-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

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
        <li class="nav-item"><a class="nav-link text-danger" href="system.php">About The System</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="review.php">Reviews</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="#">Get in Touch</a></li>
        <li class="nav-item ms-2 d-none d-md-inline"><a class="btn btn-secondary btn-danger" href="Create.php">Create an Account</a></li>
      </ul>
    </div>
  </div>
</nav>

<section id="reviews">
  <div class="container-lg">
    <div class="text-center">
      <h2 class="text-danger fw-bold">User Reviews</h2>
      <p class="lead text-muted">What our users say about the Library Management System</p>
    </div>

    <div class="row my-5 g-4 justify-content-center">
      <div class="col-md-4 d-flex">
        <div class="card border-danger review-card w-100">
          <div class="card-body text-center d-flex flex-column">
            <h5 class="card-title text-danger fw-bold">Maria Kenneth</h5>
            <p class="card-text flex-grow-1">"This system made managing our school library very easy. Borrowing and tracking books is now faster and more organized."</p>
            <p class="text-warning">★★★★★</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 d-flex">
        <div class="card border-danger review-card w-100">
          <div class="card-body text-center d-flex flex-column">
            <h5 class="card-title text-danger fw-bold">Clifford John</h5>
            <p class="card-text flex-grow-1">"The interface is simple and user-friendly. Perfect for students and librarians to manage books efficiently."</p>
            <p class="text-warning">★★★★☆</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 d-flex">
        <div class="card border-danger review-card w-100">
          <div class="card-body text-center d-flex flex-column">
            <h5 class="card-title text-danger fw-bold">Joe Brain</h5>
            <p class="card-text flex-grow-1">"Tracking borrowed books and due dates is very convenient. This system helps keep everything organized."</p>
            <p class="text-warning">★★★★★</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row justify-content-center mt-5">
      <div class="col-md-6">
        <h4 class="text-center text-danger fw-bold">Leave a Review</h4>
        <form>
          <div class="mb-3">
            <label class="form-label">Your Name</label>
            <input type="text" class="form-control" placeholder="Enter your name">
          </div>
          <div class="mb-3">
            <label class="form-label">Your Review</label>
            <textarea class="form-control" rows="4" placeholder="Write your review"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Rating</label>
            <select class="form-select">
              <option>★★★★★</option>
              <option>★★★★☆</option>
              <option>★★★☆☆</option>
              <option>★★☆☆☆</option>
              <option>★☆☆☆☆</option>
            </select>
          </div>
          <div class="text-center">
            <button class="btn btn-danger btn-lg">Submit Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
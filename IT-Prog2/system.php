<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LIBRARY MANAGEMENT SYSTEM - About</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    section {
      padding: 60px 0;
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
        <li class="nav-item">
          <a class="nav-link text-danger" href="#about">About The System</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="review.php">Reviews</a>
        </li>
        <li class="nav-item ms-2 d-none d-md-inline">
          <a class="btn btn-secondary btn-danger" href="signup.php">Create an Account</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<section id="about">
  <div class="container-lg">
    <div class="text-center">
      <h2 class="text-danger fw-bold">About The System</h2>
      <p class="lead text-muted">Learn how the Library Management System works</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-8">
        <p class="lead text-center">
          The <strong>Library Management System</strong> is a digital platform designed to simplify
          and organize the management of library resources. It allows librarians and users to
          efficiently manage books, borrowing records, and user accounts in one centralized system.
        </p>

        <hr>

        <h4 class="text-danger fw-bold mt-4">What Does the System Do?</h4>
        <p>
          The system helps libraries manage their collection of books and keep track of borrowing
          activities. It reduces manual work by automating tasks such as recording borrowed books,
          tracking due dates, and managing user information.
        </p>

        <h4 class="text-danger fw-bold mt-4">How the System Works</h4>
        <p>
          Users create an account and log in to the system. Librarians or administrators can add
          books to the library database, update book information, and monitor borrowing records.
          When a user borrows a book, the system records the transaction and sets a due date for returning the book.
        </p>

        <h4 class="text-danger fw-bold mt-4">Key Features</h4>
        <ul>
          <li>Book catalog management</li>
          <li>User account registration and login</li>
          <li>Borrow and return book tracking</li>
          <li>Due date monitoring</li>
          <li>User reviews and feedback</li>
        </ul>

        <h4 class="text-danger fw-bold mt-4">Why Use This System?</h4>
        <p>
          This system helps libraries improve efficiency and organization. It reduces paperwork,
          prevents lost records, and makes it easier for both librarians and users to access
          information about books and borrowing history.
        </p>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
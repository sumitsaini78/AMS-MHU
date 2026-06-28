<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Teacher Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold" href="index.php"><i
                        class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD</a>
                <!-- Example single danger button -->
                <div class="btn-group">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Attendence
                    </button>
                    <a href="../index.php">
                        <button type="button" class="btn btn-danger" 
                        aria-expanded="false">
                      Main Menu
                    </button>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="attendence.php">Mark Attendence</a></li>
                        <li><a class="dropdown-item" href="#">View Attendence</a></li>
                        <li><a class="dropdown-item" href="#">Request For Correction</a></li>
                        <li><a class="dropdown-item" href="insert_subject.php">insert subjects</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    </ul>
                </div>
                <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                    <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                </span>
            </div>
        </nav>
    </header>
    <main>

    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
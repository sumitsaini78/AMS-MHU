<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the admin is actually logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ./index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $admin = mysqli_fetch_assoc($result);
    $admin_name = $admin['name'];
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Admin</title>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>

    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                <a href="index.php" class="--bs-body-bg">Home</a>
                <div class="right"><a href=""><span
                            class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                            <i class="fa-solid fa-user-tie me-1"></i> Welcome, <?php echo $admin_name; ?>
                        </span>
                        <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a></a></div>
            </div>
        </nav>
    </header>
    <div class="container my-5">
        <div class="row">
            <div class="col"> </div>

            <div class="col"></div>
            <div class="col"></div>
            <div class="col">
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div
                class="col border border-danger d-flex m-4 p-5 justify-content-center align-items-center flex-wrap gap-3">
                <h2 class="text-center w-100">Create</h2>
                <a href="add_dean.php" target="_blank"> <button type="button" class="btn btn-outline-danger">Add
                        Dean</button></a>
                <a href="add_Faculty.php">
                    <button type="button" class="btn btn-outline-danger">Add Faculty</button>
                </a>
                <a href="add_Students.php">
                    <button type="button" class="btn btn-outline-danger">Add Students</button>
                </a>
                <a href="add_Subjects.php">
                    <button type="button" class="btn btn-outline-danger">Add Subjects</button>
                </a>
                <a href="add_Teacher.php">
                    <button type="button" class="btn btn-outline-danger">Add Teachers</button>
                </a>
                <a href="subject_Teacher_Allotment.php">
                    <button type="button" class="btn btn-outline-danger">Assign-Subject</button>
                </a>
            </div>
            <div
                class="col border border-success d-flex m-4 p-5 justify-content-center align-items-center flex-wrap gap-3">
                <h2 class="text-center w-100">Read</h2>

                <a href="All_Dept_Details.php" class="btn btn-outline-success">All Departments</a>
                <a href="All_Dept_Details.php" class="btn btn-outline-success">All Departments</a>
                <a href="All_Dean_Details.php" class="btn btn-outline-success">All Dean</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
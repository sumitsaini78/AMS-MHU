<?php
include "../db_connect.php";
session_start();

if (isset($_POST['course_name'])) {
    $course = mysqli_real_escape_string($conn, $_POST['course_name']);
    
    // Now you can run queries specific to that course track!
    echo "<h1>Viewing detailed data for: " . htmlspecialchars($course) . "</h1>";
    
    // Example target query:
    // $query = "SELECT * FROM `attendance` WHERE course = '$course'";
} else {
    // Fallback if accessed directly without submission
    header("Location: dashboard.php");
    exit();
}
?>
<?php
include "../db_connect.php";

// 1. Secure the page: Check if the Dean is actually logged in
if (!isset($_SESSION['dean_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ./index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['dean_id'];
$query = "SELECT * FROM deans WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $dean = mysqli_fetch_assoc($result);
    $dean_name = $dean['Dean_name'];
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Dean</title>
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
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS <sub class="text-primary">Dean</sub></span>
                <div class="right"> <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome, <mark><?php echo $dean_name; ?></mark>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a><a href=""></a>
                </div>
            </div>
        </nav>
    </header>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
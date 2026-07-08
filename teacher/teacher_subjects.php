<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ../index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['teacher_id'];
$query = "SELECT * FROM teachers WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Title</title>
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
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container w-25">
            <h2 class="text-align-center my-3">My Subjects</h2>
            <ul class="list-group">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Select A Subject for Marking Attendance</label>

                    <!-- get subjects dropdown -->
                    <?php
                    $query = "SELECT subject_name FROM `subjected_teacher` WHERE teacher_id = '$id'";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $val = $row ['subject_name'];
                        echo "<form action='select_attendence_details.php' method='POST'>";
                        echo "<input type='hidden' name='subject_name' value='" . htmlspecialchars($val) . "'>";
                        echo "<button type='submit' class='btn btn-outline-primary w-100 mb-2'>" . htmlspecialchars($val) . "</button>";
                        echo "</form>";
                    }
                    ?>
                    </select>
                    <!--  end subject geting-->
                </div>
            </ul>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
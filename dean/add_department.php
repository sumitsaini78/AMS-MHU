<?php
include "../db_connect.php";
if (isset($_POST['dept_name'])) {
    $department_name = $_POST['dept_name'];
    $department_full_name = $_POST['dept_full_name'];
    $query = "insert into `departments`(dep_name,full_name)VALUES('$department_name','$department_full_name') ";
    if (mysqli_query($conn, $query) == 1) {
        echo "inserted succefull";
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Add-Department</title>
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
    <div class="container w-50 my-5 border p-3">
        <form method="post">
            <div class="mb-3">
                <label for="dept_name" class="form-label">Department name</label>
                <input type="text" class="form-control" name="dept_name" id="dept_name" aria-describedby="emailHelp">
                <label for="dept_name" class="form-label">Department Full name</label>
                <input type="text" class="form-control" name="dept_full_name" id="dept_full_name"
                    aria-describedby="emailHelp">
                <input type="submit" class="btn btn-primary mt-3">
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
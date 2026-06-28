<?php
include "../db_connect.php";
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head><title>Admin</title>

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
    <div class="container my-5">
        <div class="row">
            <div class="col"> <a href="add_dean.php" target="_blank"> <button type="button" class="btn btn-primary">Add
                        Dean</button></a></div>
            <div class="col"> <a href="add_department.php" target="_blank"><button type="button"
                        class="btn btn-primary">Add
                        Department</button></a></div>
                        <div class="col"><a href="All_Dean_Details.php"  class="btn btn-primary">All Dean</a></div>
                        <div class="col"><a href="All_Dept_Details.php"  class="btn btn-primary">All Departments</a></div>
                        <div class="col">
                        <div class="col"><a href="add_teacher.php"  class="btn btn-primary">Add Teachers</a></div>
                        </div>
                        <div class="col"><a href="add_subjects.php"  class="btn btn-primary">Add Subjects</a></div>
        </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
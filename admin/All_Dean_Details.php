<?php
include "../db_connect.php";
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
    <div class="row border m-4 w-50">
        <div class="col"> <?php
        $query = "SELECT dean_name FROM `deans`";
        $result = mysqli_query($conn, $query);
        echo "<table class='table-primary'><tr><th>Faculty Dean</th></tr></table>";
        while ($row = mysqli_fetch_assoc($result)) {
            $val = $row['dean_name'];
            echo "$val  </br>";
        }
        ?></div>
        <div class="col">
            <?php
            $query = "SELECT dept_name FROM `deans`";
            $result = mysqli_query($conn, $query);
            echo "<table class='table-primary'><tr><th>Faculty Dept</th></tr></table>";
            while ($row = mysqli_fetch_assoc($result)) {
                $val = $row['dept_name'];
                echo "$val  </br>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
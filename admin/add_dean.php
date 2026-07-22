<?php
include "../db_connect.php";
if (isset($_POST['ins_dean'])) {
    echo "<script>added successfull</script>";
    $Dean_name = $_POST['Dean_name'];
    $Dept_name = $_POST['Dept_name'];
    $query = "insert into `Deans`(Dean_name,faculty_name) VALUES('$Dean_name','$Dept_name')";
    mysqli_query($conn, $query);
}

?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Add Dean</title>
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
                <a href="index.php" class="--bs-body-bg">Admin</a>
                <div class="right">kl</div>
            </div>
        </nav>
    </header>
    <main>
        <p class="text-center my-5">To add a new Dean Please Fill Below:</p>
        <div class="container w-50 border p-4">
            <form method="post">
                <div class="mb-3">
                    <label for="dean_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="dean_name" name="Dean_name">
                </div>
                <div class="mb-3">
                    <label for="Dept_name" class="form-label">feculty name</label>
                    <select class="form-select" name="Dept_name" aria-label="Default select example">
                        <option selected disabled>Open this select menu</option>
                        <?php
                        $query = "SELECT faculty_full_name FROM `faculty`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['faculty_full_name'];
                            echo "<option value='" . htmlspecialchars($val) . "' name='Dept_name'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>

                </div>
                <!-- <div class="mb-3">
                    <label for="dean_name" class="form-label">Department</label>
                    <input type="number" class="form-control" id="dean_name" aria-describedby="emailHelp">
                </div> -->
                <input type="submit" name="ins_dean" id="ins_dean">
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
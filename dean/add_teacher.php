<?php 
include "../db_connect.php";
if(isset($_POST['add_teacher'])){
    $teacher_name=$_POST['teacher_name'];
    $dept_name=$_POST['dept_name'];
    $query="insert into `teachers`(name,faculty) VALUES('$teacher_name','$dept_name')";
    if(mysqli_query($conn,$query)){
        echo "Aaaaaaa";
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Add Teachers</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body>  <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                <a href="index.php" class="--bs-body-bg">Home</a>
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>
     
<main>
   <div class="container w-50 my-5 border p-3">
        <form method="post">
            <div class="mb-3">
                <label for="teacher_name" class="form-label">Teacher name</label>
                <input type="text" class="form-control" name="teacher_name" id="teacher_name" required>
                     <select class="form-select" name="dept_name" aria-label="Default select example">
                        <option selected disabled>Open this select menu</option>
                        <?php
                        $query = "SELECT dep_name FROM `departments`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['dep_name'];
                            echo "<option value='" . htmlspecialchars($val) . "' name='dept_name'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
                <input type="submit" class="btn btn-primary mt-3" name="add_teacher">
        </form>
    </div>
</main>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>

<?php 
include "../db_connect.php";
if(isset($_POST['insert_subject'])){
$course_name=$_POST['course_name'];
$subject_name=$_POST['subject_name'];
$query="insert into `subjects` (subject_name,course_name) VALUES('$subject_name','$course_name')";
if(mysqli_query($conn,$query)){
    echo "success";
}
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
                   <div class="mb-3">
                    <label for="Dept_name" class="form-label">feculty name</label>
                    <select class="form-select" name="Dept_name" aria-label="Default select example">
                        <option selected disabled>Open this select menu</option>
                        <?php
                        $query = "SELECT dep_name FROM `departments`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['dep_name'];
                            echo "<option value='" . htmlspecialchars($val) . "' name='Dept_name'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>

                </div>
                <label for="teacher_name" class="form-label">subject name</label>
                <input type="text" class="form-control" name="subject_name" id="subject_name" >
                <input type="submit" class="btn btn-primary mt-3" name="insert_subject">
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

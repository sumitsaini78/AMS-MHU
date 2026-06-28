<?php
include "../db_connect.php";
if (isset($_POST['Allocate_Subject'])) {
    $selected_teacher = $_POST['selected_teacher'];
    // getting teacher name and id separate
     $parts = explode('--', $selected_teacher, 2);
     $teacher_name=$parts[0];
     $teacher_id=$parts[1];
    $selected_course = $_POST['selected_course'];
    // geeting sub id and name seprately
      $parts = explode('--', $selected_course, 2);
     $course_name=$parts[0];
     $sub_id=$parts[1];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    echo  $teacher_name ,$teacher_id, $sub_id,$course_name,$year,$semester ;
    $query="insert into `subjected_teacher` (teacher_id,sub_id,teacher_name,subject_name,year,semester) VALUES('$teacher_id','$sub_id','$teacher_name','$course_name','$year','$semester')";
    if(mysqli_query($conn,$query)){
        echo "successfully added";
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Assign-Subject</title>
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
                <a href="index.php" class="--bs-body-bg">Home</a>
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container w-50 border mt-3">
            <p class="text-center bg-body-secondary">Subject Teacher Allotment.</p>
            <form method="post">
                <!-- select teacher -->
                <label for="select_teacher">Select Teacher</label>
                <select class="form-select" name="selected_teacher" id="select_course"
                    aria-label="Default select example">
                    <option selected disabled>Select Teacher</option>
                    <?php
                    $query1 = "SELECT id,name FROM `teachers` WHERE faculty = 'FOCBS'";
                    $result1 = mysqli_query($conn, $query1);

                    if ($result1) {
                        while ($row = mysqli_fetch_assoc($result1)) {
                            $val = $row['name'];
                            $teacher_id = $row['id'];
                            $combined_value = $val. "--"  .$teacher_id ;
                            // Wrap the value in HTML option tags
                            echo "<option value='$combined_value'>" . htmlspecialchars($val) . "</option> ";
                        }
                    }
                    ?>
                </select>
                <label for="select_course">Select Course</label>
                <select class="form-select" name="selected_course" id="select_course"
                    aria-label="Default select example">
                    <option selected disabled>Select course</option>
                    <?php
                    // 1. Fixed the SQL operator from '==' to '='
                    $query = "SELECT course_id,subject_name FROM `courses` WHERE dept_name = 'FOCBS'";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // 2. Fixed the array key to match the columns selected in your query
                            $course_id = $row['course_id'];
                            $subject_name = $row['subject_name'];
                            $combined_sub_id=$subject_name. "--" .$course_id;
                            // 3. Removed duplicate 'name' attribute inside <option>
                            echo "<option value='$combined_sub_id'>$subject_name</option> ";
                        }
                    }
                    ?>
                </select>
                <div class="mb-3">

                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <!-- 2. Moved validation attributes here into the input field -->
                    <select class="form-select" aria-label="Default select example" name="year">
                        <option selected>Year select</option>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                        <option value="3">3rd</option>
                        <option value="4">4th</option>
                        <option value="5">5th</option>
                    </select>
                    <div class="invalid-feedback">Please enter exactly 3 letters (A-Z or a-z).</div>
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <!-- 2. Moved validation attributes here into the input field -->
                    <select class="form-select" aria-label="Default select example" name="semester">
                        <option selected>Semester select</option>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                        <option value="3">3rd</option>
                        <option value="4">4th</option>
                        <option value="5">5th</option>
                        <option value="6">6th</option>
                        <option value="7">7th</option>
                        <option value="8">8th</option>
                        <option value="9">9th</option>
                        <option value="10">10th</option>
                    </select>
                    <div class="invalid-feedback">Please enter exactly 3 letters (A-Z or a-z).</div>
                </div>
                <button type="submit" class="btn btn-primary" name="Allocate_Subject">Submit</button>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
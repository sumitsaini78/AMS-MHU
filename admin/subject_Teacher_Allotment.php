<?php
include "../db_connect.php";
$message = "";

if (isset($_POST['Allocate_Subject'])) {
    // Getting Teacher data
    $selected_teacher = $_POST['selected_teacher'];
    $parts_t = explode('--', $selected_teacher, 2);
    $teacher_name = $parts_t[0];
    $teacher_id = $parts_t[1];

    // Getting Course and Subject data
    $selected_course_info = $_POST['selected_course'];
    $parts_c = explode('--', $selected_course_info, 3);
    $subject_name = $parts_c[0];
    $sub_id = $parts_c[1];
    $subject_code = $parts_c[2];

    $course_name = $_POST['course_name']; // New: Getting selected course
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    $query = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) 
              VALUES ('$teacher_id', '$sub_id', '$teacher_name', '$subject_name', '$course_name', '$year', '$semester', '$subject_code')";
    
    if (mysqli_query($conn, $query)) {
        $message = '<div class="alert alert-success mt-3">Successfully allocated ' . htmlspecialchars($subject_name) . ' to ' . htmlspecialchars($teacher_name) . '!</div>';
    } else {
        $message = '<div class="alert alert-danger mt-3">Error: ' . mysqli_error($conn) . '</div>';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Assign-Subject | Mhu-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold">Mhu-AMS <span class="text-primary">Dean</span></span>
        <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
    </div>
</nav>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Subject Teacher Allotment</h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>
                    <form method="post">
                        <!-- Select Teacher -->
                        <div class="mb-3">
                            <label class="form-label">Select Teacher</label>
                            <select class="form-select" name="selected_teacher" required>
                                <option value="" selected disabled>Select Teacher</option>
                                <?php
                                $q = mysqli_query($conn, "SELECT id, name FROM `teachers` WHERE faculty = 'FOCBS'");
                                while ($row = mysqli_fetch_assoc($q)) {
                                    echo "<option value='{$row['name']}--{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Select Course -->
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select class="form-select" name="course_name" required>
                                <option value="" selected disabled>Select Course</option>
                                <?php
                                $q = mysqli_query($conn, "SELECT DISTINCT course_name FROM `courses_list` WHERE faculty_name='Faculty of Commerce & Business Studies'");
                                while ($row = mysqli_fetch_assoc($q)) {
                                    echo "<option value='{$row['course_name']}'>{$row['course_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Select Subject -->
                        <div class="mb-3">
                            <label class="form-label">Select Subject</label>
                            <select class="form-select" name="selected_course" required>
                                <option value="" selected disabled>Select Subject</option>
                                <?php
                                $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code FROM `subjects` WHERE dept_name = 'FOCBS'");
                                while ($row = mysqli_fetch_assoc($q)) {
                                    echo "<option value='{$row['subject_name']}--{$row['course_id']}--{$row['subject_code']}'>{$row['subject_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Year</label>
                                <select class="form-select" name="year" required>
                                    <option value="" selected disabled>Select</option>
                                    <option value="1">1st</option><option value="2">2nd</option>
                                    <option value="3">3rd</option><option value="4">4th</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester</label>
                                <select class="form-select" name="semester" required>
                                    <option value="" selected disabled>Select</option>
                                    <?php for($i=1; $i<=10; $i++) echo "<option value='$i'>{$i}th</option>"; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" name="Allocate_Subject">Confirm Allocation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
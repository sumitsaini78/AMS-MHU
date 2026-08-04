<?php
include "../db_connect.php";
session_start();
$faculty_name = $_SESSION['faculty_name'];    
$message = "";

if (!isset($_POST['course_submit'])) {
    if (!isset($_POST['course_name'])) {
        header("Location: ./index.php");
        exit;
    }
}
$course_name = isset($_POST['course_name']) ? $_POST['course_name'] : '';

if (isset($_POST['Allocate_Subject'])) {
    // Getting Teacher data
    $selected_teacher = $_POST['selected_teacher'];
    $parts_t = explode('--', $selected_teacher, 2);
    $teacher_name = $parts_t[0];
    $teacher_id = $parts_t[1];
    
    // Getting Course and Subject data
    $selected_course_info = $_POST['selected_course'];
    $parts_c = explode('--', $selected_course_info, 5);
    $subject_name = $parts_c[0];
    $sub_id = $parts_c[1];
    $subject_code = $parts_c[2];
    $year = $parts_c[3];
    $semester = $parts_c[4];

    $course_name = $_POST['course_name'];

    // Check if this teacher is already allocated this specific subject code
    $check_dup_query = "SELECT * FROM `subjected_teacher` WHERE teacher_id = '$teacher_id' AND sub_id = '$sub_id' AND subject_code = '$subject_code'";
    $check_dup_result = mysqli_query($conn, $check_dup_query);

    if (mysqli_num_rows($check_dup_result) > 0) {
        // Duplicate found
        $message = '<div class="alert alert-warning mt-3">Warning: ' . htmlspecialchars($teacher_name) . ' is already allocated to ' . htmlspecialchars($subject_name) . ' (' . htmlspecialchars($subject_code) . ').</div>';
    } else {
        // No duplicate, proceed with insertion
        $query = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) 
                  VALUES ('$teacher_id', '$sub_id', '$teacher_name', '$subject_name', '$course_name', '$year', '$semester', '$subject_code')";

        if (mysqli_query($conn, $query)) {
            $message = '<div class="alert alert-success mt-3">Successfully allocated ' . htmlspecialchars($subject_name) . ' to ' . htmlspecialchars($teacher_name) . '!</div>';
        } else {
            $message = '<div class="alert alert-danger mt-3">Error: ' . mysqli_error($conn) . '</div>';
        }
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
                        <h5 class="mb-0">Subject Teacher Allotment For <mark><?php echo htmlspecialchars($course_name); ?></mark></h5>
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
                                    $q = mysqli_query($conn, "SELECT id, name FROM `teachers` WHERE faculty = '$faculty_name'");
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        echo "<option value='{$row['name']}--{$row['id']}'>{$row['name']}</option>";
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
                                    $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code, year, semester FROM `subjects` WHERE faculty_name = '$faculty_name' AND course_name = '$course_name'"); 
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        $val = "{$row['subject_name']}--{$row['course_id']}--{$row['subject_code']}--{$row['year']}--{$row['semester']}";
                                        $text = "{$row['subject_name']} (Year: {$row['year']}, Sem: {$row['semester']}) - [{$row['subject_code']}]";
                                        echo "<option value='$val'>$text</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>">
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
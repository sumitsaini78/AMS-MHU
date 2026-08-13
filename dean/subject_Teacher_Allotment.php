<?php
include "../db_connect.php";
session_start();

// Sanitize session variable
$session_faculty = mysqli_real_escape_string($conn, $_SESSION['faculty_name']);    
$message = "";

// 1. Fetching both Short Name and Full Name from the faculty table
$faculty_short_name = $session_faculty; // Default fallback
$faculty_full_name = $session_faculty;  // Default fallback

$fac_query = mysqli_query($conn, "SELECT faculty_name, faculty_full_name FROM `faculty` WHERE faculty_full_name = '$session_faculty' OR faculty_name = '$session_faculty' LIMIT 1");
if ($fac_row = mysqli_fetch_assoc($fac_query)) {
    $faculty_short_name = $fac_row['faculty_name'];       // Short Name (e.g., FOCBS)
    $faculty_full_name = $fac_row['faculty_full_name'];   // Full Name (e.g., FACULTY OF COMMERCE...)
}

if (!isset($_POST['course_submit'])) {
    if (!isset($_POST['course_name'])) {
        header("Location: ./index.php");
        exit;
    }
}
$course_name = isset($_POST['course_name']) ? mysqli_real_escape_string($conn, $_POST['course_name']) : '';

if (isset($_POST['Allocate_Subject'])) {
    // Getting Teacher data
    $selected_teacher = $_POST['selected_teacher'];
    $parts_t = explode('--', $selected_teacher, 2);
    $teacher_name = mysqli_real_escape_string($conn, $parts_t[0]);
    $teacher_id = mysqli_real_escape_string($conn, $parts_t[1]);
    
    // Getting Course and Subject data
    $selected_course_info = $_POST['selected_course'];
    $parts_c = explode('--', $selected_course_info, 5);
    $subject_name = mysqli_real_escape_string($conn, $parts_c[0]);
    $sub_id = mysqli_real_escape_string($conn, $parts_c[1]);
    $subject_code = mysqli_real_escape_string($conn, $parts_c[2]);
    $year = mysqli_real_escape_string($conn, $parts_c[3]);
    $semester = mysqli_real_escape_string($conn, $parts_c[4]);

    $course_name_post = mysqli_real_escape_string($conn, $_POST['course_name']);

    // UPDATED: Check if this specific subject is already allocated to ANY teacher
    // Removed teacher_id from WHERE clause to prevent ANY duplicate allocation
    $check_dup_query = "SELECT teacher_name FROM `subjected_teacher` 
                        WHERE sub_id = '$sub_id' 
                        AND course_name = '$course_name_post' 
                        AND year = '$year' 
                        AND semester = '$semester'";
                        
    $check_dup_result = mysqli_query($conn, $check_dup_query);

    if (mysqli_num_rows($check_dup_result) > 0) {
        // Duplicate found: Subject is already assigned to someone
        $existing_data = mysqli_fetch_assoc($check_dup_result);
        $assigned_teacher = $existing_data['teacher_name'];
        
        $message = '<div class="alert alert-warning mt-3">Warning: <strong>' . htmlspecialchars($subject_name) . '</strong> is already allocated to <strong>' . htmlspecialchars($assigned_teacher) . '</strong> for ' . htmlspecialchars($course_name_post) . ' (Year: ' . htmlspecialchars($year) . ', Sem: ' . htmlspecialchars($semester) . ').</div>';
    } else {
        // No duplicate, proceed with insertion
        $query = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) 
                  VALUES ('$teacher_id', '$sub_id', '$teacher_name', '$subject_name', '$course_name_post', '$year', '$semester', '$subject_code')";

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

    <?php include 'dean_navbar.php'; ?>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Subject Teacher Allotment For <mark class="bg-light text-dark px-2 rounded"><?php echo htmlspecialchars($course_name) . ' (' . htmlspecialchars($faculty_short_name) . ')'; ?></mark></h5>
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
                                    $q = mysqli_query($conn, "SELECT id, name FROM `teachers` WHERE faculty = '$faculty_full_name'");
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
                                    $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code, year, semester FROM `subjects` WHERE faculty_name = '$faculty_short_name' AND course_name = '$course_name'"); 
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

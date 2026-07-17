<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['teacher_id'];

// Fetch teacher credentials securely via prepared statement
$query = "SELECT name, faculty FROM teachers WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
    $faculty_name = $teacher['faculty'];
} else {
    header("Location: ../logout.php");
    exit;
}

// 2. Direct Landing Logic: Capture subject details straight from database mappings
if (!isset($_POST['subject_name'])) {
    header("Location: index.php");
    exit;
}

$subject_name = $_POST['subject_name'];

// Automatically grab the associated course metadata from the assigned record table
$meta_query = "SELECT subject_code, course_name, year, semester FROM `subjected_teacher` WHERE teacher_id = ? AND subject_name = ? LIMIT 1";
$meta_stmt = mysqli_prepare($conn, $meta_query);
mysqli_stmt_bind_param($meta_stmt, "is", $id, $subject_name);
mysqli_stmt_execute($meta_stmt);
$meta_result = mysqli_stmt_get_result($meta_stmt);

if ($meta_result && mysqli_num_rows($meta_result) == 1) {
    $meta = mysqli_fetch_assoc($meta_result);
    $subject_code = !empty($meta['subject_code']) ? $meta['subject_code'] : 'N/A';
    $course_name = !empty($meta['course_name']) ? $meta['course_name'] : 'N/A';
    $year = $meta['year'];
    $semester = $meta['semester'];
} else {
    // Fallback parameters if sent via form POST directly
    $subject_code = isset($_POST['subject_code']) ? $_POST['subject_code'] : 'N/A';
    $course_name = isset($_POST['course_name']) ? $_POST['course_name'] : 'N/A';
    $year = isset($_POST['year']) ? $_POST['year'] : 'N/A';
    $semester = isset($_POST['semester']) ? $_POST['semester'] : 'N/A';
}

$date_of_attendance = date('dmy');
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Insert Attendance | Live Session</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body {
            background-color: #f4f6f9;
        }

        .session-banner {
            background: #ffffff;
            border-left: 5px solid #0d6efd;
            border-radius: 8px;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow py-2">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-4 fw-bold text-warning d-flex align-items-center">
                    <i class="fa-solid fa-graduation-cap me-2"></i> MHU-AMS
                </span>
                <div class="d-flex align-items-center ms-auto">
                    <span
                        class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1 rounded-pill small me-3 d-none d-sm-inline">
                        <i class="fa-solid fa-user-tie me-1 text-warning"></i> Prof.
                        <?php echo htmlspecialchars($teacher_name); ?>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-info me-2"><i class="fa-solid fa-house me-1"></i>
                        Home</a>
                    <a href="../logout.php" class="btn btn-sm btn-outline-danger"><i
                            class="fa-solid fa-power-off"></i></a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-9">

                <!-- Session metadata details layout banner -->
                <div class="session-banner card shadow-sm p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span
                                class="badge bg-primary mb-2 font-monospace tracking-wide"><?php echo htmlspecialchars($subject_code); ?></span>
                            <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($subject_name); ?></h3>
                            <p class="text-muted mb-0 small">
                                <i class="fa-solid fa-square-poll-horizontal me-1"></i>
                                <strong><?php echo htmlspecialchars($course_name); ?></strong>
                                <span class="mx-2 text-black-50">|</span> Year: <?php echo htmlspecialchars($year); ?>
                                <span class="mx-2 text-black-50">|</span> Sem:
                                <?php echo htmlspecialchars($semester); ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-dark px-3 py-2 fs-6 rounded-3 shadow-sm font-monospace">
                                <i class="fa-regular fa-calendar me-2"></i><?php echo date('d-M-Y'); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Students attendance sheet form -->
                <form action="save_attendance.php" method="POST">
                    <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
                    <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                    <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                    <input type="hidden" name="date_of_attendance"
                        value="<?php echo htmlspecialchars($date_of_attendance); ?>">

                    <div class="card shadow-sm border-0 overflow-hidden">
                        <table class='table table-striped table-hover table-bordered text-center align-middle mb-0'>
                            <thead class='table-dark'>
                                <tr>
                                    <th style="width: 25%;">Roll Number</th>
                                    <th class="text-start ps-4">Student Name</th>
                                    <th style="width: 35%;">
                                        Attendance Status 
                                        <button type="button" class="btn btn-sm btn-outline-light ms-2" onclick="checkAll()">Check All</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // JOIN query pulling the missing roll_number while safely filtering for the active class session context
                                $students_query = "SELECT ss.student_name, s.roll_number 
                                                   FROM subjected_student ss 
                                                   INNER JOIN students s ON ss.student_name = s.name 
                                                   WHERE ss.subject_name = ?";
                                
                                $students_stmt = mysqli_prepare($conn, $students_query);
                                mysqli_stmt_bind_param($students_stmt, "s", $subject_name);
                                mysqli_stmt_execute($students_stmt);
                                $students_result = mysqli_stmt_get_result($students_stmt);

                                if ($students_result && mysqli_num_rows($students_result) > 0) {
                                    while ($row = mysqli_fetch_assoc($students_result)) {
                                        $roll_number = $row['roll_number'];
                                        $student_name = $row['student_name'];

                                        echo "
                                        <tr>
                                            <td class='font-monospace fw-bold'>" . htmlspecialchars($roll_number) . "</td>
                                            <td class='text-start ps-4 fw-medium text-secondary'>" . htmlspecialchars($student_name) . "</td>
                                            <td>
                                                <input type='hidden' name='student_names[" . htmlspecialchars($roll_number) . "]' value='" . htmlspecialchars($student_name) . "'>
                                                <input type='hidden' name='attendance[" . htmlspecialchars($roll_number) . "]' value='Absent'>
                                                <div class='form-check d-flex justify-content-center m-0'>
                                                    <input class='form-check-input p-2 border border-secondary cursor-pointer' type='checkbox' style='transform: scale(1.3);' name='attendance[" . htmlspecialchars($roll_number) . "]' value='Present'>
                                                </div>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-muted py-4'><i class='fa-solid fa-folder-open me-2'></i>No mapped student records found for this subject.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center my-4">
                        <button type="submit" name="insert_attendance"
                            class="btn btn-success btn-lg px-5 shadow fw-bold"><i
                                class="fa-solid fa-floppy-disk me-2"></i>Save Attendance</button>
                    </div>
                </form>  

            </div>
        </div>
    </main>

    <script>
        function checkAll() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
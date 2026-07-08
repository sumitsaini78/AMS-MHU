<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ../index.php"); 
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['teacher_id'];
$query = "SELECT * FROM teachers WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
}
// get POST data
$subject_name = $_POST['subject_name'];
$subject_code = $_POST['subject_code'];
$faculty_name = $_POST['faculty_name'];
$course_name = $_POST['course_name'];
$year = $_POST['year'];
$semester = $_POST['semester'];
$date_of_attendance = date('dmy'); 
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Insert Attendance</title>
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
                <a href="index.php" class="text-white text-decoration-none">Home</a>
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>
    
    <main class="container my-4">
        <!-- Centering Row Wrapper -->
        <div class="row justify-content-center">
            <!-- Table and Details Column -->
            <div class="col-md-10 col-lg-8">
                
                <!-- showing details for the selected subject -->
                <div class="bg-warning-subtle border p-3 rounded mb-3 text-center">
                    <strong><?php echo htmlspecialchars($subject_name) . " (" . htmlspecialchars($subject_code) . ")"; ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($faculty_name) . " | " . htmlspecialchars($course_name) . " | Year " . htmlspecialchars($year) . " | Sem " . htmlspecialchars($semester); ?></small>
                </div>

                <!-- students list for marking attendance -->
                <form action="save_attendance.php" method="POST">
                    <!-- Forward metadata parameters as hidden payload fields -->
                    <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
                    <input type="hidden" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>">
                    <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">

                    <table class='table table-striped table-bordered text-center align-middle'> 
                        <thead class='table-dark'>
                            <tr>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th>Attendance Status (Checked = Present)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM students";
                            $result = mysqli_query($conn, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Map row data using your database's column names
                                    $roll_number = isset($row['roll_number']) ? $row['roll_number'] : $row['id'];
                                    $student_name = $row['name'];
                                    
                                    echo "
                                    <tr>
                                        <td>" . htmlspecialchars($roll_number) . "</td>
                                        <td class='text-start ps-3'>
                                            " . htmlspecialchars($student_name) . "
                                            <input type='hidden' name='student_names[" . htmlspecialchars($roll_number) . "]' value='" . htmlspecialchars($student_name) . "'>
                                        </td>
                                        <td>
                                            <!-- Fallback input tracking mechanism: Unchecked defaults to Absent -->
                                            <input type='hidden' name='attendance[" . htmlspecialchars($roll_number) . "]' value='Absent'>
                                            <div class='form-check d-flex justify-content-center'>
                                                <input class='form-check-input p-2 border border-secondary' type='checkbox' style='transform: scale(1.3);' name='attendance[" . htmlspecialchars($roll_number) . "]' value='Present' >
                                            </div>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-muted py-3'>No students found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
    <input type="hidden" name="date_of_attendance" id="date_of_attendance" value="<?php echo htmlspecialchars($date_of_attendance); ?>">
                    <!-- Action control submit block -->
                    <div class="text-center my-4">
                        <button type="submit" name="insert_attendance" class="btn btn-success px-5 py-2 fw-bold shadow-sm">Save Attendance</button>
                    </div>
                </form>
                
            </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>

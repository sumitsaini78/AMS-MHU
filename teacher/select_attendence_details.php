<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['teacher_id'];
$teacher_name = "";

// Use prepared statement to fetch Teacher info
$query = "SELECT name FROM teachers WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($teacher = mysqli_fetch_assoc($result)) {
        $teacher_name = $teacher['name'];
    }
    mysqli_stmt_close($stmt);
}

// 2. Safely check if subject_name was posted BEFORE assigning it
if (!isset($_POST['subject_name']) || empty(trim($_POST['subject_name']))) {
    header("Location: teacher_subjects.php");
    exit;
}

$subject_name = $_POST['subject_name'];
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Select Attendance Details</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                <div class="right d-flex align-items-center">
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small me-3">
                        Welcome, <?php echo htmlspecialchars($teacher_name); ?>
                    </span>
                    <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
                </div>
            </div>
        </nav>
    </header>
    
    <main>
        <!-- Adjusted width class from w-25 to handle responsive viewports -->
        <div class="container mt-5 col-12 col-md-6 col-lg-4 border p-4 bg-light shadow rounded">
            <form action="insert_attendence.php" method="POST">
                
                <div class="mb-3">
                    <label class="form-label d-block">Selected Subject</label>
                    <p class="fs-5 fw-semibold text-primary"><mark class="px-2 rounded"><?php echo htmlspecialchars($subject_name); ?></mark></p>
                    <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject_name); ?>">
                </div>

                <div class="mb-3">
                    <label for="subject_code" class="form-label">Select Subject Code</label>
                    <select class="form-select" name="subject_code" id="subject_code" required>
                        <option selected disabled value="">Subject-Code</option>
                        <?php
                        // Prepared statement for dynamic user query
                        $query = "SELECT subject_code FROM `subjected_teacher` WHERE teacher_id = ?";
                        if ($stmt = mysqli_prepare($conn, $query)) {
                            mysqli_stmt_bind_param($stmt, "i", $id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $code = $row['subject_code'];
                                echo "<option value='" . htmlspecialchars($code) . "'>" . htmlspecialchars($code) . "</option>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="faculty_name" class="form-label">Faculty</label>
                    <select class="form-select" name="faculty_name" id="faculty_name" required>
                        <option selected disabled value="">Select Faculty</option>
                        <?php
                        $query = "SELECT full_name FROM `departments`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['full_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="course_name" class="form-label">Course</label>
                    <select class="form-select" name="course_name" id="course_name" required>
                        <option selected disabled value="">Select Course</option>
                        <?php
                        $query = "SELECT course_name FROM `courses`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['course_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" name="year" id="year" required>
                        <option selected disabled value="">Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                    </select>
                </div>

                <!-- Fixed unclosed and nested div structural bug here -->
                <div class="mb-4">
                    <label for="semester" class="form-label">Semester</label>
                    <select class="form-select" name="semester" id="semester" required>
                        <option selected disabled value="">Semester</option>
                        <option value="1">1st sem</option>
                        <option value="2">2nd sem</option>
                        <option value="3">3rd sem</option>
                        <option value="4">4th sem</option>
                        <option value="5">5th sem</option>
                    </select>
                </div>

                <div class="d-grid">
                    <input type="submit" class="btn btn-primary" value="Proceed to Attendance">
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the Dean is actually logged in
if (!isset($_SESSION['dean_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ./index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['dean_id'];
$query = "SELECT * FROM deans WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $dean = mysqli_fetch_assoc($result);
    $dean_name = $dean['Dean_name'];
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Dean</title>
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
                <div class="right"> <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome, <mark><?php echo $dean_name; ?></mark>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a><a href=""></a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="row">

                <div class="col mt-4">
                    <a href="add_Faculty.php">
                        <button type="button" class="btn btn-info">Add Faculty</button>
                    </a>
                    <a href="add_Students.php">
                        <button type="button" class="btn btn-info">Add Students</button>
                    </a>
                    <a href="add_Subjects.php">
                        <button type="button" class="btn btn-info">Add Subjects</button>
                    </a>
                    <a href="add_Teacher.php">
                        <button type="button" class="btn btn-info">Add Teachers</button>
                    </a>
                    <a href="subject_Teacher_Allotment.php">
                        <button type="button" class="btn btn-info">Assign-Subject</button>
                    </a>
                    <a href="add_bulk_subject.php">
                        <button type="button" class="btn btn-info">Add Subjects(Bulk)</button>
                    </a>

                </div>
            </div>
        </div>
    </main>
    <main>
        <div class="table-responsive shadow-sm rounded border w-75 m-auto mt-3">
            <!-- Hidden Form that handles the actual submission redirect -->
            <form id="courseRedirectForm" method="POST" action="course_details.php">
                <input type="hidden" id="selectedCourseInput" name="course_name" value="">
            </form>

            <div class="table-responsive shadow-sm rounded border">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 8%;">#</th>
                            <th scope="col" style="width: 37%;">Course Track</th>
                            <th scope="col" style="width: 15%;">Total Lectures</th>
                            <th scope="col" style="width: 15%;">Total Present Calls</th>
                            <th scope="col" style="width: 25%;">Attendance Performance Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Filter query explicitly for BBA, BCom, MCom, and MBA
                        $query = "SELECT 
                        course,
                        COUNT(DISTINCT date_of_attendence) as total_lectures,
                        COUNT(*) as total_student_records,
                        SUM(CASE WHEN LOWER(attendance_status) IN ('present', '1') THEN 1 ELSE 0 END) as present_count
                      FROM `attendance` 
                      WHERE course IN ('BBA', 'BCom', 'MCom', 'MBA')
                      GROUP BY course 
                      ORDER BY FIELD(course, 'BBA', 'BCom', 'MCom', 'MBA')";

                        $result = mysqli_query($conn, $query);
                        $index = 1;

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $course_name = $row['course'];
                                $total_lectures = $row['total_lectures'];
                                $total_records = $row['total_student_records'];
                                $present_count = $row['present_count'];

                                // Calculate percentage safely
                                $percentage = ($total_records > 0) ? round(($present_count / $total_records) * 100, 2) : 0;

                                // Set dynamic styling colors based on performance
                                if ($percentage >= 75) {
                                    $badge_class = "text-success fw-bold";
                                    $progress_class = "bg-success";
                                } elseif ($percentage >= 50) {
                                    $badge_class = "text-warning fw-bold";
                                    $progress_class = "bg-warning";
                                } else {
                                    $badge_class = "text-danger fw-bold";
                                    $progress_class = "bg-danger";
                                }

                                // Escaping the course name for safe JavaScript passing
                                $js_course = htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8');

                                echo "<tr>";
                                echo "<th scope='row' class='fw-bold text-muted'>" . $index . "</th>";

                                // Added an interactive link button styling to make the course name clickable
                                echo "<td>
                            <button type='button' class='btn btn-link p-0 fw-bold text-decoration-none text-primary fs-6 text-start' onclick='submitCourse(\"{$js_course}\")'>
                                <i class='fa-solid fa-folder-open me-2 text-warning'></i>" . htmlspecialchars($course_name) . "
                            </button>
                          </td>";

                                echo "<td><span class='badge bg-secondary px-2.5 py-1.5'>" . $total_lectures . " Lectures</span></td>";
                                echo "<td><span class='badge bg-info text-dark px-2.5 py-1.5 fw-semibold'>" . $present_count . " Checked</span></td>";
                                echo "<td>
                            <div class='d-flex align-items-center gap-2'>
                                <div class='progress flex-grow-1' style='height: 8px;'>
                                    <div class='progress-bar {$progress_class}' role='progressbar' style='width: {$percentage}%' aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'></div>
                                </div>
                                <span class='{$badge_class}' style='min-width: 55px; text-align: right;'>" . $percentage . "%</span>
                            </div>
                          </td>";
                                echo "</tr>";

                                $index++;
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted'><i class='fa-solid fa-triangle-exclamation me-2'></i> No attendance records found for BBA, BCom, MCom, or MBA.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- JavaScript to intercept click and inject value into target form inputs dynamically -->
            <script>
                function submitCourse(courseName) {
                    document.getElementById('selectedCourseInput').value = courseName;
                    document.getElementById('courseRedirectForm').submit();
                }
            </script>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the Dean is actually logged in
if (!isset($_SESSION['dean_id'])) {
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
} else {
    $dean_name = "Dean";
}

// 3. Live count for pending attendance correction requests
$correction_count_query = "SELECT COUNT(*) as pending_total FROM `attendance_corrections` WHERE status = 'Pending'";
$correction_count_result = mysqli_query($conn, $correction_count_query);
$pending_count = 0;
if ($correction_count_result) {
    $count_row = mysqli_fetch_assoc($correction_count_result);
    $pending_count = $count_row['pending_total'];
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Dean Dashboard | MHU-AMS</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .navbar-brand sub { bottom: 0; font-size: 0.65rem; letter-spacing: 1px; }
        .action-card { border: none; border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease; background: #ffffff; }
        .action-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08) !important; }
        .table-card { border: none; border-radius: 14px; background: #ffffff; overflow: hidden; }
        .table th { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap text-warning me-2"></i>MHU-AMS <sub class="text-primary text-uppercase ms-1 fw-semibold">Dean</sub>
                </a>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary border-opacity-50 px-3 py-1.5 rounded-pill small">
                        <i class="fa-solid fa-user-tie me-1 text-warning"></i> Welcome, <span class="fw-semibold"><?php echo htmlspecialchars($dean_name); ?></span>
                    </span>
                    
                    <!-- NEW: Attendance Correction Action Link Component with Counter Badge -->
                    <a href="manage_corrections.php" class="btn btn-sm role-button position-relative px-3 shadow-sm <?php echo ($pending_count > 0) ? 'btn-warning fw-bold text-dark' : 'btn-outline-warning text-white'; ?>">
                        <i class="fa-solid fa-bell-conflict fa-triangle-exclamation me-1"></i> Corrections Request
                        <?php if ($pending_count > 0) { ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow border border-light animate__animated animate__pulse animate__infinite">
                                <?php echo $pending_count; ?>
                            </span>
                        <?php } ?>
                    </a>
                    
                    <a href="../logout.php" class="btn btn-sm btn-danger px-3 shadow-sm ms-2"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <!-- Section: Overview Header -->
        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h2 class="fw-bold text-dark">Dean Administrative Console</h2>
                <p class="text-muted small">Manage faculty roster indices, course mapping metrics, and student profiles execution directories.</p>
            </div>
        </div>

        <!-- Section: Grid Action Control Items -->
        <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-6 mb-5">
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-network-wired"></i></div>
                    <a href="add_Faculty.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Add Faculty</a>
                </div>
            </div>
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-user-graduate"></i></div>
                    <a href="add_Students.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Add Students</a>
                </div>
            </div>
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-book-bookmark"></i></div>
                    <a href="add_Subjects.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Add Subjects</a>
                </div>
            </div>
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <a href="add_Teacher.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Add Teachers</a>
                </div>
            </div>
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-address-card"></i></div>
                    <a href="subject_Teacher_Allotment.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Assign Subject</a>
                </div>
            </div>
            <div class="col">
                <div class="action-card card h-100 shadow-sm text-center p-3">
                    <div class="fs-2 text-info mb-2"><i class="fa-solid fa-layer-group"></i></div>
                    <a href="add_bulk_subject.php" class="btn btn-sm btn-outline-info w-100 mt-auto fw-medium">Bulk Subjects</a>
                </div>
            </div>
        </div>

        <!-- Section: Analytics Stream Trackers Table -->
        <div class="row">
            <div class="col-12 col-xl-11 mx-auto">
                <div class="table-card card shadow-sm p-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="fs-4 text-secondary me-2"><i class="fa-solid fa-chart-bar text-warning"></i></span>
                        <h4 class="fw-bold text-dark mb-0">Operational Department Performance Index</h4>
                    </div>

                    <!-- Hidden Form that handles the actual submission redirect -->
                    <form id="courseRedirectForm" method="POST" action="course_details.php">
                        <input type="hidden" id="selectedCourseInput" name="course_name" value="">
                    </form>

                    <div class="table-responsive rounded border">
                        <table class="table table-hover align-middle mb-0 table-striped">
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

                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $course_name = $row['course'];
                                        $total_lectures = $row['total_lectures'];
                                        $total_records = $row['total_student_records'];
                                        $present_count = $row['present_count'];

                                        $percentage = ($total_records > 0) ? round(($present_count / $total_records) * 100, 2) : 0;

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

                                        $js_course = htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8');

                                        echo "<tr>";
                                        echo "<th scope='row' class='fw-bold text-muted'>" . $index . "</th>";
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
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <!-- JavaScript Injection Handler -->
    <script>
        function submitCourse(courseName) {
            document.getElementById('selectedCourseInput').value = courseName;
            document.getElementById('courseRedirectForm').submit();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ./index.php");
    exit;
}

$id = $_SESSION['teacher_id'];

// 2. Fetch all teacher credentials in a single concise transaction
$query = "SELECT name, faculty FROM teachers WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
    $faculty = $teacher['faculty'];

    // Storing essential details in session variables for site-wide use
    $_SESSION['teacher_name'] = $teacher_name;
} else {
    header("Location: ../logout.php");
    exit;
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Teacher Dashboard | MHU-AMS</title>
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

        .dashboard-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 16px;
            color: #ffffff;
        }

        .action-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .dropdown-item-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 0.5rem 1rem;
        }

        .dropdown-item-btn:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNavbar"
                    aria-controls="dashboardNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="dashboardNavbar">
                    <ul class="navbar-nav align-items-center me-3">

                        <!-- Mark Attendance Split Dropdown Menu -->
                        <li class="nav-item mx-1 my-1 my-lg-0 dropdown">
                            <div class="btn-group w-100">
                                <a href="teacher_subjects.php" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-calendar-check me-1"></i> Mark Attendance
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-1"
                                    style="min-width: 260px;">
                                    <li class="dropdown-header text-uppercase tracking-wider small-xs text-secondary">
                                        Quick Access Subjects</li>
                                    <?php
                                    $sub_query = "SELECT subject_name FROM `subjected_teacher` WHERE teacher_id = '$id' ORDER BY subject_name ASC";
                                    $sub_result = mysqli_query($conn, $sub_query);
                                    if (mysqli_num_rows($sub_result) > 0) {
                                        while ($row = mysqli_fetch_assoc($sub_result)) {
                                            $val = $row['subject_name'];

                                            if (preg_match('/^([A-Za-z0-9#-]+)[:\-\s](.*)$/', $val, $matches)) {
                                                $display_code = trim($matches[1]);
                                                $display_title = trim($matches[2]);
                                            } else {
                                                $parts = explode(' ', $val, 2);
                                                $display_code = (count($parts) > 1 && strlen($parts[0]) <= 7) ? $parts[0] : 'SUB';
                                                $display_title = (count($parts) > 1 && strlen($parts[0]) <= 7) ? $parts[1] : $val;
                                            }

                                            echo "<li>";
                                            // FIXED: Target changed straight to insert_attendance.php to support immediate bypass rules
                                            echo "<form action='insert_attendance.php' method='POST' class='m-0'>";
                                            echo "<input type='hidden' name='subject_name' value='" . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "'>";
                                            echo "<button type='submit' class='dropdown-item-btn small fw-medium text-dark d-flex align-items-center justify-content-between'>";
                                            echo "<span class='text-truncate me-2'><i class='fa-regular fa-file-lines me-2 text-muted'></i>" . htmlspecialchars($display_title) . "</span>";
                                            echo "<span class='badge bg-light text-secondary font-monospace border small-xs flex-shrink-0'>" . htmlspecialchars($display_code) . "</span>";
                                            echo "</button>";
                                            echo "</form>";
                                            echo "</li>";
                                        }
                                    } else {
                                        echo "<li><span class='dropdown-item text-muted small italic'>No subjects assigned</span></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item mx-1 my-1 my-lg-0 dropdown">
                            <button class="btn btn-sm btn-outline-light w-100 dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-chart-pie me-1"></i> View Reports
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item py-2 small fw-medium" href="subject_attendence.php"><i
                                            class="fa-solid fa-book me-2 text-muted"></i>Subject Overview</a></li>
                                <li><a class="dropdown-item py-2 small fw-medium" href="student_attendence.php"><i
                                            class="fa-solid fa-users me-2 text-muted"></i>Student Specific</a></li>
                            </ul>
                        </li>
                        <li class="nav-item mx-1 my-1 my-lg-0">
                            <a href="request_correction.php" class="btn btn-sm btn-outline-light w-100"><i
                                    class="fa-solid fa-triangle-exclamation me-1"></i> Correction</a>
                        </li>
                        <li class="nav-item mx-1 my-1 my-lg-0">
                            <a href="assign_student_subject.php" class="btn btn-sm btn-outline-light w-100"><i
                                    class="fa-solid fa-user-plus me-1"></i> Assign Subject</a>
                        </li>
                    </ul>

                    <div
                        class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mt-2 mt-lg-0 w-100 w-lg-auto">
                        <span
                            class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-inline-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome,
                            <?php echo htmlspecialchars($teacher_name); ?>
                            <span
                                class="badge bg-warning text-dark ms-2 rounded-pill fw-semibold"><?php echo htmlspecialchars($faculty); ?></span>
                        </span>
                        <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i
                                class="fa-solid fa-power-off me-1"></i> Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="dashboard-banner p-4 p-md-5 shadow mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span
                        class="badge bg-warning text-dark fw-bold mb-2 px-3 py-1.5 rounded-pill text-uppercase tracking-wider">AMS
                        Control Panel</span>
                    <h1 class="display-5 fw-bold mb-2">Hello, Professor <?php echo htmlspecialchars($teacher_name); ?>!
                    </h1>
                    <p class="lead text-white-50 mb-0">Welcome to your dashboard interface. Monitor analytics records,
                        file correction parameters, and initialize class roll calls.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <span style="font-size: 6rem;" class="text-white-50 opacity-25"><i
                            class="fa-solid fa-shield-halved"></i></span>
                </div>
            </div>
        </div>

        <h4 class="fw-bold text-dark mb-3 mt-5"><i class="fa-solid fa-layer-group text-muted me-2"></i>Quick Management
            Channels</h4>
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="action-card card shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="p-2 bg-primary-subtle text-primary rounded-3 fs-4 px-3"><i
                                class="fa-solid fa-calendar-check"></i></span>
                        <span class="text-muted small fw-medium font-monospace">Routine</span>
                    </div>
                    <h5 class="fw-bold text-dark">Record Sessions</h5>
                    <p class="text-muted small flex-grow-1">Select courses and catalog new live entry tracking lists
                        into the log servers.</p>
                    <a href="teacher_subjects.php" class="btn btn-sm btn-outline-primary w-100 mt-2 fw-semibold">Open
                        Panel</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="action-card card shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="p-2 bg-success-subtle text-success rounded-3 fs-4 px-3"><i
                                class="fa-solid fa-chart-line"></i></span>
                        <span class="text-muted small fw-medium font-monospace">Reports</span>
                    </div>
                    <h5 class="fw-bold text-dark">Course Metrics</h5>
                    <p class="text-muted small flex-grow-1">Extract calculated metrics, ranges, and percentage averages
                        sorted by subject profiles.</p>
                    <a href="subject_attendence.php" class="btn btn-sm btn-outline-success w-100 mt-2 fw-semibold">View
                        Metrics</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="action-card card shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="p-2 bg-warning-subtle text-warning-emphasis rounded-3 fs-4 px-3"><i
                                class="fa-solid fa-square-poll-vertical"></i></span>
                        <span class="text-muted small fw-medium font-monospace">Students</span>
                    </div>
                    <h5 class="fw-bold text-dark">Individual Tracking</h5>
                    <p class="text-muted small flex-grow-1">Audit profile records and open individual student monthly
                        calendar view setups.</p>
                    <a href="student_attendence.php"
                        class="btn btn-sm btn-outline-warning w-100 mt-2 fw-semibold text-dark">Browse Roster</a>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="action-card card shadow-sm p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="p-2 bg-danger-subtle text-danger rounded-3 fs-4 px-3"><i
                                class="fa-solid fa-wrench"></i></span>
                        <span class="text-muted small fw-medium font-monospace">Utility</span>
                    </div>
                    <h5 class="fw-bold text-dark">System Adjustments</h5>
                    <p class="text-muted small flex-grow-1">File logs for attendance adjustments or map new student
                        rosters to current items.</p>
                    <a href="request_correction.php"
                        class="btn btn-sm btn-outline-danger w-100 mt-2 fw-semibold">Configure Tools</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
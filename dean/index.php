<?php
include "../db_connect.php";
session_start();

// 1. Secure the page
if (!isset($_SESSION['dean_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['dean_id'];

// Fetch Dean Info
$stmt = $conn->prepare("SELECT * FROM deans WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $dean = $result->fetch_assoc();
    $dean_name = $dean['Dean_name'];
    $faculty_name = $dean['faculty_name'];
    $_SESSION['faculty_name'] = $faculty_name;
    $_SESSION['dean_name'] = $dean_name;
}

// Pending Corrections Count
$correction_count_query = "SELECT COUNT(*) as pending_total FROM attendance_corrections WHERE status = 'Pending'";
$correction_count_result = mysqli_query($conn, $correction_count_query);
$pending_count = ($correction_count_result) ? mysqli_fetch_assoc($correction_count_result)['pending_total'] : 0;

// Fetch courses for dropdowns once to avoid multiple queries
$courses_array = [];
$query = "SELECT course_name FROM courses_list WHERE faculty_name = ? ORDER BY course_name";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$courses_result = $stmt->get_result();
if ($courses_result) {
    while ($row = mysqli_fetch_assoc($courses_result)) {
        $courses_array[] = $row['course_name'];
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Dean Dashboard | MHU-AMS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .action-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
            background: #ffffff;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .table-container {
            border-radius: 15px;
            overflow: hidden;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
        }

        .navbar-brand sub {
            font-size: 0.6rem;
            letter-spacing: 2px;
        }

        /* Increased z-index for dropdown menus to prevent them from hiding behind cards/tables */
        .dropdown-menu {
            z-index: 9999 !important;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap text-warning me-2"></i> MHU-AMS
                    <sub class="text-primary ms-1">DEAN</sub>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white small d-none d-md-block"><i
                            class="fa-solid fa-user-tie me-2 text-warning"></i>Welcome,
                        <strong><?= htmlspecialchars($dean_name ?? 'Dean') ?></strong></span>
                    <a href="manage_corrections.php" class="btn btn-sm btn-outline-warning position-relative">
                        <i class="fa-solid fa-bell"></i>
                        <?php if ($pending_count > 0): ?>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $pending_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="../logout.php" class="btn btn-sm btn-danger px-3"><i class="fa-solid fa-power-off"></i></a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Dashboard Overview</h2>
                <p class="text-muted">Manage your department operations and     monitor daily attendance flow.</p>
            </div>
        </div>

        <!-- Action Cards Grid -->
        <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4 mb-4">
            <?php
            $actions = [
                ['Add Faculty & Departments', 'fa-network-wired', 'text-info', 'add_Faculty.php'],
                ['Add Students', 'fa-user-graduate', 'text-info', 'add_Students.php'],
                ['View Students', 'fa-users-viewfinder', 'text-primary', 'view_students.php'],
                ['Add Subjects', 'fa-book-bookmark', 'text-info', 'add_bulk_subject.php'],
                ['Add Teachers', 'fa-chalkboard-user', 'text-info', 'add_Teacher.php'],
                ['Manage Subjected Students', 'fa-user-pen', 'text-warning', 'dean_subjected_students.php'],
                ['Manage Subjected Teachers', 'fa-chalkboard-user', 'text-warning', 'dean_subjected_teachers.php']
            ];
            foreach ($actions as $act): ?>  
                <div class="col">
                    <div class="action-card card h-100 shadow-sm p-3 text-center">
                        <div class="fs-3 <?= $act[2] ?> mb-2"><i class="fa-solid <?= $act[1] ?>"></i></div>
                        <a href="<?= $act[3] ?>"
                            class="btn btn-sm btn-light w-100 text-muted fw-semibold border-0"><?= $act[0] ?></a>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Dropdown 1: Add Student Subject -->
            <div class="col">
                <div class="action-card card h-100 shadow-sm p-3 text-center dropdown">
                    <div class="fs-3 text-warning mb-2"><i class="fa-solid fa-address-card"></i></div>
                    <button class="btn btn-sm btn-light text-muted fw-semibold border-0 dropdown-toggle w-100"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Add Student<br>Subject
                    </button>
                    <ul class="dropdown-menu shadow p-2" style="max-height: 300px; overflow-y: auto; min-width: 200px;">
                        <li class="px-2 pb-2">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Search course..." onkeyup="filterCourseList(this)">
                        </li>
                        <div>
                            <?php if (!empty($courses_array)): ?>
                                <?php foreach ($courses_array as $course_name): ?>
                                    <li class="course-item">
                                        <form action="assign_student_subject.php" method="POST" class="px-2 py-1">
                                            <input type="hidden" name="course_name" value="<?= htmlspecialchars($course_name) ?>">
                                            <button type="submit" name="course_submit"
                                                class="btn btn-link text-start w-100 text-decoration-none text-dark small">
                                                <?= htmlspecialchars($course_name) ?>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="text-muted text-center small p-2">No courses found</li>
                            <?php endif; ?>
                        </div>
                    </ul>
                </div>
            </div>

            <!-- Dropdown 2: Add Subject Teacher -->
            <div class="col">
                <div class="action-card card h-100 shadow-sm p-3 text-center dropdown">
                    <div class="fs-3 text-warning mb-2"><i class="fa-solid fa-chalkboard-teacher"></i></div>
                    <button class="btn btn-sm btn-light text-muted fw-semibold border-0 dropdown-toggle w-100"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Add Subject<br>Teacher
                    </button>
                    <ul class="dropdown-menu shadow p-2" style="max-height: 300px; overflow-y: auto; min-width: 200px;">
                        <li class="px-2 pb-2">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Search course..." onkeyup="filterCourseList(this)">
                        </li>
                        <div>
                            <?php if (!empty($courses_array)): ?>
                                <?php foreach ($courses_array as $course_name): ?>
                                    <li class="course-item">
                                        <form action="subject_Teacher_Allotment.php" method="POST" class="px-2 py-1">
                                            <input type="hidden" name="course_name" value="<?= htmlspecialchars($course_name) ?>">
                                            <button type="submit" name="course_submit"
                                                class="btn btn-link text-start w-100 text-decoration-none text-dark small">
                                                <?= htmlspecialchars($course_name) ?>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="text-muted text-center small p-2">No courses found</li>
                            <?php endif; ?>
                        </div>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Today's Report Table -->
        <div class="card shadow-sm border-0 table-container">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h4 class="fw-bold text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i>Today's Attendance
                    Report</h4>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Completion Status</th>
                                <th class="text-end">Progress</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php
                            $index = 1;
                            $today_date = (int) date('dmy');
                            $query = "SELECT c.course_name, COUNT(DISTINCT s.subject_name) AS total_subjects,
                                     (SELECT COUNT(DISTINCT a.subject_name) FROM `attendance` a WHERE a.course = c.course_name AND a.date_of_attendence = '$today_date') AS marked_today
                                     FROM `courses_list` c LEFT JOIN `subjects` s ON c.course_name = s.course_name 
                                     WHERE c.faculty_name = '$faculty_name' GROUP BY c.course_name";
                            $result = mysqli_query($conn, $query);

                            if ($result):
                                while ($val = mysqli_fetch_assoc($result)):
                                    $total = $val['total_subjects'] ?: 1;
                                    $marked = $val['marked_today'] ?: 0;
                                    $percent = ($marked / $total) * 100;
                                    $color = ($percent >= 100) ? 'bg-success' : 'bg-primary';
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $index++ ?></td>
                                        <td><a href='view_subjects_attendance.php?course=<?= urlencode($val['course_name']) ?>'
                                                class="text-decoration-none fw-semibold text-dark"><?= htmlspecialchars($val['course_name']) ?></a>
                                        </td>
                                        <td><small class="text-muted"><?= $marked ?> / <?= $total ?> subjects</small></td>
                                        <td style="width: 250px;">
                                            <div class="progress">
                                                <div class="progress-bar <?= $color ?>" role="progressbar"
                                                    style="width: <?= $percent ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-muted border-top">
        <small>&copy; 2026 Motherhood University Attendance Management System</small>
    </footer>

    <!-- Script for Live Course Search inside Dropdowns -->
    <script>
        function filterCourseList(input) {
            let filter = input.value.toLowerCase();
            let dropdownMenu = input.closest('.dropdown-menu');
            let items = dropdownMenu.getElementsByClassName('course-item');
            for (let i = 0; i < items.length; i++) {
                let text = items[i].textContent || items[i].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
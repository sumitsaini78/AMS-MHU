<?php
include "../db_connect.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin_name = "Administrator";
if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $admin_name = $admin['name'];
}

$faculty_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM faculty"))['total'] ?? 0;
$dean_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM deans"))['total'] ?? 0;
$teacher_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM teachers"))['total'] ?? 0;
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'] ?? 0;
$course_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM courses_list"))['total'] ?? 0;
$subject_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects"))['total'] ?? 0;
$pending_corrections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM attendance_corrections WHERE status = 'Pending'"))['total'] ?? 0;
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Master Admin Dashboard | MHU-AMS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; scroll-behavior: smooth; }
        .action-card { border: none; border-radius: 16px; transition: all 0.3s ease; background: #ffffff; }
        .action-card:hover { transform: translateY(-6px); box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08) !important; }
        .stat-card { border: none; border-radius: 16px; background: #ffffff; transition: all 0.3s ease; border-left: 5px solid #dc3545; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06); }
        .search-box { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; }
        .search-box:focus { background: rgba(255, 255, 255, 0.15); color: #fff; border-color: #dc3545; box-shadow: none; }
        .search-box::placeholder { color: #adb5bd; }
        .anchor-pill { transition: all 0.2s ease-in-out; }
        .anchor-pill:hover { background-color: #dc3545 !important; color: #fff !important; }
    </style>
</head>
<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-shield-halved text-danger me-2"></i> MHU-AMS
                    <sub class="text-danger ms-1">MASTER INDEX</sub>
                </a>
                <div class="d-none d-lg-flex mx-auto" style="width: 320px;">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control search-box" placeholder="Search modules..." id="globalSearch" onkeyup="filterAdminCards(this)">
                        <span class="input-group-text bg-transparent border-secondary text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="manage_corrections.php" class="btn btn-sm btn-outline-danger position-relative" title="Pending Corrections">
                        <i class="fa-solid fa-bell"></i>
                        <?php if ($pending_corrections > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $pending_corrections ?></span>
                        <?php endif; ?>
                    </a>
                    <span class="text-white small d-none d-md-block"><i class="fa-solid fa-user-shield me-1 text-danger"></i><strong><?= htmlspecialchars($admin_name) ?></strong></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger px-3 shadow-sm"><i class="fa-solid fa-power-off"></i></a>
                </div>
            </div>
        </nav>
        
        <!-- Quick Anchor Navigation Bar -->
        <div class="bg-white border-bottom shadow-sm py-2">
            <div class="container d-flex align-items-center overflow-auto gap-2 py-1">
                <span class="text-muted small fw-bold text-uppercase me-2"><i class="fa-solid fa-anchor text-danger me-1"></i> Jump To:</span>
                <a href="#faculties" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Faculties</a>
                <a href="#deans" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Deans</a>
                <a href="#teachers" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Teachers</a>
                <a href="#students" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Students</a>
                <a href="#courses" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Courses</a>
                <a href="#subjects" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Subjects</a>
                <a href="#corrections" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Corrections</a>
                <a href="#settings" class="anchor-pill btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 small">Settings</a>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark mb-1">System Master Control Index</h2>
                <p class="text-muted mb-0">Direct anchor indices and quick metrics for the Motherhood University Attendance Management System.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa-solid fa-circle-check me-1"></i> Status: Fully Operational
                </span>
            </div>
        </div>

        <!-- Metric Summary Counters -->
        <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-6 mb-5">
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #0d6efd;"><h6 class="text-muted small mb-0">Faculties</h6><h3 class="fw-bold text-dark mb-0"><?= $faculty_count ?></h3></div></div>
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #0dcaf0;"><h6 class="text-muted small mb-0">Deans</h6><h3 class="fw-bold text-dark mb-0"><?= $dean_count ?></h3></div></div>
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #ffc107;"><h6 class="text-muted small mb-0">Teachers</h6><h3 class="fw-bold text-dark mb-0"><?= $teacher_count ?></h3></div></div>
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #198754;"><h6 class="text-muted small mb-0">Students</h6><h3 class="fw-bold text-dark mb-0"><?= $student_count ?></h3></div></div>
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #dc3545;"><h6 class="text-muted small mb-0">Courses</h6><h3 class="fw-bold text-dark mb-0"><?= $course_count ?></h3></div></div>
            <div class="col"><div class="stat-card p-3 shadow-sm" style="border-left-color: #6c757d;"><h6 class="text-muted small mb-0">Subjects</h6><h3 class="fw-bold text-dark mb-0"><?= $subject_count ?></h3></div></div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-12"><h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-bookmark text-danger me-2"></i>Module Anchors Directory</h4></div>
        </div>

        <!-- Cards Grid with ID Anchors -->
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-4 mb-5" id="adminCardsContainer">
            
            <div class="col admin-card-item" id="faculties">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Faculties</h5>
                        <p class="text-muted small mb-4">Add, edit or remove departments & faculties configuration.</p>
                    </div>
                    <a href="manage_faculty.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="deans">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-info-subtle text-info rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Deans</h5>
                        <p class="text-muted small mb-4">Assign faculty deans and manage institutional credentials.</p>
                    </div>
                    <a href="manage_deans.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="teachers">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-warning-subtle text-warning rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Teachers</h5>
                        <p class="text-muted small mb-4">Control faculty teachers, contacts, and instructor lists.</p>
                    </div>
                    <a href="manage_teachers.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="students">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success-subtle text-success rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Students</h5>
                        <p class="text-muted small mb-4">Register, update, or remove student enrollments & details.</p>
                    </div>
                    <a href="manage_students.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="courses">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-danger-subtle text-danger rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Courses</h5>
                        <p class="text-muted small mb-4">Handle degree programs, curricula, and course mappings.</p>
                    </div>
                    <a href="manage_courses.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="subjects">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-secondary-subtle text-secondary rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Manage Subjects</h5>
                        <p class="text-muted small mb-4">Configure course subjects, semester mappings, and codes.</p>
                    </div>
                    <a href="manage_subjects.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="corrections">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Attendance Logs</h5>
                        <p class="text-muted small mb-4">Review attendance correction requests and pending logs.</p>
                    </div>
                    <a href="manage_corrections.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

            <div class="col admin-card-item" id="settings">
                <div class="action-card card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-light text-dark rounded-3 p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-gears"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark fs-5 mb-2">Admin Settings</h5>
                        <p class="text-muted small mb-4">Update administrator security profile and login credentials.</p>
                    </div>
                    <a href="admin_settings.php" class="btn btn-sm btn-outline-dark w-100 fw-semibold py-2 rounded-pill">Open Module <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>

        </div>
    </main>

    <footer class="text-center py-4 text-muted border-top bg-white mt-5">
        <small>&copy; 2026 Motherhood University Attendance Management System &bull; Master Index Hub</small>
    </footer>

    <script>
        function filterAdminCards(input) {
            let filter = input.value.toLowerCase();
            let container = document.getElementById('adminCardsContainer');
            let items = container.getElementsByClassName('admin-card-item');
            for (let i = 0; i < items.length; i++) {
                let title = items[i].querySelector('h5').innerText.toLowerCase();
                let desc = items[i].querySelector('p').innerText.toLowerCase();
                items[i].style.display = (title.indexOf(filter) > -1 || desc.indexOf(filter) > -1) ? "" : "none";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
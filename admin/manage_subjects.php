<?php
include "../db_connect.php";
session_start();

// 1. Admin Authentication Check
if (!isset($_SESSION['admin_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}

$msg = ""; $error = "";

// --- FLASH MESSAGE HANDLING ---
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
}

// --- FILTER HANDLING ---
if (isset($_POST['reset_filters'])) {
    unset($_SESSION['sub_filter_course'], $_SESSION['sub_filter_sem']);
    $filter_course = "";
    $filter_sem = "";
    header("Location: manage_subjects.php");
    exit;
} else {
    if (isset($_POST['apply_filters'])) {
        $filter_course = $_POST['sub_filter_course'];
        $filter_sem = $_POST['sub_filter_sem'];
        $_SESSION['sub_filter_course'] = $filter_course;
        $_SESSION['sub_filter_sem'] = $filter_sem;
        header("Location: manage_subjects.php");
        exit;
    } else {
        $filter_course = $_SESSION['sub_filter_course'] ?? '';
        $filter_sem = $_SESSION['sub_filter_sem'] ?? '';
    }
}

// Build common WHERE clause for filters
$where_sql = "WHERE 1=1";
if (!empty($filter_course)) {
    $where_sql .= " AND course_name = '" . $conn->real_escape_string($filter_course) . "'";
}
if (!empty($filter_sem)) {
    $where_sql .= " AND semester = " . intval($filter_sem);
}

// 2. Handle Filtered CSV Export
if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="filtered_subjects_export.csv"');
    $output = fopen("php://output", "w");
    
    // Add header row
    fputcsv($output, ['course_name', 'year', 'semester', 'subject_name', 'subject_code', 'faculty_name']);
    
    $export_query = "SELECT course_name, Year, semester, subject_name, subject_code, faculty_name FROM subjects $where_sql ORDER BY course_id DESC";
    $export_res = $conn->query($export_query);
    if ($export_res) {
        while ($row = $export_res->fetch_assoc()) {
            fputcsv($output, [
                $row['course_name'] ?? '', 
                $row['Year'] ?? '', 
                $row['semester'] ?? '', 
                $row['subject_name'] ?? '', 
                $row['subject_code'] ?? '', 
                $row['faculty_name'] ?? ''
            ]);
        }
    }
    fclose($output);
    exit;
}

// 3. Handle Sample CSV Download
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_subjects.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['course_name', 'year', 'semester', 'subject_name', 'subject_code', 'faculty_name']);
    fputcsv($output, ['BBA', '2026', '1', 'Principles and Practice of Management', 'MUBBA 101', 'FOCBS']);
    fputcsv($output, ['BBA', '2026', '1', 'Computer Applications in Business', 'MUBBA 102', 'FOCBS']);
    fclose($output);
    exit;
}

// 4. Process Single Subject Manual Add
if (isset($_POST['add_subject'])) {
    $subject_name = trim($_POST['subject_name']);
    $course_name  = trim($_POST['course_name']);
    $semester     = (int)trim($_POST['semester']);
    
    if (!empty($subject_name) && !empty($course_name) && !empty($semester)) {
        // Check for duplicates
        $check_stmt = $conn->prepare("SELECT course_id FROM subjects WHERE subject_name = ? AND course_name = ? AND semester = ?");
        $check_stmt->bind_param("ssi", $subject_name, $course_name, $semester);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "This subject already exists for the selected course and semester.";
        } else {
            // No duplicate found, proceed with insertion
            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, course_name, semester) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $subject_name, $course_name, $semester);
            if ($stmt->execute()) { 
                $_SESSION['flash_msg'] = "Subject added successfully!";
                header("Location: manage_subjects.php");
                exit;
            } else { 
                $error = "Error: " . $conn->error; 
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else { 
        $error = "Subject Name, Course, and Semester are all required fields."; 
    }
}

// 5. Process Bulk CSV Import with Duplicate Check
elseif (isset($_POST['import_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
        
        // Skip header row
        fgetcsv($handle); 

        $check_stmt = $conn->prepare("SELECT subject_code FROM subjects WHERE subject_code = ? AND subject_code != ''");
        $stmt       = $conn->prepare("INSERT INTO subjects (course_name, Year, semester, subject_name, subject_code, faculty_name) VALUES (?, ?, ?, ?, ?, ?)");

        $success_count   = 0;
        $duplicate_count = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 4) {
                $course   = trim($data[0] ?? '');
                $year     = (int)($data[1] ?? 0);
                $sem      = (int)($data[2] ?? 0);
                $sub_name = trim($data[3] ?? '');
                $sub_code = trim($data[4] ?? '');
                $faculty  = trim($data[5] ?? '');

                if (empty($sub_name)) {
                    continue;
                }

                // Duplicate check by subject_code if provided
                if (!empty($sub_code)) {
                    $check_stmt->bind_param("s", $sub_code);
                    $check_stmt->execute();
                    $check_stmt->store_result();

                    if ($check_stmt->num_rows > 0) {
                        $duplicate_count++;
                        continue;
                    }
                }

                // Insert record
                $stmt->bind_param("siisss", $course, $year, $sem, $sub_name, $sub_code, $faculty);
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $duplicate_count++;
                }
            }
        }
        
        fclose($handle);
        $check_stmt->close();
        $stmt->close();
        
        $_SESSION['flash_msg'] = "CSV Import Complete! Successfully Imported: $success_count, Skipped/Duplicates: $duplicate_count";
        header("Location: manage_subjects.php");
        exit;
    } else {
        $error = "Please select a valid CSV file.";
    }
}

// 6. Delete Subject Handler
if (isset($_GET['delete_id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $did = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM subjects WHERE course_id = $did")) {
        $_SESSION['flash_msg'] = "Subject deleted successfully!";
    } else {
        $error = "Error deleting subject.";
    }
    header("Location: manage_subjects.php");
    exit;
}

// 7. Fetch data for dropdowns
$courses_res = $conn->query("
    SELECT cl.course_name, f.faculty_name AS faculty_short_name 
    FROM courses_list cl 
    LEFT JOIN faculty f ON cl.faculty_name = f.faculty_full_name 
    GROUP BY cl.course_name, f.faculty_name
    ORDER BY f.faculty_name ASC, cl.course_name ASC
");

// Fetch unique semesters for filter dropdown
$sem_list_res = $conn->query("SELECT DISTINCT semester FROM subjects WHERE semester IS NOT NULL ORDER BY semester ASC");
$available_semesters = [];
if ($sem_list_res) {
    while ($s_row = $sem_list_res->fetch_assoc()) {
        $available_semesters[] = $s_row['semester'];
    }
}

// 8. Pagination Setup
$limit = 10; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total matching records for pagination
$count_query = "SELECT COUNT(*) AS total FROM subjects $where_sql";
$count_res = $conn->query($count_query);
$total_records = $count_res ? $count_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_records / $limit);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Subjects | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .filter-box { background-color: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; border: 1px solid #e9ecef; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-danger me-2"></i> MHU-AMS ADMIN</a>
            <a href="index.php" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
        </div>
    </nav>
    <main class="container py-5">
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-book-bookmark text-secondary me-2"></i>Manage Subjects</h2>
        
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded-4 shadow-sm" id="subjectTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-4 fw-semibold small" id="single-tab" data-bs-toggle="pill" data-bs-target="#single-add" type="button" role="tab">Single Add</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-4 fw-semibold small" id="bulk-tab" data-bs-toggle="pill" data-bs-target="#bulk-add" type="button" role="tab">Bulk CSV Upload</button>
                    </li>
                </ul>

                <div class="tab-content" id="subjectTabContent">
                    <!-- TAB 1: Single Subject Add Form -->
                    <div class="tab-pane fade show active" id="single-add" role="tabpanel">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                            <h5 class="fw-bold mb-3">Add Subject</h5>
                            <form method="POST">
                                <div class="mb-3"><label class="form-label small fw-semibold">Subject Name *</label><input type="text" name="subject_name" class="form-control" required></div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Course Name</label>
                                    <select name="course_name" class="form-select" required>
                                        <option value="" selected disabled>Select a course</option>
                                        <?php if ($courses_res && $courses_res->num_rows > 0): mysqli_data_seek($courses_res, 0); ?>
                                            <?php while($c = $courses_res->fetch_assoc()):
                                                $display_text = (isset($c['faculty_short_name']) ? htmlspecialchars($c['faculty_short_name']) . ' - ' : '') . htmlspecialchars($c['course_name']);
                                            ?>
                                                <option value="<?= htmlspecialchars($c['course_name']) ?>"><?= $display_text ?></option>
                                            <?php endwhile; endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Semester</label>
                                    <input type="number" name="semester" class="form-control" min="1" max="12" placeholder="e.g., 1" required>
                                </div>
                                <button type="submit" name="add_subject" class="btn btn-secondary w-100 rounded-pill fw-semibold">Save Subject</button>
                            </form>
                        </div>
                    </div>

                    <!-- TAB 2: Bulk CSV Upload Form -->
                    <div class="tab-pane fade" id="bulk-add" role="tabpanel">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Bulk CSV Upload</h5>
                                <a href="?download_sample=1" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:0.75rem;">
                                    <i class="fa-solid fa-download me-1"></i> Sample CSV
                                </a>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Select CSV File *</label>
                                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                </div>
                                <div class="p-2 bg-light rounded-3 mb-3 small text-muted border">
                                    <strong>Format:</strong> course_name, year, semester, subject_name, subject_code, faculty_name
                                </div>
                                <button type="submit" name="import_csv" class="btn btn-success w-100 rounded-pill fw-semibold">
                                    <i class="fa-solid fa-upload me-1"></i> Upload Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Subjects List</h5>
                        <a href="manage_subjects.php?export_csv=1" class="btn btn-sm btn-success rounded-pill fw-semibold px-3">
                            <i class="fa-solid fa-file-excel me-1"></i> Export Filtered CSV
                        </a>
                    </div>

                    <!-- FILTER PANEL -->
                    <div class="filter-box">
                        <form method="POST" action="manage_subjects.php" class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary mb-1">Filter by Course:</label>
                                <select class="form-select form-select-sm" name="sub_filter_course">
                                    <option value="">-- All Courses --</option>
                                    <?php 
                                    if ($courses_res && $courses_res->num_rows > 0) {
                                        mysqli_data_seek($courses_res, 0);
                                        while ($c_row = $courses_res->fetch_assoc()) {
                                            $selected = ($filter_course == $c_row['course_name']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($c_row['course_name']) . '" ' . $selected . '>' . htmlspecialchars($c_row['course_name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary mb-1">Filter by Semester:</label>
                                <select class="form-select form-select-sm" name="sub_filter_sem">
                                    <option value="">-- All Semesters --</option>
                                    <?php foreach ($available_semesters as $sem_val): ?>
                                        <option value="<?= htmlspecialchars($sem_val) ?>" <?= ($filter_sem == $sem_val) ? 'selected' : '' ?>>
                                            Semester <?= htmlspecialchars($sem_val) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-1">
                                <button type="submit" name="apply_filters" class="btn btn-sm btn-primary w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                                <?php if (!empty($filter_course) || !empty($filter_sem)): ?>
                                    <button type="submit" name="reset_filters" class="btn btn-sm btn-outline-secondary w-100 fw-semibold" title="Reset Filters">Reset</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>#</th><th>Subject Name</th><th>Course</th><th>Semester</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php 
                                $query = "SELECT * FROM subjects $where_sql ORDER BY course_id DESC LIMIT $limit OFFSET $offset";
                                $res = $conn->query($query); 
                                $i = $offset + 1;
                                
                                if ($res && $res->num_rows > 0) {
                                    while($row = $res->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                        <td class="text-end"><a href="manage_subjects.php?delete_id=<?= $row['course_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete subject?');"><i class="fa-solid fa-trash"></i></a></td>
                                    </tr>
                                    <?php endwhile; 
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-4">No subjects found matching the selected filters.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION CONTROLS -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1">Previous</a>
                                </li>
                                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                    <li class="page-item <?= ($page == $p) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                </div>
            </div> 
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
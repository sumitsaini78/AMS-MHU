<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['dean_id'])) { header("Location: ../index.php"); exit; }
$msg = ""; $error = "";

// --- AUTOMATICALLY GET FACULTY FROM SESSION ---
$session_faculty = $_SESSION['faculty_name'] ?? 'Default Faculty'; 

// --- DYNAMICALLY GENERATE SESSION OPTIONS ---
$current_year = date('Y');
$current_month = date('m');
$session_options = [];
for ($y = $current_year - 4; $y <= $current_year + 4; $y++) {
    $session_options[] = $y . '-' . ($y + 1);
}
// Default session logic: If month is June or later, start this year, else start last year
$default_session = ($current_month >= 6) ? $current_year . '-' . ($current_year + 1) : ($current_year - 1) . '-' . $current_year;

// --- FLASH MESSAGE HANDLING ---
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
}

// Fetch courses early so we can use them in both filters and forms
$courses_res = $conn->query("SELECT DISTINCT course_name FROM courses_list ORDER BY course_name ASC");

// --- HANDLER: DOWNLOAD SAMPLE CSV ---
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_students.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Name', 'Father Name', 'Enroll', 'Roll', 'Course', 'Section', 'Year', 'Sem', 'Admission Date', 'Session']);
    fputcsv($output, ['John Doe', 'Richard Doe', '12345678', 'A-101', 'B.Tech', 'A', '2026', '4', '2026-07-01', '2026-2027']);
    fclose($output);
    exit;
}

// --- HANDLER: SINGLE STUDENT ADD ---
if (isset($_POST['add_student'])) {
    $student_name = trim($_POST['student_name']);
    $father_name = trim($_POST['father_name']);
    $enrollment_number = !empty(trim($_POST['enrollment_number'])) ? trim($_POST['enrollment_number']) : null; 
    $roll_number = trim($_POST['roll_number']);
    $course_name = strtoupper(trim($_POST['course_name']));
    $semester = trim($_POST['semester']);
    $session_val = trim($_POST['session']); 
    $faculty_name = $session_faculty; 
    
    if (!empty($student_name)) {
        $stmt = $conn->prepare("INSERT INTO students (name, father_name, enrollment_number, roll_number, course, sem, faculty, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $student_name, $father_name, $enrollment_number, $roll_number, $course_name, $semester, $faculty_name, $session_val);
        if ($stmt->execute()) { 
            $_SESSION['flash_msg'] = "Student registered successfully!";
            header("Location: manage_students.php");
            exit;
        } else { $error = "Error: " . $conn->error; }
        $stmt->close();
    } else { $error = "Student Name is a required field."; }
}

// --- HANDLER: BULK CSV UPLOAD ---
if (isset($_POST['import_csv'])) {
    if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == 0) {
        $filename = $_FILES["csv_file"]["tmp_name"];
        $file = fopen($filename, "r");
        fgetcsv($file); // Skip header row
        
        $success_count = 0;
        $duplicate_count = 0;

        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            if (count($row) >= 2) {
                $name        = mysqli_real_escape_string($conn, trim($row[0] ?? ''));
                $father_name = mysqli_real_escape_string($conn, trim($row[1] ?? ''));
                $enroll      = trim($row[2] ?? '');
                $roll        = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
                $faculty     = mysqli_real_escape_string($conn, $session_faculty);
                $course      = mysqli_real_escape_string($conn, strtoupper(trim($row[4] ?? '')));
                $section     = mysqli_real_escape_string($conn, trim($row[5] ?? ''));
                $year        = (int)($row[6] ?? 0);
                $sem         = mysqli_real_escape_string($conn, trim($row[7] ?? ''));
                
                $raw_date = trim($row[8] ?? '');
                $date_parts = explode('-', $raw_date);
                if (count($date_parts) == 3) {
                    $admission_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
                } else {
                    $admission_date = $raw_date;
                }
                $admission_date = mysqli_real_escape_string($conn, $admission_date);
                $session = mysqli_real_escape_string($conn, trim($row[9] ?? '')); 

                if (!empty($name)) {
                    $enroll_sql = !empty($enroll) ? "'" . mysqli_real_escape_string($conn, $enroll) . "'" : "NULL";
                    $is_duplicate = false;

                    if (!empty($enroll)) {
                        $check_sql = "SELECT id FROM students WHERE enrollment_number = '$enroll'";
                        $check_result = mysqli_query($conn, $check_sql);
                        if ($check_result && mysqli_num_rows($check_result) > 0) {
                            $is_duplicate = true;
                            $duplicate_count++;
                        }
                    }

                    if (!$is_duplicate) {
                        $sql = "INSERT INTO students (name, father_name, enrollment_number, roll_number, faculty, course, section, year, sem, date_of_admission, session) 
                                VALUES ('$name', '$father_name', $enroll_sql, '$roll', '$faculty', '$course', '$section', $year, '$sem', '$admission_date', '$session')";
                        
                        if (mysqli_query($conn, $sql)) {
                            $success_count++;
                        } else {
                            $fallback_sql = "INSERT INTO students (name, father_name, enrollment_number, course, sem, faculty, session) 
                                             VALUES ('$name', '$father_name', $enroll_sql, '$course', '$sem', '$faculty', '$session')";
                            if (mysqli_query($conn, $fallback_sql)) {
                                $success_count++;
                            } else {
                                $duplicate_count++;
                            }
                        }
                    }
                }
            }
        }
        fclose($file);
        $_SESSION['flash_msg'] = "CSV Import Complete! Imported: $success_count, Skipped (Duplicates/Errors): $duplicate_count";
        header("Location: manage_students.php");
        exit;
    } else {
        $error = "Please upload a valid CSV file.";
    }
}

// --- HANDLER: DELETE STUDENT ---
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    if ($conn->query("DELETE FROM students WHERE id = $did")) {
        $_SESSION['flash_msg'] = "Student deleted successfully!";
    } else {
        $error = "Error deleting student.";
    }
    header("Location: manage_students.php");
    exit;
}

// --- HANDLER: UPDATE STUDENT ---
if (isset($_POST['update_student'])) {
    $student_id = intval($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $father_name = trim($_POST['father_name']);
    $enrollment_number = !empty(trim($_POST['enrollment_number'])) ? trim($_POST['enrollment_number']) : null;
    $roll_number = trim($_POST['roll_number']);
    $course_name = strtoupper(trim($_POST['course_name']));
    $semester = trim($_POST['semester']);
    $session_val = trim($_POST['session']); 
    $faculty_name = $session_faculty; 

    $stmt = $conn->prepare("UPDATE students SET name=?, father_name=?, enrollment_number=?, roll_number=?, course=?, sem=?, faculty=?, session=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $student_name, $father_name, $enrollment_number, $roll_number, $course_name, $semester, $faculty_name, $session_val, $student_id);
    if ($stmt->execute()) { $_SESSION['flash_msg'] = "Student record updated successfully!"; } else { $error = "Error updating record: " . $conn->error; }
    $stmt->close();
    header("Location: manage_students.php");
    exit;
}

// --- DATA FETCHING, FILTERING & PAGINATION ---
$limit = 20; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter Variables
$search_term = $_GET['search'] ?? '';
$course_filter = $_GET['course_filter'] ?? '';
$sem_filter = $_GET['sem_filter'] ?? '';
$session_filter = $_GET['session_filter'] ?? '';

$where_clauses = [];

if (!empty($search_term)) {
    $search_term_esc = mysqli_real_escape_string($conn, $search_term);
    $where_clauses[] = "(name LIKE '%$search_term_esc%' OR enrollment_number LIKE '%$search_term_esc%' OR roll_number LIKE '%$search_term_esc%')";
}
if (!empty($course_filter)) {
    $course_filter_esc = mysqli_real_escape_string($conn, $course_filter);
    $where_clauses[] = "course = '$course_filter_esc'";
}
if (!empty($sem_filter)) {
    $sem_filter_esc = mysqli_real_escape_string($conn, $sem_filter);
    $where_clauses[] = "sem = '$sem_filter_esc'";
}
if (!empty($session_filter)) {
    $session_filter_esc = mysqli_real_escape_string($conn, $session_filter);
    $where_clauses[] = "session = '$session_filter_esc'";
}

$search_query = "";
if (count($where_clauses) > 0) {
    $search_query = "WHERE " . implode(" AND ", $where_clauses);
}

// Count total records for pagination
$count_res = $conn->query("SELECT COUNT(id) as total FROM students $search_query");
$total_records = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Fetch students for the current page
$students_res = $conn->query("SELECT * FROM students $search_query ORDER BY id DESC LIMIT $limit OFFSET $offset");


// --- EDIT MODE DATA FETCHING ---
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $edit_res = $conn->query("SELECT * FROM students WHERE id = $edit_id");
    if ($edit_res) $edit_data = $edit_res->fetch_assoc();
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Students | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .form-control:focus, .form-select:focus { box-shadow: none; border-color: #86b7fe; }
        .input-group:focus-within .input-group-text { border-color: #86b7fe; color: #0d6efd; }
        .page-link { transition: all 0.2s ease-in-out; }
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
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-user-graduate text-success me-2"></i>Manage Students</h2>
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <?php if ($edit_data): // --- EDIT FORM --- ?>
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-pencil text-primary me-2"></i>Edit Student Record</h5>
                    <a href="manage_students.php" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fa-solid fa-xmark me-1"></i> Cancel Edit</a>
                </div>
                <form method="POST" action="manage_students.php">
                    <input type="hidden" name="student_id" value="<?= $edit_data['id'] ?>">
                    <div class="mb-3"><label class="form-label small fw-semibold">Student Name *</label><input type="text" name="student_name" class="form-control" value="<?= htmlspecialchars($edit_data['name']) ?>" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Father's Name</label><input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($edit_data['father_name'] ?? '') ?>"></div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><label class="form-label small fw-semibold">Enrollment No.</label><input type="text" name="enrollment_number" class="form-control" value="<?= htmlspecialchars($edit_data['enrollment_number'] ?? '') ?>"></div>
                        <div class="col-md-6"><label class="form-label small fw-semibold">Roll No.</label><input type="text" name="roll_number" class="form-control" value="<?= htmlspecialchars($edit_data['roll_number'] ?? '') ?>"></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Course Name</label>
                            <select name="course_name" class="form-select">
                                <?php mysqli_data_seek($courses_res, 0); while($c = $courses_res->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($c['course_name']) ?>" <?= ($edit_data['course'] == $c['course_name']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Semester</label>
                            <select name="semester" class="form-select">
                                <?php for($i=1; $i<=10; $i++): ?><option value="<?= $i ?>" <?= ($edit_data['sem'] == $i) ? 'selected' : '' ?>><?= $i ?></option><?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Session</label>
                            <select name="session" class="form-select" required>
                                <option value="" disabled>Select Session</option>
                                <?php foreach($session_options as $sess): ?>
                                    <option value="<?= $sess ?>" <?= (isset($edit_data['session']) && $edit_data['session'] == $sess) ? 'selected' : '' ?>><?= $sess ?></option>
                                <?php endforeach; ?>
                                <?php 
                                // Old session check fallback
                                if (isset($edit_data['session']) && !in_array($edit_data['session'], $session_options) && !empty($edit_data['session'])): 
                                ?>
                                    <option value="<?= htmlspecialchars($edit_data['session']) ?>" selected><?= htmlspecialchars($edit_data['session']) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_student" class="btn btn-primary w-100 rounded-pill fw-semibold">Update Student</button>
                </form>
            </div>
        <?php else: // --- ADD FORMS --- ?>
            <div class="accordion mb-4" id="studentFormsAccordion">
                <div class="accordion-item rounded-4 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-4 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="fa-solid fa-plus me-2"></i> Add New Student
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#studentFormsAccordion">
                        <div class="accordion-body">
                            <h5 class="fw-bold mb-3">Add Student Details</h5>
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3"><label class="form-label small fw-semibold">Student Name *</label><input type="text" name="student_name" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Father's Name</label><input type="text" name="father_name" class="form-control"></div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6"><label class="form-label small fw-semibold">Enrollment No.</label><input type="text" name="enrollment_number" class="form-control" placeholder="Optional"></div>
                                    <div class="col-md-6"><label class="form-label small fw-semibold">Roll No.</label><input type="text" name="roll_number" class="form-control" placeholder="Optional"></div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4"><label class="form-label small fw-semibold">Course Name</label><select name="course_name" class="form-select"><option value="" selected disabled>Select Course</option><?php mysqli_data_seek($courses_res, 0); ?><?php while($c = $courses_res->fetch_assoc()): ?><option value="<?= htmlspecialchars($c['course_name']) ?>"><?= htmlspecialchars($c['course_name']) ?></option><?php endwhile; ?></select></div>
                                    <div class="col-md-4"><label class="form-label small fw-semibold">Semester</label><select name="semester" class="form-select"><option value="" selected disabled>Select Semester</option><?php for($i=1; $i<=10; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Session</label>
                                        <select name="session" class="form-select" required>
                                            <option value="" disabled>Select Session</option>
                                            <?php foreach($session_options as $sess): ?>
                                                <option value="<?= $sess ?>" <?= ($sess == $default_session) ? 'selected' : '' ?>><?= $sess ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" name="add_student" class="btn btn-success w-100 rounded-pill fw-semibold">Save Student</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="accordion-item mt-3 rounded-4 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded-4 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="fa-solid fa-upload me-2"></i> Bulk Upload Students via CSV
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#studentFormsAccordion">
                        <div class="accordion-body">
                            <div class="d-flex justify-content-between align-items-center mb-3"><h5 class="fw-bold mb-0">Bulk CSV Upload</h5><a href="?download_sample=1" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:0.75rem;"><i class="fa-solid fa-download me-1"></i> Sample CSV</a></div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3"><label class="form-label small fw-semibold">Select CSV File *</label><input type="file" name="csv_file" class="form-control" accept=".csv" required></div>
                                <div class="p-2 bg-light rounded-3 mb-3 small text-muted border"><strong>Format:</strong> Name, Father Name, Enroll, Roll, Course, Section, Year, Sem, Admission Date, Session <br><em>(Note: Faculty is assigned automatically based on your login)</em></div>
                                <button type="submit" name="import_csv" class="btn btn-success w-100 rounded-pill fw-semibold"><i class="fa-solid fa-upload me-1"></i> Upload Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Student List Card -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                        <h5 class="fw-bold mb-3 mb-md-0 w-100">Students List (<?= $total_records ?>)</h5>
                    </div>

                    <!-- SEARCH AND FILTER FORM -->
                    <form method="GET" class="row g-2 mb-4 align-items-center p-3 bg-light rounded-3 border">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1 text-muted">Search Query</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Enroll, Roll..." value="<?= htmlspecialchars($search_term) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold mb-1 text-muted">Course</label>
                            <select name="course_filter" class="form-select form-select-sm">
                                <option value="">All Courses</option>
                                <?php 
                                mysqli_data_seek($courses_res, 0); 
                                while($c = $courses_res->fetch_assoc()): 
                                ?>
                                    <option value="<?= htmlspecialchars($c['course_name']) ?>" <?= ($course_filter == $c['course_name']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold mb-1 text-muted">Semester</label>
                            <select name="sem_filter" class="form-select form-select-sm">
                                <option value="">All Semesters</option>
                                <?php for($i=1; $i<=10; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($sem_filter == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold mb-1 text-muted">Session</label>
                            <select name="session_filter" class="form-select form-select-sm">
                                <option value="">All Sessions</option>
                                <?php foreach($session_options as $sess): ?>
                                    <option value="<?= $sess ?>" <?= ($session_filter == $sess) ? 'selected' : '' ?>><?= $sess ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mt-2 mt-md-0">
                            <div class="w-100 d-flex gap-2">
                                <button class="btn btn-sm btn-primary flex-grow-1" type="submit" style="margin-top: 26px;"><i class="fa-solid fa-filter me-1"></i> Apply Filters</button>
                                <?php if (!empty($search_term) || !empty($course_filter) || !empty($sem_filter) || !empty($session_filter)): ?>
                                <a href="manage_students.php" class="btn btn-sm btn-outline-secondary" title="Reset Filters" style="margin-top: 26px;"><i class="fa-solid fa-rotate-left"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <!-- END SEARCH AND FILTER FORM -->

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Identifiers</th>
                                    <th>Course & Sem</th>
                                    <th>Faculty & Session</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($students_res && $students_res->num_rows > 0): ?>
                                <?php 
                                $i = $offset + 1;
                                while($row = $students_res->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['father_name'] ?? 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <div class="small">Enroll: <code><?= htmlspecialchars($row['enrollment_number'] ?: 'N/A') ?></code></div>
                                        <div class="small">Roll: <code><?= htmlspecialchars($row['roll_number'] ?: 'N/A') ?></code></div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($row['course'] ?? 'N/A') ?></div>
                                        <div class="small text-muted">Sem: <?= htmlspecialchars($row['sem'] ?? 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold mb-1"><?= htmlspecialchars($row['faculty'] ?? 'N/A') ?></span>
                                        <div class="small text-muted">Session: <?= htmlspecialchars($row['session'] ?? 'N/A') ?></div>
                                    </td>
                                    <td class="text-end">
                                        <a href="manage_students.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Student"><i class="fa-solid fa-pencil"></i></a>
                                        <a href="manage_students.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete student?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No students found matching your filters.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Controls -->
                    <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php
                                // Carry over current filters to pagination links
                                $query_params = $_GET;
                                $query_params['page'] = $i;
                            ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?<?= http_build_query($query_params) ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
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
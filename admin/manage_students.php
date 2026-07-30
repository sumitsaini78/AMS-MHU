<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
$msg = ""; $error = "";

// --- HANDLER: DOWNLOAD SAMPLE CSV ---
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_students.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Name', 'Enroll', 'Roll', 'Faculty', 'Course', 'Section', 'Year', 'Sem', 'Admission Date', 'Session']);
    fputcsv($output, ['John Doe', '12345678', 'A-101', 'Computer Science', 'B.Tech', 'A', '2026', '4', '2026-07-01', '2026-2027']);
    fclose($output);
    exit;
}

// --- HANDLER: SINGLE STUDENT ADD ---
if (isset($_POST['add_student'])) {
    $student_name = trim($_POST['student_name']);
    $enrollment = trim($_POST['enrollment']);
    $course_name = trim($_POST['course_name']);
    $semester = trim($_POST['semester']);
    $faculty_name = trim($_POST['faculty_name']);
    if (!empty($student_name) && !empty($enrollment)) {
        $stmt = $conn->prepare("INSERT INTO students (name, enrollment_number, course, sem, faculty) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $student_name, $enrollment, $course_name, $semester, $faculty_name);
        if ($stmt->execute()) { $msg = "Student registered successfully!"; } else { $error = "Error: " . $conn->error; }
        $stmt->close();
    } else { $error = "Fill all required fields."; }
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
                $name    = mysqli_real_escape_string($conn, trim($row[0] ?? ''));
                $enroll  = mysqli_real_escape_string($conn, trim($row[1] ?? ''));
                $roll    = mysqli_real_escape_string($conn, trim($row[2] ?? ''));
                $faculty = mysqli_real_escape_string($conn, trim($row[3] ?? ''));
                $course  = mysqli_real_escape_string($conn, trim($row[4] ?? ''));
                $section = mysqli_real_escape_string($conn, trim($row[5] ?? ''));
                $year    = (int)($row[6] ?? 0);
                $sem     = mysqli_real_escape_string($conn, trim($row[7] ?? ''));
                
                // --- CONVERT DATE FORMAT FROM DD-MM-YYYY TO YYYY-MM-DD ---
                $raw_date = trim($row[8] ?? '');
                $date_parts = explode('-', $raw_date);
                if (count($date_parts) == 3) {
                    $admission_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
                } else {
                    $admission_date = $raw_date;
                }
                $admission_date = mysqli_real_escape_string($conn, $admission_date);
                $session = mysqli_real_escape_string($conn, trim($row[9] ?? '')); 

                if (!empty($name) && !empty($enroll)) {
                    // Check duplicate enrollment
                    $check_sql = "SELECT id FROM students WHERE enrollment_number = '$enroll'";
                    $check_result = mysqli_query($conn, $check_sql);

                    if ($check_result && mysqli_num_rows($check_result) > 0) {
                        $duplicate_count++;
                    } else {
                        // Attempt full insert, fallback to basic columns if extra columns don't exist
                        $sql = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, section, year, sem, date_of_admission, session) 
                                VALUES ('$name', '$enroll', '$roll', '$faculty', '$course', '$section', $year, '$sem', '$admission_date', '$session')";
                        
                        if (mysqli_query($conn, $sql)) {
                            $success_count++;
                        } else {
                            $fallback_sql = "INSERT INTO students (name, enrollment_number, course, sem, faculty) 
                                             VALUES ('$name', '$enroll', '$course', '$sem', '$faculty')";
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
        $msg = "CSV Import Complete! Imported: $success_count, Skipped (Duplicates/Errors): $duplicate_count";
    } else {
        $error = "Please upload a valid CSV file.";
    }
}

// --- HANDLER: DELETE STUDENT ---
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $conn->query("DELETE FROM students WHERE id = $did");
    $msg = "Student deleted successfully!";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Students | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }</style>
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
        <div class="row g-4">
            <div class="col-lg-4">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded-4 shadow-sm" id="studentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-4 fw-semibold small" id="single-tab" data-bs-toggle="pill" data-bs-target="#single-add" type="button" role="tab">Single Add</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-4 fw-semibold small" id="bulk-tab" data-bs-toggle="pill" data-bs-target="#bulk-add" type="button" role="tab">Bulk CSV Upload</button>
                    </li>
                </ul>

                <div class="tab-content" id="studentTabContent">
                    <!-- TAB 1: Single Student Form -->
                    <div class="tab-pane fade show active" id="single-add" role="tabpanel">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                            <h5 class="fw-bold mb-3">Add Student</h5>
                            <form method="POST">
                                <div class="mb-3"><label class="form-label small fw-semibold">Student Name *</label><input type="text" name="student_name" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Enrollment / Roll No *</label><input type="text" name="enrollment" class="form-control" required></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Course Name</label><input type="text" name="course_name" class="form-control"></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Semester</label><input type="text" name="semester" class="form-control"></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Faculty</label>
                                    <select name="faculty_name" class="form-select">
                                        <option value="" selected disabled>Select Faculty</option>
                                        <?php $fr = $conn->query("SELECT DISTINCT faculty_name FROM faculty"); while($f = $fr->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($f['faculty_name']) ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" name="add_student" class="btn btn-success w-100 rounded-pill fw-semibold">Save Student</button>
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
                                    <strong>Format:</strong> Name, Enroll, Roll, Faculty, Course, Section, Year, Sem, Admission Date, Session
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
                    <h5 class="fw-bold mb-3">Students List</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>#</th><th>Name</th><th>Enrollment</th><th>Course</th><th>Faculty</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php $res = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 50"); $i=1;
                                while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                    <td><code><?= htmlspecialchars($row['enrollment_number']) ?></code></td>
                                    <td><?= htmlspecialchars($row['course_name'] ?? ($row['course'] ?? 'N/A')) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row['faculty_name'] ?? ($row['faculty'] ?? 'N/A')) ?></span></td>
                                    <td class="text-end"><a href="manage_students.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete student?');"><i class="fa-solid fa-trash"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
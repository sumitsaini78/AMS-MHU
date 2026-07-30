<?php
include "../db_connect.php";
session_start();

// 1. Admin Authentication Check
if (!isset($_SESSION['admin_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}

$msg = ""; 
$error = "";

// 2. Handle CSV Sample Download
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

// 3. Process Single Subject Manual Add
if (isset($_POST['add_subject'])) {
    $subject_name = trim($_POST['subject_name']);
    $course_name  = trim($_POST['course_name']);
    $semester     = trim($_POST['semester']);
    
    if (!empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, course_name, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject_name, $course_name, $semester);
        if ($stmt->execute()) { 
            $msg = "Subject added successfully!"; 
        } else { 
            $error = "Error: " . $conn->error; 
        }
        $stmt->close();
    } else { 
        $error = "Subject name is required."; 
    }
}

// 4. Process Bulk CSV Import with Duplicate Check
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
        
        $msg = "CSV Import Complete! Successfully Imported: $success_count, Skipped/Duplicates: $duplicate_count";
    } else {
        $error = "Please select a valid CSV file.";
    }
}

// 5. Delete Subject Handler
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $conn->query("DELETE FROM subjects WHERE course_id = $did");
    $msg = "Subject deleted successfully!";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Subjects | Admin</title>
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
                                <div class="mb-3"><label class="form-label small fw-semibold">Course Name</label><input type="text" name="course_name" class="form-control"></div>
                                <div class="mb-3"><label class="form-label small fw-semibold">Semester</label><input type="text" name="semester" class="form-control"></div>
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
                    <h5 class="fw-bold mb-3">Subjects List</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>#</th><th>Subject Name</th><th>Course</th><th>Semester</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php 
                                $res = $conn->query("SELECT * FROM subjects ORDER BY course_id DESC"); $i=1;
                                while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                    <td class="text-end"><a href="manage_subjects.php?delete_id=<?= $row['course_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete subject?');"><i class="fa-solid fa-trash"></i></a></td>
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
<?php
include "../db_connect.php";
session_start();

// 1. Security Check
if (!isset($_SESSION['dean_id'])) {
    header("Location: ../index.php");
    exit;
}

$faculty_name = $_SESSION['faculty_name'];

// 2. Handle Dynamic CSV Sample Download
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_subjects.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['course_name', 'year', 'semester', 'subject_name', 'subject_code']);
    fputcsv($output, ['B.Sc CS', '2026', '1', 'Introduction to Programming', 'CS-101']);
    fputcsv($output, ['B.Sc CS', '2026', '1', 'Mathematics-I', 'MAT-101']);
    fclose($output);
    exit;
}

// 3. Process Manual Entry
if (isset($_POST['insert_subject'])) {
    if (!empty($_POST['course_name']) && !empty($_POST['subject_name'])) {
        $stmt = $conn->prepare("INSERT INTO subjects (course_name, Year, semester, subject_name, faculty_name, subject_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siisss", $_POST['course_name'], (int)$_POST['year'], (int)$_POST['semester'], $_POST['subject_name'], $faculty_name, $_POST['subject_code']);
        
        if ($stmt->execute()) {
            echo "<script>alert('Subject added successfully!');</script>";
        } else {
            echo "<script>alert('Database Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

// 4. Process CSV Import
// 4. Process CSV Import (Updated Loop)
elseif (isset($_POST['import_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
        
        // Skip the header row
        fgetcsv($handle); 

        $stmt = $conn->prepare("INSERT INTO subjects (course_name, Year, semester, subject_name, faculty_name, subject_code) VALUES (?, ?, ?, ?, ?, ?)");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Store values in variables first to avoid the "passed by reference" error
            $course = $data[0];
            $year = (int)$data[1];
            $sem = (int)$data[2];
            $sub_name = $data[3];
            $sub_code = $data[4];

            // Now pass these variables, which are safe references
            $stmt->bind_param("siisss", $course, $year, $sem, $sub_name, $faculty_name, $sub_code);
            $stmt->execute();
        }
        
        fclose($handle);
        $stmt->close();
        echo "<script>alert('Bulk import complete!');</script>";
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Manage Subjects | Dean Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .form-label { font-weight: 600; color: #495057; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Subject Management</h2>
            </div>
            <a href="index.php" class="btn btn-outline-dark"><i class="fa-solid fa-arrow-left me-2"></i>Dashboard</a>
        </div>

        <div class="row g-4">
            <!-- Bulk Import -->
            <div class="col-lg-12">
                <div class="card main-card p-4">
                    <h5 class="mb-3 text-success"><i class="fa-solid fa-file-csv me-2"></i>Bulk Import</h5>
                    <a href="?download_sample=1" class="btn btn-sm btn-outline-secondary mb-3 w-25"><i class="fa-solid fa-download me-1"></i> Download Sample CSV</a>
                    <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="import_csv" class="btn btn-success w-100">Upload Data</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Manual Entry -->
            <div class="col-lg-12">
                <div class="card main-card p-4">
                    <h5 class="mb-4 text-primary"><i class="fa-solid fa-plus-circle me-2"></i>Manual Entry</h5>
                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <h6>Faculty: <?php echo htmlspecialchars($faculty_name); ?></h6>
                            <label class="form-label">Course</label>
                            <?php
                            $stmt = $conn->prepare("SELECT DISTINCT course_name FROM courses_list WHERE faculty_name = ?");
                            $stmt->bind_param("s", $faculty_name);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            echo '<select name="course_name" class="form-select" required><option value="">-- Select Course --</option>';
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row['course_name']) . '">' . htmlspecialchars($row['course_name']) . '</option>';
                            }
                            echo '</select>';
                            $stmt->close();
                            ?>
                        </div>
                        <div class="col-md-8"><label class="form-label">Subject Name</label><input type="text" class="form-control" name="subject_name" required></div>
                        <div class="col-md-4"><label class="form-label">Subject Code</label><input type="text" class="form-control" name="subject_code" required></div>
                        <div class="col-md-6"><label class="form-label">Year</label><input type="number" class="form-control" name="year" required></div>
                        <div class="col-md-6"><label class="form-label">Semester</label><input type="number" class="form-control" name="semester" required></div>
                        <div class="col-12"><button type="submit" name="insert_subject" class="btn btn-primary px-5">Add Subject</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
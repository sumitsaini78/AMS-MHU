<?php
session_start();
include "../db_connect.php";

// Security Check
if (!isset($_SESSION['faculty_name'])) {
    header("Location: ./index.php");
    exit;
}

$faculty_name = $_SESSION['faculty_name'];
$message = "";

// --- HANDLER: DOWNLOAD SAMPLE CSV ---
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="teachers_sample.csv"');
    $output = fopen('php://output', 'w');
    // Add header and sample rows with designation
    fputcsv($output, ['Name', 'Designation', 'Number']);
    fputcsv($output, ['Dr. Snehashish Bhardwaj', 'Professor', '333']);
    fputcsv($output, ['Prof. John Smith', 'Associate Professor', '9876543210']);
    fclose($output);
    exit;
}

// Form Submission Handlers
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    $form_type = $_POST['form_type'];

    // --- HANDLER: SINGLE TEACHER ---
    if ($form_type === 'single_teacher') {
        $teacher_name = trim($_POST['teacher_name']);
        $designation = trim($_POST['teacher_designation']);
        $number = trim($_POST['teacher_number']);

        if (!empty($teacher_name) && !empty($designation) && !empty($number)) {
            
            // Check for duplicate entry (by number or same name in same faculty)
            $check_stmt = $conn->prepare("SELECT id FROM `teachers` WHERE number = ? OR (name = ? AND faculty = ?)");
            $check_stmt->bind_param("iss", $number, $teacher_name, $faculty_name);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $message = "warning|⚠️ Teacher with this name or mobile number already exists!";
            } else {
                $stmt = $conn->prepare("INSERT INTO `teachers` (name, designation, faculty, number) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $teacher_name, $designation, $faculty_name, $number);
                
                if ($stmt->execute()) {
                    $message = "success|🎉 Teacher <b>" . htmlspecialchars($teacher_name) . "</b> registered successfully!";
                } else {
                    $message = "danger|❌ System Error: " . $conn->error;
                }
                $stmt->close();
            }
            $check_stmt->close();

        } else {
            $message = "warning|⚠️ Please fill in all required input fields.";
        }
    }

    // --- HANDLER: BULK CSV UPLOAD ---
    if ($form_type === 'bulk_upload') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $file_extension = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
            
            if (strtolower($file_extension) === 'csv') {
                $filename = $_FILES['csv_file']['tmp_name'];
                $handle = fopen($filename, "r");
                
                if ($handle !== FALSE) {
                    $is_first_row = true;
                    $success_count = 0;
                    $error_count = 0;

                    $stmt = $conn->prepare("INSERT INTO `teachers` (name, designation, faculty, number) VALUES (?, ?, ?, ?)");

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Skip header row if it contains text labels like 'name'
                        if ($is_first_row) {
                            $is_first_row = false;
                            if (isset($data[0]) && stripos($data[0], 'name') !== false) {
                                continue;
                            }
                        }

                        // Expecting 3 columns: Name, Designation, Number
                        if (count($data) >= 3) {
                            $t_name = trim($data[0]);
                            $t_desig = trim($data[1]);
                            $t_num = trim($data[2]);

                            if (!empty($t_name) && !empty($t_desig) && !empty($t_num)) {
                                
                                // Check if duplicate exists before inserting
                                $check_stmt = $conn->prepare("SELECT id FROM `teachers` WHERE number = ?");
                                $check_stmt->bind_param("i", $t_num);
                                $check_stmt->execute();
                                $check_stmt->store_result();

                                if ($check_stmt->num_rows > 0) {
                                    $error_count++; // Skip duplicate and count as failed/skipped
                                } else {
                                    $stmt->bind_param("sssi", $t_name, $t_desig, $faculty_name, $t_num);
                                    if ($stmt->execute()) {
                                        $success_count++;
                                    } else {
                                        $error_count++;
                                    }
                                }
                                $check_stmt->close();
                            }
                        }
                    }
                    fclose($handle);
                    $stmt->close();
                    $message = "success|🎉 Bulk upload complete! Successfully added <b>$success_count</b> teachers." . ($error_count > 0 ? " ($error_count rows skipped/failed due to duplicates or errors)" : "");
                } else {
                    $message = "danger|❌ Error opening the uploaded CSV file.";
                }
            } else {
                $message = "warning|⚠️ Please upload a valid .csv file format.";
            }
        } else {
            $message = "warning|⚠️ Please select a CSV file to upload.";
        }
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Manage Teachers | MHU-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .form-card { border: none; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .input-group-text { background-color: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        .form-control:focus { box-shadow: none; border-color: #dee2e6; }
        .input-group:focus-within .input-group-text { border-color: #86b7fe; color: #0d6efd; }
        .input-group:focus-within .form-control { border-color: #86b7fe; }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap text-warning me-2"></i>MHU-AMS
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary border-opacity-50 px-3 py-1.5 rounded-pill small">
                        <i class="fa-solid fa-building me-1 text-warning"></i> Faculty: <span class="fw-semibold"><?php echo htmlspecialchars($faculty_name); ?></span>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-light px-3"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <!-- Status Feedback Alerts Notification Block -->
        <?php if (!empty($message)): 
            $parts = explode('|', $message);
        ?>
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="alert alert-<?php echo $parts[0]; ?> alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center">
                            <div><?php echo $parts[1]; ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            
            <!-- LEFT COLUMN: Add Single Teacher -->
            <div class="col-12 col-lg-5">
                <div class="form-card card p-4 p-md-5 h-100">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-user-plus fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Add Single Teacher</h4>
                        <p class="text-muted small">Register an individual faculty instructor</p>
                    </div>

                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="form_type" value="single_teacher">
                        
                        <div class="mb-3">
                            <label for="teacher_name" class="form-label small fw-bold text-secondary">Teacher Name</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control" name="teacher_name" id="teacher_name" placeholder="E.g., Dr. John Doe" required autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="teacher_designation" class="form-label small fw-bold text-secondary">Designation</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-id-badge"></i></span>
                                <input type="text" class="form-control" name="teacher_designation" id="teacher_designation" placeholder="E.g., Professor / Assistant Professor" required autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="teacher_number" class="form-label small fw-bold text-secondary">Teacher Mobile/ID Number</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-phone"></i></span>
                                <input type="number" class="form-control" name="teacher_number" id="teacher_number" placeholder="E.g., 9876543210" required autocomplete="off">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="add_teacher" class="btn btn-primary py-2.5 fw-bold shadow-sm">
                                <i class="fa-solid fa-plus-circle me-1"></i> Register Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Bulk Add Teachers via CSV -->
            <div class="col-12 col-lg-5">
                <div class="form-card card p-4 p-md-5 h-100">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success p-3 rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-file-csv fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Bulk Upload Teachers</h4>
                        <p class="text-muted small">Import multiple teachers via a CSV file</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="form_type" value="bulk_upload">
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="csv_file" class="form-label small fw-bold text-secondary mb-0">Select CSV File</label>
                                <a href="?download_sample=1" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-download me-1"></i> Download Sample CSV
                                </a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-file-arrow-up"></i></span>
                                <input type="file" class="form-control" name="csv_file" id="csv_file" accept=".csv" required>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-4 border small text-muted">
                            <span class="fw-bold text-dark"><i class="fa-solid fa-circle-info text-info me-1"></i> CSV Format Note:</span>
                            <p class="mb-1 mt-1">Your CSV file should look like this (Column 1 = Name, Column 2 = Designation, Column 3 = Number):</p>
                            <code class="d-block bg-white p-2 border rounded text-dark">
                                Name,Designation,Number<br>
                                Dr. Snehashish Bhardwaj,Professor,333<br>
                                Prof. John Smith,Associate Professor,9876543210
                            </code>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-2.5 fw-bold shadow-sm">
                                <i class="fa-solid fa-upload me-1"></i> Upload and Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-link text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Return to Dashboard</a>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <!-- Bootstrap Validation Script -->
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
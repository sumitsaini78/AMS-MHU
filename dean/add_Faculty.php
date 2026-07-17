<?php
include "../db_connect.php";
session_start();

// 1. Session Protection: Secure page under Dean authentication context
if (!isset($_SESSION['dean_id'])) {
    header("Location: ../index.php");
    exit;
}

$dean_id = $_SESSION['dean_id'];

// Fetch Dean Name for dynamic navbar sync
$query_dean = "SELECT * FROM deans WHERE id = '$dean_id'";
$result_dean = mysqli_query($conn, $query_dean);
$dean_name = ($result_dean && mysqli_num_rows($result_dean) == 1) ? mysqli_fetch_assoc($result_dean)['Dean_name'] : "Dean";

// 2. Form Submission Handler with Safety Sanitation Filters
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dept_name'])) {
    // Escaping to prevent basic SQL Injection vulnerabilities
    $department_name = mysqli_real_escape_string($conn, trim($_POST['dept_name']));
    $department_full_name = mysqli_real_escape_string($conn, trim($_POST['dept_full_name']));
    
    if (!empty($department_name) && !empty($department_full_name)) {
        // Checking duplicate entry barrier index mapping
        $check_duplicate = mysqli_query($conn, "SELECT * FROM `faculty` WHERE dep_name = '$department_name'");
        
        if (mysqli_num_rows($check_duplicate) > 0) {
            $message = "danger|⚠️ Department short code already exists!";
        } else {
            $query = "INSERT INTO `faculty` (dep_name, full_name) VALUES ('$department_name', '$department_full_name')";
            if (mysqli_query($conn, $query)) {
                $message = "success|🎉 Department registered successfully into indices directory!";
            } else {
                $message = "danger|❌ System Error: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "warning|⚠️ Please populate all input parameters correctly.";
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Add Department | Dean Console</title>
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
                    <i class="fa-solid fa-graduation-cap text-warning me-2"></i>MHU-AMS <sub class="text-primary text-uppercase ms-1 fw-semibold" style="font-size: 0.65rem; letter-spacing: 1px;">Dean</sub>
                </a>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary border-opacity-50 px-3 py-1.5 rounded-pill small">
                        <i class="fa-solid fa-user-tie me-1 text-warning"></i> Welcome, <span class="fw-semibold"><?php echo htmlspecialchars($dean_name); ?></span>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-light px-3"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-5 mx-auto">
                
                <!-- Status Feedback Alerts Notification Block -->
                <?php if (!empty($message)): 
                    $parts = explode('|', $message);
                ?>
                    <div class="alert alert-<?php echo $parts[0]; ?> alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                        <div class="d-flex align-items-center">
                            <div><?php echo $parts[1]; ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Primary Form Card Content -->
                <div class="form-card card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info p-3 rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-network-wired fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Create Department</h4>
                        <p class="text-muted small">Register institutional structural divisions metrics map</p>
                    </div>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <!-- Department Short Identity Input -->
                        <div class="mb-3">
                            <label for="dept_name" class="form-label small fw-bold text-secondary">Department Code / Alias</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-address-card"></i></span>
                                <input type="text" class="form-control" name="dept_name" id="dept_name" placeholder="e.g., BBA, MBA, CSE" required autocomplete="off">
                            </div>
                            <div class="form-text text-muted extra-small" style="font-size: 0.75rem;">Use standard capitalized tracking aliases.</div>
                        </div>

                        <!-- Department Full Identity Input -->
                        <div class="mb-4">
                            <label for="dept_full_name" class="form-label small fw-bold text-secondary">Department Full Title</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-font"></i></span>
                                <input type="text" class="form-control" name="dept_full_name" id="dept_full_name" placeholder="e.g., Bachelor of Business Administration" required autocomplete="off">
                            </div>
                        </div>

                        <!-- Execution Commit Action Trigger Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2.5 fw-bold shadow-sm"><i class="fa-solid fa-plus-circle me-1"></i> Add Department System</button>
                            <a href="index.php" class="btn btn-link btn-sm text-decoration-none text-muted mt-1">Cancel & Return</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <!-- Bootstrap Client-side Validation Interceptor -->
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
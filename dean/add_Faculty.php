<?php
    include "../db_connect.php";
    session_start();

    // 1. Session Protection: Secure page under Dean authentication context
    if (!isset($_SESSION['dean_id'])) {
        header("Location: ../index.php");
        exit;
    }

    $dean_id = $_SESSION['dean_id'];

    // Fetch Dean Name for dynamic navbar sync using Prepared Statements
    $stmt_dean = $conn->prepare("SELECT * FROM deans WHERE id = ?");
    $stmt_dean->bind_param("i", $dean_id);
    $stmt_dean->execute();
    $result_dean = $stmt_dean->get_result();
    $dean_name = ($result_dean && $result_dean->num_rows == 1) ? $result_dean->fetch_assoc()['Dean_name'] : "Dean";
    $stmt_dean->close();

    // 2. Form Submission Handlers with Prepared Statements
    $message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];

        // --- HANDLER: ADD FACULTY ---
        if ($form_type === 'add_faculty') {
            $department_name = trim($_POST['dept_name']);
            $department_full_name = trim($_POST['dept_full_name']);
            
            if (!empty($department_name) && !empty($department_full_name)) {
                // Checking duplicate entry barrier index mapping
                $stmt_check = $conn->prepare("SELECT id FROM `faculty` WHERE faculty_name = ?");
                $stmt_check->bind_param("s", $department_name);
                $stmt_check->execute();
                $stmt_check->store_result();
                
                if ($stmt_check->num_rows > 0) {
                    $message = "danger|⚠️ Faculty short code already exists!";
                } else {
                    $stmt_insert = $conn->prepare("INSERT INTO `faculty` (faculty_name, faculty_full_name) VALUES (?, ?)");
                    $stmt_insert->bind_param("ss", $department_name, $department_full_name);
                    
                    if ($stmt_insert->execute()) {
                        $message = "success|🎉 Faculty registered successfully into indices directory!";
                    } else {
                        $message = "danger|❌ System Error: " . $conn->error;
                    }
                    $stmt_insert->close();
                }
                $stmt_check->close();
            } else {
                $message = "warning|⚠️ Please populate all input parameters correctly.";
            }
        }

        // --- HANDLER: ADD DEPARTMENT ---
        if ($form_type === 'add_department') {
            $faculty_data = trim($_POST['faculty_selection']);
            $department_name = trim($_POST['department_name']);

            if (!empty($faculty_data) && !empty($department_name)) {
                // Extract faculty_name and faculty_full_name from the dropdown value
                list($faculty_name, $faculty_full_name) = explode('|', $faculty_data);

                // Checking duplicate entry for this specific department under the selected faculty
                $check_stmt = $conn->prepare("SELECT id FROM `faculty` WHERE department = ? AND faculty_name = ?");
                $check_stmt->bind_param("ss", $department_name, $faculty_name);
                $check_stmt->execute();
                $check_stmt->store_result();
                
                if ($check_stmt->num_rows > 0) {
                    $message = "danger|⚠️ This Department already exists under the selected Faculty!";
                } else {
                    // Insert into the `faculty` table securely
                    $insert_stmt = $conn->prepare("INSERT INTO `faculty` (faculty_name, faculty_full_name, department) VALUES (?, ?, ?)");
                    $insert_stmt->bind_param("sss", $faculty_name, $faculty_full_name, $department_name);
                    
                    if ($insert_stmt->execute()) {
                        $message = "success|🎉 Department successfully linked and registered!";
                    } else {
                        $message = "danger|❌ System Error: " . $conn->error;
                    }
                    $insert_stmt->close();
                }
                $check_stmt->close();
            } else {
                $message = "warning|⚠️ Please select a Faculty and enter a Department name.";
            }
        }
    }

    // FIX APPLIED HERE: Added DISTINCT to prevent duplicate faculties in the dropdown
    $faculty_query = "SELECT DISTINCT faculty_name, faculty_full_name FROM `faculty` ORDER BY faculty_name ASC";
    $faculty_results = mysqli_query($conn, $faculty_query);
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Manage Faculties & Departments | Dean Console</title>
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
        .form-control, .form-select { border-left: none; }
        .form-control:focus, .form-select:focus { box-shadow: none; border-color: #dee2e6; }
        .input-group:focus-within .input-group-text { border-color: #86b7fe; color: #0d6efd; }
        .input-group:focus-within .form-control, .input-group:focus-within .form-select { border-color: #86b7fe; }
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
            
            <!-- LEFT COLUMN: Create New Faculty -->
            <div class="col-12 col-lg-5">
                <div class="form-card card p-4 p-md-5 h-100">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info p-3 rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-network-wired fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Create New Faculty</h4>
                        <p class="text-muted small">Register institutional structural divisions metrics map</p>
                    </div>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="form_type" value="add_faculty">
                        
                        <div class="mb-3">
                            <label for="dept_name" class="form-label small fw-bold text-secondary">Faculty Name (Short Code)</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-address-card"></i></span>
                                <input type="text" class="form-control" name="dept_name" id="dept_name" placeholder="E.g., FOCBS" required autocomplete="off">
                            </div>
                            <div class="form-text text-muted extra-small" style="font-size: 0.75rem;">Use standard capitalized tracking aliases.</div>
                        </div>

                        <div class="mb-4">
                            <label for="dept_full_name" class="form-label small fw-bold text-secondary">Faculty Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-font"></i></span>
                                <input type="text" class="form-control" name="dept_full_name" id="dept_full_name" placeholder="E.g., Faculty of Commerce" required autocomplete="off">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2.5 fw-bold shadow-sm"><i class="fa-solid fa-plus-circle me-1"></i> Add New Faculty</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT COLUMN: Add Department -->
            <div class="col-12 col-lg-5">
                <div class="form-card card p-4 p-md-5 h-100">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success p-3 rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-building-user fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Add Department</h4>
                        <p class="text-muted small">Map a new department to an existing faculty</p>
                    </div>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="form_type" value="add_department">
                        
                        <div class="mb-3">
                            <label for="faculty_selection" class="form-label small fw-bold text-secondary">Select Target Faculty</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-layer-group"></i></span>
                                <select class="form-select" name="faculty_selection" id="faculty_selection" required>
                                    <option value="" disabled selected>Choose a Faculty...</option>
                                    <?php 
                                    if ($faculty_results && mysqli_num_rows($faculty_results) > 0) {
                                        while ($row = mysqli_fetch_assoc($faculty_results)) {
                                            $combined_value = htmlspecialchars($row['faculty_name'] . '|' . $row['faculty_full_name']);
                                            $display_text = htmlspecialchars($row['faculty_name'] . ' - ' . $row['faculty_full_name']);
                                            echo "<option value=\"$combined_value\">$display_text</option>";
                                        }
                                    } else {
                                        echo "<option value=\"\" disabled>No Faculties Found. Add one first.</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="department_name" class="form-label small fw-bold text-secondary">Department Name</label>
                            <div class="input-group">
                                <span class="input-group-text text-muted"><i class="fa-solid fa-sitemap"></i></span>
                                <input type="text" class="form-control" name="department_name" id="department_name" placeholder="E.g., Dept. of Management" required autocomplete="off">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success py-2.5 fw-bold shadow-sm"><i class="fa-solid fa-folder-plus me-1"></i> Register Department</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-link text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Cancel & Return to Dashboard</a>
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
<?php 
include "./db_connect.php";
session_start();

// Handle deletion with flash message
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $del_query = mysqli_query($conn, "DELETE FROM subjected_teacher WHERE id = '$id'");
    if ($del_query) {
        $_SESSION['flash_message'] = "Assignment deleted successfully!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to delete assignment.";
        $_SESSION['flash_type'] = "danger";
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// --- FILTER HANDLING ---
if (isset($_POST['reset_assignment_filters'])) {
    unset($_SESSION['assign_filter_teacher'], $_SESSION['assign_filter_course']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['apply_assignment_filters'])) {
    $_SESSION['assign_filter_teacher'] = trim($_POST['assign_filter_teacher'] ?? '');
    $_SESSION['assign_filter_course'] = trim($_POST['assign_filter_course'] ?? '');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$filter_teacher = $_SESSION['assign_filter_teacher'] ?? '';
$filter_course = $_SESSION['assign_filter_course'] ?? '';

// Build WHERE clause securely
$where_clause = "WHERE 1=1";
if (!empty($filter_teacher)) {
    $where_clause .= " AND teacher_id = '" . intval($filter_teacher) . "'";
}
if (!empty($filter_course)) {
    $where_clause .= " AND course_name = '" . mysqli_real_escape_string($conn, $filter_course) . "'";
}

// --- CSV EXPORT: ASSIGNMENTS / RESULTS ---
if (isset($_GET['export']) && $_GET['export'] == 'assigned') {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=teacher_subject_assignments_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('#', 'Teacher Name', 'Assigned Subject', 'Course Name', 'Year', 'Semester', 'Subject Code'));
    
    $assigned_query = "SELECT * FROM subjected_teacher $where_clause ORDER BY id DESC";
    $assigned_res = mysqli_query($conn, $assigned_query);
    $counter = 1;
    while ($row = mysqli_fetch_assoc($assigned_res)) {
        fputcsv($output, array(
            $counter++,
            $row['teacher_name'],
            $row['subject_name'],
            $row['course_name'],
            $row['year'],
            $row['semester'],
            $row['subject_code']
        ));
    }
    fclose($output);
    exit();
}

// --- CSV EXPORT: ALL TEACHERS WITH SUBJECTS ---
if (isset($_GET['export']) && $_GET['export'] == 'teachers') {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=all_teachers_with_subjects_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('#', 'Teacher Name', 'Designation', 'Faculty', 'Assigned Subjects'));
    
    $all_teachers_query = "SELECT t.*, GROUP_CONCAT(st.subject_name SEPARATOR ', ') as assigned_subjects 
                           FROM teachers t 
                           LEFT JOIN subjected_teacher st ON t.id = st.teacher_id 
                           GROUP BY t.id";
    $teachers_list_res = mysqli_query($conn, $all_teachers_query);
    $t_counter = 1;
    while ($t_row = mysqli_fetch_assoc($teachers_list_res)) {
        fputcsv($output, array(
            $t_counter++,
            $t_row['name'],
            $t_row['designation'],
            $t_row['faculty'],
            $t_row['assigned_subjects'] ?? 'No Subject Assigned'
        ));
    }
    fclose($output);
    exit();
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Manage Teachers and Subjects - Mhu-AMS</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            body { background-color: #f4f6f9; }
            .filter-box { background-color: #ffffff; border-radius: 10px; padding: 16px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
            .card { border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        </style>
    </head>

    <body>  
        <header>
            <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
                <div class="container-fluid px-4">
                    <span class="navbar-brand mb-0 h1 fs-4 fw-bold tracking-wide"><i class="fa-solid fa-graduation-cap me-2"></i>Mhu-AMS</span>
                    <div class="d-flex align-items-center gap-3">
                        <a href="index.php" class="text-white text-decoration-none fw-semibold"><i class="fa-solid fa-house me-1"></i> Home</a>
                    </div>
                </div>
            </nav>
        </header>

        <main class="container my-5">
            <!-- Flash Message Display -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i><?php echo $_SESSION['flash_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>

            <div class="row">
                <!-- Main Content Container -->
                <div class="col-md-12">
                    <div class="card rounded-4 overflow-hidden">
                        <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-chalkboard-user me-2"></i>Teacher and Subject List</h5>
                                <p class="text-muted small mb-0 text-white-50">Manage academic assignments and export department reports.</p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="?export=assigned" class="btn btn-sm btn-success fw-semibold px-3 py-2 shadow-sm">
                                    <i class="fa-solid fa-file-excel me-1"></i> Export Assignments CSV
                                </a>
                                <a href="?export=teachers" class="btn btn-sm btn-info text-dark fw-semibold px-3 py-2 shadow-sm">
                                    <i class="fa-solid fa-file-excel me-1"></i> Export All Teachers CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- FILTER PANEL -->
                            <div class="filter-box">
                                <form method="POST" action="" class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold text-secondary mb-1">Filter by Teacher</label>
                                        <select class="form-select" name="assign_filter_teacher">
                                            <option value="">-- All Teachers --</option>
                                            <?php 
                                            $t_filter_res = mysqli_query($conn, "SELECT id, name FROM teachers ORDER BY name ASC");
                                            while ($tf = mysqli_fetch_assoc($t_filter_res)) {
                                                $selected = ($filter_teacher == $tf['id']) ? 'selected' : '';
                                                echo '<option value="' . $tf['id'] . '" ' . $selected . '>' . htmlspecialchars($tf['name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary mb-1">Filter by Course</label>
                                        <select class="form-select" name="assign_filter_course">
                                            <option value="">-- All Courses --</option>
                                            <?php 
                                            $c_filter_res = mysqli_query($conn, "SELECT DISTINCT course_name FROM subjects WHERE course_name IS NOT NULL ORDER BY course_name ASC");
                                            while ($cf = mysqli_fetch_assoc($c_filter_res)) {
                                                $selected = ($filter_course == $cf['course_name']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($cf['course_name']) . '" ' . $selected . '>' . htmlspecialchars($cf['course_name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" name="apply_assignment_filters" class="btn btn-primary w-100 fw-semibold shadow-sm"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                                        <?php if (!empty($filter_teacher) || !empty($filter_course)): ?>
                                            <button type="submit" name="reset_assignment_filters" class="btn btn-outline-secondary w-100 fw-semibold" title="Reset Filters"><i class="fa-solid fa-rotate-right me-1"></i> Reset</button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>

                            <!-- DATA TABLE -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border">
                                    <thead class="table-light text-uppercase fs-7">
                                        <tr>
                                            <th class="py-3 ps-3">#</th>
                                            <th class="py-3">Teacher Name</th>
                                            <th class="py-3">Assigned Subject</th>
                                            <th class="py-3">Course / Academic Details</th>
                                            <th class="py-3 text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $assigned_query = "SELECT * FROM subjected_teacher $where_clause ORDER BY id DESC";
                                        $assigned_res = mysqli_query($conn, $assigned_query);
                                        
                                        if (mysqli_num_rows($assigned_res) > 0) {
                                            $counter = 1;
                                            while ($row = mysqli_fetch_assoc($assigned_res)) {
                                                echo '<tr>';
                                                echo '<td class="ps-3 text-muted fw-semibold">' . $counter++ . '</td>';
                                                echo '<td class="fw-semibold text-dark">' . htmlspecialchars($row['teacher_name']) . '</td>';
                                                echo '<td><span class="badge bg-secondary px-2 py-1">' . htmlspecialchars($row['subject_name']) . '</span></td>';
                                                echo '<td><span class="text-secondary">' . htmlspecialchars($row['course_name']) . '</span> <span class="badge bg-light text-dark border ms-1">Yr: ' . $row['year'] . ' | Sem: ' . $row['semester'] . '</span></td>';
                                                echo '<td class="text-end pe-3">
                                                        <a href="?delete=' . $row['id'] . '" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="return confirm(\'Are you sure you want to delete this assignment mapping?\');" title="Delete Assignment"><i class="fa-solid fa-trash"></i></a>
                                                      </td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center text-muted py-5"><div class="py-3"><i class="fa-solid fa-folder-open fs-2 text-black-50 mb-2"></i><p class="mb-0">No assignment records found matching your filters.</p></div></td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </main>

        <!-- Bootstrap JS Bundle -->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
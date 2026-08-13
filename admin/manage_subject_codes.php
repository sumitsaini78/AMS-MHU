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

// --- FLASH MESSAGE HANDLING ---
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
}

// --- FILTER HANDLING ---
if (isset($_POST['reset_filters'])) {
    unset($_SESSION['sub_filter_course'], $_SESSION['sub_filter_sem'], $_SESSION['sub_filter_no_code']);
    $filter_course = "";
    $filter_sem = "";
    $filter_no_code = false;
    header("Location: manage_subject_codes.php");
    exit;
} else {
    if (isset($_POST['apply_filters'])) {
        $filter_course = $_POST['sub_filter_course'];
        $filter_sem = $_POST['sub_filter_sem'];
        $filter_no_code = isset($_POST['sub_filter_no_code']);
        $_SESSION['sub_filter_course'] = $filter_course;
        $_SESSION['sub_filter_sem'] = $filter_sem;
        $_SESSION['sub_filter_no_code'] = $filter_no_code;
        header("Location: manage_subject_codes.php");
        exit;
    } else {
        $filter_course = $_SESSION['sub_filter_course'] ?? '';
        $filter_sem = $_SESSION['sub_filter_sem'] ?? '';
        $filter_no_code = $_SESSION['sub_filter_no_code'] ?? false;
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
if ($filter_no_code) {
    $where_sql .= " AND (subject_code IS NULL OR subject_code = '')";
}

// 2. Handle Update Subject Code
if (isset($_POST['update_subject_code'])) {
    $course_id = (int)$_POST['course_id'];
    $new_code = trim($_POST['new_code']);
    
    $upd_stmt = $conn->prepare("UPDATE subjects SET subject_code = ? WHERE course_id = ?");
    $upd_stmt->bind_param("si", $new_code, $course_id);
    if ($upd_stmt->execute()) {
        $_SESSION['flash_msg'] = "Subject code updated successfully!";
    } else {
        $error = "Failed to update subject code.";
    }
    $upd_stmt->close();
    header("Location: manage_subject_codes.php");
    exit;
}

// 3. Handle Export Filtered CSV
if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="filtered_subjects_export.csv"');
    $output = fopen("php://output", "w");

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

// 7. Fetch data for dropdowns
$faculties_res = $conn->query("SELECT faculty_name, faculty_full_name FROM faculty ORDER BY faculty_full_name ASC");

$courses_res = $conn->query("
    SELECT cl.course_name, f.faculty_name AS faculty_short_name 
    FROM courses_list cl 
    LEFT JOIN faculty f ON cl.faculty_name = f.faculty_full_name 
    GROUP BY cl.course_name, f.faculty_name
    ORDER BY f.faculty_name ASC, cl.course_name ASC
");

$sem_list_res = $conn->query("SELECT DISTINCT semester FROM subjects WHERE semester IS NOT NULL ORDER BY semester ASC");
$available_semesters = [];
if ($sem_list_res) {
    while ($s_row = $sem_list_res->fetch_assoc()) {
        $available_semesters[] = $s_row['semester'];
    }
}

// 8. Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$count_query = "SELECT COUNT(*) AS total FROM subjects $where_sql";
$count_res = $conn->query($count_query);
$total_records = $count_res ? $count_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_records / $limit);
?>
<!doctype html>
<html lang="en">

<head>
    <title>Manage Subject Codes | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .filter-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <main class="container py-5">
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-barcode text-secondary me-2"></i>Manage Subject Codes
        </h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-success rounded-4 shadow-sm"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-4 shadow-sm"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Subjects List</h5>
                        <a href="manage_subject_codes.php?export_csv=1"
                            class="btn btn-sm btn-success rounded-pill fw-semibold px-3">
                            <i class="fa-solid fa-file-excel me-1"></i> Export Filtered CSV
                        </a>
                    </div>

                    <div class="filter-box">
                        <form method="POST" action="manage_subject_codes.php" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Filter by
                                    Course:</label>
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
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Filter by
                                    Semester:</label>
                                <select class="form-select form-select-sm" name="sub_filter_sem">
                                    <option value="">-- All Semesters --</option>
                                    <?php foreach ($available_semesters as $sem_val): ?>
                                        <option value="<?= htmlspecialchars($sem_val) ?>" <?= ($filter_sem == $sem_val) ? 'selected' : '' ?>>
                                            Semester <?= htmlspecialchars($sem_val) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-2 pt-2">
                                    <input class="form-check-input" type="checkbox" name="sub_filter_no_code" id="noCodeCheck" <?= $filter_no_code ? 'checked' : '' ?>>
                                    <label class="form-check-label small fw-semibold text-secondary" for="noCodeCheck">
                                        Without Subject Code
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex gap-1">
                                <button type="submit" name="apply_filters"
                                    class="btn btn-sm btn-primary w-100 fw-semibold"><i
                                        class="fa-solid fa-filter me-1"></i>Filter</button>
                                <?php if (!empty($filter_course) || !empty($filter_sem) || $filter_no_code): ?>
                                    <button type="submit" name="reset_filters"
                                        class="btn btn-sm btn-outline-secondary w-100 fw-semibold"
                                        title="Reset Filters">Reset</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Subject Name</th>
                                    <th>Course</th>
                                    <th>Year/Sem</th>
                                    <th>Update Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM subjects $where_sql ORDER BY course_id DESC LIMIT $limit OFFSET $offset";
                                $res = $conn->query($query);
                                $i = $offset + 1;

                                if ($res && $res->num_rows > 0) {
                                    while ($row = $res->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($row['subject_name']) ?></td>
                                            <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($row['Year'] ?? '') ?> / Sem
                                                <?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                            <td>
                                                <form method="POST" action="manage_subject_codes.php" class="d-flex align-items-center gap-2 m-0">
                                                    <input type="hidden" name="course_id" value="<?= $row['course_id'] ?>">
                                                    <input type="text" name="new_code" class="form-control form-control-sm" style="width: 120px;" placeholder="Code..." value="<?= htmlspecialchars($row['subject_code'] ?? '') ?>">
                                                    <button type="submit" name="update_subject_code" class="btn btn-sm btn-primary">Update</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                } else {
                                    echo '<tr><td colspan="6" class="text-center text-muted py-4">No subjects found matching the selected filters.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <?php include 'admin_navbar.php'; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

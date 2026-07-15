<?php
include "../db_connect.php";
session_start();

// 1. Security Check
if (!isset($_SESSION['dean_id'])) { 
    header("Location: ./index.php"); 
    exit; 
}

// 2. Capture Course Name
$course = mysqli_real_escape_string($conn, $_POST['course_name'] ?? $_GET['course_name'] ?? '');

if (empty($course)) { 
    header("Location: index.php"); 
    exit(); 
}

// 3. Logic for Date Filtering (Default: Today's Date)
$today_dmy = date('dmy'); // Format: 150726
$sql_where = "WHERE course = '$course'";
$filter_text = "Showing records for: Today (" . date('d/m/Y') . ")";

if (!empty($_GET['date_from'])) {
    $d_from = date('dmy', strtotime($_GET['date_from']));
    if (!empty($_GET['date_to'])) {
        $d_to = date('dmy', strtotime($_GET['date_to']));
        $sql_where .= " AND date_of_attendence BETWEEN $d_from AND $d_to";
        $filter_text = "From " . $_GET['date_from'] . " to " . $_GET['date_to'];
    } else {
        $sql_where .= " AND date_of_attendence = $d_from";
        $filter_text = "For date: " . $_GET['date_from'];
    }
} else {
    // Apply today's filter automatically
    $sql_where .= " AND date_of_attendence = $today_dmy";
}

// 4. Get Summary Stats (Calculated dynamically based on filter)
$stats_query = "SELECT 
                    COUNT(DISTINCT date_of_attendence) as total_lectures,
                    COUNT(*) as total_entries,
                    SUM(CASE WHEN LOWER(attendance_status) IN ('present', '1') THEN 1 ELSE 0 END) as present_count
                FROM `attendance` $sql_where";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
$percentage = ($stats['total_entries'] > 0) ? round(($stats['present_count'] / $stats['total_entries']) * 100, 2) : 0;

// 5. Get Detailed Records
$list_res = mysqli_query($conn, "SELECT * FROM `attendance` $sql_where ORDER BY date_of_attendence DESC");
?>

<!doctype html>
<html lang="en">
<head>
    <title>Course Details | <?php echo htmlspecialchars($course); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <a href="index.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0"><?php echo htmlspecialchars($course); ?> Performance</h3>
            <span class="badge bg-primary px-3 py-2"><?php echo $filter_text; ?></span>
        </div>

        <!-- Dashboard Widgets -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card p-3 border-start border-primary border-4 shadow-sm">
                    <small class="text-muted">Total Lectures Conducted</small>
                    <h2 class="fw-bold mt-1"><?php echo $stats['total_lectures']; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-start border-success border-4 shadow-sm">
                    <small class="text-muted">Attendance Rate</small>
                    <h2 class="fw-bold mt-1 text-success"><?php echo $percentage; ?>%</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-start border-info border-4 shadow-sm">
                    <small class="text-muted">Total Student Records</small>
                    <h2 class="fw-bold mt-1"><?php echo $stats['total_entries']; ?></h2>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($course); ?>">
                    <div class="col-md-4">
                        <label class="form-label small">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo $_GET['date_from'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $_GET['date_to'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Apply Filter</button>
                        <a href="course_details.php?course_name=<?php echo urlencode($course); ?>" class="btn btn-outline-danger">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><strong>Detailed Logs</strong></div>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Roll No</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($list_res) > 0) {
                        while($row = mysqli_fetch_assoc($list_res)) { 
                            $status_color = (strtolower($row['attendance_status']) == 'present') ? 'text-success' : 'text-danger';
                            echo "<tr>
                                    <td>{$row['date_of_attendence']}</td>
                                    <td>{$row['student_name']}</td>
                                    <td>{$row['roll_number']}</td>
                                    <td>{$row['subject_name']}</td>
                                    <td class='fw-bold {$status_color}'>{$row['attendance_status']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No records found for this period.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
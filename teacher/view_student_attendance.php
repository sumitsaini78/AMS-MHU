<?php
include "../db_connect.php";

session_start();
if (!isset($_SESSION['teacher_name']) || empty($_SESSION['teacher_name'])) {
    header("Location: ../index.php");
    exit();
}

// 1. Process Post & Redirect (PRG Pattern) to prevent Form Resubmission error on reload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_name'])) {
    $_SESSION['subject_name'] = $_POST['subject_name'];
    header("Location: view_student_attendance.php");
    exit();
}

// 2. Enforce fallback routing states if subject parameters missing
if (!isset($_SESSION['subject_name'])) {
    header("Location: student_attendence.php");
    exit();
}

$current_subject = $_SESSION['subject_name'];

// 3. Handle Date Filters from GET request
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// 4. Query to calculate attendance ONLY for students assigned to this subject
$query = "SELECT 
            ss.student_name AS name, 
            ss.roll_number,
            COUNT(a.attendance_status) AS total_days,
            SUM(CASE WHEN LOWER(a.attendance_status) = 'present' OR a.attendance_status = '1' THEN 1 ELSE 0 END) AS present_days
          FROM `subjected_student` ss
          LEFT JOIN `attendance` a 
            ON ss.roll_number = a.roll_number 
            AND a.subject_name = ss.subject_name";

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND a.date_of_attendence BETWEEN ? AND ?";
}

$query .= " WHERE ss.subject_name = ?
            GROUP BY ss.roll_number, ss.student_name 
            ORDER BY ss.roll_number ASC";

$stmt = mysqli_prepare($conn, $query);

if (!empty($from_date) && !empty($to_date)) {
    mysqli_stmt_bind_param($stmt, "sss", $from_date, $to_date, $current_subject);
} else {
    mysqli_stmt_bind_param($stmt, "s", $current_subject);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// =========================================================
// 5. EXPORT TO EXCEL (CSV FORMAT) LOGIC
// =========================================================
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $filename = "Attendance_Report_" . date('Y-m-d') . ".csv";
    
    // Set headers for download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    
    // Write UTF-8 BOM for Excel compatibility (ensures special characters render correctly)
    fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
    
    // Output the column headings
    fputcsv($output, array('#', 'Student Name', 'Roll Number', 'Present Days', 'Total Days', 'Percentage (%)'));
    
    $index = 1;
    // Loop over the rows, outputting them
    while ($row = mysqli_fetch_assoc($result)) {
        $total = (int)$row['total_days'];
        $present = (int)$row['present_days'];
        $raw_pct = ($total > 0) ? round(($present / $total) * 100, 2) : 0;
        
        fputcsv($output, array(
            $index,
            $row['name'],
            $row['roll_number'],
            $present,
            $total,
            $raw_pct
        ));
        $index++;
    }
    fclose($output);
    exit(); // Exit here to ensure the HTML below isn't printed into the CSV
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>View Student Attendance</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body {
            background-color: #f8f9fa;
        }
        #mhu-text {
            color: #a2c250;
            text-shadow: 1px 2px 14px rgb(46 195 41);
        }
        .custom-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
        }
        .student-link-btn {
            background: none;
            border: none;
            color: #0d6efd;
            font-weight: 600;
            text-align: left;
            padding: 0;
            transition: color 0.15s ease-in-out;
        }
        .student-link-btn:hover {
            color: #0a58ca;
            text-decoration: underline;
            cursor: pointer;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.82rem;
            letter-spacing: 0.5px;
        }
        .table td {
            vertical-align: middle;
        }
        .progress {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS
                    <sub>Student-Records</sub></span>
                <div class="d-flex align-items-center">
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small me-3">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                        <?php echo htmlspecialchars($_SESSION['teacher_name']); ?>
                    </span>
                    <a href="student_attendence.php" class="btn btn-sm btn-outline-info me-2">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                    <a href="../logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                
                <div class="custom-card shadow-sm p-4 border border-light">
                    
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1 text-uppercase fw-bold tracking-wider px-2.5 py-1">Subject Scope</span>
                            <h3 class="fw-bold text-dark mb-0">
                                <i class="fa-solid fa-book-bookmark text-muted me-2"></i><?php echo htmlspecialchars($current_subject); ?>
                            </h3>
                        </div>
                    </div>

                    <!-- Date Range Filter & Export Form -->
                    <div class="bg-light p-3 rounded mb-4 border border-light-subtle">
                        <form method="GET" action="view_student_attendance.php" class="row g-3 align-items-end m-0">
                            <div class="col-12 col-md-auto">
                                <label for="from_date" class="form-label fw-bold text-secondary small mb-1"><i class="fa-regular fa-calendar me-1"></i> From Date:</label>
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($from_date); ?>">
                            </div>
                            <div class="col-12 col-md-auto">
                                <label for="to_date" class="form-label fw-bold text-secondary small mb-1"><i class="fa-regular fa-calendar me-1"></i> To Date:</label>
                                <input type="date" id="to_date" name="to_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($to_date); ?>">
                            </div>
                            <div class="col-12 col-md-auto">
                                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                                <?php if(!empty($from_date) && !empty($to_date)): ?>
                                    <a href="view_student_attendance.php" class="btn btn-outline-secondary btn-sm px-3 ms-2">Clear</a>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Export Button -->
                            <div class="col-12 col-md-auto ms-auto">
                                <button type="submit" name="export" value="excel" class="btn btn-success btn-sm px-3 shadow-sm">
                                    <i class="fa-solid fa-file-excel me-1"></i> Export to Excel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Students Performance Grid -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th scope="col" class="ps-3" style="width: 8%">#</th>
                                    <th scope="col" style="width: 32%">Student Name</th>
                                    <th scope="col" style="width: 18%">Roll Number</th>
                                    <th scope="col" style="width: 27%">Attendance Progress</th>
                                    <th scope="col" class="text-end pe-3" style="width: 15%">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                mysqli_data_seek($result, 0);

                                $index = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total = (int)$row['total_days'];
                                    $present = (int)$row['present_days'];
                                    
                                    $raw_pct = ($total > 0) ? round(($present / $total) * 100, 2) : 0;
                                    
                                    if ($raw_pct >= 75) {
                                        $progress_color = "bg-success";
                                        $badge_color = "bg-success-subtle text-success border border-success-subtle";
                                    } elseif ($raw_pct >= 50) {
                                        $progress_color = "bg-warning";
                                        $badge_color = "bg-warning-subtle text-warning-emphasis border border-warning-subtle";
                                    } else {
                                        $progress_color = "bg-danger";
                                        $badge_color = "bg-danger-subtle text-danger border border-danger-subtle";
                                    }

                                    echo "<tr>";
                                    echo "<td class='fw-semibold text-secondary ps-3'>" . $index . "</td>";
                                    
                                    echo "<td>";
                                    echo "<form method='POST' action='student_calendar.php' class='m-0'>";
                                    echo "<input type='hidden' name='student_roll' value='" . htmlspecialchars($row['roll_number'], ENT_QUOTES, 'UTF-8') . "'>";
                                    echo "<input type='hidden' name='student_name' value='" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "'>";
                                    echo "<button type='submit' class='student-link-btn'>";
                                    echo "<i class='fa-regular fa-user me-2 text-muted small'></i>" . htmlspecialchars($row['name']) . "";
                                    echo "</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    
                                    echo "<td class='text-secondary font-monospace'>" . htmlspecialchars($row['roll_number']) . "</td>";
                                    
                                    echo "<td>";
                                    if ($total > 0) {
                                        echo "<div class='d-flex align-items-center'>";
                                        echo "<div class='progress flex-grow-1 bg-light me-2'>";
                                        echo "<div class='progress-bar " . $progress_color . "' role='progressbar' style='width: " . $raw_pct . "%' aria-valuenow='" . $raw_pct . "' aria-valuemin='0' aria-valuemax='100'></div>";
                                        echo "</div>";
                                        echo "<small class='text-muted text-nowrap'>" . $present . "/" . $total . " Days</small>";
                                        echo "</div>";
                                    } else {
                                        echo "<small class='text-muted italic'><i class='fa-solid fa-circle-minus me-1 text-black-50'></i>No records found</small>";
                                    }
                                    echo "</td>";
                                    
                                    echo "<td class='text-end pe-3'>";
                                    if ($total > 0) {
                                        echo "<span class='badge px-2.5 py-1.5 fw-bold " . $badge_color . "'>" . $raw_pct . "%</span>";
                                    } else {
                                        echo "<span class='badge px-2.5 py-1.5 bg-light text-secondary border border-secondary-subtle'>N/A</span>";
                                    }
                                    echo "</td>";
                                    
                                    echo "</tr>";
                                    $index++;
                                }   
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
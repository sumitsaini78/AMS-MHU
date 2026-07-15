<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
    exit();
}

// On view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    echo "<script>alert('" . addslashes($faculty_name) . "');</script>";
}

// Getting distinct subject data using GROUP BY to prevent duplicate subject rows
$query = "SELECT subject_name FROM `attendance` GROUP BY subject_name";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Student Attendance By Subject</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    
    <!-- FontAwesome for cleaner UI Icons -->
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
        .subject-btn {
            background: none;
            border: none;
            color: #0d6efd;
            font-weight: 500;
            text-align: left;
            padding: 0;
            text-decoration: none;
            transition: color 0.15s ease-in-out;
        }
        .subject-btn:hover {
            color: #0a58ca;
            text-decoration: underline;
            cursor: pointer;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS
                    <sub>Student-Attendance</sub></span>
                <div class="d-flex align-items-center">
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small me-3">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                        <?php echo isset($_SESSION['teacher_name']) ? htmlspecialchars($_SESSION['teacher_name']) : 'Teacher'; ?>
                    </span>
                    <a href="../logout.php" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                
                <!-- Main Content Card Wrapper -->
                <div class="custom-card shadow-sm p-4 border border-light">
                    
                    <div class="text-center mb-4">
                        <span class="text-info fs-1"><i class="fa-solid fa-graduation-cap"></i></span>
                        <h4 class="fw-bold text-dark mt-2">Select a Subject</h4>
                        <p class="text-muted small">Click on any subject below to view detailed breakdown configurations and student lists.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th scope="col" class="ps-3" style="width: 10%">#</th>
                                    <th scope="col" style="width: 65%">Subject Name</th>
                                    <th scope="col" class="text-end pe-3" style="width: 25%">Avg Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $index = 1;
                                
                                while ($val = mysqli_fetch_assoc($result)) {
                                    $subject_escaped = mysqli_real_escape_string($conn, $val['subject_name']);
                                    $status_query = "SELECT attendance_status FROM `attendance` WHERE subject_name = '$subject_escaped'";
                                    $status_result = mysqli_query($conn, $status_query);

                                    $total_records = 0;
                                    $present_count = 0;

                                    while ($status_row = mysqli_fetch_assoc($status_result)) {
                                        $attendance_status = $status_row['attendance_status'];
                                        $total_records++;
                                        if (strtolower($attendance_status) === 'present' || $attendance_status == '1') {
                                            $present_count++;
                                        }
                                    }

                                    $percentage = ($total_records > 0) ? round(($present_count / $total_records) * 100, 2) : 0;

                                    // Determine structural color metrics for the summary metric dynamic badges
                                    if ($percentage >= 75) {
                                        $badge_class = "bg-success-subtle text-success border border-success-subtle";
                                    } elseif ($percentage >= 50) {
                                        $badge_class = "bg-warning-subtle text-warning-emphasis border border-warning-subtle";
                                    } else {
                                        $badge_class = "bg-danger-subtle text-danger border border-danger-subtle";
                                    }

                                    echo "<tr>";
                                    echo "<td class='fw-semibold text-secondary ps-3'>" . $index . "</td>";
                                    echo "<td>";
                                    echo "<form method='post' action='view_student_attendance.php' class='m-0'>";
                                    echo "<button type='submit' name='subject_name' value='" . htmlspecialchars($val['subject_name'], ENT_QUOTES, 'UTF-8') . "' class='subject-btn'>";
                                    echo "<i class='fa-regular fa-folder-open me-2 text-muted'></i>" . htmlspecialchars($val['subject_name']) . "";
                                    echo "</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "<td class='text-end pe-3'><span class='badge px-2.5 py-1.5 " . $badge_class . "'>" . $percentage . "%</span></td>";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
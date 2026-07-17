<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the Dean is logged in
if (!isset($_SESSION['dean_id'])) {
    header("Location: ./index.php");
    exit;
}

$dean_id = $_SESSION['dean_id'];

// Fetch Dean Name for the Navbar
$query_dean = "SELECT * FROM deans WHERE id = '$dean_id'";
$result_dean = mysqli_query($conn, $query_dean);
$dean_name = ($result_dean && mysqli_num_rows($result_dean) == 1) ? mysqli_fetch_assoc($result_dean)['Dean_name'] : "Dean";

// ==========================================
// ACTION HANDLER: APPROVAL LOGIC (SINGLE)
// ==========================================
if (isset($_POST['action']) && $_POST['action'] == 'approve') { 
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']); 
    
    // Fetch request metadata from corrections mapping safely 
    $fetch_req = mysqli_query($conn, "SELECT * FROM `attendance_corrections` WHERE id = '$request_id'"); 
    
    if ($fetch_req && mysqli_num_rows($fetch_req) > 0) { 
        $req_data = mysqli_fetch_assoc($fetch_req); 
        
        // Target dynamic attributes mapped variables 
        $attendance_id    = $req_data['attendance_id'];
        $requested_status = $req_data['requested_status'];
        $student_name     = mysqli_real_escape_string($conn, $req_data['student_name']); 
        $roll_number      = mysqli_real_escape_string($conn, $req_data['roll_number']); 
        $subject_name     = mysqli_real_escape_string($conn, $req_data['subject_name']); 
        $date_of_att      = $req_data['date_of_attendance']; // corrected schema structural mapping key
        
        // Normalize and preserve capitalization constraints safely ('Present' / 'Absent')
        $clean_status = ucfirst(strtolower(trim($requested_status))); 
        
        // Strict transactional relational sync using composite safety logic keys
        $update_attendance = "UPDATE `attendance` 
                              SET attendance_status = '$clean_status' 
                              WHERE id = '$attendance_id' 
                                 OR (roll_number = '$roll_number' 
                                     AND date_of_attendence = '$date_of_att' 
                                     AND TRIM(subject_name) = TRIM('$subject_name'))"; 
        
        if (mysqli_query($conn, $update_attendance)) { 
            // Update correction status records logs tracking pointer index 
            mysqli_query($conn, "UPDATE `attendance_corrections` SET status = 'Approved' WHERE id = '$request_id'"); 
            
            echo "<script>
                    alert('✅ Attendance approved and student calendar synced successfully!'); 
                    window.location.href='manage_corrections.php';
                  </script>";
            exit;
        } else { 
            echo "<script>
                    alert('❌ Attendance sync error: " . addslashes(mysqli_error($conn)) . "'); 
                    window.history.back();
                  </script>";
            exit;
        }
    } else {
        echo "<script>
                alert('❌ Correction request record not found.'); 
                window.location.href='manage_corrections.php';
              </script>";
        exit;
    }
}

// ==========================================
// ACTION HANDLER: REJECTION LOGIC (SINGLE)
// ==========================================
if (isset($_POST['action']) && $_POST['action'] == 'reject') {
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']);
    
    $reject_query = "UPDATE `attendance_corrections` SET status = 'Rejected' WHERE id = '$request_id'";
    if (mysqli_query($conn, $reject_query)) {
        echo "<script>
                alert('🛑 Correction request has been rejected.'); 
                window.location.href='manage_corrections.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('❌ Error: " . addslashes(mysqli_error($conn)) . "');</script>";
    }
}

// ==========================================
// ACTION HANDLER: APPROVE ALL LOGIC
// ==========================================
if (isset($_POST['action']) && $_POST['action'] == 'approve_all') {
    $fetch_all = mysqli_query($conn, "SELECT * FROM `attendance_corrections` WHERE status = 'Pending'");
    $success_count = 0;
    $error_count = 0;

    if ($fetch_all && mysqli_num_rows($fetch_all) > 0) {
        while ($req_data = mysqli_fetch_assoc($fetch_all)) {
            $request_id       = $req_data['id'];
            $attendance_id    = $req_data['attendance_id'];
            $requested_status = $req_data['requested_status'];
            $student_name     = mysqli_real_escape_string($conn, $req_data['student_name']); 
            $roll_number      = mysqli_real_escape_string($conn, $req_data['roll_number']); 
            $subject_name     = mysqli_real_escape_string($conn, $req_data['subject_name']); 
            $date_of_att      = $req_data['date_of_attendance'];

            $clean_status = ucfirst(strtolower(trim($requested_status))); 

            // Sync structural attendance records
            $update_attendance = "UPDATE `attendance` 
                                  SET attendance_status = '$clean_status' 
                                  WHERE id = '$attendance_id' 
                                     OR (roll_number = '$roll_number' 
                                         AND date_of_attendence = '$date_of_att' 
                                         AND TRIM(subject_name) = TRIM('$subject_name'))"; 

            if (mysqli_query($conn, $update_attendance)) { 
                mysqli_query($conn, "UPDATE `attendance_corrections` SET status = 'Approved' WHERE id = '$request_id'"); 
                $success_count++;
            } else {
                $error_count++;
            }
        }

        if ($error_count == 0) {
            echo "<script>
                    alert('✅ Success! All ($success_count) pending requests approved and student calendars synced.'); 
                    window.location.href='manage_corrections.php';
                  </script>";
        } else {
            echo "<script>
                    alert('⚠️ Completed with warnings: $success_count succeeded, $error_count failed to sync.'); 
                    window.location.href='manage_corrections.php';
                  </script>";
        }
        exit;
    } else {
        echo "<script>
                alert('❌ No pending requests found to approve.'); 
                window.location.href='manage_corrections.php';
              </script>";
        exit;
    }
}

// ==========================================
// ACTION HANDLER: REJECT ALL LOGIC
// ==========================================
if (isset($_POST['action']) && $_POST['action'] == 'reject_all') {
    $reject_all_query = "UPDATE `attendance_corrections` SET status = 'Rejected' WHERE status = 'Pending'";
    
    if (mysqli_query($conn, $reject_all_query)) {
        $affected_rows = mysqli_affected_rows($conn);
        echo "<script>
                alert('🛑 Successfully rejected all ($affected_rows) pending correction requests.'); 
                window.location.href='manage_corrections.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Error processing batch rejection: " . addslashes(mysqli_error($conn)) . "'); 
                window.history.back();
              </script>";
        exit;
    }
}

// Fetch all pending requests to list in UI
$pending_requests = mysqli_query($conn, "SELECT * FROM `attendance_corrections` WHERE status = 'Pending' ORDER BY id DESC");
$pending_count = ($pending_requests) ? mysqli_num_rows($pending_requests) : 0;
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Manage Corrections | Dean Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .table-card { border: none; border-radius: 14px; background: #ffffff; overflow: hidden; }
        .table th { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-status { font-size: 0.8rem; padding: 6px 12px; border-radius: 30px; }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap text-warning me-2"></i>MHU-AMS <sub class="text-primary text-uppercase ms-1 fw-semibold">Dean</sub>
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
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Attendance Correction Verification</h2>
                <p class="text-muted small mb-md-0">Review anomalies filed by structural subject teachers and authorize student log updates directly.</p>
            </div>
            
            <!-- Dynamic Bulk Action Panel -->
            <div class="col-md-6 d-flex justify-content-center justify-content-md-end gap-2 mt-3 mt-md-0">
                <?php if ($pending_count > 0) { ?>
                    <!-- Batch Approve Button Form -->
                    <form method="POST" action="" onsubmit="return confirm('⚠️ Bulk Action: Are you sure you want to APPROVE and SYNC ALL (<?php echo $pending_count; ?>) pending requests? This cannot be undone.');">
                        <input type="hidden" name="action" value="approve_all">
                        <button type="submit" class="btn btn-success fw-bold shadow-sm d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> Approve All
                        </button>
                    </form>

                    <!-- Batch Reject Button Form -->
                    <form method="POST" action="" onsubmit="return confirm('⚠️ Bulk Action: Are you sure you want to REJECT ALL (<?php echo $pending_count; ?>) pending correction requests?');">
                        <input type="hidden" name="action" value="reject_all">
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm d-flex align-items-center gap-2">
                            <i class="fa-solid fa-ban"></i> Reject All
                        </button>
                    </form>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-card card shadow-sm p-4">
                    <div class="table-responsive rounded border">
                        <table class="table table-hover align-middle mb-0 table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student Details</th>
                                    <th>Subject & Date</th>
                                    <th>Current Status</th>
                                    <th>Requested Change</th>
                                    <th>Reason / Remarks</th>
                                    <th class="text-center" style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($pending_count > 0) {
                                    while ($row = mysqli_fetch_assoc($pending_requests)) {
                                        // Formulate nice raw custom date format output (e.g., 140726 -> 14-07-26)
                                        $raw_date = strval($row['date_of_attendance']);
                                        $formatted_date = strlen($raw_date) == 6 ? substr($raw_date, 0, 2) . '-' . substr($raw_date, 2, 2) . '-' . substr($raw_date, 4, 2) : $raw_date;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                                <div class="text-muted small fw-semibold"><i class="fa-solid fa-id-card me-1"></i><?php echo htmlspecialchars($row['roll_number']); ?></div>
                                            </td>
                                            <td>
                                                <div class="text-wrap" style="max-width: 250px;"><i class="fa-solid fa-book text-secondary me-1"></i><?php echo htmlspecialchars(trim($row['subject_name'])); ?></div>
                                                <div class="text-primary small fw-bold mt-1"><i class="fa-solid fa-calendar-day me-1"></i><?php echo $formatted_date; ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5"><?php echo htmlspecialchars($row['current_status']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 fw-bold"><i class="fa-solid fa-arrow-right-long me-1"></i><?php echo htmlspecialchars($row['requested_status']); ?></span>
                                            </td>
                                            <td>
                                                <div class="text-muted small text-wrap" style="max-width: 220px; font-style: italic;">
                                                    "<?php echo htmlspecialchars($row['reason']); ?>"
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Approve Action Control -->
                                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to approve this correction and sync logs?');">
                                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>"/>
                                                        <input type="hidden" name="action" value="approve"/>
                                                        <button type="submit" class="btn btn-sm btn-success px-3 shadow-sm"><i class="fa-solid fa-check me-1"></i> Approve</button>
                                                    </form>
                                                    
                                                    <!-- Reject Action Control -->
                                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>"/>
                                                        <input type="hidden" name="action" value="reject"/>
                                                        <button type="submit" class="btn btn-sm btn-danger px-3 shadow-sm"><i class="fa-solid fa-xmark me-1"></i> Reject</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='fa-solid fa-circle-check text-success fs-3 mb-2 d-block'></i> Hurrah! No pending attendance correction requests found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
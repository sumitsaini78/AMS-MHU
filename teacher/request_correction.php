<?php
include "../db_connect.php";
session_start();

// Secure the page
if (!isset($_SESSION['teacher_id']) || !isset($_SESSION['teacher_name'])) {
    header("Location: ../index.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];
$teacher_name = $_SESSION['teacher_name'];

$attendance_record = null;
$search_error = "";

// Step 1: Search for the wrong attendance record using Roll Number & Date
if (isset($_POST['search_attendance'])) {
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $date_input = mysqli_real_escape_string($conn, $_POST['date_of_attendance']); // Expected format: DDMMYY matching your active DB format
    
    $search_query = "SELECT * FROM `attendance` WHERE roll_number = '$roll_number' AND date_of_attendence = '$date_input' LIMIT 1";
    $search_result = mysqli_query($conn, $search_query);
    
    if ($search_result && mysqli_num_rows($search_result) > 0) {
        $attendance_record = mysqli_fetch_assoc($search_result);
    } else {
        $search_error = "❌ No attendance record found for this Roll Number on the given date.";
    }
}

// Step 2: Process the Correction Request Insertion
if (isset($_POST['submit_correction'])) {
    $attendance_id = mysqli_real_escape_string($conn, $_POST['attendance_id']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $date_of_attendance = mysqli_real_escape_string($conn, $_POST['date_of_attendance']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current_status']);
    $requested_status = mysqli_real_escape_string($conn, $_POST['requested_status']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
    // Insert into correction log table
    $insert_query = "INSERT INTO `attendance_corrections` 
                    (attendance_id, teacher_id, student_name, roll_number, subject_name, date_of_attendance, current_status, requested_status, reason, status) 
                    VALUES 
                    ('$attendance_id', '$teacher_id', '$student_name', '$roll_number', '$subject_name', '$date_of_attendance', '$current_status', '$requested_status', '$reason', 'Pending')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('✅ Correction request submitted successfully to Admin!'); window.location.href='request_correction.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Query Error: " . addslashes(mysqli_error($conn)) . "');</script>";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Attendance Correction | MHU-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body { background-color: #f4f6f9; }
        .form-card { background: #ffffff; border: none; border-radius: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold fs-4" href="index.php"><i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY</a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white small"><i class="fa-solid fa-user me-1 text-warning"></i> <?php echo htmlspecialchars($teacher_name); ?></span>
                    <a href="index.php" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                
                <!-- STEP 1: Search Form -->
                <div class="form-card card p-4 mb-4">
                    <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Find Incorrect Attendance Row</h4>
                    <form method="POST" action="request_correction.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">STUDENT ROLL NUMBER</label>
                                <input type="text" name="roll_number" class="form-control" placeholder="e.g., 26CSE001" required value="<?php echo isset($_POST['roll_number']) ? htmlspecialchars($_POST['roll_number']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">DATE OF ATTENDANCE (DDMMYY format)</label>
                                <input type="text" name="date_of_attendance" class="form-control" placeholder="e.g., 130726" required value="<?php echo isset($_POST['date_of_attendance']) ? htmlspecialchars($_POST['date_of_attendance']) : ''; ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" name="search_attendance" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-search me-2"></i>Fetch Attendance Row</button>
                            </div>
                        </div>
                    </form>
                    <?php if(!empty($search_error)) { echo "<div class='alert alert-danger mt-3 small py-2'>$search_error</div>"; } ?>
                </div>

                <!-- STEP 2: Correction Submission Form (Appears only when record is found) -->
                <?php if ($attendance_record !== null) { ?>
                <div class="form-card card p-4 border border-warning-subtle animate__animated animate__fadeIn">
                    <h4 class="fw-bold text-warning mb-3"><i class="fa-solid fa-pen-to-square me-2"></i>Submit Correction Details</h4>
                    
                    <div class="alert alert-secondary bg-light text-dark small mb-4">
                        <strong>Found Row Details:</strong> <br>
                        Student: <?php echo htmlspecialchars($attendance_record['student_name']); ?> | 
                        Subject: <?php echo htmlspecialchars($attendance_record['subject_name']); ?> | 
                        Current Status: <span class="badge bg-secondary"><?php echo htmlspecialchars($attendance_record['attendance_status']); ?></span>
                    </div>

                    <form method="POST" action="request_correction.php">
                        <!-- Hidden tokens to retain identity specs -->
                        <input type="hidden" name="attendance_id" value="<?php echo $attendance_record['id']; ?>">
                        <input type="hidden" name="student_name" value="<?php echo $attendance_record['student_name']; ?>">
                        <input type="hidden" name="roll_number" value="<?php echo $attendance_record['roll_number']; ?>">
                        <input type="hidden" name="subject_name" value="<?php echo $attendance_record['subject_name']; ?>">
                        <input type="hidden" name="date_of_attendance" value="<?php echo $attendance_record['date_of_attendence']; ?>">
                        <input type="hidden" name="current_status" value="<?php echo $attendance_record['attendance_status']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">REQUESTED NEW ATTENDANCE STATUS</label>
                            <select name="requested_status" class="form-select" required>
                                <option value="" disabled selected>Select corrected status...</option>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">REASON FOR CORRECTION / RE-MARKING</label>
                            <textarea name="reason" rows="3" class="form-control" placeholder="Explain the error (e.g., Mistakenly marked absent during roll call, student was present late)..." required></textarea>
                        </div>

                        <button type="submit" name="submit_correction" class="btn btn-success w-100 fw-bold">
                            <i class="fa-solid fa-paper-plane me-2"></i>Send Request To Admin
                        </button>
                    </form>
                </div>
                <?php } ?>

            </div>
        </div>
    </main>
</body>
</html>
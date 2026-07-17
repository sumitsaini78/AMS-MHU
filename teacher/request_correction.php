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
$bulk_attendance_records = null;
$search_error = "";
$bulk_search_error = "";
$active_tab = "single"; // Controls which tab remains active on page load

// --- STEP 1: Single Search ---
if (isset($_POST['search_attendance'])) {
    $active_tab = "single";
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $date_input = mysqli_real_escape_string($conn, $_POST['date_of_attendance']); // Expected format: DDMMYY
    
    $search_query = "SELECT * FROM `attendance` WHERE roll_number = '$roll_number' AND date_of_attendence = '$date_input' LIMIT 1";
    $search_result = mysqli_query($conn, $search_query);
    
    if ($search_result && mysqli_num_rows($search_result) > 0) {
        $attendance_record = mysqli_fetch_assoc($search_result);
    } else {
        $search_error = "❌ No attendance record found for this Roll Number on the given date.";
    }
}

// --- STEP 2: Single Correction Submission ---
if (isset($_POST['submit_correction'])) {
    $active_tab = "single";
    $attendance_id = mysqli_real_escape_string($conn, $_POST['attendance_id']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $date_of_attendance = mysqli_real_escape_string($conn, $_POST['date_of_attendance']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current_status']);
    $requested_status = mysqli_real_escape_string($conn, $_POST['requested_status']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    
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

// --- STEP 3: Bulk Search ---
if (isset($_POST['search_bulk_attendance'])) {
    $active_tab = "bulk";
    $bulk_subject = mysqli_real_escape_string($conn, $_POST['bulk_subject_name']);
    $bulk_date = mysqli_real_escape_string($conn, $_POST['bulk_date_of_attendance']);
    
    $bulk_search_query = "SELECT * FROM `attendance` WHERE subject_name = '$bulk_subject' AND date_of_attendence = '$bulk_date' ORDER BY student_name ASC";
    $bulk_search_result = mysqli_query($conn, $bulk_search_query);
    
    if ($bulk_search_result && mysqli_num_rows($bulk_search_result) > 0) {
        $bulk_attendance_records = [];
        while ($row = mysqli_fetch_assoc($bulk_search_result)) {
            $bulk_attendance_records[] = $row;
        }
    } else {
        $bulk_search_error = "❌ No attendance records found for this Subject on the specified date.";
    }
}

// --- STEP 4: Bulk Correction Submission ---
if (isset($_POST['submit_bulk_correction'])) {
    $active_tab = "bulk";
    $attendance_ids = $_POST['attendance_ids'] ?? [];
    $requested_statuses = $_POST['requested_statuses'] ?? [];
    $reasons = $_POST['reasons'] ?? [];
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($attendance_ids as $att_id) {
        // Only process if a target state change is requested
        if (!empty($requested_statuses[$att_id])) {
            $req_status = mysqli_real_escape_string($conn, $requested_statuses[$att_id]);
            $reason = mysqli_real_escape_string($conn, $reasons[$att_id]);
            $att_id_escaped = mysqli_real_escape_string($conn, $att_id);
            
            // Get original record parameters for the logging table
            $orig_query = mysqli_query($conn, "SELECT * FROM `attendance` WHERE id = '$att_id_escaped' LIMIT 1");
            if ($orig_query && mysqli_num_rows($orig_query) > 0) {
                $orig = mysqli_fetch_assoc($orig_query);
                
                // Only submit correction if it actually changes the status
                if ($orig['attendance_status'] !== $req_status) {
                    $student_name = mysqli_real_escape_string($conn, $orig['student_name']);
                    $roll_number = mysqli_real_escape_string($conn, $orig['roll_number']);
                    $subject_name = mysqli_real_escape_string($conn, $orig['subject_name']);
                    $date_of_attendance = mysqli_real_escape_string($conn, $orig['date_of_attendence']);
                    $current_status = mysqli_real_escape_string($conn, $orig['attendance_status']);
                    
                    $insert_query = "INSERT INTO `attendance_corrections` 
                                    (attendance_id, teacher_id, student_name, roll_number, subject_name, date_of_attendance, current_status, requested_status, reason, status) 
                                    VALUES 
                                    ('$att_id_escaped', '$teacher_id', '$student_name', '$roll_number', '$subject_name', '$date_of_attendance', '$current_status', '$req_status', '$reason', 'Pending')";
                    
                    if (mysqli_query($conn, $insert_query)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
        }
    }
    
    if ($success_count > 0) {
        $failed_msg = ($error_count > 0) ? " ($error_count requests failed)" : "";
        echo "<script>alert('✅ Successfully submitted $success_count correction request(s) to Admin!$failed_msg'); window.location.href='request_correction.php';</script>";
        exit;
    } else {
        echo "<script>alert('⚠️ No changes were submitted. Please change the status of at least one student before sending.');</script>";
    }
}

// Fetch list of subjects taught for the dropdown picker
$subjects_list = [];
$sub_query = mysqli_query($conn, "SELECT DISTINCT subject_name FROM `attendance` ORDER BY subject_name ASC");
if ($sub_query) {
    while ($s_row = mysqli_fetch_assoc($sub_query)) {
        $subjects_list[] = trim($s_row['subject_name']);
    }
}
$subjects_list = array_unique($subjects_list);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Attendance Correction | MHU-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body { background-color: #f4f6f9; }
        .form-card { background: #ffffff; border: none; border-radius: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .nav-pills .nav-link.active { background-color: #0d6efd; }
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
            <div class="col-12 col-lg-10">

                <!-- Navigation Tabs -->
                <ul class="nav nav-pills mb-4 justify-content-center" id="correctionTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $active_tab === 'single' ? 'active' : ''; ?>" id="single-tab" data-bs-toggle="pill" data-bs-target="#singleCorrection" type="button" role="tab">
                            <i class="fa-solid fa-user me-2"></i>Single Record Correction
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $active_tab === 'bulk' ? 'active' : ''; ?>" id="bulk-tab" data-bs-toggle="pill" data-bs-target="#bulkCorrection" type="button" role="tab">
                            <i class="fa-solid fa-users me-2"></i>Bulk Class Correction
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="correctionTabsContent">
                    
                    <!-- TAB 1: SINGLE CORRECTION -->
                    <div class="tab-pane fade <?php echo $active_tab === 'single' ? 'show active' : ''; ?>" id="singleCorrection" role="tabpanel">
                        <!-- Single Search Card -->
                        <div class="form-card card p-4 mb-4">
                            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Find Single Attendance Row</h4>
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

                        <!-- Single Submission Card -->
                        <?php if ($attendance_record !== null) { ?>
                        <div class="form-card card p-4 border border-warning-subtle">
                            <h4 class="fw-bold text-warning mb-3"><i class="fa-solid fa-pen-to-square me-2"></i>Submit Correction Details</h4>
                            <div class="alert alert-secondary bg-light text-dark small mb-4">
                                <strong>Found Row Details:</strong> <br>
                                Student: <?php echo htmlspecialchars($attendance_record['student_name']); ?> | 
                                Subject: <?php echo htmlspecialchars($attendance_record['subject_name']); ?> | 
                                Current Status: <span class="badge bg-secondary"><?php echo htmlspecialchars($attendance_record['attendance_status']); ?></span>
                            </div>

                            <form method="POST" action="request_correction.php">
                                <input type="hidden" name="attendance_id" value="<?php echo $attendance_record['id']; ?>">
                                <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($attendance_record['student_name']); ?>">
                                <input type="hidden" name="roll_number" value="<?php echo htmlspecialchars($attendance_record['roll_number']); ?>">
                                <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($attendance_record['subject_name']); ?>">
                                <input type="hidden" name="date_of_attendance" value="<?php echo htmlspecialchars($attendance_record['date_of_attendence']); ?>">
                                <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($attendance_record['attendance_status']); ?>">

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
                                    <textarea name="reason" rows="3" class="form-control" placeholder="Explain the error..." required></textarea>
                                </div>

                                <button type="submit" name="submit_correction" class="btn btn-success w-100 fw-bold">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Send Request To Admin
                                </button>
                            </form>
                        </div>
                        <?php } ?>
                    </div>

                    <!-- TAB 2: BULK CORRECTION -->
                    <div class="tab-pane fade <?php echo $active_tab === 'bulk' ? 'show active' : ''; ?>" id="bulkCorrection" role="tabpanel">
                        <!-- Bulk Search Card -->
                        <div class="form-card card p-4 mb-4">
                            <h4 class="fw-bold text-dark mb-3"><i class="fa-solid fa-users-rectangle me-2 text-primary"></i>Find Entire Class Attendance</h4>
                            <form method="POST" action="request_correction.php">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary">SELECT SUBJECT</label>
                                        <select name="bulk_subject_name" class="form-select" required>
                                            <option value="" disabled selected>Choose a subject...</option>
                                            <?php foreach ($subjects_list as $subject) { ?>
                                                <option value="<?php echo htmlspecialchars($subject); ?>" <?php echo (isset($_POST['bulk_subject_name']) && $_POST['bulk_subject_name'] == $subject) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($subject); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-secondary">DATE OF ATTENDANCE (DDMMYY format)</label>
                                        <input type="text" name="bulk_date_of_attendance" class="form-control" placeholder="e.g., 140726" required value="<?php echo isset($_POST['bulk_date_of_attendance']) ? htmlspecialchars($_POST['bulk_date_of_attendance']) : ''; ?>">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="search_bulk_attendance" class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-users me-2"></i>Fetch Class Records</button>
                                    </div>
                                </div>
                            </form>
                            <?php if(!empty($bulk_search_error)) { echo "<div class='alert alert-danger mt-3 small py-2'>$bulk_search_error</div>"; } ?>
                        </div>

                        <!-- Bulk Processing Table -->
                        <?php if (!empty($bulk_attendance_records)) { ?>
                        <div class="form-card card p-4">
                            <form method="POST" action="request_correction.php">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                                    <div>
                                        <h4 class="fw-bold text-success mb-0"><i class="fa-solid fa-list-check me-2"></i>Class Attendance Grid</h4>
                                        <small class="text-secondary">Only changed rows will submit a request.</small>
                                    </div>
                                    <!-- Global reason helper tool -->
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="text" id="global_reason" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Fast fill: Reason for all changed...">
                                        <button type="button" class="btn btn-secondary btn-sm fw-bold" onclick="applyGlobalReason()"><i class="fa-solid fa-copy me-1"></i>Apply</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 30%;">Student Info</th>
                                                <th class="text-center" style="width: 20%;">Current Status</th>
                                                <th style="width: 25%;">Requested Status</th>
                                                <th style="width: 25%;">Reason for Correction</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($bulk_attendance_records as $record) { 
                                                $id = $record['id'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold d-block text-dark"><?php echo htmlspecialchars($record['student_name']); ?></span>
                                                    <span class="badge bg-light text-dark border small">Roll: <?php echo htmlspecialchars($record['roll_number']); ?></span>
                                                    <input type="hidden" name="attendance_ids[]" value="<?php echo $id; ?>">
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($record['attendance_status'] === 'Present') { ?>
                                                        <span class="badge bg-success-subtle text-success px-3 py-2 border border-success-subtle">Present</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 border border-danger-subtle">Absent</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <select name="requested_statuses[<?php echo $id; ?>]" class="form-select form-select-sm status-select" onchange="toggleReasonRequired(this, '<?php echo $id; ?>')">
                                                        <option value="">-- No Correction Needed --</option>
                                                        <option value="Present" <?php echo ($record['attendance_status'] === 'Present') ? 'disabled class="text-muted"' : ''; ?>>Change to Present</option>
                                                        <option value="Absent" <?php echo ($record['attendance_status'] === 'Absent') ? 'disabled class="text-muted"' : ''; ?>>Change to Absent</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="reason_<?php echo $id; ?>" name="reasons[<?php echo $id; ?>]" class="form-control form-control-sm bulk-reason-input" placeholder="Required if changed...">
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <button type="submit" name="submit_bulk_correction" class="btn btn-success w-100 fw-bold py-3 mt-3 shadow-sm">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Send All Correction Requests To Admin
                                </button>
                            </form>
                        </div>
                        <?php } ?>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- UI Interaction Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy the global reason value down to all row reason inputs
        function applyGlobalReason() {
            const globalText = document.getElementById('global_reason').value.trim();
            if(!globalText) {
                alert("Please type a reason in the global reason box first.");
                return;
            }
            
            // Apply only to rows where a correction has actually been picked
            const selectors = document.querySelectorAll('.status-select');
            selectors.forEach(select => {
                if(select.value !== "") {
                    const rowId = select.name.match(/\[(.*?)\]/)[1];
                    const inputField = document.getElementById('reason_' + rowId);
                    if(inputField) {
                        inputField.value = globalText;
                        inputField.required = true;
                    }
                }
            });
        }

        // Dynamically enforce "required" on individual reason inputs when status has changed
        function toggleReasonRequired(selectElement, id) {
            const reasonInput = document.getElementById('reason_' + id);
            if(selectElement.value !== "") {
                reasonInput.required = true;
                reasonInput.classList.add('border-warning');
            } else {
                reasonInput.required = false;
                reasonInput.value = "";
                reasonInput.classList.remove('border-warning');
            }
        }
    </script>
</body>
</html>
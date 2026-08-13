<?php
include "../db_connect.php";
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

$teacher_id = $_SESSION['teacher_id'];
$teacher_name = $_SESSION['teacher_name'] ?? ''; // Ensure this is set
$faculty_name = $_SESSION['faculty'] ?? '';

// If teacher name is somehow not in session, fetch it
if (empty($teacher_name) || empty($faculty_name)) {
    $stmt = $conn->prepare("SELECT name, faculty FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $teacher_name = $row['name'];
        $faculty_name = $row['faculty'];
        $_SESSION['teacher_name'] = $teacher_name;
        $_SESSION['faculty'] = $faculty_name;
    }
}

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_subject'])) {
    $course_name = trim($_POST['course_name']);
    $year = trim($_POST['year']);
    $semester = trim($_POST['semester']);
    $subject_id = intval($_POST['subject_name']); // We use subject_id as the value in dropdown

    if (empty($course_name) || empty($year) || empty($semester) || empty($subject_id)) {
        $error = "All fields are required.";
    } else {
        // Fetch subject details
        $sub_stmt = $conn->prepare("SELECT subject_name, subject_code FROM subjects WHERE course_id = ?");
        $sub_stmt->bind_param("i", $subject_id);
        $sub_stmt->execute();
        $sub_res = $sub_stmt->get_result();
        
        if ($sub_res && $sub_res->num_rows > 0) {
            $sub_data = $sub_res->fetch_assoc();
            $subject_name = $sub_data['subject_name'];
            $subject_code = $sub_data['subject_code'];

            // Check if subject is already assigned to ANY teacher
            $assign_check_stmt = $conn->prepare("SELECT teacher_name FROM subjected_teacher WHERE sub_id = ?");
            $assign_check_stmt->bind_param("i", $subject_id);
            $assign_check_stmt->execute();
            $assign_res = $assign_check_stmt->get_result();

            if ($assign_res->num_rows > 0) {
                $assign_data = $assign_res->fetch_assoc();
                $error = "This subject is already assigned to " . htmlspecialchars($assign_data['teacher_name']) . ".";
            } else {
                // Check if request already exists for this teacher (Pending or Approved)
                $check_stmt = $conn->prepare("SELECT id FROM subject_requests WHERE teacher_id = ? AND sub_id = ? AND status != 'Rejected'");
                $check_stmt->bind_param("ii", $teacher_id, $subject_id);
                $check_stmt->execute();
                if ($check_stmt->get_result()->num_rows > 0) {
                    $error = "You have already requested this subject, or it has been approved.";
                } else {
                    $ins_stmt = $conn->prepare("INSERT INTO subject_requests (teacher_id, teacher_name, sub_id, subject_name, course_name, year, semester, faculty_name, subject_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $ins_stmt->bind_param("isissssss", $teacher_id, $teacher_name, $subject_id, $subject_name, $course_name, $year, $semester, $faculty_name, $subject_code);
                
                if ($ins_stmt->execute()) {
                    $msg = "Subject request submitted successfully!";
                } else {
                    $error = "Failed to submit request: " . $conn->error;
                }
                } // End of nested else for request exists check
            } // End of nested else for assignment check
        } else {
            $error = "Invalid subject selected.";
        }
    }
}

// Fetch past requests for this teacher
$history_res = $conn->query("SELECT * FROM subject_requests WHERE teacher_id = '$teacher_id' ORDER BY request_date DESC");

?>
<!doctype html>
<html lang="en">
<head>
    <title>Request Subject | Teacher Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .form-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); background: #fff; }
        .table-card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); background: #fff; }
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-none d-lg-inline-flex">
                        <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome, <?php echo htmlspecialchars($teacher_name); ?>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-info px-3 shadow-sm"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                    <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center g-4">
            <!-- Request Form -->
            <div class="col-lg-5">
                <div class="form-card p-4 p-md-5 h-100">
                    <h3 class="fw-bold text-dark mb-4"><i class="fa-solid fa-code-pull-request text-primary me-2"></i>Request Subject</h3>
                    
                    <?php if (!empty($msg)): ?>
                        <div class="alert alert-success rounded-3 small fw-medium"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger rounded-3 small fw-medium"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" id="requestForm">
                        <input type="hidden" id="faculty_name" value="<?= htmlspecialchars($faculty_name) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Course Name *</label>
                            <select name="course_name" id="courseSelect" class="form-select" required>
                                <option value="" selected disabled>Loading courses...</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Year *</label>
                            <select name="year" id="yearSelect" class="form-select" required disabled>
                                <option value="" selected disabled>Select Course first</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Semester *</label>
                            <select name="semester" id="semesterSelect" class="form-select" required disabled>
                                <option value="" selected disabled>Select Year first</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Subject *</label>
                            <select name="subject_name" id="subjectSelect" class="form-select" required disabled>
                                <option value="" selected disabled>Select Semester first</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="request_subject" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow-sm">
                            <i class="fa-solid fa-paper-plane me-2"></i>Submit Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Request History -->
            <div class="col-lg-7">
                <div class="table-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i>My Requests</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject</th>
                                    <th>Course details</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($history_res && $history_res->num_rows > 0): ?>
                                    <?php while ($req = $history_res->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($req['subject_name']) ?></div>
                                                <div class="small text-muted font-monospace"><?= htmlspecialchars($req['subject_code']) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-medium"><?= htmlspecialchars($req['course_name']) ?></div>
                                                <div class="small text-muted">Yr: <?= htmlspecialchars($req['year']) ?> | Sem: <?= htmlspecialchars($req['semester']) ?></div>
                                            </td>
                                            <td>
                                                <?php 
                                                    $bg = 'bg-secondary';
                                                    if ($req['status'] === 'Approved') $bg = 'bg-success';
                                                    if ($req['status'] === 'Rejected') $bg = 'bg-danger';
                                                    if ($req['status'] === 'Pending') $bg = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge <?= $bg ?> rounded-pill px-3"><?= htmlspecialchars($req['status']) ?></span>
                                            </td>
                                            <td class="small text-muted">
                                                <?= date('M d, Y', strtotime($req['request_date'])) ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">You have not requested any subjects yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faculty = document.getElementById('faculty_name').value;
            const courseSel = document.getElementById('courseSelect');
            const yearSel = document.getElementById('yearSelect');
            const semSel = document.getElementById('semesterSelect');
            const subSel = document.getElementById('subjectSelect');

            // 1. Load Courses
            fetch(`ajax_request.php?action=get_courses&faculty_name=${encodeURIComponent(faculty)}`)
                .then(r => r.json())
                .then(data => {
                    courseSel.innerHTML = '<option value="" selected disabled>Select a course</option>';
                    if (data.status === 'success') {
                        data.data.forEach(c => {
                            courseSel.innerHTML += `<option value="${c.course_name}">${c.course_name}</option>`;
                        });
                        courseSel.disabled = false;
                    }
                });

            // 2. Load Years on Course Change
            courseSel.addEventListener('change', () => {
                yearSel.innerHTML = '<option value="" selected disabled>Loading years...</option>';
                yearSel.disabled = true;
                semSel.innerHTML = '<option value="" selected disabled>Select Year first</option>';
                semSel.disabled = true;
                subSel.innerHTML = '<option value="" selected disabled>Select Semester first</option>';
                subSel.disabled = true;

                fetch(`ajax_request.php?action=get_years&faculty_name=${encodeURIComponent(faculty)}&course_name=${encodeURIComponent(courseSel.value)}`)
                    .then(r => r.json())
                    .then(data => {
                        yearSel.innerHTML = '<option value="" selected disabled>Select a year</option>';
                        if (data.status === 'success') {
                            data.data.forEach(y => {
                                yearSel.innerHTML += `<option value="${y.Year}">${y.Year}</option>`;
                            });
                            yearSel.disabled = false;
                        }
                    });
            });

            // 3. Load Semesters on Year Change
            yearSel.addEventListener('change', () => {
                semSel.innerHTML = '<option value="" selected disabled>Loading semesters...</option>';
                semSel.disabled = true;
                subSel.innerHTML = '<option value="" selected disabled>Select Semester first</option>';
                subSel.disabled = true;

                fetch(`ajax_request.php?action=get_semesters&faculty_name=${encodeURIComponent(faculty)}&course_name=${encodeURIComponent(courseSel.value)}&year=${encodeURIComponent(yearSel.value)}`)
                    .then(r => r.json())
                    .then(data => {
                        semSel.innerHTML = '<option value="" selected disabled>Select a semester</option>';
                        if (data.status === 'success') {
                            data.data.forEach(s => {
                                semSel.innerHTML += `<option value="${s.semester}">${s.semester}</option>`;
                            });
                            semSel.disabled = false;
                        }
                    });
            });

            // 4. Load Subjects on Semester Change
            semSel.addEventListener('change', () => {
                subSel.innerHTML = '<option value="" selected disabled>Loading subjects...</option>';
                subSel.disabled = true;

                fetch(`ajax_request.php?action=get_subjects&faculty_name=${encodeURIComponent(faculty)}&course_name=${encodeURIComponent(courseSel.value)}&year=${encodeURIComponent(yearSel.value)}&semester=${encodeURIComponent(semSel.value)}`)
                    .then(r => r.json())
                    .then(data => {
                        subSel.innerHTML = '<option value="" selected disabled>Select a subject</option>';
                        if (data.status === 'success') {
                            data.data.forEach(sub => {
                                if (sub.assigned_to) {
                                    subSel.innerHTML += `<option value="${sub.course_id}" disabled>${sub.subject_name} (${sub.subject_code}) - Already assigned to ${sub.assigned_to}</option>`;
                                } else {
                                    subSel.innerHTML += `<option value="${sub.course_id}">${sub.subject_name} (${sub.subject_code})</option>`;
                                }
                            });
                            subSel.disabled = false;
                        }
                    });
            });
        });
    </script>
</body>
</html>

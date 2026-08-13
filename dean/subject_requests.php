<?php
include "../db_connect.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['dean_id'])) {
    header("Location: ../index.php");
    exit;
}

$faculty_name = $_SESSION['faculty_name'] ?? '';
$msg = "";
$error = "";

// Handle Approve
if (isset($_POST['approve_request'])) {
    $req_id = intval($_POST['request_id']);
    
    // Fetch request details
    $req_stmt = $conn->prepare("SELECT * FROM subject_requests WHERE id = ? AND faculty_name = ? AND status = 'Pending'");
    $req_stmt->bind_param("is", $req_id, $faculty_name);
    $req_stmt->execute();
    $req_res = $req_stmt->get_result();
    
    if ($req_res && $req_res->num_rows > 0) {
        $req_data = $req_res->fetch_assoc();
        
        $teacher_id = $req_data['teacher_id'];
        $teacher_name = $req_data['teacher_name'];
        $sub_id = $req_data['sub_id'];
        $subject_name = $req_data['subject_name'];
        $course_name = $req_data['course_name'];
        $year = $req_data['year'];
        $semester = $req_data['semester'];
        $subject_code = $req_data['subject_code'];

        // Assign teacher in subjected_teacher
        $assign_stmt = $conn->prepare("INSERT INTO subjected_teacher (teacher_id, teacher_name, sub_id, subject_name, course_name, year, semester, subject_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $assign_stmt->bind_param("isisssss", $teacher_id, $teacher_name, $sub_id, $subject_name, $course_name, $year, $semester, $subject_code);
        
        if ($assign_stmt->execute()) {
            // Mark request as Approved
            $upd_stmt = $conn->prepare("UPDATE subject_requests SET status = 'Approved' WHERE id = ?");
            $upd_stmt->bind_param("i", $req_id);
            $upd_stmt->execute();
            $msg = "Request approved and subject assigned successfully.";
        } else {
            $error = "Failed to assign subject: " . $conn->error;
        }
    } else {
        $error = "Request not found or already processed.";
    }
}

// Handle Reject
if (isset($_POST['reject_request'])) {
    $req_id = intval($_POST['request_id']);
    
    $upd_stmt = $conn->prepare("UPDATE subject_requests SET status = 'Rejected' WHERE id = ? AND faculty_name = ? AND status = 'Pending'");
    $upd_stmt->bind_param("is", $req_id, $faculty_name);
    
    if ($upd_stmt->execute() && $upd_stmt->affected_rows > 0) {
        $msg = "Request rejected successfully.";
    } else {
        $error = "Failed to reject request or already processed.";
    }
}

// Fetch all requests for this faculty
$requests_query = "SELECT * FROM subject_requests WHERE faculty_name = '$faculty_name' ORDER BY CASE WHEN status = 'Pending' THEN 1 ELSE 2 END, request_date DESC";
$requests_res = $conn->query($requests_query);

?>
<!doctype html>
<html lang="en">
<head>
    <title>Subject Requests | Dean Panel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .table-card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); background: #fff; }
    </style>
</head>
<body>
    <?php include 'dean_navbar.php'; ?>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-code-pull-request text-primary me-2"></i>Subject Requests</h2>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-success rounded-3 small fw-medium"><i class="fa-solid fa-circle-check me-2"></i><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-3 small fw-medium"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="table-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Teacher</th>
                            <th>Requested Subject</th>
                            <th>Course Details</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($requests_res && $requests_res->num_rows > 0): ?>
                            <?php while ($req = $requests_res->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="fa-solid fa-user text-muted me-1"></i><?= htmlspecialchars($req['teacher_name']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($req['subject_name']) ?></div>
                                        <div class="small text-muted font-monospace"><?= htmlspecialchars($req['subject_code']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($req['course_name']) ?></div>
                                        <div class="small text-muted">Yr: <?= htmlspecialchars($req['year']) ?> | Sem: <?= htmlspecialchars($req['semester']) ?></div>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('M d, Y h:i A', strtotime($req['request_date'])) ?>
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
                                    <td class="text-end">
                                        <?php if ($req['status'] === 'Pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                                <button type="submit" name="approve_request" class="btn btn-sm btn-success fw-medium" onclick="return confirm('Approve and assign this subject?');">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                                <button type="submit" name="reject_request" class="btn btn-sm btn-outline-danger fw-medium" onclick="return confirm('Reject this request?');">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small italic">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No subject requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

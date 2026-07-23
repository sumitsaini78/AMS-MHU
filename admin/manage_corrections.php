<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
$msg = "";
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    $conn->query("UPDATE attendance_corrections SET status = '$status' WHERE id = $id");
    $msg = "Correction request $status successfully!";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Attendance Corrections | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }</style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-danger me-2"></i> MHU-AMS ADMIN</a>
            <a href="index.php" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
        </div>
    </nav>
    <main class="container py-5">
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-bell text-danger me-2"></i>Attendance Correction Requests</h2>
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>#</th><th>Student / Enrollment</th><th>Reason</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        <?php 
                        $res = $conn->query("SELECT * FROM attendance_corrections ORDER BY id DESC"); 
                        $i = 1;
                        if ($res && $res->num_rows > 0):
                            while($row = $res->fetch_assoc()): 
                                $badge_bg = ($row['status'] == 'Approved') ? 'bg-success' : (($row['status'] == 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong class="text-dark"><?= htmlspecialchars($row['enrollment'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($row['reason'] ?? 'N/A') ?></td>
                            <td><span class="badge <?= $badge_bg ?>"><?= htmlspecialchars($row['status'] ?? 'Pending') ?></span></td>
                            <td class="text-end">
                                <a href="manage_corrections.php?action=approve&id=<?= $row['id'] ?>" class="btn btn-sm btn-success"><i class="fa-solid fa-check me-1"></i> Approve</a>
                                <a href="manage_corrections.php?action=reject&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"><i class="fa-solid fa-xmark me-1"></i> Reject</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No correction requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
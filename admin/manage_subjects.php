<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
$msg = ""; $error = "";
if (isset($_POST['add_subject'])) {
    $subject_name = trim($_POST['subject_name']);
    $course_name = trim($_POST['course_name']);
    $semester = trim($_POST['semester']);
    if (!empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, course_name, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject_name, $course_name, $semester);
        if ($stmt->execute()) { $msg = "Subject added successfully!"; } else { $error = "Error: " . $conn->error; }
        $stmt->close();
    } else { $error = "Subject name is required."; }
}
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $conn->query("DELETE FROM subjects WHERE id = $did");
    $msg = "Subject deleted successfully!";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Subjects | Admin</title>
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
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-book-bookmark text-secondary me-2"></i>Manage Subjects</h2>
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Add Subject</h5>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label small fw-semibold">Subject Name *</label><input type="text" name="subject_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Course Name</label><input type="text" name="course_name" class="form-control"></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Semester</label><input type="text" name="semester" class="form-control"></div>
                        <button type="submit" name="add_subject" class="btn btn-secondary w-100 rounded-pill fw-semibold">Save Subject</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Subjects List</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>#</th><th>Subject Name</th><th>Course</th><th>Semester</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php $res = $conn->query("SELECT * FROM subjects ORDER BY id DESC"); $i=1;
                                while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['semester'] ?? 'N/A') ?></td>
                                    <td class="text-end"><a href="manage_subjects.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete subject?');"><i class="fa-solid fa-trash"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
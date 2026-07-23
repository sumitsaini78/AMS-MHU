<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
$msg = ""; $error = "";
if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    $faculty_name = trim($_POST['faculty_name']);
    if (!empty($course_name) && !empty($faculty_name)) {
        $stmt = $conn->prepare("INSERT INTO courses_list (course_name, faculty_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $course_name, $faculty_name);
        if ($stmt->execute()) { $msg = "Course added successfully!"; } else { $error = "Error: " . $conn->error; }
        $stmt->close();
    } else { $error = "Fill all required fields."; }
}
if (isset($_GET['delete_id'])) {
    $did = intval($_GET['delete_id']);
    $conn->query("DELETE FROM courses_list WHERE id = $did");
    $msg = "Course deleted successfully!";
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Manage Courses | Admin</title>
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
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-graduation-cap text-danger me-2"></i>Manage Courses</h2>
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Add Course</h5>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label small fw-semibold">Course Name *</label><input type="text" name="course_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-semibold">Faculty *</label>
                            <select name="faculty_name" class="form-select" required>
                                <option value="" selected disabled>Select Faculty</option>
                                <?php $fr = $conn->query("SELECT DISTINCT faculty_name FROM faculty"); while($f = $fr->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($f['faculty_name']) ?>"><?= htmlspecialchars($f['faculty_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" name="add_course" class="btn btn-danger w-100 rounded-pill fw-semibold">Save Course</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Courses List</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>#</th><th>Course Name</th><th>Faculty</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php $res = $conn->query("SELECT * FROM courses_list ORDER BY id DESC"); $i=1;
                                while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['course_name']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row['faculty_name']) ?></span></td>
                                    <td class="text-end"><a href="manage_courses.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete course?');"><i class="fa-solid fa-trash"></i></a></td>
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
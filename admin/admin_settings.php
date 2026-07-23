<?php
include "../db_connect.php";
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../index.php"); exit; }
$admin_id = $_SESSION['admin_id'];
$msg = ""; $error = "";

if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $stmt = $conn->prepare("UPDATE admin SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $admin_id);
    if ($stmt->execute()) { $msg = "Profile updated successfully!"; } else { $error = "Error updating profile."; }
    $stmt->close();
}

$res = $conn->query("SELECT * FROM admin WHERE id = $admin_id");
$admin = $res->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
    <title>Admin Settings | Admin</title>
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
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-gears text-dark me-2"></i>Admin Security & Settings</h2>
        <?php if (!empty($msg)): ?><div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <h5 class="fw-bold mb-3">Update Profile</h5>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label small fw-semibold">Admin Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['name'] ?? '') ?>" required></div>
                        <button type="submit" name="update_profile" class="btn btn-dark w-100 rounded-pill fw-semibold">Update Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
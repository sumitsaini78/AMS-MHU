<?php
$nav_dean_name = $_SESSION['dean_name'] ?? 'Dean';
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2 mb-4">
        <div class="container-fluid">
            <a class="navbar-brand text-warning fw-bold fs-4 d-flex align-items-center" href="index.php">
                <i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-none d-lg-inline-flex">
                    <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome, <?php echo htmlspecialchars($nav_dean_name); ?>
                </span>
                <a href="index.php" class="btn btn-sm btn-outline-info px-3 shadow-sm"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
            </div>
        </div>
    </nav>
</header>

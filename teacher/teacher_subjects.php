<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Enforce teacher authentication state
if (!isset($_SESSION['teacher_id']) || !isset($_SESSION['teacher_name'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['teacher_id'];
$teacher_name = $_SESSION['teacher_name'];

// 2. Fetch all unique courses/subjects mapped to this authenticated teacher
$query = "SELECT id, subject_name FROM `subjected_teacher` WHERE teacher_id = '$id' ORDER BY subject_name ASC";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Assigned Subjects | Record Sessions</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    
    <!-- FontAwesome for Premium UI Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body {
            background-color: #f4f6f9;
        }
        #mhu-text {
            color: #a2c250;
            text-shadow: 1px 2px 14px rgb(46 195 41);
        }
        .subject-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .subject-card:hover {
            transform: translateY(-5px);
            border-color: #0d6efd;
            box-shadow: 0 12px 20px rgba(13, 110, 253, 0.08) !important;
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e7f1ff;
            color: #0d6efd;
            font-size: 1.25rem;
            transition: all 0.25s ease;
        }
        .subject-card:hover .icon-box {
            background-color: #0d6efd;
            color: #ffffff;
        }
        .card-submit-btn {
            background: none;
            border: none;
            color: inherit;
            padding: 0;
            font: inherit;
            cursor: pointer;
            outline: inherit;
            text-align: left;
            width: 100%;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS <sub>Sessions</sub></span>
                <div class="d-flex align-items-center">
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small me-3">
                        <i class="fa-solid fa-user-tie me-1"></i> Professor: <?php echo htmlspecialchars($teacher_name); ?>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-info me-2">
                        <i class="fa-solid fa-house me-1"></i> Dashboard
                    </a>
                    <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <!-- Structural Section Header Block -->
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill uppercase fw-bold tracking-wider mb-2">Attendance Logging Hub</span>
                <h2 class="fw-bold text-dark">Select a Subject to Record Session</h2>
                <p class="text-muted">Click on any course track card below to generate and configure today's student active presence roster ledger sheet.</p>
            </div>
        </div>

        <!-- Dynamic Subjects Grid Layout -->
        <div class="row g-4 justify-content-center">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $subject = $row['subject_name'];
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <!-- Wraps the entire card interface element into a clean operational submission module form -->
                        <form action="select_attendence_details.php" method="POST" class="h-100 m-0">
                            <input type="hidden" name="subject_name" value="<?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>">
                            
                            <button type="submit" class="card-submit-btn h-100">
                                <div class="subject-card card p-4 shadow-sm h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box me-3">
                                            <i class="fa-solid fa-book-open-reader"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-uppercase text-muted font-monospace small-xs tracking-wide">Course Module</span>
                                            <h5 class="fw-bold text-dark text-truncate mb-0"><?php echo htmlspecialchars($subject); ?></h5>
                                        </div>
                                    </div>
                                    
                                    <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center text-primary fw-semibold small">
                                        <span>Initialize Roll Call</span>
                                        <i class="fa-solid fa-circle-arrow-right transition-transform"></i>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                // Empty State Feedback Box Display
                ?>
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="bg-white border rounded-3 p-5 text-center shadow-sm">
                        <span style="font-size: 3.5rem;" class="text-warning mb-3 d-inline-block"><i class="fa-solid fa-folder-open"></i></span>
                        <h4 class="fw-bold text-dark">No Subjects Assigned</h4>
                        <p class="text-muted small mb-4">It looks like your profile system record hasn't been mapped to any courses yet. Please coordinate with the university admin workspace console to attach your class subjects.</p>
                        <a href="index.php" class="btn btn-sm btn-primary px-4"><i class="fa-solid fa-arrow-left me-2"></i>Return Home</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
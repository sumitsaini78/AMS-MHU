<?php
include "../db_connect.php";
session_start();

// Security Check
if (!isset($_SESSION['dean_id'])) {
    header("Location: ./index.php");
    exit;
}

// Logic: Search functionality
// Logic: Search functionality filtered by FOCBS
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Added 'AND faculty = "FOCBS"' to restrict results
$sql = "SELECT * FROM students WHERE faculty = 'FOCBS'";

if ($search != '') {
    $sql .= " AND (name LIKE '%$search%' OR enrollment_number LIKE '%$search%' OR roll_number LIKE '%$search%')";
}
$sql .= " ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
?>

<!doctype html>
<html lang="en">
<head>
    <title>Student Directory | MHU-AMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .table-card { border-radius: 12px; overflow: hidden; }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fa-solid fa-users text-primary me-2"></i> Student Directory</h3>
            <a href="index.php" class="btn btn-outline-dark"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
        </div>

        <!-- Search Bar -->
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-3">
            <form method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, enrollment, or roll number..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search me-1"></i> Search</button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="card table-card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Enrollment No</th>
                            <th>Roll No</th>
                            <th>Course</th>
                            <th>Year/Sem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td class='fw-semibold'>{$row['name']}</td>
                                        <td>{$row['enrollment_number']}</td>
                                        <td>{$row['roll_number']}</td>
                                        <td><span class='badge bg-info-subtle text-info fw-bold'>{$row['course']}</span></td>
                                        <td>{$row['year']} /  {$row['sem']}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No students found matching your search.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
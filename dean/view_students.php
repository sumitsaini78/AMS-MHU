<?php
include "../db_connect.php";
session_start();

// Security Check
if (!isset($_SESSION['dean_id'])) {
    header("Location: ./index.php");
    exit;
}

// Logic: Filters handling
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_course = isset($_GET['course']) ? mysqli_real_escape_string($conn, $_GET['course']) : '';
$filter_year = isset($_GET['year']) ? mysqli_real_escape_string($conn, $_GET['year']) : '';

// Base Query restricted to FOCBS
$sql = "SELECT * FROM students WHERE faculty = 'FOCBS'";

// Apply text search filter
if ($search != '') {
    $sql .= " AND (name LIKE '%$search%' OR enrollment_number LIKE '%$search%' OR roll_number LIKE '%$search%')";
}

// Apply course filter
if ($filter_course != '') {
    $sql .= " AND course = '$filter_course'";
}

// Apply year filter
if ($filter_year != '') {
    $sql .= " AND year = '$filter_year'";
}

$sql .= " ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

// Fetch available courses for the dropdown filter options
$courses_result = mysqli_query($conn, "SELECT DISTINCT course_name FROM courses_list ORDER BY course_name ASC");

// Fetch available years for the dropdown filter options
$years_result = mysqli_query($conn, "SELECT DISTINCT year FROM students WHERE faculty = 'FOCBS' ORDER BY year DESC");
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

        <!-- Search and Filter Bar -->
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-3">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small text-muted fw-bold">Search Keywords</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, enrollment, or roll number..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold">Filter by Course</label>
                    <select name="course" class="form-select">
                        <option value="">All Courses</option>
                        <?php 
                        while($c = mysqli_fetch_assoc($courses_result)) {
                            $selected = ($filter_course == $c['course_name']) ? 'selected' : '';
                            echo "<option value='{$c['course_name']}' $selected>{$c['course_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Filter by Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        <?php 
                        while($y = mysqli_fetch_assoc($years_result)) {
                            $selected = ($filter_year == $y['year']) ? 'selected' : '';
                            echo "<option value='{$y['year']}' $selected>{$y['year']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
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
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>No students found matching your filters.</td></tr>";
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
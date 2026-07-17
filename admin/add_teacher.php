<?php 
include "../db_connect.php";
$message = "";

if(isset($_POST['add_teacher'])){
    // Validating based on the correct 'faculty_name' name attribute
    if (!empty($_POST['teacher_name']) && !empty($_POST['faculty_name'])) {
        $teacher_name = mysqli_real_escape_string($conn, $_POST['teacher_name']);
        $faculty_name = mysqli_real_escape_string($conn, $_POST['faculty_name']);
        
        $query = "INSERT INTO `teachers`(name, faculty) VALUES('$teacher_name', '$faculty_name')";
        
        if(mysqli_query($conn, $query)){
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>Teacher added successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Error: ' . mysqli_error($conn) . '</div>';
        }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Add Teacher | Mhu-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .card { transition: transform 0.2s; }
        .btn-primary { transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(13,110,253,0.3); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Mhu-AMS</span>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-house me-1"></i>Home</a>
    </div>
</nav>
     
<main class="container my-5">
   <div class="row justify-content-center">
       <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 p-4">
                <h4 class="mb-4 text-center fw-bold text-primary"><i class="fa-solid fa-user-plus me-2"></i>Register New Teacher</h4>
                
                <?php echo $message; ?>

                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teacher Name</label>
                        <input type="text" class="form-control" name="teacher_name" placeholder="Full Name" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Faculty / Department</label>
                        <select class="form-select" name="faculty_name" required>
                            <option value="" selected disabled>Select a Faculty</option>
                            <?php
                            // Fetching faculty names correctly based on your database column
                            $result = mysqli_query($conn, "SELECT faculty_name FROM `faculty`");
                            if($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $val = htmlspecialchars($row['faculty_name']);
                                    echo "<option value='{$val}'>{$val}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" name="add_teacher">
                        <i class="fa-solid fa-user-check me-2"></i>Confirm Registration
                    </button>
                </form>
            </div>
            <p class="text-center text-muted mt-4 small"><i class="fa-solid fa-lock me-1"></i>Secure Administrative Panel</p>
       </div>
   </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
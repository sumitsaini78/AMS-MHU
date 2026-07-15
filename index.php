<?php include "db_connect.php";
// Dean login
if (isset($_POST['dean-login'])) {
   
    $dean_id = $_POST['dean_id'];
    $number = $_POST['number'];
    $query = "SELECT * FROM deans WHERE id = '$dean_id' AND number = '$number'";


    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {  
        // Successful login
        session_start(); 
        $_SESSION['dean_id'] = $dean_id;
        header("Location: dean/index.php" );
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid Dean ID or Password');</script>";
    }
}
// admin login
if (isset($_POST['admin-login'])) {
   
    $admin_id = $_POST['admin_id'];
    $number = $_POST['number'];
    $query = "SELECT * FROM admin WHERE id = '$admin_id' AND number = '$number'";


    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Successful login
        session_start();
        $_SESSION['admin_id'] = $admin_id;
        header("Location: admin/index.php");
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid Admin ID or Password');</script>";
    }
}
// teacher login
if (isset($_POST['teacher-login'])) {

    $teacher_id = $_POST['teacher_id'];
    $number = $_POST['number'];

    $query = "SELECT * FROM teachers WHERE id = '$teacher_id' AND number = '$number'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Successful login
        session_start();
        $_SESSION['teacher_id'] = $teacher_id;
        header("Location: teacher/index.php");
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid Teacher ID or Password');</script>";
    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mhu-AMS | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- new commit -->
    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fs-3 fw-bold">MHU-AMS</span>
            <a href="admin/index.php" class="--bs-body-bg">Admin</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h3>Welcome to MHU-AMS</h3>
            <p class="text-muted">Please Click on Your Respective Role Below.</p>
        </div>

        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">

            <button class="btn btn-primary px-4 py-2" onclick="showForm('adminForm')">Admin</button>


            <button class="btn btn-success px-4 py-2" onclick="showForm('deanForm')">Dean</button>


            <button class="btn btn-warning px-4 py-2 text-dark" onclick="showForm('teacherForm')">Teachers</button>


            <button class="btn btn-info px-4 py-2 text-dark" onclick="showForm('studentForm')">Students</button>

        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- Admin Form (Visible by default) -->
                <div id="adminForm" class="card shadow role-form">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Admin ID  </label>
                                <input type="text" name="admin_id" class="form-control" placeholder="Admin ID" required>
                            </div>
                            <div class="mb-3">
                                <label>Admin Number</label>
                                <input type="text" name="number" class="form-control" placeholder="Enter number"
                                    required>
                            </div>
                            <input type="submit" name="admin-login" class="btn btn-success w-100">
                        </form>
                    </div>
                </div>

                <!-- Dean Form -->
                <div id="deanForm" class="card shadow role-form d-none">
                    <div class="card-header bg-success text-white text-center">
                        <h4>Dean Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Dean ID</label>
                                <input type="text" name="dean_id" class="form-control" placeholder="Enter Dean ID"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="text" name="number" class="form-control" placeholder="Enter number"
                                    required>
                            </div>
                            <input type="submit" name="dean-login" class="btn btn-success w-100">
                        </form>
                    </div>
                </div>

                <!-- Teacher Form -->
                <div id="teacherForm" class="card shadow role-form d-none">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4>Teacher Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Teacher ID</label>
                                <input type="text" name="teacher_id" class="form-control" placeholder="Enter Teacher ID"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="number" class="form-control" placeholder="Enter Password"
                                    required>
                            </div>
                            <input type="submit" class="btn btn-warning w-100" name="teacher-login" value="Login">
                        </form>
                    </div>
                </div>

                <!-- Student Form -->
                <div id="studentForm" class="card shadow role-form d-none">
                    <div class="card-header bg-info text-dark text-center">
                        <h4>Student Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Roll Number / Student ID</label>
                                <input type="text" name="student_id" class="form-control"
                                    placeholder="Enter Roll Number" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password"
                                    required>
                            </div>
                            <button type="submit" name="stu_submit" class="btn btn-info w-100">Login</button>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(formId) {
            const forms = document.querySelectorAll('.role-form');
            forms.forEach(form => {
                form.classList.add('d-none');
            });
            const selectedForm = document.getElementById(formId);
            if (selectedForm) {
                selectedForm.classList.remove('d-none');
            }
        } 
    </script>
</body>

</html>
<?php include "db_connect.php";


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

    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
            <a href="admin/index.php" class="--bs-body-bg">Admin</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h3>Select Your Role to Login</h3>
            <p class="text-muted">Please click on your respective role below.</p>
        </div>

        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
            <a href="./admin/index.php">
                <button class="btn btn-primary px-4 py-2" onclick="showForm('adminForm')">Admin</button>
            </a>
            <a href="./dean/index.php">
                <button class="btn btn-success px-4 py-2" onclick="showForm('deanForm')">Dean</button>
            </a>
            <a href="./teacher/index.php">
                <button class="btn btn-warning px-4 py-2 text-dark" onclick="showForm('teacherForm')">Teachers</button>
            </a>
            <a href="student/index.php">
                <button class="btn btn-info px-4 py-2 text-dark" onclick="showForm('studentForm')">Students</button>
            </a>
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
                                <label>Admin Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Admin name" required>
                            </div>
                            <div class="mb-3">
                                <label>Admin Number</label>
                                <input type="password" name="number" class="form-control" placeholder="Enter number"
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
                        <form action="dean_login.php" method="POST">
                            <div class="mb-3">
                                <label>Dean ID</label>
                                <input type="text" name="dean_id" class="form-control" placeholder="Enter Dean ID"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Login</button>
                        </form>
                    </div>
                </div>

                <!-- Teacher Form -->
                <div id="teacherForm" class="card shadow role-form d-none">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4>Teacher Login</h4>
                    </div>
                    <div class="card-body">
                        <form action="teacher_login.php" method="POST">
                            <div class="mb-3">
                                <label>Teacher ID</label>
                                <input type="text" name="teacher_id" class="form-control" placeholder="Enter Teacher ID"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Login</button>
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
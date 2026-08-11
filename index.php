<?php include "db_connect.php";

// Dean login
if (isset($_POST['dean-login'])) {
    $dean_id = $_POST['dean_id'];
    $number = $_POST['number'];
    $query = "SELECT * FROM deans WHERE id = '$dean_id' AND number = '$number'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {  
        // Fetch the row to get the Dean's name
        $row = mysqli_fetch_assoc($result);

        // Successful login
        session_start(); 
        $_SESSION['dean_id'] = $dean_id;
        $_SESSION['dean_name'] = $row['Dean_name']; // Fetched from the database
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
        // Fetch the row to get the admin's details
        $row = mysqli_fetch_assoc($result);

        // Successful login
        session_start();
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_name'] = $row['name']; // Fetched from the 'name' column in the admin table
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
        // If you need the teacher's name in the session, fetch it here just like the admin/dean blocks!
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .navbar-brand {
            color: #333 !important;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .login-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            padding: 2.5rem;
        }

        .role-selectors {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-role {
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            opacity: 0.7;
        }

        .btn-role:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .btn-role.active {
            opacity: 1;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transform: scale(1.05);
        }

        /* Color Themes */
        .btn-admin { background: linear-gradient(45deg, #007bff, #00d2ff); } /* Blue */
        .btn-dean { background: linear-gradient(45deg, #28a745, #54d069); } /* Green */
        .btn-teacher { background: linear-gradient(45deg, #fd7e14, #ffb347); } /* Orange */
        .btn-student { background: linear-gradient(45deg, #dc3545, #ff6a88); } /* Red */

        .role-form {
            animation: fadeIn 0.5s ease;
            max-width: 450px;
            margin: 0 auto;
            width: 100%;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            border: none;
            border-radius: 15px;
            background: transparent;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 1px solid #e0e0e0;
            background: #f9f9f9;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: none;
            background: #fff;
        }

        .admin-focus:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
        .dean-focus:focus { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40,167,69,.25); }
        .teacher-focus:focus { border-color: #fd7e14; box-shadow: 0 0 0 0.2rem rgba(253,126,20,.25); }
        .student-focus:focus { border-color: #dc3545; box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25); }

        .submit-btn {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            color: white;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }

        .icon-header {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .text-admin { color: #007bff; }
        .text-dean { color: #28a745; }
        .text-teacher { color: #fd7e14; }
        .text-student { color: #dc3545; }

    </style>
</head>

<body>

    <nav class="navbar shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand"><i class="fa-solid fa-graduation-cap me-2"></i>MHU-AMS</span>
            <a href="admin/index.php" class="text-decoration-none text-muted"><i class="fa-solid fa-shield-halved me-1"></i> Admin Portal</a>
        </div>
    </nav>

    <div class="main-container">
        <div class="login-wrapper">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Welcome to MHU-AMS</h2>
                <p class="text-muted">Select your portal to continue</p>
            </div>

            <div class="role-selectors">
                <button class="btn-role btn-admin active" onclick="showForm('adminForm', this)">Admin</button>
                <button class="btn-role btn-dean" onclick="showForm('deanForm', this)">Dean</button>
                <button class="btn-role btn-teacher" onclick="showForm('teacherForm', this)">Teacher</button>
                <button class="btn-role btn-student" onclick="showForm('studentForm', this)">Student</button>
            </div>

            <div class="row justify-content-center">
                <div class="col-12">
 
                    <!-- Admin Form -->
                    <div id="adminForm" class="role-form">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-user-shield icon-header text-admin"></i>
                            <h4 class="fw-bold">Admin Login</h4>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Admin ID</label>
                                <input type="text" name="admin_id" class="form-control admin-focus" placeholder="Enter Admin ID" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Password</label>
                                <input type="password" name="number" class="form-control admin-focus" placeholder="Enter Password" required>
                            </div>
                            <button type="submit" name="admin-login" class="submit-btn btn-admin w-100">Login to Dashboard</button>
                        </form>
                    </div>

                    <!-- Dean Form -->
                    <div id="deanForm" class="role-form d-none">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-user-tie icon-header text-dean"></i>
                            <h4 class="fw-bold">Dean Login</h4>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Dean ID</label>
                                <input type="text" name="dean_id" class="form-control dean-focus" placeholder="Enter Dean ID" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Password</label>
                                <input type="password" name="number" class="form-control dean-focus" placeholder="Enter Password" required>
                            </div>
                            <button type="submit" name="dean-login" class="submit-btn btn-dean w-100">Login to Dashboard</button>
                        </form>
                    </div>

                    <!-- Teacher Form -->
                    <div id="teacherForm" class="role-form d-none">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-chalkboard-user icon-header text-teacher"></i>
                            <h4 class="fw-bold">Teacher Login</h4>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Teacher ID</label>
                                <input type="text" name="teacher_id" class="form-control teacher-focus" placeholder="Enter Teacher ID" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Password</label>
                                <input type="password" name="number" class="form-control teacher-focus" placeholder="Enter Password" required>
                            </div>
                            <button type="submit" name="teacher-login" class="submit-btn btn-teacher w-100">Login to Dashboard</button>
                        </form>
                    </div>

                    <!-- Student Form -->
                    <div id="studentForm" class="role-form d-none">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-user-graduate icon-header text-student"></i>
                            <h4 class="fw-bold">Student Login</h4>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Roll Number / Student ID</label>
                                <input type="text" name="student_id" class="form-control student-focus" placeholder="Enter Roll Number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium text-muted">Password</label>
                                <input type="password" name="password" class="form-control student-focus" placeholder="Enter Password" required>
                            </div>
                            <button type="submit" name="stu_submit" class="submit-btn btn-student w-100">Login to Dashboard</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(formId, btnElement) {
            // Hide all forms
            const forms = document.querySelectorAll('.role-form');
            forms.forEach(form => {
                form.classList.add('d-none');
            });
            
            // Show selected form
            const selectedForm = document.getElementById(formId);
            if (selectedForm) {
                selectedForm.classList.remove('d-none');
            }

            // Update active button state
            const buttons = document.querySelectorAll('.btn-role');
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            btnElement.classList.add('active');
        } 
    </script>
</body>

</html>
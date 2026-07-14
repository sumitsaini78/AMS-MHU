<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
}

// On view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    // Fixed: Added quotes and addslashes to prevent JavaScript syntax errors with strings
    echo "<script>alert('" . addslashes($faculty_name) . "');</script>";
}

// Getting distinct subject data using GROUP BY to prevent duplicate subject rows
$query = "SELECT subject_name FROM `attendance` GROUP BY subject_name";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Subject Attendance</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <style>
        #mhu-text {
            color: #a2c250;
            text-shadow: 1px 2px 14px rgb(46 195 41);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS
                    <sub>Assign-Student-Subject</sub></span>
                <p></p>
                <div class="right"><a href=""></a></div>
                <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                    <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                    <?php echo isset($_SESSION['teacher_name']) ? htmlspecialchars($_SESSION['teacher_name']) : 'Teacher'; ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <?php
        $query = "SELECT * FROM `students`";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            echo '<div class="container mt-4">';
            echo '<h2 class="mb-4">Assign Subjects to Students</h2>';
            echo '<form method="POST" action="assign_student_subject.php">';
            echo '<div class="mb-3">';
            echo '<label for="student" class="form-label">Select Student:</label>';
            echo '<select class="form-select" id="student" name="student_id" required>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
            }
            echo '</select>';
            echo '</div>';

            // Fetch subjects for the dropdown
            $subject_query = "SELECT subject_name FROM `subjected_teacher` WHERE teacher_id = '" . $_SESSION['teacher_id'] . "'";
            $subject_result = mysqli_query($conn, $subject_query);

            if ($subject_result && mysqli_num_rows($subject_result) > 0) {
                echo '<div class="mb-3">';
                echo '<label for="subject" class="form-label">Select Subject:</label>';
                echo '<select class="form-select" id="subject" name="subject_name" required>';
                while ($subject_row = mysqli_fetch_assoc($subject_result)) {
                    echo '<option value="' . htmlspecialchars($subject_row['subject_name']) . '">' . htmlspecialchars($subject_row['subject_name']) . '</option>';
                }
                echo '</select>';
                echo '</div>';
            } else {
                echo '<p>No subjects available for assignment.</p>';
            }

            echo '<button type="submit" name="assign_subject" class="btn btn-primary">Assign Subject</button>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<p>No students found.</p>';
        }   


        ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
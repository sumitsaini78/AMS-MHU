<?php 
include "../db_connect.php";

// Handle form submission for assigning a subject to a teacher
if (isset($_POST['submit_assignment'])) {
    $teacher_id = $_POST['teacher_id'];
    $subject_id = $_POST['subject_id'];

    // Fetch teacher details
    $teacher_query = mysqli_query($conn, "SELECT * FROM teachers WHERE id = '$teacher_id'");
    $teacher = mysqli_fetch_assoc($teacher_query);

    // Fetch subject details
    $subject_query = mysqli_query($conn, "SELECT * FROM subjects WHERE course_id = '$subject_id'");
    $subject = mysqli_fetch_assoc($subject_query);

    if ($teacher && $subject) {
        $teacher_name = $teacher['name'];
        $subject_name = $subject['subject_name'];
        $course_name = $subject['course_name'];
        $year = $subject['Year'];
        $semester = $subject['semester'];
        $subject_code = $subject['subject_code'];

        $insert_query = "INSERT INTO subjected_teacher (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) 
                         VALUES ('$teacher_id', '$subject_id', '$teacher_name', '$subject_name', '$course_name', '$year', '$semester', '$subject_code')";
        mysqli_query($conn, $insert_query);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM subjected_teacher WHERE id = '$id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Manage Teachers and Subjects - Mhu-AMS</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body>  
        <header>
            <nav class="navbar navbar-dark bg-dark shadow">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Button to open the All Teachers Modal -->
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#allTeachersModal">
                            Show All Teachers
                        </button>
                        <a href="index.php" class="text-white text-decoration-none">Home</a>
                    </div>
                </div>
            </nav>
        </header>

        <main class="container my-5">
            <div class="row">
                <!-- Form to Assign/Add Teacher & Subject -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Assign Subject to Teacher</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="teacherSelect" class="form-label">Teacher Name</label>
                                    <select class="form-select" id="teacherSelect" name="teacher_id" required>
                                        <option value="" selected disabled>Select Teacher</option>
                                        <?php
                                        $teachers_res = mysqli_query($conn, "SELECT * FROM teachers");
                                        while ($t = mysqli_fetch_assoc($teachers_res)) {
                                            echo '<option value="' . $t['id'] . '">' . htmlspecialchars($t['name']) . ' (' . htmlspecialchars($t['faculty']) . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="subjectSelect" class="form-label">Assigned Subject</label>
                                    <select class="form-select" id="subjectSelect" name="subject_id" required>
                                        <option value="" selected disabled>Select Subject</option>
                                        <?php
                                        $subjects_res = mysqli_query($conn, "SELECT * FROM subjects");
                                        while ($s = mysqli_fetch_assoc($subjects_res)) {
                                            echo '<option value="' . $s['course_id'] . '">' . htmlspecialchars($s['subject_name']) . ' - ' . htmlspecialchars($s['course_name']) . ' (Sem ' . $s['semester'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="submit_assignment" class="btn btn-primary w-100">Save Assignment</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Table to Manage/View Teachers and Subjects -->
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Teacher and Subject List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Teacher Name</th>
                                            <th>Assigned Subject</th>
                                            <th>Course / Details</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $assigned_res = mysqli_query($conn, "SELECT * FROM subjected_teacher");
                                        if (mysqli_num_rows($assigned_res) > 0) {
                                            $counter = 1;
                                            while ($row = mysqli_fetch_assoc($assigned_res)) {
                                                echo '<tr>';
                                                echo '<td>' . $counter++ . '</td>';
                                                echo '<td>' . htmlspecialchars($row['teacher_name']) . '</td>';
                                                echo '<td><span class="badge bg-secondary">' . htmlspecialchars($row['subject_name']) . '</span></td>';
                                                echo '<td>' . htmlspecialchars($row['course_name']) . ' (Yr: ' . $row['year'] . ', Sem: ' . $row['semester'] . ')</td>';
                                                echo '<td>
                                                        <a href="?delete=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this assignment?\');">Delete</a>
                                                      </td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center text-muted">No assignments found.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal for All Teachers with Designations and Assigned Subjects -->
        <div class="modal fade" id="allTeachersModal" tabindex="-1" aria-labelledby="allTeachersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="allTeachersModalLabel">All Faculty Teachers & Assigned Subjects</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Teacher Name</th>
                                        <th>Designation</th>
                                        <th>Faculty</th>
                                        <th>Assigned Subject(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $all_teachers_query = "SELECT t.*, GROUP_CONCAT(st.subject_name SEPARATOR ', ') as assigned_subjects 
                                                           FROM teachers t 
                                                           LEFT JOIN subjected_teacher st ON t.id = st.teacher_id 
                                                           GROUP BY t.id";
                                    $teachers_list_res = mysqli_query($conn, $all_teachers_query);
                                    
                                    if (mysqli_num_rows($teachers_list_res) > 0) {
                                        $t_counter = 1;
                                        while ($t_row = mysqli_fetch_assoc($teachers_list_res)) {
                                            echo '<tr>';
                                            echo '<td>' . $t_counter++ . '</td>';
                                            echo '<td><strong>' . htmlspecialchars($t_row['name']) . '</strong></td>';
                                            echo '<td><span class="badge bg-info text-dark">' . htmlspecialchars($t_row['designation']) . '</span></td>';
                                            echo '<td>' . htmlspecialchars($t_row['faculty']) . '</td>';
                                            
                                            $subjects = $t_row['assigned_subjects'];
                                            if (!empty($subjects)) {
                                                echo '<td><span class="badge bg-success">' . htmlspecialchars($subjects) . '</span></td>';
                                            } else {
                                                echo '<td><span class="badge bg-warning text-dark">No Subject Assigned</span></td>';
                                            }
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center text-muted">No teachers found in the database.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
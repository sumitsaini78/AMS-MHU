<?php
include "../db_connect.php";
if (isset($_POST['insert_subject'])) {
    $course_name = $_POST['course_name'];
    $subject_name = $_POST['subject_name'];
    $dept_name = $_POST['dept_name'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];


    $query = "insert into `courses` (course_name,subject_name,dept_name,semester,year) VALUES('$course_name','$subject_name','$dept_name','$semester','$year')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('" . addslashes($dept_name) . " : " . addslashes($course_name) . " : " . addslashes($subject_name) . " ,Added Successfully');</script>";
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                <a href="index.php" class="text-white">Home</a>
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container w-50 my-5 border p-3">
            <!-- 1. Added 'needs-validation' and 'novalidate' to the form -->
            <form method="post" class="needs-validation" novalidate>

                <!-- Faculty Name (Department) Selection -->
                <div class="mb-3">
                    <label for="Dept_name" class="form-label">Faculty Name</label>
                    <select class="form-select" name="dept_name" id="Dept_name" required>
                        <option selected disabled value="">Open this select menu</option>
                        <?php
                        $query = "SELECT dep_name FROM `departments`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['dep_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">Please select a faculty.</div>
                </div>

                <!-- Course Name Input -->
                <div class="mb-3">
                    <label for="course_name" class="form-label">Course Name (3 Letters)</label>
                    <!-- 2. Moved validation attributes here into the input field -->
                    <input type="text" class="form-control" name="course_name" id="course_name" minlength="3"
                        maxlength="3" pattern="[A-Za-z]{3}" required>
                    <div class="invalid-feedback">Please enter exactly 3 letters (A-Z or a-z).</div>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Semester</label>
                    <!-- 2. Moved validation attributes here into the input field -->
                    <select class="form-select" aria-label="Default select example" name="year">
                        <option selected>Open this select menu</option>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                        <option value="3">3rd</option>
                        <option value="4">4th</option>
                        <option value="5">5th</option>
                    </select>
                    <div class="invalid-feedback">Please enter exactly 3 letters (A-Z or a-z).</div>
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <!-- 2. Moved validation attributes here into the input field -->
                    <select class="form-select" aria-label="Default select example" name="semester">
                        <option selected>Open this select menu</option>
                        <option value="1">1st</option>
                        <option value="2">2nd</option>
                        <option value="3">3rd</option>
                        <option value="4">4th</option>
                        <option value="5">5th</option>
                        <option value="6">6th</option>
                        <option value="7">7th</option>
                        <option value="8">8th</option>
                        <option value="9">9th</option>
                        <option value="10">10th</option>
                    </select>
                    <div class="invalid-feedback">Please enter exactly 3 letters (A-Z or a-z).</div>
                </div>

                <!-- Subject Name Input -->
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name</label>
                    <input type="text" class="form-control" name="subject_name" id="subject_name" required>
                    <div class="invalid-feedback">Please enter a subject name.</div>
                </div>

                <!-- Submit Button -->
                <input type="submit" class="btn btn-primary mt-3" name="insert_subject" value="Add Subject">
            </form>
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <!-- 3. Mandatory Bootstrap 5 form validation script -->
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>
<?php
include "../db_connect.php";
$message = "";
if (!isset($_POST['course_submit'])) {
    header("Location: ./index.php");
} else {
    $course_name = $_POST['course_name'];
}
if (isset($_POST['Allocate_Subject'])) {
    echo "<script>alert('" . $_POST['course_name'] . "');</script>";

    // Getting Teacher data
    $selected_teacher = $_POST['selected_teacher'];
    $parts_t = explode('--', $selected_teacher, 2);
    $teacher_name = $parts_t[0];
    $teacher_id = $parts_t[1];
    // Getting Course and Subject data
    $selected_course_info = $_POST['selected_course'];
    $parts_c = explode('--', $selected_course_info, 3);
    $subject_name = $parts_c[0];
    $sub_id = $parts_c[1];
    $subject_code = $parts_c[2];

    $course_name = $_POST['course_name'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    $query = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) 
              VALUES ('$teacher_id', '$sub_id', '$teacher_name', '$subject_name', '$course_name', '$year', '$semester', '$subject_code')";

    if (mysqli_query($conn, $query)) {
        $message = '<div class="alert alert-success mt-3">Successfully allocated ' . htmlspecialchars($subject_name) . ' to ' . htmlspecialchars($teacher_name) . '!</div>';
    } else {
        $message = '<div class="alert alert-danger mt-3">Error: ' . mysqli_error($conn) . '</div>';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Assign-Subject | Mhu-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <span class="navbar-brand fw-bold">Mhu-AMS <span class="text-primary">Dean</span></span>
            <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Subject Teacher Allotment For <mark><?php echo $course_name; ?></mark></h5>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="post">
                            <!-- Select Teacher -->
                            <div class="mb-3">
                                <label class="form-label">Select Teacher</label>
                                <select class="form-select" name="selected_teacher" required>
                                    <option value="" selected disabled>Select Teacher</option>
                                    <?php
                                    $q = mysqli_query($conn, "SELECT id, name FROM `teachers` WHERE faculty = 'FOCBS'");
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        echo "<option value='{$row['name']}--{$row['id']}'>{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Select Course -->
                            <!-- <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select class="form-select" name="course_name" required>
                                <option value="" selected disabled>Select Course</option>
                                <?php
                                $q = mysqli_query($conn, "SELECT DISTINCT course_name FROM `courses_list` WHERE faculty_name='Faculty of Commerce & Business Studies'");
                                while ($row = mysqli_fetch_assoc($q)) {
                                    echo "<option value='{$row['course_name']}'>{$row['course_name']}</option>";
                                }
                                ?>
                            </select>
                        </div> -->

                            <!-- Select Subject -->
                            <div class="mb-3">
                                <label class="form-label">Select Subject</label>
                                <select class="form-select" name="selected_course" required>
                                    <option value="" selected disabled>Select Subject</option>
                                    <?php
                                    $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code FROM `subjects` WHERE dept_name = 'FOCBS'");
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        echo "<option value='{$row['subject_name']}--{$row['course_id']}--{$row['subject_code']}'>{$row['subject_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 500px;">
                                <h4 class="mb-4 text-primary">Select Year & Semester</h4>

                                <!-- Added Bootstrap 'row' to align items horizontally -->
                                <div class="row">

                                    <!-- Year Dropdown (Takes up 50% of the row) -->
                                    <div class="col-6 mb-3">
                                        <label for="year" class="form-label fw-bold">Select Year:</label>
                                        <select id="year" name="year" class="form-select">
                                            <option value="">-- Choose Year --</option>
                                            <option value="1">1st Year</option>
                                            <option value="2">2nd Year</option>
                                            <option value="3">3rd Year</option>
                                            <option value="4">4th Year</option>
                                        </select>
                                    </div>

                                    <!-- Semester Dropdown (Takes up the other 50% of the row) -->
                                    <div class="col-6 mb-3">
                                        <label for="semester" class="form-label fw-bold">Select Semester:</label>
                                        <!-- Default disable rakha hai jab tak year select na ho -->
                                        <select id="semester" name="semester" class="form-select" disabled>
                                            <option value="">-- Choose Semester --</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div>
                                <input type="hidden" name="course_name"
                                    value="<?php echo htmlspecialchars($course_name); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="Allocate_Subject">Confirm
                                Allocation</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- JavaScript / AJAX Logic -->
    <script>
        document.getElementById('year').addEventListener('change', function () {
            let selectedYear = this.value;
            let semesterDropdown = document.getElementById('semester');

            // Dropdown reset karna
            semesterDropdown.innerHTML = '<option value="">-- Choose Semester --</option>';
            semesterDropdown.disabled = true; // Disable if no year is selected

            // Agar user ne koi valid year select kiya hai
            if (selectedYear !== "") {

                // Form data prepare karna
                let formData = new FormData();
                formData.append('year', selectedYear);

                // Fetch API (Modern AJAX) se PHP file ko data bhejna
                fetch('get_semesters.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json()) // JSON data receive karna
                    .then(data => {
                        // Semester dropdown ko enable karna
                        semesterDropdown.disabled = false;

                        // Received JSON data ko dropdown options mein convert karna
                        for (let key in data) {
                            let option = document.createElement('option');
                            option.value = key;              // Option ki value (e.g., '1')
                            option.textContent = data[key];  // Option ka text (e.g., 'Semester 1')
                            semesterDropdown.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching semesters:', error);
                    });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
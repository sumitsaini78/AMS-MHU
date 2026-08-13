<?php
include "../db_connect.php";
$message = "";

// ==========================================
// 1. DOWNLOAD SAMPLE CSV LOGIC
// ==========================================
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_bulk_assign.csv"');
    $output = fopen('php://output', 'w');

    fputcsv($output, array('Teacher_Name', 'Course_Name', 'Subject_Name', 'Year', 'Semester', '', 'REFERENCE: Valid_Teacher_Names_To_Copy'));

    $teachers = [];
    $t_q = mysqli_query($conn, "SELECT name FROM teachers");
    while ($t = mysqli_fetch_assoc($t_q)) {
        $teachers[] = $t['name'];
    }

    $subjects = [];
    $s_query = "SELECT s.course_name, s.subject_name, s.Year, s.semester, st.teacher_name 
                FROM subjects s
                LEFT JOIN subjected_teacher st 
                ON s.course_id = st.sub_id 
                AND s.course_name = st.course_name 
                AND s.Year = st.year 
                AND s.semester = st.semester
                ORDER BY s.course_name, s.semester";
    
    $s_q = mysqli_query($conn, $s_query);
    while ($s = mysqli_fetch_assoc($s_q)) {
        $subjects[] = $s;
    }

    $max_rows = max(count($teachers), count($subjects));

    for ($i = 0; $i < $max_rows; $i++) {
        $t_name_fill = isset($subjects[$i]['teacher_name']) && !empty($subjects[$i]['teacher_name']) ? $subjects[$i]['teacher_name'] : "";
        $c_name = isset($subjects[$i]) ? $subjects[$i]['course_name'] : "";
        $s_name = isset($subjects[$i]) ? $subjects[$i]['subject_name'] : "";
        $yr = isset($subjects[$i]) ? $subjects[$i]['Year'] : "";
        $sem = isset($subjects[$i]) ? $subjects[$i]['semester'] : "";

        $valid_teacher_ref = isset($teachers[$i]) ? $teachers[$i] : "";

        fputcsv($output, array($t_name_fill, $c_name, $s_name, $yr, $sem, '', $valid_teacher_ref));
    }

    fclose($output);
    exit();
}

// ==========================================
// 2. SINGLE ALLOCATION LOGIC
// ==========================================
if (isset($_POST['Allocate_Subject'])) {
    $selected_teacher = $_POST['selected_teacher'];
    $parts_t = explode('--', $selected_teacher, 2);
    $teacher_name = mysqli_real_escape_string($conn, $parts_t[0]);
    $teacher_id = mysqli_real_escape_string($conn, $parts_t[1]);

    $selected_course_info = $_POST['selected_course'];
    $parts_c = explode('--', $selected_course_info, 3);
    $subject_name = mysqli_real_escape_string($conn, $parts_c[0]);
    $sub_id = mysqli_real_escape_string($conn, $parts_c[1]);
    $subject_code = mysqli_real_escape_string($conn, $parts_c[2]);

    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);

    $check_query = "SELECT id FROM `subjected_teacher` WHERE teacher_id = '$teacher_id' AND sub_id = '$sub_id' AND course_name = '$course_name' AND year = '$year' AND semester = '$semester'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = '<div class="alert alert-warning mt-3"><strong>Duplicate Entry:</strong> ' . htmlspecialchars($subject_name) . ' is already allocated to ' . htmlspecialchars($teacher_name) . '.</div>';
    } else {
        $query = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) VALUES ('$teacher_id', '$sub_id', '$teacher_name', '$subject_name', '$course_name', '$year', '$semester', '$subject_code')";
        if (mysqli_query($conn, $query)) {
            $message = '<div class="alert alert-success mt-3">Successfully allocated ' . htmlspecialchars($subject_name) . ' to ' . htmlspecialchars($teacher_name) . '!</div>';
        } else {
            $message = '<div class="alert alert-danger mt-3">Error: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// ==========================================
// 3. BULK ALLOCATION (CSV) LOGIC
// ==========================================
if (isset($_POST['bulk_upload']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if ($_FILES['csv_file']['size'] > 0) {
        $handle = fopen($file, "r");
        $header = fgetcsv($handle, 1000, ","); 
        $success_count = 0;
        $errors = [];
        $row_num = 1;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_num++;
            if (count($data) < 5) continue; 

            $t_name = mysqli_real_escape_string($conn, trim($data[0]));
            $c_name = mysqli_real_escape_string($conn, trim($data[1]));
            $s_name = mysqli_real_escape_string($conn, trim($data[2]));
            $yr = mysqli_real_escape_string($conn, trim($data[3]));
            $sem = mysqli_real_escape_string($conn, trim($data[4]));

            if (empty($t_name) || empty($c_name) || empty($s_name)) continue;

            $t_query = mysqli_query($conn, "SELECT id FROM teachers WHERE name = '$t_name' LIMIT 1");
            if (mysqli_num_rows($t_query) == 0) {
                $errors[] = "Row $row_num: Teacher <b>'$t_name'</b> not found in database.";
                continue;
            }
            $teacher_id = mysqli_fetch_assoc($t_query)['id'];

            $s_query = mysqli_query($conn, "SELECT course_id, subject_code FROM subjects WHERE subject_name = '$s_name' AND course_name = '$c_name' AND Year = '$yr' AND semester = '$sem'");
            if (mysqli_num_rows($s_query) == 0) {
                $errors[] = "Row $row_num: Subject <b>'$s_name'</b> for Course <b>'$c_name'</b> (Sem $sem) not found.";
                continue;
            }
            $s_row = mysqli_fetch_assoc($s_query);
            $sub_id = $s_row['course_id'];
            $subject_code = $s_row['subject_code'];

            $dup_query = mysqli_query($conn, "SELECT id FROM subjected_teacher WHERE teacher_id = '$teacher_id' AND sub_id = '$sub_id' AND course_name = '$c_name' AND year = '$yr' AND semester = '$sem'");
            if (mysqli_num_rows($dup_query) > 0) {
                $errors[] = "Row $row_num: Duplicate - <b>'$s_name'</b> is already allocated to <b>$t_name</b>.";
                continue;
            }

            $insert = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) VALUES ('$teacher_id', '$sub_id', '$t_name', '$s_name', '$c_name', '$yr', '$sem', '$subject_code')";
            if (mysqli_query($conn, $insert)) {
                $success_count++;
            } else {
                $errors[] = "Row $row_num: DB Error - " . mysqli_error($conn);
            }
        }
        fclose($handle);
        $message = "<div class='alert alert-success mt-3'><b>Bulk Upload Complete:</b> $success_count records added successfully.</div>";
        if (count($errors) > 0) {
            $message .= "<div class='alert alert-warning mt-3'><strong>Warnings/Skipped Records:</strong><ul class='mb-0'>";
            foreach ($errors as $err) { $message .= "<li>$err</li>"; }
            $message .= "</ul></div>";
        }
    } else {
        $message = '<div class="alert alert-danger mt-3">Please upload a valid CSV file.</div>';
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

    <?php include 'admin_navbar.php'; ?>

    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <?php echo $message; ?>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Admin Subject Teacher Allotment</h5>
                    </div>

                    <div class="card-body p-0">
                        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="single-tab" data-bs-toggle="tab"
                                    data-bs-target="#single" type="button" role="tab">Single Assign</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="bulk-tab" data-bs-toggle="tab"
                                    data-bs-target="#bulk" type="button" role="tab">Bulk Assign (CSV)</button>
                            </li>
                        </ul>

                        <div class="tab-content p-4" id="myTabContent">

                            <!-- TAB 1: SINGLE ASSIGNMENT -->
                            <div class="tab-pane fade show active" id="single" role="tabpanel" tabindex="0">
                                <form method="post">
                                    
                                    <!-- 1. FACULTY DROPDOWN -->
                                    <div class="mb-3">
                                        <label class="form-label text-primary fw-bold">1. Select Faculty</label>
                                        <select class="form-select border-primary" name="faculty_selection" id="facultySelect" required>
                                            <option value="" selected disabled>Select Faculty</option>
                                            <?php
                                            $fac_q = mysqli_query($conn, "SELECT faculty_full_name, faculty_name AS short_name FROM `faculty` ORDER BY faculty_name");
                                            while ($f = mysqli_fetch_assoc($fac_q)) {
                                                echo "<option value='{$f['faculty_full_name']}' data-short='{$f['short_name']}'>{$f['short_name']} - {$f['faculty_full_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <hr class="text-muted">

                                    <!-- 2. TEACHER DROPDOWN -->
                                    <div class="mb-3">
                                        <label class="form-label">2. Select Teacher</label>
                                        <select class="form-select" name="selected_teacher" id="teacherSelect" required>
                                            <option value="" selected disabled>Select Teacher</option>
                                            <?php
                                            $q = mysqli_query($conn, "SELECT id, name, faculty FROM `teachers`");
                                            while ($row = mysqli_fetch_assoc($q)) {
                                                echo "<option value='{$row['name']}--{$row['id']}' data-faculty='{$row['faculty']}'>{$row['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- 3. COURSE DROPDOWN -->
                                    <div class="mb-3">
                                        <label class="form-label">3. Select Course</label>
                                        <select class="form-select" name="course_name" id="courseSelect" required>
                                            <option value="" selected disabled>Select Course</option>
                                            <?php
                                            $sql = "SELECT DISTINCT c.course_name, c.faculty_name, f.faculty_name AS short_name 
                                                FROM `courses_list` c 
                                                LEFT JOIN `faculty` f ON c.faculty_name = f.faculty_full_name 
                                                ORDER BY f.faculty_name, c.course_name";
                                            $q = mysqli_query($conn, $sql);
                                            while ($row = mysqli_fetch_assoc($q)) {
                                                echo "<option value='{$row['course_name']}' data-faculty='{$row['faculty_name']}'>{$row['course_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- 4. YEAR & SEMESTER -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">4. Year</label>
                                            <select class="form-select" name="year" id="yearSelect" required>
                                                <option value="" selected disabled>Select Year</option>
                                                <option value="1">1st Year</option>
                                                <option value="2">2nd Year</option>
                                                <option value="3">3rd Year</option>
                                                <option value="4">4th Year</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">5. Semester</label>
                                            <select class="form-select" name="semester" id="semSelect" required>
                                                <option value="" selected disabled>Select Year First</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 5. SUBJECT DROPDOWN -->
                                    <div class="mb-4">
                                        <label class="form-label">6. Select Subject</label>
                                        <select class="form-select" name="selected_course" id="subjectSelect" required>
                                            <option value="" selected disabled>Select Subject</option>
                                            <?php
                                            $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code, course_name, Year, semester FROM `subjects`");
                                            while ($row = mysqli_fetch_assoc($q)) {
                                                echo "<option value='{$row['subject_name']}--{$row['course_id']}--{$row['subject_code']}' data-course='{$row['course_name']}' data-year='{$row['Year']}' data-sem='{$row['semester']}'>{$row['subject_name']} (Yr: {$row['Year']}, Sem: {$row['semester']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 fw-bold" name="Allocate_Subject">Confirm Allocation</button>
                                </form>
                            </div>

                            <!-- TAB 2: BULK UPLOAD (CSV) -->
                            <div class="tab-pane fade" id="bulk" role="tabpanel" tabindex="0">
                                <div class="alert alert-secondary d-flex flex-column gap-2">
                                    <div>
                                        <strong>How to use Bulk Assign:</strong>
                                        <ul class="mb-0">
                                            <li>Download the Sample CSV below. It contains ALL subjects and their current assignments.</li>
                                            <li>Copy a valid Teacher Name from the right-most Reference column and paste it in the first column next to the subject you want to assign.</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a href="?download_sample=1" class="btn btn-success btn-sm w-100 fw-bold">⬇️ Download Ready-to-Use Sample CSV</a>
                                    </div>
                                </div>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Select CSV File to Upload</label>
                                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 fw-bold" name="bulk_upload">Upload & Allocate Bulk Subjects</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const facultySelect = document.getElementById('facultySelect');
            const teacherSelect = document.getElementById('teacherSelect');
            const courseSelect = document.getElementById('courseSelect');
            const yearSelect = document.getElementById('yearSelect');
            const semSelect = document.getElementById('semSelect');
            const subjectSelect = document.getElementById('subjectSelect');

            const allTeachers = Array.from(teacherSelect.querySelectorAll('option:not(:first-child)'));
            const allCourses = Array.from(courseSelect.querySelectorAll('option:not(:first-child)'));
            const allSubjects = Array.from(subjectSelect.querySelectorAll('option:not(:first-child)'));
            
            teacherSelect.innerHTML = '<option value="" selected disabled>Select Faculty First</option>';
            courseSelect.innerHTML = '<option value="" selected disabled>Select Faculty First</option>';
            subjectSelect.innerHTML = '<option value="" selected disabled>Select Course, Year & Semester First</option>';

            // -------------------------------------------------------------
            // LOGICAL SEMESTER MAPPING (1st Yr -> 1,2 | 2nd Yr -> 3,4 | 3rd Yr -> 5,6 | 4th Yr -> 7,8)
            // Note: If your DB stores semester as "1" and "2" for EVERY year, 
            // change values below to val: "1" and val: "2" for all years.
            // -------------------------------------------------------------
            const semestersMap = {
                "1": [{ val: "1", text: "1st Semester" }, { val: "2", text: "2nd Semester" }],
                "2": [{ val: "3", text: "3rd Semester" }, { val: "4", text: "4th Semester" }],
                "3": [{ val: "5", text: "5th Semester" }, { val: "6", text: "6th Semester" }],
                "4": [{ val: "7", text: "7th Semester" }, { val: "8", text: "8th Semester" }]
            };

            // --- 1. FACULTY FILTER (Filters Teachers & Courses) ---
            facultySelect.addEventListener('change', function() {
                const selectedFacFull = this.value;
                const selectedOption = this.options[this.selectedIndex];
                const selectedFacShort = selectedOption.getAttribute('data-short');

                yearSelect.value = "";
                semSelect.innerHTML = '<option value="" selected disabled>Select Year First</option>';
                subjectSelect.innerHTML = '<option value="" selected disabled>Select Course, Year & Semester First</option>';

                // Filter Teachers
                teacherSelect.innerHTML = '<option value="" selected disabled>Select Teacher</option>';
                let hasTeachers = false;
                allTeachers.forEach(t => {
                    const teacherFac = t.getAttribute('data-faculty');
                    if (teacherFac === selectedFacFull || teacherFac === selectedFacShort) {
                        teacherSelect.appendChild(t.cloneNode(true));
                        hasTeachers = true;
                    }
                });
                if(!hasTeachers) teacherSelect.innerHTML = '<option value="" selected disabled>No teachers found for this faculty</option>';

                // Filter Courses
                courseSelect.innerHTML = '<option value="" selected disabled>Select Course</option>';
                let hasCourses = false;
                allCourses.forEach(c => {
                    const courseFac = c.getAttribute('data-faculty');
                    if (courseFac === selectedFacFull || courseFac === selectedFacShort) {
                        courseSelect.appendChild(c.cloneNode(true));
                        hasCourses = true;
                    }
                });
                if(!hasCourses) courseSelect.innerHTML = '<option value="" selected disabled>No courses found for this faculty</option>';
            });

            // --- 2. YEAR TO LOGICAL SEMESTER FILTER ---
            yearSelect.addEventListener("change", function () {
                const selectedYear = this.value;

                semSelect.innerHTML = '<option value="" selected disabled>Select Semester</option>';
                subjectSelect.innerHTML = '<option value="" selected disabled>Select Course, Year & Semester First</option>';

                if (semestersMap[selectedYear]) {
                    semestersMap[selectedYear].forEach(sem => {
                        const option = document.createElement("option");
                        option.value = sem.val;
                        option.textContent = sem.text;
                        semSelect.appendChild(option);
                    });
                }

                filterSubjects();
            });

            // --- 3. SUBJECT FILTER ---
            function filterSubjects() {
                const selectedCourse = courseSelect.value;
                const selectedYear = yearSelect.value;
                const selectedSem = semSelect.value;

                subjectSelect.innerHTML = '<option value="" selected disabled>Select Subject</option>';

                if (selectedCourse && selectedYear && selectedSem) {
                    let hasOptions = false;
                    allSubjects.forEach(subject => {
                        const sCourse = subject.getAttribute('data-course');
                        const sYear = subject.getAttribute('data-year');
                        const sSem = subject.getAttribute('data-sem');

                        if (sCourse === selectedCourse && sYear === selectedYear && sSem === selectedSem) {
                            subjectSelect.appendChild(subject.cloneNode(true));
                            hasOptions = true;
                        }
                    });

                    if (!hasOptions) {
                        subjectSelect.innerHTML = '<option value="" selected disabled>No subjects found for this selection</option>';
                    }
                } else {
                    subjectSelect.innerHTML = '<option value="" selected disabled>Select Course, Year & Semester First</option>';
                }
            }

            semSelect.addEventListener("change", filterSubjects);
            courseSelect.addEventListener("change", filterSubjects);
        });
    </script>
</body>
</html>

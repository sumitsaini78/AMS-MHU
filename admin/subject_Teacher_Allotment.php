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
    
    // Headers for CSV (Added a Reference Column for Teacher Names)
    fputcsv($output, array('Teacher_Name', 'Course_Name', 'Subject_Name', 'Year', 'Semester', '', 'REFERENCE: Valid_Teacher_Names_To_Copy'));

    // Fetch all valid teachers for reference
    $teachers = [];
    $t_q = mysqli_query($conn, "SELECT name FROM teachers");
    while($t = mysqli_fetch_assoc($t_q)) {
        $teachers[] = $t['name'];
    }

    // Fetch all subjects to pre-fill the template
    $subjects = [];
    $s_q = mysqli_query($conn, "SELECT course_name, subject_name, Year, semester FROM subjects ORDER BY course_name, semester");
    while($s = mysqli_fetch_assoc($s_q)) {
        $subjects[] = $s;
    }

    // Determine the max rows needed to print both lists side-by-side
    $max_rows = max(count($teachers), count($subjects));

    for ($i = 0; $i < $max_rows; $i++) {
        $t_name_fill = ""; // User will type/paste the teacher name here
        $c_name = isset($subjects[$i]) ? $subjects[$i]['course_name'] : "";
        $s_name = isset($subjects[$i]) ? $subjects[$i]['subject_name'] : "";
        $yr     = isset($subjects[$i]) ? $subjects[$i]['Year'] : "";
        $sem    = isset($subjects[$i]) ? $subjects[$i]['semester'] : "";
        
        // This will print the teacher's name in column G for easy copy-pasting
        $valid_teacher_ref = isset($teachers[$i]) ? $teachers[$i] : "";

        fputcsv($output, array($t_name_fill, $c_name, $s_name, $yr, $sem, '', $valid_teacher_ref));
    }

    fclose($output);
    exit(); // Stop further page loading
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

    // Prevent Duplicacy
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
        $header = fgetcsv($handle, 1000, ","); // Skip the header row
        
        $success_count = 0;
        $errors = [];
        $row_num = 1;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_num++;
            
            // Checking if row has basic required data
            if(count($data) < 5) {
                continue; // Skip silently if it's a completely broken row
            }

            $t_name = mysqli_real_escape_string($conn, trim($data[0]));
            $c_name = mysqli_real_escape_string($conn, trim($data[1]));
            $s_name = mysqli_real_escape_string($conn, trim($data[2]));
            $yr     = mysqli_real_escape_string($conn, trim($data[3]));
            $sem    = mysqli_real_escape_string($conn, trim($data[4]));

            // IF Teacher Name is blank in CSV, simply skip it (assuming it's an unassigned subject in the template)
            if (empty($t_name) || empty($c_name) || empty($s_name)) {
                continue;
            }

            // A. Validate Teacher Name & Get ID
            $t_query = mysqli_query($conn, "SELECT id FROM teachers WHERE name = '$t_name' LIMIT 1");
            if (mysqli_num_rows($t_query) == 0) {
                $errors[] = "Row $row_num: Teacher <b>'$t_name'</b> not found in database. Please check spelling.";
                continue;
            }
            $teacher_id = mysqli_fetch_assoc($t_query)['id'];

            // B. Validate Subject & Get Details
            $s_query = mysqli_query($conn, "SELECT course_id, subject_code FROM subjects WHERE subject_name = '$s_name' AND course_name = '$c_name' AND Year = '$yr' AND semester = '$sem'");
            if (mysqli_num_rows($s_query) == 0) {
                $errors[] = "Row $row_num: Subject <b>'$s_name'</b> for Course <b>'$c_name'</b> (Sem $sem) is in Excel but NOT in Database. Skipped.";
                continue;
            }
            $s_row = mysqli_fetch_assoc($s_query);
            $sub_id = $s_row['course_id'];
            $subject_code = $s_row['subject_code'];

            // C. Check for Duplicate Allocation
            $dup_query = mysqli_query($conn, "SELECT id FROM subjected_teacher WHERE teacher_id = '$teacher_id' AND sub_id = '$sub_id' AND course_name = '$c_name' AND year = '$yr' AND semester = '$sem'");
            if (mysqli_num_rows($dup_query) > 0) {
                $errors[] = "Row $row_num: Duplicate - <b>'$s_name'</b> is already allocated to <b>$t_name</b>.";
                continue;
            }

            // D. Insert Data
            $insert = "INSERT INTO `subjected_teacher` (teacher_id, sub_id, teacher_name, subject_name, course_name, year, semester, subject_code) VALUES ('$teacher_id', '$sub_id', '$t_name', '$s_name', '$c_name', '$yr', '$sem', '$subject_code')";
            
            if (mysqli_query($conn, $insert)) {
                $success_count++;
            } else {
                $errors[] = "Row $row_num: DB Error - " . mysqli_error($conn);
            }
        }
        fclose($handle);

        // Compile output message
        $message = "<div class='alert alert-success mt-3'><b>Bulk Upload Complete:</b> $success_count records added successfully.</div>";
        if (count($errors) > 0) {
            $message .= "<div class='alert alert-warning mt-3'><strong>Warnings/Skipped Records:</strong><ul class='mb-0'>";
            foreach($errors as $err) {
                $message .= "<li>$err</li>";
            }
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

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold">Mhu-AMS <span class="text-primary">Dean</span></span>
        <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
    </div>
</nav>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            <!-- Global Message Area -->
            <?php echo $message; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Subject Teacher Allotment</h5>
                </div>
                
                <div class="card-body p-0">
                    <!-- Bootstrap Tabs Header -->
                    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">Single Assign</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab">Bulk Assign (CSV)</button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content p-4" id="myTabContent">
                        
                        <!-- TAB 1: SINGLE ASSIGNMENT -->
                        <div class="tab-pane fade show active" id="single" role="tabpanel" tabindex="0">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Select Teacher</label>
                                    <select class="form-select" name="selected_teacher" required>
                                        <option value="" selected disabled>Select Teacher</option>
                                        <?php
                                        $q = mysqli_query($conn, "SELECT id, name FROM `teachers`");
                                        while ($row = mysqli_fetch_assoc($q)) {
                                            echo "<option value='{$row['name']}--{$row['id']}'>{$row['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Course</label>
                                    <select class="form-select" name="course_name" id="courseSelect" required>
                                        <option value="" selected disabled>Select Faculty & Course</option>
                                        <?php
                                        $sql = "SELECT DISTINCT c.course_name, f.faculty_name AS short_name 
                                                FROM `courses_list` c 
                                                LEFT JOIN `faculty` f ON c.faculty_name = f.faculty_full_name 
                                                ORDER BY f.faculty_name, c.course_name";
                                        $q = mysqli_query($conn, $sql);
                                        while ($row = mysqli_fetch_assoc($q)) {
                                            $short_name = $row['short_name'] ? $row['short_name'] : 'N/A';
                                            echo "<option value='{$row['course_name']}'>{$short_name} - {$row['course_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Subject</label>
                                    <select class="form-select" name="selected_course" id="subjectSelect" required>
                                        <option value="" selected disabled>Select Subject</option>
                                        <?php
                                        $q = mysqli_query($conn, "SELECT course_id, subject_name, subject_code, course_name FROM `subjects`");
                                        while ($row = mysqli_fetch_assoc($q)) {
                                            echo "<option value='{$row['subject_name']}--{$row['course_id']}--{$row['subject_code']}' data-course='{$row['course_name']}'>{$row['subject_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Year</label>
                                        <select class="form-select" name="year" id="yearSelect" required>
                                            <option value="" selected disabled>Select Year</option>
                                            <option value="1">1st Year</option>
                                            <option value="2">2nd Year</option>
                                            <option value="3">3rd Year</option>
                                            <option value="4">4th Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Semester</label>
                                        <select class="form-select" name="semester" id="semSelect" required>
                                            <option value="" selected disabled>Select Semester</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100" name="Allocate_Subject">Confirm Allocation</button>
                            </form>
                        </div>

                        <!-- TAB 2: BULK UPLOAD (CSV) -->
                        <div class="tab-pane fade" id="bulk" role="tabpanel" tabindex="0">
                            
                            <div class="alert alert-secondary d-flex flex-column gap-2">
                                <div>
                                    <strong>How to use Bulk Assign:</strong>
                                    <ul class="mb-0">
                                        <li>Download the Sample CSV below. It contains ALL subjects from your database.</li>
                                        <li>Copy a valid Teacher Name from the right-most Reference column and paste it in the first column next to the subject you want to assign.</li>
                                        <li>If you leave a Teacher Name blank, that subject will simply be ignored.</li>
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
                                <button type="submit" class="btn btn-dark w-100" name="bulk_upload">Upload & Allocate Bulk Subjects</button>
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
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. YEAR TO SEMESTER LOGIC ---
    const yearSelect = document.getElementById('yearSelect');
    const semSelect = document.getElementById('semSelect');
    
    const semestersMap = {
        "1": [{val: "1", text: "1st"}, {val: "2", text: "2nd"}],
        "2": [{val: "3", text: "3rd"}, {val: "4", text: "4th"}],
        "3": [{val: "5", text: "5th"}, {val: "6", text: "6th"}],
        "4": [{val: "7", text: "7th"}, {val: "8", text: "8th"}]
    };

    if(yearSelect){
        yearSelect.addEventListener("change", function() {
            const selectedYear = this.value;
            semSelect.innerHTML = '<option value="" selected disabled>Select Semester</option>';
            if(semestersMap[selectedYear]) {
                semestersMap[selectedYear].forEach(sem => {
                    const option = document.createElement("option");
                    option.value = sem.val;
                    option.textContent = sem.text;
                    semSelect.appendChild(option);
                });
            }
        });
    }

    // --- 2. COURSE TO SUBJECT FILTER LOGIC ---
    const courseSelect = document.getElementById('courseSelect');
    const subjectSelect = document.getElementById('subjectSelect');
    
    if(subjectSelect){
        const allSubjects = Array.from(subjectSelect.querySelectorAll('option:not(:first-child)'));

        courseSelect.addEventListener("change", function() {
            const selectedCourse = this.value; 
            
            subjectSelect.innerHTML = '<option value="" selected disabled>Select Subject</option>';
            
            allSubjects.forEach(subject => {
                const subjectCourse = subject.getAttribute('data-course');
                if (subjectCourse === selectedCourse || !subjectCourse) {
                    subjectSelect.appendChild(subject.cloneNode(true));
                }
            });
        });
    }
});
</script>
</body>
</html>
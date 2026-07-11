<?php
include "./db_connect.php";
session_start();

// Security: Check if Dean/Staff is logged in
if (!isset($_SESSION['dean_id'])) {
    header("Location: ./index.php");
    exit;
}
// Variables initialization
$selected_dept = isset($_GET['dept_name']) ? mysqli_real_escape_string($conn, $_GET['dept_name']) : '';
$selected_course = isset($_GET['course_name']) ? mysqli_real_escape_string($conn, $_GET['course_name']) : '';
$selected_subject = isset($_GET['subject_code']) ? mysqli_real_escape_string($conn, $_GET['subject_code']) : '';
$selected_semester = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : '';

$dates = [];
$students_attendance = [];

// Agar saare filters select ho chuke hain, tabhi data fetch karein
if ($selected_dept && $selected_course && $selected_subject && $selected_semester) {
    
    // 1. Is subject aur semester ki saari UNIQUE dates nikaalein (Table Headers ke liye)
    $date_query = "SELECT DISTINCT date FROM attendance 
                   WHERE subject_code = '$selected_subject' AND semester = '$selected_semester' 
                   ORDER BY date ASC";
    $date_result = mysqli_query($conn, $date_query);
    while ($row = mysqli_fetch_assoc($date_result)) {
        $dates[] = $row['date'];
    }

    // 2. Students ki list aur unki date-wise attendance status nikaalein
    // NOTE: Apni attendance table ke columns ke hisab se mapping check kar lein
    $att_query = "SELECT s.roll_number, s.name as student_name, a.date, a.status 
                  FROM students s
                  LEFT JOIN attendance a ON s.roll_number = a.roll_number AND a.subject_code = '$selected_subject'
                  WHERE s.faculty = '$selected_dept' AND s.course = '$selected_course' AND s.sem = '$selected_semester'
                  ORDER BY s.roll_number ASC, a.date ASC";
                  
    $att_result = mysqli_query($conn, $att_query);

    while ($row = mysqli_fetch_assoc($att_result)) {
        $roll = $row['roll_number'];
        
        // Agar student array me nahi h to structure create karein
        if (!isset($students_attendance[$roll])) {
            $students_attendance[$roll] = [
                'name' => $row['student_name'],
                'attendance' => [], // Isme date => status store hoga
                'present_count' => 0,
                'total_lectures' => 0
            ];
        }

        // Agar us date par record exist karta hai
        if ($row['date'] !== null) {
            $students_attendance[$roll]['attendance'][$row['date']] = $row['status'];
            $students_attendance[$roll]['total_lectures']++;
            if ($row['status'] == 'Present') {
                $students_attendance[$roll]['present_count']++;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>View Attendance | Matrix View</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome Icons for Check/Cross -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .table-responsive { max-height: 600px; overflow-y: auto; }
        .sticky-col { position: sticky; left: 0; background-color: white !important; z-index: 1; }
        .sticky-header { position: sticky; top: 0; background-color: #212529 !important; color: white; z-index: 2; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fs-4">Mhu-AMS ➔ View Attendance</span>
            <a href="dean_dashboard.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
        </div>
    </nav>

    <main class="container-fluid my-4">
        <!-- FILTER CARD -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white fw-bold">Filter Class & Subject</div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <!-- Department -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">faculty</label>
                        <select class="form-select form-select-sm" name="dept_name" required>
                            <option value="">Select Dept</option>
                            <?php
                            $q = mysqli_query($conn, "SELECT dep_name FROM departments");
                            while($r = mysqli_fetch_assoc($q)){
                                $sel = ($selected_dept == $r['dep_name']) ? 'selected' : '';
                                echo "<option value='".htmlspecialchars($r['dep_name'])."' $sel>".htmlspecialchars($r['dep_name'])."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Course -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Course</label>
                        <select class="form-select form-select-sm" name="course_name" required>
                            <option value="">Select Course</option>
                            <?php
                            $q = mysqli_query($conn, "SELECT course_name FROM courses_list");
                            while($r = mysqli_fetch_assoc($q)){
                                $sel = ($selected_course == $r['course_name']) ? 'selected' : '';
                                echo "<option value='".htmlspecialchars($r['course_name'])."' $sel>".htmlspecialchars($r['faculty_name'].' - '.$r['course_name'])."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Semester</label>
                        <input type="number" class="form-select form-select-sm" name="semester" value="<?php echo htmlspecialchars($selected_semester); ?>" placeholder="e.g. 4" required>
                    </div>

                    <!-- Subject -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Subject / Code</label>
                        <select class="form-select form-select-sm" name="subject_code" required>
                            <option value="">Select Subject</option>
                            <?php
                            $q = mysqli_query($conn, "SELECT subject_code, subject_name FROM subjects");
                            while($r = mysqli_fetch_assoc($q)){
                                $sel = ($selected_subject == $r['subject_code']) ? 'selected' : '';
                                echo "<option value='".htmlspecialchars($r['subject_code'])."' $sel>".htmlspecialchars($r['subject_name'])." (".$r['subject_code'].")</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="fa fa-search"></i> Fetch</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ATTENDANCE MATRIX SHEET -->
        <?php if (!empty($students_attendance)): ?>
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover m-0 text-center align-middle">
                            <thead class="table-dark sticky-header">
                                <tr>
                                    <th class="sticky-col" style="min-width: 100px;">Roll No</th>
                                    <th class="sticky-col" style="left: 100px; min-width: 180px;">Student Name</th>
                                    <!-- Dynamic Dates Headers loop -->
                                    <?php foreach ($dates as $date): ?>
                                        <th style="min-width: 90px; font-size: 13px;"><?php echo date('d-M', strtotime($date)); ?></th>
                                    <?php endforeach; ?>
                                    <th style="min-width: 100px; background-color: #198754;">Total %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students_attendance as $roll => $data): ?>
                                    <tr>
                                        <!-- Roll & Name (Sticky columns for clean scrolling) -->
                                        <td class="sticky-col fw-bold bg-light"><?php echo $roll; ?></td>
                                        <td class="sticky-col text-start bg-light" style="left: 100px;"><?php echo htmlspecialchars($data['name']); ?></td>
                                        
                                        <!-- Dates Matrix looping -->
                                        <?php foreach ($dates as $date): 
                                            $status = isset($data['attendance'][$date]) ? $data['attendance'][$date] : '-';
                                            if ($status == 'Present') {
                                                echo "<td><span class='text-success fw-bold'><i class='fa-solid fa-circle-check'></i> P</span></td>";
                                            } elseif ($status == 'Absent') {
                                                echo "<td><span class='text-danger fw-bold'><i class='fa-solid fa-circle-xmark'></i> A</span></td>";
                                            } else {
                                                echo "<td class='text-muted'>-</td>";
                                            }
                                        endforeach; ?>

                                        <!-- Percentage Logic -->
                                        <?php 
                                        $percentage = ($data['total_lectures'] > 0) ? round(($data['present_count'] / $data['total_lectures']) * 100, 1) : 0;
                                        $badge_class = ($percentage < 75) ? 'bg-danger' : 'bg-success';
                                        ?>
                                        <td class="fw-bold">
                                            <span class="badge <?php echo $badge_class; ?> px-2 py-1 fs-6">
                                                <?php echo $percentage; ?>%
                                            </span>
                                            <div class="small text-muted"><?php echo $data['present_count']."/".$data['total_lectures']; ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($selected_subject): ?>
            <div class="alert alert-warning text-center fw-bold shadow-sm">
                ⚠️ Is Batch/Subject ke liye koi attendance record nahi mila! Filters check karein.
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center fw-bold shadow-sm">
                💡 Upar diye gaye filters select karke "Fetch" button par click karein.
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
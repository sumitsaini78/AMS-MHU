<?php
session_start();
include "../db_connect.php";
// on view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    $teacher_name = $_POST['teacher_name'];
    echo "<script>alert($faculty_name);</script>";
}
// getting subject attendence data 
$query = "select * from `attendance`";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
print_r($row);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Subject Attendence</title>
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
                    <sub>Subject-Attendence</sub></span>
                <P></P>
                <div class="right"><a href=""></a></div>
                <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                    <i class="fa-solid fa-user-tie me-1"></i> Welcome, <?php echo $_SESSION['teacher_name']; ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
            </div>
        </nav>

    </header>
    <main>
        <div class="container w-50 border border-warning mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Attendance %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Manlo aapka data ek array mein hai
                    $subjects = [
                        ["name" => "Maths", "percent" => 85],
                        ["name" => "Physics", "percent" => 65],
                        ["name" => "Chemistry", "percent" => 40]
                    ];

                    foreach ($subjects as $sub) {
                        // Logic: Agar 75% se kam hai toh red dikhayein
                        $color = ($sub['percent'] < 75) ? 'text-danger' : 'text-success';

                        echo "<tr>
                    <td>{$sub['name']}</td>
                    <td class='$color'>{$sub['percent']}%</td>
                    <td>
                        <div class='progress'>
                            <div class='progress-bar' style='width:{$sub['percent']}%'></div>
                        </div>
                    </td>
                  </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- <div class="row">
    <?php foreach ($subjects as $sub): ?>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $sub['name']; ?></h5>
                    <p class="card-text">Attendance: <?php echo $sub['percent']; ?>%</p>
                    <a href="subject_attendance.php?sub=<?php echo $sub['name']; ?>" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> -->


    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
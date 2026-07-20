<?php
include "../db_connect.php";
// Note: Ensure your database connection ($conn) is included at the top of this file

// 1. Capture the course name from the URL
$course_param = $_GET['course'] ?? '';
$safe_course_name = mysqli_real_escape_string($conn, $course_param);

// 2. Get today's date in DDMMYY integer format
$today_date = (int)date('dmy');

// 3. Query to fetch data (Updated with LEFT JOIN for subjected_teacher)

// 3. Query to fetch data (Fixed to prevent duplicate rows)
  // 3. Query to fetch data (Fixed to prevent duplicate rows AND get the teacher's name)
    $query = "SELECT 
                s.subject_name,
                MAX(s.year) AS year,          
                MAX(s.semester) AS semester,      
                c.course_name,
                MAX(st.teacher_name) AS assigned_teacher,
                (
                    SELECT COUNT(*)
                    FROM `attendance` a     
                    WHERE TRIM(a.subject_name) = TRIM(s.subject_name) 
                    AND TRIM(a.course) = TRIM(c.course_name) 
                    AND a.date_of_attendence = '$today_date'
                ) AS marked_today
            FROM `subjects` s
            JOIN `courses_list` c ON s.course_name = c.course_name
            LEFT JOIN `subjected_teacher` st ON TRIM(s.subject_name) = TRIM(st.subject_name)
            WHERE s.course_name = '$safe_course_name'
            GROUP BY s.subject_name, c.course_name";

    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Dashboard</title>
    <style>
        /* Base Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        /* Table Container for Responsiveness and Card Look */
        .table-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            overflow-x: auto; /* Makes table scrollable on small screens */
        }

        /* Modern Table Styling */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95em;
            min-width: 600px;
        }

        .styled-table thead tr {
            background-color: #4361ee;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th, 
        .styled-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        /* Zebra striping for rows */
        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }

        /* Hover effect on rows */
        .styled-table tbody tr:hover {
            background-color: #f1f3f5;
            transition: all 0.2s ease-in-out;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #4361ee;
        }

        /* Styling the Subject Link */
        .subject-link {
            color: #4361ee;
            text-decoration: none;
            font-weight: 600;
        }

        .subject-link:hover {
            color: #3f37c9;
            text-decoration: underline;
        }

        /* Beautiful Status Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }

        .badge.marked {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .badge.not-marked {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Subject Name (Year - Sem)</th>
                <th>Course Name</th>
                <th>Assigned Teacher</th>
                <th>Status (Today)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $index = 1;
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $subject_name = htmlspecialchars($row['subject_name'] ?? '');
                    $year = htmlspecialchars($row['year'] ?? '');
                    $semester = htmlspecialchars($row['semester'] ?? '');
                    $course_name = htmlspecialchars($row['course_name'] ?? '');
                    
                    // Fetch the teacher name instead of faculty name. Provide fallback if none assigned.
                    $assigned_teacher = htmlspecialchars($row['assigned_teacher'] ?? 'Not Assigned');
                    
                    // UI Updated Status Badges
                    if ($row['marked_today'] > 0) {
                        $status_text = "<span class='badge marked'>✓ Marked</span>";
                    } else {
                        $status_text = "<span class='badge not-marked'>✗ Pending</span>";
                    }
                    
                    $url_subject = urlencode($row['subject_name']);
                    $url_course = urlencode($row['course_name']);

                    echo "<tr>";
                    echo "<td><strong>{$index}</strong></td>";
                    
                    // Styled Link Class Added
                    echo "<td><a class='subject-link' href='course_details.php?subject={$url_subject}&course={$url_course}'>{$subject_name} (Yr: {$year}, Sem: {$semester})</a></td>";
                    
                    echo "<td>{$course_name}</td>";
                    echo "<td>{$assigned_teacher}</td>"; // Display the Assigned Teacher
                    echo "<td>{$status_text}</td>";
                    echo "</tr>";
                    
                    $index++;
                }
            } else {
                echo "<tr><td colspan='5' class='empty-state'>No subjects found for this course.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
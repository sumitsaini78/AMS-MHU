<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Manage Subjects | Dean Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #495057; }
        .btn-submit { padding: 12px 30px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Subject Management</h2>
            <p class="text-muted">Register new subjects or upload in bulk.</p>
        </div>
        <a href="index.php" class="btn btn-outline-dark"><i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard</a>
    </div>

    <div class="row g-4">
        <!-- 1. Bulk Import Card -->
        <div class="col-lg-12">
            <div class="card main-card p-4">
                <h5 class="mb-3 text-success"><i class="fa-solid fa-file-csv me-2"></i>Bulk Import</h5>
                <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label">Upload CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="text-muted">Format: Dept, Course, Subject, Year, Sem, Code</small>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="import_csv" class="btn btn-success w-100">Upload Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Manual Entry Form Card -->
        <div class="col-lg-12">
            <div class="card main-card p-4">
                <h5 class="mb-4 text-primary"><i class="fa-solid fa-plus-circle me-2"></i>Manual Entry</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="dept_name" required>
                            <option value="" disabled selected>Choose...</option>
                            <?php /* PHP loop here */ ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course</label>
                        <select class="form-select" name="course_name" required>
                            <option value="" disabled selected>Choose...</option>
                            <?php /* PHP loop here */ ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" placeholder="Enter full subject name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" placeholder="e.g. CS-101" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" name="year" placeholder="e.g. 2026" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semester</label>
                        <input type="number" class="form-control" name="semester" placeholder="e.g. 1" required>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" name="insert_subject" class="btn btn-primary btn-submit px-5">Add Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
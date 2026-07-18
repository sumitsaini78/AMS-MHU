<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Year & Semester Selector</title>
    
    <!-- Bootstrap for basic styling (Optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 500px;">
        <h4 class="mb-4 text-primary">Select Year & Semester</h4>

        <div class="mb-3">
            <label for="year" class="form-label fw-bold">Select Year:</label>
            <select id="year" name="year" class="form-select">
                <option value="">-- Choose Year --</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="semester" class="form-label fw-bold">Select Semester:</label>
            <!-- Default disable rakha hai jab tak year select na ho -->
            <select id="semester" name="semester" class="form-select" disabled>
                <option value="">-- Choose Semester --</option>
            </select>
        </div>
    </div>

    <!-- JavaScript / AJAX Logic -->
    <script>
        document.getElementById('year').addEventListener('change', function() {
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

</body>
</html>
<select class="form-select" name="subject_name" id="subject_name" required>
    <option selected disabled value="">Select Subject</option>
    <?php
    $query = "SELECT subject_name, subject_code FROM `subjected_teacher` WHERE teacher_id = '$id'";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $val = $row['subject_name'];
        $code = $row['subject_code'];
        echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . " (" . htmlspecialchars($code) . ")</option>";
    }
    ?>
</select>

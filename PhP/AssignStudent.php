<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$assessors = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'Assessor'");
$students = $conn->query("SELECT student_id, student_name FROM students");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $s_id = $_POST['student_id'];
    $a_id = $_POST['assessor_id'];

    $stmt = $conn->prepare("UPDATE students SET assessor_id = ? WHERE student_id = ?");
    $stmt->bind_param("is", $a_id, $s_id);

    if ($stmt->execute()) {
        $msg = "Success! Student assigned to assessor.";
    } else {
        $error = "Error: " . $conn->error;
    }
}
include 'layout.php';
?>
<div class="container">
    <h2>Assign Assessor to Student</h2>
    
    <?php if(isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>

    <form method="POST" class="admin-form">
        <div class="form-group">
            <label>Select Student:</label>
            <select name="student_id" required>
                <?php while($s = $students->fetch_assoc()): ?>
                    <option value="<?php echo $s['student_id']; ?>"><?php echo $s['student_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Select Assessor:</label>
            <select name="assessor_id" required>
                <?php while($a = $assessors->fetch_assoc()): ?>
                    <option value="<?php echo $a['user_id']; ?>"><?php echo $a['full_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn-add">Confirm Assignment</button>
        <a href="AdminStudents.php" class="btn-back">Back</a>
    </form>
</div>
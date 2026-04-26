<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$students = $conn->query("SELECT student_id, student_name FROM students");
$assessors = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'Assessor'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sid = $_POST['student_id'];
    $aid = $_POST['assessor_id'];
    $company = $_POST['company_name'];

    $stmt = $conn->prepare("INSERT INTO internships (student_id, assessor_id, company_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $sid, $aid, $company);

    if ($stmt->execute()) {
        header("Location: AdminStudents.php?msg=assigned");
        exit();
    }
}

include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Assign Internship</h2>
        <a href="AdminStudents.php" class="btn-back">← Back</a>
    </div>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Select Student:</label>
                <select name="student_id" required>
                    <option value="">-- Choose a Student --</option>
                    <?php while($s = $students->fetch_assoc()): ?>
                        <option value="<?php echo $s['student_id']; ?>">
                            <?php echo $s['student_name']; ?> (<?php echo $s['student_id']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Company Name:</label>
                <input type="text" name="company_name" placeholder="e.g. Google, Intel, etc." required>
            </div>

            <div class="form-group">
                <label>Assign Assessor:</label>
                <select name="assessor_id" required>
                    <option value="">-- Choose an Assessor --</option>
                    <?php while($a = $assessors->fetch_assoc()): ?>
                        <option value="<?php echo $a['user_id']; ?>">
                            <?php echo $a['full_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">Confirm Assignment</button>
            </div>
        </form>
    </div>
</div>
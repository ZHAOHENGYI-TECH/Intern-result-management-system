<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$students = $conn->query("SELECT student_id, CONCAT(first_name, ' ', last_name) AS student_name FROM student");
$assessors = $conn->query("SELECT user_id, username FROM user WHERE role = 'Assessor'");
$companies = $conn->query("SELECT company_id, name FROM company");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sid = $_POST['student_id'];
    $aid = $_POST['assessor_id'];
    $cid = $_POST['company_id'];
    $length = $_POST['length'] ?? 12;

    $stmt = $conn->prepare("INSERT INTO internship (student_id, user_id, company_id, length) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $sid, $aid, $cid, $length);

    if ($stmt->execute()) {
        header("Location: AdminStudents.php?msg=assigned");
        exit();
    } else {
        $error = "Error: " . $conn->error;
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
                            <?php echo htmlspecialchars($s['student_name']); ?> (<?php echo $s['student_id']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Company:</label>
                <select name="company_id" required>
                    <option value="">-- Choose a Company --</option>
                    <?php while($c = $companies->fetch_assoc()): ?>
                        <option value="<?php echo $c['company_id']; ?>">
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Assessor:</label>
                <select name="assessor_id" required>
                    <option value="">-- Choose an Assessor --</option>
                    <?php while($a = $assessors->fetch_assoc()): ?>
                        <option value="<?php echo $a['user_id']; ?>">
                            <?php echo htmlspecialchars($a['username']); ?>
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
</body>
</html>
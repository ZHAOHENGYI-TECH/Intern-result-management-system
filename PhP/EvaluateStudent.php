<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Assessor');

$intern_id = $_GET['intern_id'] ?? '';

$sql = "SELECT s.student_name, i.company_name, i.grade, i.feedback 
        FROM internships i 
        JOIN students s ON i.student_id = s.student_id 
        WHERE i.internship_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    $update_sql = "UPDATE internships SET grade = ?, feedback = ? WHERE internship_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("isi", $grade, $feedback, $intern_id);

    if ($update_stmt->execute()) {
        header("Location: AssessorDashboard.php?status=completed");
        exit();
    }
}

include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Student Evaluation</h2>
        <a href="AssessorDashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>

    <div class="form-card">
        <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 5px;">
            <p><strong>Student:</strong> <?php echo htmlspecialchars($details['student_name']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($details['company_name']); ?></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Internship Grade (0-100):</label>
                <input type="number" name="grade" min="0" max="100" 
                       value="<?php echo htmlspecialchars($details['grade'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Feedback / Comments:</label>
                <textarea name="feedback" rows="5" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #CBD5E0;" 
                          required><?php echo htmlspecialchars($details['feedback'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">Submit Assessment</button>
            </div>
        </form>
    </div>
</div>
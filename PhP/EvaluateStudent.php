<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Assessor');

$intern_id = $_GET['intern_id'] ?? '';

$sql = "SELECT s.student_name, i.company_name, i.feedback,
               i.task_mark, i.safety_mark, i.theory_mark, i.report_presentation_mark, 
               i.language_mark, i.lifelong_learning_mark, i.project_mgmt_mark, i.time_mgmt_mark
        FROM internships i 
        JOIN students s ON i.student_id = s.student_id 
        WHERE i.internship_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = $_POST['task_mark'];
    $safety = $_POST['safety_mark'];
    $theory = $_POST['theory_mark'];
    $report = $_POST['report_presentation_mark'];
    $lang = $_POST['language_mark'];
    $learning = $_POST['lifelong_learning_mark'];
    $pm = $_POST['project_mgmt_mark'];
    $tm = $_POST['time_mgmt_mark'];
    $feedback = $_POST['feedback'];

    $final_score = ($task * 0.10) + 
                   ($safety * 0.10) + 
                   ($theory * 0.10) + 
                   ($report * 0.15) + 
                   ($lang * 0.10) + 
                   ($learning * 0.15) + 
                   ($pm * 0.15) + 
                   ($tm * 0.15);

    $update_sql = "UPDATE internships SET 
                    task_mark = ?, safety_mark = ?, theory_mark = ?, 
                    report_presentation_mark = ?, language_mark = ?, 
                    lifelong_learning_mark = ?, project_mgmt_mark = ?, 
                    time_mgmt_mark = ?, final_score = ?, feedback = ? 
                   WHERE internship_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iiiiiiiidsi", 
        $task, $safety, $theory, $report, $lang, $learning, $pm, $tm, $final_score, $feedback, $intern_id);

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
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Tasks/Projects (10%)</label>
                    <input type="number" name="task_mark" min="0" max="100" value="<?php echo $details['task_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Health & Safety (10%)</label>
                    <input type="number" name="safety_mark" min="0" max="100" value="<?php echo $details['safety_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Theoretical Knowledge (10%)</label>
                    <input type="number" name="theory_mark" min="0" max="100" value="<?php echo $details['theory_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Report Presentation (15%)</label>
                    <input type="number" name="report_presentation_mark" min="0" max="100" value="<?php echo $details['report_presentation_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Language & Illustration (10%)</label>
                    <input type="number" name="language_mark" min="0" max="100" value="<?php echo $details['language_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Lifelong Learning (15%)</label>
                    <input type="number" name="lifelong_learning_mark" min="0" max="100" value="<?php echo $details['lifelong_learning_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Project Management (15%)</label>
                    <input type="number" name="project_mgmt_mark" min="0" max="100" value="<?php echo $details['project_mgmt_mark'] ?? 0; ?>" required>
                </div>
                <div class="form-group">
                    <label>Time Management (15%)</label>
                    <input type="number" name="time_mgmt_mark" min="0" max="100" value="<?php echo $details['time_mgmt_mark'] ?? 0; ?>" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label>Feedback / Comments:</label>
                <textarea name="feedback" rows="4" style="width: 100%;" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">Submit Assessment</button>
            </div>
        </form>

    </div>
</div>
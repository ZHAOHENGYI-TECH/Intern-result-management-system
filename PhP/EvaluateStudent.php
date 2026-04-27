<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Assessor');

$intern_id = $_GET['intern_id'] ?? '';

$sql = "SELECT 
            CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
            c.name AS company_name 
        FROM internship i 
        JOIN student s ON i.student_id = s.student_id 
        JOIN company c ON i.company_id = c.company_id
        WHERE i.internship_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();

$grade_sql = "SELECT * FROM assessment WHERE internship_id = ?";
$grade_stmt = $conn->prepare($grade_sql);
$grade_stmt->bind_param("i", $intern_id);
$grade_stmt->execute();
$existing_grade = $grade_stmt->get_result()->fetch_assoc();

if (!$details) {
    header("Location: AssessorDashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tp = $_POST['tp_mark'];
    $hs = $_POST['hs_mark'];
    $ctk = $_POST['ctk_mark'];
    $pr = $_POST['pr_mark'];
    $cli = $_POST['cli_mark'];
    $lla = $_POST['lla_mark'];
    $pm = $_POST['pm_mark'];
    $tm = $_POST['tm_mark'];
    $feedback = $_POST['feedback'];

    $total_score = ($tp * 0.10) + ($hs * 0.10) + ($ctk * 0.10) + 
                   ($pr * 0.15) + ($cli * 0.10) + ($lla * 0.15) + 
                   ($pm * 0.15) + ($tm * 0.15);

    $check_sql = "SELECT assessment_id FROM assessment WHERE internship_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $intern_id);
    $check_stmt->execute();
    $exists = $check_stmt->get_result()->fetch_assoc();

    if ($exists) {
        $save_sql = "UPDATE assessment SET 
                        TP_mark = ?, HS_mark = ?, CTK_mark = ?, PR_mark = ?, 
                        CLI_mark = ?, LLA_mark = ?, PM_mark = ?, TM_mark = ?, 
                        total_score = ?, feedback=?
                     WHERE internship_id = ?";
        $save_stmt = $conn->prepare($save_sql);
        $save_stmt->bind_param("iiiiiiiidsi", $tp, $hs, $ctk, $pr, $cli, $lla, $pm, $tm, $total_score, $feedback, $intern_id);
    } else {
        $save_sql = "INSERT INTO assessment (internship_id, TP_mark, HS_mark, CTK_mark, PR_mark, CLI_mark, LLA_mark, PM_mark, TM_mark, total_score, feedback) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $save_stmt = $conn->prepare($save_sql);
        $save_stmt->bind_param("iiiiiiiiids", $intern_id, $tp, $hs, $ctk, $pr, $cli, $lla, $pm, $tm, $total_score, $feedback);
    }

    if ($save_stmt->execute()) {
        header("Location: AssessorDashboard.php?msg=evaluated");
        exit();
    } else {
        $error = "Error saving evaluation: " . $conn->error;
    }
}

include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Evaluate: <?php echo htmlspecialchars($details['student_name']); ?></h2>
        <a href="AssessorDashboard.php" class="btn-back">← Cancel</a>
    </div>

    <div class="form-card">
        <p style="margin-bottom: 20px; color: #718096;"><strong>Company:</strong> <?php echo htmlspecialchars($details['company_name']); ?></p>
        
        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Undertaking Tasks/Projects (10%)</label>
                    <input type="number" name="tp_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['TP_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Health and Safety (10%)</label>
                    <input type="number" name="hs_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['HS_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Theoretical Knowledge (10%)</label>
                    <input type="number" name="ctk_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['CTK_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Report Presentation (15%)</label>
                    <input type="number" name="pr_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['PR_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Language & Illustration (10%)</label>
                    <input type="number" name="cli_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['CLI_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Lifelong Learning (15%)</label>
                    <input type="number" name="lla_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['LLA_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Project Management (15%)</label>
                    <input type="number" name="pm_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['PM_mark'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>Time Management (15%)</label>
                    <input type="number" name="tm_mark" min="0" max="100" required placeholder="0-100" value="<?php echo $existing_grade['TM_mark'] ?? ''; ?>">
                </div>
                <div class="form-group" style="grid-column: span 2; margin-top: 10px;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Qualitative Comments / Feedback</label>
                    <textarea name="feedback" class="feedback-area" required><?php echo htmlspecialchars($existing_grade['feedback'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-action" style="margin-top: 30px;">
                <button type="submit" class="btn-add">Submit Final Evaluation</button>
            </div>
        </form>
    </div>
</div>
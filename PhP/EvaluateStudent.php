<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Assessor');

$intern_id = $_GET['intern_id'] ?? '';

$sql = "SELECT s.first_name, s.last_name, c.name AS company_name 
        FROM internship i 
        JOIN student s ON i.student_id = s.student_id 
        JOIN company c ON i.company_id = c.company_id
        WHERE i.internship_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $intern_id);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tp = $_POST['tp_mark'];   // Undertaking Tasks/Projects
    $hs = $_POST['hs_mark'];   // Health and Safety
    $ctk = $_POST['ctk_mark']; // Theoretical Knowledge
    $pr = $_POST['pr_mark'];   // Presentation of Report
    $cli = $_POST['cli_mark']; // Language and Illustration
    $lla = $_POST['lla_mark']; // Lifelong Learning
    $pm = $_POST['pm_mark'];   // Project Management
    $tm = $_POST['tm_mark'];   // Time Management

    $total_score = ($tp * 0.10) + ($hs * 0.10) + ($ctk * 0.10) + 
                   ($pr * 0.15) + ($cli * 0.10) + ($lla * 0.15) + 
                   ($pm * 0.15) + ($tm * 0.15);

    $check_sql = "SELECT assessment_id FROM assessment WHERE internship_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $intern_id);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();

    if ($existing) {
        $query = "UPDATE assessment SET total_score = ?, TP_mark = ?, HS_mark = ?, CTK_mark = ?, 
                  PR_mark = ?, CLI_mark = ?, LLA_mark = ?, PM_mark = ?, TM_mark = ? 
                  WHERE internship_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("dddddddddi", $total_score, $tp, $hs, $ctk, $pr, $cli, $lla, $pm, $tm, $intern_id);
    } else {
        $query = "INSERT INTO assessment (internship_id, total_score, TP_mark, HS_mark, CTK_mark, 
                  PR_mark, CLI_mark, LLA_mark, PM_mark, TM_mark) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iddddddddd", $intern_id, $total_score, $tp, $hs, $ctk, $pr, $cli, $lla, $pm, $tm);
    }

    if ($stmt->execute()) {
        header("Location: AssessorDashboard.php?success=1");
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

        <form method="POST" action="EvaluateStudent.php?intern_id=<?php echo $intern_id; ?>">
            <div class="evaluation-grid">
                <div class="form-group">
                    <label>Undertaking Tasks/Projects (10%)</label>
                    <input type="number" name="tp_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Health and Safety Requirements (10%)</label>
                    <input type="number" name="hs_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Connectivity and Use of Theoretical Knowledge (10%)</label>
                    <input type="number" name="ctk_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Presentation of the Report as a Written Document (15%)</label>
                    <input type="number" name="pr_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Clarity of Language and Illustration (10%)</label>
                    <input type="number" name="cli_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Lifelong Learning Activities (15%)</label>
                    <input type="number" name="lla_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Project Management (15%)</label>
                    <input type="number" name="pm_mark" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label>Time Management (15%)</label>
                    <input type="number" name="tm_mark" min="0" max="100" required>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-add">Submit Final Evaluation</button>
            </div>
        </form>

    </div>
</div>
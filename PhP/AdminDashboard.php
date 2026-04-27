<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$student_count = $conn->query("SELECT COUNT(*) FROM student")->fetch_row()[0];
$assessor_count = $conn->query("SELECT COUNT(*) FROM user WHERE role='Assessor'")->fetch_row()[0];
$graded_count = $conn->query("SELECT COUNT(*) FROM assessment")->fetch_row()[0];

include 'layout.php';
?>
    <div class="container">
        <h1>Admin Overview</h1>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #4A90E2; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; color: #718096;">Total Students</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0;"><?php echo $student_count; ?></p>
                <a href="AdminStudents.php" style="color: #4A90E2; text-decoration: none; font-size: 0.9rem;">Manage Students →</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #48BB78; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; color: #718096;">Active Assessors</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0;"><?php echo $assessor_count; ?></p>
                <a href="AdminAssessors.php" style="color: #48BB78; text-decoration: none; font-size: 0.9rem;">Manage Assessors →</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #ED8936; shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0; color: #718096;">Assessments Completed</h3>
                <p style="font-size: 2rem; font-weight: bold; margin: 10px 0;"><?php echo $graded_count; ?></p>
                <a href="FinalReport.php" style="color: #ED8936; text-decoration: none; font-size: 0.9rem;">View Final Report →</a>
            </div>
        </div>
    </div>
</body>
</html>

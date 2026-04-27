<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="top_nav">
    <?php if (isset($_SESSION['role'])): ?>
        
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="AdminDashboard.php">Dashboard</a>
            <a href="AdminStudents.php">Manage Students</a>
            <a href="AdminAssessors.php">Manage Assessors</a>
            <a href="FinalReport.php">Final Report</a>
            
        <?php elseif ($_SESSION['role'] === 'Assessor'): ?>
            <a href="AssessorDashboard.php">Dashboard</a>
        <?php endif; ?>

        <a href="logout.php">Logout</a>

    <?php endif; ?>
</header>
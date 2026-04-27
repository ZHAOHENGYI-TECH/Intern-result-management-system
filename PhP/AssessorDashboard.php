<?php
require_once 'AuthCheck.php';
checkRole('Assessor'); 

require_once 'Database.php';

$current_assessor_id = $_SESSION['user_id'];

$sql = "SELECT s.student_id, s.first_name, s.last_name, p.name AS pName, c.name AS cName, i.internship_id 
        FROM Student s
        LEFT JOIN Programme p ON s.programme_id = p.programme_id
        LEFT JOIN Internship i ON s.student_id = i.student_id
        LEFT JOIN Company c ON i.company_id = c.company_id 
        WHERE i.user_id = '$current_assessor_id'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluator Workbench - Internship Management System</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h2>welcome back, <?php echo $_SESSION['username']; ?> (Assessor)</h2>
            <a href="logout.php" class="btn-logout">log out</a>
        </header>

        <h3>The list of students I am responsible for evaluating</h3>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>student ID</th>
                    <th>first name</th>
                    <th>last name</th>
                    <th>major</th>
                    <th>internship company</th>
                    <th>opearation</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['pName']; ?></td>
                            <td><?php echo $row['cName']; ?></td>
                            <td>
                                <a href="EvaluateStudent.php?intern_id=<?php echo $row['internship_id']; ?>" class="btn-assess">Start rating</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">There are no students assigned to you currently.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

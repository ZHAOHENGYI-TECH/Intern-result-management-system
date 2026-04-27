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

// Bring in the master CSS and navigation layout!
include 'layout.php';
?>

    <div class="container">
        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?> (Assessor)</h2>
        
        <div class="table-controls">
            <div class="search-box">
                <span style="font-weight: bold; color: #555;">My Assigned Students</span>
            </div>
            <div class="action-buttons">
                <a href="logout.php" class="btn-logout">Log out</a>
            </div>
        </div>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Major</th>
                        <th>Internship Company</th>
                        <th>Operation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pName']); ?></td>
                                <td><?php echo htmlspecialchars($row['cName']); ?></td>
                                <td>
                                    <a href="EvaluateStudent.php?intern_id=<?php echo htmlspecialchars($row['internship_id']); ?>" class="btn-assess">Start rating</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">There are no students assigned to you currently.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
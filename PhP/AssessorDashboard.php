<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Assessor'); 

    $current_assessor_id = $_SESSION['user_id'];

    $sql = "SELECT s.student_id, s.student_name, s.programme, i.company_name, i.internship_id 
            FROM internships i 
            JOIN students s ON i.student_id = s.student_id 
            WHERE i.assessor_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_assessor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    include 'layout.php';
?>
    <div class="container">
        <header>
            <h2>Welcome Back, <?php echo $_SESSION['username']; ?> (Assessor)</h2>
            <a href="logout.php" class="btn-logout">log out</a>
        </header>

        <h3>The list of students I am responsible for evaluating</h3>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student name</th>
                    <th>Programme</th>
                    <th>Company</th>
                    <th>Opearation</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['programme']; ?></td>
                            <td><?php echo $row['company_name']; ?></td>
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
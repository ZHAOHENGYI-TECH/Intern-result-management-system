<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Assessor'); 

    $current_assessor_id = $_SESSION['user_id'];
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (!empty($search)) {
        $sql = "SELECT 
                    s.student_id, 
                    CONCAT(s.first_name, ' ', s.last_name) AS student_full_name, 
                    p.name AS programme_name, 
                    c.name AS company_name, 
                    i.internship_id,
                    a.assessment_id,
                    a.total_score
                FROM internship i 
                JOIN student s ON i.student_id = s.student_id 
                LEFT JOIN programme p ON s.programme_id = p.programme_id
                LEFT JOIN company c ON i.company_id = c.company_id
                LEFT JOIN assessment a ON i.internship_id = a.internship_id
                WHERE i.user_id = ? 
                AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_id LIKE ?)";

        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->bind_param("isss", $current_assessor_id, $searchTerm, $searchTerm, $searchTerm);
    }
    else {
        $sql = "SELECT 
                    s.student_id, 
                    CONCAT(s.first_name, ' ', s.last_name) AS student_full_name, 
                    p.name AS programme_name, 
                    c.name AS company_name, 
                    i.internship_id,
                    a.assessment_id,
                    a.total_score
                FROM internship i 
                JOIN student s ON i.student_id = s.student_id 
                LEFT JOIN programme p ON s.programme_id = p.programme_id
                LEFT JOIN company c ON i.company_id = c.company_id
                LEFT JOIN assessment a ON i.internship_id = a.internship_id
                WHERE i.user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $current_assessor_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    include 'layout.php';
?>
<div class="container">
    <div class="dashboard-header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    </div>

    <div style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 15px;">
        <h3 style="color: #4A5568; margin: 0;">Students Assigned for Evaluation</h3>
        <span class="badge" style="background: #E2E8F0; padding: 4px 12px; border-radius: 4px; font-size: 0.85rem; color: #4A5568; font-weight: 600;">
            Total: <?php echo $result->num_rows; ?>
        </span>
    </div>
    <div class="search-box" style="margin-bottom: 20px;">
        <form method="GET" action="AssessorDashboard.php">
            <input type="text" name="search" placeholder="Search student by ID or Name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if (!empty($_GET['search'])): ?>
                <a href="AssessorDashboard.php" class="btn-clear">✕ Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Programme</th>
                    <th>Company</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['programme_name'] ?? 'N/A'); ?></td>
                            
                            <td>
                                <?php if ($row['assessment_id']): ?>
                                    <strong style="color: #2F855A;"><?php echo number_format($row['total_score'], 1); ?>%</strong>
                                <?php else: ?>
                                    <i style="color: #A0AEC0;">Not Graded</i>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['assessment_id']): ?>
                                    <a href="EvaluateStudent.php?intern_id=<?php echo $row['internship_id']; ?>" class="btn-edit" style="background-color: #4A5568;">View / Edit</a>
                                <?php else: ?>
                                    <a href="EvaluateStudent.php?intern_id=<?php echo $row['internship_id']; ?>" class="btn-edit">Start Rating</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
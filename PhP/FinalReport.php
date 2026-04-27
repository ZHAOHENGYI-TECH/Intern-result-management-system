<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
include 'layout.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "";

if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $where_clause = " WHERE (
                        s.student_name LIKE '%$s%'
                        OR s.student_id LIKE '%$s%'
                        OR u_assessor.full_name LIKE '%$s%'
                        OR u_assessor.username LIKE '%$s%'
                        OR i.company_name LIKE '%$s%'
                    ) ";
}

$sql = "SELECT 
            i.internship_id, s.student_id, s.student_name, i.company_name, 
            u_assessor.user_id AS assessor_id, u_assessor.full_name AS assessor_name, 
            u_assessor.username AS assessor_username, i.final_score, 
            i.task_mark, i.safety_mark, i.theory_mark, i.report_presentation_mark, 
            i.language_mark, i.lifelong_learning_mark, i.project_mgmt_mark, i.time_mgmt_mark,
            i.feedback
        FROM internships i
        JOIN students s ON i.student_id = s.student_id
        LEFT JOIN users u_assessor ON i.assessor_id = u_assessor.user_id
        $where_clause
        ORDER BY i.final_score DESC, s.student_name ASC";

$result = $conn->query($sql);
?>

<div class="container">
    <h2>Final Internship Results</h2>
    
    <div class="table-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div class="search-box">
            <form method="GET" action="FinalReport.php" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" placeholder="Search by ID, Name, or Company..." 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 280px;">
                <button type="submit" class="btn-search">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="FinalReport.php" class="btn-clear">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="action-buttons">
            <button onclick="window.print()" class="btn-print" style="background-color: #2D3748; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Print Report</button>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Company</th>
                    <th>Assessor</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['company_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['assessor_name'] ?? 'Unassigned'); ?></td>
                            <td><strong><?php echo ($row['final_score'] > 0) ? number_format($row['final_score'], 2) . '%' : '0.00%'; ?></strong></td>
                            <td>
                                <?php 
                                    if ($row['final_score'] == 0) echo '<span style="color: #718096;">Pending</span>';
                                    elseif ($row['final_score'] >= 40) echo '<span style="color: #38A169; font-weight: bold;">PASS</span>';
                                    else echo '<span style="color: #E53E3E; font-weight: bold;">FAIL</span>';
                                ?>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="toggleDetails('details-<?php echo $row['student_id']; ?>')">View Marks</button>
                            </td>
                        </tr>
                        
                        <tr id="details-<?php echo $row['student_id']; ?>" style="display: none; background-color: #F7FAFC;">
                            <td colspan="7" style="padding: 20px;">
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; font-size: 13px;">
                                    <div><strong>Tasks (10%):</strong> <?php echo $row['task_mark']; ?></div>
                                    <div><strong>Safety (10%):</strong> <?php echo $row['safety_mark']; ?></div>
                                    <div><strong>Theory (10%):</strong> <?php echo $row['theory_mark']; ?></div>
                                    <div><strong>Report (15%):</strong> <?php echo $row['report_presentation_mark']; ?></div>
                                    <div><strong>Language (10%):</strong> <?php echo $row['language_mark']; ?></div>
                                    <div><strong>Learning (15%):</strong> <?php echo $row['lifelong_learning_mark']; ?></div>
                                    <div><strong>Proj Mgmt (15%):</strong> <?php echo $row['project_mgmt_mark']; ?></div>
                                    <div><strong>Time Mgmt (15%):</strong> <?php echo $row['time_mgmt_mark']; ?></div>
                                </div>
                                <?php if(!empty($row['feedback'])): ?>
                                    <div style="margin-top: 10px; border-top: 1px solid #E2E8F0; padding-top: 10px;">
                                        <strong>Assessor Feedback:</strong> <?php echo htmlspecialchars($row['feedback']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No internship data matches your search.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleDetails(id) {
    var x = document.getElementById(id);
    if (x.style.display === "none") {
        x.style.display = "table-row";
    } else {
        x.style.display = "none";
    }
}
</script>
</body> 
</html>
<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
include 'layout.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "";

if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $where_clause = " WHERE (
                        s.first_name LIKE '%$s%'
                        OR s.last_name LIKE '%$s%'
                        OR s.student_id LIKE '%$s%'
                        OR u.username LIKE '%$s%'
                        OR c.name LIKE '%$s%'
                    ) ";
}

$sql = "SELECT 
            s.student_id, 
            CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
            c.name AS company_name, 
            u.username AS assessor_name, 
            a.total_score AS final_score,
            a.TP_mark, a.HS_mark, a.CTK_mark, a.PR_mark, 
            a.CLI_mark, a.LLA_mark, a.PM_mark, a.TM_mark
        FROM internship i
        JOIN student s ON i.student_id = s.student_id
        LEFT JOIN company c ON i.company_id = c.company_id
        LEFT JOIN user u ON i.user_id = u.user_id
        LEFT JOIN assessment a ON i.internship_id = a.internship_id
        $where_clause
        ORDER BY a.total_score DESC";

$result = $conn->query($sql);
?>

<div class="container">
    <h2>Final Internship Report</h2>
    
    <div class="table-controls" style="margin-bottom: 20px;">
        <div class="search-box">
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Student or Company...">
                <button type="submit" class="btn-search">Search</button>
                <?php if(!empty($search)): ?>
                    <a href="FinalReport.php" class="btn-clear">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Company</th>
                    <th>Assessor</th>
                    <th>Final Score</th>
                    <th>Grade</th>
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
                            <td><strong><?php echo $row['final_score'] !== null ? number_format($row['final_score'], 1) . "%" : "Pending"; ?></strong></td>
                            <td>
                                <?php 
                                    if($row['final_score'] === null) echo "-";
                                    else if($row['final_score'] >= 70) echo "<span style='color: #2D3748; font-weight:bold;'>Distinction</span>";
                                    else if($row['final_score'] >= 40) echo "Pass";
                                    else echo "<span style='color: #E53E3E;'>Fail</span>";
                                ?>
                            </td>
                            <td>
                                <button onclick="toggleDetails('details_<?php echo $row['student_id']; ?>')" class="btn-edit">View Marks</button>
                            </td>
                        </tr>
                        <tr id="details_<?php echo $row['student_id']; ?>" style="display: none; background-color: #F7FAFC;">
                            <td colspan="7">
                                <div style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div><strong>Task/Problem (10%):</strong> <?php echo $row['TP_mark'] ?? 0; ?></div>
                                    <div><strong>Health & Safety (10%):</strong> <?php echo $row['HS_mark'] ?? 0; ?></div>
                                    <div><strong>Theory/Knowledge (10%):</strong> <?php echo $row['CTK_mark'] ?? 0; ?></div>
                                    <div><strong>Presentation (15%):</strong> <?php echo $row['PR_mark'] ?? 0; ?></div>
                                    <div><strong>Language (10%):</strong> <?php echo $row['CLI_mark'] ?? 0; ?></div>
                                    <div><strong>Learning (15%):</strong> <?php echo $row['LLA_mark'] ?? 0; ?></div>
                                    <div><strong>Proj Mgmt (15%):</strong> <?php echo $row['PM_mark'] ?? 0; ?></div>
                                    <div><strong>Time Mgmt (15%):</strong> <?php echo $row['TM_mark'] ?? 0; ?></div>
                                </div>
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
    if (x.style.display === "none" || x.style.display === "") {
        x.style.display = "table-row";
    } else {
        x.style.display = "none";
    }
}
</script>
</body> 
</html>
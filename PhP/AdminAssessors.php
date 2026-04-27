<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
include 'layout.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT 
            u.user_id, 
            u.username,
            COUNT(i.internship_id) AS student_count,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS company_name
        FROM user u
        LEFT JOIN internship i ON u.user_id = i.user_id
        LEFT JOIN company c ON i.company_id = c.company_id
        WHERE u.role = 'Assessor'";

if (!empty($search)) {
    $sql .= " AND (u.username LIKE ? OR u.user_id LIKE ?)";
    $sql .= " GROUP BY u.user_id";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " GROUP BY u.user_id";
    $result = $conn->query($sql);
}
?>

<div class="container">
    <h2>Assessor Management</h2>
    
    <div class="table-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div class="search-box">
            <form method="GET" action="AdminAssessors.php" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" placeholder="Search Assessor by ID or Name..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 250px;">
                <button type="submit" class="btn-search">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="AdminAssessors.php" class="btn-clear">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="action-buttons">
            <a href="AddAssessor.php" class="btn-add" style="background-color: #48BB78; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">+ Add Assessor</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Students Assigned</th>
                    <th>Company</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_count']); ?></td>
                            <td>
                                <?php 
                                    $company = $row['company_name'] ?? '';
                                    echo !empty($company) ? htmlspecialchars($company) : '<i style="color: #A0AEC0;">No Company</i>'; 
                                ?>
                            </td>
                            <td class="operations-cell">
                                <a href="EditAssessor.php?id=<?php echo $row['user_id']; ?>" class="btn-edit">Edit</a>
                                <a href="DeleteAssessor.php?id=<?php echo $row['user_id']; ?>" class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this assessor?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No Assessor found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

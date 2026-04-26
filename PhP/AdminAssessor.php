<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$sql = "SELECT user_id, username, full_name FROM users WHERE role = 'Assessor'";
$result = $conn->query($sql);

    include 'layout.php';
?>
    <div class="container">
        <header>
            <h2>Assessor Management</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
        </header>

        <div class="action-bar">
            <a href="add_assessor.php" class="btn btn-primary">+ Add New Assessor</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Operations</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td>
                                <a href="edit_assessor.php?id=<?php echo $row['user_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <a href="delete_assessor.php?id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this assessor?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No assessors found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

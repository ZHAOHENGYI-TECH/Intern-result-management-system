<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

$id = $_GET['id'] ?? '';

$sql = "SELECT user_id, username, full_name FROM users WHERE user_id = ? AND role = 'Assessor'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$assessor = $stmt->get_result()->fetch_assoc();

if (!$assessor) {
    header("Location: AdminAssessors.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['full_name'];
    $username = $_POST['username'];
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET full_name = ?, username = ?, password = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $fullname, $username, $hashed_password, $id);
    } 
    else {
        $update_sql = "UPDATE users SET full_name = ?, username = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $fullname, $username, $id);
    }

    if ($update_stmt->execute()) {
        header("Location: AdminAssessors.php?msg=updated");
        exit();
    }
}

include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Edit Assessor Profile</h2>
        <a href="AdminAssessors.php" class="btn-back">← Cancel Changes</a>
    </div>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($assessor['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Username (ID):</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($assessor['username']); ?>" required>
            </div>

            <div class="form-group">
                <label>New Password (Leave blank to keep current):</label>
                <input type="password" name="password" placeholder="Enter new password only if changing">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">Update Assessor</button>
            </div>
        </form>
    </div>
</div>
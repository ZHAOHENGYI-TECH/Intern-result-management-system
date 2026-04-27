<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'Assessor';

    $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        header("Location: AdminAssessors.php?msg=added");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}

include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Add New Assessor</h2>
        <a href="AdminAssessors.php" class="btn-back">← Back</a>
    </div>

    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Username (Login ID):</label>
                <input type="text" name="username" placeholder="e.g. jdoe_assessor" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter temporary password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-add">Register Assessor</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('assessorForm').addEventListener('submit', function(event) {
    const username = document.querySelector('input[name="username"]').value;
    const password = document.querySelector('input[name="password"]').value;

    const usernameRegex = /^[a-zA-Z0-9_]+$/;
    if (!usernameRegex.test(username)) {
        alert('warning：Username can only contain letters, numbers, and underscores');
        event.preventDefault(); 
        return;
    }

    if (password.length < 6) {
        alert('Warning: For safety, the password must be at least 6 characters long!');
        event.preventDefault(); 
        return;
    }
});
</script>
</body>
</html>

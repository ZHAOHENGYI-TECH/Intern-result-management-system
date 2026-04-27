<?php
session_start(); 
require_once 'Database.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        
            if ($user['role'] == 'Admin') {
                header("Location: AdminStudents.php");
            } 
            else {
                header("Location: AssessorDashboard.php");
            }
            exit();
        } 
        else {
            $error = "Invalid password or username.";
        }
    }
    elseif ($result->num_rows > 1) {
        $error = "Error: Multiple accounts found with this username. Please clean your database.";
    } 
    else {
        $error = "Username not found.";
    }
}
include 'layout.php';
?>
        <div class="login-container">
            <h2>System Login</h2>
            
            <?php if(isset($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Please enter your username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Please enter your password" required>
                </div>
                <button type="submit">login</button>
            </form>
        </div>
    </body>
</html>

<?php
require_once 'AuthCheck.php';
checkRole('Admin');

include 'layout.php';
?>
    <h1>Welcome to the Admin</h1>
    <p><a href="admin_students.php">Students Management</a></p>
    <p><a href="logout.php">log out</a></p>
</body>
</html>

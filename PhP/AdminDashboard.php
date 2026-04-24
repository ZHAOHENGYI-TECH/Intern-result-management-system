<?php
require_once 'AuthCheck.php';
checkRole('Admin');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <h1>Welcome to the Admin</h1>
    <p><a href="admin_students.php">Students Management</a></p>
    <p><a href="logout.php">log out</a></p>
</body>
</html>

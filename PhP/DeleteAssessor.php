<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $check_sql = "SELECT COUNT(*) as count FROM internship WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $count = $check_stmt->get_result()->fetch_assoc()['count'];

   if ($count > 0) {
        header("Location: AdminAssessors.php?error=has_students&count=$count");
        exit();
    }

    $delete_sql = "DELETE FROM user WHERE user_id = ? AND role = 'Assessor'";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $user_id);
    
    if ($delete_stmt->execute()) {
        header("Location: AdminAssessors.php?msg=Deleted successfully");
    } else {
        echo "Delete failure: " . $conn->error;
    }
} else {
    header("Location: AdminAssessors.php");
}
exit();
?>
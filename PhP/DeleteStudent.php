<?php
require_once 'AuthCheck.php';
require_once 'Database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header("Location: AdminStudents.php?msg=deleted");
    } 
    else {
        echo "Error: Could not delete student. They might be assigned to an internship.";
    }
} 
else {
    header("Location: AdminStudents.php");
}
exit();
?>
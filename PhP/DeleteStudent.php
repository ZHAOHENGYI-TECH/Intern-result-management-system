<?php
require_once 'AuthCheck.php';
require_once 'Database.php';
checkRole('Admin');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn->begin_transaction();

    try {
        $stmt1 = $conn->prepare("DELETE FROM internship WHERE student_id = ?");
        $stmt1->bind_param("s", $id);
        $stmt1->execute();

        $stmt2 = $conn->prepare("DELETE FROM student WHERE student_id = ?");
        $stmt2->bind_param("s", $id);
        $stmt2->execute();

        $conn->commit();
        header("Location: AdminStudents.php?msg=deleted");
        
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        echo "Error: Could not delete student. Technical details: " . $e->getMessage();
    }
} else {
    header("Location: AdminStudents.php");
}
exit();
?>
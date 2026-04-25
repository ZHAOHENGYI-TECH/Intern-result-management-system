<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Admin');

    $id = $_GET['id'];
    
    $sql = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sname = $_POST['student_name'];
        $prog = $_POST['programme'];

        $update_sql = "UPDATE students SET student_name = ?, programme = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $sname, $prog, $id);

        if ($update_stmt->execute()) {
            header("Location: AdminStudents.php");
            exit();
        }
    }

    include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Edit Student Profile</h2>
        <a href="AdminStudents.php" class="btn-back">← Cancel Changes</a>
    </div>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Student ID (Read Only):</label>
                <input type="text" value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>Student Full Name:</label>
                <input type="text" name="student_name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Programme:</label>
                <input type="text" name="programme" value="<?php echo htmlspecialchars($student['programme']); ?>" required>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-add">Save Changes</button>
            </div>
        </form>
    </div>
</div>
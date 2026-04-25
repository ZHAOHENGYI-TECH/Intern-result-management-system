<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Admin');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
       $sid = $_POST['student_id'];
        $sname = $_POST['student_name'];
        $prog = $_POST['programme'];

        $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, programme) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sid, $sname, $prog);

        if ($stmt->execute()) {
            header("Location: AdminStudents.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }

    include 'layout.php';
?>
    <div class="container">
        <div class="form-header">
            <h2>Add New Student</h2>
            <a href="AdminStudents.php" class="btn-back">← Back to the Student Records</a>
        </div>
        <div class="form-card">
            <form method="POST" class="register-student">
                <div class="form-group">
                    <label>Student ID:</label>
                    <input type="text" name="student_id" placeholder="Enter Student ID" required>
                </div>
                <div class="form-group">
                    <label>Student Full Name:</label>
                    <input type="text" name="student_name" placeholder="Enter Student Full Name" required>
                </div>
                <div class="form-group">
                    <label>Programme:</label>
                    <input type="text" name="programme" placeholder="Enter Programme Title" required>
                </div>
                <div class="form-action">
                    <button type="submit" class="btn-add">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

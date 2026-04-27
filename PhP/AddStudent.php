<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Admin');

    $programmes = $conn->query("SELECT * FROM programme");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sid = $_POST['student_id'];
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $prog_id = $_POST['programme_id'];

        $stmt = $conn->prepare("INSERT INTO student (student_id, first_name, last_name, programme_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $sid, $fname, $lname, $prog_id);

        if ($stmt->execute()) {
            header("Location: AdminStudents.php?msg=added");
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
            <a href="AdminStudents.php" class="btn-back">← Back</a>
        </div>
        
        <?php if(isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" class="register-student">
                <div class="form-group">
                    <label>Student ID:</label>
                    <input type="text" name="student_id" placeholder="e.g. 20456789" required>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>First Name:</label>
                        <input type="text" name="first_name" placeholder="Enter First Name" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" placeholder="Enter Last Name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Programme:</label>
                    <select name="programme_id" required style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
                        <option value="">-- Select Programme --</option>
                        <?php while($p = $programmes->fetch_assoc()): ?>
                            <option value="<?php echo $p['programme_id']; ?>">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-action" style="margin-top: 20px;">
                    <button type="submit" class="btn-add">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

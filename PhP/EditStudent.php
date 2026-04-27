<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Admin');

    $id = $_GET['id'] ?? '';
    
    $sql = "SELECT s.*, i.company_id, i.user_id AS assessor_id 
            FROM student s 
            LEFT JOIN internship i ON s.student_id = i.student_id 
            WHERE s.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    $programmes = $conn->query("SELECT * FROM programme");
    $companies = $conn->query("SELECT * FROM company");
    $assessors = $conn->query("SELECT user_id, username FROM user WHERE role = 'Assessor'");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $prog_id = $_POST['programme_id'];
        $comp_id = $_POST['company_id'];
        $assessor_id = $_POST['assessor_id'];

        $update_student = $conn->prepare("UPDATE student SET first_name = ?, last_name = ?, programme_id = ? WHERE student_id = ?");
        $update_student->bind_param("ssis", $fname, $lname, $prog_id, $id);
        $update_student->execute();

        $check_intern = $conn->prepare("SELECT internship_id FROM internship WHERE student_id = ?");
        $check_intern->bind_param("s", $id);
        $check_intern->execute();
        $res = $check_intern->get_result();

        if ($res->num_rows > 0) {
            $update_i = $conn->prepare("UPDATE internship SET company_id = ?, user_id = ? WHERE student_id = ?");
            $update_i->bind_param("iis", $comp_id, $assessor_id, $id);
            $update_i->execute();
        } else {
            $insert_i = $conn->prepare("INSERT INTO internship (student_id, company_id, user_id) VALUES (?, ?, ?)");
            $insert_i->bind_param("sii", $id, $comp_id, $assessor_id);
            $insert_i->execute();
        }

        header("Location: AdminStudents.php?msg=updated");
        exit();
    }

    include 'layout.php';
?>

<div class="container">
    <div class="form-header">
        <h2>Edit Student Details</h2>
        <a href="AdminStudents.php" class="btn-back">← Cancel Changes</a>
    </div>
    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label>Student ID (Read Only):</label>
                <input type="text" value="<?php echo htmlspecialchars($student['student_id']); ?>" disabled>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Programme:</label>
                <select name="programme_id" required>
                    <?php while($p = $programmes->fetch_assoc()): ?>
                        <option value="<?php echo $p['programme_id']; ?>" <?php if($p['programme_id'] == $student['programme_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($p['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Company:</label>
                <select name="company_id">
                    <option value="">-- Select Company --</option>
                    <?php while($c = $companies->fetch_assoc()): ?>
                        <option value="<?php echo $c['company_id']; ?>" <?php if($c['company_id'] == $student['company_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Assessor:</label>
                <select name="assessor_id">
                    <option value="">-- Select Assessor --</option>
                    <?php while($a = $assessors->fetch_assoc()): ?>
                        <option value="<?php echo $a['user_id']; ?>" <?php if($a['user_id'] == $student['assessor_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($a['username']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-add">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
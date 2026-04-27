<?php
    require_once 'AuthCheck.php';
    require_once 'Database.php';
    checkRole('Admin');

    $id = $_GET['id'];
    
    $sql = "SELECT s.*, i.company_name, i.assessor_id 
        FROM students s 
        LEFT JOIN internships i ON s.student_id = i.student_id 
        WHERE s.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    $assessors_result = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'Assessor'");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sname = $_POST['student_name'];
    $prog = $_POST['programme'];
    $company = $_POST['company_name'];
    $assessor_id = $_POST['assessor_id'];

    $update_student = $conn->prepare("UPDATE students SET student_name = ?, programme = ? WHERE student_id = ?");
    $update_student->bind_param("sss", $sname, $prog, $id);
    $update_student->execute();

    $check_intern = $conn->prepare("SELECT internship_id FROM internships WHERE student_id = ?");
    $check_intern->bind_param("s", $id);
    $check_intern->execute();
    $res = $check_intern->get_result();

    if ($res->num_rows > 0) {
        $update_intern = $conn->prepare("UPDATE internships SET company_name = ?, assessor_id = ? WHERE student_id = ?");
        $update_intern->bind_param("sis", $company, $assessor_id, $id);
        $update_intern->execute();
    } 
    else {
        $insert_intern = $conn->prepare("INSERT INTO internships (student_id, company_name, assessor_id) VALUES (?, ?, ?)");
        $insert_intern->bind_param("ssi", $id, $company, $assessor_id);
        $insert_intern->execute();
    }

    header("Location: AdminStudents.php?success=1");
    exit();
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
            <div class="form-group">
                <label>Company:</label>
                <input type="text" name="company_name" value="<?php echo $student['company_name']; ?>">
            </div>

            <div class="form-group">
                <label>Assessor:</label>
                <select name="assessor_id">
                    <option value="">-- Select Assessor --</option>
                    <?php while($assessor = $assessors_result->fetch_assoc()): ?>
                        <option value="<?php echo $assessor['user_id']; ?>" 
                            <?php if($assessor['user_id'] == $student['assessor_id']) echo 'selected'; ?>>
                            <?php echo $assessor['full_name']; ?>
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
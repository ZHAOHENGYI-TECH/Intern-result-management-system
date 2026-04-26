<?php
require_once 'AuthCheck.php'; 
checkRole('Admin');

require_once 'Database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sql = "SELECT s.student_id, s.student_name, s.programme, i.company_name 
        FROM students s 
        LEFT JOIN internships i ON s.student_id = i.student_id";
$result = $conn->query($sql); 

include 'layout.php';
?>
    <div class="container">
        <h2>Admin - Student Archive Management</h2>
        <div class="table-controls">
            <div class="search-box">
                <input type="text" placeholder="Search by ID or Name...">
            </div>
            <div class="action-buttons">
                <a href="AssignInternship.php" class="btn-link">Assign Internship</a>
                <a href="AddStudent.php" class="btn-add">+ Add Student</a>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Programme</th>
                        <th>Company</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["student_id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["student_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["programme"]) . "</td>";
                            $company = !empty($row["company_name"]) ? $row["company_name"] : "<i>Not Assigned</i>";
                            echo "<td>" . $company . "</td>";
                            echo "<td>
                                    <a class='btn-edit' href='EditStudent.php?id=" . $row["student_id"] . "'>Edit</a>
                                    <a class='btn-delete' href='DeleteStudent.php?id=" . $row["student_id"] . "' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No student records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

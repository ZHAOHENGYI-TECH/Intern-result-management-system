<?php
require_once 'AuthCheck.php'; 
checkRole('Admin');

require_once 'Database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sql = "SELECT s.student_id, s.first_name, s.last_name, p.name AS pName, c.name AS cName
        FROM student s 
        LEFT JOIN Programme p ON s.programme_id = p.programme_id
        LEFT JOIN Internship i ON s.student_id = i.student_id
        LEFT JOIN Company c ON i.company_id = c.company_id";
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
                        <th>First Name</th>
                        <th>Last Name</th>
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
                            echo "<td>" . htmlspecialchars($row["first_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["last_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["pName"]) . "</td>";
                            $company = !empty($row["cName"]) ? $row["cName"] : "<i>Not Assigned</i>";
                            echo "<td>" . $company . "</td>";
                            echo "<td>
                                    <a class='btn-edit' href='EditStudent.php?id=" . $row["student_id"] . "'>Edit</a>
                                    <a class='btn-delete' href='DeleteStudent.php?id=" . $row["student_id"] . "' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No student records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fellowship_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete user functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $u_id = $_POST['u_id'];
    $sql = "DELETE FROM users WHERE u_id = $u_id";
    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    exit;
}



// Delete sponsor functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_sponsor'])) {
    $s_id = $_POST['s_id'];
    $sql = "DELETE FROM sponsor WHERE s_id = $s_id";
    if ($conn->query($sql) === TRUE) {
        echo "Sponsor deleted successfully.";
    } else {
        echo "Error deleting sponsor: " . $conn->error;
    }
    exit;
}


// Delete course functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $sql = "DELETE FROM courses WHERE course_id = $course_id";
    if ($conn->query($sql) === TRUE) {
        echo "Course deleted successfully.";
    } else {
        echo "Error deleting course: " . $conn->error;
    }
    exit;
}

// Delete department functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_department'])) {
    $dept_id = $_POST['dept_id'];
    $sql = "DELETE FROM department WHERE dept_id = $dept_id";
    if ($conn->query($sql) === TRUE) {
        echo "Department deleted successfully.";
    } else {
        echo "Error deleting department: " . $conn->error;
    }
    exit;
}


// Delete fellowship functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_fellowship'])) {
    $fellowship_id = $_POST['fellowship_id'];
    $sql = "DELETE FROM fellowship WHERE fellowship_id = $fellowship_id";
    if ($conn->query($sql) === TRUE) {
        echo "Fellowship deleted successfully.";
    } else {
        echo "Error deleting fellowship: " . $conn->error;
    }
    exit;
}

// Delete funding functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_funding'])) {
    $fund_id = $_POST['fund_id'];
    $sql = "DELETE FROM funding WHERE fund_id = $fund_id";
    if ($conn->query($sql) === TRUE) {
        echo "Funding deleted successfully.";
    } else {
        echo "Error deleting funding: " . $conn->error;
    }
    exit;
}

// Delete installment functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_installment'])) {
    $installment_id = $_POST['installment_id'];
    $sql = "DELETE FROM installment WHERE id = $installment_id";
    if ($conn->query($sql) === TRUE) {
        echo "Installment deleted successfully.";
    } else {
        echo "Error deleting installment: " . $conn->error;
    }
    exit;
}
// Delete faculty functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_faculty'])) {
    $faculty_id = $_POST['faculty_id'];
    $sql = "DELETE FROM faculty WHERE faculty_id = $faculty_id";
    if ($conn->query($sql) === TRUE) {
        echo "Faculty deleted successfully.";
    } else {
        echo "Error deleting faculty: " . $conn->error;
    }
    exit;
}

// Delete Project Functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];
    $sql = "DELETE FROM project WHERE project_id = $project_id";
    if ($conn->query($sql) === TRUE) {
        echo "Project deleted successfully.";
    } else {
        echo "Error deleting project: " . $conn->error;
    }
    exit;
}

// Function to display users
function viewUsers($conn) {
    $sql = "SELECT u_id, name, department, email, district, phone FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>u_id</th><th>Name</th><th>department</th><th>Email</th><th>District</th><th>Phone</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['u_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['department']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['district']}</td>
                    <td>{$row['phone']}</td>
                    <td>
                        <button class='delete-btn' onclick='deleteUser({$row['u_id']})'>Delete</button>
                    </td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No users found.";
    }
}

// Function to display applications
function viewApplications($conn) {
    $sql = "SELECT * FROM applications";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>ID</th><th>User ID</th><th>Degree</th><th>Institution</th><th>Graduation Year</th><th>Experience</th><th>Motivation</th><th>Status</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr id='application-{$row['application_id']}'>
                    <td>{$row['application_id']}</td>
                    <td>{$row['user_id']}</td>
                    <td>{$row['degree']}</td>
                    <td>{$row['institution']}</td>
                    <td>{$row['graduation_year']}</td>
                    <td>{$row['experience']}</td>
                    <td>{$row['motivation']}</td>
                    <td class='status'>{$row['status']}</td>
                    <td><button class='review-btn' onclick='reviewApplication({$row['application_id']})'>Review</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No applications found.";
    }
}


// Function to display sponsors
function viewSponsors($conn) {
    $sql = "SELECT s_id, name_of_sponsor FROM sponsor";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>ID</th><th>Name of Sponsor</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['s_id']}</td>
                    <td>{$row['name_of_sponsor']}</td>
                    <td>
                        <button class='delete-btn' onclick='deleteSponsor({$row['s_id']})'>Delete</button>
                    </td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No sponsors found.";
    }
}

// Function to display courses
function viewCourses($conn) {
    $sql = "SELECT course_id, title, credit_hour FROM courses";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Course ID</th><th>Title</th><th>Credit Hour</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['course_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['credit_hour']}</td>
                    <td>
                        <button class='delete-btn' onclick='deleteCourse({$row['course_id']})'>Delete</button>
                    </td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No courses found.";
    }
}

// Function to display departments
function viewDepartments($conn) {
    $sql = "SELECT dept_id, dept_name, building, location FROM department";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Department ID</th><th>Name</th><th>Building</th><th>Location</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['dept_id']}</td>
                    <td>{$row['dept_name']}</td>
                    <td>{$row['building']}</td>
                    <td>{$row['location']}</td>
                    <td>
                        <button class='delete-btn' onclick='deleteDepartment({$row['dept_id']})'>Delete</button>
                    </td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No departments found.";
    }
}

// Function to display fellowships
function viewFellowships($conn) {
    $sql = "SELECT fellowship_id, name, description, start, end, fund_id FROM fellowship";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>ID</th><th>Name</th><th>Description</th><th>Start</th><th>End</th><th>Fund ID</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['fellowship_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['start']}</td>
                    <td>{$row['end']}</td>
                    <td>{$row['fund_id']}</td>
                    <td><button class='delete-btn' onclick='deleteFellowship({$row['fellowship_id']})'>Delete</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No fellowships found.";
    }
}

// Function to display funding
function viewFunding($conn) {
    $sql = "SELECT fund_id, tot_amt, date_of_proposal, phd_id FROM funding";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Fund ID</th><th>Total Amount</th><th>Date of Proposal</th><th>PhD ID</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['fund_id']}</td>
                    <td>{$row['tot_amt']}</td>
                    <td>{$row['date_of_proposal']}</td>
                    <td>{$row['phd_id']}</td>
                    <td><button class='delete-btn' onclick='deleteFunding({$row['fund_id']})'>Delete</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No funding found.";
    }
}

// Function to display installments
function viewInstallments($conn) {
    $sql = "SELECT id, fellow_id, amount, due_date, created_at FROM installment";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>ID</th><th>Fellowship ID</th><th>Amount</th><th>Due Date</th><th>Created At</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['fellow_id']}</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['due_date']}</td>
                    <td>{$row['created_at']}</td>
                    <td><button class='delete-btn' onclick='deleteInstallment({$row['id']})'>Delete</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No installments found.";
    }
}

// Function to display faculty
function viewFaculty($conn) {
    $sql = "SELECT faculty_id, name, mobile, start_date, end_date, dept_head, dept_id FROM faculty";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Faculty ID</th><th>Name</th><th>Mobile</th><th>Start Date</th><th>End Date</th><th>Department Head</th><th>Department ID</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['faculty_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['mobile']}</td>
                    <td>{$row['start_date']}</td>
                    <td>{$row['end_date']}</td>
                    <td>{$row['dept_head']}</td>
                    <td>{$row['dept_id']}</td>
                    <td><button class='delete-btn' onclick='deleteFaculty({$row['faculty_id']})'>Delete</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No faculty found.";
    }
}

// View Projects Function
function viewProjects($conn) {
    $sql = "SELECT project_id, title, start_date, end_date, duration, fellowship_id FROM project";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table">';
        echo '<tr><th>Project ID</th><th>Title</th><th>Start Date</th><th>End Date</th><th>Duration</th><th>Fellowship ID</th><th>Actions</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['project_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['start_date']}</td>
                    <td>{$row['end_date']}</td>
                    <td>{$row['duration']}</td>
                    <td>{$row['fellowship_id']}</td>
                    <td><button class='delete-btn' onclick='deleteProject({$row['project_id']})'>Delete</button></td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "No projects found.";
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICT Division</title>
    <style>
        /* General Reset and Styling */


        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            width: 100%;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        .dashboard-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            width: 100%;
            max-width: 1200px;
            display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 20px; 
        }
        .admin-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            text-align: center;
        }
        .admin-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
        }
        .admin-button:hover {
            background-color: #0056b3;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table, .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .delete-btn, .review-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .review-btn {
            background-color: #28a745;
            color: white;
        }
        .review-btn:hover {
            background-color: #218838;
        }
        footer {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>ICT Division Fellowship Management - Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="Fellowships.php">Fellowship</a></li>
                <li><a href="apply.php">Apply</a></li>
                <li><a href="status.php">Application Status</a></li>
                <li><a href="mentorship.php">Mentorship</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="dashboard-container">
            <section class="admin-section">
                <h3>User Management</h3>
                <button class="admin-button" onclick="loadView('users')">View Users</button>
                <div id="user-section">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'users') { viewUsers($conn); } ?>
                </div>
            </section>

            <section class="admin-section">
                <h3>Application Management</h3>
                <button class="admin-button" onclick="loadView('applications')">View Applications</button>
                <div id="application-section">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'applications') { viewApplications($conn); } ?>
                </div>
            </section>

             <!-- New sections for sponsors, courses, and departments -->
             <section class="admin-section">
                <h3>Sponsor Management</h3>
                <button class="admin-button" onclick="loadView('sponsors')">View Sponsors</button>
                <div id="sponsor-section">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'sponsors') { viewSponsors($conn); } ?>
                </div>
            </section>
            <section class="admin-section">
                <h3>Course Management</h3>
                <button class="admin-button" onclick="loadView('courses')">View Courses</button>
                <div id="course-section">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'courses') { viewCourses($conn); } ?>
                </div>
            </section>
            <section class="admin-section">
                <h3>Department Management</h3>
                <button class="admin-button" onclick="loadView('departments')">View Departments</button>
                <div id="department-section">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'departments') { viewDepartments($conn); } ?>
                </div>
            </section>


            <section class="admin-section">
    <h3>Fellowship Management</h3>
    <button class="admin-button" onclick="loadView('fellowships')">View Fellowships</button>
    <div id="fellowship-section">
        <?php if (isset($_GET['view']) && $_GET['view'] == 'fellowships') { viewFellowships($conn); } ?>
    </div>
</section>

<section class="admin-section">
    <h3>Funding Management</h3>
    <button class="admin-button" onclick="loadView('funding')">View Funding</button>
    <div id="funding-section">
        <?php if (isset($_GET['view']) && $_GET['view'] == 'funding') { viewFunding($conn); } ?>
    </div>
</section>

<section class="admin-section">
    <h3>Installment Management</h3>
    <button class="admin-button" onclick="loadView('installments')">View Installments</button>
    <div id="installment-section">
        <?php if (isset($_GET['view']) && $_GET['view'] == 'installments') { viewInstallments($conn); } ?>
    </div>
</section>


<section class="admin-section">
    <h3>Faculty Management</h3>
    <button class="admin-button" onclick="loadView('faculty')">View Faculty</button>
    <div id="faculty-section">
        <?php if (isset($_GET['view']) && $_GET['view'] == 'faculty') { viewFaculty($conn); } ?>
    </div>
</section>

<section class="admin-section">
        <h3>Project Management</h3>
        <button class="admin-button" onclick="loadView('projects')">View Projects</button>
        <div id="project-section">
            <?php if (isset($_GET['view']) && $_GET['view'] == 'projects') { viewProjects($conn); } ?>
        </div>
    </section>
    <section class="admin-section">
    <h3>Add User</h3>
    <a href="user_crud.php" class="admin-button">Add User</a>
</section>

<section class="admin-section">
    <h3>Add Fellows</h3>
    <a href="fellow.php" class="admin-button">Add Fellows</a>
    </section>

    <section class="admin-section">
    <h3>Management Application</h3>
    <a href="manage_applications.php" class="admin-button">Approve or Not</a>
    </section>

    

        </div>

    </main>

    <footer>
        <p>&copy; 2024 ICT Division. All Rights Reserved.</p>
    </footer>

    <script>
        function loadView(view) {
            location.href = "admin.php?view=" + view;
        }

        function deleteUser(u_id) {
            if (confirm("Are you sure you want to delete this user?")) {
                const formData = new FormData();
                formData.append("delete_user", true);
                formData.append("u_id", u_id);

                fetch("admin.php", { method: "POST", body: formData })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    });
            }
        }

        function reviewApplication(applicationId) {
            const status = prompt("Enter status (approve/reject):").toLowerCase();
            if (status === 'approve' || status === 'reject') {
                const formData = new FormData();
                formData.append("review_application", true);
                formData.append("application_id", applicationId);
                formData.append("status", status);

                fetch("admin.php", { method: "POST", body: formData })
                    .then(response => response.text())
                    .then(data => {
                        document.querySelector(`#application-${applicationId} .status`).innerText = data;
                    });
            } else {
                alert("Invalid status. Please enter 'approve' or 'reject'.");
            }
        }


        function deleteSponsor(sId) {
    if (confirm("Are you sure you want to delete this sponsor?")) {
        // AJAX request to delete sponsor
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "admin.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText); // Show success or error message
                location.reload(); // Refresh page to update sponsor list
            }
        };
        xhr.send("delete_sponsor=true&s_id=" + sId);
    }
}

function deleteCourse(courseId) {
    if (confirm("Are you sure you want to delete this course?")) {
        // AJAX request to delete course
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "admin.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText); // Show success or error message
                location.reload(); // Refresh page to update course list
            }
        };
        xhr.send("delete_course=true&course_id=" + courseId);
    }
}

function deleteDepartment(deptId) {
    if (confirm("Are you sure you want to delete this department?")) {
        // AJAX request to delete department
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "admin.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText); // Show success or error message
                location.reload(); // Refresh page to update department list
            }
        };
        xhr.send("delete_department=true&dept_id=" + deptId);
    }
}

function deleteFellowship(fellowshipId) {
    if (confirm("Are you sure you want to delete this fellowship?")) {
        const formData = new FormData();
        formData.append("delete_fellowship", true);
        formData.append("fellowship_id", fellowshipId);

        fetch("admin.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
}

function deleteFunding(fundId) {
    if (confirm("Are you sure you want to delete this funding?")) {
        const formData = new FormData();
        formData.append("delete_funding", true);
        formData.append("fund_id", fundId);

        fetch("admin.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
}

function deleteInstallment(installmentId) {
    if (confirm("Are you sure you want to delete this installment?")) {
        const formData = new FormData();
        formData.append("delete_installment", true);
        formData.append("installment_id", installmentId);

        fetch("admin.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
}

function deleteFaculty(facultyId) {
    if (confirm("Are you sure you want to delete this faculty member?")) {
        const formData = new FormData();
        formData.append("delete_faculty", true);
        formData.append("faculty_id", facultyId);

        fetch("admin.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
}

// JavaScript function to handle project deletion
        function deleteProject(projectId) {
            if (confirm("Are you sure you want to delete this project?")) {
                const formData = new FormData();
                formData.append("delete_project", true);
                formData.append("project_id", projectId);

                fetch("admin.php", { method: "POST", body: formData })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    });
            }
        }

    </script>


</body>
</html>
<?php $conn->close(); ?>

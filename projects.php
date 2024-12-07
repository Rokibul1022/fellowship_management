<?php
// Enable error reporting to help debug any issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Update with your DB password
$dbname = "fellowship_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : NULL;
    $title = $_POST['title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $duration = $_POST['duration'];
    $fellowship_id = $_POST['fellowship_id'];

    // Prepare SQL query
    $sql = "INSERT INTO project (project_id, title, start_date, end_date, duration, fellowship_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $project_id, $title, $start_date, $end_date, $duration, $fellowship_id);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Project added successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all projects from the database (no filter, all projects)
$sql_fetch_all = "SELECT project_id, title, start_date, end_date, duration, fellowship_id FROM project";
$result_all_projects = $conn->query($sql_fetch_all);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        .navbar ul li {
            float: left;
        }
        .navbar ul li a {
            display: block;
            color: white;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar ul li a:hover {
            background-color: #575757;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
        }
        .form-container label {
            font-size: 16px;
            margin-top: 10px;
            display: block;
            color: #555;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            font-size: 16px;
        }
        .project-info {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .project-info h2 {
            color: #333;
        }
        .project-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .project-info table th,
        .project-info table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .project-info table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<!-- Navigation Menu -->
<nav class="navbar">
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="Fellowships.php">Fellowship</a></li>
        <li><a href="applications.php">Apply</a></li>
        <li><a href="check_status.php">Application Status</a></li>
        <li><a href="department.php">Department</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="scholarship.php"><i class="fas fa-award"></i> Scholarship</a></li>
        <li><a href="sponsor.php">Sponsor</a></li>
        <li><a href="user_crud.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="faculty.php">Faculty</a></li>
        <li><a href="admin.php">Admin</a></li>
        <li><a href="login.html" class="login-btn">Login</a></li>
    </ul>
</nav>

<!-- Add Project Form -->
<div class="form-container">
    <h2>Add Project</h2>
    <form action="projects.php" method="POST">
        <label for="project_id">Project ID (optional):</label>
        <input type="number" id="project_id" name="project_id">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>

        <label for="duration">Duration (in days):</label>
        <input type="number" id="duration" name="duration" required>

        <label for="fellowship_id">Fellowship ID:</label>
        <input type="number" id="fellowship_id" name="fellowship_id" required>

        <button type="submit">Add Project</button>
    </form>
    <div class="message"><?= $message ?></div>
</div>

<!-- Display All Projects -->
<div class="project-info">
    <h2>All Projects</h2>
    <table>
        <thead>
            <tr>
                <th>Project ID</th>
                <th>Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Fellowship ID</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display all projects
            if ($result_all_projects->num_rows > 0) {
                while ($row = $result_all_projects->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['project_id'] . "</td>
                            <td>" . $row['title'] . "</td>
                            <td>" . $row['start_date'] . "</td>
                            <td>" . $row['end_date'] . "</td>
                            <td>" . $row['duration'] . "</td>
                            <td>" . $row['fellowship_id'] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No projects found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close connection
$conn->close();
?>

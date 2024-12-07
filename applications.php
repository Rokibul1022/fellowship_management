<?php
// Start the session and include the database connection
session_start();

// Database connection
$servername = "localhost"; // Your server address (localhost or IP address)
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "fellowship_management"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u_id = (int)$_POST['u_id'];
    $degree = htmlspecialchars(trim($_POST['degree']));
    $institution = htmlspecialchars(trim($_POST['institution']));
    $graduation_year = (int)$_POST['graduationYear'];
    $experience = htmlspecialchars(trim($_POST['experience']));
    $motivation = htmlspecialchars(trim($_POST['motivation']));

    // Check if the u_id exists in the users table
    $check_user_sql = "SELECT u_id FROM users WHERE u_id = ?";
    $stmt_check_user = $conn->prepare($check_user_sql);
    $stmt_check_user->bind_param("i", $u_id);
    $stmt_check_user->execute();
    $stmt_check_user->store_result();

    if ($stmt_check_user->num_rows > 0) {
        // u_id exists, proceed with application insertion
        $sql = "INSERT INTO applications (u_id, degree, institution, graduation_year, experience, motivation) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ississ", $u_id, $degree, $institution, $graduation_year, $experience, $motivation);

        if ($stmt->execute()) {
            $message = "<p style='text-align:center; color:green;'>Application submitted successfully!</p>";
        } else {
            $message = "<p style='text-align:center; color:red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        // u_id does not exist
        $message = "<p style='text-align:center; color:red;'>Error: User ID does not exist in the system.</p>";
    }
    $stmt_check_user->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; margin: 0; padding: 0; }
        .container { width: 50%; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; }
        label, input, select, textarea { margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px; }
        input[type="submit"]:hover { background-color: #45a049; }
        .menu-bar ul { list-style-type: none; padding: 0; }
        .menu-bar li { display: inline; margin-right: 15px; }
        .menu-bar a { text-decoration: none; color: #333; }
    </style>
</head>
<body>

<div class="menu-bar">
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="Fellowships.php">Fellowship</a></li>
        <li><a href="applications.php">Apply</a></li>
        <li><a href="status.html">Application Status</a></li>
        <li><a href="mentorship.html">Mentorship</a></li>
        <li><a href="projects.html">Projects</a></li>
        <li><a href="courses.html">Courses</a></li>
        <li><a href="scholarship.php"><i class="fas fa-award"></i>Scholarship</a></li>
        <li><a href="admin.html">Admin</a></li>
        <li><a href="user_crud.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="login.html" class="login-btn">Login</a></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</div>

<div class="container">
    <h2>Apply for Fellowship</h2>
    <form method="POST" action="">
        <label for="u_id">User ID (must be entered manually):</label>
        <input type="text" id="u_id" name="u_id" required>
        
        <label for="degree">Degree:</label>
        <input type="text" id="degree" name="degree" required>
        
        <label for="institution">Institution:</label>
        <input type="text" id="institution" name="institution" required>
        
        <label for="graduationYear">Graduation Year:</label>
        <input type="number" id="graduationYear" name="graduationYear" required>
        
        <label for="experience">Experience:</label>
        <textarea id="experience" name="experience" required></textarea>
        
        <label for="motivation">Motivation:</label>
        <textarea id="motivation" name="motivation" required></textarea>
        
        <input type="submit" value="Submit Application">
    </form>

    <?php
    // Display success or error message
    if (isset($message)) {
        echo $message;
    }
    ?>
</div>

</body>
</html>

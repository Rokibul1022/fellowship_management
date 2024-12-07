<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "fellowship_management";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = "";
$login_error = "";
$faculty_data = null;

// Handle new faculty form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_faculty'])) {
    // Sanitize inputs
    $faculty_id = $conn->real_escape_string($_POST['faculty_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $dept_head = $conn->real_escape_string($_POST['dept_head']);
    $dept_id = $conn->real_escape_string($_POST['dept_id']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    $sql = "INSERT INTO faculty (faculty_id, name, mobile, start_date, end_date, dept_head, dept_id, password) 
            VALUES ('$faculty_id', '$name', '$mobile', '$start_date', '$end_date', '$dept_head', '$dept_id', '$password')";

    if ($conn->query($sql) === TRUE) {
        $message = "Faculty added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle faculty login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $faculty_id = $conn->real_escape_string($_POST['faculty_id']);
    $password = $_POST['password'];

    // Query to fetch faculty details
    $stmt = $conn->prepare("SELECT * FROM faculty WHERE faculty_id = ?");
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $faculty = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $faculty['password'])) {
            $faculty_data = $faculty;
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "Faculty ID not found.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management</title>
    <style>
        /* Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar a.active {
            background-color: #04AA6D;
            color: white;
        }
        .container {
            padding: 20px;
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #04AA6D;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin-top: 20px;
            color: green;
        }
        .error {
            text-align: center;
            margin-top: 20px;
            color: red;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="Fellowships.php">Fellowship</a>
    <a href="faculty.php" class="active">Faculty</a>
    <a href="login.html">Login</a>
</div>

<div class="container">
    <h2>Add Faculty</h2>
    <form method="POST" action="faculty.php">
        <input type="hidden" name="add_faculty">
        <div class="form-group">
            <label for="faculty_id">Faculty ID:</label>
            <input type="text" id="faculty_id" name="faculty_id" required>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" id="mobile" name="mobile" required>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date">
        </div>
        <div class="form-group">
            <label for="dept_head">Department Head (Yes/No):</label>
            <select id="dept_head" name="dept_head" required>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dept_id">Department ID:</label>
            <input type="text" id="dept_id" name="dept_id" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Add Faculty</button>
        <div class="message"><?php echo $message; ?></div>
    </form>
</div>

<div class="container">
    <h2>Faculty Login</h2>
    <form method="POST" action="faculty.php">
        <input type="hidden" name="login">
        <div class="form-group">
            <label for="faculty_id">Faculty ID:</label>
            <input type="text" id="faculty_id" name="faculty_id" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <div class="error"><?php echo $login_error; ?></div>
    </form>
</div>

<?php if ($faculty_data): ?>
    <div class="container">
        <h2>Faculty Profile</h2>
        <p><strong>Name:</strong> <?php echo $faculty_data['name']; ?></p>
        <p><strong>Mobile:</strong> <?php echo $faculty_data['mobile']; ?></p>
        <p><strong>Department Head:</strong> <?php echo $faculty_data['dept_head']; ?></p>
        <p><strong>Department ID:</strong> <?php echo $faculty_data['dept_id']; ?></p>
        <p><strong>Start Date:</strong> <?php echo $faculty_data['start_date']; ?></p>
        <p><strong>End Date:</strong> <?php echo $faculty_data['end_date']; ?></p>
    </div>
<?php endif; ?>

</body>
</html>

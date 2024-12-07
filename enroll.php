<?php
// Database connection
$host = 'localhost'; // Database host
$user = 'root';      // Database username
$pass = '';          // Database password
$dbname = 'fellowship_management'; // Database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch course_id and fellowship_id for dropdown
$courses_query = "SELECT course_id FROM courses";
$fellowships_query = "SELECT fellowship_id FROM fellowship";

$courses_result = $conn->query($courses_query);
$fellowships_result = $conn->query($fellowships_query);

// Insert data into enroll table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $fellowship_id = $_POST['fellowship_id'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $mark = $_POST['mark'];

    $insert_query = "INSERT INTO enroll (course_id, fellowship_id, semester, year, mark)
                     VALUES ('$course_id', '$fellowship_id', '$semester', '$year', '$mark')";

    if ($conn->query($insert_query) === TRUE) {
        $success_message = "Data inserted successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        select, input[type="text"], input[type="number"], input[type="submit"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: #ffffff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Enroll Form</h2>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="course_id">Course ID:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">Select a Course</option>
                    <?php
                    if ($courses_result->num_rows > 0) {
                        while ($row = $courses_result->fetch_assoc()) {
                            echo "<option value='" . $row['course_id'] . "'>" . $row['course_id'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fellowship_id">Fellowship ID:</label>
                <select name="fellowship_id" id="fellowship_id" required>
                    <option value="">Select a Fellowship</option>
                    <?php
                    if ($fellowships_result->num_rows > 0) {
                        while ($row = $fellowships_result->fetch_assoc()) {
                            echo "<option value='" . $row['fellowship_id'] . "'>" . $row['fellowship_id'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="semester">Semester:</label>
                <input type="text" name="semester" id="semester" placeholder="Enter Semester" required>
            </div>
            <div class="form-group">
                <label for="year">Year:</label>
                <input type="number" name="year" id="year" placeholder="Enter Year" required>
            </div>
            <div class="form-group">
                <label for="mark">Mark:</label>
                <input type="number" name="mark" id="mark" placeholder="Enter Mark" required>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

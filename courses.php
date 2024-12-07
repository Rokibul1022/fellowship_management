<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fellowship_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert course data into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $credit_hour = $_POST['credit_hour'];

    $sql = "INSERT INTO courses (course_id, title, credit_hour) 
            VALUES ('$course_id', '$title', '$credit_hour')";

    if ($conn->query($sql) === TRUE) {
        echo "New course added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch courses from the database
$sql_fetch_courses = "SELECT course_id, title, credit_hour FROM courses";
$result = $conn->query($sql_fetch_courses);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <style>
        /* CSS styles for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        header .logo {
            max-width: 100px;
            vertical-align: middle;
        }

        header h1 {
            display: inline-block;
            margin-left: 20px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            padding: 20px;
            background-color: white;
            max-width: 800px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
            padding: 12px;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        footer {
            text-align: center;
            background-color: #333;
            color: white;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <img src="logo.png" alt="ICT Division Logo" class="logo">
        <h1>ICT Division Fellowship Management</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="Fellowships.html">Fellowship</a></li>
                <li><a href="apply.html">Apply</a></li>
                <li><a href="status.html">Application Status</a></li>
                <li><a href="mentorship.html">Mentorship</a></li>
                <li><a href="projects.html">Projects</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="admin.html">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Available Courses</h2>
        <p>We offer a variety of courses to enhance your skills in ICT. Please add new courses to our system:</p>
        <h3>Explore our available courses:</h3>
        <ul>
            <?php
            // Display fetched courses
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li><strong>Course ID:</strong> " . $row["course_id"] . " | <strong>Title:</strong> " . $row["title"] . " | <strong>Credit Hour:</strong> " . $row["credit_hour"] . "</li>";
                }
            } else {
                echo "<li>No courses available at the moment.</li>";
            }
            ?>
        </ul>
            <h3>Insert new Courses here: </h3>
        <form method="POST" action="courses.php">
            <label for="course_id">Course ID:</label>
            <input type="text" id="course_id" name="course_id" required><br><br>

            <label for="title">Course Title:</label>
            <input type="text" id="title" name="title" required><br><br>

            <label for="credit_hour">Credit Hour:</label>
            <input type="number" id="credit_hour" name="credit_hour" required><br><br>

            <input type="submit" value="Add Course" class="submit-btn">
        </form>

        
    </main>

    <footer>
        <p>&copy; 2024 ICT Division. All Rights Reserved.</p>
    </footer> 
</body>
</html>

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

// Function to insert department data into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dept_id = $_POST['dept_id'];
    $dept_name = $_POST['dept_name'];
    $building = $_POST['building'];
    $location = $_POST['location'];

    $sql = "INSERT INTO department (dept_id, dept_name, building, location) 
            VALUES ('$dept_id', '$dept_name', '$building', '$location')";

    if ($conn->query($sql) === TRUE) {
        echo "New department added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department</title>
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

        input, select {
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

        ul {
            margin-top: 20px;
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
                <li><a href="department.php">Department</a></li>
                <li><a href="projects.html">Projects</a></li>
                <li><a href="courses.html">Courses</a></li>
                <li><a href="admin.html">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Department</h2>
        <p>Select your department and give us the required information.</p>

        <form method="POST" action="department.php">
            <label for="dept_id">Department ID:</label>
            <input type="text" id="dept_id" name="dept_id" required><br><br>

            <label for="dept_name">Department Name:</label>
            <select id="dept_name" name="dept_name" required>
                <option value="Computer Science">Computer Science</option>
                <option value="Mechanical Engineering">Mechanical Engineering</option>
                <option value="Civil Engineering">Civil Engineering</option>
                <option value="Electrical Engineering">Electrical Engineering</option>
                <option value="Chemical Engineering">Chemical Engineering</option>
                <option value="Biomedical Engineering">Biomedical Engineering</option>
                <option value="Business Administration">Business Administration</option>
                <option value="Economics">Economics</option>
                <option value="Law">Law</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Physics">Physics</option>
                <option value="Chemistry">Chemistry</option>
                <option value="Biology">Biology</option>
                <option value="Psychology">Psychology</option>
                <option value="Sociology">Sociology</option>
                <option value="Literature">Literature</option>
                <option value="History">History</option>
                <option value="Arts">Arts</option>
                <option value="Music">Music</option>
                <option value="Philosophy">Philosophy</option>
                <option value="Education">Education</option>
            </select><br><br>

            <label for="building">Building:</label>
            <input type="text" id="building" name="building" required><br><br>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required><br><br>

            <input type="submit" value="Submit" class="submit-btn">
        </form>

        <h3>Explore more about your department.</h3>
        <ul>
            <li>Sign up for the mentorship program during your application.</li>
            <li>Attend mentorship workshops and events.</li>
            <li>Engage actively with your mentor and seek guidance.</li>
        </ul>
    </main>

    <footer>
        <p>&copy; 2024 ICT Division. All Rights Reserved.</p>
    </footer>

    <script>
        // JavaScript for additional functionality (if any)
        document.addEventListener("DOMContentLoaded", function() {
            // Example: You can add validation or dynamic behavior here
        });
    </script>
</body>
</html>

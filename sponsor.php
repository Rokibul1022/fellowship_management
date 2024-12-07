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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $s_id = $_POST['s_id'];
    $name_of_sponsor = $_POST['name_of_sponsor'];

    $sql = "INSERT INTO sponsor (s_id, name_of_sponsor) VALUES ('$s_id', '$name_of_sponsor')";
    if ($conn->query($sql) === TRUE) {
        $successMessage = "Sponsor added successfully!";
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Sponsors</title>
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

        /* Menu Bar Styling */
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            background-color: #007bff;
            width: 100%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        nav ul li {
            margin: 0 15px;
        }
        
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 15px 10px;
            display: block;
        }

        /* Content and Form Styling */
        .content {
            background-color: #fff;
            padding: 20px;
            max-width: 800px;
            width: 100%;
            margin: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .content h2 {
            text-align: center;
            color: #007bff;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .submit-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
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

    <!-- Header and Menu Bar -->
    <header>
        <h1>Our Sponsors</h1>
    </header>
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
                <li><a href ="sponsor.php">Sponsor</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <h2>About Us</h2>
        <p>Welcome to the ICT Division Fellowship Management platform. We aim to provide valuable opportunities for students and professionals in the technology sector through sponsorships, mentorships, and hands-on projects. Our sponsors play a vital role in supporting our initiatives and helping us achieve our mission.</p>
        
        <div class="form-container">
            <h2>Become a Sponsor</h2>
            <form method="POST" action="sponsor.php">
                <div class="form-group">
                    <label for="s_id">Sponsor ID</label>
                    <input type="text" id="s_id" name="s_id" required>
                </div>
                <div class="form-group">
                    <label for="name_of_sponsor">Name of Sponsor</label>
                    <input type="text" id="name_of_sponsor" name="name_of_sponsor" required>
                </div>
                <button type="submit" class="submit-btn">Add Sponsor</button>
            </form>
            
            <?php if (isset($successMessage)) { ?>
                <div class="message"><?php echo $successMessage; ?></div>
            <?php } elseif (isset($errorMessage)) { ?>
                <div class="message" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;">
                    <?php echo $errorMessage; ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 ICT Division. All Rights Reserved.</p>
    </footer>

</body>
</html>

<?php $conn->close(); ?>

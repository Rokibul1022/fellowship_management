<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Details and Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header & Menu Styles */
        header {
            width: 100%;
            background: #333;
            color: #fff;
            padding: 10px 0;
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        
        header h1 {
            margin: 5px 0;
            font-size: 24px;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            gap: 15px;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        nav ul li a:hover {
            color: #6e8efb;
        }

        nav ul li a i {
            margin-right: 5px;
            font-size: 18px;
        }

        /* Content Section Styling */
        .content-section {
            max-width: 800px;
            width: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .content-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 26px;
        }

        .content-section p,
        .content-section ul {
            font-size: 16px;
            color: #555;
            text-align: left;
        }

        .content-section ul {
            list-style: disc;
            padding-left: 20px;
        }

        /* Scholarship Form Styling */
        .form-container {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: border-color 0.3s;
            font-size: 16px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 5px rgba(110, 142, 251, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #6e8efb;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #5a7ae6;
        }

        .success-message, .error-message {
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>

<header>
    <h1>ICT Division Scholarship</h1>
    <nav>
        <ul>
            <li><a href="index.html"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="fellowship.html"><i class="fas fa-user-graduate"></i>Fellowship</a></li>
            <li><a href="apply.html"><i class="fas fa-clipboard-check"></i>Apply</a></li>
            <li><a href="status.html"><i class="fas fa-info-circle"></i>Status</a></li>
            <li><a href="mentorship.html"><i class="fas fa-chalkboard-teacher"></i>Mentorship</a></li>
            <li><a href="projects.html"><i class="fas fa-project-diagram"></i>Projects</a></li>
            <li><a href="courses.html"><i class="fas fa-book"></i>Courses</a></li>
            <li><a href="scholarship.php"><i class="fas fa-award"></i>Scholarship</a></li>
            <li><a href="login.html"><i class="fas fa-sign-in-alt"></i>Login</a></li>
        </ul>
    </nav>
</header>

<!-- Scholarship Details Section -->
<div class="content-section">
    <h2>Scholarship Program</h2>

    <!-- Description -->
    <h3>Description</h3>
    <p>This scholarship aims to support talented students with financial assistance and resources to help them succeed in their studies and future careers in the ICT field.</p>

    <!-- Advantages -->
    <h3>Advantages</h3>
    <ul>
        <li>Financial assistance for tuition and resources.</li>
        <li>Access to mentorship and training programs.</li>
        <li>Networking opportunities with industry leaders.</li>
    </ul>

    <!-- Disadvantages -->
    <h3>Disadvantages</h3>
    <ul>
        <li>Limited number of scholarships available each year.</li>
        <li>Competitive application process.</li>
    </ul>

    <!-- How to Apply -->
    <h3>How to Apply</h3>
    <p>Fill out the application form below, providing all necessary information. Once submitted, you’ll receive an email confirming your application, and our team will review it.</p>
</div>

<!-- Scholarship Application Form -->
<div class="form-container content-section">
    <h2>Apply for Scholarship</h2>

    <!-- PHP Success Message -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply'])) {
        include 'db.php'; // Database connection file

        // Get form data and sanitize it
        $u_id = htmlspecialchars($_POST['u_id']);
        $name = htmlspecialchars($_POST['name']);
        $institute = htmlspecialchars($_POST['institute']);
        $topic = htmlspecialchars($_POST['topic']);
        $major_in = htmlspecialchars($_POST['major_in']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);

        // SQL to insert data
        $sql = "INSERT INTO scholarship_applications (u_id, name, institute, topic, major_in, email, phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $u_id, $name, $institute, $topic, $major_in, $email, $phone);

        if ($stmt->execute()) {
            echo "<p class='success-message'>Your application has been submitted successfully!</p>";
        } else {
            echo "<p class='error-message'>There was an error submitting your application. Please try again.</p>";
        }
        
        $stmt->close();
        $conn->close();
    }
    ?>

    <!-- Application Form -->
    <form action="scholarship.php" method="POST">
        <label for="u_id">User ID:</label>
        <input type="text" id="u_id" name="u_id" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="institute">Institute:</label>
        <input type="text" id="institute" name="institute" required>

        <label for="topic">Topic:</label>
        <input type="text" id="topic" name="topic" required>

        <label for="major_in">Major In:</label>
        <input type="text" id="major_in" name="major_in" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" required>

        <button type="submit" name="apply">Submit Application</button>
    </form>
</div>

</body>
</html>

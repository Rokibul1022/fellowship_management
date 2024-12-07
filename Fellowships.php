<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fellowship_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Fellowship insertion
    if (isset($_POST['submit_fellowship'])) {
        $fellowship_id = $_POST['fellowship_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $fund_id = $_POST['fund_id'];
        $course_id = $_POST['course_id'];
        $u_id = $_POST['u_id'];

        // Check if the provided u_id exists in users table
        $check_user_stmt = $conn->prepare("SELECT u_id FROM users WHERE u_id = ?");
        $check_user_stmt->bind_param("i", $u_id);
        $check_user_stmt->execute();
        $result = $check_user_stmt->get_result();
        if ($result && $result->num_rows > 0) {
            // Insert fellowship record
            $stmt = $conn->prepare("INSERT INTO fellowship (fellowship_id, name, description, start, end, fund_id, course_id, u_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssiii", $fellowship_id, $name, $description, $start, $end, $fund_id, $course_id, $u_id);

            if ($stmt->execute()) {
                $message = "Fellowship record added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error: Selected User ID does not exist!";
        }
        $check_user_stmt->close();
    }

    // PhD Fellowship Application
    elseif (isset($_POST['submit_phd'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $field_of_study = $_POST['field_of_study'];
        $institution = $_POST['institution'];

        // Insert PhD application
        $stmt = $conn->prepare("INSERT INTO phd (name, email, field_of_study, institution) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $field_of_study, $institution);

        if ($stmt->execute()) {
            $message = "PhD Application submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // MPhil Fellowship Application
    elseif (isset($_POST['submit_mphil'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $research_topic = $_POST['research_topic'];

        // Insert MPhil application
        $stmt = $conn->prepare("INSERT INTO mph (name, email, research_topic) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $research_topic);

        if ($stmt->execute()) {
            $message = "MPhil Application submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // Funding record insertion
    elseif (isset($_POST['submit_funding'])) {
        $fund_id = $_POST['fund_id'];
        $tot_amt = $_POST['tot_amt'];
        $date_of_proposal = date('Y-m-d', strtotime($_POST['date_of_proposal']));
        $phd_id = $_POST['phd_id'];

        // Check if the PhD ID exists
        $check_stmt = $conn->prepare("SELECT id FROM phd WHERE id = ?");
        $check_stmt->bind_param("i", $phd_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO funding (fund_id, tot_amt, date_of_proposal, phd_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idsi", $fund_id, $tot_amt, $date_of_proposal, $phd_id);

            if ($stmt->execute()) {
                $message = "Funding record added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error: PhD ID not found!";
        }
        $check_stmt->close();
    }

    // Installment record insertion
    elseif (isset($_POST['submit_installment'])) {
        $fellow_id = $_POST['fellow_id'];
        $amount = $_POST['amount'];
        $due_date = $_POST['due_date'];

        $stmt = $conn->prepare("INSERT INTO installment (fellow_id, amount, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $fellow_id, $amount, $due_date);

        if ($stmt->execute()) {
            $message = "Installment record added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fellowship - ICT Division Fellowship Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .header, .footer { background-color: #343a40; padding: 20px; text-align: center; color: white; }
        .main-content { padding: 20px; }
        .form-section { margin-top: 20px; display: none; }
        .message { margin-top: 20px; font-weight: bold; color: green; }
        #section-select { margin-bottom: 20px; }
        
        .box-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .box {
            width: 18%;
            background-color: #fff;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .box h3 {
            color: #343a40;
            margin-bottom: 15px;
        }

        .box button {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .box button:hover {
            background-color: #0056b3;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .btn-submit {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
        }

        .btn-submit:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header class="header">
        <img src="logo.png" alt="ICT Division Logo" class="logo" style="max-width: 100px;">
        <h1>ICT Division Fellowship Management</h1>
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="Fellowships.php">Fellowship</a></li>
                <li class="nav-item"><a class="nav-link" href="apply.html">Apply</a></li>
                <li class="nav-item"><a class="nav-link" href="status.html">Application Status</a></li>
                <li class="nav-item"><a class="nav-link" href="mentorship.html">Mentorship</a></li>
                <li class="nav-item"><a class="nav-link" href="projects.html">Projects</a></li>
                <li class="nav-item"><a class="nav-link" href="courses.html">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.html">Admin</a></li>
                <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="login.html">Login</a></li>
            </ul>
        </nav>
    </header>

    <div class="container main-content">
        <h2 class="text-center">Fellowship Programs</h2>

        <?php if (!empty($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

        <div class="form-group">
            <label for="section-select">Choose a section:</label>
            <select id="section-select" class="form-control" onchange="showSection(this.value)">
                <option value="">Select a section</option>
                <option value="phd">PhD Fellowship</option>
                <option value="mph">MPhil Fellowship</option>
                <option value="funding">Funding</option>
                <option value="installment">Installment</option>
                <option value="fellowship">Insert Fellowship</option>
            </select>
        </div>

        <div class="box-container">
            <!-- Fellowship Box -->
            <div class="box">
                <h3>Insert Fellowship</h3>
                <button onclick="showSection('fellowship')">Go</button>
            </div>

            <!-- PhD Fellowship Box -->
            <div class="box">
                <h3>PhD Fellowship</h3>
                <button onclick="showSection('phd')">Go</button>
            </div>

            <!-- MPhil Fellowship Box -->
            <div class="box">
                <h3>MPhil Fellowship</h3>
                <button onclick="showSection('mph')">Go</button>
            </div>

            <!-- Funding Box -->
            <div class="box">
                <h3>Funding</h3>
                <button onclick="showSection('funding')">Go</button>
            </div>

            <!-- Installment Box -->
            <div class="box">
                <h3>Installment</h3>
                <button onclick="showSection('installment')">Go</button>
            </div>
        </div>

        <!-- Fellowship Section -->
        <div class="form-section" id="fellowship-section">
            <h3>Insert Fellowship</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="fellowship_id">Fellowship ID</label>
                    <input type="text" class="form-control" id="fellowship_id" name="fellowship_id" required>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>
                <div class="form-group">
                    <label for="start">Start Date</label>
                    <input type="date" class="form-control" id="start" name="start" required>
                </div>
                <div class="form-group">
                    <label for="end">End Date</label>
                    <input type="date" class="form-control" id="end" name="end" required>
                </div>
                <div class="form-group">
                    <label for="fund_id">Fund ID</label>
                    <input type="text" class="form-control" id="fund_id" name="fund_id" required>
                </div>
                <div class="form-group">
                    <label for="course_id">Course ID</label>
                    <input type="text" class="form-control" id="course_id" name="course_id" required>
                </div>
                <div class="form-group">
                    <label for="u_id">User ID</label>
                    <input type="text" class="form-control" id="u_id" name="u_id" required>
                </div>
                <button type="submit" class="btn-submit" name="submit_fellowship">Submit</button>
            </form>
        </div>

        <!-- PhD Fellowship Form -->
        <div id="phd-section" class="form-section">
            <h3>PhD Fellowship Application</h3>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Applicant Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="field_of_study" class="form-control" placeholder="Field of Study" required>
                </div>
                <div class="form-group">
                    <input type="text" name="institution" class="form-control" placeholder="Institution" required>
                </div>
                <button type="submit" name="submit_phd" class="btn-submit">Submit</button>
            </form>
        </div>

        <!-- MPhil Fellowship Form -->
        <div id="mph-section" class="form-section">
            <h3>MPhil Fellowship Application</h3>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Applicant Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="field_of_study" class="form-control" placeholder="Field of Study" required>
                </div>
                <div class="form-group">
                    <input type="text" name="institution" class="form-control" placeholder="Institution" required>
                </div>
                <button type="submit" name="submit_mph" class="btn-submit">Submit</button>
            </form>
        </div>

        <!-- Funding Form -->
           <!-- Funding Form -->
           <div id="funding-section" class="form-section">
            <h3>Funding Details</h3>
            <form method="POST" class="form">
                <div class="form-group">
                    <input type="number" name="fund_id" class="form-control" placeholder="Funding ID" required>
                </div>
                <div class="form-group">
                    <input type="number" name="tot_amt" class="form-control" placeholder="Total Amount" required>
                </div>
                <div class="form-group">
                    <input type="text" name="date_of_proposal" class="form-control" placeholder="Date of Proposal (mm/dd/yyyy)" required>
                </div>
                
                <div class="form-group">
                    <input type="number" name="phd_id" class="form-control" placeholder="PhD ID (phd_id)" required>
                </div>
                <button type="submit" name="submit_funding" class="btn btn-success">Submit</button>
            </form>
        </div>

        <!-- Installment Form -->
        <div id="installment-section" class="form-section">
            <h3>Installment Details</h3>
            <form method="POST" class="form">
                <div class="form-group">
                    <input type="number" name="fellow_id" class="form-control" placeholder="Fellow ID" required>
                </div>
                <div class="form-group">
                    <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                </div>
                <div class="form-group">
                    <input type="date" name="due_date" class="form-control" placeholder="Due Date" required>
                </div>
                <button type="submit" name="submit_installment" class="btn btn-success">Submit</button>
            </form>
        </div>

    <footer class="footer">
        <p>Copyright &copy; 2024 ICT Division. All Rights Reserved.</p>
    </footer>

    <script>
        function showSection(section) {
            // Hide all sections
            const sections = document.querySelectorAll('.form-section');
            sections.forEach(sec => sec.style.display = 'none');

            // Show selected section
            if (section) {
                document.getElementById(section + '-section').style.display = 'block';
            }
        }
    </script>
</body>
</html>

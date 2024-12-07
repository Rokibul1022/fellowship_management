
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "fellowship_management";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dropdowns
$fellowships = $conn->query("SELECT name FROM fellowship");
$projects = $conn->query("SELECT project_id, title, duration FROM project");
$departments = $conn->query("SELECT dept_id, dept_name FROM department");
$users = $conn->query("SELECT u_id, name FROM users");
// Fetch all users to populate the dropdown
$user_sql = "SELECT u_id, name FROM users";
$user_result = $conn->query($user_sql);

// Initialize variables
$search_results = [];
$fellowship_results = [];
$project_results = [];
$faculty_results = [];
$applications_results = [];
// Initialize variables
$scholarships = [];
$selected_u_id = null;
// Fellowship Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_fellowship'])) {
    $fellowship_name = trim($_POST['name']);
    $filter_year = isset($_POST['filter_year']) ? intval($_POST['filter_year']) : null;

    if (!empty($fellowship_name)) {
        $query = "SELECT fellow.name AS fellow_name, fellowship.name AS name, fellowship.start, fellowship.end
                  FROM fellow
                  JOIN fellowship ON fellow.fellowship_id = fellowship.fellowship_id
                  WHERE fellowship.name = ?";
        if ($filter_year) {
            $query .= " AND YEAR(fellowship.start) = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $fellowship_name, $filter_year);
        } else {
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $fellowship_name);
        }
        $stmt->execute();
        $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// PhD and Fellowship Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_fellowship_phd'])) {
    $fellowship_name = trim($_POST['name']);

    if (!empty($fellowship_name)) {
        $query = "SELECT fellowship.name AS fellowship_name, 
                         funding.tot_amt AS total_amount, 
                         funding.date_of_proposal AS funding_date, 
                         phd.name AS phd_name, 
                         phd.email AS phd_email, 
                         phd.field_of_study AS phd_field, 
                         phd.institution AS institution
                  FROM fellowship
                  JOIN funding ON fellowship.fund_id = funding.fund_id
                  JOIN phd ON funding.phd_id = phd.id
                  WHERE fellowship.name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $fellowship_name);
        $stmt->execute();
        $fellowship_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Project Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_project'])) {
    $project_title = trim($_POST['title']);
    $filter_duration = isset($_POST['filter_duration']) && !empty($_POST['filter_duration']) ? intval($_POST['filter_duration']) : null;

    if (!empty($project_title)) {
        $query = "SELECT project.title AS project_title, 
                         project.start_date AS project_start, 
                         project.end_date AS project_end, 
                         project.duration AS project_duration,
                         fellowship.name AS fellowship_name,
                         users.name AS user_name,
                         users.email AS user_email,
                         users.phone AS user_phone,
                         users.district AS user_district,
                         users.department AS user_department
                  FROM project
                  JOIN fellowship ON project.fellowship_id = fellowship.fellowship_id
                  JOIN users ON fellowship.u_id = users.u_id
                  WHERE project.title = ?";
        if ($filter_duration) {
            $query .= " AND project.duration = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $project_title, $filter_duration);
        } else {
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $project_title);
        }
        $stmt->execute();
        $project_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Faculty Search by Department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_faculty'])) {
    $dept_id = isset($_POST['dept_id']) ? intval($_POST['dept_id']) : null;

    if (!empty($dept_id)) {
        $query = "SELECT faculty.name AS faculty_name, 
                         faculty.mobile AS faculty_mobile,
                         faculty.start_date AS faculty_start,
                         faculty.end_date AS faculty_end,
                         faculty.dept_head AS department_head,
                         department.dept_name AS department_name,
                         department.building AS department_building,
                         department.location AS department_location
                  FROM faculty
                  JOIN department ON faculty.dept_id = department.dept_id
                  WHERE faculty.dept_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $dept_id);
        $stmt->execute();
        $faculty_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

// Users and Applications Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_applications'])) {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    if (!empty($user_id)) {
        $query = "SELECT applications.application_id, 
                         applications.degree, 
                         applications.institution, 
                         applications.graduation_year, 
                         applications.experience, 
                         applications.motivation, 
                         users.name AS user_name, 
                         users.email AS user_email, 
                         users.phone AS user_phone, 
                         users.district AS user_district, 
                         users.department AS user_department
                  FROM applications
                  JOIN users ON applications.u_id = users.u_id
                  WHERE applications.u_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $applications_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}
// Handle form submission to fetch scholarships
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['u_id'])) {
    $selected_u_id = intval($_POST['u_id']);
    
    // Fetch scholarship applications for the selected user
    $scholarship_sql = "SELECT s.name, s.institute, s.topic, s.major_in, s.email, s.phone 
                        FROM scholarship_applications s 
                        WHERE s.u_id = ?";
    $stmt = $conn->prepare($scholarship_sql);
    $stmt->bind_param("i", $selected_u_id);
    $stmt->execute();
    $scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch data for fellowship dropdown
$fellowships = $conn->query("SELECT fellowship_id, name FROM fellowship");

if (!$fellowships) {
    die("Error fetching fellowships: " . $conn->error);
}


// Initialize variable for enrollment results
$enrollment_results = [];

// Handle form submission to fetch enrollment data for a manually entered fellowship ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fellowship_id'])) {
    $fellowship_id = intval($_POST['fellowship_id']);
    $query = "
        SELECT enroll.id AS enrollment_id,
               enroll.course_id,
               enroll.semester,
               enroll.year,
               enroll.mark,
               fellowship.name AS fellowship_name,
               courses.title AS course_title
        FROM enroll
        JOIN fellowship ON enroll.fellowship_id = fellowship.fellowship_id
        JOIN courses ON enroll.course_id = courses.course_id
        WHERE enroll.fellowship_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $fellowship_id);
    $stmt->execute();
    $enrollment_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 50px auto;
            max-width: 900px;
        }
        .search-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .form-label {
            font-weight: bold;
            color: #555;
        }
        .form-select, .form-control {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
    </style>
</head>
<body>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 50px auto;
            max-width: 900px;
        }
        .search-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .form-label {
            font-weight: bold;
            color: #555;
        }
        .form-select, .form-control {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        .alert {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Fellowship Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="index.html">Home</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Fellowship Search -->
    <div class="search-container">
        <h2>Search Fellows Based on Fellowship History</h2>
        <form method="POST">
            <div>
                <label for="name" class="form-label">Fellowship Name</label>
                <select name="name" id="name" class="form-select" required>
                    <option value="">Select Fellowship</option>
                    <?php while ($row = $fellowships->fetch_assoc()): ?>
                        <option value="<?= $row['name']; ?>"><?= $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="filter_year" class="form-label">Filter by Year</label>
                <input type="number" name="filter_year" id="filter_year" class="form-control" placeholder="Enter Year">
            </div>
            <button type="submit" name="search_fellowship" class="btn">Search Fellows</button>
        </form>
    </div>

    <!-- Fellowship and PhD Search -->
    <div class="search-container">
        <h2>Search Fellowships and Related PhD Researchers</h2>
        <form method="POST">
            <div>
                <label for="name" class="form-label">Fellowship Name</label>
                <select name="name" id="name" class="form-select" required>
                    <option value="">Select Fellowship</option>
                    <?php foreach ($fellowships as $row): ?>
                        <option value="<?= $row['name']; ?>"><?= $row['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="search_fellowship_phd" class="btn">Search Fellowship and PhD</button>
        </form>
    </div>

    <!-- Project Search -->
    <div class="search-container">
        <h2>Search Projects and Related Fellowship Details</h2>
        <form method="POST">
            <div>
                <label for="title" class="form-label">Project Title</label>
                <select name="title" id="title" class="form-select" required>
                    <option value="">Select Project</option>
                    <?php while ($row = $projects->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['title']); ?>">
                            <?= htmlspecialchars($row['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="filter_duration" class="form-label">Filter by Duration (Months)</label>
                <select name="filter_duration" id="filter_duration" class="form-select">
                    <option value="">Select Duration</option>
                    <?php
                    $projects->data_seek(0);
                    while ($row = $projects->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($row['duration']); ?>">
                            <?= htmlspecialchars($row['duration']) . " months"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="search_project" class="btn">Search Projects</button>
        </form>
    </div>

    <!-- Faculty Search -->
    <div class="search-container">
        <h2>Search Faculty by Department</h2>
        <form method="POST">
            <div>
                <label for="dept_id" class="form-label">Select Department</label>
                <select name="dept_id" id="dept_id" class="form-select" required>
                    <option value="">Select Department</option>
                    <?php while ($row = $departments->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['dept_id']); ?>">
                            <?= htmlspecialchars($row['dept_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="search_faculty" class="btn">Search Faculty</button>
        </form>
    </div>
    <div class="container">
    <!-- Applications Search -->
    <div class="search-container">
        <h2>Search Applications by User</h2>
        <form method="POST">
            <div>
                <label for="user_id" class="form-label">Select User:</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">Select User</option>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['u_id']); ?>">
                            <?= htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="search_applications" class="btn">Search Applications</button>
        </form>
    </div>

    <div class="container">
    <div class="search-container">
        <h2>Search Enrollments by Fellowship ID</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="fellowship_id" class="form-label">Enter Fellowship ID</label>
                <input type="number" name="fellowship_id" id="fellowship_id" class="form-control" required>
            </div>
            <button type="submit" class="btn">Search Enrollments</button>
        </form>
                    </div>


    <!-- Scholarship Applications Search Section -->
<div class="search-container">
    <h2>Search Scholarship Applications</h2>
    <form method="POST" action="search.php">
        <div>
            <label for="u_id" class="form-label">Select User:</label>
            <select name="u_id" id="u_id" class="form-select" required>
                <option value="" disabled selected>Select a user</option>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['u_id']); ?>" <?= $selected_u_id == $row['u_id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($row['u_id']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn">Search Applications</button>
    </form>
</div>


    <!-- Fellowship Results -->
    <?php if (!empty($search_results)): ?>
        <div class="search-container">
            <h2>Search Results for Fellows</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Fellow Name</th>
                    <th>Fellowship Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($search_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fellow_name']); ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['start']); ?></td>
                        <td><?= htmlspecialchars($row['end']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Fellowship and PhD Results -->
    <?php if (!empty($fellowship_results)): ?>
        <div class="search-container">
            <h2>Search Results for Fellowships and Related PhD Researchers</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Fellowship Name</th>
                    <th>Total Funding Amount</th>
                    <th>Funding Proposal Date</th>
                    <th>PhD Researcher Name</th>
                    <th>Email</th>
                    <th>Field of Study</th>
                    <th>Institution</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($fellowship_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fellowship_name']); ?></td>
                        <td><?= htmlspecialchars($row['total_amount']); ?></td>
                        <td><?= htmlspecialchars($row['funding_date']); ?></td>
                        <td><?= htmlspecialchars($row['phd_name']); ?></td>
                        <td><?= htmlspecialchars($row['phd_email']); ?></td>
                        <td><?= htmlspecialchars($row['phd_field']); ?></td>
                        <td><?= htmlspecialchars($row['institution']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Project Results -->
    <?php if (!empty($project_results)): ?>
        <div class="search-container">
            <h2>Search Results for Projects</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Project Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration (Months)</th>
                    <th>Fellowship Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>District</th>
                    <th>Department</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($project_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['project_title']); ?></td>
                        <td><?= htmlspecialchars($row['project_start']); ?></td>
                        <td><?= htmlspecialchars($row['project_end']); ?></td>
                        <td><?= htmlspecialchars($row['project_duration']); ?></td>
                        <td><?= htmlspecialchars($row['fellowship_name']); ?></td>
                        <td><?= htmlspecialchars($row['user_name']); ?></td>
                        <td><?= htmlspecialchars($row['user_email']); ?></td>
                        <td><?= htmlspecialchars($row['user_phone']); ?></td>
                        <td><?= htmlspecialchars($row['user_district']); ?></td>
                        <td><?= htmlspecialchars($row['user_department']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Faculty Results -->
    <?php if (!empty($faculty_results)): ?>
        <div class="search-container">
            <h2>Search Results for Faculty</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Faculty Name</th>
                    <th>Mobile</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Department Head</th>
                    <th>Department Name</th>
                    <th>Building</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($faculty_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['faculty_name']); ?></td>
                        <td><?= htmlspecialchars($row['faculty_mobile']); ?></td>
                        <td><?= htmlspecialchars($row['faculty_start']); ?></td>
                        <td><?= htmlspecialchars($row['faculty_end']); ?></td>
                        <td><?= htmlspecialchars($row['department_head']); ?></td>
                        <td><?= htmlspecialchars($row['department_name']); ?></td>
                        <td><?= htmlspecialchars($row['department_building']); ?></td>
                        <td><?= htmlspecialchars($row['department_location']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
     <!-- Applications Results -->
     <?php if (!empty($applications_results)): ?>
        <div class="search-container">
            <h2>Search Results for Applications</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Graduation Year</th>
                    <th>Experience</th>
                    <th>Motivation</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>District</th>
                    <th>Department</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($applications_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['application_id']); ?></td>
                        <td><?= htmlspecialchars($row['degree']); ?></td>
                        <td><?= htmlspecialchars($row['institution']); ?></td>
                        <td><?= htmlspecialchars($row['graduation_year']); ?></td>
                        <td><?= htmlspecialchars($row['experience']); ?></td>
                        <td><?= htmlspecialchars($row['motivation']); ?></td>
                        <td><?= htmlspecialchars($row['user_name']); ?></td>
                        <td><?= htmlspecialchars($row['user_email']); ?></td>
                        <td><?= htmlspecialchars($row['user_phone']); ?></td>
                        <td><?= htmlspecialchars($row['user_district']); ?></td>
                        <td><?= htmlspecialchars($row['user_department']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
        
<!-- Scholarship Results -->
<?php if (!empty($scholarships)): ?>
    <div class="search-container">
        <h2>Scholarship Applications</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Institute</th>
                    <th>Topic</th>
                    <th>Major In</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scholarships as $scholarship): ?>
                    <tr>
                        <td><?= htmlspecialchars($scholarship['name']); ?></td>
                        <td><?= htmlspecialchars($scholarship['institute']); ?></td>
                        <td><?= htmlspecialchars($scholarship['topic']); ?></td>
                        <td><?= htmlspecialchars($scholarship['major_in']); ?></td>
                        <td><?= htmlspecialchars($scholarship['email']); ?></td>
                        <td><?= htmlspecialchars($scholarship['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['u_id'])): ?>
        <div class="search-container">
            <p class="no-data">No scholarship applications found for the selected user.</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

 <!-- Enrollment Results -->
 <?php if (!empty($enrollment_results)): ?>
        <div class="search-container">
            <h2>Enrollment Results</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Enrollment ID</th>
                    <th>Course ID</th>
                    <th>Course Title</th>
                    <th>Semester</th>
                    <th>Year</th>
                    <th>Mark</th>
                    <th>Fellowship Name</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($enrollment_results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['enrollment_id']); ?></td>
                        <td><?= htmlspecialchars($row['course_id']); ?></td>
                        <td><?= htmlspecialchars($row['course_title']); ?></td>
                        <td><?= htmlspecialchars($row['semester']); ?></td>
                        <td><?= htmlspecialchars($row['year']); ?></td>
                        <td><?= htmlspecialchars($row['mark']); ?></td>
                        <td><?= htmlspecialchars($row['fellowship_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-danger">No enrollments found for the entered fellowship ID.</div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

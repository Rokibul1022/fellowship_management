<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'db.php';
session_start();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}

// Handle login authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_phone'], $_POST['password'])) {
    $email_phone = htmlspecialchars(trim($_POST['email_phone']));
    $password = $_POST['password'];

    // Query to authenticate user
    $sql = "SELECT u_id, name, email, phone, password FROM users WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_phone, $email_phone);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['u_id'] = $user['u_id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email/phone or password.";
        header("Location: login.html");
        exit();
    }
}

// Redirect to login if the session is not set
if (!isset($_SESSION['u_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch logged-in user's data
$user_id = $_SESSION['u_id'];
$sql = "SELECT u_id, name, email, phone, district, department FROM users WHERE u_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user_data) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}

// Directory for profile pictures
$upload_dir = "uploads/profile_pictures/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
$profile_picture = $upload_dir . "user_" . $user_id . ".jpg";

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        move_uploaded_file($tmp_name, $profile_picture);
    }
}
// Handle profile updates (name, email, phone, district, department)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $field_name = htmlspecialchars($_POST['field_name']);
    $field_value = htmlspecialchars($_POST['field_value']);

    $allowed_fields = ['name', 'email', 'phone', 'district', 'department'];
    if (in_array($field_name, $allowed_fields)) {
        $update_sql = "UPDATE users SET $field_name = ? WHERE u_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $field_value, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = ucfirst($field_name) . " updated successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to update " . ucfirst($field_name);
        }
        $stmt->close();
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $update_password_sql = "UPDATE users SET password = ? WHERE u_id = ?";
    $stmt = $conn->prepare($update_password_sql);
    $stmt->bind_param("si", $new_password, $user_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = "Password updated successfully!";
}

// Fetch user's fellowships
$fellowship_sql = "SELECT fellowship_id, name, description, start, end FROM fellowship WHERE u_id = ?";
$fellowship_stmt = $conn->prepare($fellowship_sql);
$fellowship_stmt->bind_param("i", $user_id);
$fellowship_stmt->execute();
$fellowships = $fellowship_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$fellowship_stmt->close();

// Fetch user's applications
$application_sql = "SELECT application_id, degree, institution, graduation_year, experience, motivation 
                    FROM applications WHERE u_id = ?";
$app_stmt = $conn->prepare($application_sql);
$app_stmt->bind_param("i", $user_id);
$app_stmt->execute();
$applications = $app_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$app_stmt->close();

//Fetch course information through fellowship
$course_sql = "SELECT c.title, c.credit_hour 
               FROM courses c
               INNER JOIN fellowship f ON f.course_id = c.course_id
               WHERE f.u_id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param("i", $user_id);
$course_stmt->execute();
$courses = $course_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$course_stmt->close();

// Fetch project information through fellowship
$project_sql = "SELECT p.title, p.start_date, p.end_date, p.duration 
                FROM project p
                INNER JOIN fellowship f ON f.fellowship_id = p.fellowship_id
                WHERE f.u_id = ?";
$project_stmt = $conn->prepare($project_sql);
$project_stmt->bind_param("i", $user_id);
$project_stmt->execute();
$projects = $project_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$project_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Poppins", sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .navbar { background-color: #1f2937; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; font-weight: 600; margin: 0 15px; }
        .navbar a:hover { color: #f39c12; }
        .dashboard { max-width: 900px; margin: 30px auto; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); }
        h2, h3 { text-align: center; color: #333; }
        .profile-section img { max-width: 100px; border-radius: 50%; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        table th { background-color: #f7f7f7; }
        .form-group { margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; }
        .btn-primary { background-color: #3498db; color: #fff; }
        .btn-primary:hover { background-color: #2980b9; }
        .btn-danger { background-color: #e74c3c; color: #fff; }
        .btn-danger:hover { background-color: #c0392b; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="dashboard.php?action=logout" class="btn-danger">Logout</a>
    </div>

    <div class="dashboard">
        <h2>Welcome, <?= htmlspecialchars($user_data['name']); ?>!</h2>

         <!-- Profile Section -->
         <div class="profile-section">
            <h3>Your Profile</h3>
            <?php if (file_exists($profile_picture)): ?>
                <img src="<?= htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <button type="submit" class="btn btn-primary mt-2">Upload</button>
            </form>
        </div>

       <!-- Editable Fields -->
       <div class="section">
            <h3>Your Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($user_data['name']); ?> 
                <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-field="name" data-value="<?= htmlspecialchars($user_data['name']); ?>">Edit</button>
            </p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']); ?> 
                <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-field="email" data-value="<?= htmlspecialchars($user_data['email']); ?>">Edit</button>
            </p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user_data['phone']); ?> 
                <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-field="phone" data-value="<?= htmlspecialchars($user_data['phone']); ?>">Edit</button>
            </p>
            <p><strong>District:</strong> <?= htmlspecialchars($user_data['district']); ?> 
                <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-field="district" data-value="<?= htmlspecialchars($user_data['district']); ?>">Edit</button>
            </p>
            <p><strong>Department:</strong> <?= htmlspecialchars($user_data['department']); ?> 
                <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-field="department" data-value="<?= htmlspecialchars($user_data['department']); ?>">Edit</button>
            </p>
        </div>


       

        
       <!-- Profile Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="field_name" id="field_name">
                        <label for="field_value">New Value</label>
                        <input type="text" name="field_value" id="field_value" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                 <!-- Change Password -->
         <div class="section">
            <h3>Change Password</h3>
            <form method="POST">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
                <div class="password-strength" id="password-strength"></div>
                <button type="submit" name="update_password" class="btn btn-primary mt-2">Update Password</button>
            </form>
        </div>
    </div>





        <div class="section">
            <h3>Your Courses</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Credit Hour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= htmlspecialchars($course['title']); ?></td>
                            <td><?= htmlspecialchars($course['credit_hour']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>



        <div class="section">
            <h3>Your Fellowships</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fellowships as $fellowship): ?>
                        <tr>
                            <td><?= htmlspecialchars($fellowship['name']); ?></td>
                            <td><?= htmlspecialchars($fellowship['description']); ?></td>
                            <td><?= htmlspecialchars($fellowship['start']); ?></td>
                            <td><?= htmlspecialchars($fellowship['end']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        
        <div class="section">
            <h3>Your Projects</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['title']); ?></td>
                            <td><?= htmlspecialchars($project['start_date']); ?></td>
                            <td><?= htmlspecialchars($project['end_date']); ?></td>
                            <td><?= htmlspecialchars($project['duration']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>



        <div class="section">
            <h3>Your Applications</h3>
            <table>
                <thead>
                    <tr>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Graduation Year</th>
                        <th>Experience</th>
                        <th>Motivation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['degree']); ?></td>
                            <td><?= htmlspecialchars($app['institution']); ?></td>
                            <td><?= htmlspecialchars($app['graduation_year']); ?></td>
                            <td><?= htmlspecialchars($app['experience']); ?></td>
                            <td><?= htmlspecialchars($app['motivation']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
        
        // Handle password strength
        const passwordInput = document.getElementById('new_password');
        const strengthText = document.getElementById('password-strength');

        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            if (password.length < 6) {
                strengthText.textContent = 'Weak';
                strengthText.style.color = 'red';
            } else if (password.length < 10) {
                strengthText.textContent = 'Moderate';
                strengthText.style.color = 'orange';
            } else {
                strengthText.textContent = 'Strong';
                strengthText.style.color = 'green';
            }
        });

        // Handle profile edit modal
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const field = button.getAttribute('data-field');
                const value = button.getAttribute('data-value');
                document.getElementById('field_name').value = field;
                document.getElementById('field_value').value = value;
            });
        });
    </script>
    
    </div>
   
</body>
</html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fellowship_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Save/Update User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_user') {
    error_log("Save user action triggered"); // Debug line
    $id = isset($_POST['u_id']) && !empty($_POST['u_id']) ? intval($_POST['u_id']) : null;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate required fields
    if (!$name || !$email || !$phone || !$district || !$department || !$password) {
        echo json_encode(["error" => true, "message" => "All fields are required!"]);
        exit();
    }

    try {
        if ($id === null) {
            // Insert operation (New User)
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, district, department, password) VALUES (?, ?, ?, ?, ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt->bind_param("ssssss", $name, $email, $phone, $district, $department, $hashedPassword);
            $message = "User added successfully!";
        } else {
            // Update operation (Existing User)
            // Check if the password needs to be updated or kept as is
            $stmt = $conn->prepare("SELECT password FROM users WHERE u_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();

            if ($existing && password_verify($password, $existing['password'])) {
                $hashedPassword = $existing['password']; // Keep the existing hashed password
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the new password
            }

            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, district=?, department=?, password=? WHERE u_id=?");
            $stmt->bind_param("ssssssi", $name, $email, $phone, $district, $department, $hashedPassword, $id);
            $message = "User updated successfully!";
        }

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["message" => $message]);
        } else {
            echo json_encode(["error" => true, "message" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(["error" => true, "message" => "Error: " . $e->getMessage()]);
    }

    $conn->close();
    exit();
}

// Handle Delete User
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete_user') {
    $id = intval($_GET['u_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE u_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => true, "message" => "Error deleting user"]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Handle Get User for Edit
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'get_user') {
    $id = intval($_GET['u_id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE u_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo json_encode($user);
    $stmt->close();
    $conn->close();
    exit();
}

// Handle Load Users
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'load_users') {
    $result = $conn->query("SELECT * FROM users");
    $users = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($users);
    $conn->close();
    exit();
}
// Before exiting, log the request
error_log(print_r($_POST, true));

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - CRUD Operations</title>
    <style>
        /* Your CSS Styles Here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .navbar a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input, select, button {
            padding: 10px;
            font-size: 1em;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f4f4f9;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #message {
            margin-top: 20px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="Fellowships.html">Fellowship</a>
    <a href="apply.html">Apply</a>
    <a href="status.html">Application Status</a>
    <a href="mentorship.html">Mentorship</a>
    <a href="projects.html">Projects</a>
    <a href="courses.html">Courses</a>
    <a href="scholarship.php">Scholarship</a>
    <a href="admin.html">Admin</a>
    <a href="user_crud.php">User Management</a>
</div>

<div class="container">
    <h1>User Management - CRUD Operations</h1>
    <form id="userForm">
        <label for="u_id">u_id:</label>
        <input type="text" id="u_id" name="u_id">

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required>

        <label for="district">District:</label>
        <select id="district" name="district" required>
            <option value="Dhaka">Dhaka</option>
            <option value="Chattogram">Chattogram</option>
            <option value="Khulna">Khulna</option>
            <option value="Sylhet">Sylhet</option>
            <option value="Rajshahi">Rajshahi</option>
            <option value="Barisal">Barisal</option>
            <option value="Rangpur">Rangpur</option>
            <option value="Mymensingh">Mymensingh</option>
        </select>

        <label for="department">Department:</label>
        <select id="department" name="department" required>
            <option value="CSE">CSE</option>
            <option value="EEE">EEE</option>
            <option value="BBA">BBA</option>
            <option value="ME">ME</option>
            <option value="Civil">Civil</option>
        </select>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="button" onclick="saveUser()">Save User</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>u_id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>District</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <!-- Dynamic User Data Here -->
        </tbody>
    </table>

    <div id="message"></div>
</div>

<script>
// JavaScript Code
document.addEventListener("DOMContentLoaded", loadUsers);

function loadUsers() {
    fetch("?action=load_users")
        .then(response => response.json())
        .then(data => {
            const userTable = document.getElementById("userTable");
            userTable.innerHTML = "";
            data.forEach(user => {
                const row = `<tr>
                    <td>${user.u_id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.phone}</td>
                    <td>${user.district}</td>
                    <td>${user.department}</td>
                    <td>
                        <button onclick="editUser(${user.u_id})">Edit</button>
                        <button onclick="deleteUser(${user.u_id})">Delete</button>
                    </td>
                </tr>`;
                userTable.innerHTML += row;
            });
        })
        .catch(error => console.error('Error loading users:', error));
}

function saveUser() {
    const form = document.getElementById("userForm");
    const formData = new FormData(form);
    formData.append("action", "save_user");

    const isNewUser = document.getElementById("u_id").value === "";
    if (isNewUser) {
        formData.delete("u_id");
    }

    fetch("user_crud.php", { // Ensure this file is correct
        method: "POST",
        body: formData,
    })
        .then(response => response.json())
        .then(result => {
            console.log("Server response:", result);
            const messageElement = document.getElementById("message");

            if (result.message) {
                messageElement.innerText = result.message;
                messageElement.style.color = "green";

                if (result.message.includes("successfully")) {
                    form.reset();
                    document.getElementById("u_id").value = "";
                    loadUsers();
                }
            } else if (result.error) {
                messageElement.innerText = result.message;
                messageElement.style.color = "red";
            } else {
                messageElement.innerText = "Unexpected error occurred.";
                messageElement.style.color = "red";
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            const messageElement = document.getElementById("message");
            messageElement.innerText = "An error occurred. Check console for details.";
            messageElement.style.color = "red";
        });
}



function editUser(id) {
    fetch(`?action=get_user&u_id=${id}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById("u_id").value = user.u_id;
            document.getElementById("name").value = user.name;
            document.getElementById("email").value = user.email;
            document.getElementById("phone").value = user.phone;
            document.getElementById("district").value = user.district;
            document.getElementById("department").value = user.department;
        })
        .catch(error => console.error('Error editing user:', error));
}

function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        fetch(`?action=delete_user&u_id=${id}`)
            .then(response => response.json())
            .then(result => {
                document.getElementById("message").innerText = result.message;
                loadUsers(); // Reload the user list after deletion
            })
            .catch(error => console.error("Error deleting user:", error));
    }
}
</script>
</body>
</html>






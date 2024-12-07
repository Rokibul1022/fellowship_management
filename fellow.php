<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$database = "fellowship_management";

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding a new fellow
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fellow'])) {
    $name = $_POST['fellow_name'];
    $fellowship_id = $_POST['fellowship_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO fellow (name, fellowship_id) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Debug prepare statement
    }
    $stmt->bind_param("si", $name, $fellowship_id);

    if ($stmt->execute()) {
        $success_message = "Fellow successfully added!";
    } else {
        die("Execute failed: " . $stmt->error); // Debug execute errors
    }
    $stmt->close();
}

// Handle delete request for a fellow
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_fellow'])) {
    $fellow_id = $_POST['fellow_id'];

    // Prepare and execute SQL statement to delete the fellow
    $stmt = $conn->prepare("DELETE FROM fellow WHERE fellow_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $fellow_id);

    if ($stmt->execute()) {
        $success_message = "Fellow successfully deleted!";
    } else {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

// Fetch fellows and fellowships for displaying in the frontend
$fellows = $conn->query("SELECT fellow.fellow_id, fellow.name, fellowship.name AS fellowship_name 
    FROM fellow 
    INNER JOIN fellowship ON fellow.fellowship_id = fellowship.fellowship_id");

$fellowships = $conn->query("SELECT fellowship_id, name FROM fellowship");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fellow Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Fellow_crud</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.html">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="mb-4">Fellow Management</h2>

    <!-- Success/Error Message -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message; ?></div>
    <?php endif; ?>

    <!-- Add Fellow Form -->
    <div class="card mb-4">
        <div class="card-header">Add Fellow</div>
        <div class="card-body">
            <form method="POST" id="addFellowForm">
                <div class="mb-3">
                    <label for="fellow_name" class="form-label">Fellow Name</label>
                    <input type="text" name="fellow_name" id="fellow_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="fellowship_id" class="form-label">Fellowship</label>
                    <select name="fellowship_id" id="fellowship_id" class="form-select" required>
                        <option value="">Select Fellowship</option>
                        <?php while ($row = $fellowships->fetch_assoc()): ?>
                            <option value="<?= $row['fellowship_id']; ?>"><?= $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="add_fellow" class="btn btn-primary">Add Fellow</button>
            </form>
        </div>
    </div>

    <!-- Fellows List -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Fellow ID</th>
            <th>Fellow Name</th>
            <th>Fellowship</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="fellowTableBody">
        <?php while ($row = $fellows->fetch_assoc()): ?>
            <tr>
                <td><?= $row['fellow_id']; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['fellowship_name']; ?></td>
                <td>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['fellow_id']; ?>">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const fellowId = this.getAttribute('data-id');

                if (confirm("Are you sure you want to delete this fellow?")) {
                    // Create a form to submit the delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'fellow_id';
                    input.value = fellowId;
                    form.appendChild(input);

                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_fellow';
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
</body>
</html>

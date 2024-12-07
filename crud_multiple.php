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


/// Handle CREATE operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $fellowship_id = intval($_POST['fellowship_id'] ?? 0); // User inputs this
    $fellowship_name = trim($_POST['fellowship_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start = trim($_POST['start'] ?? '');
    $end = trim($_POST['end'] ?? '');
    $fund_id = intval($_POST['fund_id'] ?? 0); // User selects from dropdown
    $course_id = intval($_POST['course_id'] ?? 0);
    $user_id = intval($_POST['user_id'] ?? 0);
    $fellow_names = array_filter(explode(',', trim($_POST['fellow_names'] ?? '')));
    $project_id = intval($_POST['project_id'] ?? 0); // User inputs this
    $project_title = trim($_POST['project_title'] ?? '');
    $project_duration = intval($_POST['project_duration'] ?? 0);

    // Validate all required fields
    if (
        !$fellowship_id || !$fellowship_name || !$description || !$start || !$end || !$fund_id ||
        !$course_id || !$user_id || empty($fellow_names) || !$project_id || !$project_title || !$project_duration
    ) {
        $message = "Error: All fields, including project ID, are required.";
    } else {
        // Check if fund_id exists in the funding table
        $stmt = $conn->prepare("SELECT fund_id FROM funding WHERE fund_id = ?");
        $stmt->bind_param("i", $fund_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $message = "Error: The selected fund ID does not exist in the funding table.";
        } else {
            $stmt->close();

            // Step 1: Insert into fellowship table
            $stmt = $conn->prepare("INSERT INTO fellowship (fellowship_id, name, description, start, end, fund_id, course_id, u_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssii", $fellowship_id, $fellowship_name, $description, $start, $end, $fund_id, $course_id, $user_id);

            if ($stmt->execute()) {
                $stmt->close();

                // Step 2: Insert fellows into the fellow table
                foreach ($fellow_names as $fellow_name) {
                    $stmt = $conn->prepare("INSERT INTO fellow (name, fellowship_id) VALUES (?, ?)");
                    $stmt->bind_param("si", trim($fellow_name), $fellowship_id);
                    $stmt->execute();
                    $stmt->close();
                }

                // Step 3: Insert project associated with the fellowship
                $stmt = $conn->prepare("INSERT INTO project (project_id, title, start_date, end_date, duration, fellowship_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssii", $project_id, $project_title, $start, $end, $project_duration, $fellowship_id);

                if ($stmt->execute()) {
                    $message = "Record added successfully with fellows and project!";
                } else {
                    $message = "Error adding project: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Error adding fellowship: " . $stmt->error;
            }
        }
    }
}


// Handle DELETE operation
if (isset($_GET['delete_id'])) {
    $fellowship_id = intval($_GET['delete_id']);

    // Step 1: Delete related rows from the project table
    $stmt = $conn->prepare("DELETE FROM project WHERE fellowship_id = ?");
    $stmt->bind_param("i", $fellowship_id);
    $stmt->execute();
    $stmt->close();

    // Step 2: Delete related rows from the fellow table
    $stmt = $conn->prepare("DELETE FROM fellow WHERE fellowship_id = ?");
    $stmt->bind_param("i", $fellowship_id);
    $stmt->execute();
    $stmt->close();

    // Step 3: Delete the fellowship
    $stmt = $conn->prepare("DELETE FROM fellowship WHERE fellowship_id = ?");
    $stmt->bind_param("i", $fellowship_id);
    if ($stmt->execute()) {
        $message = "Fellowship and associated data deleted successfully!";
    } else {
        $message = "Error deleting fellowship: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch data for READ operation
$sql = "SELECT 
            fs.fellowship_id, fs.name AS fellowship_name, fs.description, fs.start, fs.end, 
            GROUP_CONCAT(f.name SEPARATOR ', ') AS fellows,
            p.project_id, p.title AS project_title, p.duration AS project_duration
        FROM fellowship fs
        LEFT JOIN fellow f ON fs.fellowship_id = f.fellowship_id
        LEFT JOIN project p ON fs.fellowship_id = p.fellowship_id
        GROUP BY fs.fellowship_id";
$result = $conn->query($sql);
$fellowships = $result->fetch_all(MYSQLI_ASSOC);




// Handle UPDATE operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fellowship'])) {
    $fellowship_id = intval($_POST['edit_fellowship_id'] ?? 0);
    $fellowship_name = trim($_POST['edit_fellowship_name'] ?? '');
    $description = trim($_POST['edit_description'] ?? '');
    $start = trim($_POST['edit_start'] ?? '');
    $end = trim($_POST['edit_end'] ?? '');
    $fellow_names = array_filter(explode(',', trim($_POST['edit_fellow_names'] ?? '')));
    $project_title = trim($_POST['edit_project_title'] ?? '');
    $project_duration = intval($_POST['edit_project_duration'] ?? 0);

    if ($fellowship_id && $fellowship_name && $description && $start && $end && !empty($fellow_names) && $project_title && $project_duration) {
        $stmt = $conn->prepare("UPDATE fellowship SET name = ?, description = ?, start = ?, end = ? WHERE fellowship_id = ?");
        $stmt->bind_param("ssssi", $fellowship_name, $description, $start, $end, $fellowship_id);

        if ($stmt->execute()) {
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM fellow WHERE fellowship_id = ?");
            $stmt->bind_param("i", $fellowship_id);
            $stmt->execute();
            $stmt->close();

            foreach ($fellow_names as $fellow_name) {
                $stmt = $conn->prepare("INSERT INTO fellow (name, fellowship_id) VALUES (?, ?)");
                $stmt->bind_param("si", trim($fellow_name), $fellowship_id);
                $stmt->execute();
            }
            $stmt->close();

            $stmt = $conn->prepare("UPDATE project SET title = ?, start_date = ?, end_date = ?, duration = ? WHERE fellowship_id = ?");
            $stmt->bind_param("sssii", $project_title, $start, $end, $project_duration, $fellowship_id);

            if ($stmt->execute()) {
                $message = "Fellowship, fellows, and project updated successfully!";
            } else {
                $message = "Error updating project: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error updating fellowship: " . $stmt->error;
        }
    } else {
        $message = "Error: All fields are required for updating.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fellowship Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        /* Custom CSS for Navbar */
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
        }
        .navbar-brand:hover, .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Fellowship Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#existingFellowships">#</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#logout">#</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <h2>Fellowship Management</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="my-4">
    <h3>Add New Fellowship</h3>
    <div class="mb-3">
        <label for="fellowship_id" class="form-label">Fellowship ID</label>
        <input type="number" name="fellowship_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="fellowship_name" class="form-label">Fellowship Name</label>
        <input type="text" name="fellowship_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label for="start" class="form-label">Start Date</label>
        <input type="date" name="start" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="end" class="form-label">End Date</label>
        <input type="date" name="end" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="fund_id" class="form-label">Fund ID</label>
        <select name="fund_id" class="form-control" required>
            <option value="">Select Fund ID</option>
            <?php
            $funds = $conn->query("SELECT fund_id FROM funding");
            while ($row = $funds->fetch_assoc()) {
                echo "<option value='{$row['fund_id']}'>{$row['fund_id']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="course_id" class="form-label">Course ID</label>
        <input type="number" name="course_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="user_id" class="form-label">User ID</label>
        <input type="number" name="user_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="fellow_names" class="form-label">Fellows (comma-separated)</label>
        <input type="text" name="fellow_names" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="project_id" class="form-label">Project ID</label>
        <input type="number" name="project_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="project_title" class="form-label">Project Title</label>
        <input type="text" name="project_title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="project_duration" class="form-label">Project Duration (in months)</label>
        <input type="number" name="project_duration" class="form-control" required>
    </div>
    <button type="submit" name="add_record" class="btn btn-primary">Add Fellowship</button>
</form>


    <!-- Display Existing Records -->
    <h3>Existing Fellowships</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Start</th>
            <th>End</th>
            <th>Fellows</th>
            <th>Project Title</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($fellowships as $fellowship): ?>
            <tr>
                <td><?= htmlspecialchars($fellowship['fellowship_name']); ?></td>
                <td><?= htmlspecialchars($fellowship['description']); ?></td>
                <td><?= htmlspecialchars($fellowship['start']); ?></td>
                <td><?= htmlspecialchars($fellowship['end']); ?></td>
                <td><?= htmlspecialchars($fellowship['fellows']); ?></td>
                <td><?= htmlspecialchars($fellowship['project_title']); ?></td>
                <td><?= htmlspecialchars($fellowship['project_duration']); ?> months</td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn"
                            data-id="<?= $fellowship['fellowship_id']; ?>"
                            data-name="<?= htmlspecialchars($fellowship['fellowship_name']); ?>"
                            data-description="<?= htmlspecialchars($fellowship['description']); ?>"
                            data-start="<?= htmlspecialchars($fellowship['start']); ?>"
                            data-end="<?= htmlspecialchars($fellowship['end']); ?>"
                            data-fellows="<?= htmlspecialchars($fellowship['fellows']); ?>"
                            data-project-title="<?= htmlspecialchars($fellowship['project_title']); ?>"
                            data-project-duration="<?= htmlspecialchars($fellowship['project_duration']); ?>">
                        Edit
                    </button>
                    <a href="?delete_id=<?= $fellowship['fellowship_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this fellowship?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Fellowship Form -->
    <div id="editForm" style="display: none;" class="mt-4">
        <h3>Edit Fellowship</h3>
        <form method="POST">
            <input type="hidden" name="edit_fellowship_id" id="edit_fellowship_id">
            <div class="mb-3">
                <label for="edit_fellowship_name" class="form-label">Fellowship Name</label>
                <input type="text" name="edit_fellowship_name" id="edit_fellowship_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit_description" class="form-label">Description</label>
                <textarea name="edit_description" id="edit_description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="edit_start" class="form-label">Start Date</label>
                <input type="date" name="edit_start" id="edit_start" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit_end" class="form-label">End Date</label>
                <input type="date" name="edit_end" id="edit_end" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit_fellow_names" class="form-label">Fellows (comma-separated)</label>
                <input type="text" name="edit_fellow_names" id="edit_fellow_names" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit_project_title" class="form-label">Project Title</label>
                <input type="text" name="edit_project_title" id="edit_project_title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="edit_project_duration" class="form-label">Project Duration (in months)</label>
                <input type="number" name="edit_project_duration" id="edit_project_duration" class="form-control" required>
            </div>
            <button type="submit" name="update_fellowship" class="btn btn-success">Update Fellowship</button>
        </form>
    </div>
</div>

<script>
    // Handle Edit Button Click
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.getElementById('editForm');
            form.style.display = 'block';

            // Populate the edit form with the button's dataset
            document.getElementById('edit_fellowship_id').value = button.dataset.id;
            document.getElementById('edit_fellowship_name').value = button.dataset.name;
            document.getElementById('edit_description').value = button.dataset.description;
            document.getElementById('edit_start').value = button.dataset.start;
            document.getElementById('edit_end').value = button.dataset.end;
            document.getElementById('edit_fellow_names').value = button.dataset.fellows;
            document.getElementById('edit_project_title').value = button.dataset.projectTitle;
            document.getElementById('edit_project_duration').value = button.dataset.projectDuration;

            // Scroll to the edit form
            form.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
</body>
</html>

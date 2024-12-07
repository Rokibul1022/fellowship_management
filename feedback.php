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

// Handle Feedback Submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $feedback_source = trim($_POST['feedback_source']);
    $source_id = intval($_POST['source_id']);
    $fellowship_id = intval($_POST['fellowship_id'] ?? null);
    $project_id = intval($_POST['project_id'] ?? null);
    $feedback_text = trim($_POST['feedback_text'] ?? '');

    // Validate feedback
    if (!$feedback_source || !$source_id || !$feedback_text) {
        $message = "Error: Feedback source, source ID, and feedback text are required.";
    } else {
        // Insert feedback into the database
        $stmt = $conn->prepare("INSERT INTO feedback (feedback_source, source_id, fellowship_id, project_id, feedback_text) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiis", $feedback_source, $source_id, $fellowship_id, $project_id, $feedback_text);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error submitting feedback: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch dropdown data
$userOptions = $conn->query("SELECT u_id, name FROM users");
$facultyOptions = $conn->query("SELECT faculty_id, name FROM faculty");
$sponsorOptions = $conn->query("SELECT s_id, name_of_sponsor FROM sponsor");
$fellowshipOptions = $conn->query("SELECT fellowship_id, name FROM fellowship");
$projectOptions = $conn->query("SELECT project_id, title FROM project");

// Fetch Existing Feedback
$sql = "SELECT 
            f.feedback_id, f.feedback_source, f.feedback_text, f.created_at,
            CASE 
                WHEN f.feedback_source = 'user' THEN u.name
                WHEN f.feedback_source = 'faculty' THEN fc.name
                WHEN f.feedback_source = 'sponsor' THEN s.name_of_sponsor
            END AS source_name,
            fs.name AS fellowship_name, p.title AS project_title
        FROM feedback f
        LEFT JOIN users u ON f.feedback_source = 'user' AND f.source_id = u.u_id
        LEFT JOIN faculty fc ON f.feedback_source = 'faculty' AND f.source_id = fc.faculty_id
        LEFT JOIN sponsor s ON f.feedback_source = 'sponsor' AND f.source_id = s.s_id
        LEFT JOIN fellowship fs ON f.fellowship_id = fs.fellowship_id
        LEFT JOIN project p ON f.project_id = p.project_id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
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
                        <a class="nav-link" href="feedback.php">@</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">#</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Feedback Management</h2>
        
        <!-- Display Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Feedback Submission Form -->
        <form method="POST" class="my-4">
            <h3>Submit Feedback</h3>
            <div class="mb-3">
                <label for="feedback_source" class="form-label">Feedback Source</label>
                <select name="feedback_source" id="feedback_source" class="form-control" required>
                    <option value="">Select Feedback Source</option>
                    <option value="user">User</option>
                    <option value="faculty">Faculty</option>
                    <option value="sponsor">Sponsor</option>
                </select>
            </div>
            <div class="mb-3" id="sourceDropdownContainer">
                <!-- Dynamic dropdown will be loaded here -->
            </div>
            <div class="mb-3">
                <label for="fellowship_id" class="form-label">Fellowship (optional)</label>
                <select name="fellowship_id" class="form-control">
                    <option value="">Select Fellowship</option>
                    <?php while ($fellowship = $fellowshipOptions->fetch_assoc()): ?>
                        <option value="<?= $fellowship['fellowship_id']; ?>"><?= htmlspecialchars($fellowship['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="project_id" class="form-label">Project (optional)</label>
                <select name="project_id" class="form-control">
                    <option value="">Select Project</option>
                    <?php while ($project = $projectOptions->fetch_assoc()): ?>
                        <option value="<?= $project['project_id']; ?>"><?= htmlspecialchars($project['title']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="feedback_text" class="form-label">Feedback</label>
                <textarea name="feedback_text" class="form-control" placeholder="Write your feedback here..." required></textarea>
            </div>
            <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
        </form>

        <!-- Feedback Display Table -->
        <h3>Existing Feedbacks</h3>
        <table id="feedbackTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>Source Type</th>
                    <th>Source Name</th>
                    <th>Fellowship</th>
                    <th>Project</th>
                    <th>Feedback</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td><?= htmlspecialchars($feedback['feedback_id']); ?></td>
                        <td><?= htmlspecialchars(ucfirst($feedback['feedback_source'])); ?></td>
                        <td><?= htmlspecialchars($feedback['source_name'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($feedback['fellowship_name'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($feedback['project_title'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($feedback['feedback_text']); ?></td>
                        <td><?= htmlspecialchars($feedback['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function () {
            $('#feedback_source').change(function () {
                const feedbackSource = $(this).val();
                let dropdownHtml = '';

                if (feedbackSource === 'user') {
                    dropdownHtml = `
                        <label for="source_id" class="form-label">Select User</label>
                        <select name="source_id" class="form-control">
                            <option value="">Select User</option>
                            <?php while ($user = $userOptions->fetch_assoc()): ?>
                                <option value="<?= $user['u_id']; ?>"><?= htmlspecialchars($user['name']); ?></option>
                            <?php endwhile; ?>
                        </select>`;
                } else if (feedbackSource === 'faculty') {
                    dropdownHtml = `
                        <label for="source_id" class="form-label">Select Faculty</label>
                        <select name="source_id" class="form-control">
                            <option value="">Select Faculty</option>
                            <?php while ($faculty = $facultyOptions->fetch_assoc()): ?>
                                <option value="<?= $faculty['faculty_id']; ?>"><?= htmlspecialchars($faculty['name']); ?></option>
                            <?php endwhile; ?>
                        </select>`;
                } else if (feedbackSource === 'sponsor') {
                    dropdownHtml = `
                        <label for="source_id" class="form-label">Select Sponsor</label>
                        <select name="source_id" class="form-control">
                            <option value="">Select Sponsor</option>
                            <?php while ($sponsor = $sponsorOptions->fetch_assoc()): ?>
                                <option value="<?= $sponsor['s_id']; ?>"><?= htmlspecialchars($sponsor['name_of_sponsor']); ?></option>
                            <?php endwhile; ?>
                        </select>`;
                }

                $('#sourceDropdownContainer').html(dropdownHtml);
            });

            $('#feedbackTable').DataTable();
        });
    </script>
</body>
</html>

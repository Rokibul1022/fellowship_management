<?php
include 'db.php';
session_start();

// Handle admin application status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];

    if (in_array($status, ['Y', 'N'])) {
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        $stmt->bind_param("si", $status, $application_id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "invalid";
    }
    exit;
}

// Fetch all applications for admin management
$applications = [];
$sql = "SELECT application_id, u_id, status FROM applications";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Application Status</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>User ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($applications)) {
                    foreach ($applications as $row) {
                        $statusText = ($row['status'] === 'Y') ? 'Approved' : (($row['status'] === 'N') ? 'Rejected' : 'Pending');
                        echo "<tr>
                                <td>{$row['application_id']}</td>
                                <td>{$row['u_id']}</td>
                                <td id='status-{$row['application_id']}'>" . htmlspecialchars($statusText) . "</td>
                                <td>
                                    <select onchange='updateStatus({$row['application_id']}, this.value)' class='form-select'>
                                        <option value=''>Select Status</option>
                                        <option value='Y'>Approve</option>
                                        <option value='N'>Reject</option>
                                    </select>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No applications found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateStatus(applicationId, status) {
            if (confirm("Are you sure you want to update the status?")) {
                const formData = new FormData();
                formData.append("update_status", true);
                formData.append("application_id", applicationId);
                formData.append("status", status);

                fetch("", { method: "POST", body: formData })
                    .then(response => response.text())
                    .then(data => {
                        if (data === "success") {
                            const statusCell = document.getElementById(`status-${applicationId}`);
                            statusCell.innerText = (status === 'Y') ? 'Approved' : 'Rejected';
                        } else {
                            alert("Failed to update status. Please try again.");
                        }
                    });
            }
        }
    </script>
</body>
</html>

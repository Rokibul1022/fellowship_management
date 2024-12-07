<?php
include 'db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u_id = (int)$_POST['u_id'];
    $email = htmlspecialchars(trim($_POST['email']));

    $sql = "SELECT u_id FROM users WHERE u_id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $u_id, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $sql = "SELECT status FROM applications WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $u_id);
        $stmt->execute();
        $stmt->bind_result($status);
        $stmt->fetch();

        if ($status === 'Y') {
            $message = "<div class='alert alert-success'>✅ Your application is approved.</div>";
        } elseif ($status === 'N') {
            $message = "<div class='alert alert-danger'>❌ Your application is not approved.</div>";
        } else {
            $message = "<div class='alert alert-warning'>⏳ Your application is still being reviewed.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-danger'>❌ No user found with the provided ID and email.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Application Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
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
        label {
            font-weight: bold;
        }
        .btn {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Check Your Application Status</h2>
        <?= $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="u_id" class="form-label">User ID:</label>
                <input type="number" id="u_id" name="u_id" class="form-control" placeholder="Enter your User ID" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email (Gmail):</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your Gmail" required>
            </div>
            <button type="submit" class="btn btn-primary">Check Status</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

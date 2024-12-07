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

// Fetch table names for the dropdown
$tables = [];
$table_query = "SHOW TABLES";
$table_result = $conn->query($table_query);
while ($row = $table_result->fetch_array()) {
    $tables[] = $row[0];
}

// Handle AJAX requests for table schema and query execution
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['table_name'])) {
        // Fetch schema
        $table_name = $conn->real_escape_string($_POST['table_name']);
        $schema_query = "DESCRIBE `$table_name`";
        $schema_result = $conn->query($schema_query);
        if ($schema_result) {
            $schema = $schema_result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'schema' => $schema]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to fetch schema']);
        }
        exit();
    } elseif (isset($_POST['sql_query'])) {
        // Execute SQL query
        $sql_query = $_POST['sql_query'];
        $result = $conn->query($sql_query);
        if ($result) {
            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'Query executed successfully']);
            } else {
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode(['success' => true, 'data' => $rows, 'columns' => array_keys($rows[0] ?? [])]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query Executor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 30px auto;
            max-width: 900px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .btn {
            font-weight: bold;
        }
        .result-table {
            margin-top: 20px;
        }
        .result-table th, .result-table td {
            text-align: left;
            padding: 8px;
        }
        .error-message {
            color: red;
        }
        .navbar {
            background-color: #f8f9fa; 
        }
        .navbar a {
            color: black; 
            text-decoration: none;
            font-weight: bold;
            margin-right: 15px;
        }
        .navbar a:hover {
            color: #007bff; 
        }
    </style>
</head>
<body>
    <!-- Menu Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SQL Query Tool</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Go Back to Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<div class="container">
    <h1 class="text-center">SQL Query Executor</h1>
    <div class="card p-4">
        <h3>View Table Schema</h3>
        <form id="schemaForm">
            <div class="mb-3">
                <label for="tableName" class="form-label">Select Table:</label>
                <select class="form-select" id="tableName" name="table_name" required>
                    <option value="" disabled selected>Select a table</option>
                    <?php foreach ($tables as $table): ?>
                        <option value="<?= htmlspecialchars($table) ?>"><?= htmlspecialchars($table) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">View Schema</button>
        </form>
        <div id="schemaResult" class="mt-3"></div>
    </div>
    <div class="card p-4 mt-4">
        <h3>Execute SQL Query</h3>
        <form id="queryForm">
            <div class="mb-3">
                <label for="sqlQuery" class="form-label">Write SQL Query:</label>
                <textarea class="form-control" id="sqlQuery" name="sql_query" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100">Run Query</button>
        </form>
        <div id="queryResult" class="mt-3"></div>
    </div>
</div>



    <!-- Recommended Queries Section -->
    <div class="recommended-section">
        <h3>Recommended SQL Queries for Testing</h3>
        <p>Copy and paste these queries into the SQL input box for testing:</p>
        <pre id="recommended-queries">
1. Retrieve all records from the 'fellowship' table:
   SELECT * FROM fellowship;

2. Fetch all 'applications' where graduation year is 2021:
   SELECT * FROM applications WHERE graduation_year = 2021;

3. Count the number of users in each department:
   SELECT department, COUNT(*) AS UserCount FROM users GROUP BY department;

4. List fellowships and their associated course titles:
   SELECT fellowship.name, courses.title 
   FROM fellowship JOIN courses ON fellowship.course_id = courses.course_id;

5. Display user details and the scholarships they applied for:
   SELECT u.name, s.name AS ScholarshipName 
   FROM users u JOIN scholarship_applications s ON u.u_id = s.u_id;

6. Fetch department details along with faculty names:
   SELECT d.dept_name, f.name AS FacultyName 
   FROM department d JOIN faculty f ON d.dept_id = f.dept_id;

7. Show all funding proposals after the year 2020:
   SELECT * FROM funding WHERE YEAR(date_of_proposal) > 2020;

8. Retrieve all projects with a duration of more than 6 months:
   SELECT * FROM project WHERE duration > 6;

9. Insert a new scholarship application:
   INSERT INTO scholarship_applications (name, institute, topic, major_in, email, phone, u_id) 
   VALUES ('John Doe', 'MIT', 'AI Research', 'Computer Science', 'john.doe@example.com', '1234567890', 1);

10. Update the phone number of a specific user:
    UPDATE users SET phone = '9876543210' WHERE name = 'Jane Doe';

11. Delete all installments before a specific date:
    DELETE FROM installment WHERE due_date < '2023-01-01';

12. Show all fellows and the fellowships they are part of:
    SELECT f.name AS FellowName, fs.name AS FellowshipName 
    FROM fellow f JOIN fellowship fs ON f.fellowship_id = fs.fellowship_id;

13. Fetch PhD researchers along with their field of study:
    SELECT name, field_of_study FROM phd;

14. Display the names of all sponsors and their IDs:
    SELECT s_id, name_of_sponsor FROM sponsor;

15. Find the total funding amount for each fellowship:
    SELECT f.name, SUM(fd.tot_amt) AS TotalFunding 
    FROM fellowship f JOIN funding fd ON f.fund_id = fd.fund_id GROUP BY f.name;
        </pre>
    </div>

<script>
    document.getElementById('schemaForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const tableName = document.getElementById('tableName').value;
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ table_name: tableName })
        })
            .then(response => response.json())
            .then(data => {
                const schemaResult = document.getElementById('schemaResult');
                schemaResult.innerHTML = '';
                if (data.success) {
                    let table = '<table class="table table-striped result-table">';
                    table += '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead><tbody>';
                    data.schema.forEach(row => {
                        table += `<tr><td>${row.Field}</td><td>${row.Type}</td><td>${row.Null}</td><td>${row.Key}</td><td>${row.Default ?? 'NULL'}</td><td>${row.Extra}</td></tr>`;
                    });
                    table += '</tbody></table>';
                    schemaResult.innerHTML = table;
                } else {
                    schemaResult.innerHTML = `<p class="error-message">${data.error}</p>`;
                }
            });
    });

    document.getElementById('queryForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const sqlQuery = document.getElementById('sqlQuery').value;
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ sql_query: sqlQuery })
        })
            .then(response => response.json())
            .then(data => {
                const queryResult = document.getElementById('queryResult');
                queryResult.innerHTML = '';
                if (data.success) {
                    if (data.data) {
                        let table = '<table class="table table-striped result-table">';
                        table += '<thead><tr>';
                        data.columns.forEach(col => table += `<th>${col}</th>`);
                        table += '</tr></thead><tbody>';
                        data.data.forEach(row => {
                            table += '<tr>';
                            for (const col in row) {
                                table += `<td>${row[col]}</td>`;
                            }
                            table += '</tr>';
                        });
                        table += '</tbody></table>';
                        queryResult.innerHTML = table;
                    } else {
                        queryResult.innerHTML = `<p class="text-success">${data.message}</p>`;
                    }
                } else {
                    queryResult.innerHTML = `<p class="error-message">${data.error}</p>`;
                }
            });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

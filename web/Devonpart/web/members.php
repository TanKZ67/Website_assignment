<?php
require __DIR__ . '/config/db.php'; 

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT username, email FROM user_profile WHERE username LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%{$search}%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Members List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            padding: 40px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 16px;
            background-color:rgb(59, 170, 185);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        button:hover {
            background-color:rgb(83, 69, 160);
        }
        table {
            margin: auto;
            border-collapse: collapse;
            width: 60%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h2>Registered Members</h2>
    
    <form method="GET">
        <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["username"]); ?></td>
                <td><?php echo htmlspecialchars($row["email"]); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

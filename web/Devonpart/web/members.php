<?php
session_start();
require __DIR__ . '/config/db.php'; 

// 获取当前页码
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 20; // 每页显示几条记录

// 获取搜索关键字
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchTerm = "%{$search}%";

// 获取总记录数
$count_sql = "SELECT COUNT(*) AS total FROM user_profile WHERE user_account LIKE ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $searchTerm);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];

// 计算分页起始点
$offset = ($page - 1) * $records_per_page;

// 查询分页数据
$sql = "SELECT user_account, email FROM user_profile WHERE user_account LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchTerm, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

// 计算总页数
$total_pages = ceil($total_records / $records_per_page);
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
            background-color: rgb(59, 170, 185);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        button:hover {
            background-color: rgb(83, 69, 160);
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #eee;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #3baa95;
            color: white;
        }
        .pagination a:hover {
            background-color: #aaa;
            color: white;
        }
        .total {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
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
            <th>No</th> 
            <th>Username</th>
            <th>Email</th>
        </tr>
        <?php
        $counter = 1; // 初始化计数器
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $counter++; ?></td> <!-- 显示递增的数字 -->
                <td><a href="edit_avatar.php?id=<?php echo $row['user_account']; ?>"><?php echo htmlspecialchars($row["user_account"]); ?></a></td>
                <td><?php echo htmlspecialchars($row["email"]); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="total">
        <?php echo "Total Records: " . $total_records; ?>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" class="<?php echo ($i == $page ? 'active' : ''); ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</body>
</html>

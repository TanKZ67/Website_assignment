<?php
@include 'sql.php';

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($conn, $_GET['query']);
    $query = "SELECT * FROM products WHERE name LIKE '%$search%' OR id LIKE '%$search%'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>".$row['id']."</td>
                <td><img src='".$row['main_image']."' height='100'></td>
                <td>".$row['name']."</td>
                <td>".$row['price']."</td>
                <td>".$row['category']."</td>
                <td>";

            // Display colors
            $colors = json_decode($row['colors'], true);
            if ($colors && is_array($colors)) {
                foreach ($colors as $colorInfo) {
                    echo '<div style="margin-bottom: 15px;">';
                    echo '<strong>' . htmlspecialchars($colorInfo['color']) . '</strong><br>';
                    echo '<img src="' . htmlspecialchars($colorInfo['image']) . '" height="80" style="margin-top: 5px;"><br>';
                    echo '</div>';
                }
            } else {
                echo 'No colors available';
            }

            echo "</td>
                <td>".$row['description']."</td>
                <td>".$row['stock']."</td>
                <td>
                    <a href='update.php?edit=".$row['id']."' class='btn'>Edit</a>
                    <a href='admin.php?delete=".$row['id']."' class='btn'>Delete</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9' style='text-align: center;'>No products found</td></tr>";
    }
}
?>

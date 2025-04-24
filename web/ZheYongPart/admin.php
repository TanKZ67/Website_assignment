<?php
session_start();
@include 'sql.php';


$errors = [];
$product_id = $product_name = $product_price = $quantity = $size = $product_image = $category = $colors = $description = "";

if (isset($_GET['delete'])) {
   $id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM products WHERE id = $id");
   $_SESSION['message'] = 'Product deleted successfully!';
   header('location:admin.php');
   exit();
}


if (isset($_POST['add_product'])) {
   $product_name = trim($_POST['product_name']);
   $product_price = trim($_POST['product_price']);
   $quantity = trim($_POST['quantity']);
   $category = trim($_POST['category']);
   $description = trim($_POST['description']);
   $product_image = $_FILES['product_image']['name'] ?? '';
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'] ?? '';
   $upload_folder = 'W1Demo/image/';
   $main_image_url =  '/' . $main_image_path; // for displaying later


   if (empty($product_name) || strlen($product_name) < 3) {
      $errors['product_name'] = 'Product name must be at least 3 characters long!';
   } else {
      // Check if the product name already exists
      $check_name = mysqli_query($conn, "SELECT id FROM products WHERE name = '$product_name'");
      if (mysqli_num_rows($check_name) > 0) {
         $errors['product_name'] = 'Product Name Already Exists! ';
      }
   }

   if (empty($product_price) || !filter_var($product_price, FILTER_VALIDATE_FLOAT) || $product_price <= 0 || $product_price > 1000) {
      $errors['product_price'] = 'Valid product price is required!';
   }

   $validCategories = ['men', 'women', 'kids 2-8y', 'kids 9-14y'];
   $categoryLower = strtolower(trim($category));

   if (empty($categoryLower) || !in_array($categoryLower, $validCategories)) {
      $errors['category'] = 'Product category is required!(Must be Men , Women , kids 2-8y , kids 9-14y)';
   }


   if (empty($description)) {
      $errors['description'] = 'Product description is required!';
   }

   if (empty($quantity) || !filter_var($quantity, FILTER_VALIDATE_INT) || $quantity <= 0 || $quantity > 1000) {
      $errors['quantity'] = 'Valid product quantity is required!';
   }

   // Handle the main product image upload
   if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
      $errors['product_image'] = 'Error uploading main image!';
   } else {
      // Get the uploaded file's temporary name and generate the new file path
      $product_image = $_FILES['product_image']['name'];
      $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
      $main_image_path = '/' . $upload_folder . basename($product_image);

      // Move the uploaded file to the desired location
      if (move_uploaded_file($product_image_tmp_name, $upload_folder . $product_image)) {
         // Image uploaded successfully
      } else {
         $errors['product_image'] = 'Image upload failed!';
      }
   }



   // Handle color images
   if (isset($_FILES['color_images']) && isset($_POST['color_names'])) {
      $color_names = $_POST['color_names'];
      $color_images = $_FILES['color_images'];

      $colors = [];

      for ($i = 0; $i < count($color_names); $i++) {
         $color_name = trim($color_names[$i]);
         $color_image = $color_images['name'][$i];
         $color_image_tmp_name = $color_images['tmp_name'][$i];

         if (!empty($color_name) && !empty($color_image)) {
            // Define color image path
            $color_image_path =  '/' . $upload_folder . basename($color_image);

            // Move color image to the upload folder
            if (move_uploaded_file($color_image_tmp_name, $upload_folder . $color_image)) {
               $colors[] = [
                  'color' => $color_name,
                  'image' => $color_image_path
               ];
            } else {
               $errors['colors'] = 'Error uploading one or more color images!';
            }
         }
      }

      if (empty($colors)) {
         $errors['colors'] = 'Please provide at least one color name with an image.';
      }
   }

   // Convert colors to JSON format for storage
   $colors_json = json_encode($colors);




   // Insert product into the database
   if (empty($errors)) {
      $insert = "INSERT INTO products (name, price, category, main_image, colors, description, stock) 
                 VALUES ('" . mysqli_real_escape_string($conn, $product_name) . "', 
                         '" . mysqli_real_escape_string($conn, $product_price) . "', 
                         '" . mysqli_real_escape_string($conn, $category) . "', 
                         '" . mysqli_real_escape_string($conn, $main_image_path) . "',
                         '" . mysqli_real_escape_string($conn, $colors_json) . "', 
                         '" . mysqli_real_escape_string($conn, $description) . "', 
                         '" . mysqli_real_escape_string($conn, $quantity) . "')";

      $upload = mysqli_query($conn, $insert);

      if ($upload) {
         $_SESSION['message'] = 'New product added successfully!';
         header("Location: admin.php");
         exit();
      } else {
         echo "<pre style='color: red;'>Upload failed</pre>";
      }
   }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="style.css">

</head>


<body>


   <?php
   if (isset($_SESSION['message'])) {
      echo '<span class="message">' . htmlspecialchars($_SESSION['message']) . '</span>';
      unset($_SESSION['message']); // Remove message after displaying
   }


   ?>


<a href="/a/Website_assignment/web/YuchenPart/Aftersignin.php"
   style="display: block; width: 10%; cursor: pointer; border-radius: .5rem; margin-top: 1rem; font-size: 1.7rem; padding: 1rem 3rem; background: var(--green); color: var(--white); text-align:center;">
   Back
</a>


   <div class="container">
      <div class="admin-product-form-container">
         <form action="" method="post" enctype="multipart/form-data">
            <h3>Add a New Product</h3>

            <label for="product_name" class="size">Product Name: </label>
            <input type="text" placeholder="Enter product name" name="product_name" class="box" value="<?php echo htmlspecialchars($product_name); ?>">
            <span class="error"><?php echo $errors['product_name'] ?? ''; ?></span>
            <br>

            <label for="product_price" class="size">Product Price: </label>
            <input type="number" placeholder="Enter product price" name="product_price" class="box" value="<?php echo htmlspecialchars($product_price); ?>">
            <span class="error"><?php echo $errors['product_price'] ?? ''; ?></span>
            <br>

            <label for="quantity" class="size">Product Quantity: </label>
            <input type="number" placeholder="Enter product quantity" name="quantity" class="box" value="<?php echo htmlspecialchars($quantity); ?>">
            <span class="error"><?php echo $errors['quantity'] ?? ''; ?></span>
            <br>

            <!-- New Category Field -->
            <label for="category" class="size">Category: </label>
            <input type="text" placeholder="Enter product category" name="category" class="box" value="<?php echo htmlspecialchars($category); ?>">
            <span class="error"><?php echo $errors['category'] ?? ''; ?></span>
            <br>

            <!-- New Color Field (JSON) -->
            <script src="color.js"></script>
            <label for="colors" class="size">Product Colors: </label>
            <div id="color-inputs">
               <div class="color-input">
                  <input type="text" name="color_names[]" placeholder="Enter color name (e.g., Red)" class="box">
                  <input type="file" name="color_images[]" accept="image/*" class="box">
                  <button type="button" class="remove-color-btn" onclick="removeColorInput(this)">Remove</button>
               </div>
            </div>

            <button type="button" onclick="addColorInput()">Add Another Color</button><br>



            <span class="error"><?php echo $errors['colors'] ?? ''; ?></span>


            <br>


            <!-- New Description Field -->
            <label for="description" class="size">Product Description: </label>
            <textarea name="description" class="box" placeholder="Enter product description"><?php echo htmlspecialchars($description); ?></textarea>
            <span class="error"><?php echo $errors['description'] ?? ''; ?></span>
            <br>

            <script src="script.js"></script>
            <label class=size>Product Image:</label>
            <div class="upload">
               <input type="file" accept="image/*" id="input-file" name="product_image" hidden>


               <label for="input-file" id="drop-area" style="display: block; width: 100%; padding: 20px; border: 2px dashed #ccc; text-align: center; cursor: pointer;">
                  <div id="img-view" style="width: 100%; height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                     <img src="icon.png" alt="Upload Icon" width="100">
                     <p>Drag and drop image here<br>to upload image</p>
                     <span>Upload any image from desktop</span>
                  </div>
               </label>
            </div>
            <span class="error"><?php echo $errors['product_image'] ?? ''; ?></span>


            <input type="submit" class="btn" name="add_product" value="Add Product">
         </form>



      </div>

      <!-- Filter and Search Section -->
      <form action="" method="get">
         <input type="text" name="query" placeholder="Search products by name" value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>">
         <select name="category">
            <option value="">All Categories</option>
            <option value="men" <?php echo (isset($_GET['category']) && $_GET['category'] === 'men') ? 'selected' : ''; ?>>Men</option>
            <option value="women" <?php echo (isset($_GET['category']) && $_GET['category'] === 'women') ? 'selected' : ''; ?>>Women</option>
            <option value="kids 2-8y" <?php echo (isset($_GET['category']) && $_GET['category'] === 'kids 2-8y') ? 'selected' : ''; ?>>Kids 2-8y</option>
            <option value="kids 9-14y" <?php echo (isset($_GET['category']) && $_GET['category'] === 'kids 9-14y') ? 'selected' : ''; ?>>Kids 9-14y</option>
         </select>

         <select name="price">
            <option value="">All Prices</option>
            <option value="1-10" <?php echo (isset($_GET['price']) && $_GET['price'] === '1-10') ? 'selected' : ''; ?>>$1 - $10</option>
            <option value="11-20" <?php echo (isset($_GET['price']) && $_GET['price'] === '11-20') ? 'selected' : ''; ?>>$11 - $20</option>
            <option value="21-30" <?php echo (isset($_GET['price']) && $_GET['price'] === '21-30') ? 'selected' : ''; ?>>$21 - $30</option>
            <option value="31-1000" <?php echo (isset($_GET['price']) && $_GET['price'] === '31-1000') ? 'selected' : ''; ?>>$31 - $1000</option>
         </select>


         <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; 
              text-align: center; text-decoration: none; display: inline-block; 
              font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 5px;">Search</button>

         <a href="admin.php" style="background-color: #f44336; color: white; border: none; padding: 10px 20px; 
              text-align: center; text-decoration: none; display: inline-block; 
              font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 5px;">Reset</a>
      </form>



   </div>


   <div class="product-display">
      <h1>Product List</h1>
      <?php
      // Count total number of all products (no filters)
      $all_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
      $all_row = mysqli_fetch_assoc($all_result);
      $all_products = $all_row['total'];
   
      ?>
      <p style="font-size: 18px; margin-bottom: 10px;">
         <strong>Total Products:</strong> <?php echo $all_products; ?>

      </p>



      <table class="product-display-table">
         <thead>
            <tr>
               <th>Product No</th>
               <th>Product ID</th>
               <th>Product Image</th>
               <th>Product Name</th>
               <th>Product Price</th>
               <th>Category</th>
               <th>Colour</th>
               <th>Description</th>
               <th>Quantity</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody id="product-list">
            <?php
            $limit = 5; // Number of items per page
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $counter = ($page - 1) * $limit + 1;

            // Filter query parameters
            $query = isset($_GET['query']) ? trim($_GET['query']) : '';
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $priceRange = isset($_GET['price']) ? $_GET['price'] : '';

            $where = "WHERE 1"; // default condition for filtering
            if ($query) {
               $where .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $query) . "%'";
            }
            if ($category) {
               $where .= " AND category = '" . mysqli_real_escape_string($conn, $category) . "'";
            }
            if ($priceRange && $priceRange !== 'all') {
               $priceBounds = explode('-', $priceRange);
               if (count($priceBounds) == 2) {
                  $minPrice = (float) $priceBounds[0];
                  $maxPrice = (float) $priceBounds[1];
                  $where .= " AND price BETWEEN $minPrice AND $maxPrice";
               }
            }


            // Get total number of filtered products
            $total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products $where");
            $total_row = mysqli_fetch_assoc($total_result);
            $total_products = $total_row['total'];
            $total_pages = ceil($total_products / $limit);

            // Fetch the filtered products for the current page
            $select = mysqli_query($conn, "SELECT * FROM products $where LIMIT $limit OFFSET $offset");

            if (mysqli_num_rows($select) == 0) {
               echo '<tr><td colspan="9" style="text-align: center;">No products found.</td></tr>';
            } else {
               while ($row = mysqli_fetch_assoc($select)) { ?>
                  <tr>
                     <td><?php echo $counter++; ?></td>
                     <td><?php echo $row['id']; ?></td>
                     <td><img src="<?php echo  $row['main_image']; ?>" height="100"></td>

                     <td><?php echo $row['name']; ?></td>
                     <td><?php echo $row['price']; ?></td>
                     <td><?php echo $row['category']; ?></td>
                     <td>
                        <?php
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
                        ?>
                     </td>


                     <td><?php echo $row['description']; ?></td>
                     <td><?php echo $row['stock']; ?></td>
                     <td>
                        <a href="update.php?edit=<?php echo $row['id']; ?>" class="btn"><i class="fas fa-edit"></i> Edit</a>
                        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn"><i class="fas fa-trash"></i> Delete</a>
                     </td>

                  </tr>

               <?php } ?>
            <?php } ?>

         </tbody>
      </table>

   </div>
   <div class="pagination">
    <?php if ($page > 1): ?>
        <a class="pagination-link" href="?page=<?php echo $page - 1; ?>&query=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>&price=<?php echo urlencode($priceRange); ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a class="pagination-link <?php echo ($i == $page) ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>&query=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>&price=<?php echo urlencode($priceRange); ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a class="pagination-link" href="?page=<?php echo $page + 1; ?>&query=<?php echo urlencode($query); ?>&category=<?php echo urlencode($category); ?>&price=<?php echo urlencode($priceRange); ?>">Next &raquo;</a>
    <?php endif; ?>
</div>


</body>


</html>
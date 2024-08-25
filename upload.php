<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db_connect.php';

$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Generate a unique file name to avoid overwriting existing files
        $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
        $description = $_POST['description'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        
        // Set the target directory for uploaded files
        $file_path = 'uploads/' . $file_name;
        
        // Get the MIME type of the file
        $mimetype = mime_content_type($_FILES['file']['tmp_name']);
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            // Prepare the SQL query to insert the file data into the database
            $sql = "INSERT INTO photos (path, mimetype, description, latitude, longitude) VALUES (:path, :mimetype, :description, :latitude, :longitude)";
            $stmt = $pdo->prepare($sql);
        
            // Execute the query with the appropriate parameters
            if ($stmt->execute([
                ':path' => $file_path,
                ':mimetype' => $mimetype,
                ':description' => $description,
                ':latitude' => $latitude,
                ':longitude' => $longitude
            ])) {
                // Set a success message
                $success_message = "Photo uploaded successfully!";
            } else {
                // Handle SQL execution error
                $success_message = "Error: Failed to insert into database.";
            }
        } else {
            $success_message = "Error: Failed to move the uploaded file.";
        }
    } else {
        $success_message = "Error: " . $_FILES['file']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Upload Photo</h1>

    <!-- Display success or error message -->
    <?php if ($success_message): ?>
        <p><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>

    <form action="upload" method="post" enctype="multipart/form-data">
        <label for="file">Choose a file:</label>
        <input type="file" name="file" id="file" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" id="latitude" required>

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" id="longitude" required>

        <button type="submit">Upload</button>
    </form>
</body>
</html>

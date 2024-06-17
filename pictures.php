<?php



$image = new Images(null, null, null);
$image->createImageTable($connection);



// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadedFile = $_FILES['file'];
    $imageName = $uploadedFile['name'];
    $imageData = file_get_contents($uploadedFile['tmp_name']);
    $imageID = Get_new_id_pics($connection, "images", "id");
    $_SESSION['uploaded_image_id'] = $imageID;
    $image->uploadImage($imageID, $imageData, $imageName, $connection);
}

// Handle image search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchId = $_POST['searchId'];
    $searchResult = $image->getImageById($searchId, $connection);

    if ($searchResult) {
        $imageName = $searchResult['image_name'];
        $imageData = base64_encode($searchResult['image_data']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload and Gallery</title>
    <style>
        #drop-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }

        .image-container {
            margin: 10px;
        }

        img {
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>

<body>
    <form enctype="multipart/form-data" action="" method="POST">
        <label for="fileInput">Choose Image:</label>
        <input type="file" id="fileInput" name="file" accept="image/*" required>
        <button type="submit" name="upload">Upload</button>
    </form>

<!--
    <form action="" method="POST">
        <label for="searchId">Search Image by ID:</label>
        <input type="text" id="searchId" name="searchId" required>
        <button type="submit" name="search">Search</button>
    </form>
-->

    <?php
    if (isset($searchResult)) {
        echo '<div class="image-container">';
        echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Image">';
        echo '<p>ID: ' . $searchId . '</p>';
        echo '</div>';
    }
    ?>
</body>

</html>

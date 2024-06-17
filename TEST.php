<?php
require 'pictures_subscriptions.php';
require 'Database.php';


   
        if (isset($_POST['final_submit']) && isset($_FILES['file1'])) {
            $id1 = handleImageUpload($_FILES['file1'], $connection);
            echo "pic id 1: $id1";
        }

        if (isset($_POST['final_submit']) && isset($_FILES['file2'])) {
            $id2 = handleImageUpload($_FILES['file2'], $connection);
            echo "pic id 2: $id2";
        }
          if (isset($_POST['final_submit']) && isset($_FILES['file3'])) {
            $id2 = handleImageUpload($_FILES['file3'], $connection);
            echo "pic id 3: $id2";
        }

?>

<form enctype="multipart/form-data" action="" method="POST">
   <div> <label for="fileInput1">Choose Image 1:</label>
       <input type="file" id="fileInput1" name="file1" accept="image/*"></div>
    
   <div> <label for="fileInput2">Choose Image 2:</label>
       <input type="file" id="fileInput2" name="file2" accept="image/*"></div>
    
      <div>  <label for="fileInput3">Choose Image 3:</label>
          <input type="file" id="fileInput3" name="file3" accept="image/*"></div>

    <input type="submit" name="final_submit" value="Submit">
</form>

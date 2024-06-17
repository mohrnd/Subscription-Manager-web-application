<?php

require_once 'Database.php';

class Images extends SETRAM_Database
{
    public $id, $image_data, $image_name;

    public function __construct($id, $image_data, $image_name)
    {
        parent::__construct("");
        $this->id = $id;
        $this->image_data = $image_data;
        $this->image_name = $image_name;
    }

    public function createImageTable($c)
    {
        $request = "CREATE TABLE IF NOT EXISTS images (
                        id INT PRIMARY KEY,
                        image_data LONGBLOB,
                        image_name VARCHAR(255)
                    )";
        $stmt = $c->prepare($request);
        $stmt->execute();
    }



        public function uploadImage($imageID, $imageData, $imageName, $c)
    {
        try {
            $query = "INSERT INTO images (id, image_data, image_name) VALUES (:id, :imageData, :imageName)";
            $stmt = $c->prepare($query);
            $stmt->bindParam(':id', $imageID, PDO::PARAM_INT);
            $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
            $stmt->bindParam(':imageName', $imageName, PDO::PARAM_STR);
            $stmt->execute();
            return $imageID;
            echo "Image uploaded successfully <br>";
        } catch (PDOException $e) {
            die("Image upload error: " . $e->getMessage());
        }
    }

}

function Get_new_id_pics($c, $tableName, $ID_var_Name)
{
    $request = "SELECT MAX($ID_var_Name) as maxID FROM $tableName";
    $stmt = $c->prepare($request);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['maxID'])) {
        return $result['maxID'] + 1;
    } else {
        return 1;
    }
}

function handleImageUpload($uploadedFile, $connection)
{
    $imageName = $uploadedFile['name'];
    $imageData = file_get_contents($uploadedFile['tmp_name']);
    $imageID = Get_new_id_pics($connection, "images", "id");
    $_SESSION['uploaded_image_id'] = $imageID;
    return (new Images(null, null, null))->uploadImage($imageID, $imageData, $imageName, $connection);
}

function uploadrenewPfp_clients($c,$userId, $uploadedImageID){
        $Request = "UPDATE Clients SET ProfilePictureID = :ProfilePictureID WHERE ClientID = :userId";
        $stmt = $c->prepare($Request);
        $stmt->execute([
            ':ProfilePictureID' => $uploadedImageID,
            ':userId' => $userId
        ]);

}


function uploadrenewPfp_agents($c,$userId, $uploadedImageID){
        $Request = "UPDATE Agents SET ProfilePictureID = :ProfilePictureID WHERE AgentID = :userId";
        $stmt = $c->prepare($Request);
        $stmt->execute([
            ':ProfilePictureID' => $uploadedImageID,
            ':userId' => $userId
        ]);

}


function uploadrenewPfp_admins($c,$userId, $uploadedImageID){
        $Request = "UPDATE Administrators SET ProfilePictureID = :ProfilePictureID WHERE AdminID = :userId";
        $stmt = $c->prepare($Request);
        $stmt->execute([
            ':ProfilePictureID' => $uploadedImageID,
            ':userId' => $userId
        ]);

}


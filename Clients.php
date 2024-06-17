<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_clt.css">
</head>
<body>
<?php
require_once 'Database.php';

class Clients extends SETRAM_Database {

    public $ClientID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID;

    public function __construct($ClientID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID) {
        parent::__construct("");
        $this->ClientID = $ClientID;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->Dob = $Dob;
        $this->Gender = $Gender;
        $this->PhoneNumber = $PhoneNumber;
        $this->Email = $Email;
        $this->Password = $Password;
        $this->ProfilePictureID = $ProfilePictureID;
    }

    public function Clients_Table_Creation($c) {
        $Request = "CREATE TABLE IF NOT EXISTS Clients (
            ClientID int(5) primary key,
            FirstName varchar(40),
            LastName varchar(40),
            Dob date,
            Gender varchar(5),
            PhoneNumber int(10),
            Email varchar(50),
            Password varchar(50),
            ProfilePictureID int(10)
        )";
        $x = $c->prepare($Request);
        $e = $x->execute();
        if (!$e) {
          //  echo "Clients table creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
          //  echo "<p class='formtxt'>Clients table created successfully</p>";
        }
    }

    public function Create_New_Client($c) {
        
        $Request = "INSERT INTO Clients (ClientID, FirstName, LastName, Dob, Gender, PhoneNumber, Email, Password,  ProfilePictureID) 
                    VALUES (:ClientID, :FirstName, :LastName, :Dob,  :Gender, :PhoneNumber, :Email, :Password, :ProfilePictureID)";
        $x = $c->prepare($Request);
        $e = $x->execute([
            ':ClientID' => $this->ClientID,
            ':FirstName' => $this->FirstName,
            ':LastName' => $this->LastName,
            ':Dob' => $this->Dob,
            ':Gender' => $this->Gender,
            ':PhoneNumber' => $this->PhoneNumber,
            ':Email' => $this->Email,
            ':Password' => $this->Password,
            ':ProfilePictureID' => $this->ProfilePictureID
        ]);

        if (!$e) {
            echo "Client account creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
          echo "<p class='formtxt'>Client $this->FirstName $this->LastName has been Created successfully</p>";

        }
    }
    
    
public function View_Clients($c){
    try {
      $request = "SELECT * FROM Clients LEFT JOIN images ON images.id = Clients.ProfilePictureID";
      $stmt = $c->prepare($request);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return []; 
    }
  }
    
  public function Delete_Client($c, $ClientID)
  {
    try {
      $request = "DELETE FROM Clients WHERE ClientID = :ClientID";
      $stmt = $c->prepare($request);
      $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
      $stmt->execute();

      return true;
        echo "Client $ClientID deleted";
    } catch (PDOException $e) {

      return false;
    }
  }    
}


?>
</body>
</html>


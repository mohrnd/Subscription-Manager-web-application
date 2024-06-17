<?php
require_once 'Database.php';
class AGENTS extends SETRAM_Database {

    public $AgentID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID;

    public function __construct($AgentID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID) {
        parent::__construct("");
        $this->AgentID = $AgentID;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->Dob = $Dob;
        $this->Gender = $Gender;
        $this->PhoneNumber = $PhoneNumber;
        $this->Email = $Email;
        $this->Password = $Password;
        $this->ProfilePictureID = $ProfilePictureID;
    }

    public function AGENTS_Table_Creation($c) {
        $Request = "CREATE TABLE IF NOT EXISTS Agents (
            AgentID int(5) primary key,
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
           // echo "AGENTS table creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
          //  echo "AGENTS table created successfully <br>";
        }
    }

    public function Create_New_AGENTS($c) {
        $Request = "INSERT INTO AGENTS (AgentID, FirstName, LastName, Dob, Gender, PhoneNumber, Email, Password,  ProfilePictureID) 
                    VALUES (:AgentID, :FirstName, :LastName, :Dob,  :Gender, :PhoneNumber, :Email, :Password, :ProfilePictureID)";
        $x = $c->prepare($Request);
        $e = $x->execute([
            ':AgentID' => $this->AgentID,
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
            echo "Agent account creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "Agent $this->FirstName $this->LastName has been Created successfully <br>";
        }
    }
    public function View_Agents($c){
    try {
      $request = "SELECT * FROM Agents LEFT JOIN images ON images.id = Agents.ProfilePictureID";
      $stmt = $c->prepare($request);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return []; 
    }
  }
    
  public function Delete_Agent($c, $AgentID)
  {
    try {
      $request = "DELETE FROM Agents WHERE AgentID = :AgentID";
      $stmt = $c->prepare($request);
      $stmt->bindParam(':AgentID', $AgentID, PDO::PARAM_INT);
      $stmt->execute();

      return true;
        echo "Agent $AgentID deleted";
    } catch (PDOException $e) {

      return false;
    }
  }
}


function Get_New_ID_agents($c, $tableName, $ID_var_Name) {
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






?>

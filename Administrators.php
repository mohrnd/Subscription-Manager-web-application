<?php 
class Administrators extends SETRAM_Database {

    public $AdminID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID;

    public function __construct($AdminID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID) {
        parent::__construct("");
        $this->AdminID = $AdminID;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->Dob = $Dob;
        $this->Gender = $Gender;
        $this->PhoneNumber = $PhoneNumber;
        $this->Email = $Email;
        $this->Password = $Password;
        $this->ProfilePictureID = $ProfilePictureID;
    }

    public function Administrators_Table_Creation($c) {
        $Request = "CREATE TABLE IF NOT EXISTS Administrators (
            AdminID int(5) primary key,
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
            echo "Clients table creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "Clients table created successfully <br>";
        }
    }

    public function Create_New_Administrator($c) {
        $Request = "INSERT INTO Administrators (AdminID, FirstName, LastName, Dob, Gender, PhoneNumber, Email, Password,  ProfilePictureID) 
                    VALUES (:AdminID, :FirstName, :LastName, :Dob,  :Gender, :PhoneNumber, :Email, :Password, :ProfilePictureID)";
        $x = $c->prepare($Request);
        $e = $x->execute([
            ':AdminID' => $this->AdminID,
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
            echo "Administrator account creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "Administrator $this->FirstName $this->LastName has been Created successfully <br>";
        }
    }
}

?>

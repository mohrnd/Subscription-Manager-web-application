<?php 
class SETRAM_Database {

    public $connexion, $Db_name;

    public function __construct($Db_name) {
        $this->Db_name = $Db_name;
    }

    public function get_connex() {
        return $this->connexion;
    }

    public function Connection_to_Server() {
        $host = '';
        $dbname = '';
        $user = '';
        $password = 'place-holder-not-my-real-password';
        $this->connexion = new PDO("mysql:host=$host;dbname=$dbname",$user,$password);
        if (!$this->connexion) {
         //  echo "Connection error <br>";
        } else {
        //  echo "Connected successfully <br>";
        }
    }

public function Connection_to_Db() {
    $host = '';
    $dbname = '';
    $user = '';
    $password = 'place-holder-not-my-real-password';

    $this->connexion = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

    if (!$this->connexion) {
      // echo "Connection error: " . print_r($this->connexion->errorInfo(), true) . "<br>";
    } else {
      //  echo "Connected successfully <br>";
    }

    return $this->connexion;
}


    public function SETRAM_Database_Creation() {
        $Request = "CREATE DATABASE IF NOT EXISTS " . $this->Db_name;
        $x = $this->connexion->prepare($Request);
        $Se = $x->execute();
        if (!$Se) {
          //  echo "Database creation error creation error: " . print_r($x->errorInfo(), true) . "<br>";
        } else {
          // echo "Database created successfully <br>";
        }
    }
    

}

//////// USAGE /////////
$db = new SETRAM_Database("");
$db->Connection_to_Server();
$db->SETRAM_Database_Creation();
$connection = $db->Connection_to_Db();
////////////////////////

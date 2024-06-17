<?php
class SubscriptionRequest extends SETRAM_Database{
    
    
     public $SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan;

    public function __construct($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan) {
        parent::__construct('');
        $this->SubID = $SubID;
        $this->Date = $Date;
        $this->Category = $Category;
        $this->SubscriptionType = $SubscriptionType;
        $this->Price = $Price;
        $this->ClientID = $ClientID;
        $this->IDPic = $IDPic;
        $this->Proof = $Proof;
        $this->PaymentProof = $PaymentProof;
        $this->Plan = $Plan;
    }

    public function SubscriptionRequest_Table_Creation($c) {
       $Request = "CREATE TABLE IF NOT EXISTS SubscriptionRequest (
    SubID int(5) primary key,
    Date date,
    Category varchar(40),
    SubscriptionType varchar(40),
    Price varchar(40),
    ClientID int(5),
    IDPic int(5),
    Proof int(5),
    PaymentProof int(5),
    Plan varchar(40)
)";

        $x = $c->prepare($Request);
        $e = $x->execute();
        if (!$e) {
            echo "SubscriptionRequest table creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "SubscriptionRequest table created successfully <br>";
        }
    }

    public function Create_New_SubscriptionRequest($c) {
        
        $Request = "INSERT INTO SubscriptionRequest (SubID, Date, Category, SubscriptionType, Price, ClientID, IDPic, Proof, PaymentProof, Plan) 
                    VALUES (:SubID, :Date, :Category, :SubscriptionType, :Price, :ClientID, :IDPic, :Proof, :PaymentProof, :Plan)";
        $x = $c->prepare($Request);
        $e = $x->execute([
        ':SubID'=>$this->SubID,
        ':Date'=>$this->Date,
        ':Category'=>$this->Category,
        ':SubscriptionType'=>$this->SubscriptionType,
        ':Price'=>$this->Price,
        ':ClientID'=>$this->ClientID,
        ':IDPic'=>$this->IDPic,
        ':Proof'=>$this->Proof,
        ':PaymentProof'=>$this->PaymentProof,
        ':Plan'=>$this->Plan
        ]);

        if (!$e) {
            echo "Subscription creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "Subscription has been Created successfully <br>";
        }
    }
}
function Verify_if_client_has_subscription_request($c, $ClientID) {
    try {
        $request = "SELECT * FROM SubscriptionRequest WHERE SubscriptionRequest.ClientID = :ClientID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}
function Get_New_ID_subrequest($c, $tableName, $ID_var_Name) {
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
function View_Subscription_Requests($c){
    try {
      $request = "SELECT 
                Clients.FirstName,
                Clients.LastName,
                Clients.Email,
                SubscriptionRequest.SubID, 
                SubscriptionRequest.Date, 
                SubscriptionRequest.Category, 
                SubscriptionRequest.SubscriptionType, 
                SubscriptionRequest.Price, 
                SubscriptionRequest.ClientID, 
                SubscriptionRequest.IDPic, 
                SubscriptionRequest.Proof, 
                SubscriptionRequest.PaymentProof, 
                SubscriptionRequest.Plan,
                IDPic.image_data AS IDPicData,
                Proof.image_data AS ProofData,
                PaymentProof.image_data AS PaymentProofData
            FROM SubscriptionRequest
            LEFT JOIN images AS IDPic ON IDPic.id = SubscriptionRequest.IDPic
            LEFT JOIN images AS Proof ON Proof.id = SubscriptionRequest.Proof
            LEFT JOIN images AS PaymentProof ON PaymentProof.id = SubscriptionRequest.PaymentProof
            LEFT JOIN Clients ON Clients.ClientID = SubscriptionRequest.ClientID";
      $stmt = $c->prepare($request);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return []; 
    }
  }

function Delete_Request($c, $SubID) {
    try {
        $request = "DELETE FROM SubscriptionRequest WHERE SubID = :SubID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':SubID', $SubID, PDO::PARAM_INT);
        $stmt->execute();

        echo "Subscription $SubID deleted";  

        return true;
    } catch (PDOException $e) {
        return false;
    }
}




?>

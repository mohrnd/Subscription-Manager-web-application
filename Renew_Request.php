<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_cont.css">
</head>
<body>


<?php

class Renew_Requests extends SETRAM_Database{
    
     public $RenewalID, $AsubID, $SubscriptionType, $Price, $ProofOfPaymentID;

    public function __construct($RenewalID, $AsubID, $SubscriptionType, $Price, $ProofOfPaymentID) {
        parent::__construct('boubekeur_setram_database');
        $this->RenewalID = $RenewalID;
        $this->AsubID = $AsubID;
        $this->SubscriptionType = $SubscriptionType;
        $this->Price = $Price;
        $this->ProofOfPaymentID = $ProofOfPaymentID;
    }

    public function Renew_Request_Table_Creation($c) {
       $Request = "CREATE TABLE IF NOT EXISTS Renew_Requests (
                   RenewalID int(5) primary key,
                   AsubID int(5),
                   SubscriptionType varchar(40),
                   Price int(10),
                   ProofOfPaymentID int(5)
)";

        $x = $c->prepare($Request);
        $e = $x->execute();
        if (!$e) {
            //echo "Renew_Requests table creation error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
           // echo "Renew_Requests table created successfully <br>";
        }
    }

    public function Submit_Renewal_Request($c) {
        
        $Request = "INSERT INTO Renew_Requests (RenewalID, AsubID, SubscriptionType, Price, ProofOfPaymentID) 
                    VALUES (:RenewalID, :AsubID, :SubscriptionType, :Price, :ProofOfPaymentID)";
        $x = $c->prepare($Request);
        $e = $x->execute([
        ':RenewalID'=>$this->RenewalID,
        ':AsubID'=>$this->AsubID,
        ':SubscriptionType'=>$this->SubscriptionType,
        ':Price'=>$this->Price,
        ':ProofOfPaymentID'=>$this->ProofOfPaymentID
        ]);

        if (!$e) {
            echo "Subscription renewal error: " . print_r($x->errorInfo(), true) . " <br>";
        } else {
            echo "The renewal request has been sent successfully. Please wait for one of our agents to validate your request.<br>";
        }
    }   
}
    
function GetActSubID($c, $ClientID){
    try {
        $request = "SELECT AsubID FROM Active_Subscriptions WHERE SubID IN (SELECT SubID FROM SubscriptionRequest WHERE ClientID = :ClientID)";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
        $stmt->execute();

        $ActSubID = $stmt->fetchColumn();
        
        return $ActSubID !== false ? $ActSubID : null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}


function update_activated_subscription($c, $AsubID){
      try {
        
        $Request1 = "SELECT EndDate FROM Active_Subscriptions WHERE AsubID = :AsubID";
        $stmt1 = $c->prepare($Request1);
        $stmt1->bindParam(':AsubID', $AsubID, PDO::PARAM_INT);
        $stmt1->execute();
        $oldEndDate = $stmt1->fetchColumn();
          
        $Request2 = "SELECT SubscriptionRequest.SubscriptionType
FROM SubscriptionRequest, Active_Subscriptions
WHERE SubscriptionRequest.SubID = Active_Subscriptions.SubID
  AND Active_Subscriptions.AsubID = :AsubID;
";
        $stmt2 = $c->prepare($Request2);
        $stmt2->bindParam(':AsubID', $AsubID, PDO::PARAM_INT);
        $stmt2->execute();

        $subscriptionType = $stmt2->fetchColumn();
          
        switch ($subscriptionType) {
            case 'Weekly':
                $newEndDate = date('Y-m-d', strtotime($oldEndDate . ' +7 days'));
                break;
            case 'Monthly':
                $newEndDate = date('Y-m-d', strtotime($oldEndDate . ' +1 month'));
                break;
            case 'Quarterly':
                $newEndDate = date('Y-m-d', strtotime($oldEndDate . ' +3 months'));
                break;
            case 'Yearly':
                $newEndDate = date('Y-m-d', strtotime($oldEndDate . ' +1 year'));
                break;
            default:
                $newEndDate = $oldEndDate;
                break;
        }

        
        $Request3 = "UPDATE Active_Subscriptions SET EndDate = :NewEndDate WHERE AsubID = :AsubID";
        $stmt3 = $c->prepare($Request3);
        $stmt3->bindParam(':NewEndDate', $newEndDate, PDO::PARAM_STR);
        $stmt3->bindParam(':AsubID', $AsubID, PDO::PARAM_INT);
        $result = $stmt3->execute();

    if ($result) {
        echo '<div class="container_right2">';
        echo '<br>';  
    echo "Subscription updated successfully!";
    echo " new date: $newEndDate";
    echo '<br>';  echo '<br>';  
    echo '</div>';  
} else {
    echo '<div class="container_right2">';
    echo '<br>';  
    echo "Failed to renew the Subscription.";
    echo '<br>';  echo '<br>';  
    echo '</div>';  
}
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
}


function View_Renewal_Requests($c){
    try {
      $request = "SELECT R.RenewalID, C.FirstName, C.LastName, C.Email, A.AsubID, R.Price, R.ProofOfPaymentID, I.image_data AS ProofOfPaymentData FROM Renew_Requests R JOIN Active_Subscriptions A ON R.AsubID = A.AsubID JOIN SubscriptionRequest S ON A.SubID = S.SubID JOIN Clients C ON S.ClientID = C.ClientID LEFT JOIN images I ON R.ProofOfPaymentID = I.id;
";
      $stmt = $c->prepare($request);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return []; 
    }
  }

function Get_New_ID_Renew($c, $tableName, $ID_var_Name) {
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

function Check_if_has_to_pay($c, $ClientID){
    try {
        $request = "SELECT Price FROM SubscriptionRequest WHERE ClientID = :ClientID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
        $stmt->execute();
        $Price = $stmt->fetchColumn();
        
        if ($Price == 'Free') {
            return 0;
        } else {
            return $Price;
        }
     
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    } 
}



function hasRenewalRequest($c, $clientID)
{
    try {
        $request = "SELECT COUNT(*) FROM Renew_Requests WHERE AsubID IN (SELECT AsubID FROM Active_Subscriptions WHERE SubID IN (SELECT SubID FROM SubscriptionRequest WHERE ClientID = :ClientID))";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $clientID, PDO::PARAM_INT);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


function Delete_Renewal_Request($c, $RenewalID) {
    echo '<div class="container_right2">';
    echo '<br>'; 
    try {
        $request = "DELETE FROM Renew_Requests WHERE RenewalID = :RenewalID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':RenewalID', $RenewalID, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "Subscription $RenewalID deleted"; 

        return true;
    } catch (PDOException $e) {
        return false;
    }
    echo '<br>';  echo '<br>';  
    echo '</div>';   

}







?>
</body>
</html>
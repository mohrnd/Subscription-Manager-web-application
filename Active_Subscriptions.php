<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_cont.css">
</head>
<body>
<?php
class Active_Subscriptions extends SETRAM_Database{
    
    
     public $AsubID, $SubID, $ActivationDate, $EndDate;

    public function __construct($AsubID, $SubID, $ActivationDate, $EndDate) {
        parent::__construct('');
        $this->AsubID = $AsubID;
        $this->SubID = $SubID;
        $this->ActivationDate = $ActivationDate;
        $this->EndDate = $EndDate;
    }

    public function Active_Subscriptions_Table_Creation($c) {
       $Request = "CREATE TABLE IF NOT EXISTS Active_Subscriptions (
    AsubID int(5) primary key,
    SubID int(5),
    ActivationDate date,
    EndDate date
)";

        $x = $c->prepare($Request);
        $e = $x->execute();
        if (!$e) {
            echo '<div class="container_right2">';
            echo '<br>';  
            echo "Active_Subscriptions table creation error: " . print_r($x->errorInfo(), true) . " <br><br>";
            echo '</div>';  
        
        } else {
            echo '<div class="container_right2">';
            echo '<br>';  
            echo "Active_Subscriptions table created successfully <br><br>";
            echo '</div>';  
        }
    }

    public function Activate_Subscription($c) {
        
        $Request = "INSERT INTO Active_Subscriptions (AsubID, SubID, ActivationDate, EndDate) 
                    VALUES (:AsubID, :SubID, :ActivationDate, :EndDate)";
        $x = $c->prepare($Request);
        $e = $x->execute([
        ':AsubID'=>$this->AsubID,
        ':SubID'=>$this->SubID,
        ':ActivationDate'=>$this->ActivationDate,
        ':EndDate'=>$this->EndDate
        ]);

        if (!$e) { 
            echo '<div class="container_right2">';
            echo '<br>'; 
            echo "Subscription activation error: " . print_r($x->errorInfo(), true) . " <br><br>";
            echo '<div>';
        } else {  
            echo '<br>';
            echo '<br>';
            echo '<div class="container_right2">';
            echo '<br>';
            echo "Subscription has been Activated successfully <br><br>";
            echo '<div>';
        }
    }
}
function Verify_if_client_has_Active_Subscription($c, $ClientID) {
    try {
        $request = "SELECT Active_Subscriptions.*, Clients.FirstName, Clients.LastName  
                    FROM Active_Subscriptions
                    JOIN SubscriptionRequest ON SubscriptionRequest.SubID = Active_Subscriptions.SubID
                    JOIN Clients ON Clients.ClientID = SubscriptionRequest.ClientID 
                    WHERE Clients.ClientID = :ClientID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            $subscription = $result[0];
            $message = "Client {$subscription['FirstName']} {$subscription['LastName']} has an active subscription that will end on {$subscription['EndDate']}.";
        } else {
            $message = "Client with ID {$ClientID} doesn't have an active subscription.";
        }

        echo $message;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


function Verify_if_client_has_Active_Subscription_Client_ver($c, $ClientID) {
    try {
        $request = "SELECT Active_Subscriptions.*, Clients.FirstName, Clients.LastName  
                    FROM Active_Subscriptions
                    JOIN SubscriptionRequest ON SubscriptionRequest.SubID = Active_Subscriptions.SubID
                    JOIN Clients ON Clients.ClientID = SubscriptionRequest.ClientID 
                    WHERE Clients.ClientID = :ClientID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':ClientID', $ClientID, PDO::PARAM_INT);
        $stmt->execute();

        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        if (!empty($result)) {
            $subscription = $result[0]; 
            $message = "<div><a style='color: red;'>&nbsp;&nbsp;&nbsp;&nbsp;{$subscription['FirstName']} {$subscription['LastName']} your subscription is active and will end on {$subscription['EndDate']}.</a></div>";

        } else {
            $message = "<div><a style='color: white;'>&nbsp;&nbsp;&nbsp;&nbsp;you don't have an active subscription/ your subscription request is pending.</a></div>";

            
        }

        echo $message;
        
    } catch (PDOException $e) {
        
        echo "Error: " . $e->getMessage();
    }
}

function Get_New_ID_ActRequest($c, $tableName, $ID_var_Name) {
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
function View_Active_subscriptions($c) {
    try {
        $request = "SELECT 
                        Active_Subscriptions.AsubID, 
                        Clients.FirstName,
                        Clients.LastName,
                        Clients.Email,
                        SubscriptionRequest.ClientID, 
                        SubscriptionRequest.Category, 
                        SubscriptionRequest.SubscriptionType, 
                        SubscriptionRequest.Price,
                        SubscriptionRequest.Plan,
                        Active_Subscriptions.ActivationDate, 
                        Active_Subscriptions.EndDate
                    FROM Active_Subscriptions
                    JOIN SubscriptionRequest ON Active_Subscriptions.SubID = SubscriptionRequest.SubID
                    JOIN Clients ON SubscriptionRequest.ClientID = Clients.ClientID";
        $stmt = $c->prepare($request);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return []; 
    }
}


function Delete_Active_Sub($c) {
        $Date = date('Y-m-d');
        $request = "DELETE FROM Active_Subscriptions WHERE EndDate = :Date";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':Date', $Date, PDO::PARAM_INT);
        $stmt->execute();
}

function Get_SubscriptionType($c, $SubID) {
    try {
        $request = "SELECT SubscriptionRequest.SubscriptionType 
                    FROM SubscriptionRequest 
                    WHERE SubscriptionRequest.SubID = :SubID";
        $stmt = $c->prepare($request);
        $stmt->bindParam(':SubID', $SubID, PDO::PARAM_INT);
        $stmt->execute();
        
        // Use fetchColumn to retrieve a single value
        $subscriptionType = $stmt->fetchColumn();
        
        return $subscriptionType !== false ? $subscriptionType : null;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}
function Status($c, $SubID) {
    $request = "SELECT SubID FROM Active_Subscriptions WHERE Active_Subscriptions.SubID = :subID";
    
    $stmt = $c->prepare($request);
    $stmt->bindParam(':subID', $SubID, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
function get_Number_Subscription_Requsts($c) {
    try {
        $request = "SELECT COUNT(*) AS numSubscriptionRequests
                    FROM SubscriptionRequest";
        $stmt = $c->prepare($request);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['numSubscriptionRequests'];
        } else {
            return 0;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return 0; // Return 0 in case of an error
    }
}

function get_Number_active_Subscriptions($c) {
    try {
        $request = "SELECT COUNT(*) AS numactivesubs
                    FROM Active_Subscriptions";
        $stmt = $c->prepare($request);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['numactivesubs'];
        } else {
            return 0;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return 0; // Return 0 in case of an error
    }
}


Delete_Active_Sub($connection);


?>
</body>
</html>

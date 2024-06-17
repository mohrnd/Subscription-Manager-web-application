<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_agent.css">
</head>
<body>


<?php
session_start();
require_once 'Database.php';
require 'Active_Subscriptions.php';

// Check if the user is logged in
unset($_SESSION['user_id_Client']);
unset($_SESSION['user_id_Admins']);
unset($_SESSION['uploaded_image_id']);


if (isset($_SESSION['user_id_Agent'])) {
    $userId = $_SESSION['user_id_Agent'];
    echo '<div class="titre1">';
    echo "<h1>WELCOME!! AGENT ID: $userId</h1>";
    echo '</div>';//titre
    
$Request = "SELECT * FROM images, Agents WHERE AgentID = $userId AND id = ProfilePictureID";
$stmt = $connection->prepare($Request);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);



echo '<div class="top">';

    echo '<form method="post">';
   
    echo '<input class="bouton-left" type="submit" name="ChangePswd" value="Change my Password">';
    echo '<input class="bouton-left" type="submit" name="upchpfpforms" value="Upload/Change my profile picture">';
    echo '<input class="bouton-left" type="submit" name="ViewSubscriptionRequests" value="View/approve subscription requests">';
    echo '<input class="bouton-left" type="submit" name="Renewsubscriptions" value="Renew subscriptions">';
    echo '<input class="bouton-left" type="submit" name="Verifyifsubisactive" value="Verify Subscription">';
    echo '<input class="bouton-left" type="submit" name="ViewActivSubs" value="View Active subscriptions">';
    echo '<input class="bouton-left2" type="submit" name="Logoff" value="Logoff">';
    echo '</form>';

}
else {
   
    header("Location: login.php");
    exit();
}
echo '</div>';//top
/////////////////////////////////////////////////////////////////////////////////////////////////show pfp

echo '<div class="bottom">';
echo '<div class="bottomtop">';
if ($result) {
    $imageData = base64_encode($result['image_data']);

    echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="img2">';

} else {
    echo "Image not found";
}
echo '</div>';//bottomtop
///change pfp
echo '<div class="bottombottom">';
if (isset($_POST["upchpfpforms"])) {
    echo '<div class="container_right">';
    echo '<br>';
    echo '<div>Please upload your profile picture (make sure its a picture of you)</div><br>';
    echo '<form enctype="multipart/form-data" action="" method="POST">';
    echo '<div><label for="filee">Choose Image:</label>';
    echo '<input type="file" id="filee" name="filee" accept="image/*" required></div><br>';
    echo '<div><input class="formbot" type="submit" name="final_submit_pfp" value="Upload"></div>';
    echo '</div>';
}
if (isset($_POST["final_submit_pfp"])) {
    require 'pictures_subscriptions.php';
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        echo '<div class="container_right">';
        echo '<br>';  
        echo "Your profile picture has been changed";
        echo '<br>';  
        echo '<br>';  
        echo '</div>';
        if (!$IDPic) {
            echo '<div class="container_right">';
            echo '<br>';  
            echo "Error uploading IDPic";
            echo '<br>';   echo '<br>';  
            echo '</div>';
            exit();
        }
    }
    
uploadrenewPfp_agents($connection,$userId, $IDPic);
    
    
}



if (isset($_POST["ViewActivSubs"])) {

require_once 'Active_Subscriptions.php';

$activeSubscriptionData = View_Active_subscriptions($connection);

if (!empty($activeSubscriptionData)) {
    ?>
    
    <table border='1'>
        <tr>
            <th>AsubID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Client ID</th>
            <th>Category</th>
            <th>Subscription Type</th>
            <th>Price</th>
            <th>Plan</th>
            <th>Activation Date</th>
            <th>End Date</th>
        </tr>
        <?php foreach ($activeSubscriptionData as $subscription) : ?>
            <tr>
                <td><?= $subscription['AsubID'] ?></td>
                <td><?= $subscription['FirstName'] ?></td>
                <td><?= $subscription['LastName'] ?></td>
                <td><?= $subscription['Email'] ?></td>
                <td><?= $subscription['ClientID'] ?></td>
                <td><?= $subscription['Category'] ?></td>
                <td><?= $subscription['SubscriptionType'] ?></td>
                <td><?= $subscription['Price'] ?></td>
                <td><?= $subscription['Plan'] ?></td>
                <td><?= $subscription['ActivationDate'] ?></td>
                <td><?= $subscription['EndDate'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
} else {
    echo "<p>No active subscriptions found.</p>";
}

   
    
    
    
    
    
}

if (isset($_POST["ViewSubscriptionRequests"])) {
require_once 'Subscription_Request.php';

    $subscriptionData = View_Subscription_Requests($connection);

    if (!empty($subscriptionData)) {
        ?>
        
        <table border='1'>
            <tr>
                <th>SubID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Category</th>
                <th>Subscription Type</th>
                <th>Price</th>
                <th>Client ID</th>
                <th>Plan</th>
                <th>ID Pic</th>
                <th>Proof</th>
                <th>Payment Proof</th>
                <th>Action</th>
                <th>Status</th>
                
            </tr>
            <?php foreach ($subscriptionData as $subscription) : ?>
                <tr>
                    <td><?= $subscription['SubID'] ?></td>
                    <td><?= $subscription['FirstName'] ?></td>
                    <td><?= $subscription['LastName'] ?></td>
                    <td><?= $subscription['Email'] ?></td>
                    <td><?= $subscription['Date'] ?></td>
                    <td><?= $subscription['Category'] ?></td>
                    <td><?= $subscription['SubscriptionType'] ?></td>
                    <td><?= $subscription['Price'] ?></td>
                    <td><?= $subscription['ClientID'] ?></td>
                    <td><?= $subscription['Plan'] ?></td>
                    <td>
                        <?php
                            // Display the ID Pic if available
                            if (!empty($subscription['IDPicData'])) {
                                $imageData = base64_encode($subscription['IDPicData']);
                                echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="ID Pic" style="width: 100px; height: 100px;">';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            
                            if (!empty($subscription['ProofData'])) {
                                $imageData = base64_encode($subscription['ProofData']);
                                echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Proof" style="width: 100px; height: 100px;">';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            
                            if (!empty($subscription['PaymentProofData'])) {
                                $imageData = base64_encode($subscription['PaymentProofData']);
                                echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Payment Proof" style="width: 100px; height: 100px;">';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </td>
                    <td>
                        <form style="display:inline;" action="" method="post">
                            <input type="hidden" name="deleteSubscriptionID" value="<?= $subscription['SubID']; ?>">
                            <button type="submit" name="DeleteSubscriptionSubmit">Delete</button>
                        </form>
                        <form style="display:inline;" action="" method="post">
                            <input type="hidden" name="approveSubscriptionID" value="<?= $subscription['SubID']; ?>">
                            <button type="submit" name="ApproveSubscriptionSubmit">Approve</button>
                             <input type="hidden" name="SubID" value="<?= $subscription['SubID']; ?>">
                        </form>
                    </td> 
                    <td>
                    
                        <?php
                        require_once 'Active_Subscriptions.php';
                         $SubID = $subscription['SubID'];
                        $Status = Status($connection, $SubID);
                        if ($Status) {
                             echo "Active";
                         } else {
                         echo "Pending";
                           }
                        
                        
                        
                        
                        
                        
                        ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } else {
        echo "<p>No subscription requests found.</p>";
    }    
}
if (isset($_POST["Logoff"])) {
    session_destroy();
unset($_SESSION['user_id_Agent']);
echo '<script>window.location.href = "login.php";</script>';
exit();   
}

function Change_Password_Forms(){
    echo '<div class="container_right">';
    echo '<h2 class="titre">Change Password</h2>';
    echo '<form action="" method="post">';
    echo '    <label class="formtxt" for="new_password">New Password:</label>';
    echo '    <input class="form" type="password" id="new_password" name="new_password" required><br>';
    echo '    <label  class="formtxt" for="confirm_password">Confirm Password:</label>';
    echo '    <input class="form" type="password" id="confirm_password" name="confirm_password" required><br>';
    echo '    <input class="formbot" type="submit" name="change_password" value="Change Password">';
    echo '</form>';
    echo '</div>';
}

if (isset($_POST["ChangePswd"])) {
    Change_Password_Forms();
}


if (isset($_POST["Renewsubscriptions"])) {

    require_once 'Renew_Request.php';

    $subscriptionData = View_Renewal_Requests($connection); 

    if (!empty($subscriptionData)) {
        ?>
        <h2>Renewal Requests</h2>
        <table border='1'>
            <tr>
                <th>RenewalID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Activated Subscription ID</th>
              
                <th>Price</th>
<!--                <th>ProofOfPaymentID</th>-->
                <th>ProofOfPayment</th>
                <th>Action</th>
            </tr>
            <?php foreach ($subscriptionData as $subscription) : ?>
                <tr>
                    <td><?= $subscription['RenewalID'] ?></td>
                    <td><?= $subscription['FirstName'] ?></td>
                    <td><?= $subscription['LastName'] ?></td>
                    <td><?= $subscription['Email'] ?></td>
                    <td><?= $subscription['AsubID'] ?></td>
                    <td><?= $subscription['Price'] ?></td>
                    <td>
                        <?php
                        // Display the Proof if available
                        if (!empty($subscription['ProofOfPaymentData'])) {
                            $imageData = base64_encode($subscription['ProofOfPaymentData']);
                            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Proof" style="width: 100px; height: 100px;">';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                        <td>
                        <form style="display:inline;" action="" method="post">
                            <input type="hidden" name="deleterenewID" value="<?= $subscription['RenewalID']; ?>">
                            <button type="submit" name="DeleterenewSubmit">Delete</button>
                        </form>
                        <form style="display:inline;" action="" method="post">
                            <input type="hidden" name="approverenewID" value="<?= $subscription['AsubID']; ?>">
                            <input type="hidden" name="deleterenewID2" value="<?= $subscription['RenewalID']; ?>">
                            <button type="submit" name="ApproverenewSubmit">Approve</button>
                        </form>
                    </td> 
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } else {
        echo '<div class="container_right">';
        echo '<br>';  
        echo "<p>No renewal requests found.</p>";
        echo '<br>';  echo '<br>';  
        echo '</div>';
    }
}

if (isset($_POST["DeleterenewSubmit"])) {
        $RenewalID = $_POST["deleterenewID"];
        require_once 'Renew_Request.php';
        Delete_Renewal_Request($connection, $RenewalID);
    } 

if (isset($_POST["ApproverenewSubmit"])) {
require_once 'Renew_Request.php';
$Objj = new Renew_Requests('','','','', '');
$Objj->Renew_Request_Table_Creation($connection);
        $AsubID = $_POST["approverenewID"];
        $RenewalID = $_POST["deleterenewID2"];
         update_activated_subscription($connection, $AsubID);
        Delete_Renewal_Request($connection, $RenewalID);
    } 


if (isset($_POST["change_password"])) {
    
    
    // Validate form data
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword != $confirmPassword) {
        echo '<div class="container_right">';
        echo '<br>';  
        echo "Error: New password and confirm password do not match.";
        echo '<br>';   echo '<br>';  
        echo '</div>';
        exit();
        
    }

    // Hacher le mot de passe avec md5()
    $hashedPassword = md5($newPassword);


    $Request = "UPDATE Agents SET Password = :NewPassword WHERE AgentID = :AgentID";
    $stmt = $connection->prepare($Request);
    $stmt->execute([
        ':NewPassword' =>$hashedPassword,
        ':AgentID' => $userId
    ]);
    echo '<div class="container_right">';
    echo '<br>';  
    echo "Password changed successfully!";
    echo '<br>';  echo '<br>';   
    echo '</div">';
    }


if (isset($_POST["Verifyifsubisactive"])) {
    Delete_Active_Sub($connection);
    echo '<div class="container_right">';
    echo '<h2 class="titre">Verify subscription</h2><br>';
    echo '<form action="" method="post">';
    echo '    <label for="client_id" class="formtxt">Client ID:</label>';
    echo '    <input class="form" type="number" id="client_id" name="client_id" required><br>';
    echo '    <input type="submit" class="formbot" name="Verify" value="Verify">';
    echo '</form>';
    echo '</div>';
}

if (isset($_POST["Verify"])) {
    echo '<div class="container_right">';
    echo '<br>';  
    require_once 'Active_Subscriptions.php';
    $ClientID = $_POST["client_id"];
    Verify_if_client_has_Active_Subscription($connection, $ClientID); 
    echo '<br>';  echo '<br>';  
    echo '</div>';  
}
if (isset($_POST["DeleteSubscriptionSubmit"])) {
        $SubID = $_POST["deleteSubscriptionID"];
        require_once 'Subscription_Request.php';
        Delete_Request($connection, $SubID);
    } 

if (isset($_POST["ApproveSubscriptionSubmit"])) {
require_once 'Active_Subscriptions.php';
$Objj = new Active_Subscriptions('','','','');
$Objj->Active_Subscriptions_Table_Creation($connection);
Delete_Active_Sub($connection);
        $SubID = $_POST["approveSubscriptionID"];
        require_once 'Active_Subscriptions.php';
        $SubscriptionType = Get_SubscriptionType($connection, $SubID);
    
        $ActivationDate = date('Y-m-d');
        if ($SubscriptionType == 'Weekly'){
        $EndDate = date('Y-m-d', strtotime($ActivationDate . ' +7 days'));}
        elseif ($SubscriptionType == 'Monthly'){
        $EndDate = date('Y-m-d', strtotime($ActivationDate . ' +1 month'));}
        elseif ($SubscriptionType == 'Quarterly'){
        $EndDate = date('Y-m-d', strtotime($ActivationDate . ' +3 months'));}
        elseif ($SubscriptionType == 'Yearly'){
        $EndDate = date('Y-m-d', strtotime($ActivationDate . ' +1 year'));}
    
        $AsubID = Get_New_ID_ActRequest($connection, 'Active_Subscriptions', 'AsubID');
        $OBj = new Active_Subscriptions($AsubID, $SubID, $ActivationDate, $EndDate);
        $OBj->Activate_Subscription($connection);
    }  

    echo '</div>';//bottombottom
    echo '</div>';//bottom
?>


</body>
</html>


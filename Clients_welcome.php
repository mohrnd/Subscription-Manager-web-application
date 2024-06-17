<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_client.css">
</head>
<body>

<?php
session_start();
require_once 'Database.php';
require_once 'Active_Subscriptions.php';
require_once 'pictures_subscriptions.php';
unset($_SESSION['user_id_Agent']);
unset($_SESSION['user_id_Admins']);

echo '<div class="top">';
 
// Check if the user is logged in
if (isset($_SESSION['user_id_Client'])) {
    $userId = $_SESSION['user_id_Client'];
    echo "<h1>Client ID: $userId</h1><br/>";
    
    Verify_if_client_has_Active_Subscription_Client_ver($connection, $userId);
$Request = "SELECT * FROM images, Clients WHERE ClientID = $userId AND id = ProfilePictureID";
$stmt = $connection->prepare($Request);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);


echo '</div>';//top


echo '<div class="bottom">';

echo '<div class="bottom-left">';
    echo '<form method="post">';
    if ($result) {
        $imageData = base64_encode($result['image_data']);
    
        echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="img2">';
    
    } else {
        echo "Image not found";
    }

    echo '<input type="submit" name="ChangePswd" class="bouton-left" value="Change my Password">';
    echo '<input type="submit" name="upchpfpforms" class="bouton-left" value="Upload/Change my profile picture">';
    echo '<input type="submit" name="Subscribe" class="bouton-left" value="Subscribe">';
    echo '<input type="submit" name="RenewSub" class="bouton-left" value="Renew my current subscription">';
    echo '<input type="submit" name="Logoff" class="bouton-left2" value="Logoff">';
    echo '</form>';

    echo '</div>';//bottomleft
////////// print pfp ////////////////////////////////////////////////////

echo '<div class="bottom-right">';
echo '<div class="container_right">';
echo "<br>";
echo '<div><a class="numbr">If you want to renew your current subscription, then you have to click on renew before the subscription ends</a></div>';
echo '<div><a class="numbr">If you want to change your subscription, just wait for your current subscription to end and create a new one</a></div>';
echo "<br>";
///change pfp
if (isset($_POST["upchpfpforms"])) {
    
    echo "<br>";
    echo '<div>Please upload your profile picture (make sure its a picture of you)</div>';
    echo '<form enctype="multipart/form-data" action="" method="POST">';echo "<br>";
    echo '<div><label for="filee">Choose Image:</label>';
    echo '<input type="file" id="filee" name="filee" accept="image/*" required></div>';echo "<br>";
    echo '<div><input type="submit" class="formbot3" name="final_submit_pfp" value="Upload"></div>';
}
if (isset($_POST["final_submit_pfp"])) {
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        echo "Your profile picture has been changed";
        if (!$IDPic) {
            echo "Error uploading IDPic";
      
            exit();
        }
    }
    
uploadrenewPfp_clients($connection,$userId, $IDPic);
    
    
}




    if (isset($_POST['RenewSub'])) {
        require_once 'Renew_Request.php';
       $ClientID = $_SESSION['user_id_Client'];
        $tempbool = hasRenewalRequest($connection, $ClientID);
        if ($tempbool == 'true'){ echo "you've already submitted a renewal request";}
        else {
        $ClientID = $_SESSION['user_id_Client'];
        $pay = Check_if_has_to_pay($connection, $ClientID);

        if ($pay > 0) {
            echo "<br>";
            echo '<div>Please upload your proof of payment</div>';
            echo '<form enctype="multipart/form-data" action="" method="POST"><br>';
            echo '<div><label for="file">Choose Image:</label>';
            echo '<input type="file" id="file" name="file" accept="image/*" required></div>';
            echo "<br>";
            echo '<div><input class="formbot3" type="submit" name="final_submit1" value="Renew"></div>';
            echo '</form>';
        } else {
            echo '<form enctype="multipart/form-data" action="" method="POST">';echo "<br>";
            echo 'Renew (since your subscription was free, you dont have to upload any documents)';echo "<br>";
            echo '<div><input type="submit" class="formbot3" name="final_submit2" value="Renew"></div>';
            echo '</form>';
        }
    }}

    if (isset($_POST['final_submit1'])) {
        require_once 'Renew_Request.php';

        $ClientID = $_SESSION['user_id_Client'];
        $RenewalID = Get_New_ID_Renew($connection, 'Renew_Requests', 'RenewalID');
        $AsubID = GetActSubID($connection, $ClientID);
        $Price = Check_if_has_to_pay($connection, $ClientID);
        $SubscriptionType = null;
        $ProofOfPaymentID = null;

        if (isset($_FILES['file'])) {
            $ProofOfPaymentID = handleImageUpload($_FILES['file'], $connection);
            if (!$ProofOfPaymentID) {
                echo "Error uploading Proof";
                exit();
            }
        }

        $ReqOBJ = new Renew_Requests($RenewalID, $AsubID, $SubscriptionType, $Price, $ProofOfPaymentID);
        $ReqOBJ->Renew_Request_Table_Creation($connection);
        $ReqOBJ->Submit_Renewal_Request($connection);
    }

    if (isset($_POST['final_submit2'])) {
        require_once 'Renew_Request.php';
        $ClientID = $_SESSION['user_id_Client'];
        $RenewalID = Get_New_ID_Renew($connection, 'Renew_Requests', 'RenewalID');
        $AsubID = GetActSubID($connection, $ClientID);
        $Price = 'Free';
        $SubscriptionType = null;
        $ProofOfPaymentID = null;
        $ReqOBJ = new Renew_Requests($RenewalID, $AsubID, $SubscriptionType, $Price, $ProofOfPaymentID);
        $ReqOBJ->Renew_Request_Table_Creation($connection);
        $ReqOBJ->Submit_Renewal_Request($connection);
    }
//////// change it, check if current client has an active subscription already
    if (isset($_POST['Subscribe'])) {
        echo '<script>window.location.href = "New_Subscription_Request_Clients.php";</script>';
        //header("Location: New_Subscription_Request_Clients.php");
        exit();
    }

} else {
    header("Location: login.php");
    exit();
}

function Change_Password_Forms(){
    echo '<h2 class="titre">Change Password</h2>';
    echo '<form action="" method="post">';
    echo '    <label class="formtxt" for="new_password">New Password:</label>';
    echo '    <input class="form" type="password" id="new_password" name="new_password" required><br>';
    echo '    <label class="formtxt" for="confirm_password">Confirm Password:</label>';
    echo '    <input class="form" type="password" id="confirm_password" name="confirm_password" required><br>';
    echo '    <input class="formbot3" type="submit" name="change_password" value="Change Password">';
    echo '</form>';
}

if (isset($_POST["ChangePswd"])) {
    Change_Password_Forms();
}

if (isset($_POST["Logoff"])) {
    session_destroy();
    unset($_SESSION['user_id_Client']);
    echo '<script>window.location.href = "login.php";</script>';
    exit();
}

if (isset($_POST["change_password"])) {
    // Validate form data
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword != $confirmPassword) {
        echo "Error: New password and confirm password do not match.";
        exit();
    }

    // Hash the password with md5()
    $hashedPassword = md5($newPassword);

    $Request = "UPDATE Clients SET Password = :NewPassword WHERE ClientID = :ClientID";
    $stmt = $connection->prepare($Request);
    $stmt->execute([
        ':NewPassword' => $hashedPassword,
        ':ClientID' => $userId
    ]);

    echo "Password changed successfully!";
}

echo '</div>';//containerright
echo '</div>';//bottomright
echo '</div>';//bottom

?>

</body>
</html>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_sub_req.css">
    <title>Subscription Form</title>
</head>

<body>
    

<?php

echo '<div class="cont">';
echo '<div class="left">';

session_start();
require_once 'Database.php';
require 'Subscription_Request.php';
require 'pictures_subscriptions.php';

function getSubscriptionPrice($category, $plan, $subscription_type){
    $prices = [
        'elementary_school_student' => [
            'Tram' => ['Weekly' => 'Free', 'Monthly' => 'Free', 'Quarterly' => 'Free', 'Yearly' => 'Free'],
            'Metro+Tram' => ['Weekly' => 'Free', 'Monthly' => 'Free', 'Quarterly' => 'Free', 'Yearly' => 'Free'],
        ],
        'student' => [
            'Tram' => ['Weekly' => 100, 'Monthly' => 500, 'Quarterly' => 1200, 'Yearly' => 5000],
            'Metro+Tram' => ['Weekly' => 150, 'Monthly' => 700, 'Quarterly' => 1500, 'Yearly' => 7000],
        ],
        'classic' => [
            'Tram' => ['Weekly' => 150, 'Monthly' => 600, 'Quarterly' => 1400, 'Yearly' => 6000],
            'Metro+Tram' => ['Weekly' => 190, 'Monthly' => 800, 'Quarterly' => 1700, 'Yearly' => 8000],
        ],
        'junior' => [
            'Tram' => ['Weekly' => 100, 'Monthly' => 600, 'Quarterly' => 1400, 'Yearly' => 5500],
            'Metro+Tram' => ['Weekly' => 150, 'Monthly' => 750, 'Quarterly' => 1600, 'Yearly' => 7500],
        ],
        'senior' => [
            'Tram' => ['Weekly' => 100, 'Monthly' => 600, 'Quarterly' => 1400, 'Yearly' => 5500],
            'Metro+Tram' => ['Weekly' => 150, 'Monthly' => 750, 'Quarterly' => 1600, 'Yearly' => 7500],
        ],
        'conventionally_subscribed' => [
            'Tram' => ['Weekly' => 'Free', 'Monthly' => 'Free', 'Quarterly' => 'Free', 'Yearly' => 'Free'],
            'Metro+Tram' => ['Weekly' => 'Free', 'Monthly' => 'Free', 'Quarterly' => 'Free', 'Yearly' => 'Free'],
        ],
    ];

    if (isset($prices[$category][$plan][$subscription_type])) {
        return $prices[$category][$plan][$subscription_type];
    } else {
        return 'Price not available';
    }
}

if (isset($_SESSION['user_id_Client'])) {
    $userId = $_SESSION['user_id_Client'];
    $tempint = Verify_if_client_has_subscription_request($connection, $userId);
    if(!$tempint){

?>


        <h2 class="titre">Subscription Form</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            
            <label class="formtxt" for="subscription_type">Subscription Type:</label>
            <select class="gender" name="subscription_type" id="subscription_type" required>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Yearly">Yearly</option>
            </select><br><br>

            <label class="formtxt" for="category">Category:</label>
            <select class="gender" name="category" id="category" required>
                <option value="elementary_school_student">Elementary School Student</option>
                <option value="student">Student</option>
                <option value="classic">Classic</option>
                <option value="junior">Junior</option>
                <option value="senior">Senior</option>
                <option value="conventionally_subscribed">Conventionally Subscribed</option>
            </select><br><br>

          
            <label class="formtxt" for="plan">Select Plan:</label>
            <select class="gender" name="plan" id="plan" required>
                <option value="Tram">Tram</option>
                <option value="Metro+Tram">Metro+Tram</option>
            </select><br><br>

       
            <input class="formbot" name="Next" type="submit" value="Next">
            <a class="formbot" href="Clients_welcome.php">Return to menu<a>
            </div>
        </form>

        
        <div class="right">

<?php
    }
    
    else{
        require_once 'Active_Subscriptions.php';
        Verify_if_client_has_Active_Subscription_Client_ver($connection, $userId);
        exit();
    }
        if (isset($_POST["Next"])) {
            $subscription_type = isset($_POST["subscription_type"]) ? $_POST["subscription_type"] : "";
            $category = isset($_POST["category"]) ? $_POST["category"] : "";
            $plan = isset($_POST["plan"]) ? $_POST["plan"] : "";

            echo "<h2  class='titre' >Subscription Information</h2>";
            echo "<p class='formtxt' >Subscription Type: $subscription_type</p>";
            echo "<p class='formtxt'>Category: $category</p>";
            echo "<p class='formtxt'>Plan: $plan</p>";
            $price = getSubscriptionPrice($category, $plan, $subscription_type);
            echo "<p class='formtxt'>Price: $price</p>";

            $_SESSION['subscription_info'] = [
                'subscription_type' => $subscription_type,
                'category' => $category,
                'plan' => $plan,
                'price' => $price,
            ];

            $categories_with_proof_only = ['elementary_school_student'];
            $categories_with_id_and_proof = ['student', 'conventionally_subscribed'];
            $categories_with_id_only = ['classic', 'junior', 'senior'];

            if (in_array($category, $categories_with_proof_only) && $price != 'Free') {
                echo '<div class="formtxt">Please upload your proof</div>';
                echo '<form enctype="multipart/form-data" action="" method="POST">';
                echo '<div><label class="formtxt" for="file">Choose Image:</label>';
                echo '<input type="file" id="file" name="file" accept="image/*" required></div>';
                echo '<div>Please upload your payment proof</div>';
                echo '<div><label for="fileee">Choose Image:</label></div>';
                echo '<input type="file" id="fileee" name="fileee" accept="image/*" required></div>';
                echo '<div><input type="submit" name="final_submit1" value="Submit"></div>';
                echo '</form>';
            } 
            elseif (in_array($category, $categories_with_proof_only) && $price = 'Free') {
                echo '<div class="formtxt">Please upload your proof</div>';
                echo '<form enctype="multipart/form-data" action="" method="POST">';
                echo '<div><label class="formtxt" for="file">Choose Image:</label>';
                echo '<input type="file" id="file" name="file" accept="image/*" required></div><br>';
                echo '<div><input class="formbot" type="submit" name="final_submit2" value="Submit"></div>';
                echo '</form>';
            }
            elseif (in_array($category, $categories_with_id_and_proof)  && $price != 'Free') {
                echo '<div class="formtxt">Please upload your ID</div>';
                echo '<form enctype="multipart/form-data" action="" method="POST">';
                echo '<div><label class="formtxt" for="filee">Choose Image:</label>';
                echo '<input  type="file" id="filee" name="filee" accept="image/*" required></div>';
                echo '<div class="formtxt" >Please upload your payment proof</div>';
                echo '<div class="formtxt" ><label for="fileee">Choose Image:</label>';
                echo '<input type="file" id="fileee" name="fileee" accept="image/*" required></div>';
                echo '<div class="formtxt">Please upload your Proof</div>';
                echo '<div class="formtxt"><label for="file">Choose Image:</label>';
                echo '<input type="file" id="file" name="file" accept="image/*" required></div>';
                echo '<div><input class="formbot" type="submit" name="final_submit3" value="Submit"></div>';
                echo '</form>';

            } 
                 elseif (in_array($category, $categories_with_id_and_proof)  && $price = 'Free') {
                echo '<div class="formtxt">Please upload your ID</div>';
                echo '<form enctype="multipart/form-data" action="" method="POST">';
                echo '<div><label class="formtxt" for="filee">Choose Image:</label>';
                echo '<input  type="file" id="filee" name="filee" accept="image/*" required></div>';
                echo '<div class="formtxt">Please upload your Proof</div>';
                echo '<div class="formtxt"><label for="file">Choose Image:</label>';
                echo '<input type="file" id="file" name="file" accept="image/*" required></div>';
                echo '<div><input class="formbot" type="submit" name="final_submit5" value="Submit"></div>';
                echo '</form>';

            }
            
            elseif (in_array($category, $categories_with_id_only) && $price > 0 ) {
                echo '<div class="formtxt">Please upload your ID</div>';
                echo '<form enctype="multipart/form-data" action="" method="POST">';
                echo '<div><label class="formtxt" for="filee">Choose Image:</label>';
                echo '<input type="file" id="filee" name="filee" accept="image/*" required></div>';
                echo '<div class="formtxt">Please upload your payment proof</div>';
                echo '<label class="formtxt" for="fileee">Choose Image:</label>';
                echo '<div><input type="file" id="fileee" name="fileee" accept="image/*" required></div>';
                echo '<div><input class="formbot" type="submit" name="final_submit4" value="Submit"></div>';
                echo '</form>';
            }}


if (isset($_POST["final_submit1"])) {
    $IDPic = null;
    $Proof = null;
    if (isset($_FILES['file'])) {
        $Proof = handleImageUpload($_FILES['file'], $connection);
        if (!$Proof) {
            echo "Error uploading Proof";
          
            exit();
        }
    }
    $PaymentProof = null;
    if (isset($_FILES['fileee'])) {
        $PaymentProof = handleImageUpload($_FILES['fileee'], $connection);
        if (!$PaymentProof) {
            echo "Error uploading PaymentProof";
            exit();
        }
    }
        $subscription_info = $_SESSION['subscription_info'];
        echo "<h2 class='titre'>Subscription Information</h2>";
        echo "<p class='formtxt'>Subscription Type: " . $subscription_info['subscription_type'] . "</p>";
        echo "<p class='formtxt'>Category: " . $subscription_info['category'] . "</p>";
        echo "<p class='formtxt'>Plan: " . $subscription_info['plan'] . "</p>";

        echo "<p class='formtxt'>Price: " . $subscription_info['price'] . "</p>";

        $SubID = Get_New_ID_subrequest($connection, "SubscriptionRequest", "SubID");
        $Date = date('Y-m-d');
        $Category = $subscription_info['category'];
        $SubscriptionType = $subscription_info['subscription_type'];
        $Price = $subscription_info['price'];
        $ClientID = $userId;

        $Plan = $subscription_info['plan'];

        $clientsubreq = new SubscriptionRequest($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan);
        $clientsubreq->SubscriptionRequest_Table_Creation($connection);
        $clientsubreq->Create_New_SubscriptionRequest($connection);
          unset($_SESSION['subscription_info']);

        echo "<p class='formtxt'>Your subscription request has been submitted!</p>";
    }
if (isset($_POST["final_submit2"])) {
    $IDPic = null;
    $Proof = null;
    $PaymentProof = null;
    if (isset($_FILES['file'])) {
        $Proof = handleImageUpload($_FILES['file'], $connection);
        if (!$Proof) {
            echo "Error uploading Proof";
          
            exit();
        }
    }
        $subscription_info = $_SESSION['subscription_info'];
        echo "<h2 class='titre'>Subscription Information</h2>";
        echo "<p class='formtxt'>Subscription Type: " . $subscription_info['subscription_type'] . "</p>";
        echo "<p class='formtxt'>Category: " . $subscription_info['category'] . "</p>";
        echo "<p class='formtxt'>Plan: " . $subscription_info['plan'] . "</p>";

        echo "<p class='formtxt'>Price: " . $subscription_info['price'] . "</p>";

        $SubID = Get_New_ID_subrequest($connection, "SubscriptionRequest", "SubID");
        $Date = date('Y-m-d');
        $Category = $subscription_info['category'];
        $SubscriptionType = $subscription_info['subscription_type'];
        $Price = $subscription_info['price'];
        $ClientID = $userId;

        $Plan = $subscription_info['plan'];

        $clientsubreq = new SubscriptionRequest($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan);
        $clientsubreq->SubscriptionRequest_Table_Creation($connection);
        $clientsubreq->Create_New_SubscriptionRequest($connection);
          unset($_SESSION['subscription_info']);

          echo "<p class='formtxt'>Your subscription request has been submitted!</p>";
    }
if (isset($_POST["final_submit3"])) {
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        if (!$IDPic) {
            echo "Error uploading IDPic";
      
            exit();
        }
    }
    $Proof = null;
    if (isset($_FILES['file'])) {
        $Proof = handleImageUpload($_FILES['file'], $connection);
        if (!$Proof) {
            echo "Error uploading Proof";
          
            exit();
        }
    }
    $PaymentProof = null;
    if (isset($_FILES['fileee'])) {
        $PaymentProof = handleImageUpload($_FILES['fileee'], $connection);
        if (!$PaymentProof) {
            echo "Error uploading PaymentProof";
            exit();
        }
    }
        $subscription_info = $_SESSION['subscription_info'];
        echo "<h2 class='titre'>Subscription Information</h2>";
        echo "<p class='formtxt'>Subscription Type: " . $subscription_info['subscription_type'] . "</p>";
        echo "<p class='formtxt'>Category: " . $subscription_info['category'] . "</p>";
        echo "<p class='formtxt'>Plan: " . $subscription_info['plan'] . "</p>";

        echo "<p class='formtxt'>Price: " . $subscription_info['price'] . "</p>";

        $SubID = Get_New_ID_subrequest($connection, "SubscriptionRequest", "SubID");
        $Date = date('Y-m-d');
        $Category = $subscription_info['category'];
        $SubscriptionType = $subscription_info['subscription_type'];
        $Price = $subscription_info['price'];
        $ClientID = $userId;

        $Plan = $subscription_info['plan'];

        $clientsubreq = new SubscriptionRequest($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan);
        $clientsubreq->SubscriptionRequest_Table_Creation($connection);
        $clientsubreq->Create_New_SubscriptionRequest($connection);
          unset($_SESSION['subscription_info']);

          echo "<p class='formtxt'>Your subscription request has been submitted!</p>";
    }
if (isset($_POST["final_submit4"])) {
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        if (!$IDPic) {
            echo "Error uploading IDPic";
      
            exit();
        }
    }
    $Proof = null;
    $PaymentProof = null;
    if (isset($_FILES['fileee'])) {
        $PaymentProof = handleImageUpload($_FILES['fileee'], $connection);
        if (!$PaymentProof) {
            echo "Error uploading PaymentProof";
            exit();
        }
    }
        $subscription_info = $_SESSION['subscription_info'];
        echo "<h2 class='titre'>Subscription Information</h2>";
        echo "<p class='formtxt'>Subscription Type: " . $subscription_info['subscription_type'] . "</p>";
        echo "<p class='formtxt'>Category: " . $subscription_info['category'] . "</p>";
        echo "<p class='formtxt'>Plan: " . $subscription_info['plan'] . "</p>";

        echo "<p class='formtxt'>Price: " . $subscription_info['price'] . "</p>";

        $SubID = Get_New_ID_subrequest($connection, "SubscriptionRequest", "SubID");
        $Date = date('Y-m-d');
        $Category = $subscription_info['category'];
        $SubscriptionType = $subscription_info['subscription_type'];
        $Price = $subscription_info['price'];
        $ClientID = $userId;

        $Plan = $subscription_info['plan'];

        $clientsubreq = new SubscriptionRequest($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan);
        $clientsubreq->SubscriptionRequest_Table_Creation($connection);
        $clientsubreq->Create_New_SubscriptionRequest($connection);
          unset($_SESSION['subscription_info']);

        echo "<p class='formtxt'>Your subscription request has been submitted!</p>";
    }
if (isset($_POST["final_submit5"])) {
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        if (!$IDPic) {
            echo "Error uploading IDPic";
      
            exit();
        }
    }
    $Proof = null;
    $PaymentProof = null;
    if (isset($_FILES['file'])) {
        $Proof = handleImageUpload($_FILES['file'], $connection);
        if (!$Proof) {
            echo "Error uploading PaymentProof";
            exit();
        }
    }
        $subscription_info = $_SESSION['subscription_info'];
        echo "<h2 class='titre'>Subscription Information</h2>";
        echo "<p class='formtxt'>Subscription Type: " . $subscription_info['subscription_type'] . "</p>";
        echo "<p class='formtxt'>Category: " . $subscription_info['category'] . "</p>";
        echo "<p class='formtxt'>Plan: " . $subscription_info['plan'] . "</p>";

        echo "<p class='formtxt'>Price: " . $subscription_info['price'] . "</p>";

        $SubID = Get_New_ID_subrequest($connection, "SubscriptionRequest", "SubID");
        $Date = date('Y-m-d');
        $Category = $subscription_info['category'];
        $SubscriptionType = $subscription_info['subscription_type'];
        $Price = $subscription_info['price'];
        $ClientID = $userId;

        $Plan = $subscription_info['plan'];

        $clientsubreq = new SubscriptionRequest($SubID, $Date, $Category, $SubscriptionType, $Price, $ClientID, $IDPic, $Proof, $PaymentProof, $Plan);
        $clientsubreq->SubscriptionRequest_Table_Creation($connection);
        $clientsubreq->Create_New_SubscriptionRequest($connection);
          unset($_SESSION['subscription_info']);

        echo "<p class='formtxt'>Your subscription request has been submitted!</p>";
    }
} else {
    session_destroy();
    unset($_SESSION['user_id_Admins']);
    echo '<script>window.location.href = "login.php";</script>';
    exit();
}
echo '</div>';//right
echo '</div>';
?>

</body>

</html>

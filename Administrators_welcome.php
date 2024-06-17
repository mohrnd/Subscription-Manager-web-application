<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>


<?php
session_start();
require_once 'Database.php';
require 'Administrators.php';
// Check if the user is logged in
unset($_SESSION['user_id_Client']);
unset($_SESSION['user_id_Agent']);

echo '<div class="top">';
 

echo '<img src="https://scontent.fqfd1-1.fna.fbcdn.net/v/t1.6435-9/90259371_1333938130125008_141649855373115392_n.png?_nc_cat=100&ccb=1-7&_nc_sid=7a1959&_nc_eui2=AeHlIkEKBsJaUkGsKtU_nraZcN1pqk7Ad_dw3WmqTsB396fhGbpjlxRjcTDIqexe0ILUL1t5W3MaX3hFNmTTTouf&_nc_ohc=X4cF3XiGF3EAX89X0se&_nc_ht=scontent.fqfd1-1.fna&oh=00_AfB59Tu0goloQQbDUAy5YgMBFLSX3HE2LrUjb0TSHZIKaA&oe=65B89C44" class="img1"';




if (isset($_SESSION['user_id_Admins'])) {
    $userId = $_SESSION['user_id_Admins'];
   
    echo "<h1></h1>";
    echo "<h1  >Welcome Admin! admin ID: $userId</h1>";




$Request = "SELECT * FROM images, Administrators WHERE AdminID = $userId AND id = ProfilePictureID";
$stmt = $connection->prepare($Request);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);


if ($result) {
    $imageData = base64_encode($result['image_data']);

   
    echo '<img src="data:image/jpeg;base64,' . $imageData . '" class="img2">';
  
} else {
    echo "Image not found";
    
}

echo '</div>';//top

echo '<div class="bottom">';


echo '<div class="bottom-left">';    

    echo '<form method="post">';
   
    echo '<input type="submit" name="ChangePswd" class="bouton-left" value="Change my Password">';
    
    echo '<input type="submit" name="upchpfpforms" class="bouton-left" value="Upload/Change my profile picture">';
    
    echo '<input type="submit" name="NewAdmin" class="bouton-left" value="Create a new Administrator">';
    echo '<input type="submit" name="NewAgent" class="bouton-left" value="Create a new Agent">';
    echo '<input type="submit" name="NewClient" class="bouton-left" value="Create a new Client">';
    echo '<input type="submit" name="ViewClients" class="bouton-left" value="View Clients database">';
    echo '<input type="submit" name="ViewAgents" class="bouton-left" value="View Agents database">';
    echo '<input type="submit" name="Logoff" class="bouton-left2" value="Logoff">';

    echo '</form>';

    echo '</div>';//bottomleft

    echo '<div class="bottom-right">'; 
    echo '  <br>';echo '  <br>';echo '  <br>';echo '  <br>';echo '  <br>';

    echo '<div class="container_right">';   

    ///////////number or subscription requests
    echo "<br>"; 
            require_once 'Active_Subscriptions.php';
            $SR = get_Number_Subscription_Requsts($connection);
            echo "<div><a class='numbr'>The number of subscription Requests: $SR</a></div>";
            //////////number of active subscriptions
            $AS = get_Number_active_Subscriptions($connection);
            echo "<div><a class='numbr' >The number of active subscriptions: $AS</a></div>";
    

   
} else {
    header("Location: login.php");
    exit();
}




///change pfp
if (isset($_POST["upchpfpforms"])) {
    
    echo '<div class="titre">Please upload your profile picture (make sure its a picture of you)</div>';
    echo '<form enctype="multipart/form-data" action="" method="POST">';
    echo '<div><label for="filee">Choose Image:</label>';
    echo '<input  type="file" id="filee" name="filee" accept="image/*" required></div><br>';
    echo '<div ><input class="formbot" type="submit" name="final_submit_pfp" value="Upload"></div>';
}
if (isset($_POST["final_submit_pfp"])) {
    require 'pictures_subscriptions.php';
    $IDPic = null;
    if (isset($_FILES['filee'])) {
        $IDPic = handleImageUpload($_FILES['filee'], $connection);
        echo "Your profile picture has been changed";
        if (!$IDPic) {
            echo "Error uploading IDPic";
      
            exit();
        }
    }
    
uploadrenewPfp_admins($connection,$userId, $IDPic);
    
    
}





//view/change pswd/ delete clients
if (isset($_POST["ViewClients"])) {
    require_once 'Clients.php';

    // Assuming $connection is your PDO connection object
    $clientsObj = new Clients('', '', '', '', '', '', '', '', '');

    $clientsData = $clientsObj->View_Clients($connection);

    if (!empty($clientsData)) {
        ?>

        <h2 class="titre">Client Details</h2>
    
        <table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Family name</th>
                <th>Date of birth</th>
                <th>Email</th>
                <th>Profile Picture</th>
                <th>Action</th>
            </tr>
            <?php foreach ($clientsData as $client) : ?>
                <tr>
                    <td><?= $client['ClientID'] ?></td>
                    <td><?= $client['FirstName'] ?></td>
                    <td><?= $client['LastName'] ?></td>
                    <td><?= $client['Dob'] ?></td>
                    <td><?= $client['Email'] ?></td>
                    <td>
                        <?php
                            // Display the profile picture if available
                            if (!empty($client['ProfilePictureID'])) {
                                $imageData = base64_encode($client['image_data']);
                                echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Profile Picture" style="width: 100px; height: 100px;">';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </td>
                    <td>
                        <div><form style="display:inline;" action="" method="post">
                            <?php $clientID = $client['ClientID']; ?>
                            <input type="hidden" name="deleteClientID" value="<?= $clientID; ?>">
                            <button type="submit" name="DeleteClientSubmit">Delete</button>
                            </form></div>
                        
                          <div><form style="display:inline;" action="" method="post">
                            <?php $clientID = $client['ClientID']; ?>
                            <input type="hidden" name="ChangePsswdID" value="<?= $clientID; ?>">
                            <button type="submit" name="ChangePsswdSubmit">Change Password</button>
                            </form></div>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } else {
        echo "<p>No clients found.</p>";
    }
}
if (isset($_POST["DeleteClientSubmit"])) {
        $clientID = $_POST["deleteClientID"];
        require_once 'Clients.php';
        $clientsObj = new Clients('', '', '', '', '', '', '', '', '');
        $clientsObj->Delete_Client($connection, $clientID);
    }

if (isset($_POST["ChangePsswdSubmit"])) {
    $clientID = $_POST["ChangePsswdID"];
    require_once 'Clients.php';

    echo '<h2 class="titre">Change Client Password</h2><br>';
    echo '<form action="" method="post">';
    echo '    <label class="formtxt" for="client_id">Client ID:</label>';
    echo '    <input class="form" type="text" id="client_id" name="client_id" value="' . $clientID . '" readonly><br><br>';
    echo '    <label class="formtxt" for="new_password">New Password:</label>';
    echo '    <input class="form" type="password" id="new_password" name="new_password" required><br><br>';
    echo '    <label class="formtxt" for="confirm_password">Confirm Password:</label>';
    echo '    <input class="form" type="password" id="confirm_password" name="confirm_password" required><br>';
    echo '    <input class="formbot" type="submit" name="change_client_password" value="Confirm changing">';
    echo '</form>';
}
if (isset($_POST["change_client_password"])) {
    $clientID = $_POST["client_id"];
    ChangeClientPassword($connection, $clientID);
}
function ChangeClientPassword($c, $clientID) {
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword != $confirmPassword) {
        echo "Error: New password and confirm password do not match.";
        exit();
    }

    $hashedPassword = md5($newPassword);

    $request = "UPDATE Clients SET Password = :NewPassword WHERE ClientID = :ClientID";
    $stmt = $c->prepare($request);
    $stmt->execute([
        ':NewPassword' => $hashedPassword,
        ':ClientID' => $clientID
    ]);

    // Check if the update was successful
    $rowCount = $stmt->rowCount();
    if ($rowCount > 0) {
        echo "Client password changed successfully!<br>";
    } else {
        echo "Error changing client password.";
    }
}




        
    

//view/change pswd/ delete agent
if (isset($_POST["ViewAgents"])) {
    require_once 'Agent.php';

    // Assuming $connection is your PDO connection object
    $agentObj = new AGENTS('', '', '', '', '', '', '', '', '');

    $agentData = $agentObj->View_Agents($connection);

    if (!empty($agentData)) {
        ?>
        <h2 class="titre">Agent Details</h2>
        <table border='1' class="styled-table" >
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Family name</th>
                <th>Date of birth</th>
                <th>Email</th>
                <th>Profile Picture</th>
                <th>Action</th>
            </tr>
            <?php foreach ($agentData as $agent) : ?>
                <tr>
                    <td><?= $agent['AgentID'] ?></td>
                    <td><?= $agent['FirstName'] ?></td>
                    <td><?= $agent['LastName'] ?></td>
                    <td><?= $agent['Dob'] ?></td>
                    <td><?= $agent['Email'] ?></td>
                    <td>
                        <?php
                        // Display the profile picture if available
                        if (!empty($agent['ProfilePictureID'])) {
                            $imageData = base64_encode($agent['image_data']);
                            echo '<img src="data:image/jpeg;base64,' . $imageData . '" alt="Profile Picture" style="width: 100px; height: 100px;">';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                    <td>
                        <div><form style="display:inline;" action="" method="post">
                            <?php $agentID = $agent['AgentID']; ?>
                            <input type="hidden" name="deleteAgentID" value="<?= $agentID; ?>">
                            <button type="submit" name="DeleteAgentSubmit">Delete</button>
                            </form></div>
                        
                            <div><form style="display:inline;" action="" method="post">
                            <?php $agentID = $agent['AgentID']; ?>
                            <input type="hidden" name="ChangePsswdID_Agent" value="<?= $agentID; ?>">
                            <button class="table-bouton"  type="submit" name="ChangePsswdSubmit_Agent">Change Password</button>
                            </form></div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } else {
        echo "<p>No Agents found.</p>";
    }
}
if (isset($_POST["DeleteAgentSubmit"])) {
    $agentID = $_POST["deleteAgentID"]; 
    require_once 'Agent.php';
    $agentsObj = new Agents('', '', '', '', '', '', '', '', '');
    $agentsObj->Delete_Agent($connection, $agentID);
}

if (isset($_POST["ChangePsswdSubmit_Agent"])) {
    $agentID = $_POST["ChangePsswdID_Agent"];
    require_once 'Agent.php';
    echo '<h2 class="titre">Change Agent Password</h2><br>';
    echo '<form action="" method="post">';
    echo '    <label class="formtxt" for="agent_id">Agent ID:</label>';
    echo '    <input class="form" type="text" id="agent_id" name="agent_id" value="' . $agentID . '" readonly><br><br>';
    echo '    <label class="formtxt" for="new_password">New Password:</label>';
    echo '    <input class="form" type="password" id="new_password" name="new_password" required><br><br>';
    echo '    <label class="formtxt" for="confirm_password">Confirm Password:</label>';
    echo '    <input class="form" type="password" id="confirm_password" name="confirm_password" required><br>';
    echo '    <input class="formbot" type="submit" name="change_agent_password" value="Confirm changing">';
    echo '</form>';
}
if (isset($_POST["change_agent_password"])) {
    $agentID = $_POST["agent_id"];
    ChangeAgentPassword($connection, $agentID);
}
function ChangeAgentPassword($c, $agentID) {
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword != $confirmPassword) {
        echo "Error: New password and confirm password do not match.";
        exit();
    }

    $hashedPassword = md5($newPassword);

    $request = "UPDATE Agents SET Password = :NewPassword WHERE AgentID = :AgentID";
    $stmt = $c->prepare($request);
    $stmt->execute([
        ':NewPassword' => $hashedPassword,
        ':AgentID' => $agentID
    ]);

    // Check if the update was successful
    $rowCount = $stmt->rowCount();
    if ($rowCount > 0) {
        echo "Agent password changed successfully!<br>";
    } else {
        echo "Error changing agent password.";
    }
}






///logofff
if (isset($_POST["Logoff"])) { 
    session_destroy();
    unset($_SESSION['user_id_Admins']);
    echo '<script>window.location.href = "login.php";</script>';
    exit();   
}

///new client
if (isset($_POST["NewClient"])) {

    echo '<h2 class="titre" >Client account Creation</h2>';
    echo '<form action="" method="post">';
    echo '<p>';
    echo '  <label class="formtxt" for="firstName">First Name:</label>';
    echo '  <input class="form" type="text" id="firstName" name="firstName" required>';
    echo '  <label class="formtxt" for="lastName">Last Name:</label>';
    echo '  <input class="form" type="text" id="lastName" name="lastName" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="dob">Date of Birth:</label>';
    echo '  <input class="form" type="date" id="dob" name="dob" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="gender">Gender:</label>';
    echo '  <select class="gender" id="gender" name="gender" required>';
    echo '    <option value="male">Male</option>';
    echo '    <option value="female">Female</option>';
    echo '  </select>';
    echo '  <br><br>';
    echo '  <label class="formtxt" for="phoneNumber">Phone Number:</label>';
    echo '  <input class="form" type="tel" id="phoneNumber" name="phoneNumber" required>';
    echo '  <br>';
    echo '</p>';
    echo '  <label class="formtxt" for="email">Email:</label>';
    echo '  <input class="form" type="email" id="email" name="email" required>';
    echo '  <label class="formtxt" for="Password">Password:</label>';
    echo '  <input class="form" type="Password" id="Password" name="Password" required>';
    echo '  <br>';
    echo '<input class="formbot2" type="submit" name="Create_Client_Forms" value="Enter">';
    echo "</form>";
}
if (isset($_POST["Create_Client_Forms"])) {
    require 'Clients.php';
    $ClientID = Get_New_ID_clients($connection, "Clients", "ClientID");
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["email"];
    $Password = $_POST["Password"];
    // Hacher le mot de passe avec md5()
    $hashedPassword = md5( $Password);
    $ProfilePictureID = null;
    //$ClientID, $FirstName, $LastName, $Dob, $Gender, $PhoneNumber, $Email, $Password, $ProfilePictureID
    $Client = new Clients($ClientID, $firstName, $lastName, $dob, $gender, $phoneNumber, $email, $hashedPassword, $ProfilePictureID);
    $Client->Clients_Table_Creation($connection);
    $Client->Create_New_Client($connection);
}

function Get_New_ID_clients($c, $tableName, $ID_var_Name) {
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




///change my password
if (isset($_POST["ChangePswd"])) {
    Change_Password_Forms();
}
function Change_Password_Forms(){
    echo '<h2 class="titre">Change My Password</h2><br>';
    echo '<form action="" method="post">';
    echo '    <label class="formtxt"   for="new_password">New Password:</label>';
    echo '    <input class="form" type="password" id="new_password" name="new_password" required><br><br>';
    echo '    <label for="confirm_password">Confirm Password:</label>';
    echo '    <input  class="form" type="password" id="confirm_password" name="confirm_password" required><br>';
    echo '    <input  class="formbot" type="submit" name="change_password" value="Change Password">';
    echo '</form>';


}
if (isset($_POST["change_password"])) {
    // Validate form data
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword != $confirmPassword) {
        echo "Error: New password and confirm password do not match.";
        exit();
    }

    // Hacher le mot de passe avec md5()
    $hashedPassword = md5( $newPassword);

    $Request = "UPDATE Administrators SET Password = :NewPassword WHERE AdminID = :AdminID";
    $stmt = $connection->prepare($Request);
    $stmt->execute([
        ':NewPassword' => $hashedPassword,
        ':AdminID' => $userId
    ]);

    echo "Password changed successfully!";
}





function forms_Administrators() {
    echo '<h2 class="titre">Administrator account Creation</h2>';
    echo '<form action="" method="post">';
    echo '<p>';
    echo '  <label class="formtxt" for="firstName">First Name:</label>';
    echo '  <input class="form" type="text" id="firstName" name="firstName" required>';   
    echo '  <label class="formtxt" class="space" for="lastName">Last Name:</label>';
    echo '  <input class="form" type="text" id="lastName" name="lastName" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="dob">Date of Birth:</label>';
    echo '  <input class="form" type="date" id="dob" name="dob" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="gender">Gender:</label>';
    echo '  <select class="gender"   id="gender" name="gender" required>';
    echo '    <option value="male">Male</option>';
    echo '    <option value="female">Female</option>';
    echo '  </select>';
    echo '  <br><br>';
    echo '  <label class="formtxt" for="phoneNumber">Phone Number:</label>';
    echo '  <input class="form" type="tel" id="phoneNumber" name="phoneNumber" required>';
    echo '  <br>';
    echo '</p>';
    echo '  <label class="formtxt" for="email">Email:</label>';
    echo '  <input class="form" type="email" id="email" name="email" required>';   
    echo '  <label class="formtxt"  class="space" for="Password">Password:</label>';
    echo '  <input class="form" type="Password" id="Password" name="Password" required>';
    echo '  <br>';
    echo '<input class="formbot2" type="submit" name="Create_Admins_Forms" value="Enter">';
    echo "</form>";
}

if (isset($_POST["Create_Admins_Forms"])) {
    $AdminID = Get_New_ID_Administrator($connection, "Administrators", "AdminID");
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["email"];
    $Password = $_POST["Password"];
    // Hacher le mot de passe avec md5()
    $hashedPassword = md5($Password);
    $ProfilePictureID = null;
    $Client = new Administrators($AdminID, $firstName, $lastName, $dob, $gender, $phoneNumber, $email, $hashedPassword, $ProfilePictureID);
    $Client->Administrators_Table_Creation($connection);
    $Client->Create_New_Administrator($connection);
}

function Get_New_ID_Administrator($c, $tableName, $ID_var_Name) {
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

if (isset($_POST["NewAdmin"])) {
forms_Administrators();
}


if (isset($_POST["NewAgent"])) {
    
require 'Agent.php';


    echo '<h2 class="titre">Agent account Creation</h2>';
    echo '<form action="" method="post">';
    echo '<p>';
    echo '  <label class="formtxt" for="firstName">First Name:</label>';
    echo '  <input class="form" type="text" id="firstName" name="firstName" required>';
    echo '  <label class="formtxt" for="lastName">Last Name:</label>';
    echo '  <input class="form" type="text" id="lastName" name="lastName" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="dob">Date of Birth:</label>';
    echo '  <input class="form" type="date" id="dob" name="dob" required>';
    echo '  <br>';
    echo '  <label class="formtxt" for="gender">Gender:</label>';
    echo '  <select class="gender" id="gender" name="gender" required>';
    echo '    <option value="male">Male</option>';
    echo '    <option value="female">Female</option>';
    echo '  </select>';
    echo '  <br><br>';
    echo '  <label class="formtxt" for="phoneNumber">Phone Number:</label>';
    echo '  <input class="form" type="tel" id="phoneNumber" name="phoneNumber" required>';
    echo '  <br>';
    echo '</p>';
    echo '  <label class="formtxt" for="email">Email:</label>';
    echo '  <input class="form" type="email" id="email" name="email" required>';
    echo '  <label class="formtxt" for="Password">Password:</label>';
    echo '  <input class="form" type="Password" id="Password" name="Password" required>';
    echo '  <br>';
    echo '<input class="formbot2" type="submit" name="Create_Agent_Forms" value="Enter">';
    echo "</form>";
 }
    
if (isset($_POST["Create_Agent_Forms"])) {
    require 'Agent.php';
    $AgentID = Get_New_ID_agents($connection, "Agents", "AgentID");
    echo $AgentID;
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["email"];
    $Password = $_POST["Password"];
    // Hacher le mot de passe avec md5()
    $hashedPassword = md5($Password);
    $ProfilePictureID = null;
    $AGENTS= new AGENTS($AgentID, $firstName, $lastName, $dob, $gender, $phoneNumber, $email,$hashedPassword, $ProfilePictureID);
    $AGENTS->AGENTS_Table_Creation($connection);
    $AGENTS->Create_New_AGENTS($connection);
   
}

echo '</div>';//containerright
echo '</div>';//bottomright
echo '</div>';//bottom

?>





</body>
</html>




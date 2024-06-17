<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style_sub.css">
</head>
<body>



<?php
require_once 'Clients.php';
echo '<div class="cont">';

echo '<h2</h2>';
    echo '<h2 class="titre">Client account Creation</h2>';
    echo '<form action="" method="post">';
    echo '<p>';
    echo '  <label class="formtxt" for="firstName">First Name:</label>';
    echo '  <input class="form"  type="text" id="firstName" name="firstName" required>';
    echo '  <label class="formtxt"  for="lastName">Last Name:</label>';
    echo '  <input class="form"  type="text" id="lastName" name="lastName" required>';
    echo '  <br>';
    echo '  <label class="formtxt"  for="dob">Date of Birth:</label>';
    echo '  <input class="form"  type="date" id="dob" name="dob" required>';
    echo '  <br>';
    echo '  <label class="formtxt"  for="gender">Gender:</label>';
    echo '  <select id="gender" class="gender" name="gender" required>';
    echo '    <option value="male">Male</option>';
    echo '    <option value="female">Female</option>';
    echo '  </select>';
    echo '  <br><br>';
    echo '  <label class="formtxt"  for="phoneNumber">Phone Number:</label>';
    echo '  <input class="form"  type="tel" id="phoneNumber" name="phoneNumber" required>';
    echo '  <br>';
    echo '</p>';
    echo '  <label class="formtxt"  for="email">Email:</label>';
    echo '  <input class="form"  type="email" id="email" name="email" required>';
    echo '  <label  class="formtxt"   for="Password">Password:</label>';
    echo '  <input class="form"  type="Password" id="Password" name="Password" required>';
    echo '  <br>';
    echo '<input class="formbot"  type="submit" name="Create_Client_Forms" value="Enter">';
    echo "</form>";

if (isset($_POST["Create_Client_Forms"])) {
    $ClientID = Get_New_ID($connection, "Clients", "ClientID");
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
    $Client = new Clients($ClientID, $firstName, $lastName, $dob, $gender, $phoneNumber, $email, $hashedPassword, $ProfilePictureID);
    $Client->Clients_Table_Creation($connection);
    $Client->Create_New_Client($connection);
  
}

function Get_New_ID($c, $tableName, $ID_var_Name) {
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
echo '<a class="formbot" href="login.php">Go back to login page<a>';
echo '</div>';
?>
</body>
</html>


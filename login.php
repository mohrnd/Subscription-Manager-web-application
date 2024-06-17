<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SETRAM Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: url(https://media.licdn.com/dms/image/C4D1BAQHJ0bM1qXzs3Q/company-background_10000/0/1583754426368/setram_cover?e=2147483647&v=beta&t=lcTew1E2axAUM8V5-c3P1BpsmrSp8rFzbYUKyTrnTkg) no-repeat center center/cover;
}

.login-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    opacity: 0.85;
}


.login-container:hover {

    opacity: 1;
    cursor: pointer;

    }

.login-box {
    padding: 70px;
    text-align: center;
}

.logo img {
    width: 300px; /* Adjust the width as needed */
    transition: transform 0.3s ease;
}

.logo:hover img {
    transform: scale(1.1);
}

h2 {
    color: #333;
    animation: fadeIn 1s ease;
    font-weight: 700;
}

.input-group {
    margin: 20px 0;
    display: flex;
    align-items: center;
    border-bottom: 1px solid transparent;
    transition: border-bottom 0.3s ease;
}

.input-group i {
    margin-right: 10px;
}

.input-group:hover {
    border-bottom: 1px solid #3498db;
}

input {
    border: none;
    outline: none;
    font-size: 20px;
    width: 100%;
    transition: color 0.3s ease;
    font-weight: 400;
}

input:hover {
    color: #3498db;
}

.button-container {
    margin-top: 20px;
    margin-bottom: 20px;
}

button {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    font-weight: 700;
}

button:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

.create-account {
    margin-top: 25px; /* Adjusted margin-top value */
    font-size: 14px;
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
    font-weight: 400;
    align-self: center
}

.create-account:hover {
    color: #2980b9;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

</style>
<body>
    <div class="login-container">
      <div class="login-box">
            <div class="logo">
                <img src="https://upload.wikimedia.org/wikipedia/fr/archive/6/68/20230507215417%21Logo_SETRAM_Alg%C3%A9rie.png" alt="SETRAM Logo">
            </div>
            <!-- <h2>Login to Tram Services</h2> -->
        <form action="#" method="post">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="button-container">
                    <button type="submit" name="LOGIN"><i class="fas fa-sign-in-alt"></i>Login</button>
                </div>
          </form>
          <a href="Clients_Clients.php" class="create-account">Don't have an account? Create one here!</a>
        </div>
   


<?php
require_once 'Database.php';
require_once 'Clients.php';
require_once 'Agent.php';
require_once 'Administrators.php';
if (isset($_POST["LOGIN"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Hacher le mot de passe avec md5()
    $hashedPassword = md5($password);
    $Client = new Clients('', '', '', '', '', '', '', '', '');
    $Client->Clients_Table_Creation($connection);
    // Use prepared statements to prevent SQL injection for Clients
    $clientRequest = "SELECT ClientID, firstname, lastname, Email, Password FROM Clients WHERE Email = :email AND Password = :password";
    $clientStmt = $connection->prepare($clientRequest);
    $clientStmt->bindParam(':email', $email);
    $clientStmt->bindParam(':password', $hashedPassword);
    $clientStmt->execute();
    $agentObj = new AGENTS('', '', '', '', '', '', '', '', '');
    $agentObj->AGENTS_Table_Creation($connection);
    // Use prepared statements to prevent SQL injection for Agents
    $agentRequest = "SELECT AgentID, firstname, lastname, Email, Password  FROM Agents WHERE Email = :email AND Password = :password";
    $agentStmt = $connection->prepare($agentRequest);
    $agentStmt->bindParam(':email', $email);
    $agentStmt->bindParam(':password',  $hashedPassword);
    $agentStmt->execute();

    $ADMIN = new Administrators('', '', '', '', '', '', '', '', '');
    $ADMIN->Administrators_Table_Creation($connection);
    // Use prepared statements to prevent SQL injection for Administrators
    $adminRequest = "SELECT AdminID, FirstName, LastName, Email, Password FROM Administrators WHERE Email = :email AND Password = :password";
    $adminStmt = $connection->prepare($adminRequest);
    $adminStmt->bindParam(':email', $email);
    $adminStmt->bindParam(':password',  $hashedPassword);
    $adminStmt->execute();

    $clientRes = $clientStmt->fetch(PDO::FETCH_ASSOC);
    if ($clientRes) {
        session_start();
        $_SESSION['user_id_Client'] = $clientRes['ClientID'];
        header("Location: Clients_welcome.php");
        exit();
    } else {
        $agentRes = $agentStmt->fetch(PDO::FETCH_ASSOC);
        if ($agentRes) {
            session_start();
            $_SESSION['user_id_Agent'] = $agentRes['AgentID'];
            header("Location: Agents_welcome.php");
            exit();
        } else {
            $adminRes = $adminStmt->fetch(PDO::FETCH_ASSOC);
            if ($adminRes) {
                session_start();
                $_SESSION['user_id_Admins'] = $adminRes['AdminID'];
                header("Location: Administrators_welcome.php");
                exit();
            } else {
                echo '<script>alert("Authentication failed. Please check your email and password.");</script>';
            }
        }
    }
}

?>


 </div>
</body>
</html>



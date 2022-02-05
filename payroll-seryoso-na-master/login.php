<?php
require_once('class.php');
$payroll->login();

// if not allowed to login get the message
if(isset($_GET['message'])){
    echo $_GET['message'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <div>
            <label for="username">Username</label>
            <input type="email" id="username" name="username" placeholder="Enter username" required/>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required/>
        </div>
        <button type="submit" name="login">Login</button>
    </form>
<a href="SecretaryPortal/loginsecretary.php">SECRETARY LOGIN</a>
</body>
</html>
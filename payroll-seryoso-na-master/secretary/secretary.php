<?php
require_once('../class.php');
$sessionData = $payroll->getSessionData();
$payroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
$payroll->addSecretary($sessionData['id'], $sessionData['fullname']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretary Page</title>
</head>
<body>
    <a href="showAll.php">view all</a>

    <?= $payroll->show2Secretary(); ?>

    <form method="post">
        <h1>Secretary Details</h1>
        <div>
            <label for="name">Name</label>
            <input type="text" name="fullname" id="fullname" autocomplete="off" required/>
        </div>
        <div>
            <label for="cpnumber">Contact #</label>
            <input type="text" name="cpnumber" autocomplete="off" required/>
        </div>
        <div>
            <label for="name">Email</label>
            <input type="email" name="email" id="email" autocomplete="off" required/>
        </div>
        <div>
            <label for="name">Gender</label>
            <select name="gender" id="gender" required>
                <option value=""></option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" autocomplete="off" required/>
        </div>
        <button type="submit" name="addsecretary">Add</button>
    </form>
</body>
</html>

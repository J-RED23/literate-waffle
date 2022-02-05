<?php
require_once('../class.php');
$sessionData = $payroll->getSessionData();
$payroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Secretary Record</title>
    <link rel="stylesheet" href="modal.css">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $payroll->showAllSecretary(); ?>
        </tbody>
    </table>

    <div class="view-modal">
        <form method="post">
            <div>
                <label for="fullname">Fullname</label>
                <input type="text" id="fullname" autocomplete="off" required/>
            </div>
            <div>
                <label for="gender">Gender</label>
                <select name="gender" id="gender" required>
                    <option value=""></option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" autocomplete="off" required/>
            </div>
            <div>
                <label for="cpnumber">Contact #</label>
                <input type="text" id="cpnumber" autocomplete="off" required/>
            </div>
            <div>
                <label for="address">Address</label>
                <input type="text" id="address" autocomplete="off" required/>
            </div>
        </form>
    </div>
<?php $payroll->showSpecificSec(); ?>
</body>
</html>
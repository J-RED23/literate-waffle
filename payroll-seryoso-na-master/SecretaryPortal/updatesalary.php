<?php
require_once('secclass.php');
$sessionData = $secpayroll->getSessionData();
$secpayroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
$fullname = $sessionData['fullname'];
$access = $sessionData['access'];
$id = $sessionData['id'];
$log=$_GET['logid'];
$secpayroll->updateSalary($id,$fullname);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .view-modal {
    height: 100vh;
    width: 100vw;
    background-color: #00000045;
    position: absolute;
    top: 0; left: 0;
    display: none;
}

form {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background: white;
}

#show-modal {
    display: block !important;
}</style>
</head>
<body>
<a href="manualpayroll.php">BACK</a>
<div class="manual_payroll">
                <h1>Edit</h1>
                    <form method="post">
                        <label for="empid">Employee ID</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Deductions<br/>
                        <?php $sql ="SELECT * FROM employee_info INNER JOIN generated_salary ON employee_info.emp_id = generated_salary.emp_id WHERE generated_salary.log = ?;";$stmt = $secpayroll->con()->prepare($sql); $stmt->execute([$log]); $rows = $stmt->fetch(); echo "<select id= select-state name=empid placeholder= Pick a state...><option value=$rows->emp_id> $rows->emp_id $rows->firstname $rows->lastname</option></select>";?>&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<label for="daylate">No. of late</label>&emsp;&emsp;<label for="minlate">minute/s late</label><br/>
                        <label for="location">Location</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="daylate" id="daylate" style="width: 5em" value="<?php echo $rows->day_late;?>">&emsp;&emsp;<input type="number" name="minlate" id="minlate" style="width: 5em"value="<?php echo $rows->min_late;?>"><br/>
                        <input type="text" name="location" id="location"value="<?php echo $rows->location;?>">&emsp;&emsp;&emsp;&emsp;<label for="dayabsent">No. of absent</label><br/>
                        <label for="regholiday">Regular Holiday</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="dayabsent" id="dayabsent" style="width: 5em"value="<?php echo $rows->day_absent;?>"><br/>
                        <input type="number" name="regholiday" id="regholiday"value="<?php echo $rows->regular_holiday;?>">&emsp;&emsp;&emsp;&emsp;<label for="sss">SSS</label><br/>
                        <label for="specialholiday">Special Holiday</label> &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="sss" id="sss"value="<?php echo $rows->sss;?>"><br/>
                        <input type="number" name="specialholiday" id="specialholiday"value="<?php echo $rows->special_holiday;?>">&emsp;&emsp;&emsp;&emsp;<label for="cashbond">Cash Bond</label><br/>
                    <label for="rate">Rate/Hour</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;<input type="number" name="cashbond" id="cashbond"value="<?php echo $rows->cashbond;?>"><br/>
                    <input type="number" name="rate" id="rate"value="<?php echo $rows->rate_hour;?>">&emsp;&emsp;&emsp;&emsp;<label for="cvale">Vale</label></br>
                    <label for="thirteenmonth">13month</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="cvale" id="cvale"value="<?php echo $rows->vale;?>"><br/><input type="number" name="thirteenmonth" id="thirteenmonth"value="<?php echo $rows->thirteenmonth;?>"><br/>
                    <label for="noofdayswork"># of days work</label>&emsp;<label for="hrsduty">Duty</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/>
                    <input type="number" name="noofdayswork" id="noofdayswork" style="width: 7em"value="<?php echo $rows->no_of_work;?>">&emsp;<select name="hrsduty" id="hrsduty" style="width: 7em">
                    <option value="8">8 hours</option><option value="12">12 hours</option></select>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/>
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/><br/>
                    <button type="submit" name="edit">Update</button>
                    </form>
        </div>
        </div>
</body>
</html>
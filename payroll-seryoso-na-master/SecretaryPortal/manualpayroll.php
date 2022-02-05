<?php
require_once('secclass.php');
$sessionData = $secpayroll->getSessionData();
$secpayroll->verifyUserAccess($sessionData['access'], $sessionData['fullname']);
$fullname = $sessionData['fullname'];
$access = $sessionData['access'];
$id = $sessionData['id'];
$secpayroll->generateSalary($id,$fullname);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
.generated_salary {
  position: absolute;
  right: 0px;
  top:0px;
  width: auto;
  border: 1px solid BLACK;
  padding: 100px;
}
.sidebar{
    position: absolute;
  left: 0px;
  width: 100px;
  padding: 10px;
}
.manual_payroll{
    margin-top: 10px;
    padding-top: 0px;
    padding-left: 200px;
}
</style>
</head>
<body>

</body>
<div class="main-container">
        <!--SIDENAV START-->
        <div class="sidebar">
            <div class="sidebar__logo">
                <div class="logo"></div>
                <h3>JDTV</h3>
            </div>
            <nav>
                <ul>
                    <li class="li__records">
                        <a href="#" class="">DASHBOARD</a>
                    </li>
                    <li class="li__records active">
                        <a href="secdashboard.php" class="active">ATTENDANCE</a>
                    </li>

                    <li class="li__report" class ="active">
                        <a href="">Payroll</a>
                        <ul>
                            <li><a href="manualpayroll.php">Manual</a></li>
                            <li><a href="#">Automatic</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="sidebar__logout">
                <div class="li li__logout"><a href="../logout.php">LOGOUT</a></div>
            </div>
            </div>
        <div class="page-info-head">
            Secretary
        </div>

        <div class="user-info">
            <div class="user-profile">
            </div>
        </div>
        <div class="manual_payroll">
                <h1>Generate Payslip</h1>
                    <form method="post">
                        <label for="empid">Employee ID</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Deductions<br/>
                        <?php $sql ="SELECT emp_id,firstname,lastname FROM employee_info;";$stmt = $secpayroll->con()->prepare($sql); $stmt->execute(); $row = $stmt->fetchall(); echo "<select id= select-state name=empid placeholder= Pick a state...>"; foreach($row as $rows){echo "<option value=$rows->emp_id> $rows->emp_id $rows->firstname $rows->lastname</option>";}; ?><?php echo "</select>"; ?>&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;<label for="daylate">No. of late</label>&emsp;&emsp;<label for="minlate">total of minute/s late</label><br/>
                        <label for="location">Location</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="daylate" id="daylate" style="width: 5em">&emsp;&emsp;<input type="number" name="minlate" id="minlate" style="width: 5em"><br/>
                        <input type="text" name="location" id="location">&emsp;&emsp;&emsp;&emsp;<label for="dayabsent">No. of absent</label><br/>
                        <label for="regholiday">Regular Holiday</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="dayabsent" id="dayabsent" style="width: 5em"><br/>
                        <input type="number" name="regholiday" id="regholiday">&emsp;&emsp;&emsp;&emsp;<label for="sss">SSS</label><br/>
                        <label for="specialholiday">Special Holiday</label> &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="sss" id="sss"><br/>
                        <input type="number" name="specialholiday" id="specialholiday">&emsp;&emsp;&emsp;&emsp;<label for="cashbond">Cash Bond</label><br/>
                    <label for="rate">Rate/Hour</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;<input type="number" name="cashbond" id="cashbond"><br/>
                    <input type="number" name="rate" id="rate">&emsp;&emsp;&emsp;&emsp;<label for="cvale">Vale</label></br>
                    <label for="thirteenmonth">13month</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<input type="number" name="cvale" id="cvale"><br/><input type="number" name="thirteenmonth" id="thirteenmonth"><br/>
                    <label for="noofdayswork"># of days work</label>&emsp;<label for="hrsduty">Duty</label>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/>
                    <input type="number" name="noofdayswork" id="noofdayswork" style="width: 7em">&emsp;<select name="hrsduty" id="hrsduty" style="width: 7em">
                    <option value="8">8 hours</option><option value="12">12 hours</option></select>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/>
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<br/><br/>
                    <button type="submit" name="generate">Generate</button>
                    </form>
        </div>
                <div class="generated_salary">
                <div class="card">
                <div class="card__content">
                <table>
        <thead>
            <tr>
                <th>Employee ID </th>
                <th>Location</th>
                <th>Date</th>
                <th>Action</th>
            </tr>   
        </thead>
        <tbody>
        <?php $secpayroll->displayGeneratedSalary();?>
        </tbody>
    </table>
                </div>
</div>
</div>
</div>

</html>
